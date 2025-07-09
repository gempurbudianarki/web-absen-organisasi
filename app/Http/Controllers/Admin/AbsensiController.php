<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Kegiatan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use SimpleSoftwareIO\QrCode\Facades\QrCode; // Import library

class AbsensiController extends Controller
{
    public function index()
    {
        $kegiatans = Kegiatan::where('waktu_mulai', '>=', now()->subHours(6))
                             ->with('devisi')
                             ->orderBy('waktu_mulai', 'asc')
                             ->get();
        return view('admin.absensi.index', compact('kegiatans'));
    }

    public function show($kegiatan_id)
    {
        $kegiatan = Kegiatan::findOrFail($kegiatan_id);
        $absensi = Absensi::where('kegiatan_id', $kegiatan->id)->with('user')->get();
        $absenUserIds = $absensi->pluck('user_id')->toArray();
        $peserta = User::role('anggota')
                       ->whereNotIn('id', $absenUserIds)
                       ->orderBy('name')
                       ->get();
        return view('admin.absensi.show', compact('kegiatan', 'absensi', 'peserta'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kegiatan_id' => 'required|exists:kegiatans,id',
            'user_id' => [
                'required', 'exists:users,id',
                Rule::unique('absensis')->where(function ($query) use ($request) {
                    return $query->where('kegiatan_id', $request->kegiatan_id);
                }),
            ],
            'status' => 'required|in:hadir,izin,sakit',
            'keterangan' => 'nullable|string',
        ],[
            'user_id.unique' => 'Anggota ini sudah diabsen untuk kegiatan ini.'
        ]);

        Absensi::create([
            'kegiatan_id' => $request->kegiatan_id,
            'user_id' => $request->user_id,
            'status' => $request->status,
            'waktu_absen' => now(),
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('admin.absensi.show', $request->kegiatan_id)
                         ->with('success', 'Kehadiran berhasil dicatat!');
    }

    public function destroy(Absensi $absensi)
    {
        $kegiatan_id = $absensi->kegiatan_id;
        $absensi->delete();
        return redirect()->route('admin.absensi.show', $kegiatan_id)
                         ->with('success', 'Data absensi berhasil dihapus.');
    }
        
    /**
     * Menampilkan halaman dengan QR Code untuk kegiatan tertentu.
     */
    public function showQr(Kegiatan $kegiatan)
    {
        // Data yang akan kita sematkan di dalam QR Code
        $dataToEncode = json_encode(['kegiatan_id' => $kegiatan->id]);

        // Generate QR Code dalam format SVG
        $qrCode = QrCode::size(300)->generate($dataToEncode);

        return view('admin.absensi.qr_display', compact('kegiatan', 'qrCode'));
    }
}