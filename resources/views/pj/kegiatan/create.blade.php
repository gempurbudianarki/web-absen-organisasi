@extends('layouts.pj')

@section('title', 'Buat Kegiatan Baru')

@section('content')
<div class="container-fluid">
    {{-- Header Halaman --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-plus-circle-fill me-2"></i>
            Buat Kegiatan Baru untuk Devisi <span class="text-primary">{{ $devisi->nama_devisi }}</span>
        </h4>
        <a href="{{ route('pj.kegiatan.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Kembali
        </a>
    </div>

    {{-- Form Card --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('pj.kegiatan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    {{-- Kolom Kiri: Informasi Utama --}}
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="judul" class="form-label fw-bold">Judul Kegiatan</label>
                            <input type="text" class="form-control @error('judul') is-invalid @enderror" id="judul" name="judul" value="{{ old('judul') }}" placeholder="Contoh: Rapat Koordinasi Pekanan" required>
                            @error('judul') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label fw-bold">Deskripsi</label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="5" placeholder="Jelaskan agenda dan tujuan dari kegiatan ini..." required>{{ old('deskripsi') }}</textarea>
                            @error('deskripsi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="waktu_mulai" class="form-label fw-bold">Waktu Mulai</label>
                                <input type="datetime-local" class="form-control @error('waktu_mulai') is-invalid @enderror" id="waktu_mulai" name="waktu_mulai" value="{{ old('waktu_mulai', now()->format('Y-m-d\TH:i')) }}" required>
                                @error('waktu_mulai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="waktu_selesai" class="form-label fw-bold">Waktu Selesai <span class="text-muted">(Opsional)</span></label>
                                <input type="datetime-local" class="form-control @error('waktu_selesai') is-invalid @enderror" id="waktu_selesai" name="waktu_selesai" value="{{ old('waktu_selesai') }}">
                                @error('waktu_selesai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                         <div class="mb-3">
                            <label for="tempat" class="form-label fw-bold">Tempat Pelaksanaan</label>
                            <input type="text" class="form-control @error('tempat') is-invalid @enderror" id="tempat" name="tempat" value="{{ old('tempat') }}" placeholder="Contoh: Sekretariat LDK" required>
                            @error('tempat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    
                    {{-- Kolom Kanan: Poster --}}
                    <div class="col-md-4">
                        <div class="card bg-light border h-100">
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="poster" class="form-label fw-bold">Poster Kegiatan <span class="text-muted">(Opsional)</span></label>
                                    <input class="form-control @error('poster') is-invalid @enderror" type="file" id="poster" name="poster" accept="image/*">
                                    <div class="form-text">Format: JPG, PNG. Maks: 2MB.</div>
                                    @error('poster') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save-fill me-1"></i> Simpan Kegiatan
                    </button>
                    <a href="{{ route('pj.kegiatan.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection