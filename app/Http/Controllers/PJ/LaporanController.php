<?php

namespace App\Http\Controllers\PJ;

use App\Exports\AbsensiExport;
use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Devisi;
use App\Models\Kegiatan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;

class LaporanController extends Controller
{
    /**
     * Menampilkan halaman laporan absensi untuk devisi yang dipimpin oleh PJ.
     */
    public function index(Request $request)
    {
        $pjUser = Auth::user();
        $devisi = $pjUser->devisiYangDipimpin;

        if (!$devisi) {
            abort(403, 'AKSES DITOLAK.');
        }

        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'kegiatan_id' => 'nullable|exists:kegiatans,id',
        ]);
        
        $anggotaIds = $devisi->anggota()->pluck('id');

        // --- PERBAIKAN LOGIKA TANGGAL ---
        $startDate = $request->filled('start_date') 
            ? Carbon::parse($request->start_date)->startOfDay() 
            : Carbon::now()->subMonth()->startOfDay();

        $endDate = $request->filled('end_date')
            ? Carbon::parse($request->end_date)->endOfDay()
            : Carbon::now()->endOfDay();
        // --- AKHIR PERBAIKAN ---
        
        $selectedKegiatanId = $request->input('kegiatan_id');

        $query = Absensi::with(['user', 'kegiatan'])
            ->whereIn('user_id', $anggotaIds)
            ->whereBetween('waktu_absen', [$startDate, $endDate]);
            
        if ($selectedKegiatanId) {
            $query->where('kegiatan_id', $selectedKegiatanId);
        }

        $stats = (clone $query)
            ->select(
                DB::raw("SUM(CASE WHEN status = 'hadir' THEN 1 ELSE 0 END) as totalHadir"),
                DB::raw("SUM(CASE WHEN status = 'izin' THEN 1 ELSE 0 END) as totalIzin"),
                DB::raw("SUM(CASE WHEN status = 'sakit' THEN 1 ELSE 0 END) as totalSakit"),
                DB::raw("SUM(CASE WHEN status = 'alpa' THEN 1 ELSE 0 END) as totalAlpa")
            )
            ->first();

        $totalRecord = ($stats->totalHadir + $stats->totalIzin + $stats->totalSakit + $stats->totalAlpa);
        $persentaseKehadiran = ($totalRecord > 0) ? round((($stats->totalHadir) / $totalRecord) * 100, 1) : 0;
        
        $absensiLogs = $query->latest('waktu_absen')->paginate(15)->withQueryString();

        $kegiatanIds = Absensi::whereIn('user_id', $anggotaIds)->distinct()->pluck('kegiatan_id');
        $kegiatans = Kegiatan::whereIn('id', $kegiatanIds)->orderBy('judul')->get();

        return view('pj.laporan.index', compact(
            'absensiLogs', 'startDate', 'endDate', 'kegiatans',
            'selectedKegiatanId', 'stats', 'persentaseKehadiran', 'devisi'
        ));
    }

    /**
     * Menangani permintaan ekspor data absensi ke Excel.
     */
    public function export(Request $request)
    {
        $pjUser = Auth::user();
        $devisi = $pjUser->devisiYangDipimpin;
        
        if (!$devisi) {
            abort(403);
        }

        $anggotaIds = $devisi->anggota()->pluck('id');

        $query = Absensi::query()
            ->with(['user.devisi', 'kegiatan'])
            ->whereIn('user_id', $anggotaIds)
            ->when($request->start_date && $request->end_date, function ($q) use ($request) {
                // Perbaikan juga diterapkan di sini untuk konsistensi
                $start = Carbon::parse($request->start_date)->startOfDay();
                $end = Carbon::parse($request->end_date)->endOfDay();
                $q->whereBetween('waktu_absen', [$start, $end]);
            })
            ->when($request->kegiatan_id, function ($q) use ($request) {
                $q->where('kegiatan_id', $request->kegiatan_id);
            })
            ->latest('waktu_absen');
            
        $fileName = 'Laporan_Absensi_Devisi_' . Str::slug($devisi->nama_devisi) . '_' . Carbon::now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new AbsensiExport($query), $fileName);
    }
}