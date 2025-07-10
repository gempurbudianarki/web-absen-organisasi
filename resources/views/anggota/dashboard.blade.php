@extends('layouts.app')

@push('styles')
<style>
    .card-feature {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .card-feature:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15)!important;
    }
    .kegiatan-list .list-group-item {
        border-right: 0;
        border-left: 0;
        border-top: 0;
        border-bottom: 1px solid rgba(0,0,0,.125);
        padding: 1rem 1.25rem;
    }
    .kegiatan-list .list-group-item:first-child {
        border-top-left-radius: .25rem;
        border-top-right-radius: .25rem;
    }
    .kegiatan-list .list-group-item:last-child {
        border-bottom: 0;
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    <div class="px-4 py-5 my-5 text-center">
        <img class="d-block mx-auto mb-4" src="{{ asset('images/developer.png') }}" alt="" width="72">
        <h1 class="display-5 fw-bold text-body-emphasis">Selamat Datang, {{ Auth::user()->name }}!</h1>
        <div class="col-lg-6 mx-auto">
            <p class="lead mb-4">Ini adalah pusat kendali Anda. Lihat kegiatan mendatang, lakukan absensi, dan kelola profil Anda di sini.</p>
        </div>
    </div>

    {{-- Kartu Aksi Cepat --}}
    <div class="row g-4 justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card card-feature h-100 shadow-sm">
                <div class="card-body text-center p-4">
                    <i class="bi bi-qr-code-scan display-4 text-primary mb-3"></i>
                    <h5 class="card-title">Absen via QR</h5>
                    <p class="card-text text-muted">Pindai QR Code kegiatan untuk mencatat kehadiran.</p>
                    <a href="{{ route('absensi.scan') }}" class="btn btn-primary mt-3">
                        <i class="bi bi-camera-fill me-1"></i> Buka Kamera
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-5 col-lg-4">
            <div class="card card-feature h-100 shadow-sm">
                <div class="card-body text-center p-4">
                    <i class="bi bi-keyboard-fill display-4 text-success mb-3"></i>
                    <h5 class="card-title">Absen via Kode</h5>
                    <p class="card-text text-muted">Masukkan kode unik yang diberikan oleh panitia.</p>
                    <a href="{{ route('absensi.kode.form') }}" class="btn btn-success mt-3">
                        <i class="bi bi-input-cursor-text me-1"></i> Masukkan Kode
                    </a>
                </div>
            </div>
        </div>
    </div>

    <hr class="my-5">

    {{-- Daftar Kegiatan dan Pengumuman --}}
    <div class="row g-5">
        <div class="col-lg-7">
            <h4 class="mb-3"><i class="bi bi-calendar-week me-2"></i>Kegiatan Akan Datang</h4>
            <div class="card shadow-sm kegiatan-list">
                <ul class="list-group list-group-flush">
                    @forelse($kegiatans as $kegiatan)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">{{ $kegiatan->judul }}</h6>
                                <small class="text-muted"><i class="bi bi-geo-alt-fill"></i> {{ $kegiatan->tempat }}</small>
                            </div>
                            <span class="badge bg-primary rounded-pill">{{ \Carbon\Carbon::parse($kegiatan->waktu_mulai)->diffForHumans() }}</span>
                        </li>
                    @empty
                        <li class="list-group-item text-center text-muted p-4">Tidak ada kegiatan yang dijadwalkan.</li>
                    @endforelse
                </ul>
            </div>
        </div>
        <div class="col-lg-5">
            <h4 class="mb-3"><i class="bi bi-megaphone me-2"></i>Pengumuman Terbaru</h4>
             @forelse($pengumumans as $pengumuman)
                <div class="alert alert-info">
                    <h6 class="alert-heading">{{ $pengumuman->judul }}</h6>
                    <small>Diposting oleh {{ $pengumuman->user->name }} - {{ $pengumuman->created_at->diffForHumans() }}</small>
                </div>
             @empty
                <div class="text-center text-muted p-4 border rounded">Belum ada pengumuman baru.</div>
             @endforelse
        </div>
    </div>
</div>
@endsection