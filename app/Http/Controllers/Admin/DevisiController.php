<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Devisi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DevisiController extends Controller
{
    /**
     * Menampilkan halaman utama manajemen devisi.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $devisis = Devisi::withCount('anggota')->with('pj')->latest()->get();

        // Query yang lebih efisien untuk calon PJ:
        // User yang BUKAN admin dan BELUM menjadi PJ di devisi manapun.
        $calon_pj = User::whereHas('roles', function ($query) {
            $query->where('name', '!=', 'admin');
        })
        ->whereDoesntHave('devisiYangDipimpin')
        ->orderBy('name')
        ->get();

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

        return redirect()->route('admin.devisi.index')->with('success', 'Devisi baru berhasil ditambahkan!');
    }

    /**
     * Menampilkan halaman detail untuk sebuah devisi.
     *
     * @param  \App\Models\Devisi  $devisi
     * @return \Illuminate\View\View
     */
    public function show(Devisi $devisi)
    {
        // Eager load relasi untuk performa lebih baik
        $devisi->load('pj', 'anggota', 'kegiatan');
        
        // Calon anggota: user dengan role 'anggota' yang belum punya devisi.
        $calon_anggota = User::role('anggota')->whereNull('devisi_id')->orderBy('name')->get();

        return view('admin.devisi.show', compact('devisi', 'calon_anggota'));
    }

    /**
     * Mengupdate data devisi dan menunjuk PJ baru secara atomik.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Devisi  $devisi
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Devisi $devisi)
    {
        $request->validate([
            'nama_devisi' => ['required', 'string', 'max:255', Rule::unique('devisis')->ignore($devisi->id)],
            'deskripsi' => 'nullable|string',
            'pj_id' => 'nullable|exists:users,id',
        ]);

        DB::transaction(function () use ($request, $devisi) {
            $oldPjId = $devisi->pj_id;
            $newPjId = $request->pj_id;

            // Update informasi dasar devisi
            $devisi->update($request->only('nama_devisi', 'deskripsi', 'pj_id'));

            // Jika ada perubahan PJ, lakukan sinkronisasi role
            if ($oldPjId !== $newPjId) {
                // 1. Kembalikan role PJ lama menjadi 'anggota' jika ada
                if ($oldPjId && $oldPj = User::find($oldPjId)) {
                    $oldPj->syncRoles('anggota');
                }
                // 2. Tetapkan user baru sebagai 'pj'
                if ($newPjId && $newPj = User::find($newPjId)) {
                    $newPj->syncRoles('pj');
                }
            }
        });

        return redirect()->route('admin.devisi.index')->with('success', "Data devisi '{$devisi->nama_devisi}' berhasil diperbarui!");
    }

    /**
     * Menghapus devisi dan menangani relasi secara aman.
     *
     * @param  \App\Models\Devisi  $devisi
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Devisi $devisi)
    {
        $namaDevisi = $devisi->nama_devisi;
        DB::transaction(function () use ($devisi) {
            // 1. Lepaskan semua anggota dari devisi ini (set devisi_id menjadi null)
            User::where('devisi_id', $devisi->id)->update(['devisi_id' => null]);

            // 2. Jika ada PJ, kembalikan rolenya menjadi 'anggota'
            if ($devisi->pj) {
                $pj = $devisi->pj;
                $pj->syncRoles('anggota');
            }
            
            // 3. Hapus devisi
            $devisi->delete();
        });

        return redirect()->route('admin.devisi.index')->with('success', "Devisi '{$namaDevisi}' beserta relasinya berhasil dihapus!");
    }

    /**
     * Menambahkan anggota ke dalam devisi.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Devisi  $devisi
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addMember(Request $request, Devisi $devisi)
    {
        $request->validate(['user_id' => 'required|exists:users,id']);

        $user = User::find($request->user_id);

        if ($user->devisi_id) {
             return back()->with('error', "Gagal: Pengguna '{$user->name}' sudah terdaftar di devisi lain.");
        }

        $user->devisi_id = $devisi->id;
        $user->save();

        return back()->with('success', "'{$user->name}' berhasil ditambahkan ke devisi '{$devisi->nama_devisi}'.");
    }

    /**
     * Mengeluarkan anggota dari devisi.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Devisi  $devisi
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeMember(Request $request, Devisi $devisi, User $user)
    {
        if ($user->devisi_id !== $devisi->id) {
            return back()->with('error', 'Aksi gagal: Pengguna bukan anggota devisi ini.');
        }

        $user->devisi_id = null;
        $user->save();
        return back()->with('success', "'{$user->name}' berhasil dikeluarkan dari devisi.");
    }
}