@extends('layouts.admin')

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
            <small class="text-muted">{{ \Carbon\Carbon::parse($kegiatan->waktu_mulai)->format('l, d F Y, H:i') }} - {{ $kegiatan->tempat }}</small>
        </div>
        <a href="{{ route('admin.absensi.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Kembali ke Daftar Kegiatan
        </a>
    </div>

    <div class="row g-4">
        {{-- Kolom Kiri: Laporan Kehadiran --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Laporan Kehadiran</h5>
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
                                    <th>Keterangan</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($absensi as $index => $absen)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $absen->user->name ?? 'N/A' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($absen->waktu_absen)->format('H:i:s') }}</td>
                                    <td>
                                        @if($absen->status == 'hadir')
                                            <span class="badge bg-success">Hadir</span>
                                        @elseif($absen->status == 'izin')
                                            <span class="badge bg-warning text-dark">Izin</span>
                                        @elseif($absen->status == 'sakit')
                                            <span class="badge bg-info text-dark">Sakit</span>
                                        @endif
                                    </td>
                                    <td>{{ $absen->keterangan ?? '-' }}</td>
                                    <td class="text-center">
                                        <form action="{{ route('admin.absensi.destroy', $absen->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data absensi ini?');" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus Absen">
                                                <i class="bi bi-x-circle-fill"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">Belum ada anggota yang melakukan absensi.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Aksi Cepat --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Sesi QR Code</h5>
                </div>
                <div class="card-body text-center">
                    <p>Tampilkan QR Code di layar agar anggota dapat melakukan absensi mandiri.</p>
                    <a href="{{ route('admin.absensi.qr', $kegiatan->id) }}" class="btn btn-primary w-100" target="_blank">
                        <i class="bi bi-qr-code me-2"></i>Tampilkan QR Code
                    </a>
                </div>
            </div>
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Absensi Manual</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.absensi.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="kegiatan_id" value="{{ $kegiatan->id }}">
                        <div class="mb-3">
                            <label for="user_id" class="form-label">Pilih Anggota</label>
                            <select name="user_id" id="user_id" class="form-select" required>
                                <option value="">-- Cari Nama Anggota --</option>
                                @foreach($peserta as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
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
                            <textarea name="keterangan" id="keterangan" rows="2" class="form-control"></textarea>
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