<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreKegiatanRequest; // Menggunakan Form Request yang sudah ada
use App\Models\Kegiatan;
use App\Models\Devisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KegiatanController extends Controller
{
    /**
     * Menampilkan halaman utama manajemen kegiatan.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $kegiatans = Kegiatan::with('devisi')->latest()->paginate(10);
        return view('admin.kegiatan.index', compact('kegiatans'));
    }

    /**
     * Menampilkan form untuk membuat kegiatan baru.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        $devisis = Devisi::orderBy('nama_devisi')->get();
        // Memungkinkan admin membuat kegiatan langsung dari halaman devisi
        $selectedDevisiId = $request->query('devisi_id'); 
        return view('admin.kegiatan.create', compact('devisis', 'selectedDevisiId'));
    }

    /**
     * Menyimpan kegiatan baru ke database.
     *
     * @param  \App\Http\Requests\StoreKegiatanRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreKegiatanRequest $request)
    {
        // Validasi sudah ditangani oleh StoreKegiatanRequest
        $validatedData = $request->validated();

        if ($request->hasFile('poster')) {
            // PERBAIKAN: Simpan path relatif, bukan URL lengkap.
            $path = $request->file('poster')->store('kegiatan-posters', 'public');
            $validatedData['poster'] = $path;
        }

        $kegiatan = Kegiatan::create($validatedData);

        return redirect()->route('admin.kegiatan.index')->with('success', "Kegiatan '{$kegiatan->judul}' berhasil ditambahkan.");
    }

    /**
     * Menampilkan form untuk mengedit kegiatan.
     *
     * @param  \App\Models\Kegiatan  $kegiatan
     * @return \Illuminate\View\View
     */
    public function edit(Kegiatan $kegiatan)
    {
        $devisis = Devisi::orderBy('nama_devisi')->get();
        return view('admin.kegiatan.edit', compact('kegiatan', 'devisis'));
    }

    /**
     * Mengupdate data kegiatan di database.
     *
     * @param  \App\Http\Requests\StoreKegiatanRequest  $request
     * @param  \App\Models\Kegiatan  $kegiatan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(StoreKegiatanRequest $request, Kegiatan $kegiatan)
    {
        $validatedData = $request->validated();

        if ($request->hasFile('poster')) {
            // Hapus poster lama jika ada, menggunakan path yang tersimpan.
            if ($kegiatan->poster) {
                Storage::disk('public')->delete($kegiatan->poster);
            }
            // Simpan yang baru dan dapatkan path relatifnya.
            $path = $request->file('poster')->store('kegiatan-posters', 'public');
            $validatedData['poster'] = $path;
        }

        $kegiatan->update($validatedData);

        return redirect()->route('admin.kegiatan.index')->with('success', "Kegiatan '{$kegiatan->judul}' berhasil diperbarui.");
    }

    /**
     * Menghapus kegiatan dari database.
     *
     * @param  \App\Models\Kegiatan  $kegiatan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Kegiatan $kegiatan)
    {
        $namaKegiatan = $kegiatan->judul;
        // Hapus poster dari storage jika ada.
        if ($kegiatan->poster) {
            Storage::disk('public')->delete($kegiatan->poster);
        }
        
        $kegiatan->delete();
        
        return redirect()->route('admin.kegiatan.index')->with('success', "Kegiatan '{$namaKegiatan}' berhasil dihapus.");
    }
}