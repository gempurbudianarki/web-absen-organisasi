@extends('layouts.admin')

@section('title', 'Edit Kegiatan: ' . $kegiatan->judul)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-pencil-square me-2"></i>
            Edit Kegiatan
        </h4>
        <a href="{{ route('admin.kegiatan.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Kembali
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.kegiatan.update', $kegiatan->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="judul" class="form-label">Judul Kegiatan</label>
                            <input type="text" class="form-control @error('judul') is-invalid @enderror" id="judul" name="judul" value="{{ old('judul', $kegiatan->judul) }}" required>
                            @error('judul') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="5" required>{{ old('deskripsi', $kegiatan->deskripsi) }}</textarea>
                            @error('deskripsi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="waktu_mulai" class="form-label">Waktu Mulai</label>
                                <input type="datetime-local" class="form-control @error('waktu_mulai') is-invalid @enderror" id="waktu_mulai" name="waktu_mulai" value="{{ old('waktu_mulai', $kegiatan->waktu_mulai->format('Y-m-d\TH:i')) }}" required>
                                @error('waktu_mulai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="waktu_selesai" class="form-label">Waktu Selesai (Opsional)</label>
                                <input type="datetime-local" class="form-control @error('waktu_selesai') is-invalid @enderror" id="waktu_selesai" name="waktu_selesai" value="{{ old('waktu_selesai', optional($kegiatan->waktu_selesai)->format('Y-m-d\TH:i')) }}">
                                @error('waktu_selesai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                         <div class="mb-3">
                            <label for="tempat" class="form-label">Tempat</label>
                            <input type="text" class="form-control @error('tempat') is-invalid @enderror" id="tempat" name="tempat" value="{{ old('tempat', $kegiatan->tempat) }}" required>
                            @error('tempat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="devisi_id" class="form-label">Devisi Penyelenggara</label>
                            <select class="form-select @error('devisi_id') is-invalid @enderror" id="devisi_id" name="devisi_id">
                                {{-- --- PERUBAHAN DI SINI --- --}}
                                <option value="">-- Semua Devisi (Umum) --</option>
                                @foreach($devisis as $devisi)
                                    <option value="{{ $devisi->id }}" {{ old('devisi_id', $kegiatan->devisi_id) == $devisi->id ? 'selected' : '' }}>
                                        {{ $devisi->nama_devisi }}
                                    </option>
                                @endforeach
                            </select>
                            @error('devisi_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label for="poster" class="form-label">Ganti Poster (Opsional)</label>
                            <input class="form-control @error('poster') is-invalid @enderror" type="file" id="poster" name="poster">
                            <div class="form-text">Biarkan kosong jika tidak ingin mengganti poster.</div>
                            @error('poster') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            @if($kegiatan->poster)
                            <div class="mt-2">
                                <img src="{{ $kegiatan->poster_url }}" alt="Poster saat ini" class="img-thumbnail" style="max-width: 150px;">
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Update Kegiatan</button>
                    <a href="{{ route('admin.kegiatan.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection