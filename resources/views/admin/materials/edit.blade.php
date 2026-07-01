@extends('layouts.admin')

@section('title', 'Edit Materi - Admin')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h1>Edit Materi</h1>
        <p>Perbarui artikel belajar mengenai kewarganegaraan Empat Pilar Kebangsaan.</p>
    </div>
    <a href="{{ route('admin.materials.index') }}" class="btn btn-secondary">
        ← Kembali
    </a>
</div>

<div class="card" style="max-width: 800px; margin: 0 auto; width: 100%;">
    <div class="card-body">
        <form action="{{ route('admin.materials.update', $material) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 20px;">
                <!-- Title -->
                <div class="form-group">
                    <label for="title">Judul Materi</label>
                    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $material->title) }}" placeholder="Masukkan judul materi" required>
                    @error('title')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                
                <!-- Pillar -->
                <div class="form-group">
                    <label for="pillar">Pilar Kebangsaan</label>
                    <select name="pillar" id="pillar" class="form-control @error('pillar') is-invalid @enderror" required>
                        <option value="pancasila" {{ old('pillar', $material->pillar) === 'pancasila' ? 'selected' : '' }}>Pancasila</option>
                        <option value="uud_1945" {{ old('pillar', $material->pillar) === 'uud_1945' ? 'selected' : '' }}>UUD NRI 1945</option>
                        <option value="nkri" {{ old('pillar', $material->pillar) === 'nkri' ? 'selected' : '' }}>NKRI</option>
                        <option value="bhinneka_tunggal_ika" {{ old('pillar', $material->pillar) === 'bhinneka_tunggal_ika' ? 'selected' : '' }}>Bhinneka Tunggal Ika</option>
                    </select>
                    @error('pillar')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Read time -->
            <div class="form-group" style="width: 250px;">
                <label for="read_time">Estimasi Waktu Baca (Menit)</label>
                <input type="number" name="read_time" id="read_time" class="form-control @error('read_time') is-invalid @enderror" value="{{ old('read_time', $material->read_time) }}" min="1" required>
                @error('read_time')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- Content -->
            <div class="form-group">
                <label for="content">Konten Materi</label>
                <div style="font-size: 0.8rem; color: var(--color-gray-400); margin-bottom: 4px;">Tips: Anda dapat menulis menggunakan kode HTML dasar seperti &lt;h3&gt;, &lt;p&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;strong&gt; untuk merapikan tampilan bacaan siswa.</div>
                <textarea name="content" id="content" class="form-control @error('content') is-invalid @enderror" style="height: 350px;" placeholder="Tulis konten pembelajaran di sini..." required>{{ old('content', $material->content) }}</textarea>
                @error('content')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px;">
                <a href="{{ route('admin.materials.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Perbarui Materi</button>
            </div>
        </form>
    </div>
</div>
@endsection
