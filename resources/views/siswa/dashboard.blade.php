@extends('layouts.siswa')

@section('title', 'Dashboard Siswa - Empat Pilar')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h1>Halo, {{ Auth::user()->name }}!</h1>
        <p>Semangat belajar Kewarganegaraan! Mari pahami dasar negara demi masa depan bangsa.</p>
    </div>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon primary"><i class="fi fi-rr-book-alt"></i></div>
        <div class="stat-info" style="flex: 1;">
            <div style="display: flex; justify-content: space-between; align-items: baseline;">
                <span class="stat-value">{{ $readingProgress }}%</span>
                <span style="font-size: 0.8rem; color: var(--color-gray-600); font-weight: 500;">{{ $completedMaterials }}/{{ $totalMaterials }} Materi</span>
            </div>
            <div class="progress-container" style="height: 6px; margin-bottom: 0; margin-top: 4px;">
                <div class="progress-bar-fill" style="width: {{ $readingProgress }}%;"></div>
            </div>
            <span class="stat-label" style="font-size: 0.75rem; margin-top: 4px;">Progres Membaca</span>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon success"><i class="fi fi-rr-target"></i></div>
        <div class="stat-info">
            <span class="stat-value">{{ $averageScore }}</span>
            <span class="stat-label">Rata-rata Skor Kuis</span>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon info"><i class="fi fi-rr-edit"></i></div>
        <div class="stat-info">
            <span class="stat-value">{{ $totalQuizzesTaken }}</span>
            <span class="stat-label">Kuis Diikuti</span>
        </div>
    </div>
</div>

