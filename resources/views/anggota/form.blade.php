@extends('layouts.anggota')

@section('title', 'Absen Sekarang')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            
            {{-- Notifikasi --}}
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div class="card shadow-sm border-0">
                <div class="card-body p-4 text-center">
                    <i class="bi bi-shield-lock-fill text-primary" style="font-size: 3rem;"></i>
                    <h4 class="card-title mt-3">Absen Kehadiran</h4>
                    <p class="text-muted">Masukkan Password Sesi yang diberikan oleh Penanggung Jawab (PJ) kegiatan.</p>
                    
                    <form action="{{ route('anggota.absensi.process') }}" method="POST" class="mt-4">
                        @csrf
                        <div class="mb-3">
                            <input type="text" 
                                   class="form-control form-control-lg text-center" 
                                   name="kode_absensi" 
                                   placeholder="Ketik Password di Sini" 
                                   required 
                                   autofocus
                                   style="letter-spacing: 0.2rem; text-transform: uppercase;">
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-circle-fill me-2"></i>Konfirmasi Kehadiran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
             <div class="text-center mt-3">
                <a href="{{ route('anggota.dashboard') }}" class="text-decoration-none">
                    <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection