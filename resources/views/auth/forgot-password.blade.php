@extends('layouts.app')

@section('title', 'Lupa Kata Sandi Siswa - Empat Pilar')

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
        justify-content: flex-start;
        padding: 140px 80px 80px 80px;
        position: relative;
        overflow: hidden;
    }
    
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
        background: radial-gradient(circle, rgba(var(--color-primary-rgb), 0.2) 0%, rgba(var(--color-primary-rgb), 0) 70%);
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
        top: -90px;
        left: 0;
        font-size: 3rem;
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
        align-items: flex-start;
        justify-content: center;
        padding: 140px 60px 80px 60px;
        background-color: var(--color-white);
        position: relative;
        overflow-y: auto;
    }
    
    .form-container {
        width: 100%;
        max-width: 400px;
        animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    }
    
    .form-header {
        margin-bottom: 32px;
    }
    
    .form-header h3 {
        font-size: 2rem;
        font-weight: 700;
        color: var(--color-dark);
        margin-bottom: 8px;
    }
    
    .form-header p {
        font-size: 0.95rem;
        color: var(--color-gray-600);
    }
    
    .custom-form-group {
        margin-bottom: 24px;
    }
    
    .custom-form-group label {
        display: block;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--color-dark);
        margin-bottom: 8px;
    }
    
    .custom-input {
        width: 100%;
        height: 50px;
        padding: 12px 16px;
        font-size: 0.95rem;
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
        margin-top: 32px;
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
        margin-top: 24px;
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
    
    @media (max-width: 992px) {
        .brand-panel {
            display: none;
        }
        .form-panel {
            flex: 1;
            padding: 40px 20px;
        }
    }
</style>

<div class="split-container">
    <!-- Left Section: Brand Showcase -->
    <div class="brand-panel">
        <div class="brand-content">
            <div class="brand-logo">
                <i class="fi fi-rr-shield" style="color: var(--color-secondary); display: inline-block; vertical-align: middle;"></i>
            </div>
            <h1 class="brand-title">Lupa Kata Sandi? <br><span>Pulihkan Segera</span></h1>
            <p class="brand-subtitle">Masukkan alamat email Anda yang terdaftar, dan kami akan mengirimkan kode verifikasi OTP via Gmail untuk menyetel ulang kata sandi Anda.</p>
            
            <div class="pillars-grid">
                <div class="pillar-card">
                    <div class="pillar-icon">
                        <i class="fi fi-rr-shield" style="color: #ff5252; font-size: 1.5rem;"></i>
                    </div>
                    <div class="pillar-info">
                        <h4>Pancasila</h4>
                        <p>Dasar & Ideologi Negara</p>
                    </div>
                </div>
                <div class="pillar-card">
                    <div class="pillar-icon">
                        <i class="fi fi-rr-scroll" style="color: #ffd740; font-size: 1.5rem;"></i>
                    </div>
                    <div class="pillar-info">
                        <h4>UUD 1945</h4>
                        <p>Konstitusi Negara</p>
                    </div>
                </div>
                <div class="pillar-card">
                    <div class="pillar-icon">
                        <i class="fi fi-rr-map" style="color: #40c4ff; font-size: 1.5rem;"></i>
                    </div>
                    <div class="pillar-info">
                        <h4>NKRI</h4>
                        <p>Bentuk Negara</p>
                    </div>
                </div>
                <div class="pillar-card">
                    <div class="pillar-icon">
                        <i class="fi fi-rr-handshake" style="color: #69f0ae; font-size: 1.5rem;"></i>
                    </div>
                    <div class="pillar-info">
                        <h4>Bhinneka Tunggal Ika</h4>
                        <p>Semboyan Negara</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Right Section: Forgot Password Form -->
    <div class="form-panel">
        <div class="form-container">
            <div class="form-header">
                <h3>Lupa Kata Sandi</h3>
                <p>Masukkan email terdaftar untuk menerima kode verifikasi OTP.</p>
            </div>
            
            <form action="{{ route('password.email') }}" method="POST">
                @csrf
                
                <div class="custom-form-group">
                    <label for="email">Alamat Email Terdaftar</label>
                    <input type="email" name="email" id="email" class="custom-input @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="Contoh: siswa@gmail.com" required autocomplete="email" autofocus>
                    @error('email')
                        <span class="invalid-feedback" style="display: block; margin-top: 6px;">{{ $message }}</span>
                    @enderror
                </div>
                
                <button type="submit" class="submit-btn">
                    <span>Kirim Kode OTP</span> ➔
                </button>
            </form>
            
            <div class="form-footer">
                <p>Ingat kata sandi Anda? <a href="{{ route('login') }}">Masuk di sini</a></p>
                <div>
                    <a href="{{ route('home') }}" class="back-home">← Kembali ke Beranda</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
