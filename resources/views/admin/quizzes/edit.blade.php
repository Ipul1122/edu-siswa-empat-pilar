@extends('layouts.admin')

@section('title', 'Edit Kuis - Admin')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h1>Edit Kuis</h1>
        <p>Perbarui pengaturan dan informasi kuis evaluasi.</p>
    </div>
    <a href="{{ route('admin.quizzes.index') }}" class="btn btn-secondary">
        ← Kembali
    </a>
</div>

<div class="card" style="max-width: 700px; margin: 0 auto; width: 100%;">
    <div class="card-body">
        <form action="{{ route('admin.quizzes.update', $quiz) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 20px;">
                <!-- Title -->
                <div class="form-group">
                    <label for="title">Judul Kuis</label>
                    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $quiz->title) }}" placeholder="Masukkan judul kuis" required>
                    @error('title')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                
                <!-- Pillar -->
                <div class="form-group">
                    <label for="pillar">Pilar Kebangsaan</label>
                    <select name="pillar" id="pillar" class="form-control @error('pillar') is-invalid @enderror" required>
                        <option value="pancasila" {{ old('pillar', $quiz->pillar) === 'pancasila' ? 'selected' : '' }}>Pancasila</option>
                        <option value="uud_1945" {{ old('pillar', $quiz->pillar) === 'uud_1945' ? 'selected' : '' }}>UUD NRI 1945</option>
                        <option value="nkri" {{ old('pillar', $quiz->pillar) === 'nkri' ? 'selected' : '' }}>NKRI</option>
                        <option value="bhinneka_tunggal_ika" {{ old('pillar', $quiz->pillar) === 'bhinneka_tunggal_ika' ? 'selected' : '' }}>Bhinneka Tunggal Ika</option>
                    </select>
                    @error('pillar')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <!-- Duration -->
                <div class="form-group">
                    <label for="duration_minutes">Durasi Pengerjaan (Menit)</label>
                    <input type="number" name="duration_minutes" id="duration_minutes" class="form-control @error('duration_minutes') is-invalid @enderror" value="{{ old('duration_minutes', $quiz->duration_minutes) }}" min="1" required>
                    @error('duration_minutes')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Type -->
                <div class="form-group">
                    <label for="type">Tipe Kuis</label>
                    <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
                        <option value="practice" {{ old('type', $quiz->type) === 'practice' ? 'selected' : '' }}>Latihan Kuis</option>
                        <option value="real" {{ old('type', $quiz->type) === 'real' ? 'selected' : '' }}>Real Materi</option>
                    </select>
                    @error('type')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div class="form-group">
                <label for="description">Deskripsi / Petunjuk Kuis</label>
                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" style="height: 100px;" placeholder="Tulis deskripsi kuis atau instruksi pengerjaan kuis..." required>{{ old('description', $quiz->description) }}</textarea>
                @error('description')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px;">
                <a href="{{ route('admin.quizzes.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Perbarui Kuis</button>
            </div>
        </form>
    </div>
</div>
@endsection
