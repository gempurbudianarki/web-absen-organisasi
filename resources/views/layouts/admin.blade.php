<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'LEMS') | Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @stack('head')
    @stack('styles')
 <style>
      html, body { height: 100%; margin: 0; overflow: hidden; }
      body { display: flex; font-family: sans-serif; }
      .sidebar { height: 100vh; position: fixed; top: 0; left: 0; overflow-y: auto; z-index: 1000; background: #343a40; color: white; transition: width 0.3s ease; }
      .content-wrapper { margin-left: 200px; transition: margin-left 0.3s ease; flex-grow: 1; height: 100vh; display: flex; flex-direction: column; overflow: hidden; }
      .content { flex-grow: 1; padding: 2rem; overflow-y: auto; }
      .sidebar a { color: #adb5bd; text-decoration: none; }
      .sidebar a.active, .sidebar a:hover { background: #495057; color: white; }
      .topbar { height: 56px; padding: 0.5rem 1rem; background: #f8f9fa; border-bottom: 1px solid #dee2e6; display: flex; align-items: center; }
      .sidebar.collapsed { width: 60px; }
      .sidebar.collapsed ~ .content-wrapper { margin-left: 60px; }
      .menu-item { padding: 8px 12px; font-size: 14px; display: flex; align-items: center; cursor: pointer; position: relative; }
      .menu-item i { width: 24px; text-align: center; }
      .menu-item span { white-space: nowrap; transition: opacity 0.3s ease; }
      .sidebar.collapsed .menu-item span { opacity: 0; width: 0; overflow: hidden; }
      .sidebar.collapsed .menu-item { justify-content: center; }
      .sidebar.collapsed .menu-item:hover::after { content: attr(data-tooltip); position: absolute; left: 100%; top: 50%; transform: translateY(-50%); background: #333; color: white; padding: 4px 8px; border-radius: 4px; white-space: nowrap; margin-left: 10px; z-index: 1001; }
      .system-logo { max-height: 80px; transition: all 0.3s ease; }
      .sidebar.collapsed .system-logo { max-height: 40px !important; }
      .system-name { transition: opacity 0.3s ease; }
      .sidebar.collapsed .system-name { opacity: 0; }
 </style>
</head>
<body>

  <nav class="sidebar d-flex flex-column p-3" id="sidebar">
    <div class="text-center mb-4">
      <img src="{{ asset('images/developer.png') }}" alt="LEMS Logo" class="system-logo mx-auto d-block">
      <h4 class="system-name text-white mt-2">LEMS</h4>
    </div>

    <ul class="nav nav-pills flex-column mb-auto">
      <li class="nav-item mb-1">
        <a href="{{ route('admin.dashboard') }}" class="menu-item nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" data-tooltip="Dashboard">
          <i class="bi bi-speedometer2 me-2"></i><span>Dashboard</span>
        </a>
      </li>
      
      <li class="nav-item mb-1">
          <a href="{{ route('admin.devisi.index') }}" class="menu-item nav-link {{ request()->routeIs('admin.devisi.*') ? 'active' : '' }}" data-tooltip="Manajemen Devisi">
              <i class="bi bi-diagram-3-fill me-2"></i><span>Manajemen Devisi</span>
          </a>
      </li>
      <li class="nav-item mb-1">
          <a href="{{ route('admin.learners.index') }}" class="menu-item nav-link {{ request()->routeIs('admin.learners.*') ? 'active' : '' }}" data-tooltip="Manage Learners">
              <i class="bi bi-people-fill me-2"></i><span>Learners</span>
          </a>
      </li>
      <li class="nav-item mb-1">
          <a href="#" class="menu-item nav-link" data-tooltip="Manage Employees">
              <i class="bi bi-person-badge-fill me-2"></i><span>Employees</span>
          </a>
      </li>
      <li class="nav-item mb-1">
          <a href="{{ route('admin.attendance.index') }}" class="menu-item nav-link {{ request()->routeIs('admin.attendance.*') ? 'active' : '' }}" data-tooltip="View Attendance Logs">
              <i class="bi bi-clipboard-check-fill me-2"></i><span>Attendance</span>
          </a>
      </li>
      <li class="nav-item mb-1">
          <a class="menu-item nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#announcementSubmenu" role="button" data-tooltip="Announcements">
              <span><i class="bi bi-megaphone-fill me-2"></i>Announce</span>
              <i class="bi bi-chevron-down small"></i>
          </a>
          <div class="collapse {{ request()->routeIs('admin.announcements.*') ? 'show' : '' }}" id="announcementSubmenu">
              <ul class="nav flex-column ms-4">
                  <li><a href="{{ route('admin.announcements.index') }}" class="nav-link text-white-50">Compose</a></li>
                  <li><a href="{{ route('admin.announcements.sendForm') }}" class="nav-link text-white-50">Send</a></li>
                  <li><a href="{{ route('admin.announcements.logs') }}" class="nav-link text-white-50">Logs</a></li>
              </ul>
          </div>
      </li>
      <li class="nav-item mb-1">
        <a href="{{ route('users.index') }}" class="menu-item nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" data-tooltip="Registered Users">
          <i class="bi bi-people-fill me-2"></i><span>Registered Users</span>
        </a>
      </li>
      <li class="nav-item mb-1">
        <a href="{{ route('email.logs') }}" class="menu-item nav-link {{ request()->routeIs('email.logs') ? 'active' : '' }}" data-tooltip="Email Audit Log">
          <i class="bi bi-clipboard-check-fill me-2"></i><span>Email Audit Log</span>
        </a>
      </li>
      <li class="nav-item mb-1">
          <a href="{{ route('email.custom.form') }}" class="menu-item nav-link {{ request()->routeIs('email.custom.form') ? 'active' : '' }}" data-tooltip="Custom Email">
              <i class="bi bi-chat-square-text-fill me-2"></i><span>Custom Email</span>
          </a>
      </li>
    </ul>
  </nav>

  <div class="content-wrapper">
    <header class="topbar">
        <button class="btn btn-light me-3" id="toggleSidebar" onclick="toggleSidebar()"><i class="bi bi-list"></i></button>
        <h5 class="mb-0">@yield('title', 'Dashboard')</h5>
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
  <script>
    function toggleSidebar() { document.getElementById('sidebar').classList.toggle('collapsed'); }
  </script>
  @stack('scripts')
</body>
</html>