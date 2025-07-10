<?php

namespace App\Http\Controllers\PJ;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Kegiatan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AbsensiController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show($kegiatan_id)
    {
        $kegiatan = Kegiatan::findOrFail($kegiatan_id);
        $devisiPJ = Auth::user()->devisiYangDipimpin;

        if (!$devisiPJ || $kegiatan->devisi_id !== $devisiPJ->id) {
            abort(403, 'AKSI TIDAK DIIZINKAN.');
        }

        $absensi = Absensi::where('kegiatan_id', $kegiatan->id)->with('user')->get();
        $absenUserIds = $absensi->pluck('user_id')->toArray();

        $peserta = User::where('devisi_id', $devisiPJ->id)
                       ->whereNotIn('id', $absenUserIds)
                       ->role('anggota')
                       ->orderBy('name')
                       ->get();

        return view('pj.absensi.show', compact('kegiatan', 'absensi', 'peserta'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $kegiatan = Kegiatan::findOrFail($request->kegiatan_id);
        $devisiPJ = Auth::user()->devisiYangDipimpin;

        if (!$devisiPJ || $kegiatan->devisi_id !== $devisiPJ->id) {
            abort(403, 'AKSI TIDAK DIIZINKAN.');
        }

        $request->validate([
            'kegiatan_id' => 'required|exists:kegiatans,id',
            'user_id' => [
                'required', 'exists:users,id',
                Rule::unique('absensis')->where('kegiatan_id', $request->kegiatan_id),
            ],
            'status' => 'required|in:hadir,izin,sakit',
        ], ['user_id.unique' => 'Anggota ini sudah diabsen.']);

        Absensi::create($request->all());

        return redirect()->route('pj.absensi.show', $request->kegiatan_id)
                         ->with('success', 'Kehadiran berhasil dicatat!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Absensi $absensi)
    {
        $kegiatan = $absensi->kegiatan;
        $devisiPJ = Auth::user()->devisiYangDipimpin;

        if (!$devisiPJ || $kegiatan->devisi_id !== $devisiPJ->id) {
            abort(403, 'AKSI TIDAK DIIZINKAN.');
        }
        
        $kegiatan_id = $absensi->kegiatan_id;
        $absensi->delete();

        return redirect()->route('pj.absensi.show', $kegiatan_id)
                         ->with('success', 'Data absensi berhasil dihapus.');
    }

    /**
     * Generate atau re-generate kode absensi untuk kegiatan.
     */
    public function generateCode(Kegiatan $kegiatan)
    {
        $devisiPJ = Auth::user()->devisiYangDipimpin;
        if (!$devisiPJ || $kegiatan->devisi_id !== $devisiPJ->id) {
            abort(403, 'AKSI TIDAK DIIZINKAN.');
        }

        // Generate kode unik 8 karakter (huruf besar, angka, tanpa simbol)
        $kegiatan->kode_absensi = Str::upper(Str::random(8));
        $kegiatan->save();

        return redirect()->route('pj.absensi.show', $kegiatan->id)
                         ->with('success', 'Kode absensi baru berhasil dibuat!');
    }
}