<?php

namespace App\Http\Controllers\PJ;

use App\Http\Controllers\Controller;
use App\Models\Devisi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // <-- Import Hash
use Illuminate\Validation\Rules; // <-- Import Rules

class AnggotaController extends Controller
{
    /**
     * Helper untuk mendapatkan devisi yang dipimpin oleh PJ yang sedang login.
     *
     * @return \App\Models\Devisi
     */
    private function getPjDevisi(): Devisi
    {
        $devisi = Auth::user()->devisiYangDipimpin;
        if (!$devisi) {
            abort(403, 'AKSES DITOLAK: ANDA BUKAN PENANGGUNG JAWAB DEVISI.');
        }
        return $devisi;
    }

    /**
     * Menampilkan daftar anggota dari devisi yang dipimpin oleh PJ.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $devisi = $this->getPjDevisi();
        $anggotas = $devisi->anggota()->role('anggota')->latest()->paginate(15);
        return view('pj.anggota.index', compact('devisi', 'anggotas'));
    }

    /**
     * Menampilkan halaman formulir untuk mendaftarkan anggota baru.
     *
     * @return \Illuminate\View\View
     */
    public function showRegisterForm()
    {
        $devisi = $this->getPjDevisi();
        return view('pj.anggota.register', compact('devisi'));
    }

    /**
     * Memproses pendaftaran anggota baru oleh PJ.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function registerMember(Request $request)
    {
        $devisi = $this->getPjDevisi();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'devisi_id' => $devisi->id, // Langsung masukkan ke devisi PJ
            'email_verified_at' => now(), // Anggap sudah terverifikasi karena didaftarkan PJ
        ]);

        $user->assignRole('anggota'); // Otomatis set role 'anggota'

        return redirect()->route('pj.anggota.index')->with('success', "Anggota baru '{$user->name}' berhasil didaftarkan dan ditambahkan ke devisimu.");
    }


    /**
     * Mengeluarkan anggota dari devisi.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeMember(User $user)
    {
        $devisi = $this->getPjDevisi();

        if ($user->devisi_id !== $devisi->id) {
            return back()->with('error', 'Aksi gagal: Pengguna ini bukan anggota dari devisimu.');
        }

        $user->devisi_id = null;
        $user->save();
        
        return back()->with('success', "'{$user->name}' berhasil dikeluarkan dari devisi.");
    }
}