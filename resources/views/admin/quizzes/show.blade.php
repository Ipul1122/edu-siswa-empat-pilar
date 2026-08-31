@extends('layouts.admin')

@section('title', 'Kelola Soal: ' . $quiz->title . ' - Admin')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h1>Kelola Soal: {{ $quiz->title }}</h1>
        <p>
            Tipe: <span class="badge {{ $quiz->type === 'real' ? 'admin' : 'siswa' }}" style="{{ $quiz->type === 'real' ? 'background: #d32f2f; color: white;' : '' }}">{{ $quiz->type === 'real' ? 'Real Materi' : 'Latihan Kuis' }}</span>
            | Kategori: <span class="badge {{ $quiz->pillar }}">{{ $quiz->formatted_pillar }}</span> 
            | <i class="fi fi-rr-clock" style="margin-right: 2px; vertical-align: middle; font-size: 0.85rem;"></i> {{ $quiz->duration_minutes }} Menit
        </p>
    </div>
    <div style="display: flex; gap: 12px;">
        <a href="{{ $quiz->type === 'real' ? route('admin.real-materi.index') : route('admin.quizzes.index') }}" class="btn btn-secondary">
            ← {{ $quiz->type === 'real' ? 'Daftar Real Materi' : 'Daftar Latihan Kuis' }}
        </a>
        <a href="{{ route('admin.questions.create', $quiz) }}" class="btn btn-primary">
            <i class="fi fi-rr-plus"></i> Tambah Soal
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Daftar Soal (Total: {{ $quiz->questions->count() }})</h3>
    </div>
    <div class="card-body" style="display: flex; flex-direction: column; gap: 24px;">
        @if($quiz->questions->count() > 0)
            @foreach($quiz->questions as $index => $question)
                <div style="border: 1px solid var(--color-gray-200); border-radius: var(--border-radius-md); padding: 24px; position: relative;" class="question-list-item">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; flex-wrap: wrap; gap: 12px;">
                        <span style="font-weight: 700; color: var(--color-gray-600); text-transform: uppercase; font-size: 0.85rem;">Soal #{{ $index + 1 }}</span>
                        <div style="display: flex; gap: 8px;">
                            <a href="{{ route('admin.questions.edit', $question) }}" class="btn btn-secondary btn-sm" style="padding: 4px 8px; font-size: 0.75rem;">
                                <i class="fi fi-rr-pencil"></i> Edit
                            </a>
                            <form action="{{ route('admin.questions.destroy', $question) }}" method="POST" class="delete-confirm-form" data-item-type="soal kuis">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" style="padding: 4px 8px; font-size: 0.75rem;">
                                    <i class="fi fi-rr-trash"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <div style="font-weight: 600; font-size: 1.05rem; margin-bottom: 16px; color: var(--color-dark); line-height: 1.6;">
                        {!! str_contains($question->question_text, '<') ? $question->question_text : nl2br(e($question->question_text)) !!}
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr; gap: 8px; margin-bottom: 16px; font-size: 0.95rem;">
                        <div style="padding: 8px 12px; border-radius: var(--border-radius-sm); border: 1px solid var(--color-gray-200); {{ $question->correct_option === 'a' ? 'border-color: var(--color-success); background-color: rgba(16, 185, 129, 0.04); font-weight: 600;' : '' }}">
                            <strong>A.</strong> {{ $question->option_a }}
                        </div>
                        <div style="padding: 8px 12px; border-radius: var(--border-radius-sm); border: 1px solid var(--color-gray-200); {{ $question->correct_option === 'b' ? 'border-color: var(--color-success); background-color: rgba(16, 185, 129, 0.04); font-weight: 600;' : '' }}">
                            <strong>B.</strong> {{ $question->option_b }}
                        </div>
                        <div style="padding: 8px 12px; border-radius: var(--border-radius-sm); border: 1px solid var(--color-gray-200); {{ $question->correct_option === 'c' ? 'border-color: var(--color-success); background-color: rgba(16, 185, 129, 0.04); font-weight: 600;' : '' }}">
                            <strong>C.</strong> {{ $question->option_c }}
                        </div>
                        <div style="padding: 8px 12px; border-radius: var(--border-radius-sm); border: 1px solid var(--color-gray-200); {{ $question->correct_option === 'd' ? 'border-color: var(--color-success); background-color: rgba(16, 185, 129, 0.04); font-weight: 600;' : '' }}">
                            <strong>D.</strong> {{ $question->option_d }}
                        </div>
                        <div style="padding: 8px 12px; border-radius: var(--border-radius-sm); border: 1px solid var(--color-gray-200); {{ $question->correct_option === 'e' ? 'border-color: var(--color-success); background-color: rgba(16, 185, 129, 0.04); font-weight: 600;' : '' }}">
                            <strong>E.</strong> {{ $question->option_e }}
                        </div>
                    </div>
                    
                    @if($question->explanation)
                        <div class="explanation-box" style="font-size: 0.9rem;">
                            <strong><i class="fi fi-rr-info" style="margin-right: 4px; vertical-align: middle;"></i>Pembahasan/Keterangan:</strong><br>
                            <div style="margin-top: 4px;">
                                {!! str_contains($question->explanation, '<') ? $question->explanation : nl2br(e($question->explanation)) !!}
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        @else
            <div style="text-align: center; padding: 40px 20px; color: var(--color-gray-400);">
                <p style="font-size: 2rem; margin-bottom: 8px;"><i class="fi fi-rr-box-open" style="color: var(--color-gray-400); font-size: 2rem;"></i></p>
                <p>Belum ada pertanyaan dalam kuis ini.</p>
                <a href="{{ route('admin.questions.create', $quiz) }}" class="btn btn-primary btn-sm" style="margin-top: 12px;">Tambah Soal Pertama</a>
            </div>
        @endif
    </div>
</div>
@endsection
