<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Ruang Siswa - Empat Pilar')</title>
    
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
@php
    $isFullpageMode = Route::is('siswa.real-materi.start');
@endphp
<body class="{{ $isFullpageMode ? 'fullpage-mode has-fullpage-support' : '' }}" data-fullpage-enabled="{{ $isFullpageMode ? 'true' : 'false' }}">
    @if($isFullpageMode)
        <!-- Full Page Overlay Controls -->
        <div id="fullpage-controls" class="fullpage-controls">
            <!-- Mobile Pull-down Hint Pill -->
            <div class="fullpage-mobile-pill" id="fullpage-mobile-pill">
                <span class="pull-bar"></span>
                <span class="pill-text">Swipe ke bawah atau klik ✕ untuk keluar layar penuh</span>
            </div>

            <!-- Floating Exit "X" Button -->
            <button type="button" class="fullpage-exit-btn" id="fullpage-exit-btn" title="Keluar Layar Penuh (Esc)">
                <span class="btn-text">Keluar Layar Penuh</span>
                <span class="btn-key-badge">Esc</span>
                <i class="fi fi-rr-cross"></i>
            </button>

            <!-- Floating Re-enter Button (Visible when exited fullpage) -->
            <button type="button" class="fullpage-reenter-btn" id="fullpage-reenter-btn" title="Kembali ke Mode Layar Penuh">
                <i class="fi fi-rr-expand"></i>
                <span>Layar Penuh</span>
            </button>
        </div>
    @endif
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
                </div>                <div class="sidebar-logo-text">
                    Empat Pilar <span>Portal Seleksi</span>
                </div>
            </div>
            
            <ul class="sidebar-menu">
                <li class="sidebar-menu-item {{ Route::is('siswa.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('siswa.dashboard') }}" data-title="Dashboard">
                        <i class="fi fi-rr-home"></i> <span>Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ Route::is('siswa.tutorial') ? 'active' : '' }}">
                    <a href="{{ route('siswa.tutorial') }}" data-title="Tutorial & Panduan">
                        <i class="fi fi-rr-book-alt"></i> <span>Tutorial & Panduan</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ Route::is('siswa.real-materi.*') ? 'active' : '' }}">
                    <a href="{{ route('siswa.real-materi.index') }}" data-title="Ujian Seleksi Online">
                        <i class="fi fi-rr-document-signed"></i> <span>Ujian Seleksi CBT</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ Route::is('siswa.zoom-sessions.*') ? 'active' : '' }}">
                    <a href="{{ route('siswa.zoom-sessions.index') }}" data-title="Sesi Zoom Pengawas">
                        <i class="fi fi-rr-video-camera-alt"></i> <span>Sesi Zoom (Maks 500)</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ Route::is('siswa.my-results') ? 'active' : '' }}">
                    <a href="{{ route('siswa.my-results') }}" data-title="Hasil Skor Tim">
                        <i class="fi fi-rr-diploma"></i> <span>Hasil Skor Tim</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ Route::is('siswa.profile.*') ? 'active' : '' }}">
                    <a href="{{ route('siswa.profile.edit') }}" data-title="Akun Sekolah & PIC">
                        <i class="fi fi-rr-user"></i> <span>Akun Sekolah</span>
                    </a>
                </li>
            </ul>
            
            <div class="sidebar-footer">
                <div class="user-brief-info" title="{{ Auth::user()->school_name ?? Auth::user()->name }} ({{ Auth::user()->province->name ?? 'Tim 10 Siswa' }})">
                    <img src="{{ Auth::user()->image_url }}" alt="{{ Auth::user()->name }}" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(255,255,255,0.2); flex-shrink: 0; background: #fff;">
                    <div class="user-brief-text" style="min-width: 0; flex: 1;">
                        <p style="font-weight: 600; color: var(--color-white); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; margin-bottom: 2px;">{{ Auth::user()->school_name ?? Auth::user()->name }}</p>
                        <p style="font-size: 0.72rem; color: var(--color-secondary); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ Auth::user()->province->name ?? 'Tim 10 Siswa' }}</p>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST" id="logout-form">
                    @csrf
                    <button type="submit" class="logout-btn-link" title="Keluar">
                        <i class="fi fi-rr-sign-out-alt"></i> <span class="logout-label">Keluar</span>
                    </button>
                </form>
            </div>
        </aside>
        
        <!-- Main Content Area -->
        <main class="app-content">
            <!-- Top Navbar for Siswa -->
            <header class="app-topbar">
                <div class="topbar-left">
                    <button id="menu-toggle" class="menu-toggle" type="button" aria-label="Buka / Kecilkan Menu" title="Kecilkan / Perlebar Menu">
                        <i class="fi fi-rr-menu-burger" style="line-height: 1;"></i>
                    </button>
                    
                    <div style="font-size: 0.9rem; font-weight: 700; color: var(--color-dark); display: flex; align-items: center; gap: 8px;">
                        <span class="badge" style="background: rgba(var(--color-primary-rgb), 0.1); color: rgb(var(--color-primary-rgb)); padding: 4px 8px; border-radius: 6px; font-size: 0.75rem;">
                            🏫 Akun Resmi Sekolah
                        </span>
                        <span style="color: var(--color-gray-500); font-weight: 400; font-size: 0.8rem;">| Tim 10 Siswa (1 Perangkat)</span>
                    </div>
                </div>
                
                <div class="topbar-right">
                    <a href="{{ route('siswa.tutorial') }}" class="btn btn-secondary btn-sm" style="font-size: 0.78rem; padding: 6px 12px; display: inline-flex; align-items: center; gap: 6px;" title="Petunjuk Teknis">
                        <i class="fi fi-rr-interrogation"></i> Panduan
                    </a>

                    <a href="{{ route('siswa.profile.edit') }}" class="topbar-user-dropdown" title="Lihat Profil Sekolah">
                        <img src="{{ Auth::user()->image_url }}" alt="{{ Auth::user()->name }}" class="topbar-avatar">
                        <div class="topbar-user-info">
                            <span class="topbar-user-name">{{ Auth::user()->school_name ?? Auth::user()->name }}</span>
                            <span class="topbar-user-role">{{ Auth::user()->province->name ?? 'Tim 10 Siswa' }}</span>
                        </div>
                    </a>
                    
                    <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                        @csrf
                        <button type="submit" class="topbar-logout-btn" title="Keluar">
                            <i class="fi fi-rr-sign-out-alt"></i>
                            <span class="logout-text">Keluar</span>
                        </button>
                    </form>
                </div>
            </header>
            
            @yield('content')
        </main>
    </div>

    <!-- Mobile Bottom Navigation Bar (< 768px) -->
    <nav class="mobile-bottom-nav">
        <a href="{{ route('siswa.dashboard') }}" class="mobile-bottom-nav-item {{ Route::is('siswa.dashboard') ? 'active' : '' }}">
            <i class="fi fi-rr-home"></i>
            <span>Beranda</span>
        </a>
        <a href="{{ route('siswa.tutorial') }}" class="mobile-bottom-nav-item {{ Route::is('siswa.tutorial') ? 'active' : '' }}">
            <i class="fi fi-rr-book-alt"></i>
            <span>Tutorial</span>
        </a>
        <a href="{{ route('siswa.real-materi.index') }}" class="mobile-bottom-nav-item {{ Route::is('siswa.real-materi.*') ? 'active' : '' }}">
            <i class="fi fi-rr-document-signed"></i>
            <span>Seleksi CBT</span>
        </a>
        <a href="{{ route('siswa.zoom-sessions.index') }}" class="mobile-bottom-nav-item {{ Route::is('siswa.zoom-sessions.*') ? 'active' : '' }}">
            <i class="fi fi-rr-video-camera-alt"></i>
            <span>Zoom</span>
        </a>
        <a href="{{ route('siswa.my-results') }}" class="mobile-bottom-nav-item {{ Route::is('siswa.my-results') ? 'active' : '' }}">
            <i class="fi fi-rr-diploma"></i>
            <span>Hasil Skor</span>
        </a>
    </nav>

    <!-- Global Customer Service Chatbot Widget -->
    @include('partials.chatbot_widget')
    @stack('scripts')
</body>
</html>
