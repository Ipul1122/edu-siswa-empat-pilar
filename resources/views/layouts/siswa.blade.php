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
    
    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Flaticon Uicons CDN -->
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.6.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="app-sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo-icon">
                    <img src="{{ asset('img/mpr-logo.svg') }}" alt="Logo MPR" style="width: 100%; height: 100%; object-fit: contain;">
                </div>
                <div class="sidebar-logo-text">
                    Empat Pilar <span>Ruang Siswa</span>
                </div>
            </div>
            
            <ul class="sidebar-menu">
                <li class="sidebar-menu-item {{ Route::is('siswa.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('siswa.dashboard') }}">
                        <i class="fi fi-rr-home"></i> Dashboard
                    </a>
                </li>
                <li class="sidebar-menu-item {{ Route::is('siswa.materials.*') ? 'active' : '' }}">
                    <a href="{{ route('siswa.materials.index') }}">
                        <i class="fi fi-rr-book-alt"></i> Materi Belajar
                    </a>
                </li>
                <li class="sidebar-menu-item {{ Route::is('siswa.videos.*') ? 'active' : '' }}">
                    <a href="{{ route('siswa.videos.index') }}">
                        <i class="fi fi-rr-play-alt"></i> Video Pembelajaran
                    </a>
                </li>
                <li class="sidebar-menu-item {{ Route::is('siswa.quizzes.*') ? 'active' : '' }}">
                    <a href="{{ route('siswa.quizzes.index') }}">
                        <i class="fi fi-rr-edit"></i> Latihan Kuis
                    </a>
                </li>
                <li class="sidebar-menu-item {{ Route::is('siswa.real-materi.*') ? 'active' : '' }}">
                    <a href="{{ route('siswa.real-materi.index') }}">
                        <i class="fi fi-rr-document-signed"></i> Real Materi
                    </a>
                </li>
                <li class="sidebar-menu-item {{ Route::is('siswa.leaderboard') ? 'active' : '' }}">
                    <a href="{{ route('siswa.leaderboard') }}">
                        <i class="fi fi-rr-trophy"></i> Papan Peringkat
                    </a>
                </li>
                <li class="sidebar-menu-item {{ Route::is('siswa.profile.*') ? 'active' : '' }}">
                    <a href="{{ route('siswa.profile.edit') }}">
                        <i class="fi fi-rr-user"></i> Edit Profil
                    </a>
                </li>
            </ul>
            
            <div class="sidebar-footer">
                <div class="user-brief-info" style="display: flex; align-items: center; gap: 10px; padding: 10px 0; border-bottom: 1px solid rgba(255,255,255,0.08); margin-bottom: 12px; font-size: 0.85rem; color: var(--color-gray-400);">
                    <img src="{{ Auth::user()->image_url }}" alt="{{ Auth::user()->name }}" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(255,255,255,0.2); flex-shrink: 0; background: #fff;">
                    <div style="min-width: 0; flex: 1;">
                        <p style="font-weight: 600; color: var(--color-white); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; margin-bottom: 2px;">{{ Auth::user()->name }}</p>
                        <p style="font-size: 0.72rem; color: var(--color-secondary); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ Auth::user()->dapil ?? Auth::user()->school_name }}</p>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST" id="logout-form">
                    @csrf
                    <button type="submit" class="logout-btn-link">
                        <i class="fi fi-rr-sign-out-alt"></i> Keluar
                    </button>
                </form>
            </div>
        </aside>
        
        <!-- Main Content Area -->
        <main class="app-content">
            <!-- Top Navbar for Siswa -->
            <header class="app-topbar">
                <div class="topbar-left">
                    <button id="menu-toggle" class="menu-toggle" type="button" aria-label="Buka Menu">
                        <i class="fi fi-rr-menu-burger" style="line-height: 1;"></i>
                    </button>
                    
                    <div class="topbar-search">
                        <i class="fi fi-rr-search search-icon"></i>
                        <input type="text" id="global-search-input" placeholder="Cari materi, kuis, atau topik pilar..." autocomplete="off">
                    </div>
                </div>
                
                <div class="topbar-right">
                    <a href="{{ route('siswa.profile.edit') }}" class="topbar-user-dropdown" title="Lihat & Edit Profil">
                        <img src="{{ Auth::user()->image_url }}" alt="{{ Auth::user()->name }}" class="topbar-avatar">
                        <div class="topbar-user-info">
                            <span class="topbar-user-name">{{ Auth::user()->name }}</span>
                            <span class="topbar-user-role">{{ Auth::user()->school_name ?? 'Siswa SMA/K' }}</span>
                        </div>
                    </a>
                    
                    <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                        @csrf
                        <button type="submit" class="topbar-logout-btn" title="Keluar dari Akun Siswa">
                            <i class="fi fi-rr-sign-out-alt"></i>
                            <span class="logout-text">Keluar</span>
                        </button>
                    </form>
                </div>
            </header>
            
            @yield('content')
        </main>
    </div>
</body>
</html>
