@extends('layouts.admin')

@section('title', 'Manajemen Devisi')

@section('content')
<div class="container-fluid">
    {{-- Notifikasi Sukses --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-diagram-3-fill me-2"></i>
            Manajemen Devisi
        </h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahDevisiModal">
            <i class="bi bi-plus-circle-fill me-1"></i>
            Tambah Devisi
        </button>
    </div>

    {{-- Devisi Table Card --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" style="width: 5%;">#</th>
                            <th scope="col">Nama Devisi</th>
                            <th scope="col">Penanggung Jawab (PJ)</th>
                            <th scope="col" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($devisis as $index => $devisi)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <a href="{{ route('admin.devisi.show', $devisi->id) }}">{{ $devisi->nama_devisi }}</a>
                                </td>
                                <td>{{ $devisi->pj->name ?? 'Belum ada PJ' }}</td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-warning btn-edit"
                                            data-id="{{ $devisi->id }}"
                                            data-nama="{{ $devisi->nama_devisi }}"
                                            data-deskripsi="{{ $devisi->deskripsi }}"
                                            data-pj_id="{{ $devisi->pj_id }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editDevisiModal">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                    <form action="{{ route('admin.devisi.destroy', $devisi->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus devisi ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    Belum ada data devisi. Silakan tambahkan devisi baru.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="tambahDevisiModal" tabindex="-1" aria-labelledby="tambahDevisiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.devisi.store') }}" method="POST">
                @csrf
                <div class="modal-header"><h5 class="modal-title" id="tambahDevisiModalLabel"><i class="bi bi-plus-circle-fill me-2"></i>Tambah Devisi Baru</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
                <div class="modal-body">
                    <div class="mb-3"><label for="nama_devisi" class="form-label">Nama Devisi</label><input type="text" class="form-control" id="nama_devisi" name="nama_devisi" placeholder="Contoh: Syiar" required></div>
                    <div class="mb-3"><label for="deskripsi" class="form-label">Deskripsi</label><textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" placeholder="Jelaskan tugas dari devisi ini"></textarea></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Simpan</button></div>
            </form>
        </div>
    </div>
</div>

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
                    
                    {{-- DROPDOWN UNTUK PJ --}}
                    <div class="mb-3">
                        <label for="edit_pj_id" class="form-label">Penanggung Jawab (PJ)</label>
                        <select class="form-select" id="edit_pj_id" name="pj_id">
                            <option value="">-- Tidak ada PJ --</option>
                            @foreach ($calon_pj as $pj)
                                <option value="{{ $pj->id }}">{{ $pj->name }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-primary">Update</button></div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const editButtons = document.querySelectorAll('.btn-edit');
    const editForm = document.getElementById('editDevisiForm');
    const editNamaInput = document.getElementById('edit_nama_devisi');
    const editDeskripsiInput = document.getElementById('edit_deskripsi');
    const editPjSelect = document.getElementById('edit_pj_id');

    editButtons.forEach(button => {
        button.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const nama = this.getAttribute('data-nama');
            const deskripsi = this.getAttribute('data-deskripsi');
            const pj_id = this.getAttribute('data-pj_id');

            editForm.action = `/admin/devisi/${id}`;
            editNamaInput.value = nama;
            editDeskripsiInput.value = deskripsi;
            editPjSelect.value = pj_id;
        });
    });
});
</script>
@endpush