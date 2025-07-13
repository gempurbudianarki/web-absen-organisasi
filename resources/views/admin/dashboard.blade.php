@extends('layouts.admin')

@section('title', 'Dashboard')

@push('styles')
    <style>
        .stat-card {
            border: none;
            border-radius: .75rem;
            color: white;
            position: relative;
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 1rem 1.5rem rgba(0,0,0,.15)!important;
        }
        .stat-card .stat-icon {
            font-size: 4rem;
            position: absolute;
            right: -15px;
            bottom: -15px;
            opacity: 0.2;
            transform: rotate(-15deg);
        }
        .stat-card .stat-number {
            font-weight: 700;
        }
        .bg-card-users { background: linear-gradient(135deg, #0d6efd, #0a58ca); }
        .bg-card-devisi { background: linear-gradient(135deg, #6f42c1, #59359a); }
        .bg-card-kegiatan { background: linear-gradient(135deg, #198754, #146c43); }
        .bg-card-attendance { background: linear-gradient(135deg, #20c997, #19a279); }
        .bg-card-announcements { background: linear-gradient(135deg, #fd7e14, #d36a10); }
        .bg-card-mails { background: linear-gradient(135deg, #dc3545, #b02a37); }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    {{-- Header Selamat Datang --}}
    <div class="mb-4 p-4 rounded" style="background-color: #0C342C;">
        <h4 class="text-white mb-0">Assalamu'alaikum, {{ Auth::user()->name }}!</h4>
        <p class="text-white-50 mb-0">Selamat datang kembali di Dashboard Admin LDK At-Tadris.</p>
    </div>

    {{-- Kartu Statistik --}}
    <div class="row g-4">
        <div class="col-md-6 col-xl-4"><div class="card stat-card bg-card-users shadow-sm h-100"><div class="card-body"><i class="bi bi-people-fill stat-icon"></i><h6 class="text-uppercase small">Total Pengguna</h6><p class="display-5 stat-number mb-0">{{ $userCount }}</p></div></div></div>
        <div class="col-md-6 col-xl-4"><div class="card stat-card bg-card-devisi shadow-sm h-100"><div class="card-body"><i class="bi bi-diagram-3-fill stat-icon"></i><h6 class="text-uppercase small">Total Devisi</h6><p class="display-5 stat-number mb-0">{{ $devisiCount }}</p></div></div></div>
        <div class="col-md-6 col-xl-4"><div class="card stat-card bg-card-kegiatan shadow-sm h-100"><div class="card-body"><i class="bi bi-calendar-event-fill stat-icon"></i><h6 class="text-uppercase small">Total Kegiatan</h6><p class="display-5 stat-number mb-0">{{ $kegiatanCount }}</p></div></div></div>
        <div class="col-md-6 col-xl-4"><div class="card stat-card bg-card-attendance shadow-sm h-100"><div class="card-body"><i class="bi bi-clipboard2-check-fill stat-icon"></i><h6 class="text-uppercase small">Hadir Hari Ini</h6><p class="display-5 stat-number mb-0">{{ $attendanceTodayCount }}</p></div></div></div>
        <div class="col-md-6 col-xl-4"><div class="card stat-card bg-card-announcements shadow-sm h-100"><div class="card-body"><i class="bi bi-megaphone-fill stat-icon"></i><h6 class="text-uppercase small">Total Pengumuman</h6><p class="display-5 stat-number mb-0">{{ $announcementCount }}</p></div></div></div>
        <div class="col-md-6 col-xl-4"><div class="card stat-card bg-card-mails shadow-sm h-100"><div class="card-body"><i class="bi bi-envelope-paper-fill stat-icon"></i><h6 class="text-uppercase small">Email Terkirim</h6><p class="display-5 stat-number mb-0">{{ $mailLogCount }}</p></div></div></div>
    </div>

    {{-- Grafik --}}
    <div class="row mt-4 g-4">
        <div class="col-lg-7">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light"><h5 class="card-title mb-0">Tren Absensi (7 Hari Terakhir)</h5></div>
                <div class="card-body">
                    <canvas id="attendanceTrendChart" height="150"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light"><h5 class="card-title mb-0">Distribusi Role Pengguna</h5></div>
                <div class="card-body">
                    <canvas id="userRoleChart" height="150"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Data dari Controller
        const userRoleData = @json($userRoleData);
        const attendanceLabels = @json($attendanceLabels);
        const attendanceData = @json($attendanceData);

        // Grafik Distribusi Role Pengguna
        const userRoleCtx = document.getElementById('userRoleChart').getContext('2d');
        new Chart(userRoleCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(userRoleData).map(key => key.charAt(0).toUpperCase() + key.slice(1)),
                datasets: [{
                    label: 'User Roles',
                    data: Object.values(userRoleData),
                    backgroundColor: ['#0d6efd', '#6f42c1', '#198754', '#ffc107', '#dc3545'],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
        });

        // Grafik Tren Absensi
        const attendanceTrendCtx = document.getElementById('attendanceTrendChart').getContext('2d');
        new Chart(attendanceTrendCtx, {
            type: 'bar',
            data: {
                labels: attendanceLabels,
                datasets: [{
                    label: 'Jumlah Kehadiran',
                    data: attendanceData,
                    backgroundColor: 'rgba(25, 135, 84, 0.7)',
                    borderColor: 'rgba(25, 135, 84, 1)',
                    borderWidth: 1,
                    borderRadius: 5,
                    hoverBackgroundColor: 'rgba(25, 135, 84, 0.9)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } },
                plugins: { legend: { display: false }, tooltip: { backgroundColor: '#0C342C', titleFont: { size: 14 }, bodyFont: { size: 12 }, boxPadding: 8 } }
            }
        });
    });
</script>
@endpush