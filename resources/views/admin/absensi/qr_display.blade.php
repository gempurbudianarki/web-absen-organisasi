<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sesi Absensi: {{ $kegiatan->judul }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
        }
        .qr-container {
            background-color: white;
            padding: 2.5rem;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.1);
        }
        .qr-code svg {
            width: 300px;
            height: 300px;
        }
    </style>
</head>
<body>
    <div class="qr-container">
        <h1 class="h3 mb-3">Absensi Kegiatan</h1>
        <h2 class="h5 mb-4 text-primary fw-bold">{{ $kegiatan->judul }}</h2>
        <p class="text-muted">Pindai QR Code di bawah ini menggunakan kamera Anda.</p>
        <div class="qr-code my-4">
            {{-- QR Code akan di-generate di sini oleh controller --}}
            {!! $qrCode !!}
        </div>
        <p class="mt-4">
            <a href="{{ route('admin.absensi.show', $kegiatan->id) }}" class="btn btn-outline-secondary">
                <i class="bi bi-people-fill me-1"></i> Lihat Laporan Kehadiran
            </a>
        </p>
        <div class="mt-3 text-muted" id="clock"></div>
    </div>

    <script>
        function updateClock() {
            const now = new Date();
            const options = {
                weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
                hour: '2-digit', minute: '2-digit', second: '2-digit'
            };
            document.getElementById('clock').textContent = now.toLocaleString('id-ID', options);
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>
</html>