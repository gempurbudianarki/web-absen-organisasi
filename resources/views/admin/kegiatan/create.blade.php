@extends('layouts.admin')

@section('title', 'Tambah Kegiatan Baru')

@section('content')
<div class="container-fluid">
    {{-- Header Halaman --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-plus-circle-fill me-2"></i>
            Tambah Kegiatan Baru
        </h4>
        <a href="{{ route('admin.kegiatan.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Kembali
        </a>
    </div>

    {{-- Form Card --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('admin.kegiatan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    {{-- Kolom Kiri: Informasi Utama --}}
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="judul" class="form-label fw-bold">Judul Kegiatan</label>
                            <input type="text" class="form-control @error('judul') is-invalid @enderror" id="judul" name="judul" value="{{ old('judul') }}" placeholder="Contoh: Kajian Akbar Menyambut Ramadhan" required>
                            @error('judul') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label fw-bold">Deskripsi</label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="5" placeholder="Jelaskan detail mengenai kegiatan ini..." required>{{ old('deskripsi') }}</textarea>
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
                            <input type="text" class="form-control @error('tempat') is-invalid @enderror" id="tempat" name="tempat" value="{{ old('tempat') }}" placeholder="Contoh: Masjid Kampus Al-Azhar" required>
                            @error('tempat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    
                    {{-- Kolom Kanan: Metadata & Poster --}}
                    <div class="col-md-4">
                        <div class="card bg-light border">
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="devisi_id" class="form-label fw-bold">Devisi Penyelenggara</label>
                                    <select class="form-select @error('devisi_id') is-invalid @enderror" id="devisi_id" name="devisi_id">
                                        <option value="">-- Umum (Semua Devisi) --</option>
                                        @foreach($devisis as $devisi)
                                            <option value="{{ $devisi->id }}" {{ old('devisi_id', $selectedDevisiId) == $devisi->id ? 'selected' : '' }}>
                                                {{ $devisi->nama_devisi }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">Pilih devisi jika kegiatan ini khusus untuk mereka.</div>
                                    @error('devisi_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="poster" class="form-label fw-bold">Poster Kegiatan <span class="text-muted">(Opsional)</span></label>
                                    <input class="form-control @error('poster') is-invalid @enderror" type="file" id="poster" name="poster">
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
                    <a href="{{ route('admin.kegiatan.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection