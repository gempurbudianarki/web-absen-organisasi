@extends('layouts.pj')

@section('title', 'Pengumuman Devisi')

@push('head')
    {{-- Trix Editor CSS & JS --}}
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.0/dist/trix.css">
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.0/dist/trix.umd.min.js"></script>
    {{-- Style kustom untuk Trix Editor dan halaman --}}
    <style>
        .trix-button-group--file-tools { display: none; }
        .trix-content {
            line-height: 1.6;
            color: #495057;
        }
        .trix-content h1 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            margin-top: 1.5rem;
            font-weight: 600;
        }
        .trix-content ul, .trix-content ol {
            padding-left: 1.5rem;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="bi bi-megaphone-fill me-2"></i>Pengumuman Devisi {{ $devisi->nama_devisi }}</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahPengumumanModal">
            <i class="bi bi-plus-circle-fill me-1"></i>Buat Pengumuman
        </button>
    </div>

    {{-- Daftar Pengumuman --}}
    @forelse($pengumumans as $pengumuman)
        <div class="card shadow-sm mb-3">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h5 class="card-title">{{ $pengumuman->judul }}</h5>
                        <h6 class="card-subtitle mb-2 text-muted small">
                            <i class="bi bi-clock-fill"></i> Dipublikasikan pada {{ $pengumuman->created_at->isoFormat('dddd, D MMMM YYYY, H:mm') }}
                        </h6>
                    </div>
                    <form action="{{ route('pj.pengumuman.destroy', $pengumuman->id) }}" method="POST" onsubmit="return confirm('Yakin ingin hapus pengumuman ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash-fill"></i></button>
                    </form>
                </div>
                <hr>
                <div class="card-text trix-content">
                    {!! $pengumuman->isi !!}
                </div>
            </div>
        </div>
    @empty
        <div class="text-center text-muted py-5 bg-light rounded">
            <i class="bi bi-bell-slash fs-1"></i>
            <h5 class="mt-3">Anda belum membuat pengumuman.</h5>
            <p>Klik tombol "Buat Pengumuman" untuk memulai.</p>
        </div>
    @endforelse

    {{-- Paginasi --}}
    @if($pengumumans->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $pengumumans->links() }}
        </div>
    @endif
</div>

{{-- Modal Tambah Pengumuman --}}
<div class="modal fade" id="tambahPengumumanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('pj.pengumuman.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-circle-fill me-2"></i>Buat Pengumuman Baru untuk Devisi Anda</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="judul" class="form-label fw-bold">Judul</label>
                        <input type="text" class="form-control" name="judul" required>
                    </div>
                    <div class="mb-3">
                        <label for="isi_pengumuman" class="form-label fw-bold">Isi Pengumuman</label>
                        <input id="isi_pengumuman" type="hidden" name="isi">
                        <trix-editor input="isi_pengumuman" class="form-control" style="min-height: 200px;"></trix-editor>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-send-fill me-1"></i> Publikasikan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection