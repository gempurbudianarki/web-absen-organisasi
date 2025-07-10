<?php

namespace App\Http\Controllers\PJ;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnggotaController extends Controller
{
    /**
     * Menampilkan daftar anggota dari devisi yang dipimpin oleh PJ.
     */
    public function index()
    {
        $devisi = Auth::user()->devisiYangDipimpin;

        if (!$devisi) {
            abort(403, 'Anda tidak ditugaskan sebagai PJ untuk devisi manapun.');
        }

        // Ambil semua user dengan role 'anggota' yang memiliki devisi_id yang sama
        $anggotas = $devisi->anggota()->role('anggota')->latest()->paginate(15);

        return view('pj.anggota.index', compact('devisi', 'anggotas'));
    }
}