@extends('layouts.admin')

@section('title', 'Laporan Absensi')

@push('styles')
<style>
    .stat-card { border-left: 5px solid; border-radius: .5rem; }
    .stat-card .stat-icon { font-size: 2.5rem; opacity: 0.2; position: absolute; right: 15px; top: 50%; transform: translateY(-50%); }
</style>
@endpush

@section('content')
<div class="container-fluid">
    @if (session('success'))<div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>@endif
    @if (session('error'))<div class="alert alert-danger alert-dismissible fade show" role="alert">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>@endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="bi bi-journal-text me-2"></i>Laporan Absensi</h4>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.absensi.export', request()->query()) }}" class="btn btn-success"><i class="bi bi-file-earmark-excel-fill me-1"></i>Ekspor ke Excel</a>
            <a href="{{ route('admin.absensi.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle-fill me-1"></i>Input Absensi Manual</a>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Filter Data Laporan</h5>
            <form method="GET" action="{{ route('admin.absensi.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3"><label for="start_date" class="form-label">Tanggal Mulai</label><input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}"></div>
                <div class="col-md-3"><label for="end_date" class="form-label">Tanggal Selesai</label><input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}"></div>
                <div class="col-md-3"><label for="devisi_id" class="form-label">Berdasarkan Devisi</label><select class="form-select" id="devisi_id" name="devisi_id"><option value="">Semua Devisi</option>@foreach ($devisis as $devisi)<option value="{{ $devisi->id }}" {{ $selectedDevisiId == $devisi->id ? 'selected' : '' }}>{{ $devisi->nama_devisi }}</option>@endforeach</select></div>
                <div class="col-md-3"><label for="kegiatan_id" class="form-label">Berdasarkan Kegiatan</label><select class="form-select" id="kegiatan_id" name="kegiatan_id"><option value="">Semua Kegiatan</option>@foreach ($kegiatans as $kegiatan)<option value="{{ $kegiatan->id }}" {{ $selectedKegiatanId == $kegiatan->id ? 'selected' : '' }}>{{ $kegiatan->judul }}</option>@endforeach</select></div>
                <div class="col-md-12 text-end mt-3"><a href="{{ route('admin.absensi.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-repeat me-1"></i> Reset</a><button type="submit" class="btn btn-info"><i class="bi bi-search me-1"></i> Terapkan</button></div>
            </form>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3"><div class="card stat-card shadow-sm h-100" style="border-color: #198754;"><div class="card-body position-relative"><i class="bi bi-person-check-fill stat-icon text-success"></i><h6 class="text-muted text-uppercase small">Total Hadir</h6><p class="display-6 fw-bold mb-0">{{ $totalHadir }}</p></div></div></div>
        <div class="col-md-3"><div class="card stat-card shadow-sm h-100" style="border-color: #ffc107;"><div class="card-body position-relative"><i class="bi bi-person-exclamation-fill stat-icon text-warning"></i><h6 class="text-muted text-uppercase small">Total Izin/Sakit</h6><p class="display-6 fw-bold mb-0">{{ $totalIzin + $totalSakit }}</p></div></div></div>
        <div class="col-md-3"><div class="card stat-card shadow-sm h-100" style="border-color: #6c757d;"><div class="card-body position-relative"><i class="bi bi-person-x-fill stat-icon text-secondary"></i><h6 class="text-muted text-uppercase small">Total Alpa</h6><p class="display-6 fw-bold mb-0">{{ $totalAlpa }}</p></div></div></div>
        <div class="col-md-3"><div class="card stat-card shadow-sm h-100" style="border-color: #0d6efd;"><div class="card-body position-relative"><i class="bi bi-pie-chart-fill stat-icon text-primary"></i><h6 class="text-muted text-uppercase small">Kehadiran (%)</h6><p class="display-6 fw-bold mb-0">{{ $persentaseKehadiran }}<small class="fs-4">%</small></p></div></div></div>
    </div>
    
    <div class="card shadow-sm mb-4">
        <div class="card-body"><h5 class="card-title">Tren Kehadiran</h5><canvas id="attendanceTrendChart" height="100"></canvas></div>
    </div>

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
                                <td><div>{{ $log->user->name ?? 'N/A' }}</div><small class="text-muted">{{ $log->user->devisi->nama_devisi ?? 'Umum' }}</small></td>
                                <td>{{ $log->kegiatan->judul ?? 'N/A' }}</td>
                                <td>{{ $log->waktu_absen->isoFormat('dddd, D MMM Y, H:mm') }}</td>
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
            @if ($absensiLogs->hasPages())<div class="d-flex justify-content-end mt-3">{{ $absensiLogs->appends(request()->query())->links() }}</div>@endif
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
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {y: {beginAtZero: true, ticks: {precision: 0}}},
            plugins: {legend: {display: false}}
        }
    });
});
</script>
@endpush