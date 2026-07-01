@extends('layouts.siswa')

@section('title', 'Detail Kuis - Empat Pilar')

@section('content')
<div class="page-header" style="border: none; padding-bottom: 0;">
    <a href="{{ route('siswa.quizzes.index') }}" class="btn btn-secondary btn-sm">
        ← Kembali
    </a>
</div>

<div class="card" style="max-width: 600px; margin: 0 auto; width: 100%; box-shadow: var(--shadow-md); border-radius: var(--border-radius-lg);">
    <div class="card-body" style="padding: 40px; text-align: center; display: flex; flex-direction: column; gap: 24px;">
        <div>
            <span class="badge {{ $quiz->pillar }}" style="font-size: 0.85rem; margin-bottom: 12px; font-weight: 700; padding: 6px 14px;">
                Kuis {{ $quiz->formatted_pillar }}
            </span>
            <h2 style="font-size: 1.75rem; color: var(--color-dark); font-family: var(--font-heading);">{{ $quiz->title }}</h2>
        </div>

        <p style="color: var(--color-gray-600); line-height: 1.6; font-size: 1rem;">
            {{ $quiz->description ?? 'Uji pemahaman Anda mengenai pilar kebangsaan ini melalui beberapa butir soal pilihan ganda.' }}
        </p>

        <!-- Quiz Meta Details -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin: 8px 0; background-color: var(--color-gray-100); padding: 20px; border-radius: var(--border-radius-md);">
            <div style="text-align: center; border-right: 1px solid var(--color-gray-200);">
                <div style="font-size: 1.75rem; font-weight: 700; color: var(--color-dark); display: flex; align-items: center; justify-content: center; gap: 6px;"><i class="fi fi-rr-clock" style="font-size: 1.5rem; line-height: 1;"></i> {{ $quiz->duration_minutes }}</div>
                <div style="font-size: 0.75rem; color: var(--color-gray-600); font-weight: 600; text-transform: uppercase; margin-top: 4px;">Menit Pengerjaan</div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 1.75rem; font-weight: 700; color: var(--color-dark); display: flex; align-items: center; justify-content: center; gap: 6px;"><i class="fi fi-rr-list" style="font-size: 1.5rem; line-height: 1;"></i> {{ $quiz->questions_count }}</div>
                <div style="font-size: 0.75rem; color: var(--color-gray-600); font-weight: 600; text-transform: uppercase; margin-top: 4px;">Jumlah Pertanyaan</div>
            </div>
        </div>

        <div style="background-color: #fef3c7; border-left: 4px solid var(--color-warning); padding: 12px 16px; text-align: left; font-size: 0.85rem; color: #92400e; border-radius: 0 var(--border-radius-sm) var(--border-radius-sm) 0;">
            <strong>Petunjuk Ujian:</strong>
            <ul style="margin-left: 20px; margin-top: 4px;">
                <li>Kuis akan otomatis dikirimkan jika waktu pengerjaan habis.</li>
                <li>Menutup atau mereload tab browser saat kuis berlangsung akan menghilangkan progres jawaban Anda.</li>
                <li>Setelah selesai, Anda dapat meninjau pembahasan jawaban yang benar.</li>
            </ul>
        </div>

        <div style="margin-top: 8px;">
            <a href="{{ route('siswa.quizzes.start', $quiz) }}" class="btn btn-primary" style="width: 100%; padding: 14px; font-size: 1.05rem; font-weight: 700; letter-spacing: 0.5px;">
                Mulai Kuis Sekarang
            </a>
        </div>
    </div>
</div>
@endsection
