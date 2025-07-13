<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreKegiatanRequest; // Menggunakan Form Request baru
use App\Models\Kegiatan;
use App\Models\Devisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KegiatanController extends Controller
{
    public function index()
    {
        $kegiatans = Kegiatan::with('devisi')->latest()->paginate(10);
        return view('admin.kegiatan.index', compact('kegiatans'));
    }

    public function create(Request $request)
    {
        $devisis = Devisi::orderBy('nama_devisi')->get();
        $selectedDevisiId = $request->query('devisi_id');
        return view('admin.kegiatan.create', compact('devisis', 'selectedDevisiId'));
    }

    public function store(StoreKegiatanRequest $request)
    {
        // Validasi sudah ditangani oleh StoreKegiatanRequest
        $validatedData = $request->validated();

        if ($request->hasFile('poster')) {
            // Simpan gambar dan dapatkan path-nya (relatif terhadap root disk 'public')
            $path = $request->file('poster')->store('kegiatan-posters', 'public');
            $validatedData['poster'] = $path;
        }

        Kegiatan::create($validatedData);

        return redirect()->route('admin.kegiatan.index')->with('success', 'Kegiatan baru berhasil ditambahkan.');
    }

    public function edit(Kegiatan $kegiatan)
    {
        $devisis = Devisi::orderBy('nama_devisi')->get();
        return view('admin.kegiatan.edit', compact('kegiatan', 'devisis'));
    }

    public function update(StoreKegiatanRequest $request, Kegiatan $kegiatan)
    {
        $validatedData = $request->validated();

        if ($request->hasFile('poster')) {
            // Hapus poster lama jika ada
            if ($kegiatan->poster) {
                Storage::disk('public')->delete($kegiatan->poster);
            }
            // Simpan yang baru
            $path = $request->file('poster')->store('kegiatan-posters', 'public');
            $validatedData['poster'] = $path;
        }

        $kegiatan->update($validatedData);

        return redirect()->route('admin.kegiatan.index')->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function destroy(Kegiatan $kegiatan)
    {
        // Hapus poster dari storage jika ada
        if ($kegiatan->poster) {
            Storage::disk('public')->delete($kegiatan->poster);
        }
        
        $kegiatan->delete();
        return redirect()->route('admin.kegiatan.index')->with('success', 'Kegiatan berhasil dihapus.');
    }
}