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
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AbsensiController extends Controller
{
    /**
     * Menampilkan halaman utama laporan absensi dengan filter dan statistik.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'kegiatan_id' => 'nullable|exists:kegiatans,id',
            'devisi_id' => 'nullable|exists:devisis,id',
        ]);

        $startDate = $request->input('start_date', Carbon::now()->startOfWeek()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfWeek()->toDateString());
        $selectedKegiatanId = $request->input('kegiatan_id');
        $selectedDevisiId = $request->input('devisi_id');

        // Query utama untuk log absensi
        $query = Absensi::with(['user.devisi', 'kegiatan'])
            ->whereBetween('waktu_absen', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        if ($selectedDevisiId) {
            $query->whereHas('user', fn($q) => $q->where('devisi_id', $selectedDevisiId));
        }
        if ($selectedKegiatanId) {
            $query->where('kegiatan_id', $selectedKegiatanId);
        }

        // Statistik Kehadiran (Lebih efisien dengan satu query)
        $stats = (clone $query)
            ->select(
                DB::raw("SUM(CASE WHEN status = 'hadir' THEN 1 ELSE 0 END) as totalHadir"),
                DB::raw("SUM(CASE WHEN status = 'izin' THEN 1 ELSE 0 END) as totalIzin"),
                DB::raw("SUM(CASE WHEN status = 'sakit' THEN 1 ELSE 0 END) as totalSakit"),
                DB::raw("SUM(CASE WHEN status = 'alpa' THEN 1 ELSE 0 END) as totalAlpa")
            )
            ->first();

        $totalPeserta = ($stats->totalHadir + $stats->totalIzin + $stats->totalSakit + $stats->totalAlpa);
        $persentaseKehadiran = ($totalPeserta > 0) ? round((($stats->totalHadir) / $totalPeserta) * 100, 1) : 0;
        
        // Data untuk Grafik Tren Kehadiran
        $attendanceTrend = (clone $query)
            ->whereIn('status', ['hadir']) // Hanya tren yang hadir
            ->select(DB::raw('DATE(waktu_absen) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->pluck('count', 'date');

        $trendLabels = [];
        $trendData = [];
        $period = Carbon::parse($startDate)->toPeriod($endDate);
        foreach ($period as $date) {
            $formattedDate = $date->format('Y-m-d');
            $trendLabels[] = $date->isoFormat('D MMM');
            $trendData[] = $attendanceTrend->get($formattedDate, 0);
        }
        
        $absensiLogs = $query->latest('waktu_absen')->paginate(20)->withQueryString();

        $devisis = Devisi::orderBy('nama_devisi')->get();
        $kegiatans = Kegiatan::orderBy('judul')->get();

        return view('admin.absensi.index', compact(
            'absensiLogs', 'startDate', 'endDate', 'devisis', 'kegiatans',
            'selectedDevisiId', 'selectedKegiatanId',
            'stats', 'persentaseKehadiran', 'trendLabels', 'trendData'
        ));
    }

    /**
     * Menampilkan form untuk input absensi manual.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $kegiatans = Kegiatan::where('waktu_mulai', '>=', now()->subDays(30))->orderBy('waktu_mulai', 'desc')->get();
        $users = User::role('anggota')->orderBy('name')->get();
        return view('admin.absensi.create', compact('kegiatans', 'users'));
    }

    /**
     * Menyimpan data absensi manual ke database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'kegiatan_id' => 'required|exists:kegiatans,id',
            'user_id' => [
                'required',
                'exists:users,id',
                // Validasi anti duplikat
                Rule::unique('absensis')->where(function ($query) use ($request) {
                    return $query->where('kegiatan_id', $request->kegiatan_id)
                                 ->where('user_id', $request->user_id);
                }),
            ],
            'status' => 'required|in:hadir,izin,sakit,alpa',
            'keterangan' => 'nullable|string|max:255',
            'waktu_absen' => 'required|date',
        ], [
            'user_id.unique' => 'Anggota ini sudah memiliki catatan absensi untuk kegiatan tersebut.'
        ]);
        
        Absensi::create($request->all());
        
        return redirect()->route('admin.absensi.index')->with('success', 'Data absensi manual berhasil disimpan.');
    }

    /**
     * Menampilkan detail absensi per kegiatan.
     *
     * @param \App\Models\Kegiatan $kegiatan
     * @return \Illuminate\View\View
     */
    public function show(Kegiatan $kegiatan)
    {
        $absensi = Absensi::where('kegiatan_id', $kegiatan->id)->with('user')->get();
        $absenUserIds = $absensi->pluck('user_id')->toArray();
        $peserta = User::role('anggota')->whereNotIn('id', $absenUserIds)->orderBy('name')->get();
        return view('admin.absensi.show', compact('kegiatan', 'absensi', 'peserta'));
    }
    
    /**
     * Menampilkan halaman dengan QR Code untuk sesi absensi.
     *
     * @param \App\Models\Kegiatan $kegiatan
     * @return \Illuminate\View\View
     */
    public function qr(Kegiatan $kegiatan)
    {
        $qrData = json_encode(['kegiatan_id' => $kegiatan->id]);
        $qrCode = QrCode::size(300)->margin(10)->generate($qrData);
        return view('admin.absensi.qr_display', compact('kegiatan', 'qrCode'));
    }

    /**
     * Menghapus data absensi.
     *
     * @param \App\Models\Absensi $absensi
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Absensi $absensi)
    {
        $absensi->delete();
        return back()->with('success', 'Data absensi berhasil dihapus.');
    }

    /**
     * Menangani permintaan ekspor data absensi ke Excel.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Request $request)
    {
        // Logika query ekspor disederhanakan dan diserahkan ke class AbsensiExport
        $query = Absensi::query()
            ->with(['user.devisi', 'kegiatan'])
            ->when($request->start_date && $request->end_date, function ($q) use ($request) {
                $q->whereBetween('waktu_absen', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
            })
            ->when($request->devisi_id, function ($q) use ($request) {
                $q->whereHas('user', fn($sq) => $sq->where('devisi_id', $request->devisi_id));
            })
            ->when($request->kegiatan_id, function ($q) use ($request) {
                $q->where('kegiatan_id', $request->kegiatan_id);
            })
            ->latest('waktu_absen');
            
        $fileName = 'Laporan_Absensi_LDK_' . Carbon::now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new AbsensiExport($query), $fileName);
    }
}