<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Ruang Siswa - Empat Pilar')</title>
    
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
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="app-sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo-icon"><i class="fi fi-rr-graduation-cap" style="color: var(--color-white); line-height: 1; font-size: 1.3rem;"></i></div>
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
                <li class="sidebar-menu-item {{ Route::is('siswa.quizzes.*') ? 'active' : '' }}">
                    <a href="{{ route('siswa.quizzes.index') }}">
                        <i class="fi fi-rr-edit"></i> Latihan Kuis
                    </a>
                </li>
                <li class="sidebar-menu-item {{ Route::is('siswa.profile.*') ? 'active' : '' }}">
                    <a href="{{ route('siswa.profile.edit') }}">
                        <i class="fi fi-rr-user"></i> Edit Profil
                    </a>
                </li>
            </ul>
            
            <div class="sidebar-footer">
                <div class="user-brief-info" style="padding: 10px 0; border-bottom: 1px solid rgba(255,255,255,0.08); margin-bottom: 12px; font-size: 0.85rem; color: var(--color-gray-400);">
                    <p style="font-weight: 600; color: var(--color-white); max-width: 100%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ Auth::user()->name }}</p>
                    <p>{{ Auth::user()->class_name }}</p>
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
            <!-- Mobile Navigation Toggle -->
            <div style="display: flex; align-items: center; justify-content: space-between; width: 100%;" class="mobile-only-header">
                <button id="menu-toggle" class="menu-toggle">☰ Menu</button>
                <h3 style="font-size: 1.1rem; font-family: var(--font-heading);">Belajar PPKN</h3>
            </div>
            
            @yield('content')
        </main>
    </div>
</body>
</html>
