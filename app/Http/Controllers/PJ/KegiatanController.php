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
     * Menampilkan halaman manajemen kegiatan untuk devisi PJ.
     */
    public function index()
    {
        $devisi = Auth::user()->devisiYangDipimpin;
        if (!$devisi) {
            abort(403, 'Anda tidak ditugaskan sebagai PJ untuk devisi manapun.');
        }
        $kegiatans = Kegiatan::where('devisi_id', $devisi->id)->latest()->paginate(10);
        return view('pj.kegiatan.index', compact('kegiatans', 'devisi'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $devisi = Auth::user()->devisiYangDipimpin;
        return view('pj.kegiatan.create', compact('devisi'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after_or_equal:waktu_mulai',
            'tempat' => 'required|string|max:255',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $devisi = Auth::user()->devisiYangDipimpin;
        $data = $request->except('poster');

        if ($request->hasFile('poster')) {
            $path = $request->file('poster')->store('public/posters');
            $data['poster'] = Storage::url($path);
        }

        $data['devisi_id'] = $devisi->id;
        Kegiatan::create($data);

        return redirect()->route('pj.kegiatan.index')
                         ->with('success', 'Kegiatan baru berhasil dibuat!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kegiatan $kegiatan)
    {
        // Otorisasi: Pastikan PJ hanya bisa edit kegiatan devisinya
        if ($kegiatan->devisi_id !== Auth::user()->devisiYangDipimpin->id) {
            abort(403, 'AKSI TIDAK DIIZINKAN.');
        }
        return view('pj.kegiatan.edit', compact('kegiatan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kegiatan $kegiatan)
    {
        // Otorisasi: Pastikan PJ hanya bisa update kegiatan devisinya
        if ($kegiatan->devisi_id !== Auth::user()->devisiYangDipimpin->id) {
            abort(403, 'AKSI TIDAK DIIZINKAN.');
        }
        
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after_or_equal:waktu_mulai',
            'tempat' => 'required|string|max:255',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except('poster');

        if ($request->hasFile('poster')) {
            if ($kegiatan->poster) {
                Storage::delete(str_replace('/storage', 'public', $kegiatan->poster));
            }
            $path = $request->file('poster')->store('public/posters');
            $data['poster'] = Storage::url($path);
        }

        $kegiatan->update($data);

        return redirect()->route('pj.kegiatan.index')
                         ->with('success', 'Kegiatan berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kegiatan $kegiatan)
    {
        // Otorisasi: Pastikan PJ hanya bisa hapus kegiatan devisinya
        if ($kegiatan->devisi_id !== Auth::user()->devisiYangDipimpin->id) {
            abort(403, 'AKSI TIDAK DIIZINKAN.');
        }

        if ($kegiatan->poster) {
            Storage::delete(str_replace('/storage', 'public', $kegiatan->poster));
        }

        $kegiatan->delete();

        return redirect()->route('pj.kegiatan.index')
                         ->with('success', 'Kegiatan berhasil dihapus.');
    }
}