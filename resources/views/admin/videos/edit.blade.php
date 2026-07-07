@extends('layouts.admin')

@section('title', 'Edit Materi Video - Admin')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h1>Edit Materi Video</h1>
        <p>Perbarui detail dan media video pembelajaran Empat Pilar Kebangsaan.</p>
    </div>
    <a href="{{ route('admin.videos.index') }}" class="btn btn-secondary">
        ← Kembali
    </a>
</div>

<div class="card" style="max-width: 800px; margin: 0 auto; width: 100%;">
    <div class="card-body">
        <form action="{{ route('admin.videos.update', $video) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 20px;">
                <!-- Title -->
                <div class="form-group">
                    <label for="title">Judul Video</label>
                    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $video->title) }}" placeholder="Masukkan judul video pembelajaran" required>
                    @error('title')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                
                <!-- Pillar -->
                <div class="form-group">
                    <label for="pillar">Pilar Kebangsaan</label>
                    <select name="pillar" id="pillar" class="form-control @error('pillar') is-invalid @enderror" required>
                        <option value="pancasila" {{ old('pillar', $video->pillar) === 'pancasila' ? 'selected' : '' }}>Pancasila</option>
                        <option value="uud_1945" {{ old('pillar', $video->pillar) === 'uud_1945' ? 'selected' : '' }}>UUD NRI 1945</option>
                        <option value="nkri" {{ old('pillar', $video->pillar) === 'nkri' ? 'selected' : '' }}>NKRI</option>
                        <option value="bhinneka_tunggal_ika" {{ old('pillar', $video->pillar) === 'bhinneka_tunggal_ika' ? 'selected' : '' }}>Bhinneka Tunggal Ika</option>
                    </select>
                    @error('pillar')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Video URL -->
            <div class="form-group">
                <label for="video_url">Tautan Video (YouTube)</label>
                <input type="text" name="video_url" id="video_url" class="form-control @error('video_url') is-invalid @enderror" value="{{ old('video_url', $video->video_url) }}" placeholder="Contoh: https://www.youtube.com/watch?v=z44q-rBntSg" required>
                <div style="font-size: 0.8rem; color: var(--color-gray-600); margin-top: 4px;">Dukung tautan YouTube biasa, share link (youtu.be), atau format embed langsung.</div>
                @error('video_url')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- Duration (mapped to read_time in DB) -->
            <div class="form-group" style="width: 250px;">
                <label for="read_time">Estimasi Durasi Video (Menit)</label>
                <input type="number" name="read_time" id="read_time" class="form-control @error('read_time') is-invalid @enderror" value="{{ old('read_time', $video->read_time) }}" min="1" required>
                @error('read_time')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- Content (Description) -->
            <div class="form-group">
                <label for="content">Deskripsi / Catatan Video (Opsional)</label>
                <div style="font-size: 0.8rem; color: var(--color-gray-400); margin-bottom: 4px;">Tuliskan poin penting atau ringkasan penjelasan video ini untuk siswa. HTML diperbolehkan.</div>
                <textarea name="content" id="content" class="form-control @error('content') is-invalid @enderror" style="height: 180px;" placeholder="Tulis catatan atau ringkasan video di sini...">{{ old('content', $video->content) }}</textarea>
                @error('content')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px;">
                <a href="{{ route('admin.videos.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Perbarui Video</button>
            </div>
        </form>
    </div>
</div>
@endsection
