<?php

namespace App\Http\Controllers\PJ;

use App\Http\Controllers\Controller;
use App\Models\Devisi;
use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PengumumanController extends Controller
{
    /**
     * Helper untuk mendapatkan devisi yang dipimpin oleh PJ yang sedang login.
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
     */
    public function index()
    {
        $devisi = $this->getPjDevisi();

        $baseQuery = Pengumuman::where(function ($query) use ($devisi) {
            $query->where('devisi_id', $devisi->id)
                  ->orWhereNull('devisi_id');
        })->with('user')->latest();

        $pengumumanAktif = (clone $baseQuery)->aktif()->paginate(5, ['*'], 'aktif');
        $pengumumanRiwayat = (clone $baseQuery)->riwayat()->paginate(10, ['*'], 'riwayat');
        
        return view('pj.pengumuman.index', compact('pengumumanAktif', 'pengumumanRiwayat', 'devisi'));
    }

    /**
     * Menyimpan pengumuman baru yang dibuat oleh PJ.
     */
    public function store(Request $request)
    {
        $devisi = $this->getPjDevisi();
        
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'publish_at' => 'required|date',
            'expires_at' => 'nullable|date|after_or_equal:publish_at',
        ]);

        Pengumuman::create([
            'judul' => $validated['judul'],
            'isi' => $validated['isi'],
            'devisi_id' => $devisi->id,
            'user_id' => Auth::id(),
            'target' => 'devisi',
            'publish_at' => Carbon::parse($validated['publish_at']),
            'expires_at' => $validated['expires_at'] ? Carbon::parse($validated['expires_at']) : null,
        ]);

        return redirect()->route('pj.pengumuman.index')
                         ->with('success', 'Pengumuman berhasil dipublikasikan untuk devisimu!');
    }

    /**
     * Menghapus pengumuman.
     */
    public function destroy(Pengumuman $pengumuman)
    {
        // Terapkan Policy 'delete' di sini.
        $this->authorize('delete', $pengumuman);
        
        $namaPengumuman = $pengumuman->judul;
        $pengumuman->delete();

        return redirect()->route('pj.pengumuman.index')
                         ->with('success', "Pengumuman '{$namaPengumuman}' berhasil dihapus.");
    }
}