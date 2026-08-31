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
                    Empat Pilar <span>Panel Admin</span>
                </div>
            </div>
            
            <ul class="sidebar-menu">
                <li class="sidebar-menu-item {{ Route::is('admin.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="fi fi-rr-chart-pie"></i> Dashboard
                    </a>
                </li>
                <li class="sidebar-menu-item {{ Route::is('admin.materials.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.materials.index') }}">
                        <i class="fi fi-rr-book-alt"></i> Materi Belajar
                    </a>
                </li>
                <li class="sidebar-menu-item {{ Route::is('admin.videos.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.videos.index') }}">
                        <i class="fi fi-rr-play-alt"></i> Materi Video
                    </a>
                </li>
                <li class="sidebar-menu-item {{ Route::is('admin.quizzes.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.quizzes.index') }}">
                        <i class="fi fi-rr-clipboard-list"></i> Latihan Kuis
                    </a>
                </li>
                <li class="sidebar-menu-item {{ Route::is('admin.real-materi.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.real-materi.index') }}">
                        <i class="fi fi-rr-diploma"></i> Real Materi
                    </a>
                </li>
                <li class="sidebar-menu-item {{ Route::is('admin.students.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.students.index') }}">
                        <i class="fi fi-rr-users-alt"></i> Pemantauan Siswa
                    </a>
                </li>
                <li class="sidebar-menu-item {{ Route::is('admin.profile.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.profile.edit') }}">
                        <i class="fi fi-rr-user"></i> Pengaturan Akun
                    </a>
                </li>
            </ul>
            
            <div class="sidebar-footer">
                @if(Auth::guard('admin')->check())
                    <div style="display: flex; align-items: center; gap: 10px; padding: 10px 0; border-bottom: 1px solid rgba(255,255,255,0.08); margin-bottom: 12px; font-size: 0.85rem; color: var(--color-gray-400);">
                        <img src="{{ Auth::guard('admin')->user()->image_url }}" alt="{{ Auth::guard('admin')->user()->name }}" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(255,255,255,0.2); flex-shrink: 0; background: #fff;">
                        <div style="min-width: 0; flex: 1;">
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
                    <button type="submit" class="logout-btn-link">
                        <i class="fi fi-rr-sign-out-alt"></i> Keluar (Logout)
                    </button>
                </form>
            </div>
        </aside>
        
        <!-- Main Content Area -->
        <main class="app-content">
            <!-- Mobile Navigation Toggle -->
            <div style="display: flex; align-items: center; justify-content: space-between; width: 100%;" class="mobile-only-header">
                <button id="menu-toggle" class="menu-toggle">☰ Menu</button>
                <h3 style="font-size: 1.1rem; font-family: var(--font-heading);">Admin Empat Pilar</h3>
            </div>
            
            @yield('content')
        </main>
    </div>
</body>
</html>
