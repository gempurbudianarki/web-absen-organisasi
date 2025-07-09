<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pindai QR Absensi</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>

    <style>
        body { background-color: #0f172a; color: #f1f5f9; }
        .scanner-container { max-width: 500px; margin: 2rem auto; }
        #qr-reader { border: 2px solid #334155; border-radius: 0.5rem; }
    </style>
</head>
<body>
    <div class="container scanner-container">
        <div class="text-center mb-4">
            <h2 class="fw-bold">Pindai QR Absensi</h2>
            <p class="text-white-50">Arahkan kamera ke QR Code yang ditampilkan oleh panitia.</p>
        </div>

        <div id="qr-reader"></div>

        <div class="text-center mt-4">
            <a href="{{ route('anggota.dashboard') }}" class="btn btn-outline-light">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>

    <script>
        function onScanSuccess(decodedText, decodedResult) {
            // Hentikan pemindaian setelah berhasil
            html5QrcodeScanner.clear();

            // Tampilkan loading
            Swal.fire({
                title: 'Memproses Absensi...',
                text: 'Mohon tunggu sebentar.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Kirim data ke server
            fetch("{{ route('absensi.process') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: decodedText // Kirim data mentah dari QR Code
            })
            .then(res => res.json())
            .then(data => {
                Swal.fire({
                    icon: data.status, // 'success' atau 'error'
                    title: data.message,
                    showConfirmButton: false,
                    timer: 2500
                }).then(() => {
                    // Arahkan kembali ke dashboard setelah notifikasi hilang
                    window.location.href = "{{ route('anggota.dashboard') }}";
                });
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error', 'Terjadi kesalahan saat mengirim data!', 'error');
            });
        }

        let html5QrcodeScanner = new Html5QrcodeScanner("qr-reader", { fps: 10, qrbox: 250 });
        html5QrcodeScanner.render(onScanSuccess);
    </script>
</body>
</html>