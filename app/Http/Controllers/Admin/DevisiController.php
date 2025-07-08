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
        //
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
        // 1. Validasi data yang masuk, termasuk pj_id
        $request->validate([
            'nama_devisi' => [
                'required',
                'string',
                'max:255',
                Rule::unique('devisis')->ignore($devisi->id),
            ],
            'deskripsi' => 'nullable|string',
            'pj_id' => 'nullable|exists:users,id', // <-- Validasi baru
        ]);

        // 2. Update data devisi, termasuk pj_id
        $devisi->update([
            'nama_devisi' => $request->nama_devisi,
            'deskripsi' => $request->deskripsi,
            'pj_id' => $request->pj_id, // <-- Field baru untuk di-update
        ]);

        // 3. Kembali ke halaman sebelumnya dengan pesan sukses
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
}