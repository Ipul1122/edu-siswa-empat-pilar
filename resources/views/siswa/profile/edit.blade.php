@extends('layouts.siswa')

@section('title', 'Edit Profil - Empat Pilar')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h1>Pengaturan Profil</h1>
        <p>Perbarui informasi biodata sekolah dan kredensial kata sandi akun Anda.</p>
    </div>
</div>

<div class="card" style="max-width: 650px; margin: 0 auto; width: 100%;">
    <div class="card-body">
        <form action="{{ route('siswa.profile.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <h3 style="font-size: 1.15rem; margin-bottom: 16px; border-bottom: 1px solid var(--color-gray-200); padding-bottom: 8px;">Informasi Biodata</h3>

            <div class="form-group">
                <label for="name">Nama Lengkap</label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">Alamat Email (Tidak dapat diubah)</label>
                <input type="email" id="email" class="form-control" value="{{ $user->email }}" disabled style="background-color: var(--color-gray-100); cursor: not-allowed; color: var(--color-gray-400);">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label for="class_name">Kelas</label>
                    <select name="class_name" id="class_name" class="form-control @error('class_name') is-invalid @enderror" required>
                        <option value="" disabled {{ old('class_name', $user->class_name) == '' ? 'selected' : '' }}>Pilih Kelas</option>
                        <option value="X" {{ old('class_name', $user->class_name) == 'X' ? 'selected' : '' }}>X</option>
                        <option value="XI" {{ old('class_name', $user->class_name) == 'XI' ? 'selected' : '' }}>XI</option>
                        <option value="XII" {{ old('class_name', $user->class_name) == 'XII' ? 'selected' : '' }}>XII</option>
                    </select>
                    @error('class_name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="school_name">Asal Sekolah</label>
                    <input type="text" name="school_name" id="school_name" class="form-control @error('school_name') is-invalid @enderror" value="{{ old('school_name', $user->school_name) }}" required>
                    @error('school_name')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <h3 style="font-size: 1.15rem; margin-top: 32px; margin-bottom: 16px; border-bottom: 1px solid var(--color-gray-200); padding-bottom: 8px;">Ganti Kata Sandi (Kosongkan jika tidak diubah)</h3>

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
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Ketik ulang sandi">
                </div>
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 32px; border-top: 1px solid var(--color-gray-200); padding-top: 20px;">
                <a href="{{ route('siswa.dashboard') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Perbarui Profil</button>
            </div>
        </form>
    </div>
</div>
@endsection
