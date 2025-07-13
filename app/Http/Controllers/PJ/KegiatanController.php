<?php

namespace App\Http\Controllers\PJ;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\Devisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class KegiatanController extends Controller
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
            // Abort jika user bukan PJ dari devisi manapun.
            abort(403, 'AKSI TIDAK DIIZINKAN: ANDA BUKAN PENANGGUNG JAWAB DIVISI.');
        }
        return $devisi;
    }

    /**
     * Menampilkan halaman manajemen kegiatan untuk devisi PJ.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $devisi = $this->getPjDevisi();
        $kegiatans = Kegiatan::where('devisi_id', $devisi->id)->latest()->paginate(10);
        
        return view('pj.kegiatan.index', compact('kegiatans', 'devisi'));
    }
    
    /**
     * Menampilkan dashboard khusus untuk PJ.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        $devisi = $this->getPjDevisi();
        $anggotaCount = $devisi->anggota()->count();
        $kegiatanCount = $devisi->kegiatan()->count();
        
        // Ambil 5 kegiatan terbaru dari devisi tersebut
        $kegiatans = $devisi->kegiatan()->latest()->take(5)->get();

        return view('pj.dashboard', compact('devisi', 'anggotaCount', 'kegiatanCount', 'kegiatans'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $devisi = $this->getPjDevisi();
        return view('pj.kegiatan.create', compact('devisi'));
    }

    /**
     * Store a newly created resource in storage.
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
            // PERBAIKAN: Simpan path relatif, bukan URL.
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
     * Show the form for editing the specified resource.
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
     * Update the specified resource in storage.
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
     * Remove the specified resource from storage.
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