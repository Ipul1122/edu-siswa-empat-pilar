@extends('layouts.siswa')

@section('title', 'Edit Profil & Biodata - Empat Pilar')

@section('content')
<!-- Tom Select CDN (Searchable Dropdown) -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
    .ts-wrapper {
        width: 100% !important;
        position: relative !important;
    }
    .ts-control {
        border-radius: var(--border-radius-md) !important;
        border: 1.5px solid var(--color-gray-300) !important;
        padding: 10px 14px !important;
        font-size: 0.95rem !important;
        font-family: var(--font-body) !important;
        background-color: var(--color-white) !important;
        background: var(--color-white) !important;
        min-height: 48px !important;
        box-shadow: none !important;
        transition: var(--transition-smooth) !important;
    }
    .ts-control:focus, .ts-wrapper.focus .ts-control {
        border-color: rgb(var(--color-primary-rgb)) !important;
        box-shadow: 0 0 0 3px rgba(var(--color-primary-rgb), 0.12) !important;
    }
    .ts-dropdown {
        position: absolute !important;
        background-color: #ffffff !important;
        background: #ffffff !important;
        border: 1px solid var(--color-gray-300) !important;
        border-radius: var(--border-radius-md) !important;
        box-shadow: 0 14px 28px rgba(0, 0, 0, 0.18), 0 10px 10px rgba(0, 0, 0, 0.12) !important;
        font-size: 0.9rem !important;
        max-height: 280px !important;
        overflow-y: auto !important;
        z-index: 999999 !important;
        padding: 6px 0 !important;
        margin-top: 4px !important;
    }
    .ts-dropdown .option {
        background-color: #ffffff !important;
        background: #ffffff !important;
        padding: 10px 14px !important;
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
        gap: 3px;
    }
    .dapil-main-title {
        font-weight: 700;
        color: #0f172a;
        font-size: 0.92rem;
    }
    .dapil-subtext {
        font-size: 0.78rem;
        color: #64748b;
        line-height: 1.35;
    }
    .dapil-selected-item {
        display: flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        color: #0f172a;
    }
    .dapil-badge-pill {
        font-size: 0.75rem;
        color: #64748b;
        font-weight: normal;
    }
    .required-star {
        color: var(--color-danger);
        font-weight: 700;
        margin-left: 2px;
    }
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
</style>

<div class="page-header">
    <div class="page-title">
        <h1>Pengaturan Biodata & Profil</h1>
        <p>Perbarui data kependudukan (foto profil, alamat & Dapil), asal sekolah, dan kredensial akun Anda.</p>
    </div>
</div>

