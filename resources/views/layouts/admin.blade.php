<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin - Empat Pilar')</title>
    
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
                <div class="sidebar-logo-icon">EP</div>
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
                <li class="sidebar-menu-item {{ Route::is('admin.quizzes.*') || Route::is('admin.questions.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.quizzes.index') }}">
                        <i class="fi fi-rr-clipboard-list"></i> Kuis & Soal
                    </a>
                </li>
                <li class="sidebar-menu-item {{ Route::is('admin.students.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.students.index') }}">
                        <i class="fi fi-rr-users-alt"></i> Pemantauan Siswa
                    </a>
                </li>
            </ul>
            
            <div class="sidebar-footer">
                <form action="{{ route('logout') }}" method="POST" id="logout-form">
                    @csrf
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
