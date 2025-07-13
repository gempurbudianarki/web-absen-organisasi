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
     *
     * @param  \App\Models\Kegiatan  $kegiatan
     * @return \Illuminate\View\View
     */
    public function show(Kegiatan $kegiatan)
    {
        // Otorisasi: Memastikan PJ hanya bisa mengakses absensi dari kegiatannya sendiri.
        $this->authorize('manage', $kegiatan);

        $absensi = Absensi::where('kegiatan_id', $kegiatan->id)->with('user')->get();
        
        // Ambil daftar anggota devisi yang belum diabsen untuk kegiatan ini.
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
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $kegiatan = Kegiatan::findOrFail($request->kegiatan_id);
        $this->authorize('manage', $kegiatan); // Otorisasi

        $validated = $request->validate([
            'kegiatan_id' => 'required|exists:kegiatans,id',
            'user_id' => [
                'required', 
                'exists:users,id',
                // Validasi anti duplikat yang lebih kuat.
                Rule::unique('absensis')->where(fn ($query) => 
                    $query->where('kegiatan_id', $request->kegiatan_id)
                          ->where('user_id', $request->user_id)
                ),
            ],
            'status' => 'required|in:hadir,izin,sakit',
            'keterangan' => 'nullable|string|max:255',
        ], ['user_id.unique' => 'Anggota ini sudah diabsen untuk kegiatan ini.']);

        Absensi::create($validated + ['waktu_absen' => now()]);
        
        $user = User::find($validated['user_id']);

        return redirect()->route('pj.absensi.show', $kegiatan->id)
                         ->with('success', "Absensi untuk '{$user->name}' berhasil dicatat!");
    }

    /**
     * Menghapus data absensi.
     *
     * @param  \App\Models\Absensi  $absensi
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Absensi $absensi)
    {
        $this->authorize('manage', $absensi->kegiatan); // Otorisasi
        
        $kegiatan_id = $absensi->kegiatan_id;
        $userName = $absensi->user->name;
        $absensi->delete();

        return redirect()->route('pj.absensi.show', $kegiatan_id)
                         ->with('success', "Data absensi untuk '{$userName}' berhasil dihapus.");
    }

    /**
     * Generate atau re-generate "Password Sesi" untuk kegiatan.
     *
     * @param  \App\Models\Kegiatan  $kegiatan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function generateCode(Kegiatan $kegiatan)
    {
        $this->authorize('manage', $kegiatan); // Otorisasi

        $kegiatan->kode_absensi = Str::upper(Str::random(8));
        $kegiatan->save();

        return redirect()->route('pj.absensi.show', $kegiatan->id)
                         ->with('success', 'Password Sesi baru berhasil dibuat!');
    }

    /**
     * Menutup sesi dan menandai semua anggota yang belum absen sebagai 'alpa'.
     *
     * @param  \App\Models\Kegiatan  $kegiatan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function closeAndMarkAbsentees(Kegiatan $kegiatan)
    {
        $this->authorize('manage', $kegiatan);

        $anggotaAlpaCount = 0;
        DB::transaction(function () use ($kegiatan, &$anggotaAlpaCount) {
            // 1. Dapatkan semua ID anggota devisi ini
            $semuaAnggotaIds = User::role('anggota')
                ->where('devisi_id', $kegiatan->devisi_id)
                ->pluck('id');

            // 2. Dapatkan semua ID anggota yang sudah punya record absensi untuk kegiatan ini
            $sudahAbsenIds = Absensi::where('kegiatan_id', $kegiatan->id)->pluck('user_id');

            // 3. Cari anggota yang belum absen (alpa)
            $alpaIds = $semuaAnggotaIds->diff($sudahAbsenIds);
            $anggotaAlpaCount = count($alpaIds);

            if ($anggotaAlpaCount > 0) {
                $dataToInsert = [];
                $now = now();

                foreach ($alpaIds as $userId) {
                    $dataToInsert[] = [
                        'kegiatan_id' => $kegiatan->id,
                        'user_id' => $userId,
                        'status' => 'alpa',
                        'keterangan' => 'Tidak ada konfirmasi saat sesi ditutup.',
                        'waktu_absen' => $now,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                // 4. Masukkan semua data 'alpa' sekaligus untuk efisiensi
                Absensi::insert($dataToInsert);
            }
        });

        $message = "Sesi telah ditutup. ";
        if ($anggotaAlpaCount > 0) {
            $message .= "Sebanyak {$anggotaAlpaCount} anggota yang tidak hadir telah ditandai sebagai Alpa.";
        } else {
            $message .= "Semua anggota devisi telah melakukan absensi.";
        }

        return redirect()->route('pj.absensi.show', $kegiatan->id)->with('success', $message);
    }
}