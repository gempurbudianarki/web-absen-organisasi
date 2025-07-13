@extends('layouts.admin')

@section('title', 'Laporan Absensi')

@push('styles')
<style>
    .stat-card {
        border-left: 5px solid;
        border-radius: .5rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
    .stat-card .stat-icon {
        font-size: 2.8rem;
        opacity: 0.15;
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    {{-- Notifikasi --}}
    @if (session('success'))<div class="alert alert-success alert-dismissible fade show" role="alert"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>@endif
    @if (session('error'))<div class="alert alert-danger alert-dismissible fade show" role="alert"><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>@endif

    {{-- Header Halaman --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="bi bi-journal-text me-2"></i>Laporan Absensi</h4>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.absensi.export', request()->query()) }}" class="btn btn-success"><i class="bi bi-file-earmark-excel-fill me-1"></i>Ekspor ke Excel</a>
            <a href="{{ route('admin.absensi.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle-fill me-1"></i>Input Absensi Manual</a>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3"><i class="bi bi-funnel-fill me-2"></i>Filter Laporan</h5>
            <form method="GET" action="{{ route('admin.absensi.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3"><label for="start_date" class="form-label">Tanggal Mulai</label><input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}"></div>
                <div class="col-md-3"><label for="end_date" class="form-label">Tanggal Selesai</label><input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}"></div>
                <div class="col-md-2"><label for="devisi_id" class="form-label">Devisi</label><select class="form-select" id="devisi_id" name="devisi_id"><option value="">Semua Devisi</option>@foreach ($devisis as $devisi)<option value="{{ $devisi->id }}" {{ $selectedDevisiId == $devisi->id ? 'selected' : '' }}>{{ $devisi->nama_devisi }}</option>@endforeach</select></div>
                <div class="col-md-2"><label for="kegiatan_id" class="form-label">Kegiatan</label><select class="form-select" id="kegiatan_id" name="kegiatan_id"><option value="">Semua Kegiatan</option>@foreach ($kegiatans as $kegiatan)<option value="{{ $kegiatan->id }}" {{ $selectedKegiatanId == $kegiatan->id ? 'selected' : '' }}>{{ $kegiatan->judul }}</option>@endforeach</select></div>
                <div class="col-md-2 d-flex"><a href="{{ route('admin.absensi.index') }}" class="btn btn-secondary w-100 me-2">Reset</a><button type="submit" class="btn btn-info w-100">Filter</button></div>
            </form>
        </div>
    </div>

    {{-- Kartu Statistik --}}
    <div class="row g-4 mb-4">
        <div class="col-md-3"><div class="card stat-card shadow-sm h-100" style="border-color: #198754;"><div class="card-body position-relative"><i class="bi bi-person-check-fill stat-icon text-success"></i><h6 class="text-muted text-uppercase small">Total Hadir</h6><p class="display-5 fw-bold mb-0">{{ $stats->totalHadir ?? 0 }}</p></div></div></div>
        <div class="col-md-3"><div class="card stat-card shadow-sm h-100" style="border-color: #ffc107;"><div class="card-body position-relative"><i class="bi bi-person-exclamation-fill stat-icon text-warning"></i><h6 class="text-muted text-uppercase small">Total Izin/Sakit</h6><p class="display-5 fw-bold mb-0">{{ ($stats->totalIzin ?? 0) + ($stats->totalSakit ?? 0) }}</p></div></div></div>
        <div class="col-md-3"><div class="card stat-card shadow-sm h-100" style="border-color: #6c757d;"><div class="card-body position-relative"><i class="bi bi-person-x-fill stat-icon text-secondary"></i><h6 class="text-muted text-uppercase small">Total Alpa</h6><p class="display-5 fw-bold mb-0">{{ $stats->totalAlpa ?? 0 }}</p></div></div></div>
        <div class="col-md-3"><div class="card stat-card shadow-sm h-100" style="border-color: #0d6efd;"><div class="card-body position-relative"><i class="bi bi-pie-chart-fill stat-icon text-primary"></i><h6 class="text-muted text-uppercase small">Partisipasi (%)</h6><p class="display-5 fw-bold mb-0">{{ $persentaseKehadiran }}<small class="fs-4">%</small></p></div></div></div>
    </div>
    
    {{-- Grafik Tren Kehadiran --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body"><h5 class="card-title">Tren Kehadiran (Hadir)</h5><canvas id="attendanceTrendChart" height="100"></canvas></div>
    </div>

    {{-- Tabel Log Absensi --}}
    <div class="card shadow-sm">
        <div class="card-header bg-light"><h5 class="mb-0">Detail Log Absensi</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light"><tr><th>#</th><th>Nama Anggota</th><th>Kegiatan</th><th>Waktu Absen</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse ($absensiLogs as $log)
                            <tr>
                                <td>{{ $loop->iteration + $absensiLogs->firstItem() - 1 }}</td>
                                <td><div><a href="{{ route('admin.users.show', $log->user->id) }}">{{ $log->user->name ?? 'N/A' }}</a></div><small class="text-muted">{{ $log->user->devisi->nama_devisi ?? 'Umum' }}</small></td>
                                <td>{{ $log->kegiatan->judul ?? 'N/A' }}</td>
                                <td>{{ $log->waktu_absen->isoFormat('dddd, D MMM YYYY, H:mm') }}</td>
                                <td>
                                    @if($log->status == 'hadir')<span class="badge bg-success">Hadir</span>@elseif($log->status == 'izin')<span class="badge bg-warning text-dark">Izin</span>@elseif($log->status == 'sakit')<span class="badge bg-info text-dark">Sakit</span>@elseif($log->status == 'alpa')<span class="badge bg-secondary">Alpa</span>@endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">Tidak ada data absensi untuk filter yang dipilih.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($absensiLogs->hasPages())<div class="d-flex justify-content-end mt-3">{{ $absensiLogs->withQueryString()->links() }}</div>@endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('attendanceTrendChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($trendLabels),
            datasets: [{
                label: 'Jumlah Kehadiran',
                data: @json($trendData),
                backgroundColor: 'rgba(25, 135, 84, 0.6)',
                borderColor: 'rgba(25, 135, 84, 1)',
                borderWidth: 1,
                borderRadius: 4,
                hoverBackgroundColor: 'rgba(25, 135, 84, 0.8)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {y: {beginAtZero: true, ticks: {stepSize: 1}}},
            plugins: {legend: {display: false}, tooltip: {backgroundColor: '#0C342C', titleFont: {size: 14}, bodyFont: {size: 12}}}
        }
    });
});
</script>
@endpush