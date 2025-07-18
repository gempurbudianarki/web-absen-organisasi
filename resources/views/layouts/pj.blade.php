<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Dashboard PJ') | LDK At-Tadris</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <link rel="stylesheet" href="{{ asset('css/pj_dashboard.css') }}">

    @stack('head')
    @stack('styles')

    <style>
      html, body { height: 100%; margin: 0; overflow: hidden; }
      body { display: flex; font-family: sans-serif; }
      .sidebar { height: 100vh; position: fixed; top: 0; left: 0; overflow-y: auto; z-index: 1000; background: #343a40; color: white; transition: width 0.3s ease; width: 250px; }
      .content-wrapper { margin-left: 250px; transition: margin-left 0.3s ease; flex-grow: 1; height: 100vh; display: flex; flex-direction: column; overflow: hidden; background-color: #f8f9fa; }
      .content { flex-grow: 1; padding: 2rem; overflow-y: auto; }
      .sidebar a { color: #adb5bd; text-decoration: none; }
      .sidebar a.active, .sidebar a:hover { background: #495057; color: white; }
      .topbar { height: 56px; padding: 0.5rem 1.5rem; background: #ffffff; border-bottom: 1px solid #dee2e6; display: flex; align-items: center; position: sticky; top: 0; z-index: 999; }
      .sidebar.collapsed { width: 60px; }
      .sidebar.collapsed ~ .content-wrapper { margin-left: 60px; }
      .menu-item { padding: 10px 12px; font-size: 1rem; display: flex; align-items: center; cursor: pointer; position: relative; border-radius: .25rem; }
      .menu-item i { width: 24px; text-align: center; font-size: 1.2rem; }
      .menu-item span { white-space: nowrap; transition: opacity 0.3s ease; margin-left: 10px; }
      .sidebar.collapsed .menu-item span { opacity: 0; width: 0; overflow: hidden; }
      .sidebar.collapsed .menu-item { justify-content: center; }
      .sidebar.collapsed .menu-item:hover::after { content: attr(data-tooltip); position: absolute; left: 100%; top: 50%; transform: translateY(-50%); background: #333; color: white; padding: 4px 8px; border-radius: 4px; white-space: nowrap; margin-left: 10px; z-index: 1001; }
      .system-logo { max-height: 50px; transition: all 0.3s ease; }
      .sidebar.collapsed .system-logo { max-height: 40px !important; }
      .system-name { transition: opacity 0.3s ease, font-size 0.3s ease; font-size: 1.2rem; font-weight: 600; }
      .sidebar.collapsed .system-name { opacity: 0; }
    </style>
</head>
<body>

  <nav class="sidebar d-flex flex-column p-3" id="sidebar">
    <div class="text-center mb-4">
      <img src="{{ asset('images/LOGO AT TADRIZ.jpg') }}" alt="LEMS Logo" class="system-logo mx-auto d-block rounded-circle">
      <h4 class="system-name text-white mt-2">PJ Area</h4>
    </div>

    <ul class="nav nav-pills flex-column mb-auto">
      <li class="nav-item mb-1">
        <a href="{{ route('pj.dashboard') }}" class="menu-item nav-link {{ request()->routeIs('pj.dashboard') ? 'active' : '' }}" data-tooltip="Dashboard">
          <i class="bi bi-speedometer2"></i><span>Dashboard</span>
        </a>
      </li>
      <li class="nav-item mb-1">
        <a href="{{ route('pj.kegiatan.index') }}" class="menu-item nav-link {{ (request()->routeIs('pj.kegiatan.*') || request()->routeIs('pj.absensi.*')) ? 'active' : '' }}" data-tooltip="Manajemen Kegiatan">
          <i class="bi bi-calendar-event-fill"></i><span>Manajemen Kegiatan</span>
        </a>
      </li>
      <li class="nav-item mb-1">
        <a href="{{ route('pj.anggota.index') }}" class="menu-item nav-link {{ request()->routeIs('pj.anggota.*') ? 'active' : '' }}" data-tooltip="Anggota Devisi">
          <i class="bi bi-people-fill"></i><span>Anggota Devisi</span>
        </a>
      </li>
       <li class="nav-item mb-1">
        <a href="{{ route('pj.pengumuman.index') }}" class="menu-item nav-link {{ request()->routeIs('pj.pengumuman.*') ? 'active' : '' }}" data-tooltip="Pengumuman Devisi">
          <i class="bi bi-megaphone-fill"></i><span>Pengumuman Devisi</span>
        </a>
      </li>
      <li class="nav-item mb-1">
        <a href="{{ route('pj.laporan.index') }}" class="menu-item nav-link {{ request()->routeIs('pj.laporan.*') ? 'active' : '' }}" data-tooltip="Laporan Absensi">
          <i class="bi bi-journal-text"></i><span>Laporan Absensi</span>
        </a>
      </li>
    </ul>
    <hr class="text-secondary">
     <div class="text-center small text-white-50 p-2">
        © {{ date('Y') }} LDK At-Tadris
    </div>
  </nav>

  <div class="content-wrapper">
    <header class="topbar">
        <button class="btn btn-light me-3" id="toggleSidebar" onclick="toggleSidebar()"><i class="bi bi-list"></i></button>
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
  {{-- PERBAIKAN DI SINI: Pindahkan Chart.js ke layout utama --}}
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    function toggleSidebar() { document.getElementById('sidebar').classList.toggle('collapsed'); }
  </script>
  @stack('scripts')
</body>
</html>