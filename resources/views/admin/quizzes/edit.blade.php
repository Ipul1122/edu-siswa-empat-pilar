@extends('layouts.admin')

@section('title', 'Edit Latihan Kuis - Admin')

@section('content')
<!-- Quill.js Rich Text Editor CDN Stylesheet -->
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet" />

<style>
    .ql-toolbar.ql-snow {
        border-top-left-radius: var(--border-radius-sm);
        border-top-right-radius: var(--border-radius-sm);
        border-color: var(--color-gray-300);
        background-color: var(--color-gray-100);
        padding: 8px 10px;
    }
    .ql-container.ql-snow {
        border-bottom-left-radius: var(--border-radius-sm);
        border-bottom-right-radius: var(--border-radius-sm);
        border-color: var(--color-gray-300);
        font-family: var(--font-body);
        font-size: 0.95rem;
        background-color: var(--color-white);
    }
    .ql-editor {
        min-height: 160px;
        line-height: 1.6;
    }
    .ql-editor.ql-blank::before {
        font-style: normal;
        color: var(--color-gray-400);
    }
</style>

<div class="page-header">
    <div class="page-title">
        <h1>Edit Latihan Kuis</h1>
        <p>Perbarui informasi latihan kuis evaluasi.</p>
    </div>
    <a href="{{ route('admin.quizzes.index') }}" class="btn btn-secondary">
        ← Kembali
    </a>
</div>

<div class="card" style="max-width: 800px; margin: 0 auto; width: 100%;">
    <div class="card-body">
        <form action="{{ route('admin.quizzes.update', $quiz) }}" method="POST" id="quiz-form">
            @csrf
            @method('PUT')
            
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px;">
                <!-- Title -->
                <div class="form-group">
                    <label for="title">Judul Latihan Kuis</label>
                    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $quiz->title) }}" placeholder="Masukkan judul latihan kuis" required>
                    @error('title')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                
                <!-- Pillar -->
                <div class="form-group">
                    <label for="pillar">Kategori / Pilar</label>
                    <select name="pillar" id="pillar" class="form-control @error('pillar') is-invalid @enderror" required>
                        <option value="pancasila" {{ old('pillar', $quiz->pillar) === 'pancasila' ? 'selected' : '' }}>Pancasila</option>
                        <option value="uud_1945" {{ old('pillar', $quiz->pillar) === 'uud_1945' ? 'selected' : '' }}>UUD NRI 1945</option>
                        <option value="nkri" {{ old('pillar', $quiz->pillar) === 'nkri' ? 'selected' : '' }}>NKRI</option>
                        <option value="bhinneka_tunggal_ika" {{ old('pillar', $quiz->pillar) === 'bhinneka_tunggal_ika' ? 'selected' : '' }}>Bhinneka Tunggal Ika</option>
                        <option value="twk_kedinasan" {{ old('pillar', $quiz->pillar) === 'twk_kedinasan' ? 'selected' : '' }}>Simulasi TWK Kedinasan</option>
                    </select>
                    @error('pillar')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Duration -->
            <div class="form-group" style="width: 250px;">
                <label for="duration_minutes">Durasi Pengerjaan (Menit)</label>
                <input type="number" name="duration_minutes" id="duration_minutes" class="form-control @error('duration_minutes') is-invalid @enderror" value="{{ old('duration_minutes', $quiz->duration_minutes) }}" min="1" required>
                @error('duration_minutes')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- Description with Rich Text Toolbar -->
            <div class="form-group">
                <label for="editor_description">Deskripsi / Petunjuk Latihan Kuis</label>
                <div style="font-size: 0.8rem; color: var(--color-gray-600); margin-bottom: 6px;">
                    Gunakan grup teks untuk memformat petunjuk pengerjaan (<strong>Tebal</strong>, <em>Miring</em>, <u>Garis Bawah</u>, List, Heading, Link, dll).
                </div>
                
                <div id="editor_description">{!! old('description', $quiz->description) !!}</div>
                <input type="hidden" name="description" id="description" value="{{ old('description', $quiz->description) }}">

                @error('description')
                    <span class="invalid-feedback" style="display: block; margin-top: 6px;">{{ $message }}</span>
                @enderror
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 28px;">
                <a href="{{ route('admin.quizzes.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Quill.js Library -->
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toolbarOptions = [
            [{ 'header': [3, 4, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            [{ 'color': [] }, { 'background': [] }],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            ['blockquote'],
            ['link'],
            ['clean']
        ];

        const quill = new Quill('#editor_description', {
            modules: { toolbar: toolbarOptions },
            theme: 'snow',
            placeholder: 'Tulis deskripsi kuis atau instruksi pengerjaan latihan kuis di sini...'
        });

        const form = document.getElementById('quiz-form');
        const descriptionInput = document.getElementById('description');

        form.addEventListener('submit', function() {
            const htmlContent = quill.getSemanticHTML();
            if (quill.getText().trim().length === 0 && !htmlContent.includes('<img')) {
                descriptionInput.value = '';
            } else {
                descriptionInput.value = htmlContent;
            }
        });
    });
</script>
@endsection
