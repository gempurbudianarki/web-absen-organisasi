@extends('layouts.admin')

@section('title', 'Detail Devisi: ' . $devisi->nama_devisi)

@section('content')
<div class="container-fluid">
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

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-diagram-3-fill me-2"></i>
            Detail Devisi
        </h4>
        <a href="{{ route('admin.devisi.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Kembali ke Daftar Devisi
        </a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h3 class="card-title">{{ $devisi->nama_devisi }}</h3>
            <p class="card-subtitle mb-2 text-muted">Penanggung Jawab: <strong>{{ $devisi->pj->name ?? 'Belum Ditentukan' }}</strong></p>
            <hr>
            <p class="card-text">{{ $devisi->deskripsi ?? 'Tidak ada deskripsi.' }}</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Daftar Anggota ({{ $devisi->anggota->count() }})</h6>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                        <i class="bi bi-person-plus-fill me-1"></i> Tambah Anggota
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($devisi->anggota as $anggota)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><a href="{{ route('users.show', $anggota->id) }}">{{ $anggota->name }}</a></td>
                                    <td>
                                        <form action="{{ route('admin.devisi.removeMember', ['devisi' => $devisi->id, 'user' => $anggota->id]) }}" method="POST" onsubmit="return confirm('Keluarkan {{ $anggota->name }} dari devisi ini?')">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm">Keluarkan</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Belum ada anggota di devisi ini.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Daftar Kegiatan ({{ $devisi->kegiatan->count() }})</h6>
                    <a href="{{ route('admin.kegiatan.create', ['devisi_id' => $devisi->id]) }}" class="btn btn-success btn-sm">
                        <i class="bi bi-plus-circle-fill me-1"></i> Buat Kegiatan
                    </a>
                </div>
                <div class="card-body">
                     <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Judul Kegiatan</th>
                                    <th>Tanggal</th>
                                    <th>Tempat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($devisi->kegiatan as $kegiatan)
                                <tr>
                                    <td>{{ $kegiatan->judul }}</td>
                                    <td>{{ \Carbon\Carbon::parse($kegiatan->waktu_mulai)->isoFormat('D MMM YYYY') }}</td>
                                    <td>{{ $kegiatan->tempat }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Devisi ini belum memiliki kegiatan.</td>
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
                        <label for="user_id" class="form-label">Pilih Anggota</label>
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