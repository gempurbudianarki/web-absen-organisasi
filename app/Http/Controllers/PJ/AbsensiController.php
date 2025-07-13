<?php

namespace App\Http\Controllers\PJ;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Kegiatan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AbsensiController extends Controller
{
    /**
     * Menampilkan halaman kelola absensi untuk sebuah kegiatan.
     */
    public function show($kegiatan_id)
    {
        $kegiatan = Kegiatan::findOrFail($kegiatan_id);
        $this->authorize('manage', $kegiatan); // Otorisasi

        $absensi = Absensi::where('kegiatan_id', $kegiatan->id)->with('user')->get();
        $absenUserIds = $absensi->pluck('user_id')->toArray();

        $peserta = User::role('anggota')
                       ->where('devisi_id', $kegiatan->devisi_id)
                       ->whereNotIn('id', $absenUserIds)
                       ->orderBy('name')
                       ->get();

        return view('pj.absensi.show', compact('kegiatan', 'absensi', 'peserta'));
    }

    /**
     * Menyimpan data absensi manual oleh PJ.
     */
    public function store(Request $request)
    {
        $kegiatan = Kegiatan::findOrFail($request->kegiatan_id);
        $this->authorize('manage', $kegiatan); // Otorisasi

        $request->validate([
            'kegiatan_id' => 'required|exists:kegiatans,id',
            'user_id' => [
                'required', 'exists:users,id',
                Rule::unique('absensis')->where('kegiatan_id', $request->kegiatan_id),
            ],
            'status' => 'required|in:hadir,izin,sakit',
            'keterangan' => 'nullable|string|max:255',
        ], ['user_id.unique' => 'Anggota ini sudah diabsen.']);

        Absensi::create([
            'kegiatan_id' => $request->kegiatan_id,
            'user_id' => $request->user_id,
            'status' => $request->status,
            'keterangan' => $request->keterangan,
            'waktu_absen' => now(),
        ]);

        return redirect()->route('pj.absensi.show', $request->kegiatan_id)
                         ->with('success', 'Kehadiran berhasil dicatat!');
    }

    /**
     * Menghapus data absensi.
     */
    public function destroy(Absensi $absensi)
    {
        $this->authorize('manage', $absensi->kegiatan); // Otorisasi
        
        $kegiatan_id = $absensi->kegiatan_id;
        $absensi->delete();

        return redirect()->route('pj.absensi.show', $kegiatan_id)
                         ->with('success', 'Data absensi berhasil dihapus.');
    }

    /**
     * Generate atau re-generate "Password Sesi" untuk kegiatan.
     */
    public function generateCode(Kegiatan $kegiatan)
    {
        $this->authorize('manage', $kegiatan); // Otorisasi

        $kegiatan->kode_absensi = Str::upper(Str::random(8));
        $kegiatan->save();

        return redirect()->route('pj.absensi.show', $kegiatan->id)
                         ->with('success', 'Password Sesi berhasil dibuat!');
    }

    /**
     * --- FITUR BARU ---
     * Menutup sesi dan menandai semua anggota yang belum absen sebagai 'alpa'.
     */
    public function closeAndMarkAbsentees(Kegiatan $kegiatan)
    {
        $this->authorize('manage', $kegiatan);

        DB::transaction(function () use ($kegiatan) {
            // 1. Dapatkan semua ID anggota devisi ini
            $semuaAnggotaIds = User::role('anggota')
                ->where('devisi_id', $kegiatan->devisi_id)
                ->pluck('id');

            // 2. Dapatkan semua ID anggota yang sudah punya record absensi
            $sudahAbsenIds = Absensi::where('kegiatan_id', $kegiatan->id)
                ->pluck('user_id');

            // 3. Cari anggota yang belum absen (alpa)
            $alpaIds = $semuaAnggotaIds->diff($sudahAbsenIds);

            $dataToInsert = [];
            $now = now();

            foreach ($alpaIds as $userId) {
                $dataToInsert[] = [
                    'kegiatan_id' => $kegiatan->id,
                    'user_id' => $userId,
                    'status' => 'alpa',
                    'keterangan' => 'Tidak ada konfirmasi kehadiran.',
                    'waktu_absen' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // 4. Masukkan semua data 'alpa' sekaligus untuk efisiensi
            if (!empty($dataToInsert)) {
                Absensi::insert($dataToInsert);
            }
        });

        return redirect()->route('pj.absensi.show', $kegiatan->id)
                         ->with('success', 'Sesi telah ditutup dan anggota yang tidak hadir telah ditandai sebagai Alpa.');
    }
}