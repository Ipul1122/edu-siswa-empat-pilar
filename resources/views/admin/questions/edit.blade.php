@extends('layouts.admin')

@section('title', 'Edit Soal Kuis - Admin')

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
        line-height: 1.6;
    }
    .ql-editor.ql-blank::before {
        font-style: normal;
        color: var(--color-gray-400);
    }
</style>

<div class="page-header">
    <div class="page-title">
        <h1>Edit Soal Kuis</h1>
        <p>Kuis: <strong>{{ $question->quiz->title }}</strong> | Kategori: <span class="badge {{ $question->quiz->pillar }}">{{ $question->quiz->formatted_pillar }}</span></p>
    </div>
    <a href="{{ route('admin.quizzes.show', $question->quiz_id) }}" class="btn btn-secondary">
        ← Batal
    </a>
</div>

<div class="card" style="max-width: 850px; margin: 0 auto; width: 100%;">
    <div class="card-body">
        <form action="{{ route('admin.questions.update', $question) }}" method="POST" id="question-form">
            @csrf
            @method('PUT')
            
            <!-- Question text with Rich Text Toolbar -->
            <div class="form-group" style="margin-bottom: 24px;">
                <label for="editor_question">Pertanyaan / Teks Soal</label>
                <div style="font-size: 0.8rem; color: var(--color-gray-600); margin-bottom: 6px;">
                    Gunakan grup teks untuk memformat narasi soal, studi kasus, pasal rujukan (<strong>Tebal</strong>, <em>Miring</em>, <u>Garis Bawah</u>, List, Heading, dll).
                </div>
                
                <div id="editor_question" style="min-height: 150px;">{!! old('question_text', $question->question_text) !!}</div>
                <input type="hidden" name="question_text" id="question_text" value="{{ old('question_text', $question->question_text) }}">

                @error('question_text')
                    <span class="invalid-feedback" style="display: block; margin-top: 6px;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Options A to E -->
            <h3 style="font-size: 1.1rem; border-bottom: 1px solid var(--color-gray-200); padding-bottom: 8px; margin-bottom: 16px; margin-top: 28px;">Pilihan Jawaban (A-E)</h3>
            
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <div class="form-group">
                    <label for="option_a">Pilihan A</label>
                    <input type="text" name="option_a" id="option_a" class="form-control @error('option_a') is-invalid @enderror" value="{{ old('option_a', $question->option_a) }}" placeholder="Masukkan teks opsi A" required>
                    @error('option_a')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="option_b">Pilihan B</label>
                    <input type="text" name="option_b" id="option_b" class="form-control @error('option_b') is-invalid @enderror" value="{{ old('option_b', $question->option_b) }}" placeholder="Masukkan teks opsi B" required>
                    @error('option_b')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="option_c">Pilihan C</label>
                    <input type="text" name="option_c" id="option_c" class="form-control @error('option_c') is-invalid @enderror" value="{{ old('option_c', $question->option_c) }}" placeholder="Masukkan teks opsi C" required>
                    @error('option_c')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="option_d">Pilihan D</label>
                    <input type="text" name="option_d" id="option_d" class="form-control @error('option_d') is-invalid @enderror" value="{{ old('option_d', $question->option_d) }}" placeholder="Masukkan teks opsi D" required>
                    @error('option_d')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="option_e">Pilihan E</label>
                    <input type="text" name="option_e" id="option_e" class="form-control @error('option_e') is-invalid @enderror" value="{{ old('option_e', $question->option_e) }}" placeholder="Masukkan teks opsi E" required>
                    @error('option_e')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-top: 28px;">
                <!-- Correct Option -->
                <div class="form-group">
                    <label for="correct_option">Kunci Jawaban Benar</label>
                    <select name="correct_option" id="correct_option" class="form-control @error('correct_option') is-invalid @enderror" required>
                        <option value="a" {{ old('correct_option', $question->correct_option) === 'a' ? 'selected' : '' }}>Opsi A</option>
                        <option value="b" {{ old('correct_option', $question->correct_option) === 'b' ? 'selected' : '' }}>Opsi B</option>
                        <option value="c" {{ old('correct_option', $question->correct_option) === 'c' ? 'selected' : '' }}>Opsi C</option>
                        <option value="d" {{ old('correct_option', $question->correct_option) === 'd' ? 'selected' : '' }}>Opsi D</option>
                        <option value="e" {{ old('correct_option', $question->correct_option) === 'e' ? 'selected' : '' }}>Opsi E</option>
                    </select>
                    @error('correct_option')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
                
                <!-- Explanation with Rich Text Toolbar -->
                <div class="form-group">
                    <label for="editor_explanation">Pembahasan Soal (Opsional)</label>
                    <div style="font-size: 0.8rem; color: var(--color-gray-600); margin-bottom: 6px;">
                        Tulis rujukan hukum, analisis konsep, atau pembahasan kunci jawaban untuk ditampilkan kepada siswa saat review.
                    </div>
                    
                    <div id="editor_explanation" style="min-height: 120px;">{!! old('explanation', $question->explanation) !!}</div>
                    <input type="hidden" name="explanation" id="explanation" value="{{ old('explanation', $question->explanation) }}">

                    @error('explanation')
                        <span class="invalid-feedback" style="display: block; margin-top: 6px;">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 28px;">
                <a href="{{ route('admin.quizzes.show', $question->quiz_id) }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Perbarui Soal</button>
            </div>
        </form>
    </div>
</div>

<!-- Quill.js Library -->
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toolbarQuestion = [
            [{ 'header': [3, 4, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            [{ 'color': [] }, { 'background': [] }],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            ['blockquote', 'code-block'],
            ['link'],
            ['clean']
        ];

        const toolbarExplanation = [
            ['bold', 'italic', 'underline'],
            [{ 'color': [] }, { 'background': [] }],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            ['blockquote'],
            ['link'],
            ['clean']
        ];

        const quillQuestion = new Quill('#editor_question', {
            modules: { toolbar: toolbarQuestion },
            theme: 'snow',
            placeholder: 'Tulis teks pertanyaan atau narasi kasus di sini...'
        });

        const quillExplanation = new Quill('#editor_explanation', {
            modules: { toolbar: toolbarExplanation },
            theme: 'snow',
            placeholder: 'Tulis pembahasan atau analisis kunci jawaban di sini...'
        });

        const form = document.getElementById('question-form');
        const questionInput = document.getElementById('question_text');
        const explanationInput = document.getElementById('explanation');

        form.addEventListener('submit', function() {
            // Process question text
            const qHtml = quillQuestion.getSemanticHTML();
            if (quillQuestion.getText().trim().length === 0 && !qHtml.includes('<img')) {
                questionInput.value = '';
            } else {
                questionInput.value = qHtml;
            }

            // Process explanation
            const expHtml = quillExplanation.getSemanticHTML();
            if (quillExplanation.getText().trim().length === 0 && !expHtml.includes('<img')) {
                explanationInput.value = '';
            } else {
                explanationInput.value = expHtml;
            }
        });
    });
</script>
@endsection
