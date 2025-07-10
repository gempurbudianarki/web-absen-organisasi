<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Absensi via Kode</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #0f172a; color: #f1f5f9; }
        .code-container { max-width: 400px; margin: 4rem auto; }
        .form-control-lg { text-align: center; font-size: 2rem; letter-spacing: 0.5rem; text-transform: uppercase; }
    </style>
</head>
<body>
    <div class="container code-container">
        <div class="text-center mb-4">
            <h2 class="fw-bold">Absensi via Kode</h2>
            <p class="text-white-50">Masukkan kode 8 digit yang diberikan oleh panitia.</p>
        </div>

        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ route('absensi.kode.process') }}" method="POST">
            @csrf
            <div class="mb-3">
                <input type="text" name="kode_absensi" class="form-control form-control-lg" maxlength="8" required autofocus>
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg">Kirim Kehadiran</button>
                <a href="{{ route('anggota.dashboard') }}" class="btn btn-outline-light">Kembali ke Dashboard</a>
            </div>
        </form>
    </div>
</body>
</html>