<div class="card" style="max-width: 720px; margin: 0 auto; width: 100%;">
    <div class="card-body">
        <form action="{{ route('siswa.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <h3 style="font-size: 1.15rem; margin-bottom: 20px; border-bottom: 1px solid var(--color-gray-200); padding-bottom: 8px; color: var(--color-dark); display: flex; align-items: center; gap: 8px;">
                <i class="fi fi-rr-id-badge" style="color: rgb(var(--color-primary-rgb)); font-size: 1.25rem;"></i> Informasi Biodata Siswa
            </h3>

            <!-- Camera Icon Avatar Upload Trigger -->
            <div class="avatar-camera-container">
                <div class="avatar-wrapper-circle" onclick="document.getElementById('image').click()" title="Klik untuk mengganti foto profil">
                    <img id="avatar-preview" src="{{ $user->image_url }}" alt="{{ $user->name }}" class="avatar-preview-img">
                    <div class="camera-badge-btn">
                        <i class="fi fi-rr-camera" style="font-size: 1rem; line-height: 1; display: flex; align-items: center; justify-content: center;"></i>
                    </div>
                </div>
                
                <input type="file" name="image" id="image" style="display: none;" accept="image/jpeg,image/png,image/jpg,image/webp" onchange="previewAvatar(event)">
                
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

            <div class="form-group">
                <label for="name">Nama Lengkap Siswa <span class="required-star">*</span></label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required placeholder="Masukkan nama lengkap siswa">
                @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">Alamat Email (Akun)</label>
                <input type="email" id="email" class="form-control" value="{{ $user->email }}" disabled style="background-color: var(--color-gray-100); cursor: not-allowed; color: var(--color-gray-600);">
                <span style="font-size: 0.75rem; color: var(--color-gray-400); margin-top: 4px; display: block;">Email digunakan untuk masuk dan verifikasi akun.</span>
            </div>

            <div class="form-group">
                <label for="school_name">Asal Sekolah (SMA / SMK) <span class="required-star">*</span></label>
                <input type="text" name="school_name" id="school_name" class="form-control @error('school_name') is-invalid @enderror" value="{{ old('school_name', $user->school_name) }}" required placeholder="Contoh: SMAN 1 Jakarta / SMKN 2 Bandung">
                @error('school_name')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- Enhanced Searchable Dapil Dropdown with Regencies Coverage Search -->
            <div class="form-group" style="margin-top: 18px;">
                <label for="dapil">Daerah Pemilihan (Dapil) DPR-RI <span class="required-star">*</span></label>
                <div style="font-size: 0.8rem; color: var(--color-gray-600); margin-bottom: 6px;">
                    Ketik nama kota/kabupaten Anda (contoh: <em>Bandung, Bogor, Surabaya, Medan, Depok</em>) untuk mencari Dapil secara otomatis.
                </div>
                <select name="dapil" id="dapil" class="form-control @error('dapil') is-invalid @enderror" required>
                    <option value="">-- Cari Kota/Kabupaten atau Pilih Dapil --</option>
                    @foreach($dapilDetails as $dapilName => $coverage)
                        <option value="{{ $dapilName }}" data-coverage="{{ $coverage }}" {{ old('dapil', $user->dapil) === $dapilName ? 'selected' : '' }}>
                            {{ $dapilName }}
                        </option>
                    @endforeach
                </select>

                @error('dapil')
                    <span class="invalid-feedback" style="display: block; margin-top: 6px;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Alamat Rumah -->
            <div class="form-group">
                <label for="address">Alamat Rumah Tinggal Lengkap <span class="required-star">*</span></label>
                <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" style="height: 90px;" placeholder="Masukkan alamat lengkap domisili tempat tinggal siswa (Jalan, RT/RW, Kelurahan, Kecamatan, Kota/Kabupaten)..." required>{{ old('address', $user->address) }}</textarea>
                @error('address')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

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
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Ketik ulang sandi baru">
                </div>
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 32px; border-top: 1px solid var(--color-gray-200); padding-top: 20px;">
                <a href="{{ route('siswa.dashboard') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Biodata</button>
            </div>
        </form>
    </div>
</div>

<!-- Tom Select JS -->
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
    function previewAvatar(event) {
        const input = event.target;
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatar-preview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        new TomSelect("#dapil", {
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            },
            searchField: ['text', 'coverage'],
            maxOptions: 100,
            placeholder: "🔍 Cari kota/kabupaten atau nama Dapil...",
            allowEmptyOption: true,
            dropdownParent: "body",
            render: {
                option: function(data, escape) {
                    const coverage = data.coverage ? '<div class="dapil-subtext"><i class="fi fi-rr-marker" style="margin-right:3px; vertical-align:middle;"></i> Meliputi: ' + escape(data.coverage) + '</div>' : '';
                    return '<div class="dapil-select-option">' +
                           '<div class="dapil-main-title">' + escape(data.text) + '</div>' +
                           coverage +
                           '</div>';
                },
                item: function(data, escape) {
                    const coverageBrief = data.coverage ? ' <span class="dapil-badge-pill">(' + escape(data.coverage) + ')</span>' : '';
                    return '<div class="dapil-selected-item"><strong>' + escape(data.text) + '</strong>' + coverageBrief + '</div>';
        });
    });
</script>
@endsection
