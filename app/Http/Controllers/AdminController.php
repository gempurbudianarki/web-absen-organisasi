<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Devisi;
use App\Models\Kegiatan;
use App\Models\Absensi;
use App\Models\Pengumuman;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        // --- Data untuk Kartu Statistik ---
        $totalUsers = User::count();
        $totalDevisi = Devisi::count();
        $totalKegiatan = Kegiatan::count();
        $totalPengumumanAktif = Pengumuman::aktif()->count();

        // --- Data untuk Grafik Tren Kehadiran ---
        $startDate = Carbon::now()->subDays(29)->startOfDay();
        $endDate = Carbon::now()->endOfDay();
        
        $kehadiran = Absensi::where('status', 'hadir')
            ->whereBetween('waktu_absen', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get([
                DB::raw('DATE(waktu_absen) as date'),
                DB::raw('COUNT(*) as count')
            ])
            ->pluck('count', 'date');

        $dates = collect();
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dates->put($date->format('Y-m-d'), 0);
        }

        $kehadiranPerHari = $dates->merge($kehadiran);

        $chartLabels = $kehadiranPerHari->keys()->map(function ($date) {
            return Carbon::parse($date)->format('d M');
        });
        $chartData = $kehadiranPerHari->values();

        // --- Data untuk Tabel Kegiatan Akan Datang ---
        $kegiatanAkanDatang = Kegiatan::where('waktu_mulai', '>=', Carbon::now())
            ->orderBy('waktu_mulai', 'asc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalDevisi',
            'totalKegiatan',
            'totalPengumumanAktif',
            'chartLabels',
            'chartData',
            'kegiatanAkanDatang'
        ));
    }
}