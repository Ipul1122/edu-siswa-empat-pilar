@extends('layouts.app')

@section('title', 'Daftar Akun Siswa - Empat Pilar')

@section('content')
<!-- Tom Select CDN (Searchable Dropdown) -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">

<style>
    .split-container {
        display: flex;
        min-height: 100vh;
        width: 100vw;
        background-color: var(--color-white);
        overflow: hidden;
    }
    
    .brand-panel {
        flex: 1.1;
        background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%);
        color: var(--color-white);
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        padding: 90px 60px 50px 60px;
        position: relative;
        overflow: hidden;
    }
    
    /* Glowing decorative background blobs */
    .brand-panel::before {
        content: '';
        position: absolute;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(255, 193, 7, 0.15) 0%, rgba(255, 193, 7, 0) 70%);
        top: -100px;
        right: -100px;
        border-radius: 50%;
        filter: blur(50px);
        pointer-events: none;
    }

    .brand-panel::after {
        content: '';
        position: absolute;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(var(--color-primary-rgb), 0.25) 0%, rgba(var(--color-primary-rgb), 0) 70%);
        bottom: -150px;
        left: -150px;
        border-radius: 50%;
        filter: blur(60px);
        pointer-events: none;
    }
    
    .brand-content {
        position: relative;
        z-index: 2;
        max-width: 600px;
    }
    
    .brand-logo {
        position: absolute;
        top: -65px;
        left: 0;
        width: 56px;
        height: 56px;
        filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2));
        animation: float 4s ease-in-out infinite;
    }
    .brand-logo img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
    
    .brand-title {
        font-size: 2.3rem;
        font-weight: 800;
        line-height: 1.2;
        margin-bottom: 12px;
        letter-spacing: -0.5px;
    }
    
    .brand-title span {
        color: var(--color-secondary);
    }
    
    .brand-subtitle {
        font-size: 0.92rem;
        color: rgba(255, 255, 255, 0.85);
        margin-bottom: 24px;
        font-weight: 400;
        line-height: 1.5;
    }
    
    .pillars-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }
    
    .pillar-card {
        background: rgba(255, 255, 255, 0.06);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: var(--border-radius-md);
        padding: 12px;
        transition: var(--transition-smooth);
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .pillar-card:hover {
        transform: translateY(-3px);
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 193, 7, 0.3);
        box-shadow: 0 10px 20px rgba(0,0,0,0.15);
    }
    
    .pillar-icon {
        font-size: 1.3rem;
        background: rgba(255, 255, 255, 0.1);
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: var(--border-radius-sm);
        flex-shrink: 0;
    }
    
    .pillar-info h4 {
        color: var(--color-white);
        font-size: 0.85rem;
        margin-bottom: 2px;
        font-weight: 600;
    }
    
    .pillar-info p {
        font-size: 0.7rem;
        color: rgba(255, 255, 255, 0.6);
        font-weight: 400;
    }
    
    .form-panel {
        flex: 0.9;
        display: flex;
        align-items: flex-start;
        justify-content: center;
        padding: 35px 40px;
        background-color: var(--color-white);
        position: relative;
        overflow-y: auto;
        max-height: 100vh;
    }
    
    .form-container {
        width: 100%;
        max-width: 500px;
        animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    }
    
    .form-header {
        margin-bottom: 16px;
    }
    
    .form-header h3 {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--color-dark);
        margin-bottom: 4px;
    }
    
    .form-header p {
        font-size: 0.88rem;
        color: var(--color-gray-600);
    }
    
    .custom-form-group {
        margin-bottom: 14px;
        position: relative;
    }
    
    .custom-form-group label {
        display: block;
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--color-dark);
        margin-bottom: 5px;
    }

    .required-star {
        color: var(--color-danger);
        font-weight: 700;
        margin-left: 2px;
    }
    
    .input-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }
    
    .custom-input {
        width: 100%;
        height: 42px;
        padding: 8px 12px;
        font-size: 0.88rem;
        font-family: var(--font-body);
        color: var(--color-dark);
        background-color: var(--color-gray-100);
        border: 2px solid transparent;
        border-radius: var(--border-radius-md);
        transition: var(--transition-smooth);
        outline: none;
        box-sizing: border-box;
    }
    
    .custom-input:focus {
        background-color: var(--color-white);
        border-color: rgb(var(--color-primary-rgb));
        box-shadow: 0 0 0 3px rgba(var(--color-primary-rgb), 0.1);
    }
    
    .custom-input.is-invalid {
        border-color: var(--color-danger);
        background-color: rgba(244, 67, 54, 0.02);
    }

    /* Modern Camera Avatar Trigger */
    .register-avatar-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
        text-align: center;
    }
    .register-avatar-wrapper {
        position: relative;
        width: 88px;
        height: 88px;
        cursor: pointer;
        margin-bottom: 6px;
    }
    .register-avatar-preview {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid rgb(var(--color-primary-rgb));
        box-shadow: var(--shadow-sm);
        transition: var(--transition-smooth);
        background: #f1f5f9;
    }
    .register-avatar-wrapper:hover .register-avatar-preview {
        filter: brightness(0.9);
    }
    .register-camera-badge {
        position: absolute;
        bottom: 0;
        right: 0;
        background-color: rgb(var(--color-primary-rgb));
        color: var(--color-white);
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.25);
        border: 2px solid var(--color-white);
        transition: transform 0.2s ease, background-color 0.2s ease;
    }
    .register-avatar-wrapper:hover .register-camera-badge {
        transform: scale(1.1);
        background-color: var(--color-primary-hover);
    }

    /* Tom Select Opaque & Robust Styling Fix */
    .ts-wrapper {
        width: 100% !important;
        position: relative !important;
    }
    .ts-control {
        border-radius: var(--border-radius-md) !important;
        border: 2px solid transparent !important;
        background-color: var(--color-gray-100) !important;
        background: var(--color-gray-100) !important;
        padding: 8px 14px !important;
        font-size: 0.88rem !important;
        font-family: var(--font-body) !important;
        min-height: 42px !important;
        display: flex !important;
        align-items: center !important;
        transition: var(--transition-smooth) !important;
        box-shadow: none !important;
    }
    .ts-control:focus, .ts-wrapper.focus .ts-control {
        background-color: #ffffff !important;
        background: #ffffff !important;
        border-color: rgb(var(--color-primary-rgb)) !important;
        box-shadow: 0 0 0 3px rgba(var(--color-primary-rgb), 0.1) !important;
    }
    .ts-dropdown {
        position: absolute !important;
        top: 100% !important;
        left: 0 !important;
        right: 0 !important;
        width: 100% !important;
        background-color: #ffffff !important;
        background: #ffffff !important;
        border: 1px solid var(--color-gray-300) !important;
        border-radius: var(--border-radius-md) !important;
        box-shadow: 0 14px 28px rgba(0, 0, 0, 0.18), 0 10px 10px rgba(0, 0, 0, 0.12) !important;
        font-size: 0.85rem !important;
        max-height: 240px !important;
        overflow-y: auto !important;
        z-index: 999999 !important;
        padding: 4px 0 !important;
        margin-top: 4px !important;
    }
    .ts-dropdown .option {
        background-color: #ffffff !important;
        background: #ffffff !important;
        padding: 9px 14px !important;
        border-bottom: 1px solid #f1f5f9 !important;
        color: #1e293b !important;
        cursor: pointer !important;
    }
    .ts-dropdown .option:last-child {
        border-bottom: none !important;
    }
    .ts-dropdown .option:hover, .ts-dropdown .option.active {
        background-color: #f1f5f9 !important;
        background: #f1f5f9 !important;
        color: #0f172a !important;
    }
    .ts-dropdown .option.selected {
        background-color: rgba(var(--color-primary-rgb), 0.08) !important;
        background: rgba(var(--color-primary-rgb), 0.08) !important;
        color: rgb(var(--color-primary-rgb)) !important;
        font-weight: 600 !important;
    }
    .dapil-select-option {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .dapil-main-title {
        font-weight: 700;
        color: #0f172a;
        font-size: 0.88rem;
    }
    .dapil-subtext {
        font-size: 0.74rem;
        color: #64748b;
        line-height: 1.3;
    }
    .dapil-selected-item {
        display: flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        color: #0f172a;
    }
    .dapil-badge-pill {
        font-size: 0.72rem;
        color: #64748b;
        font-weight: normal;
    }
    
    .submit-btn {
        width: 100%;
        padding: 12px;
        font-size: 0.95rem;
        font-weight: 600;
        font-family: var(--font-heading);
        color: var(--color-white);
        background-color: rgb(var(--color-primary-rgb));
        border: none;
        border-radius: var(--border-radius-md);
        cursor: pointer;
        transition: var(--transition-smooth);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: var(--shadow-sm);
        margin-top: 14px;
    }
    
    .submit-btn:hover {
        background-color: var(--color-primary-hover);
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }
    
    .form-footer {
        text-align: center;
        margin-top: 16px;
        font-size: 0.85rem;
        color: var(--color-gray-600);
    }
    
    .form-footer a {
        color: rgb(var(--color-primary-rgb));
        font-weight: 600;
    }
    
    .back-home {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 10px;
        font-size: 0.8rem;
        color: var(--color-gray-400);
        font-weight: 500;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-8px); }
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .mobile-logo-wrapper {
        display: none;
        text-align: center;
        margin-bottom: 20px;
    }
    
    .mobile-mpr-logo {
        width: 60px;
        height: 60px;
        object-fit: contain;
        filter: drop-shadow(0 4px 10px rgba(0, 0, 0, 0.08));
        transition: var(--transition-smooth);
    }
    
    .mobile-mpr-logo:hover {
        transform: scale(1.05);
    }

    @media (max-width: 992px) {
        .brand-panel {
            display: none;
        }
        .form-panel {
            flex: 1;
            padding: 30px 20px;
            max-height: none;
        }
        .mobile-logo-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
        }
    }
    
    @media (max-width: 576px) {
        .input-row {
            grid-template-columns: 1fr;
            gap: 0;
        }
    }
</style>

<div class="split-container">
    <!-- Left Section: Brand Showcase -->
    <div class="brand-panel">
        <div class="brand-content">
            <div class="brand-logo">
                <img src="{{ asset('img/mpr-logo.svg') }}" alt="Logo MPR">
            </div>
            <h1 class="brand-title text-white">Mari Belajar <br><span class="text-black">Empat Pilar</span></h1>
            <p class="brand-subtitle">Daftarkan akun siswa SMA/SMK Anda sekarang untuk mengakses modul pembelajaran interaktif, mengerjakan evaluasi kuis, dan meningkatkan kompetensi kenegaraan.</p>
            
            <div class="pillars-grid">
                <div class="pillar-card">
                    <div class="pillar-icon">
                        <i class="fi fi-rr-shield text-white" style="font-size: 1.3rem;"></i>
                    </div>
                    <div class="pillar-info">
                        <h4>Pancasila</h4>
                        <p>Dasar & Ideologi Negara</p>
                    </div>
                </div>
                <div class="pillar-card">
                    <div class="pillar-icon">
                        <i class="fi fi-rr-scroll text-white" style="font-size: 1.3rem;"></i>
                    </div>
                    <div class="pillar-info">
                        <h4>UUD 1945</h4>
                        <p>Konstitusi Negara</p>
                    </div>
                </div>
                <div class="pillar-card">
                    <div class="pillar-icon">
                        <i class="fi fi-rr-map text-white" style="font-size: 1.3rem;"></i>
                    </div>
                    <div class="pillar-info">
                        <h4>NKRI</h4>
                        <p>Bentuk Negara</p>
                    </div>
                </div>
                <div class="pillar-card">
                    <div class="pillar-icon">
                        <i class="fi fi-rr-handshake text-white" style="font-size: 1.3rem;"></i>
                    </div>
                    <div class="pillar-info">
                        <h4>Bhinneka Tunggal Ika</h4>
                        <p>Semboyan Negara</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Right Section: Register Form -->
    <div class="form-panel">
        <div class="form-container">
            <div class="mobile-logo-wrapper">
                <a href="{{ route('home') }}" title="Kembali ke Beranda">
                    <img src="{{ asset('img/mpr-logo.svg') }}" alt="Logo MPR RI" class="mobile-mpr-logo">
                </a>
            </div>
            <div class="form-header">
                <h3>Daftar Akun Siswa</h3>
                <p>Lengkapi formulir biodata diri Anda (bidang bertanda <span class="required-star">*</span> wajib diisi).</p>
            </div>
            
            <form action="{{ route('register') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <!-- Mandatory Profile Photo with Camera Icon Trigger -->
                <div class="register-avatar-container">
                    <div class="register-avatar-wrapper" onclick="document.getElementById('image').click()" title="Klik untuk mengunggah foto profil">
                        <img id="register-avatar-preview" src="https://ui-avatars.com/api/?name=Siswa&background=dc2626&color=ffffff&size=120" alt="Preview Foto" class="register-avatar-preview">
                        <div class="register-camera-badge">
                            <i class="fi fi-rr-camera" style="font-size: 0.9rem; line-height: 1; display: flex; align-items: center; justify-content: center;"></i>
                        </div>
                    </div>

                    <input type="file" name="image" id="image" style="display: none;" accept="image/jpeg,image/png,image/jpg,image/webp" required onchange="previewRegisterAvatar(event)">
                    
                    <button type="button" class="btn btn-secondary btn-sm" style="padding: 4px 12px; font-size: 0.78rem; border-radius: 20px; display: inline-flex; align-items: center; gap: 5px; background: var(--color-gray-100); border: 1px solid var(--color-gray-300);" onclick="document.getElementById('image').click()">
                        <i class="fi fi-rr-camera" style="font-size: 0.8rem; color: rgb(var(--color-primary-rgb));"></i> Upload Foto Profil <span class="required-star">*</span>
                    </button>
                    <div style="font-size: 0.72rem; color: var(--color-gray-500); margin-top: 3px;">Format JPG, PNG, WEBP (Maks 2MB)</div>

                    @error('image')
                        <span class="invalid-feedback" style="display: block; margin-top: 4px;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="custom-form-group">
                    <label for="name">Nama Lengkap Siswa <span class="required-star">*</span></label>
                    <input type="text" name="name" id="name" class="custom-input @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Nama lengkap Anda" required autocomplete="name" autofocus>
                    @error('name')
                        <span class="invalid-feedback" style="display: block; margin-top: 4px;">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="custom-form-group">
                    <label for="email">Alamat Email <span class="required-star">*</span></label>
                    <input type="email" name="email" id="email" class="custom-input @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="Contoh: siswa@gmail.com" required autocomplete="email">
                    @error('email')
                        <span class="invalid-feedback" style="display: block; margin-top: 4px;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="custom-form-group">
                    <label for="school_name">Asal Sekolah (SMA / SMK) <span class="required-star">*</span></label>
                    <input type="text" name="school_name" id="school_name" class="custom-input @error('school_name') is-invalid @enderror" value="{{ old('school_name') }}" placeholder="Contoh: SMAN 1 Jakarta / SMKN 2 Bandung" required>
                    @error('school_name')
                        <span class="invalid-feedback" style="display: block; margin-top: 4px;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Searchable Dapil Dropdown -->
                <div class="custom-form-group">
                    <label for="dapil">Daerah Pemilihan (Dapil) DPR-RI <span class="required-star">*</span></label>
                    <select name="dapil" id="dapil" class="@error('dapil') is-invalid @enderror" required>
                        <option value="">-- Pilih atau Cari Daerah Pemilihan (Dapil) --</option>
                        @foreach($dapilList as $dapilOption)
                            <option value="{{ $dapilOption }}" {{ old('dapil') === $dapilOption ? 'selected' : '' }}>
                                {{ $dapilOption }}
                            </option>
                        @endforeach
                    </select>

                    @error('dapil')
                        <span class="invalid-feedback" style="display: block; margin-top: 4px;">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Address (Mandatory) -->
                <div class="custom-form-group">
                    <label for="address">Alamat Rumah Tinggal Lengkap <span class="required-star">*</span></label>
                    <input type="text" name="address" id="address" class="custom-input @error('address') is-invalid @enderror" value="{{ old('address') }}" placeholder="Alamat lengkap tempat tinggal siswa (Jalan, RT/RW, Kel/Kec)" required>
                    @error('address')
                        <span class="invalid-feedback" style="display: block; margin-top: 4px;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="input-row">
                    <div class="custom-form-group">
                        <label for="password">Kata Sandi <span class="required-star">*</span></label>
                        <input type="password" name="password" id="password" class="custom-input @error('password') is-invalid @enderror" placeholder="Min. 8 karakter" required autocomplete="new-password">
                        @error('password')
                            <span class="invalid-feedback" style="display: block; margin-top: 4px;">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="custom-form-group">
                        <label for="password_confirmation">Konfirmasi Sandi <span class="required-star">*</span></label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="custom-input" placeholder="Ulangi kata sandi" required autocomplete="new-password">
                    </div>
                </div>
                
                <button type="submit" class="submit-btn">
                    <span>Daftar Akun Baru</span> ➔
                </button>
            </form>
            
            <div class="form-footer">
                <p>Sudah memiliki akun? <a href="{{ route('login') }}">Masuk di sini</a></p>
                <div>
                    <a href="{{ route('home') }}" class="back-home">← Kembali ke Beranda</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tom Select JS -->
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
    function previewRegisterAvatar(event) {
        const input = event.target;
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('register-avatar-preview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        new TomSelect("#dapil", {
            create: false,
            maxOptions: 100,
            allowEmptyOption: true,
            placeholder: "🔍 Cari kota/kabupaten atau nama Dapil..."
        });
    });
</script>
@endsection
