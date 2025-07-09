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
     * Menampilkan halaman manajemen devisi.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Eager load relasi 'pj' untuk optimasi query (menghindari N+1 problem)
        $devisis = Devisi::with('pj')->latest()->get();

        // Ambil semua user yang memiliki peran 'pj' sebagai calon penanggung jawab
        $calon_pj = User::role('pj')->get();

        return view('admin.devisi.index', compact('devisis', 'calon_pj'));
    }

    /**
     * Menyimpan devisi baru ke dalam database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_devisi' => 'required|string|max:255|unique:devisis,nama_devisi',
            'deskripsi' => 'nullable|string',
        ]);

        Devisi::create($request->all());

        return redirect()->route('admin.devisi.index')
                         ->with('success', 'Devisi baru berhasil ditambahkan!');
    }

    /**
     * Mengupdate data devisi yang sudah ada di database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Devisi  $devisi
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Devisi $devisi)
    {
        $request->validate([
            'nama_devisi' => [
                'required',
                'string',
                'max:255',
                // Pastikan nama devisi unik, kecuali untuk devisi yang sedang diedit
                Rule::unique('devisis')->ignore($devisi->id),
            ],
            'deskripsi' => 'nullable|string',
            // Pastikan pj_id yang dikirim ada di tabel users
            'pj_id' => 'nullable|exists:users,id',
        ]);

        // Update data devisi dengan data dari request
        $devisi->update($request->all());

        return redirect()->route('admin.devisi.index')
                         ->with('success', 'Data devisi berhasil diperbarui!');
    }

    /**
     * Menghapus devisi dari database.
     *
     * @param  \App\Models\Devisi  $devisi
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Devisi $devisi)
    {
        $devisi->delete();

        return redirect()->route('admin.devisi.index')
                         ->with('success', 'Devisi berhasil dihapus!');
    }
}