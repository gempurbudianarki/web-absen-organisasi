@extends('layouts.pj')

@section('title', 'Register Anggota Baru')

@section('content')
<div class="container-fluid">
    {{-- Header Halaman --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0 fw-bold">
                <i class="bi bi-person-plus-fill me-2"></i>
                Register Anggota Baru
            </h4>
            <p class="text-muted mb-0">Anggota yang didaftarkan akan otomatis masuk ke Devisi <span class="fw-bold text-primary">{{ $devisi->nama_devisi }}</span>.</p>
        </div>
        <a href="{{ route('pj.anggota.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Kembali
        </a>
    </div>

    {{-- Form Card --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('pj.anggota.store') }}" method="POST">
                @csrf
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row g-3">
                    <div class="col-12 mb-3">
                        <label for="name" class="form-label fw-bold">Nama Lengkap</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="Masukkan nama lengkap anggota baru" required>
                    </div>
                    <div class="col-12 mb-3">
                        <label for="email" class="form-label fw-bold">Alamat Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="contoh@email.com" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label fw-bold">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="password_confirmation" class="form-label fw-bold">Konfirmasi Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                </div>
                <div class="mt-4 border-top pt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save-fill me-1"></i> Daftarkan Anggota
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection