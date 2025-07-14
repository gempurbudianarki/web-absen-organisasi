<?php

namespace App\Http\Controllers\PJ;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard untuk Penanggung Jawab (PJ) Devisi.
     */
    public function index()
    {
        $user = Auth::user();
        $devisi = $user->devisiYangDipimpin;

        if (!$devisi) {
            abort(403, 'AKSES DITOLAK: ANDA BUKAN PJ DEVISI.');
        }

        // --- Statistik Umum ---
        $anggotaCount = $devisi->anggota()->count();
        $kegiatanCount = $devisi->kegiatan()->count();
        $kegiatans = $devisi->kegiatan()->latest()->take(5)->get();

        // --- STATISTIK BARU UNTUK CHART & KARTU ---
        $anggotaIds = $devisi->anggota()->pluck('id');
        $statsAbsensi = Absensi::whereIn('user_id', $anggotaIds)
            ->where('waktu_absen', '>=', Carbon::now()->subDays(30))
            ->select(
                'status',
                DB::raw('count(*) as total')
            )
            ->groupBy('status')
            ->pluck('total', 'status');

        $totalHadir = $statsAbsensi->get('hadir', 0);
        $totalIzinSakit = $statsAbsensi->get('izin', 0) + $statsAbsensi->get('sakit', 0);
        $totalAlpa = $statsAbsensi->get('alpa', 0);
        $totalRecords = $totalHadir + $totalIzinSakit + $totalAlpa;
        
        $persentaseKehadiran = ($totalRecords > 0) 
            ? round(($totalHadir / $totalRecords) * 100)
            : 0;

        // Data untuk Chart.js
        $chartData = [
            $totalHadir,
            $statsAbsensi->get('izin', 0),
            $statsAbsensi->get('sakit', 0),
            $totalAlpa,
        ];

        return view('pj.dashboard', compact(
            'devisi', 'anggotaCount', 'kegiatanCount', 'kegiatans',
            'persentaseKehadiran', 'chartData'
        ));
    }
}