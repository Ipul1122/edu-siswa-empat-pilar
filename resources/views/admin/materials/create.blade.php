@extends('layouts.admin')

@section('title', 'Tambah Materi Baru - Admin')

@section('content')
<!-- Quill.js Rich Text Editor CDN Stylesheet -->
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet" />

<style>
    .ql-toolbar.ql-snow {
        border-top-left-radius: var(--border-radius-sm);
        border-top-right-radius: var(--border-radius-sm);
        border-color: var(--color-gray-300);
        background-color: var(--color-gray-100);
        padding: 10px 12px;
    }
    .ql-container.ql-snow {
        border-bottom-left-radius: var(--border-radius-sm);
        border-bottom-right-radius: var(--border-radius-sm);
        border-color: var(--color-gray-300);
        font-family: var(--font-body);
        font-size: 1rem;
        min-height: 320px;
        background-color: var(--color-white);
    }
    .ql-editor {
        min-height: 320px;
        line-height: 1.7;
    }
    .ql-editor.ql-blank::before {
        font-style: normal;
        color: var(--color-gray-400);
    }
    .ql-snow .ql-picker.ql-header {
        width: 140px;
    }
</style>

<div class="page-header">
    <div class="page-title">
        <h1>Tambah Materi Baru</h1>
        <p>Tulis artikel belajar mengenai kewarganegaraan Empat Pilar Kebangsaan menggunakan editor teks kaya.</p>
    </div>
    <a href="{{ route('admin.materials.index') }}" class="btn btn-secondary">
        ← Kembali
    </a>
</div>

<div class="card" style="max-width: 900px; margin: 0 auto; width: 100%;">
    <div class="card-body">
        <form action="{{ route('admin.materials.store') }}" method="POST" id="material-form">
            @csrf
            
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px;">
                <!-- Title -->
                <div class="form-group">
                    <label for="title">Judul Materi</label>
                    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="Masukkan judul materi" required>
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

            <!-- Read time -->
            <div class="form-group" style="width: 250px;">
                <label for="read_time">Estimasi Waktu Baca (Menit)</label>
                <input type="number" name="read_time" id="read_time" class="form-control @error('read_time') is-invalid @enderror" value="{{ old('read_time', 5) }}" min="1" required>
                @error('read_time')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- Rich Text Content Editor -->
            <div class="form-group">
                <label for="editor">Konten Materi</label>
                <div style="font-size: 0.85rem; color: var(--color-gray-600); margin-bottom: 8px;">
                    Gunakan grup teks di toolbar bawah ini untuk memformat teks (Heading H2/H3, <strong>Tebal</strong>, <em>Miring</em>, <u>Garis Bawah</u>, Link tautan, List, Kutipan, dll).
                </div>
                
                <div id="editor">{!! old('content') !!}</div>
                <input type="hidden" name="content" id="content" value="{{ old('content') }}">

                @error('content')
                    <span class="invalid-feedback" style="display: block; margin-top: 6px;">{{ $message }}</span>
                @enderror
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 28px;">
                <a href="{{ route('admin.materials.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Materi</button>
            </div>
        </form>
    </div>
</div>

<!-- Quill.js Library -->
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toolbarOptions = [
            [{ 'header': [2, 3, 4, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            [{ 'color': [] }, { 'background': [] }],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            ['blockquote', 'code-block'],
            [{ 'align': [] }],
            ['link'],
            ['clean']
        ];

        const quill = new Quill('#editor', {
            modules: {
                toolbar: toolbarOptions
            },
            theme: 'snow',
            placeholder: 'Tulis isi materi pembelajaran di sini. Anda dapat memformat teks secara langsung...'
        });

        const form = document.getElementById('material-form');
        const contentInput = document.getElementById('content');

        form.addEventListener('submit', function(e) {
            // Check if editor is empty
            const htmlContent = quill.getSemanticHTML();
            if (quill.getText().trim().length === 0 && !htmlContent.includes('<img')) {
                contentInput.value = '';
            } else {
                contentInput.value = htmlContent;
            }
        });
    });
</script>
@endsection