<div class="dashboard-grid">
    <!-- Left Column: Recommendation & Recent attempts -->
    <div style="display: flex; flex-direction: column; gap: 32px;">
        <!-- Recommendation Card -->
        @if($recommendedMaterial)
            <div class="card" style="border-left: 5px solid rgb(var(--color-primary-rgb));">
                <div class="card-body" style="padding: 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
                    <div style="flex: 1; min-width: 250px;">
                        <span class="badge {{ $recommendedMaterial->pillar }}" style="margin-bottom: 8px;">
                            Rekomendasi Bacaan
                        </span>
                        <h3 style="font-size: 1.35rem; margin-bottom: 8px;">{{ $recommendedMaterial->title }}</h3>
                        <p style="color: var(--color-gray-600); font-size: 0.95rem;">
                            Materi mengenai pilar <strong>{{ str_replace('_', ' ', $recommendedMaterial->pillar) }}</strong> ini belum Anda baca. Yuk pelajari sekarang!
                        </p>
                    </div>
                    <a href="{{ route('siswa.materials.show', $recommendedMaterial) }}" class="btn btn-primary" style="white-space: nowrap;">
                        Mulai Membaca
                    </a>
                </div>
            </div>
        @else
            <div class="card" style="border-left: 5px solid var(--color-success); background-color: rgba(16, 185, 129, 0.02);">
                <div class="card-body" style="padding: 24px; text-align: center;">
                    <span style="font-size: 2rem; color: var(--color-warning);"><i class="fi fi-rr-trophy"></i></span>
                    <h3 style="font-size: 1.25rem; margin-top: 8px; margin-bottom: 4px;">Hebat! Semua Materi Telah Dibaca</h3>
                    <p style="color: var(--color-gray-600); font-size: 0.95rem;">Anda telah menyelesaikan seluruh modul bacaan Empat Pilar Kebangsaan. Mari asah pemahaman Anda dengan mengerjakan kuis!</p>
                </div>
            </div>
        @endif

        <!-- Recent Attempts -->
        <div class="card">
            <div class="card-header">
                <h3>Kuis yang Baru Anda Kerjakan</h3>
                <a href="{{ route('siswa.quizzes.index') }}" style="font-size: 0.85rem; font-weight: 600;">Lihat Kuis Lainnya →</a>
            </div>
            <div class="card-body" style="padding: 16px;">
                @if($recentAttempts->count() > 0)
                    <div class="table-responsive">
                        <table class="table" style="font-size: 0.95rem;">
                            <thead>
                                <tr>
                                    <th>Kuis</th>
                                    <th>Pilar</th>
                                    <th>Skor</th>
                                    <th>Tanggal</th>
                                    <th style="width: 100px; text-align: center;">Tinjau</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentAttempts as $attempt)
                                    <tr>
                                        <td style="font-weight: 600; color: var(--color-dark);">
                                            {{ $attempt->quiz->title }}
                                        </td>
                                        <td>
                                            <span class="badge {{ $attempt->quiz->pillar }}" style="font-size: 0.7rem;">
                                                {{ str_replace('_', ' ', $attempt->quiz->pillar) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span style="font-weight: 700; font-size: 1.1rem; color: {{ $attempt->score >= 70 ? 'var(--color-success)' : 'var(--color-danger)' }}">
                                                {{ $attempt->score }}
                                            </span>
                                        </td>
                                        <td style="font-size: 0.85rem; color: var(--color-gray-600);">
                                            {{ $attempt->created_at->timezone('Asia/Jakarta')->format('d M Y') }}
                                        </td>
                                        <td>
                                            <div style="text-align: center;">
                                                <a href="{{ route('siswa.quizzes.result', $attempt) }}" class="btn btn-secondary btn-sm" style="padding: 4px 8px; font-size: 0.75rem;">
                                                    Detail
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div style="text-align: center; padding: 30px 20px; color: var(--color-gray-400);">
                        <p style="font-size: 1.5rem; margin-bottom: 8px;"><i class="fi fi-rr-edit" style="color: var(--color-gray-400); font-size: 1.5rem;"></i></p>
                        <p>Anda belum pernah mengerjakan kuis evaluasi.</p>
                        <a href="{{ route('siswa.quizzes.index') }}" class="btn btn-primary btn-sm" style="margin-top: 12px;">Coba Kuis Pertama</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Column: Info & Tips -->
    <div style="display: flex; flex-direction: column; gap: 32px;">
        <div class="card" style="background: radial-gradient(circle at 10% 20%, rgba(211, 47, 47, 0.04) 0%, rgba(255, 193, 7, 0.02) 90%);">
            <div class="card-header" style="background: none; border: none; padding-bottom: 0;">
                <h3>Empat Pilar Kebangsaan</h3>
            </div>
            <div class="card-body" style="display: flex; flex-direction: column; gap: 12px; font-size: 0.9rem; line-height: 1.5; color: var(--color-gray-600);">
                <p>Empat Pilar Kebangsaan adalah empat landasan penting kehidupan bernegara di Indonesia:</p>
                <div style="display: flex; flex-direction: column; gap: 8px; margin-top: 8px;">
                    <div style="display: flex; align-items: center; gap: 8px; font-weight: 500; color: var(--color-dark);">
                        <i class="fi fi-rr-shield" style="color: rgb(211, 47, 47); font-size: 1.1rem; line-height: 1;"></i> 1. Pancasila
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px; font-weight: 500; color: var(--color-dark);">
                        <i class="fi fi-rr-scroll" style="color: var(--color-warning); font-size: 1.1rem; line-height: 1;"></i> 2. UUD NRI 1945
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px; font-weight: 500; color: var(--color-dark);">
                        <i class="fi fi-rr-map" style="color: var(--color-info); font-size: 1.1rem; line-height: 1;"></i> 3. NKRI
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px; font-weight: 500; color: var(--color-dark);">
                        <i class="fi fi-rr-handshake" style="color: var(--color-success); font-size: 1.1rem; line-height: 1;"></i> 4. Bhinneka Tunggal Ika
                    </div>
                </div>
                <p style="margin-top: 12px; font-size: 0.8rem; font-style: italic;">"Pendidikan kewarganegaraan adalah senjata paling ampuh untuk mengubah dunia berawal dari diri kita."</p>
            </div>
        </div>
    </div>
</div>
@endsection
