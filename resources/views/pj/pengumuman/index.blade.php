@extends('layouts.pj')

@section('title', 'Pengumuman Devisi')

@push('styles')
    {{-- Flatpickr (datetime picker) CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    {{-- Trix Editor CSS --}}
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.0/dist/trix.css">
    <style>
        .trix-button-group--file-tools { display: none; }
        .nav-tabs .nav-link { color: #6c757d; }
        .nav-tabs .nav-link.active { font-weight: bold; color: #0d6efd; border-color: #dee2e6 #dee2e6 #fff; }
        .pengumuman-card { border-left: 4px solid; transition: all 0.2s ease-in-out; }
        .pengumuman-card.border-primary { border-left-color: #0d6efd; }
        .pengumuman-card.border-secondary { border-left-color: #6c757d; }
        .pengumuman-card:hover { transform: translateY(-3px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.10)!important; }
        .trix-content { font-size: 1rem; line-height: 1.6; }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    {{-- Notifikasi --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Header Halaman --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold"><i class="bi bi-megaphone-fill me-2"></i>Pengumuman Devisi {{ $devisi->nama_devisi }}</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPengumumanModal">
            <i class="bi bi-plus-circle-fill me-1"></i>Buat Pengumuman
        </button>
    </div>

    {{-- KONTEN UTAMA DENGAN TAB --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white p-0 border-0">
            <ul class="nav nav-tabs nav-fill" id="pengumumanTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="aktif-tab" data-bs-toggle="tab" data-bs-target="#aktif-tab-pane" type="button">
                        <i class="bi bi-broadcast me-1"></i> Pengumuman Aktif
                        <span class="badge bg-success rounded-pill">{{ $pengumumanAktif->total() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="riwayat-tab" data-bs-toggle="tab" data-bs-target="#riwayat-tab-pane" type="button">
                        <i class="bi bi-archive-fill me-1"></i> Riwayat Pengumuman
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body bg-light">
            <div class="tab-content" id="pengumumanTabContent">
                {{-- Konten Tab Aktif --}}
                <div class="tab-pane fade show active" id="aktif-tab-pane" role="tabpanel">
                    @forelse ($pengumumanAktif as $item)
                        <div class="card shadow-sm mb-3 pengumuman-card {{ $item->user->hasRole('admin') ? 'border-secondary' : 'border-primary' }}">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h5 class="card-title mb-1">{{ $item->judul }}</h5>
                                        <div class="text-muted small">
                                            <i class="bi bi-person-fill"></i> Dibuat oleh: <strong>{{ $item->user->name }}</strong>
                                            <span class="mx-2">|</span>
                                            <i class="bi bi-clock"></i> {{ $item->created_at->isoFormat('D MMM YYYY, HH:mm') }}
                                        </div>
                                    </div>
                                    @can('delete', $item)
                                    <form action="{{ route('pj.pengumuman.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin hapus pengumuman ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash-fill"></i></button>
                                    </form>
                                    @endcan
                                </div>
                                <hr>
                                <div class="trix-content">
                                    {!! Str::limit(strip_tags($item->isi, '<br><strong><em><ul><li><ol>'), 200) !!}
                                </div>
                                @if (strlen(strip_tags($item->isi)) > 200)
                                <a class="btn-link small" data-bs-toggle="collapse" href="#collapse-{{$item->id}}" role="button">
                                    Baca Selengkapnya
                                </a>
                                <div class="collapse mt-2" id="collapse-{{$item->id}}">
                                    <div class="trix-content">{!! $item->isi !!}</div>
                                </div>
                                @endif
                            </div>
                            <div class="card-footer bg-white small text-muted">
                                Akan berakhir pada: {{ $item->expires_at ? $item->expires_at->isoFormat('D MMM YYYY, HH:mm') . ' (' . $item->expires_at->diffForHumans() . ')' : 'Selamanya' }}
                            </div>
                        </div>
                    @empty
                        <div class="text-center p-5">
                            <i class="bi bi-bell-slash fs-1 text-muted"></i>
                            <h5 class="mt-3">Tidak ada pengumuman aktif.</h5>
                        </div>
                    @endforelse
                    @if($pengumumanAktif->hasPages())
                        <div class="d-flex justify-content-center mt-3">{{ $pengumumanAktif->links() }}</div>
                    @endif
                </div>
                {{-- Konten Tab Riwayat --}}
                <div class="tab-pane fade" id="riwayat-tab-pane" role="tabpanel">
                     @forelse ($pengumumanRiwayat as $item)
                        <div class="card shadow-sm mb-3 pengumuman-card border-light">
                            <div class="card-body">
                                <h5 class="card-title mb-1">{{ $item->judul }}</h5>
                                <div class="text-muted small">
                                    <i class="bi bi-person-fill"></i> Oleh: <strong>{{ $item->user->name }}</strong>
                                    <span class="mx-2">|</span>
                                    <i class="bi bi-calendar-check"></i> Berakhir pada: {{ $item->expires_at->isoFormat('D MMM YYYY, HH:mm') }}
                                </div>
                            </div>
                        </div>
                    @empty
                         <div class="text-center p-5">
                            <i class="bi bi-archive-fill fs-1 text-muted"></i>
                            <h5 class="mt-3">Tidak ada riwayat pengumuman.</h5>
                        </div>
                    @endforelse
                    @if($pengumumanRiwayat->hasPages())
                        <div class="d-flex justify-content-center mt-3">{{ $pengumumanRiwayat->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah Pengumuman --}}
<div class="modal fade" id="createPengumumanModal" tabindex="-1" aria-labelledby="createPengumumanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('pj.pengumuman.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createPengumumanModalLabel">Buat Pengumuman Baru untuk Devisi Anda</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="judul" class="form-label fw-bold">Judul Pengumuman</label>
                        <input type="text" class="form-control" id="judul" name="judul" value="{{ old('judul') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="isi" class="form-label fw-bold">Isi Pengumuman</label>
                        <input id="isi" type="hidden" name="isi" value="{{ old('isi') }}">
                        <trix-editor input="isi" class="form-control" style="min-height: 150px;"></trix-editor>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="publish_at" class="form-label fw-bold">Mulai Tayang</label>
                            <input type="text" class="form-control" id="publish_at" name="publish_at" value="{{ old('publish_at', now()->format('Y-m-d H:i')) }}" placeholder="Pilih tanggal & waktu" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="expires_at" class="form-label fw-bold">Berhenti Tayang (Opsional)</label>
                            <input type="text" class="form-control" id="expires_at" name="expires_at" value="{{ old('expires_at') }}" placeholder="Pilih tanggal & waktu">
                            <div class="form-text">Kosongkan jika ingin tayang selamanya.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-send-fill me-1"></i> Terbitkan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Flatpickr & Trix Editor JS --}}
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script type="text/javascript" src="https://unpkg.com/trix@2.0.0/dist/trix.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const commonConfig = {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            time_24hr: true,
        };
        
        flatpickr("#publish_at", { ...commonConfig, defaultDate: 'today' });
        flatpickr("#expires_at", { ...commonConfig, minDate: 'today' });

        // Mencegah Trix Editor menyisipkan file
        document.addEventListener('trix-file-accept', function(e) {
            e.preventDefault();
            alert('Penyisipan file tidak diizinkan.');
        });
    });
</script>
@endpush