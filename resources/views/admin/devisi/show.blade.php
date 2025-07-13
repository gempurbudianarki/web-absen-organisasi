@extends('layouts.admin')

@section('title', 'Detail Devisi: ' . $devisi->nama_devisi)

@push('styles')
<style>
    .stat-card {
        border-left: 5px solid;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
    .stat-card.border-primary { border-color: #0d6efd; }
    .stat-card.border-success { border-color: #198754; }
    .stat-card.border-warning { border-color: #ffc107; }
    .stat-icon {
        font-size: 2.5rem;
        opacity: 0.2;
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
    }
    .nav-tabs .nav-link {
        border-top-left-radius: .5rem;
        border-top-right-radius: .5rem;
        border-bottom: none;
        color: #6c757d;
    }
    .nav-tabs .nav-link.active {
        color: #0C342C;
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 #fff;
        font-weight: 600;
    }
    .table-search-input {
        max-width: 300px;
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

    {{-- Breadcrumb dan Header Halaman --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.devisi.index') }}">Manajemen Devisi</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $devisi->nama_devisi }}</li>
                </ol>
            </nav>
            <h4 class="mt-2 mb-0">
                <i class="bi bi-diagram-3-fill me-2"></i>
                Detail Devisi: <span class="text-primary">{{ $devisi->nama_devisi }}</span>
            </h4>
        </div>
        <a href="{{ route('admin.devisi.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Kembali
        </a>
    </div>

    {{-- Kartu Statistik --}}
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card stat-card border-primary shadow-sm h-100">
                <div class="card-body position-relative">
                    <i class="bi bi-people-fill stat-icon text-primary"></i>
                    <h6 class="text-muted text-uppercase small">Total Anggota</h6>
                    <p class="display-5 fw-bold mb-0">{{ $devisi->anggota->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card border-success shadow-sm h-100">
                <div class="card-body position-relative">
                     <i class="bi bi-calendar-check-fill stat-icon text-success"></i>
                    <h6 class="text-muted text-uppercase small">Jumlah Kegiatan</h6>
                    <p class="display-5 fw-bold mb-0">{{ $devisi->kegiatan->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card border-warning shadow-sm h-100">
                <div class="card-body position-relative">
                     <i class="bi bi-person-workspace stat-icon text-warning"></i>
                    <h6 class="text-muted text-uppercase small">Penanggung Jawab</h6>
                    <h5 class="fw-bold mb-0 mt-2">{{ $devisi->pj->name ?? 'Belum Ditentukan' }}</h5>
                </div>
            </div>
        </div>
    </div>

    {{-- Konten Utama dengan Tab --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-light border-0 p-0">
            <ul class="nav nav-tabs" id="devisiTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="anggota-tab" data-bs-toggle="tab" data-bs-target="#anggota-tab-pane" type="button" role="tab">
                        <i class="bi bi-people-fill me-1"></i> Profil & Anggota
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="kegiatan-tab" data-bs-toggle="tab" data-bs-target="#kegiatan-tab-pane" type="button" role="tab">
                        <i class="bi bi-calendar-event-fill me-1"></i> Daftar Kegiatan
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body p-4">
            <div class="tab-content" id="devisiTabContent">
                
                {{-- Tab Pane: Profil & Anggota --}}
                <div class="tab-pane fade show active" id="anggota-tab-pane" role="tabpanel">
                    <h5>Profil Devisi</h5>
                    <p class="text-muted">{{ $devisi->deskripsi ?? 'Tidak ada deskripsi.' }}</p>
                    <hr class="my-4">
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Daftar Anggota Devisi</h5>
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                            <i class="bi bi-person-plus-fill me-1"></i> Tambah Anggota
                        </button>
                    </div>
                     <input type="search" id="searchAnggota" class="form-control form-control-sm mb-3 table-search-input" placeholder="Cari nama anggota...">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 5%;">#</th>
                                    <th>Nama Anggota</th>
                                    <th>Email</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tableAnggota">
                                @forelse($devisi->anggota as $anggota)
                                <tr data-name="{{ strtolower($anggota->name) }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td><a href="{{ route('users.show', $anggota->id) }}">{{ $anggota->name }}</a></td>
                                    <td>{{ $anggota->email }}</td>
                                    <td class="text-center">
                                        <form action="{{ route('admin.devisi.removeMember', ['devisi' => $devisi->id, 'user' => $anggota->id]) }}" method="POST" onsubmit="return confirm('Keluarkan {{ $anggota->name }} dari devisi ini?')">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-danger btn-sm">Keluarkan</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">Belum ada anggota di devisi ini.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Tab Pane: Daftar Kegiatan --}}
                <div class="tab-pane fade" id="kegiatan-tab-pane" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Daftar Kegiatan Devisi</h5>
                        <a href="{{ route('admin.kegiatan.create', ['devisi_id' => $devisi->id]) }}" class="btn btn-success btn-sm">
                            <i class="bi bi-plus-circle-fill me-1"></i> Buat Kegiatan Baru
                        </a>
                    </div>
                    <input type="search" id="searchKegiatan" class="form-control form-control-sm mb-3 table-search-input" placeholder="Cari nama kegiatan...">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Judul Kegiatan</th>
                                    <th>Tanggal</th>
                                    <th>Tempat</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tableKegiatan">
                                @forelse($devisi->kegiatan as $kegiatan)
                                <tr data-name="{{ strtolower($kegiatan->judul) }}">
                                    <td>{{ $kegiatan->judul }}</td>
                                    <td>{{ \Carbon\Carbon::parse($kegiatan->waktu_mulai)->isoFormat('D MMM YYYY') }}</td>
                                    <td>{{ $kegiatan->tempat }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.kegiatan.edit', $kegiatan->id) }}" class="btn btn-outline-warning btn-sm">Edit</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">Devisi ini belum memiliki kegiatan.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah Anggota --}}
<div class="modal fade" id="addMemberModal" tabindex="-1" aria-labelledby="addMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.devisi.addMember', $devisi->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addMemberModalLabel">Tambah Anggota ke Devisi {{ $devisi->nama_devisi }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="user_id" class="form-label">Pilih Anggota (yang belum memiliki devisi)</label>
                        <select name="user_id" id="user_id" class="form-select" required>
                            <option value="">-- Pilih Anggota --</option>
                            @forelse($calon_anggota as $calon)
                                <option value="{{ $calon->id }}">{{ $calon->name }}</option>
                            @empty
                                <option value="" disabled>Semua anggota sudah memiliki devisi.</option>
                            @endforelse
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Tambahkan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Fungsi pencarian untuk tabel
    function setupSearch(inputId, tableBodyId) {
        const searchInput = document.getElementById(inputId);
        const tableBody = document.getElementById(tableBodyId);
        const tableRows = tableBody.querySelectorAll('tr');

        searchInput.addEventListener('keyup', function (e) {
            const searchTerm = e.target.value.toLowerCase();

            tableRows.forEach(row => {
                const name = row.dataset.name.toLowerCase();
                if (name.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // Terapkan fungsi pencarian pada kedua tabel
    setupSearch('searchAnggota', 'tableAnggota');
    setupSearch('searchKegiatan', 'tableKegiatan');
});
</script>
@endpush