@extends('layouts.pj')

@section('title', 'Kelola Absensi Kegiatan')

@section('content')
<div class="container-fluid">
    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
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
            Kembali ke Manajemen Kegiatan
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Laporan Kehadiran</h5>
                    {{-- --- TOMBOL BARU --- --}}
                    <form action="{{ route('pj.absensi.close', $kegiatan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menutup sesi ini? Semua anggota yang belum hadir akan ditandai sebagai ALPA. Aksi ini tidak bisa dibatalkan.');">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="bi bi-lock-fill me-1"></i> Tutup Sesi & Tandai Alpa
                        </button>
                    </form>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama Anggota</th>
                                    <th>Waktu Absen</th>
                                    <th>Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($absensi->sortBy('user.name') as $index => $absen)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $absen->user->name ?? 'N/A' }}</td>
                                    <td>{{ $absen->waktu_absen->format('H:i:s') }}</td>
                                    <td>
                                        @if($absen->status == 'hadir')
                                            <span class="badge bg-success">Hadir</span>
                                        @elseif($absen->status == 'izin')
                                            <span class="badge bg-warning text-dark">Izin</span>
                                        @elseif($absen->status == 'sakit')
                                            <span class="badge bg-info text-dark">Sakit</span>
                                        {{-- --- PERUBAHAN DI SINI --- --}}
                                        @elseif($absen->status == 'alpa')
                                            <span class="badge bg-secondary">Alpa</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <form action="{{ route('pj.absensi.destroy', $absen->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data absensi ini?');" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Absen">
                                                <i class="bi bi-trash3-fill"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Belum ada anggota yang melakukan absensi.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Password Sesi Absensi</h5>
                </div>
                <div class="card-body text-center">
                    @if($kegiatan->kode_absensi)
                        <p class="text-muted mb-2">Bagikan password ini kepada anggota:</p>
                        <h3 class="display-5 fw-bold text-primary user-select-all" style="letter-spacing: 0.1rem;">{{ $kegiatan->kode_absensi }}</h3>
                        <form action="{{ route('pj.absensi.generate_code', $kegiatan->id) }}" method="POST" class="mt-3">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-secondary">Buat Password Baru</button>
                        </form>
                    @else
                        <p>Buat password unik agar anggota bisa melakukan absensi mandiri.</p>
                        <form action="{{ route('pj.absensi.generate_code', $kegiatan->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-magic me-2"></i>Generate Password Sesi
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Absensi Manual oleh PJ</h5>
                </div>
                <div class="card-body">
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
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-check-circle-fill me-2"></i>Simpan Kehadiran
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection