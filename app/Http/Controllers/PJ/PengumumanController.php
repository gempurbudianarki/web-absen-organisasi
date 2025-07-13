<?php

namespace App\Http\Controllers\PJ;

use App\Http\Controllers\Controller;
use App\Models\Devisi; // Import model Devisi
use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengumumanController extends Controller
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
     * Menampilkan daftar pengumuman untuk devisi yang dipimpin PJ.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $devisi = $this->getPjDevisi();

        $pengumumans = Pengumuman::where('devisi_id', $devisi->id)
                                 ->with('user') // Eager load pembuat pengumuman
                                 ->latest()
                                 ->paginate(10);
        
        return view('pj.pengumuman.index', compact('pengumumans', 'devisi'));
    }

    /**
     * Menyimpan pengumuman baru yang dibuat oleh PJ.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $devisi = $this->getPjDevisi();
        
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
        ]);

        Pengumuman::create([
            'judul' => $validated['judul'],
            'isi' => $validated['isi'],
            'devisi_id' => $devisi->id, // Otomatis set devisi_id sesuai devisi PJ
            'user_id' => Auth::id(),    // Set user_id dari PJ yang sedang login
            'waktu_publish' => now(),
        ]);

        return redirect()->route('pj.pengumuman.index')
                         ->with('success', 'Pengumuman berhasil dipublikasikan untuk devisi Anda!');
    }

    /**
     * Menghapus pengumuman.
     *
     * @param  \App\Models\Pengumuman  $pengumuman
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Pengumuman $pengumuman)
    {
        $devisi = $this->getPjDevisi();

        // Otorisasi: Pastikan PJ hanya bisa hapus pengumuman dari devisinya sendiri.
        if ($pengumuman->devisi_id !== $devisi->id) {
            abort(403, 'AKSI TIDAK DIIZINKAN.');
        }
        
        $namaPengumuman = $pengumuman->judul;
        $pengumuman->delete();

        return redirect()->route('pj.pengumuman.index')
                         ->with('success', "Pengumuman '{$namaPengumuman}' berhasil dihapus.");
    }
}