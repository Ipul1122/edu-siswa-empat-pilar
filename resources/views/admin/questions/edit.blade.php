@extends('layouts.admin')

@section('title', 'Edit Soal Kuis - Admin')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h1>Edit Soal Kuis</h1>
        <p>Kuis: <strong>{{ $question->quiz->title }}</strong> | Pilar: <span class="badge {{ $question->quiz->pillar }}">{{ str_replace('_', ' ', $question->quiz->pillar) }}</span></p>
    </div>
    <a href="{{ route('admin.quizzes.show', $question->quiz_id) }}" class="btn btn-secondary">
        ← Batal
    </a>
</div>

<div class="card" style="max-width: 800px; margin: 0 auto; width: 100%;">
    <div class="card-body">
        <form action="{{ route('admin.questions.update', $question) }}" method="POST">
            @csrf
            @method('PUT')
            
            <!-- Question text -->
            <div class="form-group">
                <label for="question_text">Pertanyaan</label>
                <textarea name="question_text" id="question_text" class="form-control @error('question_text') is-invalid @enderror" style="height: 120px;" placeholder="Masukkan teks pertanyaan kuis..." required>{{ old('question_text', $question->question_text) }}</textarea>
                @error('question_text')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- Options A to E -->
            <h3 style="font-size: 1.1rem; border-bottom: 1px solid var(--color-gray-200); padding-bottom: 8px; margin-bottom: 16px; margin-top: 24px;">Pilihan Jawaban (A-E)</h3>
            
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

            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-top: 24px;">
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
                
                <!-- Explanation -->
                <div class="form-group">
                    <label for="explanation">Pembahasan Soal (Opsional)</label>
                    <textarea name="explanation" id="explanation" class="form-control @error('explanation') is-invalid @enderror" style="height: 100px;" placeholder="Tulis alasan jawaban benar atau rujukan materi..." >{{ old('explanation', $question->explanation) }}</textarea>
                    @error('explanation')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px;">
                <a href="{{ route('admin.quizzes.show', $question->quiz_id) }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Perbarui Soal</button>
            </div>
        </form>
    </div>
</div>
@endsection
