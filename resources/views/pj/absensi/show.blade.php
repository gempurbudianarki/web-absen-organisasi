@extends('layouts.pj')

@section('title', 'Kelola Absensi: ' . $kegiatan->judul)

@section('content')
<div class="container-fluid">
    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong><i class="bi bi-exclamation-triangle-fill me-2"></i>Error!</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif


    {{-- Header Halaman --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">
                <i class="bi bi-person-check-fill me-2"></i>
                Kelola Absensi: {{ $kegiatan->judul }}
            </h4>
            <small class="text-muted">{{ $kegiatan->waktu_mulai->isoFormat('dddd, D MMMM YYYY, H:mm') }} - {{ $kegiatan->tempat }}</small>
        </div>
        <a href="{{ route('pj.kegiatan.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Kembali ke Kegiatan
        </a>
    </div>

    {{-- KONTEN UTAMA DENGAN STATE MANAGEMENT --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Status Sesi Absensi</h5>
            @if($kegiatan->status_absensi == 'belum_dibuka')
                <span class="badge bg-secondary fs-6">BELUM DIBUKA</span>
            @elseif($kegiatan->status_absensi == 'dibuka')
                <span class="badge bg-success fs-6">SEDANG DIBUKA</span>
            @else
                <span class="badge bg-danger fs-6">SUDAH DITUTUP</span>
            @endif
        </div>

        {{-- Tampilan berdasarkan Status Sesi --}}
        <div class="card-body p-4">
            @if($kegiatan->status_absensi == 'belum_dibuka')
                <div class="text-center py-5">
                    <i class="bi bi-lock-fill display-1 text-muted"></i>
                    <h3 class="mt-3">Sesi Absensi Belum Dibuka</h3>
                    <p class="text-muted">Buka sesi agar anggota dapat mulai melakukan absensi.</p>
                    <form action="{{ route('pj.absensi.buka', $kegiatan->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-unlock-fill me-2"></i>Buka Sesi Absensi
                        </button>
                    </form>
                </div>
            @elseif($kegiatan->status_absensi == 'dibuka')
                {{-- Tampilan saat sesi DIBUKA --}}
                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="card bg-light h-100">
                            <div class="card-body text-center d-flex flex-column justify-content-center">
                                <h5 class="mb-3">Password Sesi Absensi</h5>
                                <h3 class="display-5 fw-bold text-primary user-select-all" style="letter-spacing: 0.1rem;">{{ $kegiatan->kode_absensi }}</h3>
                                <p class="text-muted small mt-2">Bagikan password ini kepada anggota devisi Anda.</p>
                                <hr>
                                <p class="mb-2 fw-bold">Tutup Sesi Jika Selesai</p>
                                <form action="{{ route('pj.absensi.tutup', $kegiatan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menutup sesi? Semua anggota yang belum hadir akan ditandai ALPA. Aksi ini tidak bisa dibatalkan.');">
                                    @csrf
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="bi bi-lock-fill me-1"></i> Tutup Sesi & Tandai Alpa
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <h5 class="mb-3">Absensi Manual</h5>
                        <form action="{{ route('pj.absensi.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="kegiatan_id" value="{{ $kegiatan->id }}">
                            <div class="mb-3">
                                <label for="user_id" class="form-label">Pilih Anggota Devisi</label>
                                <select name="user_id" id="user_id" class="form-select" required>
                                    <option value="">-- Pilih Nama Anggota --</option>
                                    @forelse($peserta as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @empty
                                        <option value="" disabled>Semua anggota devisi sudah diabsen.</option>
                                    @endforelse
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status Kehadiran</label>
                                <select name="status" id="status" class="form-select" required>
                                    <option value="hadir">Hadir</option>
                                    <option value="izin">Izin</option>
                                    <option value="sakit">Sakit</option>
                                </select>
                            </div>
                             <div class="mb-3">
                                <label for="keterangan" class="form-label">Keterangan (Opsional)</label>
                                <textarea name="keterangan" id="keterangan" class="form-control" rows="2" placeholder="Contoh: Izin karena acara keluarga"></textarea>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle-fill me-2"></i>Simpan Kehadiran
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                {{-- Tampilan saat sesi DITUTUP --}}
                <div class="text-center py-5">
                    <i class="bi bi-check-circle-fill display-1 text-success"></i>
                    <h3 class="mt-3">Sesi Absensi Telah Ditutup</h3>
                    <p class="text-muted">Laporan akhir kehadiran sudah final dan tidak dapat diubah lagi.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Laporan Kehadiran (Tabel) --}}
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-light"><h5 class="mb-0">Laporan Kehadiran</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead><tr><th>#</th><th>Nama Anggota</th><th>Waktu Absen</th><th>Status</th><th>Keterangan</th><th class="text-center">Aksi</th></tr></thead>
                    <tbody>
                        @forelse($absensi->sortBy('user.name') as $index => $absen)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $absen->user->name ?? 'N/A' }}</td>
                            <td>{{ $absen->waktu_absen->format('H:i:s') }}</td>
                            <td>
                                @if($absen->status == 'hadir') <span class="badge bg-success">Hadir</span>
                                @elseif($absen->status == 'izin') <span class="badge bg-warning text-dark">Izin</span>
                                @elseif($absen->status == 'sakit') <span class="badge bg-info text-dark">Sakit</span>
                                @elseif($absen->status == 'alpa') <span class="badge bg-secondary">Alpa</span>
                                @endif
                            </td>
                            <td>{{ $absen->keterangan ?? '-'}}</td>
                            <td class="text-center">
                                @if($kegiatan->status_absensi != 'ditutup')
                                <form action="{{ route('pj.absensi.destroy', $absen->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data absensi ini?');" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Absen"><i class="bi bi-trash3-fill"></i></button>
                                </form>
                                @else
                                <button class="btn btn-sm btn-outline-secondary" disabled title="Sesi ditutup"><i class="bi bi-trash3-fill"></i></button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">Belum ada anggota yang melakukan absensi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection