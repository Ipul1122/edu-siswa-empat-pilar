<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin - Empat Pilar')</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('img/mpr-logo.svg') }}">
    
    <!-- Meta tags for SweetAlert2 -->
    @if(session('success'))
        <meta name="flash-success" content="{{ session('success') }}">
    @endif
    @if(session('error'))
        <meta name="flash-error" content="{{ session('error') }}">
    @endif
    @if(session('warning'))
        <meta name="flash-warning" content="{{ session('warning') }}">
    @endif
    @if(session('info'))
        <meta name="flash-info" content="{{ session('info') }}">
    @endif
    @if(session('status'))
        <meta name="flash-status" content="{{ session('status') }}">
    @endif
    @if(session('swal_title'))
        <meta name="flash-swal-title" content="{{ session('swal_title') }}">
        <meta name="flash-swal-text" content="{{ session('swal_text') }}">
        <meta name="flash-swal-icon" content="{{ session('swal_icon', 'info') }}">
    @endif
    
    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Flaticon Uicons CDN -->
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.6.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Sidebar Minimized Anti-FOUC State Handler -->
    <script>
        (function() {
            try {
                if (localStorage.getItem('sidebar_minimized') === 'true' && window.innerWidth > 768) {
                    document.documentElement.classList.add('sidebar-minimized');
                }
            } catch (e) {}
        })();
    </script>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="app-sidebar">
            <!-- Collapse Toggle Button (Tengah Sidebar Edge) -->
            <button type="button" class="sidebar-collapse-btn" id="sidebar-collapse-btn" title="Kecilkan Sidebar (Ctrl+B)" aria-label="Kecilkan Sidebar">
                <i class="fi fi-rr-angle-small-left"></i>
            </button>

            <div class="sidebar-header">
                <div class="sidebar-logo-icon" title="Empat Pilar Kebangsaan">
                    <img src="{{ asset('img/mpr-logo.svg') }}" alt="Logo MPR" style="width: 100%; height: 100%; object-fit: contain;">
                </div>
                <div class="sidebar-logo-text">
                    Empat Pilar <span>Panel Admin</span>
                </div>
            </div>
            
            <ul class="sidebar-menu">
                <li class="sidebar-menu-item {{ Route::is('admin.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}" data-title="Dashboard">
                        <i class="fi fi-rr-chart-pie"></i> <span>Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ Route::is('admin.materials.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.materials.index') }}" data-title="Materi Belajar">
                        <i class="fi fi-rr-book-alt"></i> <span>Materi Belajar</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ Route::is('admin.videos.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.videos.index') }}" data-title="Materi Video">
                        <i class="fi fi-rr-play-alt"></i> <span>Materi Video</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ Route::is('admin.quizzes.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.quizzes.index') }}" data-title="Latihan Kuis">
                        <i class="fi fi-rr-clipboard-list"></i> <span>Latihan Kuis</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ Route::is('admin.real-materi.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.real-materi.index') }}" data-title="Real Materi">
                        <i class="fi fi-rr-diploma"></i> <span>Real Materi</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ Route::is('admin.zoom-sessions.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.zoom-sessions.index') }}" data-title="Sesi Zoom">
                        <i class="fi fi-rr-video-camera-alt"></i> <span>Sesi Zoom</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ Route::is('admin.students.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.students.index') }}" data-title="Pemantauan Siswa">
                        <i class="fi fi-rr-users-alt"></i> <span>Pemantauan Siswa</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ Route::is('admin.leaderboard*') ? 'active' : '' }}">
                    <a href="{{ route('admin.leaderboard') }}" data-title="Papan Peringkat">
                        <i class="fi fi-rr-trophy"></i> <span>Papan Peringkat</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ Route::is('admin.profile.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.profile.edit') }}" data-title="Pengaturan Akun">
                        <i class="fi fi-rr-user"></i> <span>Pengaturan Akun</span>
                    </a>
                </li>
            </ul>
            
            <div class="sidebar-footer">
                @if(Auth::guard('admin')->check())
                    <div class="user-brief-info" title="{{ Auth::guard('admin')->user()->name }} (Administrator)">
                        <img src="{{ Auth::guard('admin')->user()->image_url }}" alt="{{ Auth::guard('admin')->user()->name }}" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(255,255,255,0.2); flex-shrink: 0; background: #fff;">
                        <div class="user-brief-text" style="min-width: 0; flex: 1;">
                            <a href="{{ route('admin.profile.edit') }}" style="color: var(--color-white); font-weight: 600; text-decoration: none; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                {{ Auth::guard('admin')->user()->name }}
                            </a>
                            <span style="font-size: 0.72rem; color: var(--color-secondary);">Administrator</span>
                        </div>
                    </div>
                @endif
                <form action="{{ route('logout') }}" method="POST" id="logout-form">
                    @csrf
                    <input type="hidden" name="guard" value="admin">
                    <button type="submit" class="logout-btn-link" title="Keluar">
                        <i class="fi fi-rr-sign-out-alt"></i> <span class="logout-label">Keluar (Logout)</span>
                    </button>
                </form>
            </div>
        </aside>
        
        <!-- Main Content Area -->
        <main class="app-content">
            <!-- Top Navbar for Admin -->
            <header class="app-topbar">
                <div class="topbar-left">
                    <button id="menu-toggle" class="menu-toggle" type="button" aria-label="Buka / Kecilkan Menu" title="Kecilkan / Perlebar Menu">
                        <i class="fi fi-rr-menu-burger" style="line-height: 1;"></i>
                    </button>
                    
                    <div class="topbar-search">
                        <i class="fi fi-rr-search search-icon"></i>
                        <input type="text" id="admin-global-search" placeholder="Cari materi, kuis, soal, atau siswa..." autocomplete="off">
                    </div>
                </div>
                
                <div class="topbar-right">
                    <a href="{{ route('admin.profile.edit') }}" class="topbar-user-dropdown" title="Pengaturan Akun Admin">
                        <img src="{{ Auth::guard('admin')->user()?->image_url ?? asset('img/default-avatar.svg') }}" alt="{{ Auth::guard('admin')->user()?->name ?? 'Admin' }}" class="topbar-avatar">
                        <div class="topbar-user-info">
                            <span class="topbar-user-name">{{ Auth::guard('admin')->user()?->name ?? 'Administrator' }}</span>
                            <span class="topbar-user-role" style="color: rgb(var(--color-primary-rgb)); font-weight: 600;">Administrator</span>
                        </div>
                    </a>
                    
                    <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                        @csrf
                        <input type="hidden" name="guard" value="admin">
                        <button type="submit" class="topbar-logout-btn" title="Keluar dari Panel Admin">
                            <i class="fi fi-rr-sign-out-alt"></i>
                            <span class="logout-text">Keluar</span>
                        </button>
                    </form>
                </div>
            </header>
            
            @yield('content')
        </main>
    </div>
    @stack('scripts')
</body>
</html>
