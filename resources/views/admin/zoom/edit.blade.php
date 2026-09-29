@extends('layouts.admin')

@section('title', 'Edit Sesi Zoom - Admin Empat Pilar')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h1>Edit Sesi Pertemuan Zoom</h1>
        <p>Perbarui informasi tautan Zoom, kapasitas kuota peserta, dan jadwal pertemuan "{{ $zoom_session->title }}".</p>
    </div>
    <div class="page-actions" style="display: flex; gap: 8px;">
        <a href="{{ route('admin.zoom-sessions.show', $zoom_session) }}" class="btn btn-secondary">
            <i class="fi fi-rr-eye"></i> Lihat Detail & Peserta
        </a>
        <a href="{{ route('admin.zoom-sessions.index') }}" class="btn btn-secondary">
            ← Kembali ke Daftar
        </a>
    </div>
</div>

<div class="card" style="max-width: 900px; margin: 0 auto; width: 100%;">
    <div class="card-body" style="padding: 32px;">
        <form action="{{ route('admin.zoom-sessions.update', $zoom_session) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Judul Sesi -->
            <div class="form-group" style="margin-bottom: 24px;">
                <label for="title" style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--color-dark);">
                    Judul Pertemuan / Topik Webinar <span style="color: var(--color-danger);">*</span>
                </label>
                <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $zoom_session->title) }}" placeholder="Contoh: Webinar Pendalaman Nilai Pancasila" required style="width: 100%; padding: 12px 16px; border-radius: var(--border-radius-sm); border: 1px solid var(--color-gray-300);">
                @error('title')
                    <span class="invalid-feedback" style="display: block; color: var(--color-danger); font-size: 0.85rem; margin-top: 6px;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Grid: Kategori Pilar & Kapasitas -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 24px;">
                <div class="form-group">
                    <label for="pillar" style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--color-dark);">
                        Kategori Materi / Pilar <span style="color: var(--color-danger);">*</span>
                    </label>
                    <select name="pillar" id="pillar" class="form-control @error('pillar') is-invalid @enderror" required style="width: 100%; padding: 12px 16px; border-radius: var(--border-radius-sm); border: 1px solid var(--color-gray-300);">
                        <option value="umum" {{ old('pillar', $zoom_session->pillar) === 'umum' ? 'selected' : '' }}>Umum / Empat Pilar Terpadu</option>
                        <option value="pancasila" {{ old('pillar', $zoom_session->pillar) === 'pancasila' ? 'selected' : '' }}>Pancasila</option>
                        <option value="uud_1945" {{ old('pillar', $zoom_session->pillar) === 'uud_1945' ? 'selected' : '' }}>UUD NRI Tahun 1945</option>
                        <option value="nkri" {{ old('pillar', $zoom_session->pillar) === 'nkri' ? 'selected' : '' }}>Negara Kesatuan Republik Indonesia (NKRI)</option>
                        <option value="bhinneka_tunggal_ika" {{ old('pillar', $zoom_session->pillar) === 'bhinneka_tunggal_ika' ? 'selected' : '' }}>Bhinneka Tunggal Ika</option>
                        <option value="twk_kedinasan" {{ old('pillar', $zoom_session->pillar) === 'twk_kedinasan' ? 'selected' : '' }}>Simulasi TWK Kedinasan</option>
                    </select>
                    @error('pillar')
                        <span class="invalid-feedback" style="display: block; color: var(--color-danger); font-size: 0.85rem; margin-top: 6px;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="capacity" style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--color-dark);">
                        Kapasitas Maksimal Peserta <span style="color: var(--color-danger);">*</span>
                    </label>
                    <div style="position: relative;">
                        <input type="number" name="capacity" id="capacity" min="1" max="5000" class="form-control @error('capacity') is-invalid @enderror" value="{{ old('capacity', $zoom_session->capacity) }}" required style="width: 100%; padding: 12px 16px 12px 42px; border-radius: var(--border-radius-sm); border: 1px solid var(--color-gray-300);">
                        <i class="fi fi-rr-users" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--color-gray-400);"></i>
                    </div>
                    <small style="display: block; color: var(--color-gray-600); font-size: 0.8rem; margin-top: 4px;">
                        Saat ini ada <strong>{{ $zoom_session->participants()->count() }} peserta</strong> yang telah bergabung.
                    </small>
                    @error('capacity')
                        <span class="invalid-feedback" style="display: block; color: var(--color-danger); font-size: 0.85rem; margin-top: 6px;">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Link Zoom & Kredensial -->
            <div class="form-group" style="margin-bottom: 24px;">
                <label for="zoom_link" style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--color-dark);">
                    Tautan / Link URL Zoom Meeting <span style="color: var(--color-danger);">*</span>
                </label>
                <div style="position: relative;">
                    <input type="url" name="zoom_link" id="zoom_link" class="form-control @error('zoom_link') is-invalid @enderror" value="{{ old('zoom_link', $zoom_session->zoom_link) }}" placeholder="https://zoom.us/j/1234567890?pwd=..." required style="width: 100%; padding: 12px 16px 12px 42px; border-radius: var(--border-radius-sm); border: 1px solid var(--color-gray-300);">
                    <i class="fi fi-rr-link" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--color-gray-400);"></i>
                </div>
                @error('zoom_link')
                    <span class="invalid-feedback" style="display: block; color: var(--color-danger); font-size: 0.85rem; margin-top: 6px;">{{ $message }}</span>
                @enderror
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 24px;">
                <div class="form-group">
                    <label for="meeting_id" style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--color-dark);">
                        Meeting ID (ID Rapat) <span style="color: var(--color-gray-500); font-weight: 400; font-size: 0.85rem;">(Opsional)</span>
                    </label>
                    <input type="text" name="meeting_id" id="meeting_id" class="form-control @error('meeting_id') is-invalid @enderror" value="{{ old('meeting_id', $zoom_session->meeting_id) }}" placeholder="Contoh: 823 4567 8901" style="width: 100%; padding: 12px 16px; border-radius: var(--border-radius-sm); border: 1px solid var(--color-gray-300);">
                    @error('meeting_id')
                        <span class="invalid-feedback" style="display: block; color: var(--color-danger); font-size: 0.85rem; margin-top: 6px;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="passcode" style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--color-dark);">
                        Passcode / Kata Sandi Masuk <span style="color: var(--color-gray-500); font-weight: 400; font-size: 0.85rem;">(Opsional)</span>
                    </label>
                    <input type="text" name="passcode" id="passcode" class="form-control @error('passcode') is-invalid @enderror" value="{{ old('passcode', $zoom_session->passcode) }}" placeholder="Contoh: PILAR2026" style="width: 100%; padding: 12px 16px; border-radius: var(--border-radius-sm); border: 1px solid var(--color-gray-300);">
                    @error('passcode')
                        <span class="invalid-feedback" style="display: block; color: var(--color-danger); font-size: 0.85rem; margin-top: 6px;">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Jadwal Waktu -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 24px;">
                <div class="form-group">
                    <label for="start_time" style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--color-dark);">
                        Waktu Mulai <span style="color: var(--color-danger);">*</span>
                    </label>
                    <input type="datetime-local" name="start_time" id="start_time" class="form-control @error('start_time') is-invalid @enderror" value="{{ old('start_time', $zoom_session->start_time ? $zoom_session->start_time->format('Y-m-d\TH:i') : '') }}" required style="width: 100%; padding: 12px 16px; border-radius: var(--border-radius-sm); border: 1px solid var(--color-gray-300);">
                    @error('start_time')
                        <span class="invalid-feedback" style="display: block; color: var(--color-danger); font-size: 0.85rem; margin-top: 6px;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="end_time" style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--color-dark);">
                        Waktu Selesai <span style="color: var(--color-gray-500); font-weight: 400; font-size: 0.85rem;">(Opsional)</span>
                    </label>
                    <input type="datetime-local" name="end_time" id="end_time" class="form-control @error('end_time') is-invalid @enderror" value="{{ old('end_time', $zoom_session->end_time ? $zoom_session->end_time->format('Y-m-d\TH:i') : '') }}" style="width: 100%; padding: 12px 16px; border-radius: var(--border-radius-sm); border: 1px solid var(--color-gray-300);">
                    @error('end_time')
                        <span class="invalid-feedback" style="display: block; color: var(--color-danger); font-size: 0.85rem; margin-top: 6px;">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Deskripsi & Petunjuk -->
            <div class="form-group" style="margin-bottom: 24px;">
                <label for="description" style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--color-dark);">
                    Deskripsi, Narasumber & Petunjuk Rapat
                </label>
                <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror" placeholder="Tuliskan petunjuk pertemuan..." style="width: 100%; padding: 12px 16px; border-radius: var(--border-radius-sm); border: 1px solid var(--color-gray-300); line-height: 1.6;">{{ old('description', $zoom_session->description) }}</textarea>
                @error('description')
                    <span class="invalid-feedback" style="display: block; color: var(--color-danger); font-size: 0.85rem; margin-top: 6px;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Status Aktif Switch -->
            <div style="background-color: var(--color-gray-100); padding: 16px 20px; border-radius: var(--border-radius-sm); margin-bottom: 32px; display: flex; align-items: center; justify-content: space-between;">
                <div>
                    <strong style="color: var(--color-dark); font-size: 0.95rem; display: block;">Status Publikasi Sesi</strong>
                    <span style="font-size: 0.8rem; color: var(--color-gray-600);">Jika aktif, siswa dapat melihat dan mendaftar pada sesi pertemuan ini.</span>
                </div>
                <label style="position: relative; display: inline-block; width: 48px; height: 26px; margin: 0; cursor: pointer;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $zoom_session->is_active) ? 'checked' : '' }} style="opacity: 0; width: 0; height: 0;">
                    <span style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: {{ old('is_active', $zoom_session->is_active) ? 'var(--color-primary)' : '#ccc' }}; transition: .3s; border-radius: 34px;"></span>
                </label>
            </div>

            <!-- Submit Buttons -->
            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <a href="{{ route('admin.zoom-sessions.show', $zoom_session) }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary" style="padding: 12px 24px; font-weight: 600;">
                    <i class="fi fi-rr-disk"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
