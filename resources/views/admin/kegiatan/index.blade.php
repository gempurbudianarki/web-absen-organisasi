@extends('layouts.admin')

@section('title', 'Manajemen Kegiatan')

@section('content')
<div class="container-fluid">
    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Header Halaman --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-calendar-event-fill me-2"></i>
            Manajemen Kegiatan
        </h4>
        <a href="{{ route('admin.kegiatan.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle-fill me-1"></i>
            Tambah Kegiatan
        </a>
    </div>

    {{-- Daftar Kegiatan dalam bentuk Grid Kartu --}}
    <div class="row g-4">
        @forelse ($kegiatans as $kegiatan)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0" style="transition: all 0.2s ease-in-out;">
                    <a href="{{ route('admin.kegiatan.edit', $kegiatan->id) }}">
                        <img src="{{ $kegiatan->poster_url }}" class="card-img-top" alt="Poster {{ $kegiatan->judul }}" style="height: 200px; object-fit: cover;">
                    </a>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ $kegiatan->judul }}</h5>
                        <div class="card-subtitle mb-2">
                            @if ($kegiatan->devisi)
                                <span class="badge bg-primary">{{ $kegiatan->devisi->nama_devisi }}</span>
                            @else
                                <span class="badge bg-success">Umum (Semua Devisi)</span>
                            @endif
                        </div>
                        <p class="card-text text-muted small flex-grow-1">{{ Str::limit($kegiatan->deskripsi, 100) }}</p>
                        <div class="mt-3">
                            <p class="mb-1 small"><i class="bi bi-calendar-week-fill me-2 text-primary"></i>{{ $kegiatan->waktu_mulai->isoFormat('dddd, D MMMM YYYY') }}</p>
                            <p class="mb-0 small"><i class="bi bi-clock-fill me-2 text-success"></i>Pukul {{ $kegiatan->waktu_mulai->isoFormat('HH:mm') }} WIB</p>
                            <p class="mb-0 small"><i class="bi bi-geo-alt-fill me-2 text-danger"></i>{{ $kegiatan->tempat }}</p>
                        </div>
                    </div>
                    <div class="card-footer bg-light d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.kegiatan.edit', $kegiatan->id) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                            <i class="bi bi-pencil-fill"></i> Edit
                        </a>
                        <form action="{{ route('admin.kegiatan.destroy', $kegiatan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kegiatan ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                <i class="bi bi-trash-fill"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center text-muted py-5">
                    <i class="bi bi-calendar-x fs-1"></i>
                    <h5 class="mt-3">Belum ada kegiatan yang dibuat.</h5>
                    <p>Klik tombol "Tambah Kegiatan" untuk memulai.</p>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Paginasi --}}
    @if ($kegiatans->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $kegiatans->links() }}
        </div>
    @endif
</div>
@endsection