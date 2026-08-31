@extends('layouts.siswa')

@section('title', 'Real Materi - Empat Pilar')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h1>Evaluasi Real Materi</h1>
        <p>Uji pemahaman nyata Anda mengenai Empat Pilar Kebangsaan. Kuis evaluasi ini hanya dapat dikerjakan <strong>1 kali saja</strong> dan nilainya akan tercatat di Papan Peringkat.</p>
    </div>
</div>

@php
    $pillarsInfo = [
        'pancasila' => ['title' => 'Pancasila', 'icon_class' => 'fi fi-rr-shield', 'color' => '#ff5252'],
        'uud_1945' => ['title' => 'UUD NRI 1945', 'icon_class' => 'fi fi-rr-scroll', 'color' => '#ffd740'],
        'nkri' => ['title' => 'NKRI', 'icon_class' => 'fi fi-rr-map', 'color' => '#40c4ff'],
        'bhinneka_tunggal_ika' => ['title' => 'Bhinneka Tunggal Ika', 'icon_class' => 'fi fi-rr-handshake', 'color' => '#69f0ae'],
        'twk_kedinasan' => ['title' => 'Simulasi TWK Kedinasan', 'icon_class' => 'fi fi-rr-diploma', 'color' => '#8b5cf6']
    ];
@endphp

<div style="display: flex; flex-direction: column; gap: 40px;">
    @foreach($groupedQuizzes as $pillar => $quizzesList)
        <div>
            <!-- Pillar Header -->
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px; border-bottom: 2px solid var(--color-gray-200); padding-bottom: 12px;">
                <i class="{{ $pillarsInfo[$pillar]['icon_class'] }}" style="font-size: 1.75rem; color: {{ $pillarsInfo[$pillar]['color'] }}; line-height: 1;"></i>
                <h2 style="font-size: 1.5rem; color: var(--color-dark);">Evaluasi Real {{ $pillarsInfo[$pillar]['title'] }}</h2>
            </div>

            <!-- Quizzes Grid -->
            @if(count($quizzesList) > 0)
                <div class="pillars-grid">
                    @foreach($quizzesList as $quiz)
                        <div class="card" style="transition: var(--transition-smooth); border: 1px solid var(--color-gray-200); display: flex; flex-direction: column; justify-content: space-between;">
                            <div class="card-body" style="padding: 24px; display: flex; flex-direction: column; gap: 12px; flex: 1;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <span class="badge {{ $quiz->pillar }}" style="font-size: 0.7rem;">
                                        <i class="fi fi-rr-clock" style="margin-right: 4px; font-size: 0.75rem; vertical-align: middle;"></i>{{ $quiz->duration_minutes }} Menit
                                    </span>
                                    <span class="badge admin" style="font-size: 0.7rem;">
                                        <i class="fi fi-rr-list" style="margin-right: 4px; font-size: 0.75rem; vertical-align: middle;"></i>{{ $quiz->questions_count }} Soal
                                    </span>
                                </div>
                                
                                <h3 style="font-size: 1.1rem; color: var(--color-dark); font-weight: 600; line-height: 1.4; margin-top: 4px;">
                                    {{ $quiz->title }}
                                </h3>
                                
                                <p style="color: var(--color-gray-600); font-size: 0.85rem; flex-grow: 1;">
                                    {{ Str::limit($quiz->description, 80) }}
                                </p>

                                @if($quiz->is_completed)
                                    <div style="margin-top: 12px; padding: 8px 12px; background-color: rgba(16, 185, 129, 0.05); border: 1px solid rgba(16, 185, 129, 0.2); border-radius: var(--border-radius-sm); display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem;">
                                        <span style="color: var(--color-success); font-weight: 600;">Sudah Dikerjakan (Nilai):</span>
                                        <span style="font-weight: 700; font-size: 1.1rem; color: var(--color-success)">
                                            {{ $quiz->highest_score }}
                                        </span>
                                    </div>
                                @else
                                    <div style="margin-top: 12px; padding: 8px 12px; background-color: var(--color-gray-100); border-radius: var(--border-radius-sm); text-align: center; font-size: 0.85rem; color: var(--color-gray-400); font-style: italic;">
                                        Belum pernah dikerjakan
                                    </div>
                                @endif
                            </div>
                            <div style="padding: 16px 24px; border-top: 1px solid var(--color-gray-100); background-color: var(--color-gray-100);">
                                @if($quiz->is_completed)
                                    <a href="#" class="btn btn-secondary btn-sm" style="width: 100%; justify-content: center; font-weight: 600; opacity: 0.7; cursor: not-allowed;" onclick="event.preventDefault(); Swal.fire('Informasi', 'Setiap kuis Real Materi hanya dapat dikerjakan 1 kali saja.', 'info');">
                                        Sudah Selesai
                                    </a>
                                @else
                                    <a href="{{ route('siswa.real-materi.show', $quiz) }}" class="btn btn-primary btn-sm" style="width: 100%; justify-content: center; font-weight: 600;">
                                        Mulai Evaluasi
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="background-color: var(--color-white); border-radius: var(--border-radius-md); padding: 30px; text-align: center; border: 1px solid var(--color-gray-200); color: var(--color-gray-400);">
                    <p style="font-size: 1.5rem; margin-bottom: 4px;"><i class="fi fi-rr-box-open" style="color: var(--color-gray-400); font-size: 1.5rem;"></i></p>
                    <p>Evaluasi Real Materi untuk kategori ini belum ditambahkan oleh Admin.</p>
                </div>
            @endif
        </div>
    @endforeach
</div>
@endsection
