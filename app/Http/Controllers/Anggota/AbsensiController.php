<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Kegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbsensiController extends Controller
{
    /**
     * Menampilkan form untuk memasukkan kode/password absensi.
     */
    public function showCodeForm()
    {
        return view('anggota.absensi.form');
    }

    /**
     * Memproses kode/password absensi yang di-submit oleh anggota.
     */
    public function processCode(Request $request)
    {
        $request->validate([
            'kode_absensi' => 'required|string|max:10',
        ]);

        $kode = $request->input('kode_absensi');
        $user = Auth::user();

        // 1. Cari kegiatan berdasarkan kode absensi
        $kegiatan = Kegiatan::where('kode_absensi', $kode)->first();

        if (!$kegiatan) {
            return back()->with('error', 'Password Sesi tidak valid atau salah.');
        }

        // 2. Cek apakah anggota berhak mengikuti kegiatan ini
        // Kegiatan umum (devisi_id null) bisa diikuti semua anggota.
        // Kegiatan khusus hanya bisa diikuti oleh anggota devisi tersebut.
        $isPesertaSah = ($kegiatan->devisi_id === null) || ($kegiatan->devisi_id == $user->devisi_id);

        if (!$isPesertaSah) {
            return back()->with('error', 'Anda tidak terdaftar sebagai peserta untuk kegiatan ini.');
        }

        // 3. Cek apakah anggota sudah diabsen sebelumnya
        $sudahAbsen = Absensi::where('kegiatan_id', $kegiatan->id)
                             ->where('user_id', $user->id)
                             ->exists();

        if ($sudahAbsen) {
            return back()->with('success', 'Anda sudah tercatat hadir pada kegiatan ini.');
        }

        // 4. Jika semua validasi lolos, catat kehadiran
        Absensi::create([
            'kegiatan_id' => $kegiatan->id,
            'user_id' => $user->id,
            'status' => 'hadir',
            'waktu_absen' => now(),
        ]);

        return redirect()->route('anggota.dashboard')
                         ->with('success', 'Kehadiran Anda untuk kegiatan "' . $kegiatan->judul . '" berhasil dicatat!');
    }
}