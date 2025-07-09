@extends('layouts.admin')

@section('title', 'LEMS Dashboard')

@section('content')

    @push('styles')
        <style>
            .card-border-left {
                border-left: 8px solid !important;
                border-radius: 0.75rem;
            }
            .card-users { border-color: #0d6efd !important; }
            .card-anggota { border-color: #198754 !important; }
            .card-kegiatan { border-color: #6f42c1 !important; }
            .card-absensi { border-color: #fd7e14 !important; }
            .card-pengumuman { border-color: #dc3545 !important; }
            
            .text-purple { color: #6f42c1 !important; }
        </style>
    @endpush

    <div class="row g-3 mt-1">
        <div class="col-md-4 col-xl-3">
            <div class="card card-border-left card-users text-center shadow-sm">
                <div class="card-body text-primary">
                    <i class="bi bi-people-fill display-6 mb-2"></i>
                    <h5 class="card-title">Total User</h5>
                    <p class="display-6 mb-0">{{ $userCount }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-xl-3">
            <div class="card card-border-left card-anggota text-center shadow-sm">
                <div class="card-body text-success">
                    <i class="bi bi-person-workspace display-6 mb-2"></i>
                    <h5 class="card-title">Total Anggota</h5>
                    <p class="display-6 mb-0">{{ $anggotaCount }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-xl-3">
            <div class="card card-border-left card-kegiatan text-center shadow-sm">
                <div class="card-body text-purple">
                    <i class="bi bi-calendar-event-fill display-6 mb-2"></i>
                    <h5 class="card-title">Total Kegiatan</h5>
                    <p class="display-6 mb-0">{{ $kegiatanCount }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-xl-3">
            <div class="card card-border-left card-pengumuman text-center shadow-sm">
                <div class="card-body text-danger">
                    <i class="bi bi-megaphone-fill display-6 mb-2"></i>
                    <h5 class="card-title">Total Pengumuman</h5>
                    <p class="display-6 mb-0">{{ $announcementCount }}</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 col-xl-3">
            <div class="card card-border-left card-absensi text-center shadow-sm">
                <div class="card-body text-warning">
                    <i class="bi bi-clipboard2-check-fill display-6 mb-2"></i>
                    <h5 class="card-title">Total Absensi</h5>
                    <p class="display-6 mb-0">{{ $attendanceCount }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">Distribusi Peran</h5>
                    <canvas id="userChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-3">Aktivitas Sistem</h5>
                    <canvas id="logChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const userChart = new Chart(document.getElementById('userChart'), {
            type: 'doughnut',
            data: {
                labels: ['Admin', 'PJ', 'Anggota'],
                datasets: [{
                    data: [
                        {{ \App\Models\User::role('admin')->count() }}, 
                        {{ \App\Models\User::role('pj')->count() }},
                        {{ $anggotaCount }}
                    ],
                    backgroundColor: ['#dc3545', '#ffc107', '#198754']
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });

        const logChart = new Chart(document.getElementById('logChart'), {
            type: 'bar',
            data: {
                labels: ['Kegiatan', 'Pengumuman', 'Absensi'],
                datasets: [{
                    label: 'Total',
                    data: [{{ $kegiatanCount }}, {{ $announcementCount }}, {{ $attendanceCount }}],
                    backgroundColor: ['#6f42c1', '#dc3545', '#fd7e14']
                }]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true } },
                plugins: { legend: { display: false } }
            }
        });
    </script>
@endpush