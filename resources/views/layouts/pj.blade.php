<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Dashboard PJ') | LEMS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('styles')
    <style>
      html, body { height: 100%; margin: 0; overflow: hidden; }
      body { display: flex; font-family: sans-serif; }
      .sidebar { height: 100vh; position: fixed; top: 0; left: 0; overflow-y: auto; z-index: 1000; background: #212529; color: white; width: 250px; }
      .content-wrapper { margin-left: 250px; flex-grow: 1; height: 100vh; display: flex; flex-direction: column; overflow: hidden; }
      .content { flex-grow: 1; padding: 2rem; overflow-y: auto; background-color: #f8f9fa; }
      .sidebar a { color: #adb5bd; text-decoration: none; display: flex; align-items: center; padding: 0.75rem 1.25rem; }
      .sidebar a.active, .sidebar a:hover { background: #343a40; color: white; border-left: 3px solid #0d6efd; padding-left: calc(1.25rem - 3px); }
      .sidebar .nav-link i { width: 24px; text-align: center; margin-right: 0.5rem; }
      .topbar { height: 56px; padding: 0.5rem 1rem; background: #ffffff; border-bottom: 1px solid #dee2e6; display: flex; align-items: center; }
    </style>
</head>
<body>
  <nav class="sidebar d-flex flex-column p-3" id="sidebar">
    <div class="text-center mb-4">
      <h4 class="text-white mt-2">Dashboard PJ</h4>
      <hr class="text-secondary">
    </div>
    <ul class="nav nav-pills flex-column mb-auto">
      <li class="nav-item">
        <a href="{{ route('pj.kegiatan.index') }}" class="nav-link {{ request()->routeIs('pj.kegiatan.index') ? 'active' : '' }}">
          <i class="bi bi-speedometer2"></i><span>Dashboard</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ route('pj.kegiatan.index') }}" class="nav-link {{ request()->routeIs('pj.kegiatan.*') ? 'active' : '' }}">
          <i class="bi bi-calendar-event-fill"></i><span>Manajemen Kegiatan</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ route('pj.anggota.index') }}" class="nav-link {{ request()->routeIs('pj.anggota.*') ? 'active' : '' }}">
          <i class="bi bi-people-fill"></i><span>Anggota Devisi</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="#" class="nav-link">
          <i class="bi bi-clipboard2-check-fill"></i><span>Laporan Absensi</span>
        </a>
      </li>
    </ul>
    <hr>
    <div class="text-center small">
        © 2025 LDK At-Tadris
    </div>
  </nav>

  <div class="content-wrapper">
    <header class="topbar">
        <h5 class="mb-0">@yield('title', 'Dashboard')</h5>
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
                            <button type="submit" class="dropdown-item text-danger">Logout</button>
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