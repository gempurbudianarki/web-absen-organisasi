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
        <h4 class="mb-0">Dashboard Devisi <span class="text-primary">{{ $devisi->nama_devisi }}</span></h4>
        <span class="text-muted">Selamat datang, {{ Auth::user()->name }}!</span>
    </div>

    {{-- Kartu Statistik --}}
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card text-center text-white bg-primary shadow-sm h-100">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <i class="bi bi-people-fill display-4 mb-2"></i>
                    <h5 class="card-title">Total Anggota</h5>
                    <p class="display-4 fw-bold mb-0">{{ $anggotaCount }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card text-center text-white bg-success shadow-sm h-100">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <i class="bi bi-calendar-event-fill display-4 mb-2"></i>
                    <h5 class="card-title">Total Kegiatan</h5>
                    <p class="display-4 fw-bold mb-0">{{ $kegiatanCount }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Kegiatan Terbaru --}}
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="bi bi-star-fill me-2 text-warning"></i>Kegiatan Terbaru Devisi Anda</h5>
        </div>
        <div class="card-body">
            @if($kegiatans->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach ($kegiatans as $kegiatan)
                        <a href="{{ route('pj.kegiatan.edit', $kegiatan->id) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">{{ $kegiatan->judul }}</h6>
                                <small class="text-muted"><i class="bi bi-clock me-2"></i>{{ $kegiatan->waktu_mulai->isoFormat('dddd, D MMMM YYYY - HH:mm') }} WIB</small>
                            </div>
                            <span class="badge bg-primary rounded-pill">Lihat</span>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="text-center text-muted p-4">
                    <p class="mb-0">Belum ada kegiatan yang dibuat untuk devisi ini.</p>
                </div>
            @endif
        </div>
        <div class="card-footer bg-light text-center">
             <a href="{{ route('pj.kegiatan.index') }}">Lihat semua kegiatan <i class="bi bi-arrow-right-short"></i></a>
        </div>
    </div>
</div>
@endsection