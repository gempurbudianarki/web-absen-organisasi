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
     * Menampilkan halaman manajemen pengumuman.
     */
    public function index()
    {
        // Ambil data pengumuman, diurutkan dari yang terbaru, dengan relasi ke user dan devisi.
        $pengumumans = Pengumuman::with(['user', 'devisi'])->latest()->paginate(5);
        
        // Ambil data devisi untuk ditampilkan di dropdown form.
        $devisis = Devisi::orderBy('nama_devisi')->get();
        
        return view('admin.pengumuman.index', compact('pengumumans', 'devisis'));
    }

    /**
     * Menyimpan pengumuman baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            // devisi_id boleh kosong (untuk pengumuman umum)
            'devisi_id' => 'nullable|exists:devisis,id',
        ]);

        Pengumuman::create([
            'judul' => $request->judul,
            'isi' => $request->isi,
            'devisi_id' => $request->devisi_id,
            'user_id' => Auth::id(), // Mengambil ID admin yang sedang login secara otomatis
            'waktu_publish' => now(),
        ]);

        return redirect()->route('admin.pengumuman.index')
                         ->with('success', 'Pengumuman berhasil dipublikasikan!');
    }

    /**
     * Menghapus pengumuman dari database.
     */
    public function destroy(Pengumuman $pengumuman)
    {
        // Otorisasi sederhana: pastikan hanya admin yang bisa menghapus (melalui middleware)
        $pengumuman->delete();
        return redirect()->route('admin.pengumuman.index')
                         ->with('success', 'Pengumuman berhasil dihapus.');
    }
}