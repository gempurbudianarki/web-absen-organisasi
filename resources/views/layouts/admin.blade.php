<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'LEMS') | Dashboard</title>
    <link rel="icon" href="{{ asset('images/LOGO AT TADRIZ.jpg') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('head')
    @stack('styles')
    <style>
      :root {
          --sidebar-bg: #0C342C;
          --sidebar-bg-gradient: linear-gradient(180deg, #076653 0%, #0C342C 100%);
          --sidebar-hover: #076653;
          --accent-lime: #E3EF26;
          --text-primary: #ffffff;
          --text-secondary: #adb5bd;
      }
      html, body { height: 100%; margin: 0; overflow: hidden; }
      body { display: flex; font-family: 'Inter', sans-serif; }
      .sidebar {
          height: 100vh; position: fixed; top: 0; left: 0; overflow-y: auto; z-index: 1000;
          background: var(--sidebar-bg-gradient);
          color: var(--text-primary);
          transition: width 0.3s ease-in-out;
          width: 260px;
      }
      .content-wrapper { margin-left: 260px; transition: margin-left 0.3s ease-in-out; flex-grow: 1; height: 100vh; display: flex; flex-direction: column; overflow: hidden; }
      .content { flex-grow: 1; padding: 2rem; overflow-y: auto; background-color: #f4f7f6; }
      
      .sidebar .nav-link {
          color: var(--text-secondary);
          text-decoration: none;
          display: flex;
          align-items: center;
          padding: 12px 20px;
          margin: 4px 10px;
          border-radius: 8px;
          font-weight: 500;
          transition: all 0.3s ease;
      }
      .sidebar .nav-link:hover {
          background-color: var(--sidebar-hover);
          color: var(--text-primary);
          transform: translateX(5px);
      }
      .sidebar .nav-link.active {
          background-color: var(--accent-lime);
          color: var(--sidebar-bg);
          font-weight: 700;
          box-shadow: 0 4px 15px -5px var(--accent-lime);
      }
      .sidebar .nav-link i {
          width: 24px; text-align: center; margin-right: 12px; font-size: 1.1rem;
          transition: transform 0.3s ease;
      }
      .sidebar .nav-link:hover i {
          transform: scale(1.1);
      }
      .topbar { height: 56px; padding: 0.5rem 1rem; background: #ffffff; border-bottom: 1px solid #dee2e6; display: flex; align-items: center; }
      .system-logo { max-height: 70px; transition: all 0.3s ease; }
      .system-name { font-weight: 700; transition: opacity 0.3s ease; }

      /* == CSS UNTUK SIDEBAR COLLAPSED == */
      .sidebar.collapsed { width: 80px; }
      .sidebar.collapsed ~ .content-wrapper { margin-left: 80px; }
      .sidebar.collapsed .system-name { opacity: 0; }
      .sidebar.collapsed .nav-link span { display: none; }
      .sidebar.collapsed .nav-link { justify-content: center; }
      .sidebar.collapsed .nav-link i { margin-right: 0; }
    </style>
</head>
<body>

  <nav class="sidebar d-flex flex-column p-3" id="sidebar">
    <div class="text-center mb-4">
      <img src="{{ asset('images/LOGO AT TADRIZ.jpg') }}" alt="LDK Logo" class="system-logo mx-auto d-block rounded-circle mb-2">
      <h4 class="system-name text-white">LDK At-Tadris</h4>
    </div>

    <ul class="nav nav-pills flex-column mb-auto">
      <li class="nav-item">
        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
          <i class="bi bi-speedometer2"></i><span>Dashboard</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
          <i class="bi bi-people-fill"></i><span>Manajemen User</span>
        </a>
      </li>
      <li class="nav-item">
          <a href="{{ route('admin.devisi.index') }}" class="nav-link {{ request()->routeIs('admin.devisi.*') ? 'active' : '' }}">
              <i class="bi bi-diagram-3-fill"></i><span>Manajemen Devisi</span>
          </a>
      </li>
      <li class="nav-item">
          <a href="{{ route('admin.kegiatan.index') }}" class="nav-link {{ request()->routeIs('admin.kegiatan.*') ? 'active' : '' }}">
              <i class="bi bi-calendar-event-fill"></i><span>Manajemen Kegiatan</span>
          </a>
      </li>
      <li class="nav-item">
          <a href="{{ route('admin.absensi.index') }}" class="nav-link {{ request()->routeIs('admin.absensi.*') ? 'active' : '' }}">
              <i class="bi bi-clipboard2-check-fill"></i><span>Manajemen Absensi</span>
          </a>
      </li>
       <li class="nav-item">
          <a href="{{ route('admin.pengumuman.index') }}" class="nav-link {{ request()->routeIs('admin.pengumuman.*') ? 'active' : '' }}">
              <i class="bi bi-megaphone-fill"></i><span>Pengumuman</span>
          </a>
      </li>
    </ul>
  </nav>

  <div class="content-wrapper">
    <header class="topbar">
        {{-- TOMBOL TOGGLE DIKEMBALIKAN DI SINI --}}
        <button class="btn btn-light me-3" id="toggleSidebarBtn">
            <i class="bi bi-list fs-5"></i>
        </button>
        <h5 class="mb-0 fw-bold">@yield('title', 'Dashboard')</h5>
        <div class="ms-auto d-flex align-items-center">
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle me-1"></i> {{ Auth::user()->name }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('admin.profile.edit') }}">Profile</a></li>
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

    <footer class="text-center py-3 mt-auto bg-light" style="font-size: 0.85rem;">
      <div class="container">
        <span class="text-muted">© 2025 LDK At-Tadris Universitas Bina Bangsa Getsempena.</span>
      </div>
    </footer>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  
  {{-- JAVASCRIPT TOGGLE DIKEMBALIKAN DI SINI --}}
  <script>
    document.getElementById('toggleSidebarBtn').addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('collapsed');
    });
  </script>
  @stack('scripts')
</body>
</html>