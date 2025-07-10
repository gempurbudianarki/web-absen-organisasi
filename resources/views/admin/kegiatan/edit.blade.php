@extends('layouts.admin')

@section('title', 'Edit Kegiatan')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-pencil-square me-2"></i>
            Edit Kegiatan: {{ $kegiatan->judul }}
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
                
                <div class="mb-3">
                    <label for="judul" class="form-label">Judul Kegiatan</label>
                    <input type="text" class="form-control" id="judul" name="judul" value="{{ old('judul', $kegiatan->judul) }}" required>
                </div>
                <div class="mb-3">
                    <label for="devisi_id" class="form-label">Devisi Penyelenggara</label>
                    <select class="form-select" name="devisi_id" id="devisi_id" required>
                        <option value="">-- Pilih Devisi --</option>
                        @foreach($devisis as $devisi)
                            <option value="{{ $devisi->id }}" {{ $kegiatan->devisi_id == $devisi->id ? 'selected' : '' }}>
                                {{ $devisi->nama_devisi }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi</label>
                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required>{{ old('deskripsi', $kegiatan->deskripsi) }}</textarea>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="waktu_mulai" class="form-label">Waktu Mulai</label>
                        <input type="datetime-local" class="form-control" id="waktu_mulai" name="waktu_mulai" value="{{ old('waktu_mulai', \Carbon\Carbon::parse($kegiatan->waktu_mulai)->format('Y-m-d\TH:i')) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="waktu_selesai" class="form-label">Waktu Selesai</label>
                        <input type="datetime-local" class="form-control" id="waktu_selesai" name="waktu_selesai" value="{{ old('waktu_selesai', \Carbon\Carbon::parse($kegiatan->waktu_selesai)->format('Y-m-d\TH:i')) }}" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="tempat" class="form-label">Tempat</label>
                    <input type="text" class="form-control" id="tempat" name="tempat" value="{{ old('tempat', $kegiatan->tempat) }}" required>
                </div>
                <div class="mb-3">
                    <label for="poster" class="form-label">Ganti Poster (Opsional)</label>
                    <input class="form-control" type="file" id="poster" name="poster" accept="image/*">
                    @if($kegiatan->poster)
                        <small class="form-text text-muted">Poster saat ini:</small>
                        <img src="{{ $kegiatan->poster }}" alt="Poster" class="img-thumbnail mt-2" style="max-height: 150px;">
                    @endif
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection