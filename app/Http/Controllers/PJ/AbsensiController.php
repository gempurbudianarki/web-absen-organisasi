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
        $this->authorize('manage', $kegiatan);

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
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $kegiatan = Kegiatan::findOrFail($request->kegiatan_id);
        $this->authorize('manage', $kegiatan);

        // Hanya bisa input manual jika sesi DIBUKA
        if ($kegiatan->status_absensi !== 'dibuka') {
            return back()->with('error', 'Aksi gagal! Sesi absensi belum dibuka atau sudah ditutup.');
        }

        $validated = $request->validate([
            'kegiatan_id' => 'required|exists:kegiatans,id',
            'user_id' => [
                'required', 
                'exists:users,id',
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
     * Membuka sesi absensi dan membuat kode jika belum ada.
     *
     * @param  \App\Models\Kegiatan  $kegiatan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bukaSesi(Kegiatan $kegiatan)
    {
        $this->authorize('manage', $kegiatan);

        if ($kegiatan->kode_absensi === null) {
            $kegiatan->kode_absensi = Str::upper(Str::random(8));
        }

        $kegiatan->status_absensi = 'dibuka';
        $kegiatan->save();

        return back()->with('success', 'Sesi absensi telah DIBUKA. Anggota sekarang bisa melakukan absensi.');
    }

    /**
     * Menutup sesi dan menandai semua anggota yang belum absen sebagai 'alpa'.
     *
     * @param  \App\Models\Kegiatan  $kegiatan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function tutupSesi(Kegiatan $kegiatan)
    {
        $this->authorize('manage', $kegiatan);

        $anggotaAlpaCount = 0;
        DB::transaction(function () use ($kegiatan, &$anggotaAlpaCount) {
            $semuaAnggotaIds = User::role('anggota')
                ->where('devisi_id', $kegiatan->devisi_id)
                ->pluck('id');

            $sudahAbsenIds = Absensi::where('kegiatan_id', $kegiatan->id)->pluck('user_id');
            $alpaIds = $semuaAnggotaIds->diff($sudahAbsenIds);
            $anggotaAlpaCount = count($alpaIds);

            if ($anggotaAlpaCount > 0) {
                $dataToInsert = [];
                $now = now();
                foreach ($alpaIds as $userId) {
                    $dataToInsert[] = [
                        'kegiatan_id' => $kegiatan->id, 'user_id' => $userId,
                        'status' => 'alpa', 'keterangan' => 'Tidak ada konfirmasi saat sesi ditutup.',
                        'waktu_absen' => $now, 'created_at' => $now, 'updated_at' => $now,
                    ];
                }
                Absensi::insert($dataToInsert);
            }
            
            // Update status kegiatan menjadi 'ditutup'
            $kegiatan->status_absensi = 'ditutup';
            $kegiatan->save();
        });

        $message = "Sesi telah DITUTUP. ";
        if ($anggotaAlpaCount > 0) {
            $message .= "Sebanyak {$anggotaAlpaCount} anggota telah ditandai sebagai Alpa.";
        } else {
            $message .= "Semua anggota devisi telah melakukan absensi.";
        }

        return back()->with('success', $message);
    }

    /**
     * Menghapus data absensi.
     *
     * @param  \App\Models\Absensi  $absensi
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Absensi $absensi)
    {
        $this->authorize('manage', $absensi->kegiatan);
        
        if ($absensi->kegiatan->status_absensi === 'ditutup') {
             return back()->with('error', 'Aksi gagal! Tidak dapat menghapus data jika sesi sudah ditutup.');
        }
        
        $kegiatan_id = $absensi->kegiatan_id;
        $userName = $absensi->user->name;
        $absensi->delete();

        return redirect()->route('pj.absensi.show', $kegiatan_id)
                         ->with('success', "Data absensi untuk '{$userName}' berhasil dihapus.");
    }
}