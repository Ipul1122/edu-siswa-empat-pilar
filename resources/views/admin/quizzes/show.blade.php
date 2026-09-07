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
    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
        <a href="{{ $quiz->type === 'real' ? route('admin.real-materi.index') : route('admin.quizzes.index') }}" class="btn btn-secondary">
            ← {{ $quiz->type === 'real' ? 'Daftar Real Materi' : 'Daftar Latihan Kuis' }}
        </a>
        <button type="button" class="btn btn-secondary" onclick="document.getElementById('import-csv-modal').style.display='flex'">
            <i class="fi fi-rr-file-import"></i> Import CSV
        </button>
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

<!-- Modal Import CSV Soal -->
<div id="import-csv-modal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 1050; align-items: center; justify-content: center; padding: 20px;">
    <div class="card" style="max-width: 580px; width: 100%; border-radius: var(--border-radius-lg); box-shadow: var(--shadow-xl); overflow: hidden; margin: 0;">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; padding: 18px 24px; border-bottom: 1px solid var(--color-gray-200); background: #f8fafc;">
            <h3 style="margin: 0; font-size: 1.15rem; display: flex; align-items: center; gap: 8px; color: var(--color-dark);">
                <i class="fi fi-rr-file-import" style="color: rgb(var(--color-primary-rgb));"></i> Import Soal via CSV
            </h3>
            <button type="button" onclick="document.getElementById('import-csv-modal').style.display='none'" style="background: none; border: none; font-size: 1.4rem; color: var(--color-gray-400); cursor: pointer; line-height: 1;">
                &times;
            </button>
        </div>
        <form action="{{ route('admin.questions.import', $quiz) }}" method="POST" enctype="multipart/form-data" id="import-csv-form">
            @csrf
            <div class="card-body" style="padding: 24px; display: flex; flex-direction: column; gap: 18px;">
                <div style="background: rgba(var(--color-primary-rgb), 0.06); border: 1px solid rgba(var(--color-primary-rgb), 0.2); border-radius: var(--border-radius-md); padding: 16px;">
                    <div style="font-weight: 600; color: rgb(var(--color-primary-rgb)); margin-bottom: 6px; font-size: 0.9rem;">
                        <i class="fi fi-rr-info"></i> Petunjuk Format CSV
                    </div>
                    <ul style="margin: 0; padding-left: 20px; font-size: 0.84rem; color: var(--color-gray-600); line-height: 1.5;">
                        <li>Format kolom: <code>pertanyaan, pilihan_a, pilihan_b, pilihan_c, pilihan_d, pilihan_e, jawaban_benar, pembahasan</code></li>
                        <li>Nilai kolom <strong>jawaban_benar</strong> harus salah satu dari: <code>a</code>, <code>b</code>, <code>c</code>, <code>d</code>, atau <code>e</code>.</li>
                        <li>Pastikan menggunakan file template yang telah kami sediakan agar proses impor berjalan lancar.</li>
                    </ul>
                </div>

                <div style="display: flex; justify-content: flex-start;">
                    <a href="{{ route('admin.questions.template', $quiz) }}" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fi fi-rr-download"></i> Unduh Template CSV (.csv)
                    </a>
                </div>

                <div class="form-group" style="margin: 0;">
                    <label for="csv_file" class="form-label" style="font-weight: 600; font-size: 0.9rem;">Pilih Berkas CSV (.csv)</label>
                    <input type="file" name="csv_file" id="csv_file" class="form-control" accept=".csv,text/csv" required style="padding: 10px;">
                    <small style="color: var(--color-gray-500); font-size: 0.78rem; margin-top: 4px; display: block;">
                        Maksimal ukuran berkas 5MB.
                    </small>
                </div>
            </div>
            <div style="padding: 16px 24px; background: #f8fafc; border-top: 1px solid var(--color-gray-200); display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('import-csv-modal').style.display='none'">
                    Batal
                </button>
                <button type="submit" class="btn btn-primary" id="btn-submit-import">
                    <i class="fi fi-rr-check"></i> Unggah & Import
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('import-csv-modal');
        if (modal) {
            modal.addEventListener('click', function (e) {
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            });
        }

        const form = document.getElementById('import-csv-form');
        if (form) {
            form.addEventListener('submit', function () {
                const submitBtn = document.getElementById('btn-submit-import');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = 'Memproses...';
                }
            });
        }
    });
</script>
@endsection
