@extends('layouts.admin')

@section('title', 'Laporan Absensi')

@section('content')
<div class="container-fluid">
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-journal-text me-2"></i>
            Laporan Absensi
        </h4>
        <div class="d-flex gap-2">
            <button class="btn btn-success">
                <i class="bi bi-file-earmark-excel-fill me-1"></i>
                Ekspor ke Excel
            </button>
            <a href="{{ route('admin.absensi.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle-fill me-1"></i>
                Tambah Absensi Manual
            </a>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Filter Data</h5>
            <form method="GET" action="{{ route('admin.absensi.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Tanggal Mulai</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">Tanggal Selesai</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                </div>
                <div class="col-md-3">
                    <label for="devisi_id" class="form-label">Devisi</label>
                    <select class="form-select" id="devisi_id" name="devisi_id">
                        <option value="">Semua Devisi</option>
                        @foreach ($devisis as $devisi)
                            <option value="{{ $devisi->id }}" {{ $selectedDevisiId == $devisi->id ? 'selected' : '' }}>
                                {{ $devisi->nama_devisi }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="user_id" class="form-label">Anggota</label>
                    <select class="form-select" id="user_id" name="user_id">
                        <option value="">Semua Anggota</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" {{ $selectedUserId == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-12 text-end">
                    <a href="{{ route('admin.absensi.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-repeat me-1"></i> Reset Filter
                    </a>
                    <button type="submit" class="btn btn-info">
                        <i class="bi bi-search me-1"></i> Terapkan Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Attendance Table Card --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nama Anggota</th>
                            <th>Devisi</th>
                            <th>Tanggal</th>
                            <th>Jam Masuk Pagi</th>
                            <th>Jam Pulang Pagi</th>
                            <th>Jam Masuk Siang</th>
                            <th>Jam Pulang Siang</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($absensiLogs as $log)
                            <tr>
                                <td>{{ $loop->iteration + $absensiLogs->firstItem() - 1 }}</td>
                                {{-- START OF MODIFIED CODE --}}
                                <td>{{ $log->user->name ?? 'User Not Found' }}</td>
                                <td>{{ $log->user->devisi->nama_devisi ?? 'Belum ada devisi' }}</td>
                                {{-- END OF MODIFIED CODE --}}
                                <td>{{ \Carbon\Carbon::parse($log->date)->isoFormat('dddd, D MMMM Y') }}</td>
                                <td>{{ $log->am_in ? \Carbon\Carbon::parse($log->am_in)->format('H:i') : '-' }}</td>
                                <td>{{ $log->am_out ? \Carbon\Carbon::parse($log->am_out)->format('H:i') : '-' }}</td>
                                <td>{{ $log->pm_in ? \Carbon\Carbon::parse($log->pm_in)->format('H:i') : '-' }}</td>
                                <td>{{ $log->pm_out ? \Carbon\Carbon::parse($log->pm_out)->format('H:i') : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    Tidak ada data absensi untuk rentang tanggal yang dipilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Pagination --}}
            @if ($absensiLogs->hasPages())
                <div class="mt-4">
                    {{ $absensiLogs->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection