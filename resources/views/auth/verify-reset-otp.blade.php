@extends('layouts.app')

@section('title', 'Verifikasi OTP & Reset Sandi - Empat Pilar')

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
        color: rgba(255, 255, 255, 0.8);
        margin-bottom: 40px;
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
        margin-bottom: 24px;
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
    
    .email-highlight {
        font-weight: 600;
        color: rgb(var(--color-primary-rgb));
        background-color: rgba(var(--color-primary-rgb), 0.05);
        padding: 4px 8px;
        border-radius: var(--border-radius-sm);
        display: inline-block;
        margin-top: 6px;
    }
    
    .custom-form-group {
        margin-bottom: 16px;
    }
    
    .custom-form-group label {
        display: block;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--color-dark);
        margin-bottom: 8px;
    }
    
    .otp-input-container {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 10px;
    }
    
    .otp-field {
        width: 50px;
        height: 54px;
        font-size: 1.5rem;
        font-weight: 700;
        text-align: center;
        border: 2px solid var(--color-gray-200);
        background-color: var(--color-gray-100);
        border-radius: var(--border-radius-sm);
        outline: none;
        transition: var(--transition-smooth);
        font-family: var(--font-heading);
    }
    
    .otp-field:focus {
        border-color: rgb(var(--color-primary-rgb));
        background-color: var(--color-white);
        box-shadow: 0 0 0 3px rgba(var(--color-primary-rgb), 0.1);
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
    
    .actions-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 24px;
        font-size: 0.9rem;
    }
    
    .resend-btn {
        background: none;
        border: none;
        color: rgb(var(--color-primary-rgb));
        font-weight: 600;
        font-family: var(--font-body);
        font-size: 0.9rem;
        cursor: pointer;
        padding: 0;
        transition: var(--transition-smooth);
    }
    
    .resend-btn:hover {
        color: var(--color-primary-hover);
        text-decoration: underline;
    }
    
    .cancel-link {
        color: var(--color-gray-600);
        font-weight: 500;
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
            <h1 class="brand-title">Atur Ulang <br><span>Kata Sandi Anda</span></h1>
            <p class="brand-subtitle">Masukkan kode OTP yang dikirim ke email Anda, lalu tentukan kata sandi baru Anda yang aman untuk dapat masuk kembali ke sistem.</p>
        </div>
    </div>
    
    <!-- Right Section: OTP & Password Form -->
    <div class="form-panel">
        <div class="form-container">
            <div class="mobile-logo-wrapper">
                <a href="{{ route('home') }}" title="Kembali ke Beranda">
                    <img src="{{ asset('img/mpr-logo.svg') }}" alt="Logo MPR RI" class="mobile-mpr-logo">
                </a>
            </div>
            <div class="form-header">
                <h3>Pulihkan Akun</h3>
                <p>Masukkan kode OTP yang dikirim ke:</p>
                <div class="email-highlight">{{ session('reset_email', 'Email Anda') }}</div>
            </div>
            
            <form action="{{ route('password.update') }}" method="POST" id="resetForm">
                @csrf
                
                <div class="custom-form-group">
                    <label>Kode OTP</label>
                    <div class="otp-input-container">
                        <input type="text" class="otp-field" maxlength="1" required pattern="[0-9]">
                        <input type="text" class="otp-field" maxlength="1" required pattern="[0-9]">
                        <input type="text" class="otp-field" maxlength="1" required pattern="[0-9]">
                        <input type="text" class="otp-field" maxlength="1" required pattern="[0-9]">
                        <input type="text" class="otp-field" maxlength="1" required pattern="[0-9]">
                        <input type="text" class="otp-field" maxlength="1" required pattern="[0-9]">
                    </div>
                    
                    <!-- Hidden field to combine the values -->
                    <input type="hidden" name="otp" id="fullOtp">
                    
                    @error('otp')
                        <span class="invalid-feedback" style="display: block; margin-top: 6px; text-align: center;">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="custom-form-group">
                    <label for="password">Kata Sandi Baru</label>
                    <input type="password" name="password" id="password" class="custom-input @error('password') is-invalid @enderror" placeholder="Minimal 8 karakter" required autocomplete="new-password">
                    @error('password')
                        <span class="invalid-feedback" style="display: block; margin-top: 6px;">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="custom-form-group">
                    <label for="password_confirmation">Konfirmasi Kata Sandi</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="custom-input" placeholder="Ulangi kata sandi baru" required autocomplete="new-password">
                </div>
                
                <button type="submit" class="submit-btn">
                    <span>Atur Ulang Sandi</span> ➔
                </button>
            </form>
            
            <div class="actions-row">
                <form action="{{ route('password.resend_otp') }}" method="POST" id="resendForm">
                    @csrf
                    <button type="submit" class="resend-btn" id="btnResend">Kirim Ulang Kode</button>
                </form>
                
                <a href="{{ route('password.request') }}" class="cancel-link">Kembali</a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fields = document.querySelectorAll('.otp-field');
        const hiddenField = document.getElementById('fullOtp');
        const form = document.getElementById('resetForm');
        
        fields[0].focus();
        
        fields.forEach((field, index) => {
            field.addEventListener('input', function(e) {
                // Allow only numbers
                field.value = field.value.replace(/[^0-9]/g, '');
                
                if (field.value.length === 1 && index < fields.length - 1) {
                    fields[index + 1].focus();
                }
                combineOtp();
            });
            
            field.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && field.value.length === 0 && index > 0) {
                    fields[index - 1].focus();
                }
            });
            
            // Paste support
            field.addEventListener('paste', function(e) {
                e.preventDefault();
                const pasteData = e.clipboardData.getData('text').trim().replace(/[^0-9]/g, '');
                if (pasteData.length === 6) {
                    fields.forEach((f, i) => {
                        f.value = pasteData[i];
                    });
                    combineOtp();
                    fields[5].focus();
                }
            });
        });
        
        function combineOtp() {
            let value = '';
            fields.forEach(f => value += f.value);
            hiddenField.value = value;
        }
        
        form.addEventListener('submit', function(e) {
            combineOtp();
            if (hiddenField.value.length !== 6) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan',
                    text: 'Silakan isi semua 6 digit kode OTP.'
                });
            }
        });
        
        // Cooldown resend button
        const btnResend = document.getElementById('btnResend');
        const resendForm = document.getElementById('resendForm');
        
        resendForm.addEventListener('submit', function() {
            btnResend.disabled = true;
            btnResend.style.opacity = '0.5';
            btnResend.style.cursor = 'not-allowed';
            btnResend.innerText = 'Mengirim...';
        });
    });
</script>
@endsection
