<?php

namespace App\Http\Controllers\PJ;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Menampilkan dashboard untuk Penanggung Jawab (PJ) Devisi.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        $devisi = $user->devisiYangDipimpin;

        // Jika user yang login bukan PJ, hentikan proses.
        if (!$devisi) {
            abort(403, 'AKSES DITOLAK: ANDA BUKAN PJ DEVISI.');
        }

        // Ambil data statistik khusus untuk devisi ini.
        $anggotaCount = $devisi->anggota()->count();
        $kegiatanCount = $devisi->kegiatan()->count();
        
        // Ambil 5 kegiatan terbaru dari devisi tersebut untuk ditampilkan di dashboard.
        $kegiatans = $devisi->kegiatan()->latest()->take(5)->get();

        return view('pj.dashboard', compact('devisi', 'anggotaCount', 'kegiatanCount', 'kegiatans'));
    }
}