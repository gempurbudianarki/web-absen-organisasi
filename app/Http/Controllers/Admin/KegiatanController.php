<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use App\Models\Devisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class KegiatanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kegiatans = Kegiatan::with('devisi')->latest()->paginate(10);
        $devisis = Devisi::orderBy('nama_devisi', 'asc')->get();
        return view('admin.kegiatan.index', compact('kegiatans', 'devisis'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return redirect()->route('admin.kegiatan.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'devisi_id' => 'required|exists:devisis,id',
            'deskripsi' => 'required|string',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after_or_equal:waktu_mulai',
            'tempat' => 'required|string|max:255',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except('poster');

        if ($request->hasFile('poster')) {
            $path = $request->file('poster')->store('public/posters');
            $data['poster'] = Storage::url($path);
        }

        Kegiatan::create($data);

        return redirect()->route('admin.kegiatan.index')
                         ->with('success', 'Kegiatan baru berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Kegiatan $kegiatan)
    {
        return redirect()->route('admin.kegiatan.edit', $kegiatan->id);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kegiatan $kegiatan)
    {
        $devisis = Devisi::orderBy('nama_devisi', 'asc')->get();
        return view('admin.kegiatan.edit', compact('kegiatan', 'devisis'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kegiatan $kegiatan)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'devisi_id' => 'required|exists:devisis,id',
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

        return redirect()->route('admin.kegiatan.index')
                         ->with('success', 'Kegiatan berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kegiatan $kegiatan)
    {
        // Hapus poster dari storage jika ada
        if ($kegiatan->poster) {
            Storage::delete(str_replace('/storage', 'public', $kegiatan->poster));
        }

        // Hapus record dari database
        $kegiatan->delete();

        return redirect()->route('admin.kegiatan.index')
                         ->with('success', 'Kegiatan berhasil dihapus!');
    }
}