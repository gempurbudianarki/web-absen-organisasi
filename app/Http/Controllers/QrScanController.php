<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kegiatan;
use App\Models\Absensi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class QrScanController extends Controller
{
    public function scan()
    {
        return view('absensi.scan');
    }

    public function process(Request $request)
    {
        $qrData = json_decode($request->getContent(), true);

        $validator = Validator::make($qrData, ['kegiatan_id' => 'required|exists:kegiatans,id']);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'QR Code tidak valid.'], 400);
        }

        return $this->recordAttendance($qrData['kegiatan_id']);
    }

    public function showCodeForm()
    {
        return view('absensi.kode');
    }

    public function processCode(Request $request)
    {
        $request->validate(['kode_absensi' => 'required|string|size:8']);
        $kode = strtoupper($request->kode_absensi);

        $kegiatan = Kegiatan::where('kode_absensi', $kode)->first();

        if (!$kegiatan) {
            return back()->with('error', 'Kode absensi tidak ditemukan atau tidak valid.');
        }

        // Jika kegiatan ditemukan, gunakan method yang sama untuk mencatat kehadiran
        $response = $this->recordAttendance($kegiatan->id);
        $data = json_decode($response->getContent(), true);

        if ($data['status'] === 'success') {
            return redirect()->route('anggota.dashboard')->with('success', $data['message']);
        } else {
            return back()->with('error', $data['message']);
        }
    }

    /**
     * Method terpusat untuk mencatat absensi.
     */
    private function recordAttendance($kegiatanId)
    {
        $user = Auth::user();

        if (!$user->hasRole('anggota')) {
            return response()->json(['status' => 'error', 'message' => 'Fitur ini hanya untuk Anggota.'], 403);
        }

        $sudahAbsen = Absensi::where('user_id', $user->id)
                               ->where('kegiatan_id', $kegiatanId)
                               ->exists();

        if ($sudahAbsen) {
            return response()->json(['status' => 'error', 'message' => 'Anda sudah tercatat hadir untuk kegiatan ini.'], 409);
        }

        Absensi::create([
            'user_id' => $user->id,
            'kegiatan_id' => $kegiatanId,
            'status' => 'hadir',
            'waktu_absen' => Carbon::now(),
        ]);

        return response()->json(['status' => 'success', 'message' => 'Kehadiran Anda berhasil dicatat!']);
    }
}