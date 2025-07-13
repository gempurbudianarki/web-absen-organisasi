@extends('layouts.admin')

@section('title', 'Manajemen Devisi')

@push('styles')
<style>
    .devisi-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .devisi-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
    .devisi-card-header {
        background: linear-gradient(135deg, #0C342C 0%, #1a4f43 100%);
        color: white;
    }
    .btn-edit-devisi {
        background-color: #ffc107;
        border-color: #ffc107;
        color: #000;
    }
    .btn-delete-devisi {
        background-color: #dc3545;
        border-color: #dc3545;
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
     @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Header Halaman --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-diagram-3-fill me-2"></i>
            Manajemen Devisi
        </h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahDevisiModal">
            <i class="bi bi-plus-circle-fill me-1"></i>
            Tambah Devisi Baru
        </button>
    </div>

    {{-- Daftar Devisi dalam bentuk Kartu --}}
    <div class="row g-4">
        @forelse ($devisis as $devisi)
            <div class="col-md-6 col-lg-4">
                <div class="card devisi-card shadow-sm border-0 h-100">
                    <div class="card-header devisi-card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-white">{{ $devisi->nama_devisi }}</h5>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-light" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item btn-edit" href="#"
                                       data-id="{{ $devisi->id }}"
                                       data-nama="{{ $devisi->nama_devisi }}"
                                       data-deskripsi="{{ $devisi->deskripsi }}"
                                       data-pj_id="{{ $devisi->pj_id }}"
                                       data-bs-toggle="modal"
                                       data-bs-target="#editDevisiModal">
                                       <i class="bi bi-pencil-fill me-2"></i>Edit
                                    </a>
                                </li>
                                <li>
                                    <form action="{{ route('admin.devisi.destroy', $devisi->id) }}" method="POST" onsubmit="return confirm('Peringatan: Menghapus devisi ini akan melepaskan semua anggotanya. Lanjutkan?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-trash-fill me-2"></i>Hapus
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <p class="text-muted flex-grow-1">{{ Str::limit($devisi->deskripsi, 100) ?: 'Tidak ada deskripsi.' }}</p>
                        <div class="mt-3">
                            <p class="mb-1">
                                <i class="bi bi-person-workspace me-2 text-primary"></i>
                                <strong>PJ:</strong> {{ $devisi->pj->name ?? 'Belum Ditentukan' }}
                            </p>
                            <p class="mb-0">
                                <i class="bi bi-people-fill me-2 text-success"></i>
                                <strong>Anggota:</strong> {{ $devisi->anggota_count }} Orang
                            </p>
                        </div>
                    </div>
                    <div class="card-footer bg-light border-0">
                        <a href="{{ route('admin.devisi.show', $devisi->id) }}" class="btn btn-sm btn-outline-dark w-100">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center text-muted py-5">
                    <i class="bi bi-box2-heart fs-1"></i>
                    <h5 class="mt-3">Belum ada devisi yang dibuat.</h5>
                    <p>Silakan tambahkan devisi baru untuk memulai.</p>
                </div>
            </div>
        @endforelse
    </div>
</div>

{{-- Modal Tambah Devisi --}}
<div class="modal fade" id="tambahDevisiModal" tabindex="-1" aria-labelledby="tambahDevisiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.devisi.store') }}" method="POST">
                @csrf
                <div class="modal-header"><h5 class="modal-title" id="tambahDevisiModalLabel"><i class="bi bi-plus-circle-fill me-2"></i>Tambah Devisi Baru</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
                <div class="modal-body">
                    <div class="mb-3"><label for="nama_devisi" class="form-label">Nama Devisi</label><input type="text" class="form-control" name="nama_devisi" placeholder="Contoh: Syiar dan Pelayanan Kampus" required></div>
                    <div class="mb-3"><label for="deskripsi" class="form-label">Deskripsi Singkat</label><textarea class="form-control" name="deskripsi" rows="3" placeholder="Jelaskan fokus dan tugas utama dari devisi ini"></textarea></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Edit Devisi --}}
<div class="modal fade" id="editDevisiModal" tabindex="-1" aria-labelledby="editDevisiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="editDevisiForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header"><h5 class="modal-title" id="editDevisiModalLabel"><i class="bi bi-pencil-square me-2"></i>Edit Devisi</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
                <div class="modal-body">
                    <div class="mb-3"><label for="edit_nama_devisi" class="form-label">Nama Devisi</label><input type="text" class="form-control" id="edit_nama_devisi" name="nama_devisi" required></div>
                    <div class="mb-3"><label for="edit_deskripsi" class="form-label">Deskripsi</label><textarea class="form-control" id="edit_deskripsi" name="deskripsi" rows="3"></textarea></div>
                    <div class="mb-3">
                        <label for="edit_pj_id" class="form-label">Penanggung Jawab (PJ)</label>
                        <select class="form-select" id="edit_pj_id" name="pj_id">
                            <option value="">-- Tidak Ada PJ --</option>
                            @foreach ($calon_pj as $pj)
                                <option value="{{ $pj->id }}">{{ $pj->name }}</option>
                            @endforeach
                        </select>
                         <div class="form-text">Hanya user yang belum menjadi PJ di devisi lain yang muncul di sini.</div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan Perubahan</button></div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const editModal = document.getElementById('editDevisiModal');
    editModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-id');
        const nama = button.getAttribute('data-nama');
        const deskripsi = button.getAttribute('data-deskripsi');
        const pj_id = button.getAttribute('data-pj_id');

        const form = editModal.querySelector('#editDevisiForm');
        const namaInput = editModal.querySelector('#edit_nama_devisi');
        const deskripsiInput = editModal.querySelector('#edit_deskripsi');
        const pjSelect = editModal.querySelector('#edit_pj_id');

        // Dinamis mengatur action form
        form.action = `/admin/devisi/${id}`;

        // Mengisi nilai ke dalam form
        namaInput.value = nama;
        deskripsiInput.value = deskripsi;

        // Mengatur PJ yang sedang terpilih di dropdown
        if (pj_id) {
            pjSelect.value = pj_id;
        } else {
            pjSelect.value = "";
        }
    });
});
</script>
@endpush