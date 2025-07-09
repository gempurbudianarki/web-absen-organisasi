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
     * Display a listing of the resource.
     */
    public function index()
    {
        $pengumumans = Pengumuman::with(['user', 'devisi'])->latest()->paginate(10);
        $devisis = Devisi::orderBy('nama_devisi')->get();
        
        return view('admin.pengumuman.index', compact('pengumumans', 'devisis'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'devisi_id' => 'nullable|exists:devisis,id',
        ]);

        Pengumuman::create([
            'judul' => $request->judul,
            'isi' => $request->isi,
            'devisi_id' => $request->devisi_id,
            'user_id' => Auth::id(), // ID admin yang sedang login
            'waktu_publish' => now(),
        ]);

        return redirect()->route('admin.pengumuman.index')
                         ->with('success', 'Pengumuman berhasil dipublikasikan!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pengumuman $pengumuman)
    {
        $pengumuman->delete();
        return redirect()->route('admin.pengumuman.index')
                         ->with('success', 'Pengumuman berhasil dihapus.');
    }
}