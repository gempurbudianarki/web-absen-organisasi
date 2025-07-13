@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@push('styles')
<style>
    /* Polesan Terakhir untuk Desain Premium */
    .welcome-card {
        background: linear-gradient(135deg, #0C342C 0%, #1a4f43 100%);
        border: none;
        border-radius: 1rem;
    }
    .stat-card-premium {
        border-radius: 1rem;
        background-color: #ffffff;
        border: 1px solid #e9ecef;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .stat-card-premium:hover {
        transform: translateY(-8px);
        box-shadow: 0 1rem 2rem rgba(32, 48, 60, 0.1) !important;
    }
    .stat-card-premium .card-body {
        position: relative;
        z-index: 2;
    }
    .stat-card-premium::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 100%;
        background-color: var(--icon-bg-color, #f8f9fa);
        border-radius: 50%;
        transition: transform 0.5s ease;
        transform: scale(0);
        z-index: 1;
    }
    .stat-card-premium:hover::before {
        transform: scale(3);
    }
    .stat-card-premium .stat-title {
        color: #6c757d;
        font-size: 0.9rem;
    }
    .stat-card-premium .stat-number {
        font-size: 2.25rem;
        font-weight: 700;
        color: #212529;
    }
    .stat-card-premium .stat-icon {
        font-size: 2rem;
        padding: 0.75rem;
        border-radius: 50%;
        color: #fff;
    }
    .chart-card, .list-card {
        border-radius: 1rem;
        border: 1px solid #e9ecef;
    }
    .kegiatan-list-item {
        border-bottom: 1px solid #f1f1f1;
        transition: background-color 0.2s ease;
    }
    .kegiatan-list-item:last-child {
        border-bottom: none;
    }
    .kegiatan-list-item:hover {
        background-color: #f8f9fa;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    {{-- Header Sapaan yang Lebih Elegan --}}
    <div class="card welcome-card shadow-sm mb-4">
        <div class="card-body p-4 text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">Assalamu'alaikum, {{ Auth::user()->name }}!</h4>
                    <p class="mb-0 text-white-50">Selamat datang kembali di pusat kendali LDK At-Tadris.</p>
                </div>
                <a href="{{ route('admin.pengumuman.index') }}" class="btn btn-outline-light">
                    <i class="bi bi-megaphone-fill me-1"></i> Buat Pengumuman
                </a>
            </div>
        </div>
    </div>

    {{-- Kartu Statistik dengan Desain Premium --}}
    <div class="row g-4">
        <div class="col-lg-3 col-md-6">
            <div class="card stat-card-premium shadow-sm h-100" style="--icon-bg-color: rgba(13, 110, 253, 0.1);">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="stat-title text-uppercase">Total Pengguna</h6>
                        <p class="stat-number mb-0" data-count="{{ $totalUsers }}">0</p>
                    </div>
                    <div class="stat-icon bg-primary"><i class="bi bi-people-fill"></i></div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card stat-card-premium shadow-sm h-100" style="--icon-bg-color: rgba(25, 135, 84, 0.1);">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="stat-title text-uppercase">Total Devisi</h6>
                        <p class="stat-number mb-0" data-count="{{ $totalDevisi }}">0</p>
                    </div>
                    <div class="stat-icon bg-success"><i class="bi bi-diagram-3-fill"></i></div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card stat-card-premium shadow-sm h-100" style="--icon-bg-color: rgba(255, 193, 7, 0.1);">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="stat-title text-uppercase">Total Kegiatan</h6>
                        <p class="stat-number mb-0" data-count="{{ $totalKegiatan }}">0</p>
                    </div>
                    <div class="stat-icon bg-warning"><i class="bi bi-calendar-event-fill"></i></div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card stat-card-premium shadow-sm h-100" style="--icon-bg-color: rgba(108, 117, 125, 0.1);">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="stat-title text-uppercase">Pengumuman Aktif</h6>
                        <p class="stat-number mb-0" data-count="{{ $totalPengumumanAktif }}">0</p>
                    </div>
                    <div class="stat-icon bg-secondary"><i class="bi bi-megaphone-fill"></i></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Grafik dan Jadwal dalam Layout yang Diperbarui --}}
    <div class="row g-4 mt-2">
        <div class="col-lg-8">
            <div class="card chart-card shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-graph-up-arrow me-2 text-primary"></i>
                        Tren Kehadiran (30 Hari Terakhir)
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="attendanceChart" height="150"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card list-card shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-calendar2-week-fill me-2 text-success"></i>
                        Kegiatan Akan Datang
                    </h5>
                </div>
                <div class="card-body p-2">
                    <div class="list-group list-group-flush">
                        @forelse ($kegiatanAkanDatang as $kegiatan)
                            <a href="{{ route('admin.kegiatan.edit', $kegiatan->id) }}" class="list-group-item kegiatan-list-item list-group-item-action border-0 py-3">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1 fw-bold text-dark">{{ $kegiatan->judul }}</h6>
                                    <small class="text-success">{{ $kegiatan->waktu_mulai->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1 small text-muted">
                                    <i class="bi bi-clock"></i> {{ $kegiatan->waktu_mulai->isoFormat('dddd, D MMM YYYY, H:mm') }} WIB
                                </p>
                            </a>
                        @empty
                            <div class="text-center text-muted p-5">
                                <i class="bi bi-calendar-x fs-2"></i>
                                <p class="mt-2 mb-0">Tidak ada kegiatan yang dijadwalkan.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // --- JavaScript untuk Efek Angka Berhitung ---
    const counters = document.querySelectorAll('.stat-number');
    const speed = 200; // Semakin besar, semakin lambat

    counters.forEach(counter => {
        const animate = () => {
            const value = +counter.getAttribute('data-count');
            const data = +counter.innerText;
            const time = value / speed;
            
            if (data < value) {
                counter.innerText = Math.ceil(data + time);
                setTimeout(animate, 10);
            } else {
                counter.innerText = value;
            }
        }
        animate();
    });

    // --- JavaScript untuk Grafik ---
    const ctx = document.getElementById('attendanceChart');
    if (ctx) {
        const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(13, 110, 253, 0.4)');
        gradient.addColorStop(1, 'rgba(13, 110, 253, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    label: 'Jumlah Kehadiran',
                    data: @json($chartData),
                    borderColor: '#0d6efd',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#0d6efd',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 7,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } },
                    x: { grid: { display: false } }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0C342C',
                        titleFont: { size: 14 },
                        bodyFont: { size: 12 },
                        boxPadding: 8,
                        intersect: false,
                        mode: 'index',
                    }
                }
            }
        });
    }
});
</script>
@endpush