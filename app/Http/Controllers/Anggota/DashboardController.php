<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Ambil kegiatan yang akan datang dari devisi anggota atau kegiatan umum
        $kegiatans = Kegiatan::where(function ($query) use ($user) {
            $query->where('devisi_id', $user->devisi_id)
                  ->orWhereNull('devisi_id');
        })
        ->where('waktu_mulai', '>=', now())
        ->orderBy('waktu_mulai', 'asc')
        ->take(5)
        ->get();

        // Ambil pengumuman terbaru untuk devisi anggota atau pengumuman umum
        $pengumumans = Pengumuman::where(function ($query) use ($user) {
            $query->where('devisi_id', $user->devisi_id)
                  ->orWhereNull('devisi_id');
        })
        ->latest()
        ->take(3)
        ->get();
        
        return view('anggota.dashboard', compact('kegiatans', 'pengumumans'));
    }
}