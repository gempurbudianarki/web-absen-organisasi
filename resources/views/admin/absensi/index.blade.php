@extends('layouts.admin')

@section('title', 'Manajemen Absensi')

@section('content')
<div class="container-fluid">
    {{-- Header Halaman --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-clipboard2-check-fill me-2"></i>
            Manajemen Absensi
        </h4>
    </div>

    <div class="alert alert-info" role="alert">
        <i class="bi bi-info-circle-fill me-2"></i>
        Pilih salah satu kegiatan di bawah ini untuk mengelola sesi absensi atau melihat laporan kehadiran.
    </div>

    {{-- Daftar Kegiatan untuk Absensi --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Judul Kegiatan</th>
                            <th scope="col">Devisi</th>
                            <th scope="col">Jadwal Mulai</th>
                            <th scope="col" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kegiatans as $kegiatan)
                            <tr>
                                <td>{{ $kegiatan->judul }}</td>
                                <td>{{ $kegiatan->devisi->nama_devisi ?? 'N/A' }}</td>
                                <td>{{ \Carbon\Carbon::parse($kegiatan->waktu_mulai)->format('d M Y, H:i') }}</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.absensi.show', $kegiatan->id) }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-person-check-fill me-1"></i>
                                        Kelola Absensi
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    Tidak ada kegiatan yang akan datang atau sedang berlangsung.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection