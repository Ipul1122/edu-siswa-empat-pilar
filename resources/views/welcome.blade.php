@extends('layouts.app')

@section('title', 'Empat Pilar Kebangsaan - Media Belajar SMA/K')

@section('content')
    <!-- Navbar -->
    <nav class="landing-navbar">
        <div class="logo">
            <div class="logo-icon">
                <img src="{{ asset('img/mpr-logo.svg') }}" alt="Logo MPR" style="width: 100%; height: 100%; object-fit: contain;">
            </div>
            <div class="logo-text">EmpatPilar<span>SMA/K</span></div>
        </div>
        <ul class="landing-nav-links">
            <li><a href="#pillars" style="color: var(--color-dark); font-weight: 500;">Pilar Kebangsaan</a></li>
            <li><a href="#about" style="color: var(--color-dark); font-weight: 500;">Tentang Program</a></li>
        </ul>
        <div class="landing-auth-buttons" style="display: flex; align-items: center; gap: 10px;">
            @if(Auth::guard('web')->check() || Auth::guard('admin')->check())
                @if(Auth::guard('admin')->check())
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 8px;">
                        <img src="{{ Auth::guard('admin')->user()->image_url }}" alt="Admin" style="width: 22px; height: 22px; border-radius: 50%; object-fit: cover; border: 1.5px solid #fff; background: #fff;">
                        <span>Panel Admin</span>
                    </a>
                @endif
                @if(Auth::guard('web')->check())
                    <a href="{{ route('siswa.dashboard') }}" class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 8px;">
                        <img src="{{ Auth::user()->image_url }}" alt="{{ Auth::user()->name }}" style="width: 22px; height: 22px; border-radius: 50%; object-fit: cover; border: 1.5px solid #fff; background: #fff;">
                        <span>Dashboard Siswa</span>
                    </a>
                @endif
                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    @if(Auth::guard('admin')->check())
                        <input type="hidden" name="guard" value="admin">
                    @endif
                    <button type="submit" class="btn btn-secondary btn-sm" title="Keluar" style="padding: 6px 10px; display: inline-flex; align-items: center;">
                        <i class="fi fi-rr-sign-out-alt"></i>
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-secondary btn-sm">Masuk</a>
                <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Daftar</a>
            @endif
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-section">
        <div class="hero-content">
            <h1><span>Seleksi Online Lomba Cerdas Cermat MPR RI</span></h1>
            <p>
                Platform Seleksi Online interaktif mengenai kewarganegaraan, konstitusi, dan harmoni keberagaman Indonesia. Dirancang khusus untuk siswa/siswi tingkat SMA, SMK, dan MA demi memupuk jiwa nasionalisme yang unggul.
            </p>
            <div class="hero-buttons">
                @if(Auth::guard('web')->check() || Auth::guard('admin')->check())
                    @if(Auth::guard('web')->check())
                        <a href="{{ route('siswa.real-materi.index') }}" class="btn btn-primary">Mulai Seleksi</a>
                        <a href="{{ route('siswa.dashboard') }}" class="btn btn-secondary">Dashboard Siswa</a>
                    @endif
                    @if(Auth::guard('admin')->check())
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">Masuk ke Panel Admin</a>
                    @endif
                @else
                    <a href="{{ route('register') }}" class="btn btn-primary">Mulai Seleksi</a>
                    <a href="{{ route('login') }}" class="btn btn-secondary">Daftar Akun</a>
                @endif
            </div>
        </div>
        
        <div class="hero-illustration">
            <!-- Rotating Pillars Illustration -->
            <div class="pillars-circle">
                <div class="center-logo" style="display: flex; align-items: center; justify-content: center;">
                    <img src="{{ asset('img/mpr-logo.svg') }}" alt="Logo MPR" style="width: 100%; height: 100%; object-fit: contain;">
                </div>
                <div class="pillar-node node-1" style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 4px;">
                    <i class="fi fi-rr-shield" style="font-size: 1.25rem; color: #ff5252; line-height: 1;"></i>
                    <span style="font-size: 0.65rem; font-weight: 700;">PANCASILA</span>
                </div>
                <div class="pillar-node node-2" style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 4px;">
                    <i class="fi fi-rr-scroll" style="font-size: 1.25rem; color: #ffd740; line-height: 1;"></i>
                    <span style="font-size: 0.65rem; font-weight: 700;">UUD 1945</span>
                </div>
                <div class="pillar-node node-3" style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 4px;">
                    <i class="fi fi-rr-map" style="font-size: 1.25rem; color: #40c4ff; line-height: 1;"></i>
                    <span style="font-size: 0.65rem; font-weight: 700;">NKRI</span>
                </div>
                <div class="pillar-node node-4" style="display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 4px;">
                    <i class="fi fi-rr-handshake" style="font-size: 1.25rem; color: #69f0ae; line-height: 1;"></i>
                    <span style="font-size: 0.55rem; font-weight: 700; text-align: center; line-height: 1;">BHINNEKA</span>
                </div>
            </div>
    </header>

    <!-- Statistics Section -->
    <section class="stats-section">
        <div class="stats-container">
            <!-- Siswa Terdaftar -->
            <div class="stat-box siswa-box">
                <div class="stat-icon-wrapper">
                    <i class="fi fi-rr-users-alt"></i>
                </div>
                <div class="stat-numbers">{{ number_format($siswaCount) }}</div>
                <div class="stat-desc">Siswa Terdaftar</div>
            </div>

            <!-- Total Paket Seleksi -->
            <div class="stat-box seleksi-box">
                <div class="stat-icon-wrapper">
                    <i class="fi fi-rr-diploma"></i>
                </div>
                <div class="stat-numbers">{{ number_format($seleksiCount ?? 0) }}</div>
                <div class="stat-desc">Paket Seleksi</div>
            </div>

            <!-- Total Soal -->
            <div class="stat-box soal-box">
                <div class="stat-icon-wrapper">
                    <i class="fi fi-rr-question"></i>
                </div>
                <div class="stat-numbers">{{ number_format($soalCount) }}</div>
                <div class="stat-desc">Butir Soal</div>
            </div>
        </div>
    </section>

    <!-- Pillars Info Section -->
    <section class="pillars-section" id="pillars">
        <div class="section-title">
            <h2>Mengenal Empat Pilar Kebangsaan</h2>
            <p style="max-width: 600px; margin: 8px auto 0 auto; color: var(--color-gray-600);">
                Tiang penyangga kokoh bagi keutuhan, kerukunan, dan kemajuan Negara Kesatuan Republik Indonesia.
            </p>
        </div>

        <div class="pillars-grid">
            <!-- Pancasila -->
            <div class="pillar-card pancasila">
                <div class="card-pillar-icon" style="color: #ff5252; display: flex; align-items: center; justify-content: center;">
                    <i class="fi fi-rr-shield" style="font-size: 2.5rem; line-height: 1;"></i>
                </div>
                <h3>Pancasila</h3>
                <p>
                    Sebagai dasar negara, pandangan hidup bangsa, dan ideologi nasional Indonesia. Mengatur tata kehidupan berbangsa berlandaskan ketuhanan, kemanusiaan, persatuan, kerakyatan, dan keadilan sosial.
                </p>
            </div>

            <!-- UUD 1945 -->
            <div class="pillar-card uud">
                <div class="card-pillar-icon" style="color: #ffd740; display: flex; align-items: center; justify-content: center;">
                    <i class="fi fi-rr-scroll" style="font-size: 2.5rem; line-height: 1;"></i>
                </div>
                <h3>UUD NRI 1945</h3>
                <p>
                    Sebagai hukum dasar tertulis tertinggi yang menjadi landasan konstitusional tata pemerintahan dan jaminan hak asasi setiap warga negara Indonesia.
                </p>
            </div>

            <!-- NKRI -->
            <div class="pillar-card nkri">
                <div class="card-pillar-icon" style="color: #40c4ff; display: flex; align-items: center; justify-content: center;">
                    <i class="fi fi-rr-map" style="font-size: 2.5rem; line-height: 1;"></i>
                </div>
                <h3>NKRI</h3>
                <p>
                    Sebagai bentuk negara kesatuan republik yang berdaulat, menyatukan ribuan pulau dan perairan teritorial yang membentang dari Sabang hingga Merauke.
                </p>
            </div>

            <!-- Bhinneka Tunggal Ika -->
            <div class="pillar-card bhinneka">
                <div class="card-pillar-icon" style="color: #69f0ae; display: flex; align-items: center; justify-content: center;">
                    <i class="fi fi-rr-handshake" style="font-size: 2.5rem; line-height: 1;"></i>
                </div>
                <h3>Bhinneka Tunggal Ika</h3>
                <p>
                    Sebagai semboyan pemersatu bangsa Indonesia yang menekankan bahwa di tengah keberagaman ras, suku, agama, dan budaya, kita tetap merupakan satu kesatuan utuh.
                </p>
            </div>
    </section>

    <!-- Steps Section -->
    <section class="steps-section" id="steps">
        <div class="section-title">
            <h2>Alur Mengikuti Seleksi Online</h2>
            <p style="max-width: 600px; margin: 8px auto 0 auto; color: var(--color-gray-600);">
                Ikuti 4 langkah mudah berikut ini untuk mengikuti seleksi online Empat Pilar Kebangsaan MPR RI.
            </p>
        </div>

        <div class="steps-grid">
            <!-- Step 1 -->
            <div class="step-card">
                <div class="step-badge">01</div>
                <div class="step-icon-wrapper">
                    <i class="fi fi-rr-user-add"></i>
                </div>
                <h3>1. Registrasi Akun</h3>
                <p>
                    Tekan tombol <strong>Daftar</strong> di pojok kanan atas, lalu lengkapi biodata dirimu seperti nama lengkap, sekolah, kelas, provinsi, kabupaten/kota, email, dan kata sandi.
                </p>
            </div>

            <!-- Step 2 -->
            <div class="step-card">
                <div class="step-badge">02</div>
                <div class="step-icon-wrapper">
                    <i class="fi fi-rr-shield-check"></i>
                </div>
                <h3>2. Verifikasi Akun</h3>
                <p>
                    Sistem akan mengirimkan kode OTP unik ke email yang kamu daftarkan. Masukkan kode tersebut pada halaman verifikasi untuk mengaktifkan akunmu.
                </p>
            </div>

            <!-- Step 3 -->
            <div class="step-card">
                <div class="step-badge">03</div>
                <div class="step-icon-wrapper">
                    <i class="fi fi-rr-video-camera-alt"></i>
                </div>
                <h3>3. Ruang Zoom Pengawas</h3>
                <p>
                    Masuk ke sesi Zoom pengawasan resmi pada jadwal yang telah ditentukan bersama pengawas seleksi nasional.
                </p>
            </div>

            <!-- Step 4 -->
            <div class="step-card">
                <div class="step-badge">04</div>
                <div class="step-icon-wrapper">
                    <i class="fi fi-rr-document-signed"></i>
                </div>
                <h3>4. Mulai Ujian Seleksi</h3>
                <p>
                    Akses menu Mulai Seleksi dan kerjakan paket soal ujian secara jujur dan tertib. Nilai akan langsung tercatat di papan peringkat.
                </p>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="pillars-section" id="about" style="background-color: var(--color-gray-100);">
        <div class="section-title" style="margin-bottom: 32px;">
            <h2>Tentang Platform Seleksi Online</h2>
        </div>
        <div style="max-width: 800px; margin: 0 auto; text-align: left; line-height: 1.8; color: var(--color-gray-600); display: flex; flex-direction: column; gap: 16px;">
            <p>
                Platform ini dikembangkan khusus sebagai sistem evaluasi dan seleksi online <strong>Lomba Cerdas Cermat Empat Pilar Kebangsaan MPR RI</strong> untuk jenjang Sekolah Menengah Atas, Kejuruan, dan Madrasah Aliyah (SMA/SMK/MA) di seluruh Indonesia.
            </p>
            <p>
                Didukung oleh sistem pengawasan virtual Zoom terpadu, keamanan ujian anti-curang, sistem pengacakan butir soal, dan pemantauan papan peringkat berjenjang (Nasional, Provinsi, dan Kabupaten/Kota) secara transparan dan akuntabel.
            </p>
        </div>
    </section>

    <footer style="background-color: var(--color-dark); color: var(--color-gray-400); padding: 40px 8%; text-align: center; font-size: 0.9rem; border-top: 4px solid var(--color-primary);">
        <p>&copy; {{ date('Y') }} Program Empat Pilar Kebangsaan SMA/K. Hak Cipta Dilindungi.</p>
        <p style="margin-top: 8px; font-size: 0.8rem; color: var(--color-gray-600);">Mata Pelajaran Pendidikan Pancasila dan Kewarganegaraan (PPKN)</p>
    </footer>
@endsection
