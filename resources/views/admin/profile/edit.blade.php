@extends('layouts.admin')

@section('title', 'Pengaturan Akun Admin - Empat Pilar')

@section('content')
<style>
    .avatar-camera-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        margin-bottom: 24px;
        text-align: center;
    }
    .avatar-wrapper-circle {
        position: relative;
        width: 104px;
        height: 104px;
        cursor: pointer;
        margin-bottom: 8px;
    }
    .avatar-preview-img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid rgb(var(--color-primary-rgb));
        box-shadow: var(--shadow-md);
        transition: var(--transition-smooth);
        background-color: var(--color-white);
    }
    .avatar-wrapper-circle:hover .avatar-preview-img {
        filter: brightness(0.9);
    }
    .camera-badge-btn {
        position: absolute;
        bottom: 2px;
        right: 2px;
        background-color: rgb(var(--color-primary-rgb));
        color: var(--color-white);
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.25);
        border: 2px solid var(--color-white);
        transition: transform 0.2s ease, background-color 0.2s ease;
    }
    .avatar-wrapper-circle:hover .camera-badge-btn {
        transform: scale(1.1);
        background-color: var(--color-primary-hover);
    }
    .required-star {
        color: var(--color-danger);
        font-weight: 700;
        margin-left: 2px;
    }
</style>

<div class="page-header">
    <div class="page-title">
        <h1>Pengaturan Akun Administrator</h1>
        <p>Kelola data kredensial, perbarui foto profil, alamat email, serta kata sandi administrator.</p>
    </div>
</div>

<div class="card" style="max-width: 680px; margin: 0 auto; width: 100%;">
    <div class="card-body" style="padding: 32px;">
        <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <h3 style="font-size: 1.15rem; margin-bottom: 20px; border-bottom: 1px solid var(--color-gray-200); padding-bottom: 8px; color: var(--color-dark); display: flex; align-items: center; gap: 8px;">
                <i class="fi fi-rr-user" style="color: rgb(var(--color-primary-rgb)); font-size: 1.25rem;"></i> Informasi Profil Admin
            </h3>

            <!-- Camera Icon Avatar Upload Trigger -->
            <div class="avatar-camera-container">
                <div class="avatar-wrapper-circle" onclick="document.getElementById('image').click()" title="Klik untuk mengganti foto profil">
                    <img id="admin-avatar-preview" src="{{ $admin->image_url }}" alt="{{ $admin->name }}" class="avatar-preview-img">
                    <div class="camera-badge-btn">
                        <i class="fi fi-rr-camera" style="font-size: 1rem; line-height: 1; display: flex; align-items: center; justify-content: center;"></i>
                    </div>
                </div>
                
                <input type="file" name="image" id="image" style="display: none;" accept="image/jpeg,image/png,image/jpg,image/webp" onchange="previewAdminAvatar(event)">
                
                <button type="button" class="btn btn-secondary btn-sm" style="padding: 4px 12px; font-size: 0.8rem; border-radius: 20px; display: inline-flex; align-items: center; gap: 6px;" onclick="document.getElementById('image').click()">
                    <i class="fi fi-rr-camera" style="font-size: 0.85rem;"></i> Ubah Foto Profil
                </button>
                <div style="font-size: 0.75rem; color: var(--color-gray-500); margin-top: 4px;">
                    Format JPG, JPEG, PNG, WEBP (Maks. 2MB).
                </div>

                @error('image')
                    <span class="invalid-feedback" style="display: block; margin-top: 6px;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Name -->
            <div class="form-group">
                <label for="name">Nama Lengkap Administrator <span class="required-star">*</span></label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $admin->name) }}" required placeholder="Nama administrator">
                @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- Email -->
            <div class="form-group">
                <label for="email">Alamat Email Administrator <span class="required-star">*</span></label>
                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $admin->email) }}" required placeholder="admin@example.com">
                @error('email')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
                <span style="font-size: 0.75rem; color: var(--color-gray-500); margin-top: 4px; display: block;">
                    Email digunakan untuk proses login akun panel admin.
                </span>
            </div>

            <!-- Password Section -->
            <h3 style="font-size: 1.15rem; margin-top: 36px; margin-bottom: 16px; border-bottom: 1px solid var(--color-gray-200); padding-bottom: 8px; color: var(--color-dark); display: flex; align-items: center; gap: 8px;">
                <i class="fi fi-rr-key" style="color: rgb(var(--color-primary-rgb)); font-size: 1.25rem;"></i> Ganti Kata Sandi (Opsional)
            </h3>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label for="password">Kata Sandi Baru</label>
                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Minimal 8 karakter">
                    @error('password')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Sandi Baru</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Ulangi sandi baru">
                </div>
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 32px; border-top: 1px solid var(--color-gray-200); padding-top: 20px;">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function previewAdminAvatar(event) {
        const input = event.target;
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('admin-avatar-preview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
