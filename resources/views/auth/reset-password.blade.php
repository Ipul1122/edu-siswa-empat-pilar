@extends('layouts.app')

@section('title', 'Buat Kata Sandi Baru - Empat Pilar')

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
        width: 72px;
        height: 72px;
        filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2));
        animation: float 4s ease-in-out infinite;
    }
    .brand-logo img {
        width: 100%;
        height: 100%;
        object-fit: contain;
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
        color: rgba(255, 255, 255, 0.85);
        margin-bottom: 32px;
        font-weight: 400;
        line-height: 1.6;
    }

    .step-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.25);
        color: #fff;
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 20px;
    }

    .step-badge .step-num {
        background: var(--color-secondary);
        color: var(--color-dark);
        width: 20px;
        height: 20px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 800;
    }
    
    .form-panel {
        flex: 0.8;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 90px 60px;
        background-color: var(--color-white);
        position: relative;
        overflow-y: auto;
    }
    
    .form-container {
        width: 100%;
        max-width: 420px;
        animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    }
    
    .form-header {
        margin-bottom: 28px;
    }

    .form-step-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.8rem;
        font-weight: 700;
        color: #10b981;
        background: rgba(16, 185, 129, 0.1);
        padding: 4px 10px;
        border-radius: 6px;
        margin-bottom: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
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
        margin-bottom: 4px;
    }
    
    .email-highlight {
        font-weight: 600;
        color: rgb(var(--color-primary-rgb));
        background-color: rgba(var(--color-primary-rgb), 0.06);
        border: 1px solid rgba(var(--color-primary-rgb), 0.15);
        padding: 6px 12px;
        border-radius: var(--border-radius-sm);
        display: inline-block;
        margin-top: 6px;
        word-break: break-all;
    }
    
    .custom-form-group {
        margin-bottom: 20px;
    }
    
    .custom-form-group label {
        display: block;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--color-dark);
        margin-bottom: 8px;
    }

    .input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }
    
    .custom-input {
        width: 100%;
        height: 50px;
        padding: 12px 46px 12px 16px;
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

    .toggle-password {
        position: absolute;
        right: 14px;
        background: none;
        border: none;
        cursor: pointer;
        color: var(--color-gray-500);
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: var(--transition-smooth);
    }

    .toggle-password:hover {
        color: var(--color-dark);
    }

    .password-hints {
        background: var(--color-gray-100);
        border-radius: var(--border-radius-md);
        padding: 12px 14px;
        margin-bottom: 22px;
        font-size: 0.8rem;
        color: var(--color-gray-600);
        line-height: 1.5;
    }

    .password-hints ul {
        margin: 6px 0 0 18px;
        padding: 0;
    }

    .password-hints li {
        margin-bottom: 2px;
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
        margin-top: 10px;
    }
    
    .submit-btn:hover {
        background-color: var(--color-primary-hover);
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }
    
    .submit-btn:active {
        transform: translateY(0);
    }

    .cancel-row {
        text-align: center;
        margin-top: 24px;
        font-size: 0.9rem;
    }

    .cancel-link {
        color: var(--color-gray-600);
        font-weight: 500;
        text-decoration: none;
    }

    .cancel-link:hover {
        color: var(--color-dark);
        text-decoration: underline;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .mobile-logo-wrapper {
        display: none;
        text-align: center;
        margin-bottom: 24px;
    }
    
    .mobile-mpr-logo {
        width: 64px;
        height: 64px;
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
            padding: 40px 20px;
        }
        .mobile-logo-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
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
            <div class="step-badge">
                <span class="step-num">2</span>
                <span>Langkah 2 dari 2: Kata Sandi Baru</span>
            </div>
            <h1 class="brand-title">Atur Kata Sandi <br><span>Baru Anda</span></h1>
            <p class="brand-subtitle">Silakan buat kata sandi baru yang aman dan kuat untuk mengamankan akun Siswa Anda. Pastikan kata sandi mudah Anda ingat.</p>
        </div>
    </div>
    
    <!-- Right Section: New Password Form -->
    <div class="form-panel">
        <div class="form-container">
            <div class="mobile-logo-wrapper">
                <a href="{{ route('home') }}" title="Kembali ke Beranda">
                    <img src="{{ asset('img/mpr-logo.svg') }}" alt="Logo MPR RI" class="mobile-mpr-logo">
                </a>
            </div>
            <div class="form-header">
                <div class="form-step-pill">✓ OTP Terverifikasi — Langkah 2</div>
                <h3>Kata Sandi Baru</h3>
                <p>Menyetel ulang sandi untuk akun:</p>
                <div class="email-highlight">{{ $email ?? session('reset_email', 'Akun Anda') }}</div>
            </div>
            
            <form action="{{ route('password.update') }}" method="POST" id="resetPasswordForm">
                @csrf
                
                <div class="custom-form-group">
                    <label for="password">Kata Sandi Baru</label>
                    <div class="input-wrapper">
                        <input type="password" name="password" id="password" class="custom-input @error('password') is-invalid @enderror" placeholder="Minimal 8 karakter" required autocomplete="new-password" autofocus>
                        <button type="button" class="toggle-password" onclick="togglePasswordVisibility('password', this)" title="Lihat/Sembunyikan Sandi">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                    @error('password')
                        <span class="invalid-feedback" style="display: block; margin-top: 6px; color: var(--color-danger); font-size: 0.85rem;">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="custom-form-group">
                    <label for="password_confirmation">Konfirmasi Kata Sandi Baru</label>
                    <div class="input-wrapper">
                        <input type="password" name="password_confirmation" id="password_confirmation" class="custom-input" placeholder="Ulangi kata sandi baru" required autocomplete="new-password">
                        <button type="button" class="toggle-password" onclick="togglePasswordVisibility('password_confirmation', this)" title="Lihat/Sembunyikan Sandi">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                </div>

                <div class="password-hints">
                    <strong>Ketentuan Keamanan Sandi:</strong>
                    <ul>
                        <li>Panjang minimal 8 karakter</li>
                        <li>Kombinasikan huruf besar, huruf kecil, dan angka</li>
                        <li>Hindari menggunakan tanggal lahir atau nama lengkap</li>
                    </ul>
                </div>
                
                <button type="submit" class="submit-btn">
                    <span>Simpan Kata Sandi Baru</span> ➔
                </button>
            </form>
            
            <div class="cancel-row">
                <a href="{{ route('login') }}" class="cancel-link">Batal & Kembali ke Login</a>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePasswordVisibility(fieldId, btn) {
        const input = document.getElementById(fieldId);
        if (!input) return;

        if (input.type === 'password') {
            input.type = 'text';
            btn.innerHTML = `<svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>`;
        } else {
            input.type = 'password';
            btn.innerHTML = `<svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>`;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('resetPasswordForm');
        const passInput = document.getElementById('password');
        const passConfirmInput = document.getElementById('password_confirmation');

        if (form && passInput && passConfirmInput) {
            form.addEventListener('submit', function(e) {
                if (passInput.value.length < 8) {
                    e.preventDefault();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Kata Sandi Kurang Panjang',
                            text: 'Kata sandi minimal harus terdiri dari 8 karakter demi keamanan akun Anda.',
                            confirmButtonColor: 'rgb(var(--color-primary-rgb, 229, 57, 53))',
                            confirmButtonText: 'Perbaiki'
                        });
                    }
                    passInput.focus();
                    return false;
                }

                if (passInput.value !== passConfirmInput.value) {
                    e.preventDefault();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Konfirmasi Sandi Berbeda',
                            text: 'Kata sandi baru dan konfirmasi kata sandi tidak cocok. Silakan ketik ulang dengan teliti.',
                            confirmButtonColor: 'rgb(var(--color-primary-rgb, 229, 57, 53))',
                            confirmButtonText: 'Perbaiki'
                        });
                    }
                    passConfirmInput.focus();
                    return false;
                }
            });
        }
    });
</script>
@endsection
