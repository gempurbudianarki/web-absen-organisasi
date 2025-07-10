<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" href="{{ asset('images/LOGO AT TADRIZ.jpg') }}">
    <link rel="preconnect" href="https://fonts.bunny.net" />
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    {{-- ======================================================= --}}
    {{--           CSS BARU DENGAN MOTIF ISLAMI                --}}
    {{-- ======================================================= --}}
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
        label, .text-label, .form-label, .input-label, .text-sm { color: white !important; }
        input[type='text'], input[type='email'], input[type='password'] {
            color: white !important;
            background-color: rgba(255, 255, 255, 0.05) !important;
            border-color: rgba(255, 255, 255, 0.3) !important;
        }
        input[type='checkbox'] {
            border-radius: 4px;
            border-color: rgba(255, 255, 255, 0.3);
        }
        input::placeholder { color: rgba(255, 255, 255, 0.6) !important; }
        .text-red-600 { color: #f87171 !important; }
        .link-style { color: #E3EF26; }
        .link-style:hover { color: #cddb22; }
    </style>
</head>
<body class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 font-sans text-white antialiased">
     <div class="w-full sm:max-w-md mt-6 px-6 py-8 glass shadow-md overflow-hidden sm:rounded-lg">
        <div class="flex justify-center mb-4">
            <a href="/">
                <img src="{{ asset('images/LOGO AT TADRIZ.jpg') }}" alt="Logo" class="h-20 w-20 rounded-circle" />
            </a>
        </div>
        {{ $slot }}
    </div> 
</body>
</html>