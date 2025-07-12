@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')

    @push('styles')
        <style>
            .card-border-left {
                border-left: 5px solid;
                border-radius: .5rem;
            }
            .card-users { border-color: #0d6efd; }
            .card-devisi { border-color: #6f42c1; }
            .card-kegiatan { border-color: #198754; }
            .card-attendance { border-color: #20c997; }
            .card-announcements { border-color: #fd7e14; }
            .card-mails { border-color: #dc3545; }

            .text-purple { color: #6f42c1 !important; }
            .text-teal { color: #20c997 !important; }
            
            .stat-icon {
                font-size: 2.5rem;
                opacity: 0.3;
                position: absolute;
                right: 20px;
                top: 50%;
                transform: translateY(-50%);
            }
            .card-body {
                position: relative;
            }
            .stat-number {
                font-weight: 600;
            }
        </style>
    @endpush

    <div class="row g-4">
        <div class="col-md-6 col-xl-4">
            <div class="card card-border-left card-users shadow-sm h-100">
                <div class="card-body">
                    <i class="bi bi-people-fill stat-icon text-primary"></i>
                    <h6 class="card-title text-muted text-uppercase small">Total Pengguna</h6>
                    <p class="display-5 stat-number text-primary mb-0">{{ $userCount }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-4">
            <div class="card card-border-left card-devisi shadow-sm h-100">
                <div class="card-body">
                    <i class="bi bi-diagram-3-fill stat-icon text-purple"></i>
                    <h6 class="card-title text-muted text-uppercase small">Total Devisi</h6>
                    <p class="display-5 stat-number text-purple mb-0">{{ $devisiCount }}</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 col-xl-4">
            <div class="card card-border-left card-kegiatan shadow-sm h-100">
                <div class="card-body">
                    <i class="bi bi-calendar-event-fill stat-icon text-success"></i>
                    <h6 class="card-title text-muted text-uppercase small">Total Kegiatan</h6>
                    <p class="display-5 stat-number text-success mb-0">{{ $kegiatanCount }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-4">
            <div class="card card-border-left card-attendance shadow-sm h-100">
                <div class="card-body">
                    <i class="bi bi-clipboard2-check-fill stat-icon text-teal"></i>
                    <h6 class="card-title text-muted text-uppercase small">Hadir Hari Ini</h6>
                    <p class="display-5 stat-number text-teal mb-0">{{ $attendanceTodayCount }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-4">
            <div class="card card-border-left card-announcements shadow-sm h-100">
                <div class="card-body">
                    <i class="bi bi-megaphone-fill stat-icon text-warning"></i>
                    <h6 class="card-title text-muted text-uppercase small">Total Pengumuman</h6>
                    <p class="display-5 stat-number text-warning mb-0">{{ $announcementCount }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-4">
            <div class="card card-border-left card-mails shadow-sm h-100">
                <div class="card-body">
                    <i class="bi bi-envelope-paper-fill stat-icon text-danger"></i>
                    <h6 class="card-title text-muted text-uppercase small">Total Email Terkirim</h6>
                    <p class="display-5 stat-number text-danger mb-0">{{ $mailLogCount }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4 g-4">
        <div class="col-lg-7">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title mb-3">Tren Absensi (7 Hari Terakhir)</h5>
                    <canvas id="attendanceTrendChart" height="150"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title mb-3">Distribusi Role Pengguna</h5>
                    <canvas id="userRoleChart" height="150"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @if(session('emailSuccess'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Email Sender',
                text: '{{ session('emailSuccess') }}',
                confirmButtonColor: '#3085d6',
                timer: 4000,
                showConfirmButton: false
            });
        </script>
    @endif

    <script>
        // User Role Distribution Chart
        const userRoleCtx = document.getElementById('userRoleChart').getContext('2d');
        const userRoleData = @json($userRoleData);
        new Chart(userRoleCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(userRoleData),
                datasets: [{
                    label: 'User Roles',
                    data: Object.values(userRoleData),
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 206, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });

        // --- START OF MODIFIED CODE ---
        // Attendance Trend Chart (Now with Real Data)
        const attendanceTrendCtx = document.getElementById('attendanceTrendChart').getContext('2d');
        new Chart(attendanceTrendCtx, {
            type: 'bar',
            data: {
                labels: @json($attendanceLabels), // Using real labels from controller
                datasets: [{
                    label: 'Jumlah Kehadiran',
                    data: @json($attendanceData),    // Using real data from controller
                    backgroundColor: 'rgba(32, 201, 151, 0.6)', // Using our teal color
                    borderColor: 'rgba(32, 201, 151, 1)',
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            // Ensure only whole numbers are shown on the Y-axis
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
        // --- END OF MODIFIED CODE ---
    </script>
@endpush