<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Dashboard PJ') | LDK At-Tadris</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    @stack('head')
    @stack('styles')

    {{-- Menggunakan style yang sama dengan layout admin untuk konsistensi --}}
    <style>
      html, body { height: 100%; margin: 0; overflow: hidden; }
      body { display: flex; font-family: 'Segoe UI', sans-serif; }
      .sidebar { 
          height: 100vh; 
          position: fixed; 
          top: 0; 
          left: 0; 
          overflow-y: auto; 
          z-index: 1000; 
          background: #212529; 
          color: white; 
          width: 250px;
          transition: width 0.3s ease;
      }
      .content-wrapper { 
          margin-left: 250px; 
          flex-grow: 1; 
          height: 100vh; 
          display: flex; 
          flex-direction: column; 
          overflow: hidden; 
          transition: margin-left 0.3s ease;
      }
      .content { 
          flex-grow: 1; 
          padding: 2rem; 
          overflow-y: auto; 
          background-color: #f8f9fa; 
      }
      .sidebar a { 
          color: #adb5bd; 
          text-decoration: none; 
          display: flex; 
          align-items: center; 
          padding: 0.85rem 1.25rem;
          border-left: 3px solid transparent;
          transition: all 0.2s ease-in-out;
      }
      .sidebar a.active, .sidebar a:hover { 
          background: #343a40; 
          color: white; 
          border-left-color: #0d6efd;
      }
      .sidebar .nav-link i { 
          width: 24px; 
          text-align: center; 
          margin-right: 0.75rem; 
          font-size: 1.1rem;
      }
      .sidebar a.disabled {
          color: #6c757d;
          pointer-events: none;
          cursor: default;
      }
      .topbar { 
          height: 56px; 
          padding: 0.5rem 2rem; 
          background: #ffffff; 
          border-bottom: 1px solid #dee2e6; 
          display: flex; 
          align-items: center; 
          position: sticky;
          top: 0;
          z-index: 999;
      }
      .sidebar-brand {
          padding: 1rem;
          text-align: center;
          color: white;
          font-size: 1.25rem;
          font-weight: 600;
          display: block;
          text-decoration: none;
      }
    </style>
</head>
<body>
  <nav class="sidebar d-flex flex-column p-0" id="sidebar">
    {{-- PERBAIKAN: Menambahkan Logo dan Judul --}}
    <a href="{{ route('pj.dashboard') }}" class="sidebar-brand">
        <img src="{{ asset('images/LOGO AT TADRIZ.jpg') }}" alt="Logo LDK" style="width: 40px; height: 40px; border-radius: 50%;" class="d-inline-block align-middle me-2">
        <span class="align-middle">PJ Area</span>
    </a>
    <hr class="text-secondary mt-0">
    
    <ul class="nav nav-pills flex-column mb-auto px-3">
      {{-- PERBAIKAN: Memperbaiki semua link dan menambahkan class 'active' dinamis --}}
      <li class="nav-item mb-1">
        <a href="{{ route('pj.dashboard') }}" class="nav-link {{ request()->routeIs('pj.dashboard') ? 'active' : '' }}">
          <i class="bi bi-speedometer2"></i><span>Dashboard</span>
        </a>
      </li>
      <li class="nav-item mb-1">
        <a href="{{ route('pj.kegiatan.index') }}" class="nav-link {{ request()->routeIs('pj.kegiatan.*') || request()->routeIs('pj.absensi.*') ? 'active' : '' }}">
          <i class="bi bi-calendar-event-fill"></i><span>Manajemen Kegiatan</span>
        </a>
      </li>
      <li class="nav-item mb-1">
        <a href="{{ route('pj.anggota.index') }}" class="nav-link {{ request()->routeIs('pj.anggota.*') ? 'active' : '' }}">
          <i class="bi bi-people-fill"></i><span>Anggota Devisi</span>
        </a>
      </li>
       <li class="nav-item mb-1">
        <a href="{{ route('pj.pengumuman.index') }}" class="nav-link {{ request()->routeIs('pj.pengumuman.*') ? 'active' : '' }}">
          <i class="bi bi-megaphone-fill"></i><span>Pengumuman Devisi</span>
        </a>
      </li>
      <li class="nav-item mb-1">
        <a href="#" class="nav-link disabled" title="Fitur dalam pengembangan">
          <i class="bi bi-clipboard2-check-fill"></i><span>Laporan Absensi</span>
        </a>
      </li>
    </ul>
    <hr class="text-secondary">
    <div class="text-center small text-muted p-3">
        © {{ date('Y') }} LDK At-Tadris
    </div>
  </nav>

  <div class="content-wrapper">
    <header class="topbar">
        <h5 class="mb-0 fw-bold">@yield('title', 'Dashboard')</h5>
        <div class="ms-auto d-flex align-items-center">
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle me-1"></i> {{ Auth::user()->name }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <main class="content">
        @yield('content')
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  @stack('scripts')
</body>
</html>