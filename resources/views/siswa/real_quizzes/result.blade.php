@extends('layouts.siswa')

@section('title', 'Hasil Real Materi - Empat Pilar')

@section('content')
<div class="page-header" style="border: none; padding-bottom: 0;">
    <a href="{{ route('siswa.real-materi.index') }}" class="btn btn-secondary btn-sm">
        ← Kembali ke Real Materi
    </a>
</div>

<div class="quiz-wrapper">
    <!-- Score Summary Card -->
    <div class="card" style="box-shadow: var(--shadow-md); overflow: hidden; border-radius: var(--border-radius-lg);">
        <div class="card-body" style="padding: 40px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 32px; background: radial-gradient(circle at 10% 20%, rgba(211, 47, 47, 0.04) 0%, rgba(255, 193, 7, 0.02) 90%);">
            <div style="flex: 1; min-width: 250px;">
                <span class="badge {{ $attempt->quiz->pillar }}" style="margin-bottom: 8px;">
                    Hasil Real Materi: {{ $attempt->quiz->formatted_pillar }}
                </span>
                <h2 style="font-size: 1.75rem; color: var(--color-dark); font-family: var(--font-heading); margin-bottom: 12px;">{{ $attempt->quiz->title }}</h2>
                
                <div style="display: flex; flex-direction: column; gap: 12px; margin-top: 16px;">
                    <div style="display: flex; align-items: center; gap: 8px; font-size: 0.95rem; color: var(--color-gray-600); font-weight: 500;">
                        <i class="fi fi-rr-checkbox" style="font-size: 1rem; line-height: 1; color: var(--color-gray-600);"></i> Jumlah Jawaban Benar: <strong style="color: var(--color-dark);">{{ $attempt->correct_answers }} / {{ $attempt->total_questions }} Soal</strong>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px; font-size: 0.95rem; color: var(--color-gray-600); font-weight: 500;">
                        <i class="fi fi-rr-clock" style="font-size: 1rem; line-height: 1; color: var(--color-gray-600);"></i> Waktu Pengerjaan: <strong style="color: var(--color-dark);">{{ sprintf('%02d:%02d', floor($attempt->duration_seconds_taken / 60), $attempt->duration_seconds_taken % 60) }}</strong>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px; font-size: 0.95rem; color: var(--color-gray-600); font-weight: 500;">
                        <i class="fi fi-rr-calendar" style="font-size: 1rem; line-height: 1; color: var(--color-gray-600);"></i> Tanggal Ujian: <strong style="color: var(--color-dark);">{{ $attempt->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</strong>
                    </div>
                </div>
            </div>
            
            <!-- Big Score Indicator -->
            <div style="text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; width: 140px; height: 140px; border-radius: 50%; border: 8px solid {{ $attempt->score >= 70 ? 'var(--color-success)' : 'var(--color-danger)' }}; background-color: var(--color-white); box-shadow: var(--shadow-sm);">
                <div style="font-family: var(--font-heading); font-size: 2.5rem; font-weight: 800; color: {{ $attempt->score >= 70 ? 'var(--color-success)' : 'var(--color-danger)' }}; line-height: 1;">
                    {{ $attempt->score }}
                </div>
                <div style="font-size: 0.65rem; color: var(--color-gray-400); font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 4px;">
                    Skor Akhir
                </div>
            </div>
        </div>
    </div>

    <!-- Review Answers Title -->
    <h3 style="font-size: 1.35rem; margin-top: 16px; margin-bottom: 8px; color: var(--color-dark); border-bottom: 2px solid var(--color-gray-200); padding-bottom: 12px;">Tinjauan Soal & Pembahasan</h3>

    <!-- Question list review -->
    <div style="display: flex; flex-direction: column; gap: 32px;">
        @foreach($attempt->quiz->questions as $index => $question)
            @php
                $studentChoice = $studentAnswers[$question->id] ?? null;
                $isCorrect = $studentChoice && strtolower($studentChoice) === strtolower($question->correct_option);
            @endphp
            <div class="question-card" style="border: 1px solid var(--color-gray-200); {{ $isCorrect ? 'border-left: 5px solid var(--color-success);' : 'border-left: 5px solid var(--color-danger);' }}">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span class="question-number" style="color: var(--color-gray-600);">Soal {{ $index + 1 }} dari {{ $attempt->total_questions }}</span>
                    @if($isCorrect)
                        <span class="badge completed" style="background-color: rgba(16, 185, 129, 0.15); color: var(--color-success); font-weight: 700; font-size: 0.75rem;">✓ Jawaban Benar</span>
                    @else
                        <span class="badge completed" style="background-color: rgba(239, 68, 68, 0.15); color: var(--color-danger); font-weight: 700; font-size: 0.75rem;">✗ Jawaban Salah</span>
                    @endif
                </div>

                <div class="question-text">
                    {!! nl2br(e($question->question_text)) !!}
                </div>

                <div class="options-list">
                    <!-- Option A -->
                    <div class="option-item 
                        {{ $question->correct_option === 'a' ? 'correct' : '' }} 
                        {{ ($studentChoice === 'a' && $question->correct_option !== 'a') ? 'incorrect' : '' }}">
                        <label style="cursor: default;">
                            <input type="radio" disabled {{ $studentChoice === 'a' ? 'checked' : '' }} style="cursor: default;">
                            <span>
                                <strong>A.</strong> {{ $question->option_a }}
                                @if($studentChoice === 'a') <strong style="font-size: 0.8rem;">(Jawaban Anda)</strong> @endif
                                @if($question->correct_option === 'a') <strong style="font-size: 0.8rem; color: var(--color-success);"> (Kunci Jawaban)</strong> @endif
                            </span>
                        </label>
                    </div>

                    <!-- Option B -->
                    <div class="option-item 
                        {{ $question->correct_option === 'b' ? 'correct' : '' }} 
                        {{ ($studentChoice === 'b' && $question->correct_option !== 'b') ? 'incorrect' : '' }}">
                        <label style="cursor: default;">
                            <input type="radio" disabled {{ $studentChoice === 'b' ? 'checked' : '' }} style="cursor: default;">
                            <span>
                                <strong>B.</strong> {{ $question->option_b }}
                                @if($studentChoice === 'b') <strong style="font-size: 0.8rem;">(Jawaban Anda)</strong> @endif
                                @if($question->correct_option === 'b') <strong style="font-size: 0.8rem; color: var(--color-success);"> (Kunci Jawaban)</strong> @endif
                            </span>
                        </label>
                    </div>

                    <!-- Option C -->
                    <div class="option-item 
                        {{ $question->correct_option === 'c' ? 'correct' : '' }} 
                        {{ ($studentChoice === 'c' && $question->correct_option !== 'c') ? 'incorrect' : '' }}">
                        <label style="cursor: default;">
                            <input type="radio" disabled {{ $studentChoice === 'c' ? 'checked' : '' }} style="cursor: default;">
                            <span>
                                <strong>C.</strong> {{ $question->option_c }}
                                @if($studentChoice === 'c') <strong style="font-size: 0.8rem;">(Jawaban Anda)</strong> @endif
                                @if($question->correct_option === 'c') <strong style="font-size: 0.8rem; color: var(--color-success);"> (Kunci Jawaban)</strong> @endif
                            </span>
                        </label>
                    </div>

                    <!-- Option D -->
                    <div class="option-item 
                        {{ $question->correct_option === 'd' ? 'correct' : '' }} 
                        {{ ($studentChoice === 'd' && $question->correct_option !== 'd') ? 'incorrect' : '' }}">
                        <label style="cursor: default;">
                            <input type="radio" disabled {{ $studentChoice === 'd' ? 'checked' : '' }} style="cursor: default;">
                            <span>
                                <strong>D.</strong> {{ $question->option_d }}
                                @if($studentChoice === 'd') <strong style="font-size: 0.8rem;">(Jawaban Anda)</strong> @endif
                                @if($question->correct_option === 'd') <strong style="font-size: 0.8rem; color: var(--color-success);"> (Kunci Jawaban)</strong> @endif
                            </span>
                        </label>
                    </div>

                    <!-- Option E -->
                    <div class="option-item 
                        {{ $question->correct_option === 'e' ? 'correct' : '' }} 
                        {{ ($studentChoice === 'e' && $question->correct_option !== 'e') ? 'incorrect' : '' }}">
                        <label style="cursor: default;">
                            <input type="radio" disabled {{ $studentChoice === 'e' ? 'checked' : '' }} style="cursor: default;">
                            <span>
                                <strong>E.</strong> {{ $question->option_e }}
                                @if($studentChoice === 'e') <strong style="font-size: 0.8rem;">(Jawaban Anda)</strong> @endif
                                @if($question->correct_option === 'e') <strong style="font-size: 0.8rem; color: var(--color-success);"> (Kunci Jawaban)</strong> @endif
                            </span>
                        </label>
                    </div>
                </div>

                @if($question->explanation)
                    <div class="explanation-box" style="font-size: 0.9rem;">
                        <strong>Pembahasan Soal:</strong><br>
                        {!! nl2br(e($question->explanation)) !!}
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Bottom Actions -->
    <div style="margin-top: 40px; display: flex; justify-content: center; gap: 16px;">
        <a href="{{ route('siswa.real-materi.index') }}" class="btn btn-secondary">
            Kembali ke Real Materi
        </a>
        <a href="{{ route('siswa.dashboard') }}" class="btn btn-primary">
            Kembali ke Dashboard
        </a>
    </div>
</div>
@endsection
