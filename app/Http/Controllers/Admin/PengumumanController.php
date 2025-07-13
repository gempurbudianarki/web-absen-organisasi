<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use App\Models\Devisi;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PengumumanController extends Controller
{
    public function index()
    {
        $pengumumanAktif = Pengumuman::with('user', 'devisi')->latest()->aktif()->paginate(5, ['*'], 'aktif');
        $pengumumanRiwayat = Pengumuman::with('user', 'devisi')->latest()->riwayat()->paginate(5, ['*'], 'riwayat');
        $devisis = Devisi::orderBy('nama_devisi')->get();

        return view('admin.pengumuman.index', compact('pengumumanAktif', 'pengumumanRiwayat', 'devisis'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string', // Menggunakan 'isi'
            'target' => 'required|in:semua,devisi',
            'devisi_id' => 'required_if:target,devisi|nullable|exists:devisis,id',
            'publish_at' => 'required|date',
            'expires_at' => 'nullable|date|after_or_equal:publish_at',
        ]);

        Pengumuman::create([
            'user_id' => auth()->id(),
            'judul' => $validated['judul'],
            'isi' => $validated['isi'],
            'target' => $validated['target'],
            'devisi_id' => $validated['target'] === 'devisi' ? $validated['devisi_id'] : null,
            'publish_at' => Carbon::parse($validated['publish_at']),
            'expires_at' => $validated['expires_at'] ? Carbon::parse($validated['expires_at']) : null,
        ]);

        return redirect()->route('admin.pengumuman.index')->with('success', 'Pengumuman berhasil diterbitkan.');
    }

    public function destroy(Pengumuman $pengumuman)
    {
        $pengumuman->delete();
        return redirect()->route('admin.pengumuman.index')->with('success', 'Pengumuman berhasil dihapus.');
    }
}