@extends('layouts.app')

@section('title', 'Daftar Akun Siswa - Empat Pilar')

@section('content')
<style>
    .split-container {
        display: flex;
        min-height: 100vh;
        width: 100vw;
        background-color: var(--color-white);
        overflow: hidden;
    }
    
    .brand-panel {
        flex: 1.2;
        background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-secondary) 100%);
        color: var(--color-white);
        display: flex;
        flex-direction: column;
        justify-content: center;
        padding: 80px;
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
        font-size: 3rem;
        margin-bottom: 20px;
        filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2));
        animation: float 4s ease-in-out infinite;
    }
    
    .brand-title {
        font-size: 2.8rem;
        font-weight: 800;
        line-height: 1.2;
        margin-bottom: 16px;
        letter-spacing: -0.5px;
    }
    
    .brand-title span {
        color: var(--color-secondary);
    }
    
    .brand-subtitle {
        font-size: 1.1rem;
        color: rgba(255, 255, 255, 0.8);
        margin-bottom: 40px;
        font-weight: 400;
    }
    
    .pillars-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    
    .pillar-card {
        background: rgba(255, 255, 255, 0.06);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: var(--border-radius-md);
        padding: 20px;
        transition: var(--transition-smooth);
        display: flex;
        align-items: center;
        gap: 16px;
    }
    
    .pillar-card:hover {
        transform: translateY(-5px);
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 193, 7, 0.3);
        box-shadow: 0 10px 20px rgba(0,0,0,0.15);
    }
    
    .pillar-icon {
        font-size: 2rem;
        background: rgba(255, 255, 255, 0.1);
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: var(--border-radius-sm);
        flex-shrink: 0;
    }
    
    .pillar-info h4 {
        color: var(--color-white);
        font-size: 0.95rem;
        margin-bottom: 2px;
        font-weight: 600;
    }
    
    .pillar-info p {
        font-size: 0.75rem;
        color: rgba(255, 255, 255, 0.6);
        font-weight: 400;
    }
    
    .form-panel {
        flex: 0.8;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 60px;
        background-color: var(--color-white);
        position: relative;
    }
    
    .form-container {
        width: 100%;
        max-width: 480px;
        animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    }
    
    .form-header {
        margin-bottom: 24px;
    }
    
    .form-header h3 {
        font-size: 2rem;
        font-weight: 700;
        color: var(--color-dark);
        margin-bottom: 6px;
    }
    
    .form-header p {
        font-size: 0.95rem;
        color: var(--color-gray-600);
    }
    
    .custom-form-group {
        margin-bottom: 16px;
    }
    
    .custom-form-group label {
        display: block;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--color-dark);
        margin-bottom: 6px;
    }
    
    .input-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }
    
    .custom-input {
        width: 100%;
        padding: 12px 16px;
        font-size: 0.95rem;
        font-family: var(--font-body);
        color: var(--color-dark);
        background-color: var(--color-gray-100);
        border: 2px solid transparent;
        border-radius: var(--border-radius-md);
        transition: var(--transition-smooth);
        outline: none;
    }
    
    .custom-input:focus {
        background-color: var(--color-white);
        border-color: rgb(var(--color-primary-rgb));
        box-shadow: 0 0 0 4px rgba(var(--color-primary-rgb), 0.1);
    }
    
    .custom-input.is-invalid {
        border-color: var(--color-danger);
        background-color: rgba(244, 67, 54, 0.02);
    }
    
    .custom-input.is-invalid:focus {
        box-shadow: 0 0 0 4px rgba(244, 67, 54, 0.1);
    }
    
    .submit-btn {
        width: 100%;
        padding: 14px;
        font-size: 1rem;
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
        margin-top: 16px;
    }
    
    .submit-btn:hover {
        background-color: var(--color-primary-hover);
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }
    
    .submit-btn:active {
        transform: translateY(0);
    }
    
    .form-footer {
        text-align: center;
        margin-top: 24px;
        font-size: 0.9rem;
        color: var(--color-gray-600);
    }
    
    .form-footer a {
        color: rgb(var(--color-primary-rgb));
        font-weight: 600;
    }
    
    .form-footer a:hover {
        color: var(--color-primary-hover);
        text-decoration: underline;
    }
    
    .back-home {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 20px;
        font-size: 0.85rem;
        color: var(--color-gray-400);
        font-weight: 500;
    }
    
    .back-home:hover {
        color: var(--color-gray-600);
    }

    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    /* Responsive styling */
    @media (max-width: 992px) {
        .brand-panel {
            display: none;
        }
        .form-panel {
            flex: 1;
            padding: 40px 20px;
        }
    }
    
    @media (max-width: 576px) {
        .input-row {
            grid-template-columns: 1fr;
            gap: 0;
        }
    }
    
    select.custom-input {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23475569' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 16px center;
        background-size: 16px;
        padding-right: 40px;
    }
</style>

<div class="split-container">
    <!-- Left Section: Brand Showcase -->
    <div class="brand-panel">
        <div class="brand-content">
            <div class="brand-logo">
                <i class="fi fi-rr-graduation-cap" style="color:text-black display: inline-block; vertical-align: middle;"></i>
            </div>
            <h1 class="brand-title text-white">Mari Belajar <br><span class="text-black">Empat Pilar</span></h1>
            <p class="brand-subtitle">Buat akun siswa sekarang untuk mengakses modul pembelajaran interaktif, mengerjakan kuis, dan melacak perkembangan belajar Anda.</p>
            
            <div class="pillars-grid">
                <div class="pillar-card">
                    <div class="pillar-icon">
                        <i class="fi fi-rr-shield text-white" style="font-size: 1.5rem;"></i>
                    </div>
                    <div class="pillar-info">
                        <h4>Pancasila</h4>
                        <p>Dasar & Ideologi Negara</p>
                    </div>
                </div>
                <div class="pillar-card">
                    <div class="pillar-icon">
                        <i class="fi fi-rr-scroll text-white" style="font-size: 1.5rem;"></i>
                    </div>
                    <div class="pillar-info">
                        <h4>UUD 1945</h4>
                        <p>Konstitusi Negara</p>
                    </div>
                </div>
                <div class="pillar-card">
                    <div class="pillar-icon">
                        <i class="fi fi-rr-map text-white" style="font-size: 1.5rem;"></i>
                    </div>
                    <div class="pillar-info">
                        <h4>NKRI</h4>
                        <p>Bentuk Negara</p>
                    </div>
                </div>
                <div class="pillar-card">
                    <div class="pillar-icon">
                        <i class="fi fi-rr-handshake text-white" style="font-size: 1.5rem;"></i>
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
            <div class="form-header">
                <h3>Daftar Akun</h3>
                <p>Silakan isi data diri Anda untuk memulai pembelajaran.</p>
            </div>
            
            <form action="{{ route('register') }}" method="POST">
                @csrf
                
                <div class="custom-form-group">
                    <label for="name">Nama Lengkap</label>
                    <input type="text" name="name" id="name" class="custom-input @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Nama lengkap Anda" required autocomplete="name" autofocus>
                    @error('name')
                        <span class="invalid-feedback" style="display: block; margin-top: 4px;">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="custom-form-group">
                    <label for="email">Alamat Email</label>
                    <input type="email" name="email" id="email" class="custom-input @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="Contoh: siswa@gmail.com" required autocomplete="email">
                    @error('email')
                        <span class="invalid-feedback" style="display: block; margin-top: 4px;">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="input-row">
                    <div class="custom-form-group">
                        <label for="class_name">Kelas</label>
                        <select name="class_name" id="class_name" class="custom-input @error('class_name') is-invalid @enderror" required>
                            <option value="" disabled {{ old('class_name') == '' ? 'selected' : '' }}>Pilih Kelas</option>
                            <option value="X" {{ old('class_name') == 'X' ? 'selected' : '' }}>X</option>
                            <option value="XI" {{ old('class_name') == 'XI' ? 'selected' : '' }}>XI</option>
                            <option value="XII" {{ old('class_name') == 'XII' ? 'selected' : '' }}>XII</option>
                        </select>
                        @error('class_name')
                            <span class="invalid-feedback" style="display: block; margin-top: 4px;">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="custom-form-group">
                        <label for="school_name">Asal Sekolah</label>
                        <input type="text" name="school_name" id="school_name" class="custom-input @error('school_name') is-invalid @enderror" value="{{ old('school_name') }}" placeholder="Contoh: SMKN 1 Jakarta" required>
                        @error('school_name')
                            <span class="invalid-feedback" style="display: block; margin-top: 4px;">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="input-row">
                    <div class="custom-form-group">
                        <label for="password">Kata Sandi</label>
                        <input type="password" name="password" id="password" class="custom-input @error('password') is-invalid @enderror" placeholder="Min. 8 karakter" required autocomplete="new-password">
                        @error('password')
                            <span class="invalid-feedback" style="display: block; margin-top: 4px;">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="custom-form-group">
                        <label for="password_confirmation">Konfirmasi Sandi</label>
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
@endsection
