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
     * Menampilkan daftar semua devisi beserta data untuk form.
     */
    public function index()
    {
        $devisis = Devisi::withCount('anggota')->with('pj')->latest()->get();

        // Mengambil user yang bisa menjadi PJ:
        // - Memiliki role 'anggota' ATAU
        // - Memiliki role 'pj' tapi belum ditugaskan ke devisi manapun (tidak punya devisiYangDipimpin)
        $calon_pj = User::where(function ($query) {
            $query->whereHas('roles', fn ($q) => $q->where('name', 'anggota'))
                  ->orWhere(function ($subQuery) {
                      $subQuery->whereHas('roles', fn ($q) => $q->where('name', 'pj'))
                               ->doesntHave('devisiYangDipimpin');
                  });
        })->orderBy('name')->get();

        return view('admin.devisi.index', compact('devisis', 'calon_pj'));
    }

    /**
     * Menyimpan devisi baru ke dalam database.
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
     */
    public function show(Devisi $devisi)
    {
        $devisi->load('pj', 'anggota', 'kegiatan');
        
        // Mengambil calon anggota: user dengan role 'anggota' yang belum punya devisi.
        $calon_anggota = User::role('anggota')->whereNull('devisi_id')->orderBy('name')->get();

        return view('admin.devisi.show', compact('devisi', 'calon_anggota'));
    }

    /**
     * Mengupdate data devisi, termasuk menunjuk PJ baru secara atomik.
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

            // Jika ada perubahan PJ
            if ($oldPjId !== $newPjId) {
                // 1. Copot role PJ lama (jika ada)
                if ($oldPjId && $oldPj = User::find($oldPjId)) {
                    $oldPj->removeRole('pj');
                    $oldPj->assignRole('anggota');
                }
                // 2. Berikan role PJ ke user baru (jika ada)
                if ($newPjId && $newPj = User::find($newPjId)) {
                    $newPj->removeRole('anggota');
                    $newPj->assignRole('pj');
                }
            }
        });

        return redirect()->route('admin.devisi.index')->with('success', 'Data devisi berhasil diperbarui!');
    }

    /**
     * Menghapus devisi beserta relasinya secara aman.
     */
    public function destroy(Devisi $devisi)
    {
        DB::transaction(function () use ($devisi) {
            // 1. Lepaskan semua anggota dari devisi ini
            User::where('devisi_id', $devisi->id)->update(['devisi_id' => null]);

            // 2. Jika ada PJ, copot rolenya
            if ($devisi->pj) {
                $pj = $devisi->pj;
                $pj->removeRole('pj');
                $pj->assignRole('anggota');
            }
            
            // 3. Hapus devisi
            $devisi->delete();
        });

        return redirect()->route('admin.devisi.index')->with('success', 'Devisi berhasil dihapus!');
    }

    /**
     * Menambahkan anggota ke dalam devisi.
     */
    public function addMember(Request $request, Devisi $devisi)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::find($request->user_id);

        if ($user->devisi_id) {
             return back()->with('error', $user->name . ' sudah terdaftar di devisi lain.');
        }

        $user->devisi_id = $devisi->id;
        $user->save();

        return back()->with('success', $user->name . ' berhasil ditambahkan ke devisi ' . $devisi->nama_devisi);
    }

    /**
     * Mengeluarkan anggota dari devisi.
     */
    public function removeMember(Request $request, Devisi $devisi, User $user)
    {
        if ($user->devisi_id == $devisi->id) {
            $user->devisi_id = null;
            $user->save();
            return back()->with('success', $user->name . ' berhasil dikeluarkan dari devisi.');
        }

        return back()->with('error', 'Aksi gagal: User bukan anggota devisi ini.');
    }
}