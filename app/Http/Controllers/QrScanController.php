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
    /**
     * Menampilkan halaman pemindai QR.
     */
    public function scan()
    {
        return view('absensi.scan');
    }

    /**
     * Memproses data hasil pemindaian QR Code.
     */
    public function process(Request $request)
    {
        $qrData = json_decode($request->getContent(), true);

        // 1. Validasi data dari QR
        $validator = Validator::make($qrData, [
            'kegiatan_id' => 'required|exists:kegiatans,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'QR Code tidak valid atau tidak dikenali.'
            ], 400);
        }

        $kegiatanId = $qrData['kegiatan_id'];
        $user = Auth::user();

        // 2. Cek apakah user adalah anggota
        if (!$user->hasRole('anggota')) {
            return response()->json([
                'status' => 'error',
                'message' => 'Fitur ini hanya untuk Anggota.'
            ], 403);
        }

        // 3. Cek apakah sudah pernah absen
        $sudahAbsen = Absensi::where('user_id', $user->id)
                               ->where('kegiatan_id', $kegiatanId)
                               ->exists();

        if ($sudahAbsen) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda sudah tercatat hadir untuk kegiatan ini.'
            ], 409); // 409 Conflict
        }

        // 4. Jika semua lolos, catat absensi
        Absensi::create([
            'user_id' => $user->id,
            'kegiatan_id' => $kegiatanId,
            'status' => 'hadir',
            'waktu_absen' => Carbon::now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Kehadiran Anda berhasil dicatat!'
        ]);
    }
}