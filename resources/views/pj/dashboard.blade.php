@extends('layouts.pj')

@section('title', 'Dashboard Devisi')

@push('styles')
{{-- Style ini kita adopsi dari Admin Dashboard untuk konsistensi & tampilan premium --}}
<style>
    .stat-card-premium {
        border-radius: .75rem;
        background-color: #ffffff;
        border: 1px solid #e9ecef;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        position: relative;
        overflow: hidden;
    }
    .stat-card-premium:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.1)!important;
    }
    .stat-card-premium .card-body { position: relative; z-index: 2; }
    .stat-card-premium .stat-title { color: #6c757d; font-size: 0.85rem; font-weight: 500; }
    .stat-card-premium .stat-number { font-size: 2rem; font-weight: 700; color: #212529; }
    .stat-card-premium .stat-icon {
        font-size: 1.5rem;
        padding: 0.75rem;
        border-radius: 50%;
        color: #fff;
        position: absolute;
        top: 50%;
        right: 1.25rem;
        transform: translateY(-50%);
    }
    .kegiatan-list-item { border-bottom: 1px solid #f1f1f1; transition: background-color 0.2s ease; }
    .kegiatan-list-item:last-child { border-bottom: none; }
    .kegiatan-list-item:hover { background-color: #f8f9fa; }
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
    
    {{-- Banner Sambutan Premium --}}
    <div class="welcome-banner-pj shadow-sm mb-4">
        <div class="content">
            <h1 class="display-5">Assalamu'alaikum, {{ strtok(Auth::user()->name, " ") }}.</h1>
            <p class="lead">Selamat datang kembali, Pemimpin Devisi <span class="fw-bold">{{ $devisi->nama_devisi }}</span>. Mari pacu semangat untuk kontribusi terbaik.</p>
        </div>
    </div>

    {{-- Kartu Statistik Premium --}}
    <div class="row g-4">
        <div class="col-lg-4 col-md-6">
            <div class="card stat-card-premium shadow-sm h-100">
                <div class="card-body">
                    <h6 class="stat-title text-uppercase">Total Anggota</h6>
                    <p class="stat-number mb-0">{{ $anggotaCount }}</p>
                    <div class="stat-icon" style="--icon-bg-color: #0d6efd;"><i class="bi bi-people-fill"></i></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card stat-card-premium shadow-sm h-100">
                <div class="card-body">
                    <h6 class="stat-title text-uppercase">Total Kegiatan</h6>
                    <p class="stat-number mb-0">{{ $kegiatanCount }}</p>
                    <div class="stat-icon" style="--icon-bg-color: #198754;"><i class="bi bi-calendar-event-fill"></i></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-12">
            <div class="card stat-card-premium shadow-sm h-100">
                <div class="card-body">
                    <h6 class="stat-title text-uppercase">Partisipasi (30 Hari)</h6>
                    <p class="stat-number mb-0">{{ $persentaseKehadiran }}<small>%</small></p>
                    <div class="stat-icon" style="--icon-bg-color: #0dcaf0;"><i class="bi bi-pie-chart-fill"></i></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Grafik dan Jadwal dalam Layout Dua Kolom --}}
    <div class="row g-4 mt-4">
        <div class="col-lg-5">
            <div class="card shadow-sm h-100 section-card">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-bar-chart-line-fill me-2 text-primary"></i>
                        Rekap Absensi Devisi (30 Hari)
                    </h5>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center p-3">
                    <canvas id="attendanceChart" style="max-height: 280px;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card shadow-sm h-100 section-card">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-star-fill me-2 text-warning"></i>
                        Aktivitas Terbaru Devisi
                    </h5>
                </div>
                <div class="card-body p-2">
                    <div class="list-group list-group-flush">
                        @forelse ($kegiatans as $kegiatan)
                            <a href="{{ route('pj.absensi.show', $kegiatan->id) }}" class="list-group-item kegiatan-list-item-pj list-group-item-action py-3 px-3">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1 fw-bold text-dark">{{ $kegiatan->judul }}</h6>
                                    <small class="text-success fw-medium">{{ $kegiatan->waktu_mulai->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1 small text-muted">
                                    <i class="bi bi-clock"></i> {{ $kegiatan->waktu_mulai->isoFormat('dddd, D MMMM YYYY, HH:mm') }} WIB
                                </p>
                            </a>
                        @empty
                            <div class="text-center text-muted p-5">
                                <i class="bi bi-calendar-x fs-2"></i>
                                <p class="mt-2 mb-0">Belum ada kegiatan yang dibuat.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                 <div class="card-footer bg-light text-center border-0">
                     <a href="{{ route('pj.kegiatan.index') }}">Lihat Semua Kegiatan <i class="bi bi-arrow-right-short"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- PERBAIKAN DI SINI: Hapus pemanggilan Chart.js dari sini --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('attendanceChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Hadir', 'Izin', 'Sakit', 'Alpa'],
                datasets: [{
                    label: 'Rekap Absensi',
                    data: @json($chartData),
                    backgroundColor: [
                        'rgba(25, 135, 84, 0.8)',
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(13, 202, 240, 0.8)',
                        'rgba(108, 117, 125, 0.8)'
                    ],
                    borderColor: '#fff',
                    borderWidth: 2,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            font: {
                                size: 14
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#0C342C',
                        titleFont: { size: 14 },
                        bodyFont: { size: 12 },
                        boxPadding: 8,
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed !== null) {
                                    label += context.parsed + ' orang';
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush