@extends('layouts.siswa')

@section('title', 'Mengerjakan Real Materi - Empat Pilar')

@section('content')
<div class="quiz-wrapper">
    <!-- Sticky Timer Header -->
    <div class="quiz-timer-bar">
        <div>
            <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 4px;">
                <h3 style="font-size: 1.05rem; font-family: var(--font-heading); color: var(--color-white); margin: 0;">{{ $quiz->title }}</h3>
                <span class="badge" style="background: rgba(255,255,255,0.2); color: #fff; font-size: 0.72rem; padding: 2px 8px; border-radius: 4px;">
                    📦 {{ $quiz->package_code ?? 'Paket Utama' }}
                </span>
                @if($quiz->province)
                    <span class="badge" style="background: rgba(245, 158, 11, 0.3); color: #fde68a; font-size: 0.72rem; padding: 2px 8px; border-radius: 4px;">
                        📍 Wilayah: {{ $quiz->province->name }}
                    </span>
                @endif
            </div>
            <p style="font-size: 0.75rem; color: var(--color-gray-300); margin: 0;">Pilar: {{ $quiz->formatted_pillar }} (Real Materi Evaluasi Resmi)</p>
        </div>
        <div style="text-align: right;">
            <div style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--color-gray-300); font-weight: 600;">Sisa Waktu</div>
            <div class="timer-display" id="quiz-timer" data-duration="{{ $quiz->duration_minutes }}">
                --:--
            </div>
        </div>
    </div>

    <!-- Questions Form -->
    <form action="{{ route('siswa.real-materi.submit', $quiz) }}" method="POST" id="quiz-form">
        @csrf
        <!-- Field to store seconds taken -->
        <input type="hidden" name="duration_seconds_taken" id="duration_seconds_taken" value="0">

        <div class="quiz-layout-container">
            <!-- Main Questions Column -->
            <div class="quiz-main-column">
                <div style="display: flex; flex-direction: column; gap: 32px;">
                    @foreach($shuffledQuestions as $index => $question)
                        <div class="question-card" id="q-{{ $question->id }}" data-question-id="{{ $question->id }}">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; flex-wrap: wrap; gap: 8px;">
                                <div class="question-number">Soal {{ $index + 1 }} dari {{ $shuffledQuestions->count() }}</div>
                                <button type="button" class="flag-question-btn" data-question-id="{{ $question->id }}" title="Tandai jika masih ragu">
                                    <i class="fi fi-rr-flag"></i> <span>Ragu-ragu</span>
                                </button>
                            </div>
                            
                            <div class="question-text">
                                {!! str_contains($question->question_text, '<') ? $question->question_text : nl2br(e($question->question_text)) !!}
                            </div>

                            <div class="options-list">
                                @foreach($question->shuffled_options as $opt)
                                    <div class="option-item">
                                        <label>
                                            <input type="radio" 
                                                   name="answers[{{ $question->id }}]" 
                                                   value="{{ $opt['key'] }}" 
                                                   data-display-label="{{ $opt['display_label'] }}"
                                                   data-question-id="{{ $question->id }}">
                                            <span><strong>{{ $opt['display_label'] }}.</strong> {{ $opt['text'] }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Submit Panel -->
                <div style="margin-top: 40px; display: flex; justify-content: space-between; align-items: center; background-color: var(--color-white); padding: 24px; border-radius: var(--border-radius-md); box-shadow: var(--shadow-sm); border: 1px solid var(--color-gray-200);">
                    <div style="font-size: 0.9rem; color: var(--color-gray-600); font-weight: 500;">
                        Kuis Real Materi hanya dapat dikerjakan 1 kali. Harap periksa kembali semua jawaban sebelum mengirim.
                    </div>
                    <button type="submit" class="btn btn-primary" style="background-color: var(--color-success); border: none; padding: 12px 28px; font-size: 1rem;">
                        💾 Selesai
                    </button>
                </div>
            </div>

            <!-- Sticky Question Palette Sidebar -->
            <aside class="quiz-palette-sidebar">
                <div class="palette-card">
                    <div class="palette-header">
                        <h4><i class="fi fi-rr-apps" style="color: rgb(var(--color-primary-rgb));"></i> Palet Nomor Soal</h4>
                        <span class="palette-progress-badge" id="answered-count-badge">0 / {{ $shuffledQuestions->count() }}</span>
                    </div>
                    <div class="palette-progress-bar-bg">
                        <div class="palette-progress-bar-fill" id="palette-progress-fill" style="width: 0%;"></div>
                    </div>
                    <div class="palette-grid">
                        @foreach($shuffledQuestions as $index => $question)
                            <button type="button" class="palette-btn" id="palette-btn-{{ $question->id }}" data-target="q-{{ $question->id }}" data-question-id="{{ $question->id }}" title="Lompat ke Soal {{ $index + 1 }}">
                                {{ $index + 1 }}
                            </button>
                        @endforeach
                    </div>
                    <div class="palette-legend">
                        <div class="legend-item"><span class="legend-dot answered"></span> Terjawab</div>
                        <div class="legend-item"><span class="legend-dot flagged"></span> Ragu-ragu</div>
                        <div class="legend-item"><span class="legend-dot unanswered"></span> Kosong</div>
                    </div>
                </div>
            </aside>
        </div>
    </form>
</div>
@endsection
