<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LEMS | LDK At-Tadris</title>

    <link rel="icon" href="{{ asset('images/LOGO AT TADRIZ.jpg') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Instrument Sans', sans-serif;
            background-color: #0C342C;
            background-image: 
                radial-gradient(circle, transparent, rgba(0,0,0,0.7)),
                url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'%3E%3Cpath d='M15 5h70v10H15zM5 15h10v70H5zm80 0h10v70H85zM15 85h70v10H15z' fill='%23E3EF26' fill-opacity='0.05'/%3E%3C/svg%3E");
        }

        .glass {
            background: rgba(12, 52, 44, 0.5);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(227, 239, 38, 0.2);
            border-radius: 1.5rem;
        }

        .logo-animation {
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .btn-login {
            background-color: #E3EF26;
            color: #0C342C;
            font-weight: 600;
            transition: all 0.2s ease-in-out;
        }

        .btn-login:hover {
            background-color: #cddb22;
            transform: scale(1.05);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center text-white antialiased px-4"> {{-- Tambah padding horizontal dasar --}}
    
    {{-- PERUBAHAN RESPONSIVE DI SINI --}}
    <div class="w-full max-w-xl text-center px-4 sm:px-6 py-12 glass rounded-3xl shadow-lg">
        
        <div class="flex justify-center mb-6">
            {{-- Ukuran logo dikecilkan sedikit di mobile --}}
            <img src="{{ asset('images/LOGO AT TADRIZ.jpg') }}" alt="Logo LDK At-Tadris" class="h-20 sm:h-24 w-20 sm:w-24 logo-animation rounded-circle">
        </div>

        {{-- Ukuran font dibuat adaptif --}}
        <h1 class="text-2xl sm:text-3xl font-semibold mb-2">Sistem Informasi Manajemen</h1>
        <p class="text-base sm:text-lg mb-6 text-gray-300">Lembaga Dakwah Kampus At-Tadris</p>

        <div class="flex flex-col sm:flex-row justify-center gap-4 mt-6">
            <a href="{{ route('login') }}" class="btn-login px-5 py-2 rounded-full font-medium">
                Login
            </a>
        </div>
    </div>
</body>
</html>