@extends('layouts.pj')

@section('title', 'Pengumuman Devisi')

@push('head')
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.0/dist/trix.css">
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.0/dist/trix.umd.min.js"></script>
    <style>
        .trix-button-group--file-tools { display: none; }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="bi bi-megaphone-fill me-2"></i>Pengumuman Devisi {{ $devisi->nama_devisi }}</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahPengumumanModal">
            <i class="bi bi-plus-circle-fill me-1"></i>Buat Pengumuman
        </button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            @forelse($pengumumans as $pengumuman)
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h5 class="card-title">{{ $pengumuman->judul }}</h5>
                            <form action="{{ route('pj.pengumuman.destroy', $pengumuman->id) }}" method="POST" onsubmit="return confirm('Yakin ingin hapus pengumuman ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash-fill"></i></button>
                            </form>
                        </div>
                        <h6 class="card-subtitle mb-2 text-muted">
                            <i class="bi bi-clock-fill"></i> {{ $pengumuman->created_at->format('d M Y, H:i') }}
                        </h6>
                        <div class="card-text trix-content">
                            {!! $pengumuman->isi !!}
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center text-muted py-4">Anda belum membuat pengumuman.</div>
            @endforelse

            <div class="d-flex justify-content-end mt-3">
                {{ $pengumumans->links() }}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="tambahPengumumanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('pj.pengumuman.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-circle-fill me-2"></i>Buat Pengumuman Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="judul" class="form-label">Judul</label>
                        <input type="text" class="form-control" name="judul" required>
                    </div>
                    <div class="mb-3">
                        <label for="isi" class="form-label">Isi Pengumuman</label>
                        <input id="isi_pengumuman" type="hidden" name="isi">
                        <trix-editor input="isi_pengumuman"></trix-editor>
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