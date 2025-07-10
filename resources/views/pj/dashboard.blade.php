@extends('layouts.pj')

@section('title', 'Dashboard Devisi ' . $devisi->nama_devisi)

@section('content')
<div class="container-fluid">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Dashboard Devisi {{ $devisi->nama_devisi }}</h4>
    </div>

    {{-- Kartu Statistik --}}
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card text-center text-white bg-primary shadow-sm h-100">
                <div class="card-body">
                    <i class="bi bi-people-fill display-6"></i>
                    <h5 class="card-title mt-2">Total Anggota</h5>
                    <p class="display-6 mb-0">{{ $anggotaCount }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card text-center text-white bg-success shadow-sm h-100">
                <div class="card-body">
                    <i class="bi bi-calendar-event-fill display-6"></i>
                    <h5 class="card-title mt-2">Total Kegiatan</h5>
                    <p class="display-6 mb-0">{{ $kegiatanCount }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Kegiatan Terbaru --}}
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header">
            <h5 class="mb-0">Kegiatan Terbaru Devisi Anda</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Judul Kegiatan</th>
                            <th>Jadwal</th>
                            <th>Tempat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kegiatans as $kegiatan)
                            <tr>
                                <td>{{ $kegiatan->judul }}</td>
                                <td>{{ \Carbon\Carbon::parse($kegiatan->waktu_mulai)->format('d M Y, H:i') }}</td>
                                <td>{{ $kegiatan->tempat }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">Belum ada kegiatan yang dibuat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection