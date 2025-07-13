<?php

namespace App\Http\Controllers\PJ;

use App\Http\Controllers\Controller;
use App\Models\Devisi; // Import model Devisi
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnggotaController extends Controller
{
    /**
     * Helper untuk mendapatkan devisi yang dipimpin oleh PJ yang sedang login.
     *
     * @return Devisi
     */
    private function getPjDevisi(): Devisi
    {
        $devisi = Auth::user()->devisiYangDipimpin;
        if (!$devisi) {
            abort(403, 'AKSES DITOLAK: ANDA BUKAN PENANGGUNG JAWAB DEVISI.');
        }
        return $devisi;
    }

    /**
     * Menampilkan daftar anggota dari devisi yang dipimpin oleh PJ.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $devisi = $this->getPjDevisi();

        // Ambil semua user dengan role 'anggota' yang memiliki devisi_id yang sama
        // dan gunakan paginasi untuk performa yang lebih baik.
        $anggotas = $devisi->anggota()->role('anggota')->latest()->paginate(15);

        return view('pj.anggota.index', compact('devisi', 'anggotas'));
    }
}