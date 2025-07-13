@extends('layouts.admin')

@section('title', 'Manajemen Kegiatan')

@section('content')
<div class="container-fluid">
    {{-- Notifikasi Sukses --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
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

    {{-- Tabel Kegiatan --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" style="width: 5%;">#</th>
                            <th scope="col" style="width: 10%;">Poster</th>
                            <th scope="col">Judul & Tempat Kegiatan</th>
                            <th scope="col">Devisi Penyelenggara</th>
                            <th scope="col">Jadwal Mulai</th>
                            <th scope="col" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kegiatans as $index => $kegiatan)
                            <tr>
                                <td>{{ $kegiatans->firstItem() + $index }}</td>
                                <td>
                                    <img src="{{ $kegiatan->poster_url }}" alt="Poster" class="img-fluid rounded" style="width: 80px; height: 80px; object-fit: cover;">
                                </td>
                                <td>
                                    <a href="{{ route('admin.kegiatan.edit', $kegiatan->id) }}" class="fw-bold text-decoration-none">{{ $kegiatan->judul }}</a>
                                    <p class="text-muted small mb-0"><i class="bi bi-geo-alt-fill me-1"></i>{{ $kegiatan->tempat }}</p>
                                </td>
                                <td>
                                    {{-- --- PERUBAHAN DI SINI --- --}}
                                    @if ($kegiatan->devisi)
                                        <a href="{{ route('admin.devisi.show', $kegiatan->devisi->id) }}" class="text-decoration-none">{{ $kegiatan->devisi->nama_devisi }}</a>
                                    @else
                                        <span class="badge bg-success">Semua Devisi</span>
                                    @endif
                                </td>
                                <td>{{ $kegiatan->waktu_mulai->isoFormat('dddd, D MMM YYYY, H:mm') }}</td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.kegiatan.edit', $kegiatan->id) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <form action="{{ route('admin.kegiatan.destroy', $kegiatan->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kegiatan ini?');" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="bi bi-calendar-x fs-3 d-block mb-2"></i>
                                    Belum ada kegiatan yang dibuat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($kegiatans->hasPages())
                <div class="d-flex justify-content-end mt-3">
                    {{ $kegiatans->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection