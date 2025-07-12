@extends('layouts.admin')

@section('title', 'Buat Kegiatan Baru')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-plus-circle-fill me-2"></i>
            Buat Kegiatan Baru
        </h4>
        <a href="{{ route('admin.kegiatan.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Kembali ke Daftar Kegiatan
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-4">
            <form action="{{ route('admin.kegiatan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-md-12">
                        <label for="judul" class="form-label">Judul Kegiatan</label>
                        <input type="text" class="form-control" id="judul" name="judul" value="{{ old('judul') }}" required>
                    </div>
                    <div class="col-md-12">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="4" required>{{ old('deskripsi') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="devisi_id" class="form-label">Devisi Penyelenggara</label>
                        <select name="devisi_id" id="devisi_id" class="form-select" required>
                            <option value="">-- Pilih Devisi --</option>
                            @foreach($devisis as $devisi)
                                <option value="{{ $devisi->id }}" {{ old('devisi_id', $selectedDevisiId) == $devisi->id ? 'selected' : '' }}>
                                    {{ $devisi->nama_devisi }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="tempat" class="form-label">Tempat</label>
                        <input type="text" class="form-control" id="tempat" name="tempat" value="{{ old('tempat') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="waktu_mulai" class="form-label">Waktu Mulai</label>
                        <input type="datetime-local" class="form-control" id="waktu_mulai" name="waktu_mulai" value="{{ old('waktu_mulai') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="waktu_selesai" class="form-label">Waktu Selesai (Opsional)</label>
                        <input type="datetime-local" class="form-control" id="waktu_selesai" name="waktu_selesai" value="{{ old('waktu_selesai') }}">
                    </div>
                    <div class="col-md-12">
                        <label for="poster" class="form-label">Poster/Flyer (Opsional)</label>
                        <input class="form-control" type="file" id="poster" name="poster">
                    </div>
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-primary px-4">Simpan Kegiatan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection