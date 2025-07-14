<?php

namespace App\Http\Controllers\PJ;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\Devisi; // Meskipun tidak digunakan langsung, baik untuk referensi
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class KegiatanController extends Controller
{
    /**
     * Helper untuk mendapatkan devisi yang dipimpin oleh PJ yang sedang login.
     *
     * @return \App\Models\Devisi
     */
    private function getPjDevisi(): Devisi
    {
        $devisi = Auth::user()->devisiYangDipimpin;
        if (!$devisi) {
            abort(403, 'AKSI TIDAK DIIZINKAN: ANDA BUKAN PENANGGUNG JAWAB DEVISI.');
        }
        return $devisi;
    }

    /**
     * Menampilkan halaman manajemen kegiatan untuk devisi PJ.
     * Sekarang juga menampilkan kegiatan umum.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $devisi = $this->getPjDevisi();
        
        // PERBAIKAN LOGIKA: Ambil kegiatan dari devisi PJ DAN kegiatan umum (devisi_id is null)
        $kegiatans = Kegiatan::where('devisi_id', $devisi->id)
                                ->orWhereNull('devisi_id')
                                ->latest()
                                ->paginate(9); // Dibuat 9 agar grid 3x3 terlihat bagus
        
        return view('pj.kegiatan.index', compact('kegiatans', 'devisi'));
    }

    /**
     * Menampilkan form untuk membuat kegiatan baru.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $devisi = $this->getPjDevisi();
        return view('pj.kegiatan.create', compact('devisi'));
    }

    /**
     * Menyimpan kegiatan baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $devisi = $this->getPjDevisi();
        
        $validatedData = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'nullable|date|after_or_equal:waktu_mulai',
            'tempat' => 'required|string|max:255',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('poster')) {
            $path = $request->file('poster')->store('kegiatan-posters', 'public');
            $validatedData['poster'] = $path;
        }

        // Otomatis set devisi_id sesuai devisi PJ.
        $validatedData['devisi_id'] = $devisi->id;
        $kegiatan = Kegiatan::create($validatedData);

        return redirect()->route('pj.kegiatan.index')
                         ->with('success', "Kegiatan '{$kegiatan->judul}' berhasil dibuat!");
    }

    /**
     * Menampilkan form untuk mengedit kegiatan.
     *
     * @param  \App\Models\Kegiatan  $kegiatan
     * @return \Illuminate\View\View
     */
    public function edit(Kegiatan $kegiatan)
    {
        // Otorisasi: Pastikan PJ hanya bisa edit kegiatan devisinya
        $this->authorize('manage', $kegiatan);

        return view('pj.kegiatan.edit', compact('kegiatan'));
    }

    /**
     * Mengupdate data kegiatan di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Kegiatan  $kegiatan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Kegiatan $kegiatan)
    {
        // Otorisasi: Pastikan PJ hanya bisa update kegiatan devisinya
        $this->authorize('manage', $kegiatan);
        
        $validatedData = $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'nullable|date|after_or_equal:waktu_mulai',
            'tempat' => 'required|string|max:255',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('poster')) {
            if ($kegiatan->poster) {
                Storage::disk('public')->delete($kegiatan->poster);
            }
            $path = $request->file('poster')->store('kegiatan-posters', 'public');
            $validatedData['poster'] = $path;
        }

        $kegiatan->update($validatedData);

        return redirect()->route('pj.kegiatan.index')
                         ->with('success', "Kegiatan '{$kegiatan->judul}' berhasil diperbarui!");
    }

    /**
     * Menghapus kegiatan dari database.
     *
     * @param  \App\Models\Kegiatan  $kegiatan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Kegiatan $kegiatan)
    {
        // Otorisasi: Pastikan PJ hanya bisa hapus kegiatan devisinya
        $this->authorize('manage', $kegiatan);

        $namaKegiatan = $kegiatan->judul;

        if ($kegiatan->poster) {
            Storage::disk('public')->delete($kegiatan->poster);
        }

        $kegiatan->delete();

        return redirect()->route('pj.kegiatan.index')
                         ->with('success', "Kegiatan '{$namaKegiatan}' berhasil dihapus.");
    }
}