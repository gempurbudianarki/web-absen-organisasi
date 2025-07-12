<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Devisi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DevisiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $devisis = Devisi::with('pj')->latest()->get();
        $calon_pj = User::role('pj')->get();
        return view('admin.devisi.index', compact('devisis', 'calon_pj'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_devisi' => 'required|string|max:255|unique:devisis,nama_devisi',
            'deskripsi' => 'nullable|string',
        ]);

        Devisi::create($request->all());

        return redirect()->route('admin.devisi.index')->with('success', 'Devisi baru berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Devisi $devisi)
    {
        // Eager load relasi untuk efisiensi
        $devisi->load('pj', 'anggota', 'kegiatan');
        
        // Ambil user dengan role 'anggota' yang belum punya devisi
        $calon_anggota = User::role('anggota')->whereNull('devisi_id')->orderBy('name')->get();

        return view('admin.devisi.show', compact('devisi', 'calon_anggota'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Devisi $devisi)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Devisi $devisi)
    {
        $request->validate([
            'nama_devisi' => [
                'required',
                'string',
                'max:255',
                Rule::unique('devisis')->ignore($devisi->id),
            ],
            'deskripsi' => 'nullable|string',
            'pj_id' => 'nullable|exists:users,id',
        ]);

        $devisi->update([
            'nama_devisi' => $request->nama_devisi,
            'deskripsi' => $request->deskripsi,
            'pj_id' => $request->pj_id,
        ]);

        return redirect()->route('admin.devisi.index')->with('success', 'Data devisi berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Devisi $devisi)
    {
        $devisi->delete();
        return redirect()->route('admin.devisi.index')->with('success', 'Devisi berhasil dihapus!');
    }

    // --- START OF NEW CODE ---
    /**
     * Add a member to the specified devisi.
     */
    public function addMember(Request $request, Devisi $devisi)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::find($request->user_id);
        $user->devisi_id = $devisi->id;
        $user->save();

        return back()->with('success', $user->name . ' berhasil ditambahkan ke devisi ' . $devisi->nama_devisi);
    }

    /**
     * Remove a member from the specified devisi.
     */
    public function removeMember(Request $request, Devisi $devisi, User $user)
    {
        // Pastikan user tersebut memang anggota devisi ini
        if ($user->devisi_id == $devisi->id) {
            $user->devisi_id = null;
            $user->save();
            return back()->with('success', $user->name . ' berhasil dikeluarkan dari devisi.');
        }

        return back()->with('error', 'Aksi gagal: User bukan anggota devisi ini.');
    }
    // --- END OF NEW CODE ---
}