<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Pengumuman;
use App\Models\Absensi;
use App\Models\Kegiatan;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        // Hitung total pengguna
        $userCount = User::count();
        
        // Hitung total pengguna dengan peran 'anggota'
        $anggotaCount = User::role('anggota')->count();
        
        // Hitung total pengumuman
        $announcementCount = Pengumuman::count();
        
        // Hitung total record absensi
        $attendanceCount = Absensi::count();
        
        // Hitung total kegiatan
        $kegiatanCount = Kegiatan::count();

        return view('admin.dashboard', compact(
            'userCount',
            'anggotaCount',
            'announcementCount',
            'attendanceCount',
            'kegiatanCount'
        ));
    }
}