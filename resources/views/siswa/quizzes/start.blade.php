@extends('layouts.siswa')

@section('title', 'Mengerjakan Kuis - Empat Pilar')

@section('content')
<div class="quiz-wrapper">
    <!-- Sticky Timer Header -->
    <div class="quiz-timer-bar">
        <div>
            <h3 style="font-size: 1.05rem; font-family: var(--font-heading); color: var(--color-white);">{{ $quiz->title }}</h3>
            <p style="font-size: 0.75rem; color: var(--color-gray-300);">Pilar: {{ $quiz->formatted_pillar }}</p>
        </div>
        <div style="text-align: right;">
            <div style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--color-gray-300); font-weight: 600;">Sisa Waktu</div>
            <div class="timer-display" id="quiz-timer" data-duration="{{ $quiz->duration_minutes }}">
                --:--
            </div>
        </div>
    </div>

    <!-- Questions Form -->
    <form action="{{ route('siswa.quizzes.submit', $quiz) }}" method="POST" id="quiz-form">
        @csrf
        <!-- Field to store seconds taken -->
        <input type="hidden" name="duration_seconds_taken" id="duration_seconds_taken" value="0">

        <div style="display: flex; flex-direction: column; gap: 32px;">
            @foreach($quiz->questions as $index => $question)
                <div class="question-card" id="q-{{ $question->id }}">
                    <div class="question-number">Soal {{ $index + 1 }} dari {{ $quiz->questions->count() }}</div>
                    
                    <div class="question-text">
                        {!! str_contains($question->question_text, '<') ? $question->question_text : nl2br(e($question->question_text)) !!}
                    </div>

                    <div class="options-list">
                        <!-- Option A -->
                        <div class="option-item">
                            <label>
                                <input type="radio" name="answers[{{ $question->id }}]" value="a" required>
                                <span><strong>A.</strong> {{ $question->option_a }}</span>
                            </label>
                        </div>
                        
                        <!-- Option B -->
                        <div class="option-item">
                            <label>
                                <input type="radio" name="answers[{{ $question->id }}]" value="b">
                                <span><strong>B.</strong> {{ $question->option_b }}</span>
                            </label>
                        </div>

                        <!-- Option C -->
                        <div class="option-item">
                            <label>
                                <input type="radio" name="answers[{{ $question->id }}]" value="c">
                                <span><strong>C.</strong> {{ $question->option_c }}</span>
                            </label>
                        </div>

                        <!-- Option D -->
                        <div class="option-item">
                            <label>
                                <input type="radio" name="answers[{{ $question->id }}]" value="d">
                                <span><strong>D.</strong> {{ $question->option_d }}</span>
                            </label>
                        </div>

                        <!-- Option E -->
                        <div class="option-item">
                            <label>
                                <input type="radio" name="answers[{{ $question->id }}]" value="e">
                                <span><strong>E.</strong> {{ $question->option_e }}</span>
                            </label>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Submit Panel -->
        <div style="margin-top: 40px; display: flex; justify-content: space-between; align-items: center; background-color: var(--color-white); padding: 24px; border-radius: var(--border-radius-md); box-shadow: var(--shadow-sm); border: 1px solid var(--color-gray-200);">
            <div style="font-size: 0.9rem; color: var(--color-gray-600); font-weight: 500;">
                Harap periksa kembali semua jawaban sebelum mengirim.
            </div>
            <button type="submit" class="btn btn-primary" style="background-color: var(--color-success); border: none; padding: 12px 28px; font-size: 1rem;">
                💾 Selesai
            </button>
        </div>
    </form>
</div>
@endsection
