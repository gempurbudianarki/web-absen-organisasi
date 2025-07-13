<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AbsensiExport;
use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Devisi;
use App\Models\Kegiatan;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AbsensiController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(6)->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());
        $selectedKegiatanId = $request->input('kegiatan_id');
        $selectedDevisiId = $request->input('devisi_id');

        $query = Absensi::with(['user.devisi', 'kegiatan'])
            ->whereBetween('waktu_absen', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if ($selectedDevisiId) {
            $query->whereHas('user', fn($q) => $q->where('devisi_id', $selectedDevisiId));
        }
        if ($selectedKegiatanId) {
            $query->where('kegiatan_id', $selectedKegiatanId);
        }

        $statsQuery = clone $query;
        $stats = $statsQuery->select('status', DB::raw('count(*) as total'))
                            ->groupBy('status')
                            ->pluck('total', 'status');

        $totalHadir = $stats->get('hadir', 0);
        $totalIzin = $stats->get('izin', 0);
        $totalSakit = $stats->get('sakit', 0);
        $totalAlpa = $stats->get('alpa', 0);
        $totalAbsen = $stats->sum();
        $persentaseKehadiran = ($totalAbsen > 0) ? round((($totalHadir + $totalIzin + $totalSakit) / $totalAbsen) * 100, 1) : 0;

        $trendQuery = clone $query;
        $attendanceTrend = $trendQuery
            ->whereIn('status', ['hadir', 'izin', 'sakit'])
            ->select(DB::raw('DATE(waktu_absen) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->pluck('count', 'date');

        $trendLabels = [];
        $trendData = [];
        $period = Carbon::parse($startDate)->toPeriod($endDate);
        foreach ($period as $date) {
            $formattedDate = $date->format('Y-m-d');
            $trendLabels[] = $date->isoFormat('D MMM');
            $trendData[] = $attendanceTrend->get($formattedDate, 0);
        }
        
        $absensiLogs = $query->latest('waktu_absen')->paginate(20);

        $devisis = Devisi::orderBy('nama_devisi')->get();
        $kegiatans = Kegiatan::orderBy('judul')->get();

        return view('admin.absensi.index', compact(
            'absensiLogs', 'startDate', 'endDate', 'devisis', 'kegiatans',
            'selectedDevisiId', 'selectedKegiatanId',
            'totalHadir', 'totalIzin', 'totalSakit', 'totalAlpa', 'persentaseKehadiran',
            'trendLabels', 'trendData'
        ));
    }

    public function create()
    {
        $kegiatans = Kegiatan::where('waktu_mulai', '>=', now()->subDays(30))->orderBy('waktu_mulai', 'desc')->get();
        $users = User::role('anggota')->orderBy('name')->get();
        return view('admin.absensi.create', compact('kegiatans', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kegiatan_id' => 'required|exists:kegiatans,id',
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:hadir,izin,sakit,alpa',
            'keterangan' => 'nullable|string|max:255',
            'waktu_absen' => 'required|date',
        ]);
        
        $existing = Absensi::where('kegiatan_id', $request->kegiatan_id)->where('user_id', $request->user_id)->first();
        if ($existing) {
            return back()->with('error', 'Anggota ini sudah memiliki catatan absensi untuk kegiatan tersebut.');
        }

        Absensi::create($request->all());
        return redirect()->route('admin.absensi.index')->with('success', 'Data absensi manual berhasil disimpan.');
    }

    public function show(Kegiatan $kegiatan)
    {
        $absensi = Absensi::where('kegiatan_id', $kegiatan->id)->with('user')->get();
        $absenUserIds = $absensi->pluck('user_id')->toArray();
        $peserta = User::role('anggota')->whereNotIn('id', $absenUserIds)->orderBy('name')->get();
        return view('admin.absensi.show', compact('kegiatan', 'absensi', 'peserta'));
    }
    
    public function qr(Kegiatan $kegiatan)
    {
        $qrData = json_encode(['kegiatan_id' => $kegiatan->id]);
        $qrCode = QrCode::size(300)->margin(10)->generate($qrData);
        return view('admin.absensi.qr_display', compact('kegiatan', 'qrCode'));
    }

    public function destroy(Absensi $absensi)
    {
        $absensi->delete();
        return back()->with('success', 'Data absensi berhasil dihapus.');
    }

    /**
     * Menangani permintaan ekspor data absensi ke Excel.
     */
    public function export(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $selectedKegiatanId = $request->input('kegiatan_id');
        $selectedDevisiId = $request->input('devisi_id');

        $query = Absensi::with(['user.devisi', 'kegiatan']);

        if ($startDate && $endDate) {
            $query->whereBetween('waktu_absen', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }
        if ($selectedDevisiId) {
            $query->whereHas('user', fn($q) => $q->where('devisi_id', $selectedDevisiId));
        }
        if ($selectedKegiatanId) {
            $query->where('kegiatan_id', $selectedKegiatanId);
        }
        
        $query->latest('waktu_absen');
        $fileName = 'Laporan_Absensi_LDK_' . Carbon::now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new AbsensiExport($query), $fileName);
    }
}