@extends('layouts.siswa')

@section('title', 'Hasil Kuis - Empat Pilar')

@section('content')
<div class="page-header" style="border: none; padding-bottom: 0;">
    <a href="{{ route('siswa.quizzes.index') }}" class="btn btn-secondary btn-sm">
        ← Kembali ke Daftar Kuis
    </a>
</div>

<div class="quiz-wrapper">
    <!-- Score Summary Card -->
    <div class="card" style="box-shadow: var(--shadow-md); overflow: hidden; border-radius: var(--border-radius-lg);">
        <div class="card-body" style="padding: 36px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 28px; background: radial-gradient(circle at 10% 20%, rgba(211, 47, 47, 0.04) 0%, rgba(255, 193, 7, 0.02) 90%);">
            <div style="flex: 1; min-width: 250px;">
                <div style="display: flex; gap: 8px; align-items: center; margin-bottom: 8px; flex-wrap: wrap;">
                    <span class="badge {{ $attempt->quiz->pillar }}">
                        {{ $attempt->quiz->formatted_pillar }}
                    </span>
                    @if($attempt->quiz->pillar === 'twk_kedinasan')
                        @if($attempt->score >= 65)
                            <span class="badge completed" style="background-color: rgba(16, 185, 129, 0.15); color: var(--color-success); font-weight: 700;">
                                ✓ LULUS PASSING GRADE SKD TWK (Min. 65)
                            </span>
                        @else
                            <span class="badge pending" style="background-color: rgba(239, 68, 68, 0.15); color: var(--color-danger); font-weight: 700;">
                                ✗ BELUM MEMENUHI PASSING GRADE SKD (Min. 65)
                            </span>
                        @endif
                    @endif
                </div>
                <h2 style="font-size: 1.75rem; color: var(--color-dark); font-family: var(--font-heading); margin-bottom: 12px;">{{ $attempt->quiz->title }}</h2>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; margin-top: 16px;">
                    <div style="display: flex; align-items: center; gap: 8px; font-size: 0.9rem; color: var(--color-gray-600); font-weight: 500;">
                        <i class="fi fi-rr-checkbox" style="font-size: 1rem; line-height: 1; color: var(--color-success);"></i> Jawaban Benar: <strong style="color: var(--color-dark);">{{ $attempt->correct_answers }} / {{ $attempt->total_questions }} Soal</strong>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px; font-size: 0.9rem; color: var(--color-gray-600); font-weight: 500;">
                        <i class="fi fi-rr-cross-circle" style="font-size: 1rem; line-height: 1; color: var(--color-danger);"></i> Jawaban Salah: <strong style="color: var(--color-dark);">{{ $attempt->total_questions - $attempt->correct_answers }} Soal</strong>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px; font-size: 0.9rem; color: var(--color-gray-600); font-weight: 500;">
                        <i class="fi fi-rr-clock" style="font-size: 1rem; line-height: 1; color: var(--color-primary);"></i> Waktu: <strong style="color: var(--color-dark);">{{ sprintf('%02d:%02d', floor($attempt->duration_seconds_taken / 60), $attempt->duration_seconds_taken % 60) }}</strong>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px; font-size: 0.9rem; color: var(--color-gray-600); font-weight: 500;">
                        <i class="fi fi-rr-calendar" style="font-size: 1rem; line-height: 1; color: var(--color-gray-600);"></i> Waktu Ujian: <strong style="color: var(--color-dark);">{{ $attempt->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</strong>
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
    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 24px; margin-bottom: 12px; border-bottom: 2px solid var(--color-gray-200); padding-bottom: 12px; flex-wrap: wrap; gap: 12px;">
        <h3 style="font-size: 1.35rem; color: var(--color-dark); margin: 0;">
            <i class="fi fi-rr-memo-circle-check" style="margin-right: 6px; color: rgb(var(--color-primary-rgb));"></i> Tinjauan Soal & Pembahasan Analisis
        </h3>
        <span style="font-size: 0.85rem; color: var(--color-gray-600);">
            Tinjau kunci jawaban dan penjelasan konsep materi untuk setiap soal di bawah ini.
        </span>
    </div>

    <!-- Question list review -->
    <div style="display: flex; flex-direction: column; gap: 28px;">
        @foreach($attempt->quiz->questions as $index => $question)
            @php
                $studentChoice = $studentAnswers[$question->id] ?? null;
                $isCorrect = $studentChoice && strtolower($studentChoice) === strtolower($question->correct_option);
            @endphp
            <div class="question-card" style="border: 1px solid var(--color-gray-200); border-radius: var(--border-radius-md); padding: 24px; background: var(--color-white); {{ $isCorrect ? 'border-left: 5px solid var(--color-success);' : 'border-left: 5px solid var(--color-danger);' }}">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <span class="question-number" style="font-weight: 700; color: var(--color-gray-600); font-size: 0.9rem;">
                        Soal Nomor {{ $index + 1 }} dari {{ $attempt->total_questions }}
                    </span>
                    @if($isCorrect)
                        <span class="badge completed" style="background-color: rgba(16, 185, 129, 0.15); color: var(--color-success); font-weight: 700; font-size: 0.8rem; padding: 4px 10px;">
                            ✓ Jawaban Benar
                        </span>
                    @else
                        <span class="badge completed" style="background-color: rgba(239, 68, 68, 0.15); color: var(--color-danger); font-weight: 700; font-size: 0.8rem; padding: 4px 10px;">
                            ✗ Jawaban Salah
                        </span>
                    @endif
                </div>

                <div class="question-text" style="font-size: 1.05rem; font-weight: 600; color: var(--color-dark); margin-bottom: 18px; line-height: 1.6;">
                    {!! str_contains($question->question_text, '<') ? $question->question_text : nl2br(e($question->question_text)) !!}
                </div>

                <div class="options-list" style="display: flex; flex-direction: column; gap: 10px;">
                    @foreach(['a' => $question->option_a, 'b' => $question->option_b, 'c' => $question->option_c, 'd' => $question->option_d, 'e' => $question->option_e] as $optKey => $optVal)
                        @php
                            $isThisCorrect = ($question->correct_option === $optKey);
                            $isThisStudentChoice = ($studentChoice === $optKey);
                            $bgStyle = 'background-color: var(--color-white); border: 1px solid var(--color-gray-200);';
                            if ($isThisCorrect) {
                                $bgStyle = 'background-color: rgba(16, 185, 129, 0.06); border: 2px solid var(--color-success); font-weight: 600;';
                            } elseif ($isThisStudentChoice && !$isThisCorrect) {
                                $bgStyle = 'background-color: rgba(239, 68, 68, 0.06); border: 2px solid var(--color-danger);';
                            }
                        @endphp
                        <div style="padding: 12px 16px; border-radius: var(--border-radius-sm); {{ $bgStyle }} display: flex; align-items: center; justify-content: space-between; gap: 12px;">
                            <div>
                                <span style="font-weight: 700; margin-right: 6px; text-transform: uppercase;">{{ $optKey }}.</span>
                                <span>{{ $optVal }}</span>
                            </div>
                            <div style="display: flex; gap: 6px; flex-shrink: 0;">
                                @if($isThisStudentChoice)
                                    <span style="font-size: 0.75rem; font-weight: 700; padding: 2px 8px; border-radius: 12px; {{ $isThisCorrect ? 'background: #10b981; color: white;' : 'background: #ef4444; color: white;' }}">
                                        Pilihan Anda
                                    </span>
                                @endif
                                @if($isThisCorrect)
                                    <span style="font-size: 0.75rem; font-weight: 700; padding: 2px 8px; border-radius: 12px; background: rgba(16, 185, 129, 0.2); color: #047857;">
                                        ★ Kunci Jawaban
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($question->explanation)
                    <div style="margin-top: 18px; padding: 16px 20px; background: linear-gradient(to right, #eff6ff, #f8fafc); border-left: 4px solid #3b82f6; border-radius: var(--border-radius-sm);">
                        <div style="display: flex; align-items: center; gap: 6px; font-weight: 700; color: #1e40af; font-size: 0.95rem; margin-bottom: 6px;">
                            <i class="fi fi-rr-bulb" style="font-size: 1.1rem; line-height: 1;"></i> Pembahasan Konsep & Analisis Kunci:
                        </div>
                        <div style="color: #334155; font-size: 0.9rem; line-height: 1.6;">
                            {!! str_contains($question->explanation, '<') ? $question->explanation : nl2br(e($question->explanation)) !!}
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Bottom Actions -->
    <div style="margin-top: 40px; display: flex; justify-content: center; gap: 16px;">
        <a href="{{ route('siswa.quizzes.index') }}" class="btn btn-secondary">
            Kembali ke Daftar Kuis
        </a>
        <a href="{{ route('siswa.dashboard') }}" class="btn btn-primary">
            Kembali ke Dashboard
        </a>
    </div>
</div>
@endsection
