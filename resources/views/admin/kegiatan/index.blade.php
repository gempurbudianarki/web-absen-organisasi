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
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahKegiatanModal">
            <i class="bi bi-plus-circle-fill me-1"></i>
            Tambah Kegiatan
        </button>
    </div>

    {{-- Tabel Kegiatan --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" style="width: 5%;">#</th>
                            <th scope="col">Judul Kegiatan</th>
                            <th scope="col">Devisi Penyelenggara</th>
                            <th scope="col">Jadwal</th>
                            <th scope="col">Tempat</th>
                            <th scope="col" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kegiatans as $index => $kegiatan)
                            <tr>
                                <td>{{ $kegiatans->firstItem() + $index }}</td>
                                <td>{{ $kegiatan->judul }}</td>
                                <td>{{ $kegiatan->devisi->nama_devisi ?? 'N/A' }}</td>
                                <td>{{ \Carbon\Carbon::parse($kegiatan->waktu_mulai)->format('d M Y, H:i') }}</td>
                                <td>{{ $kegiatan->tempat }}</td>
                                <td class="text-center">
                                    {{-- PERBAIKAN: Tombol Detail/Mata sekarang menjadi link ke halaman edit --}}
                                    <a href="{{ route('admin.kegiatan.edit', $kegiatan->id) }}" class="btn btn-sm btn-info" title="Lihat Detail">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    
                                    <a href="{{ route('admin.kegiatan.edit', $kegiatan->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>

                                    {{-- PERBAIKAN: Memastikan form untuk Hapus sudah benar --}}
                                    <form action="{{ route('admin.kegiatan.destroy', $kegiatan->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kegiatan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    Belum ada kegiatan yang dibuat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- Pagination --}}
            <div class="d-flex justify-content-end mt-3">
                {{ $kegiatans->links() }}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="tambahKegiatanModal" tabindex="-1" aria-labelledby="tambahKegiatanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.kegiatan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahKegiatanModalLabel"><i class="bi bi-plus-circle-fill me-2"></i>Tambah Kegiatan Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="judul" class="form-label">Judul Kegiatan</label>
                        <input type="text" class="form-control" id="judul" name="judul" required>
                    </div>
                    <div class="mb-3">
                        <label for="devisi_id" class="form-label">Devisi Penyelenggara</label>
                        <select class="form-select" name="devisi_id" id="devisi_id" required>
                            <option value="">-- Pilih Devisi --</option>
                            @foreach($devisis as $devisi)
                                <option value="{{ $devisi->id }}">{{ $devisi->nama_devisi }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="waktu_mulai" class="form-label">Waktu Mulai</label>
                            <input type="datetime-local" class="form-control" id="waktu_mulai" name="waktu_mulai" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="waktu_selesai" class="form-label">Waktu Selesai</label>
                            <input type="datetime-local" class="form-control" id="waktu_selesai" name="waktu_selesai" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="tempat" class="form-label">Tempat</label>
                        <input type="text" class="form-control" id="tempat" name="tempat" required>
                    </div>
                    <div class="mb-3">
                        <label for="poster" class="form-label">Poster Kegiatan (Opsional)</label>
                        <input class="form-control" type="file" id="poster" name="poster" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Kegiatan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection