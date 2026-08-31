@extends('layouts.admin')

@section('title', 'Tambah Materi Video - Admin')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h1>Tambah Materi Video Baru</h1>
        <p>Unggah file video MP4 atau tautkan video YouTube pembelajaran Empat Pilar Kebangsaan.</p>
    </div>
    <a href="{{ route('admin.videos.index') }}" class="btn btn-secondary">
        ← Kembali
    </a>
</div>

<div class="card" style="max-width: 800px; margin: 0 auto; width: 100%;">
    <div class="card-body">
        <form action="{{ route('admin.videos.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px;">
                <!-- Title -->
                <div class="form-group">
                    <label for="title">Judul Video</label>
                    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="Masukkan judul video pembelajaran" required>
                    @error('title')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                
                <!-- Pillar -->
                <div class="form-group">
                    <label for="pillar">Kategori / Pilar</label>
                    <select name="pillar" id="pillar" class="form-control @error('pillar') is-invalid @enderror" required>
                        <option value="" disabled selected>-- Pilih Kategori --</option>
                        <option value="pancasila" {{ old('pillar') === 'pancasila' ? 'selected' : '' }}>Pancasila</option>
                        <option value="uud_1945" {{ old('pillar') === 'uud_1945' ? 'selected' : '' }}>UUD NRI 1945</option>
                        <option value="nkri" {{ old('pillar') === 'nkri' ? 'selected' : '' }}>NKRI</option>
                        <option value="bhinneka_tunggal_ika" {{ old('pillar') === 'bhinneka_tunggal_ika' ? 'selected' : '' }}>Bhinneka Tunggal Ika</option>
                        <option value="twk_kedinasan" {{ old('pillar') === 'twk_kedinasan' ? 'selected' : '' }}>Simulasi TWK Kedinasan</option>
                    </select>
                    @error('pillar')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Video Source Selection -->
            <div class="form-group" style="margin-bottom: 20px;">
                <label style="font-weight: 700; margin-bottom: 8px; display: block;">Sumber Media Video</label>
                <div style="display: flex; gap: 16px; margin-bottom: 12px;">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; padding: 10px 16px; border: 1px solid var(--color-gray-300); border-radius: var(--border-radius-sm); background: var(--color-gray-100);" id="label_source_upload">
                        <input type="radio" name="video_source_type" value="upload" id="source_upload" {{ old('video_source_type', 'upload') === 'upload' ? 'checked' : '' }} onchange="toggleVideoSource()">
                        <span><i class="fi fi-rr-upload" style="margin-right: 4px; vertical-align: middle;"></i> Unggah File Video (MP4 / WebM)</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; padding: 10px 16px; border: 1px solid var(--color-gray-300); border-radius: var(--border-radius-sm); background: var(--color-gray-100);" id="label_source_url">
                        <input type="radio" name="video_source_type" value="url" id="source_url" {{ old('video_source_type') === 'url' ? 'checked' : '' }} onchange="toggleVideoSource()">
                        <span><i class="fi fi-rr-link-alt" style="margin-right: 4px; vertical-align: middle;"></i> Tautan / Link Video (YouTube)</span>
                    </label>
                </div>
            </div>

            <!-- Upload File Container -->
            <div id="container_upload" style="display: none;" class="form-group">
                <label for="video_file">Pilih File Video MP4 / WebM</label>
                <input type="file" name="video_file" id="video_file" class="form-control @error('video_file') is-invalid @enderror" accept="video/mp4,video/webm,video/ogg,video/quicktime">
                <div style="font-size: 0.8rem; color: var(--color-gray-600); margin-top: 4px;">
                    Format yang didukung: <strong>MP4, WebM, MOV, OGG</strong> (Maksimal 100MB).
                </div>
                @error('video_file')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- Video URL Container -->
            <div id="container_url" style="display: none;" class="form-group">
                <label for="video_url">Tautan / URL Video (YouTube / Link Luar)</label>
                <input type="text" name="video_url" id="video_url" class="form-control @error('video_url') is-invalid @enderror" value="{{ old('video_url') }}" placeholder="Contoh: https://www.youtube.com/watch?v=XaMRSuZSt0E">
                <div style="font-size: 0.8rem; color: var(--color-gray-600); margin-top: 4px;">
                    Dukung tautan YouTube biasa, share link (youtu.be), atau format embed langsung.
                </div>
                @error('video_url')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- Duration -->
            <div class="form-group" style="width: 250px;">
                <label for="read_time">Estimasi Durasi Video (Menit)</label>
                <input type="number" name="read_time" id="read_time" class="form-control @error('read_time') is-invalid @enderror" value="{{ old('read_time', 5) }}" min="1" required>
                @error('read_time')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- Description -->
            <div class="form-group">
                <label for="content">Deskripsi / Catatan Video (Opsional)</label>
                <div style="font-size: 0.8rem; color: var(--color-gray-400); margin-bottom: 4px;">Tuliskan poin penting atau ringkasan penjelasan video ini untuk siswa. HTML diperbolehkan.</div>
                <textarea name="content" id="content" class="form-control @error('content') is-invalid @enderror" style="height: 140px;" placeholder="Tulis catatan atau ringkasan video di sini...">{{ old('content') }}</textarea>
                @error('content')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px;">
                <a href="{{ route('admin.videos.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Video</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleVideoSource() {
        const isUpload = document.getElementById('source_upload').checked;
        const uploadBox = document.getElementById('container_upload');
        const urlBox = document.getElementById('container_url');
        const labelUpload = document.getElementById('label_source_upload');
        const labelUrl = document.getElementById('label_source_url');

        if (isUpload) {
            uploadBox.style.display = 'block';
            urlBox.style.display = 'none';
            labelUpload.style.borderColor = 'rgb(var(--color-primary-rgb))';
            labelUpload.style.backgroundColor = 'rgba(var(--color-primary-rgb), 0.08)';
            labelUrl.style.borderColor = 'var(--color-gray-300)';
            labelUrl.style.backgroundColor = 'var(--color-gray-100)';
        } else {
            uploadBox.style.display = 'none';
            urlBox.style.display = 'block';
            labelUrl.style.borderColor = 'rgb(var(--color-primary-rgb))';
            labelUrl.style.backgroundColor = 'rgba(var(--color-primary-rgb), 0.08)';
            labelUpload.style.borderColor = 'var(--color-gray-300)';
            labelUpload.style.backgroundColor = 'var(--color-gray-100)';
        }
    }

    document.addEventListener('DOMContentLoaded', toggleVideoSource);
</script>
@endsection
