<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\Devisi;
use Illuminate\Http\Request;

class KegiatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kegiatans = Kegiatan::with('devisi')->latest()->paginate(10);
        return view('admin.kegiatan.index', compact('kegiatans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $devisis = Devisi::orderBy('nama_devisi')->get();
        // Ambil devisi_id dari request jika ada (untuk tombol shortcut dari halaman detail devisi)
        $selectedDevisiId = $request->query('devisi_id');

        return view('admin.kegiatan.create', compact('devisis', 'selectedDevisiId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'devisi_id' => 'required|exists:devisis,id',
            'tempat' => 'required|string|max:255',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'nullable|date|after_or_equal:waktu_mulai',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validasi untuk gambar
        ]);

        $data = $request->all();

        if ($request->hasFile('poster')) {
            // Simpan gambar dan dapatkan path-nya
            $path = $request->file('poster')->store('posters', 'public');
            $data['poster'] = $path;
        }

        Kegiatan::create($data);

        return redirect()->route('admin.kegiatan.index')->with('success', 'Kegiatan baru berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Kegiatan $kegiatan)
    {
        // Untuk masa depan, kita bisa buat halaman detail kegiatan di sini
        return redirect()->route('admin.kegiatan.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kegiatan $kegiatan)
    {
        $devisis = Devisi::orderBy('nama_devisi')->get();
        return view('admin.kegiatan.edit', compact('kegiatan', 'devisis'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kegiatan $kegiatan)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'devisi_id' => 'required|exists:devisis,id',
            'tempat' => 'required|string|max:255',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'nullable|date|after_or_equal:waktu_mulai',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('poster')) {
            // Hapus poster lama jika ada
            if ($kegiatan->poster) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($kegiatan->poster);
            }
            $path = $request->file('poster')->store('posters', 'public');
            $data['poster'] = $path;
        }

        $kegiatan->update($data);

        return redirect()->route('admin.kegiatan.index')->with('success', 'Kegiatan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kegiatan $kegiatan)
    {
        // Hapus poster dari storage jika ada
        if ($kegiatan->poster) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($kegiatan->poster);
        }
        
        $kegiatan->delete();
        return redirect()->route('admin.kegiatan.index')->with('success', 'Kegiatan berhasil dihapus.');
    }
}