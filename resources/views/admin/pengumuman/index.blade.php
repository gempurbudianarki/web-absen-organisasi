@extends('layouts.admin')

@section('title', 'Manajemen Pengumuman')

@push('head')
    {{-- Trix Editor CSS --}}
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.0/dist/trix.css">
    {{-- Trix Editor JS --}}
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.0/dist/trix.umd.min.js"></script>
    <style>
        /* Sembunyikan tombol upload file di Trix Editor */
        .trix-button-group--file-tools { display: none; }
        /* Style untuk konten dari Trix agar rapi */
        .trix-content {
            line-height: 1.6;
        }
        .trix-content h1 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            margin-top: 1.5rem;
        }
        .trix-content ul, .trix-content ol {
            padding-left: 1.5rem;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Header Halaman --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="bi bi-megaphone-fill me-2"></i>Manajemen Pengumuman</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahPengumumanModal">
            <i class="bi bi-plus-circle-fill me-1"></i>Buat Pengumuman Baru
        </button>
    </div>

    {{-- Daftar Pengumuman (Desain Kartu) --}}
    @forelse($pengumumans as $pengumuman)
        <div class="card shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h4 class="card-title">{{ $pengumuman->judul }}</h4>
                        <div class="card-subtitle mb-2 text-muted small">
                            <span class="me-3"><i class="bi bi-person-fill"></i> {{ $pengumuman->user->name }}</span>
                            <span class="me-3"><i class="bi bi-clock-fill"></i> {{ $pengumuman->waktu_publish->isoFormat('dddd, D MMMM YYYY, H:mm') }}</span>
                            <span>
                                <i class="bi bi-bullseye"></i> Untuk: 
                                @if($pengumuman->devisi)
                                    <span class="badge bg-info">{{ $pengumuman->devisi->nama_devisi }}</span>
                                @else
                                    <span class="badge bg-success">Semua Devisi</span>
                                @endif
                            </span>
                        </div>
                    </div>
                    {{-- Tombol Hapus --}}
                    <form action="{{ route('admin.pengumuman.destroy', $pengumuman->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengumuman ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Pengumuman"><i class="bi bi-trash-fill"></i></button>
                    </form>
                </div>
                <hr>
                <div class="card-text trix-content">
                    {!! $pengumuman->isi !!}
                </div>
            </div>
        </div>
    @empty
        <div class="text-center text-muted py-5">
            <i class="bi bi-bell-slash-fill fs-1"></i>
            <h5 class="mt-3">Belum ada pengumuman yang dibuat.</h5>
            <p>Klik tombol "Buat Pengumuman Baru" untuk memulai.</p>
        </div>
    @endforelse

    {{-- Paginasi --}}
    <div class="d-flex justify-content-center mt-4">
        {{ $pengumumans->links() }}
    </div>
</div>

{{-- Modal Tambah Pengumuman --}}
<div class="modal fade" id="tambahPengumumanModal" tabindex="-1" aria-labelledby="tambahPengumumanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.pengumuman.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahPengumumanModalLabel"><i class="bi bi-plus-circle-fill me-2"></i>Buat Pengumuman Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="judul" class="form-label">Judul Pengumuman</label>
                        <input type="text" class="form-control" name="judul" required>
                    </div>
                    <div class="mb-3">
                        <label for="devisi_id" class="form-label">Target Devisi</label>
                        <select name="devisi_id" class="form-select">
                            <option value="">-- Untuk Semua Devisi (Umum) --</option>
                            @foreach($devisis as $devisi)
                                <option value="{{ $devisi->id }}">{{ $devisi->nama_devisi }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="isi_pengumuman" class="form-label">Isi Pengumuman</label>
                        {{-- Hidden input yang akan diisi oleh Trix Editor --}}
                        <input id="isi_pengumuman" type="hidden" name="isi">
                        {{-- Trix Editor --}}
                        <trix-editor input="isi_pengumuman" class="form-control" style="min-height: 200px;"></trix-editor>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Publikasikan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection