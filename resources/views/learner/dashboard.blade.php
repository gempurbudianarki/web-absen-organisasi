@extends('layouts.app')

@section('content')
<div class="container py-4">
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    <div class="text-center mb-5">
        <h2 class="fw-bold">👋 Welcome, {{ Auth::user()->name }}</h2>
        <p class="text-muted">Ini adalah dashboard Anggota. Akses fitur Anda di bawah ini.</p>
    </div>

    <div class="row g-4 justify-content-center">
        <div class="col-md-4">
            <div class="card border-0 shadow-lg h-100 bg-primary text-white">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <i class="bi bi-qr-code-scan display-3 mb-3"></i>
                    <h5 class="card-title">Absen via QR</h5>
                    <p class="card-text">Pindai QR Code untuk mencatat kehadiran.</p>
                    <a href="{{ route('absensi.scan') }}" class="btn btn-light stretched-link mt-auto">
                        <i class="bi bi-camera-fill me-1"></i> Buka Kamera
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-lg h-100 bg-info text-white">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <i class="bi bi-keyboard-fill display-3 mb-3"></i>
                    <h5 class="card-title">Absen via Kode</h5>
                    <p class="card-text">Masukkan kode unik yang diberikan panitia.</p>
                    <a href="{{ route('absensi.kode.form') }}" class="btn btn-light stretched-link mt-auto">
                        <i class="bi bi-input-cursor-text me-1"></i> Masukkan Kode
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow h-100">
                <div class="card-body text-center">
                    <i class="bi bi-clock-history fs-1 text-secondary mb-3"></i>
                    <h5 class="card-title">Profil & Histori</h5>
                    <p class="card-text">Lihat riwayat kehadiran dan profil Anda.</p>
                    <a href="{{ route('profile.edit') }}" class="btn btn-secondary stretched-link mt-auto">Buka Profil</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection