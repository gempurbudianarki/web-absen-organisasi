@extends('layouts.anggota')

@section('title', 'Dashboard Anggota')

@section('content')
<div class="container">
    {{-- Notifikasi Sukses --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h5 class="card-title">Selamat Datang, {{ Auth::user()->name }}!</h5>
                    <p class="card-text text-muted">Siap untuk beraktivitas hari ini? Jangan lupa untuk selalu cek jadwal kegiatan dan lakukan absensi.</p>
                    
                    {{-- --- PERUBAHAN DI SINI --- --}}
                    <a href="{{ route('anggota.absensi.form') }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-box-arrow-in-right me-2"></i> Absen Sekarang
                    </a>
                </div>
            </div>

            {{-- Jadwal Kegiatan Mendatang --}}
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Jadwal Kegiatan Mendatang</h5>
                </div>
                <div class="card-body">
                    {{-- Logika untuk menampilkan kegiatan mendatang akan ditambahkan di sini --}}
                    <p class="text-muted">Fitur ini akan segera tersedia.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            {{-- Kartu Profil --}}
            <div class="card shadow-sm border-0 text-center">
                <div class="card-body">
                    <img src="{{ Auth::user()->getAvatarUrl() }}" alt="Avatar" class="rounded-circle mb-3" style="width: 100px; height: 100px;">
                    <h5 class="card-title">{{ Auth::user()->name }}</h5>
                    <p class="text-muted mb-1">{{ Auth::user()->devisi->nama_devisi ?? 'Belum ada devisi' }}</p>
                    <span class="badge bg-secondary">{{ Str::title(Auth::user()->getRoleNames()->first()) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection