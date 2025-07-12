@extends('layouts.admin')

@section('title', 'Profil Pengguna: ' . $user->name)

@section('content')
<div class="container-fluid">
    {{-- Notifikasi Sukses --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-person-badge-fill me-2"></i>
            Profil Pengguna
        </h4>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Kembali ke Daftar User
        </a>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random&size=100" alt="Avatar" class="rounded-circle mb-3">
                    <h5 class="card-title">{{ $user->name }}</h5>
                    <p class="text-muted mb-1">{{ $user->getRoleNames()->first() ?? 'No Role' }}</p>
                    <p class="text-muted small">{{ $user->email }}</p>
                    <button type="button" class="btn btn-warning btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#resetPasswordModal">
                        <i class="bi bi-key-fill"></i> Reset Password
                    </button>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="card-title fw-bold">Informasi Detail</h6>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Devisi: <span>{{ $user->devisi->nama_devisi ?? 'Tidak ada' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Tingkat: <span>{{ $user->grade_level ?? '-' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Seksi: <span>{{ $user->section ?? '-' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Terdaftar Sejak: <span>{{ $user->created_at->isoFormat('D MMMM YYYY') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0">Riwayat Absensi Terakhir</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Masuk Pagi</th>
                                    <th>Pulang Pagi</th>
                                    <th>Masuk Siang</th>
                                    <th>Pulang Siang</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($user->attendance as $log)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($log->date)->isoFormat('dddd, D/M/Y') }}</td>
                                        <td>{{ $log->am_in ? \Carbon\Carbon::parse($log->am_in)->format('H:i') : '-' }}</td>
                                        <td>{{ $log->am_out ? \Carbon\Carbon::parse($log->am_out)->format('H:i') : '-' }}</td>
                                        <td>{{ $log->pm_in ? \Carbon\Carbon::parse($log->pm_in)->format('H:i') : '-' }}</td>
                                        <td>{{ $log->pm_out ? \Carbon\Carbon::parse($log->pm_out)->format('H:i') : '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Belum ada riwayat absensi.</td>
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

<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('users.reset_password', $user->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="resetPasswordModalLabel">Reset Password untuk {{ $user->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label for="password" class="form-label">Password Baru</label>
                        <input type="password" class="form-control" name="password" id="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Password Baru</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection