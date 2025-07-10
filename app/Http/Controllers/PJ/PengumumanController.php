<?php

namespace App\Http\Controllers\PJ;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengumumanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $devisi = Auth::user()->devisiYangDipimpin;
        if (!$devisi) {
            abort(403, 'Anda tidak ditugaskan sebagai PJ untuk devisi manapun.');
        }

        $pengumumans = Pengumuman::where('devisi_id', $devisi->id)
                                 ->with('user')
                                 ->latest()
                                 ->paginate(10);
        
        return view('pj.pengumuman.index', compact('pengumumans', 'devisi'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
        ]);

        $devisi = Auth::user()->devisiYangDipimpin;
        if (!$devisi) {
            abort(403, 'Anda tidak bisa membuat pengumuman tanpa devisi.');
        }

        Pengumuman::create([
            'judul' => $request->judul,
            'isi' => $request->isi,
            'devisi_id' => $devisi->id, // Otomatis set devisi PJ
            'user_id' => Auth::id(),
            'waktu_publish' => now(),
        ]);

        return redirect()->route('pj.pengumuman.index')
                         ->with('success', 'Pengumuman berhasil dipublikasikan untuk devisi Anda!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pengumuman $pengumuman)
    {
        $devisi = Auth::user()->devisiYangDipimpin;

        // Otorisasi: Pastikan PJ hanya bisa hapus pengumuman devisinya sendiri
        if (!$devisi || $pengumuman->devisi_id !== $devisi->id) {
            abort(403, 'AKSI TIDAK DIIZINKAN.');
        }

        $pengumuman->delete();
        return redirect()->route('pj.pengumuman.index')
                         ->with('success', 'Pengumuman berhasil dihapus.');
    }
}