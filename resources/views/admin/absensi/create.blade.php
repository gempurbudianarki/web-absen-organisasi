@extends('layouts.admin')

@section('title', 'Input Absensi Manual')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-pencil-square me-2"></i>
            Input Absensi Manual
        </h4>
        <a href="{{ route('admin.absensi.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Kembali ke Laporan
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form action="{{ route('admin.absensi.store') }}" method="POST">
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
                    <div class="col-md-6 mb-3">
                        <label for="kegiatan_id" class="form-label fw-bold">Pilih Kegiatan</label>
                        <select name="kegiatan_id" id="kegiatan_id" class="form-select" required>
                            <option value="">-- Pilih Kegiatan --</option>
                            @foreach ($kegiatans as $kegiatan)
                                <option value="{{ $kegiatan->id }}">{{ $kegiatan->judul }} ({{ $kegiatan->waktu_mulai->isoFormat('D MMM Y') }})</option>
                            @endforeach
                        </select>
                    </div>
                     <div class="col-md-6 mb-3">
                        <label for="user_id" class="form-label fw-bold">Pilih Anggota</label>
                        <select name="user_id" id="user_id" class="form-select" required>
                            <option value="">-- Pilih Anggota --</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                         <label for="status" class="form-label fw-bold">Status Kehadiran</label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="hadir">Hadir</option>
                            <option value="izin">Izin</option>
                            <option value="sakit">Sakit</option>
                            <option value="alpa">Alpa</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="waktu_absen" class="form-label fw-bold">Waktu Absen</label>
                        <input type="datetime-local" class="form-control" id="waktu_absen" name="waktu_absen" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                    </div>
                    <div class="col-12 mb-3">
                        <label for="keterangan" class="form-label">Keterangan (Opsional)</label>
                        <textarea name="keterangan" id="keterangan" rows="3" class="form-control" placeholder="Contoh: Izin karena ada urusan keluarga."></textarea>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save-fill me-1"></i> Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection