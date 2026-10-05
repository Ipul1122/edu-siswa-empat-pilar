@extends('layouts.siswa')

@section('title', 'Mulai Seleksi - Empat Pilar')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h1>Mulai Seleksi</h1>
        <p>Uji pemahaman nyata Anda mengenai Empat Pilar Kebangsaan melalui sesi seleksi evaluasi resmi. Setiap ujian seleksi hanya dapat dikerjakan <strong>1 kali saja</strong> dan nilainya langsung tercatat di Papan Peringkat Nasional & Wilayah.</p>
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

    $allQuizzesCount = 0;
    $activeQuizzesCount = 0;
    foreach($groupedQuizzes as $pList) {
        $allQuizzesCount += count($pList);
        foreach($pList as $qItem) {
            if ($qItem->is_active) $activeQuizzesCount++;
        }
    }
    $totalPackagesCount = $allQuizzesCount;
@endphp

@if($allQuizzesCount > 0 && $activeQuizzesCount === 0)
    <div style="background-color: rgba(239, 68, 68, 0.08); border: 1.5px solid rgba(239, 68, 68, 0.3); border-radius: var(--border-radius-md); padding: 18px 22px; margin-bottom: 24px; display: flex; align-items: center; gap: 16px;">
        <i class="fi fi-rr-lock" style="font-size: 2rem; color: var(--color-danger); flex-shrink: 0;"></i>
        <div>
            <h3 style="font-size: 1.05rem; color: var(--color-danger); margin-bottom: 3px; font-weight: 700;">Akses Ujian Seleksi Sedang Ditutup</h3>
            <p style="font-size: 0.88rem; color: var(--color-gray-700); margin: 0;">
                Seluruh sesi ujian seleksi saat ini sedang dinonaktifkan / ditutup oleh Guru atau Administrator. Anda dapat kembali lagi nanti saat sesi evaluasi resmi dibuka.
            </p>
        </div>
    </div>
@endif

<div class="selection-track-wrapper">
    <div class="selection-track-header">
        <div class="selection-track-info">
            <span class="selection-track-count-badge">
                <i class="fi fi-rr-diploma"></i> {{ $totalPackagesCount }} Paket Seleksi
            </span>
            <span class="selection-track-hint">
                <i class="fi fi-rr-arrows-h"></i> Geser horizontal untuk melihat & memilih paket ujian
            </span>
        </div>
        <div class="selection-track-nav-btns">
            <button type="button" class="selection-nav-btn" id="btn-scroll-left" title="Geser ke kiri" aria-label="Geser ke kiri">
                <i class="fi fi-rr-angle-left"></i>
            </button>
            <button type="button" class="selection-nav-btn" id="btn-scroll-right" title="Geser ke kanan" aria-label="Geser ke kanan">
                <i class="fi fi-rr-angle-right"></i>
            </button>
        </div>
    </div>

    <!-- 1 Baris Horizontal Track Cards (Tidak scroll ke bawah) -->
    <div class="selection-cards-track" id="selectionCardsTrack">
        @foreach($groupedQuizzes as $pillar => $quizzesList)
            @php
                $pillarData = $pillarsInfo[$pillar] ?? [
                    'title' => ucfirst(str_replace('_', ' ', $pillar)),
                    'icon_class' => 'fi fi-rr-diploma',
                    'color' => '#8b5cf6'
                ];
            @endphp
            @if(count($quizzesList) > 0)
                @foreach($quizzesList as $quiz)
                    <div class="selection-card-item pillar-{{ $pillar }} {{ !$quiz->is_active ? 'is-closed' : '' }}">
                        <div class="selection-card-body">
                            <div class="selection-card-top">
                                <div class="selection-card-icon-pill" style="background: {{ $pillarData['color'] }}15; color: {{ $pillarData['color'] }};">
                                    <i class="{{ $pillarData['icon_class'] }}"></i>
                                </div>
                                <div>
                                    @if($quiz->is_completed)
                                        <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #059669; font-weight: 700; font-size: 0.7rem;">
                                            <i class="fi fi-rr-check"></i> Selesai
                                        </span>
                                    @elseif(!$quiz->is_active)
                                        <span class="badge badge-danger" style="font-size: 0.7rem;">
                                            <i class="fi fi-rr-lock"></i> Ditutup
                                        </span>
                                    @else
                                        <span class="badge" style="background: rgba(37, 99, 235, 0.1); color: #2563eb; font-weight: 700; font-size: 0.7rem;">
                                            <i class="fi fi-rr-badge-check"></i> Siap Ujian
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <span class="badge {{ $quiz->pillar }}" style="font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.5px;">
                                    {{ $pillarData['title'] }}
                                </span>
                                <h3 class="selection-card-title" title="{{ $quiz->title }}">
                                    {{ $quiz->title }}
                                </h3>
                            </div>

                            <div class="selection-meta-chips">
                                <span class="selection-meta-chip">
                                    <i class="fi fi-rr-clock"></i> {{ $quiz->duration_minutes }} Menit
                                </span>
                                <span class="selection-meta-chip">
                                    <i class="fi fi-rr-list"></i> {{ $quiz->questions_count }} Soal
                                </span>
                                @if($quiz->package_code)
                                    <span class="selection-meta-chip" style="background: rgba(139, 92, 246, 0.1); color: #7c3aed;">
                                        <i class="fi fi-rr-box"></i> {{ $quiz->package_code }}
                                    </span>
                                @endif
                                @if($quiz->province)
                                    <span class="selection-meta-chip" style="background: rgba(245, 158, 11, 0.12); color: #b45309;">
                                        <i class="fi fi-rr-marker"></i> {{ $quiz->province->name }}
                                    </span>
                                @endif
                            </div>

                            <p class="selection-card-desc">
                                {{ Str::limit(strip_tags($quiz->description ?: 'Uji pemahaman nyata Anda mengenai pilar ini melalui paket evaluasi seleksi resmi.'), 85) }}
                            </p>

                            @if($quiz->is_completed)
                                <div class="selection-status-badge-box completed">
                                    <span style="font-weight: 600;">Skor Nilai:</span>
                                    <span style="font-weight: 800; font-size: 1.15rem;">{{ $quiz->highest_score ?? 0 }} <small style="font-size: 0.75rem; font-weight: 500;">/ 100</small></span>
                                </div>
                            @elseif(!$quiz->is_active)
                                <div class="selection-status-badge-box closed">
                                    <i class="fi fi-rr-lock" style="margin-right: 5px;"></i> Sesi ujian ditutup oleh Admin
                                </div>
                            @else
                                <div class="selection-status-badge-box ready">
                                    <i class="fi fi-rr-shield-check" style="margin-right: 5px; color: var(--color-success);"></i> 1x Kesempatan Ujian
                                </div>
                            @endif
                        </div>

                        <div class="selection-card-footer">
                            @if($quiz->is_completed)
                                @if($quiz->latest_attempt)
                                    <a href="{{ route('siswa.real-materi.result', $quiz->latest_attempt) }}" class="btn btn-secondary btn-sm w-100" style="font-weight: 600; justify-content: center;">
                                        <i class="fi fi-rr-eye" style="margin-right: 4px;"></i> Lihat Hasil Ujian
                                    </a>
                                @else
                                    <button type="button" class="btn btn-secondary btn-sm w-100" style="font-weight: 600; opacity: 0.8; justify-content: center; cursor: not-allowed;" onclick="Swal.fire('Informasi', 'Setiap ujian Seleksi hanya dapat dikerjakan 1 kali saja.', 'info');">
                                        <i class="fi fi-rr-check-circle" style="margin-right: 4px;"></i> Sudah Selesai
                                    </button>
                                @endif
                            @elseif(!$quiz->is_active)
                                <button type="button" class="btn btn-secondary btn-sm w-100" style="opacity: 0.65; cursor: not-allowed; justify-content: center;" onclick="Swal.fire('Ujian Ditutup', 'Paket seleksi ini sedang ditutup oleh Admin.', 'warning');">
                                    <i class="fi fi-rr-lock" style="margin-right: 4px;"></i> Akses Ditutup
                                </button>
                            @else
                                <a href="{{ route('siswa.real-materi.show', $quiz) }}" class="btn btn-primary btn-sm selection-btn-start">
                                    <i class="fi fi-rr-play" style="margin-right: 6px;"></i> Mulai Seleksi
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <div class="selection-card-item pillar-{{ $pillar }}" style="opacity: 0.7;">
                    <div class="selection-card-body" style="text-align: center; justify-content: center; align-items: center; min-height: 240px;">
                        <div class="selection-card-icon-pill" style="background: {{ $pillarData['color'] }}15; color: {{ $pillarData['color'] }}; margin: 0 auto;">
                            <i class="{{ $pillarData['icon_class'] }}"></i>
                        </div>
                        <h3 class="selection-card-title" style="margin-top: 10px;">{{ $pillarData['title'] }}</h3>
                        <p class="selection-card-desc" style="text-align: center;">Paket seleksi untuk kategori ini belum ditambahkan oleh Admin.</p>
                    </div>
                    <div class="selection-card-footer">
                        <button type="button" class="btn btn-secondary btn-sm w-100" disabled style="opacity: 0.5; justify-content: center; cursor: not-allowed;">
                            Belum Tersedia
                        </button>
                    </div>
                </div>
            @endif
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const track = document.getElementById('selectionCardsTrack');
        const btnLeft = document.getElementById('btn-scroll-left');
        const btnRight = document.getElementById('btn-scroll-right');

        if (track && btnLeft && btnRight) {
            btnLeft.addEventListener('click', function() {
                track.scrollBy({ left: -300, behavior: 'smooth' });
            });
            btnRight.addEventListener('click', function() {
                track.scrollBy({ left: 300, behavior: 'smooth' });
            });
        }
    });
</script>
@endpush
