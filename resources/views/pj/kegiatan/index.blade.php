@extends('layouts.pj')

@section('title', 'Manajemen Kegiatan Devisi')

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
        <h4 class="mb-0 fw-bold">Manajemen Kegiatan Devisi: <span class="text-primary">{{ $devisi->nama_devisi }}</span></h4>
        <a href="{{ route('pj.kegiatan.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle-fill me-1"></i> Buat Kegiatan Baru
        </a>
    </div>

    {{-- Daftar Kegiatan (Card Layout Premium) --}}
    <div class="row g-4">
        @forelse ($kegiatans as $kegiatan)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0" style="transition: transform 0.2s ease-in-out;">
                    <div class="card-body d-flex flex-column">
                        {{-- Badge Indikator --}}
                        <div>
                            @if ($kegiatan->devisi_id === null)
                                <span class="badge bg-success">Kegiatan Umum</span>
                            @else
                                <span class="badge bg-primary">Kegiatan Devisi</span>
                            @endif
                        </div>
                        
                        <h5 class="card-title mt-2 mb-1">{{ $kegiatan->judul }}</h5>
                        <p class="card-text text-muted small flex-grow-1">{{ Str::limit($kegiatan->deskripsi, 120) }}</p>

                        {{-- Detail Waktu dan Tempat --}}
                        <div class="mt-3 border-top pt-3">
                            <p class="mb-1 small"><i class="bi bi-calendar-week-fill me-2 text-primary"></i>{{ \Carbon\Carbon::parse($kegiatan->waktu_mulai)->isoFormat('dddd, D MMMM YYYY') }}</p>
                            <p class="mb-1 small"><i class="bi bi-clock-fill me-2 text-success"></i>Pukul {{ \Carbon\Carbon::parse($kegiatan->waktu_mulai)->isoFormat('HH:mm') }} WIB</p>
                            <p class="mb-0 small"><i class="bi bi-geo-alt-fill me-2 text-danger"></i>{{ $kegiatan->tempat }}</p>
                        </div>
                    </div>
                    <div class="card-footer bg-light d-flex justify-content-between align-items-center gap-2">
                        {{-- Tombol Absensi selalu bisa diakses untuk melihat laporan --}}
                        <a href="{{ route('pj.absensi.show', $kegiatan->id) }}" class="btn btn-sm btn-info text-white flex-fill">
                            <i class="bi bi-clipboard2-check-fill"></i> Kelola Absensi
                        </a>
                        
                        {{-- PERBAIKAN OTORISASI: Gunakan @can Blade Directive --}}
                        {{-- Tombol Edit & Hapus hanya muncul jika PJ diizinkan oleh Policy --}}
                        @can('manage', $kegiatan)
                        <div class="btn-group">
                            <a href="{{ route('pj.kegiatan.edit', $kegiatan->id) }}" class="btn btn-sm btn-outline-secondary" title="Edit"><i class="bi bi-pencil-fill"></i></a>
                            <form action="{{ route('pj.kegiatan.destroy', $kegiatan->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus kegiatan ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash-fill"></i></button>
                            </form>
                        </div>
                        @endcan
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center text-muted py-5 bg-light rounded-3">
                    <i class="bi bi-calendar-x fs-1"></i>
                    <h5 class="mt-3">Belum ada kegiatan.</h5>
                    <p>Kegiatan dari devisi Anda atau kegiatan umum akan muncul di sini.</p>
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