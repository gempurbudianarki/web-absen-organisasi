<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use App\Models\Devisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengumumanController extends Controller
{
    /**
     * Menampilkan halaman utama manajemen pengumuman.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Eager load relasi 'user' dan 'devisi' untuk mencegah N+1 query problem.
        $pengumumans = Pengumuman::with(['user', 'devisi'])->latest()->paginate(5);
        
        // Ambil data devisi untuk ditampilkan di dropdown form.
        $devisis = Devisi::orderBy('nama_devisi')->get();
        
        return view('admin.pengumuman.index', compact('pengumumans', 'devisis'));
    }

    /**
     * Menyimpan pengumuman baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            // Validasi diperketat: devisi_id boleh kosong, tapi jika diisi, harus ada di tabel devisis.
            'devisi_id' => 'nullable|exists:devisis,id',
        ]);

        $pengumuman = Pengumuman::create([
            'judul' => $request->judul,
            'isi' => $request->isi,
            'devisi_id' => $request->devisi_id,
            'user_id' => Auth::id(), // Mengambil ID admin yang sedang login secara otomatis.
            'waktu_publish' => now(),
        ]);

        return redirect()->route('admin.pengumuman.index')
                         ->with('success', "Pengumuman '{$pengumuman->judul}' berhasil dipublikasikan!");
    }

    /**
     * Menghapus pengumuman dari database.
     *
     * @param  \App\Models\Pengumuman  $pengumuman
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Pengumuman $pengumuman)
    {
        // Otorisasi sederhana sudah ditangani oleh middleware 'role:admin' di level rute.
        $namaPengumuman = $pengumuman->judul;
        $pengumuman->delete();

        return redirect()->route('admin.pengumuman.index')
                         ->with('success', "Pengumuman '{$namaPengumuman}' berhasil dihapus.");
    }
}