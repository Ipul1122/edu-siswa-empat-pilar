@extends('layouts.admin')

@section('title', 'Detail Siswa: ' . $student->name . ' - Admin Empat Pilar')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h1>Rapor & Evaluasi Siswa</h1>
        <p>Analisis capaian kompetensi Empat Pilar Kebangsaan dan rekam jejak evaluasi.</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">
            ← Kembali ke Daftar Siswa
        </a>
    </div>
</div>

<!-- Student Header Card -->
<div class="card" style="background: linear-gradient(135deg, var(--color-dark), var(--color-dark-light)); color: var(--color-white); border: none;">
    <div class="card-body" style="padding: 32px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 24px;">
        <div style="display: flex; align-items: center; gap: 24px;">
            <img src="{{ $student->image_url }}" alt="{{ $student->name }}" style="width: 84px; height: 84px; border-radius: 50%; object-fit: cover; border: 3px solid rgba(255,255,255,0.4); box-shadow: 0 4px 12px rgba(0,0,0,0.2); flex-shrink: 0; background: #fff;">
            <div>
                <h2 style="color: var(--color-white); font-size: 1.75rem; margin-bottom: 4px;">{{ $student->name }}</h2>
                <p style="color: var(--color-gray-300); font-size: 0.95rem; margin-top: 2px;">
                    Sekolah: <strong>{{ $student->school_name }}</strong>
                    @if($student->dapil)
                        | Dapil: <span class="badge" style="background-color: var(--color-secondary); color: var(--color-dark); font-weight: 700; font-size: 0.75rem; vertical-align: middle;">{{ $student->dapil }}</span>
                    @endif
                </p>
                <p style="color: var(--color-gray-400); font-size: 0.85rem; margin-top: 4px;">
                    Email: {{ $student->email }} | Terdaftar sejak: {{ $student->created_at->format('d M Y') }}
                </p>
                @if($student->address)
                    <p style="color: var(--color-gray-300); font-size: 0.85rem; margin-top: 4px; display: flex; align-items: flex-start; gap: 4px;">
                        <i class="fi fi-rr-marker" style="font-size: 0.9rem; margin-top: 2px;"></i>
                        <span>Alamat: {{ $student->address }}</span>
                    </p>
                @endif
            </div>
        </div>
        <div style="display: flex; gap: 20px; text-align: center;">
            <div style="background: rgba(255,255,255,0.1); padding: 16px 24px; border-radius: var(--border-radius-md); border: 1px solid rgba(255,255,255,0.15);">
                <span style="display: block; font-size: 2rem; font-weight: 700; color: var(--color-secondary); font-family: var(--font-heading);">
                    {{ $averageScore }}
                </span>
                <span style="font-size: 0.8rem; color: var(--color-gray-300); text-transform: uppercase; letter-spacing: 0.5px;">Rata-Rata Nilai</span>
            </div>
            <div style="background: rgba(255,255,255,0.1); padding: 16px 24px; border-radius: var(--border-radius-md); border: 1px solid rgba(255,255,255,0.15);">
                <span style="display: block; font-size: 2rem; font-weight: 700; color: var(--color-white); font-family: var(--font-heading);">
                    {{ $completedRealMateriCount }}/{{ $totalRealMateriCount }}
                </span>
                <span style="font-size: 0.8rem; color: var(--color-gray-300); text-transform: uppercase; letter-spacing: 0.5px;">Real Materi Selesai</span>
            </div>
        </div>
    </div>
</div>

<!-- Evaluation Radar Chart & Mastery Overview -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-top: 24px;">
    <!-- Radar Chart: 4 Pilar Competency -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Radar Kompetensi 4 Pilar Kebangsaan</h3>
        </div>
        <div class="card-body" style="display: flex; justify-content: center; align-items: center; min-height: 300px;">
            <canvas id="competencyRadarChart" style="max-height: 280px; max-width: 100%;"></canvas>
        </div>
    </div>

    <!-- Mastery Breakdown by Pillar -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Tingkat Penguasaan per Pilar</h3>
        </div>
        <div class="card-body">
            @php
                $pillarKeys = [
                    'pancasila' => ['title' => 'Pancasila', 'desc' => 'Ideologi & Falsafah Bangsa'],
                    'uud_1945' => ['title' => 'UUD NRI 1945', 'desc' => 'Konstitusi & Hukum Dasar'],
                    'nkri' => ['title' => 'NKRI', 'desc' => 'Bentuk Negara & Persatuan'],
                    'bhinneka_tunggal_ika' => ['title' => 'Bhinneka Tunggal Ika', 'desc' => 'Semboyan Keberagaman']
                ];
            @endphp

            <div style="display: flex; flex-direction: column; gap: 16px;">
                @foreach($pillarKeys as $key => $meta)
                    @php
                        $score = $pillarScores[$key] ?? 0;
                        $statusClass = $score >= 75 ? 'badge-success' : ($score >= 50 ? 'badge-warning' : 'badge-danger');
                    @endphp
                    <div style="padding: 12px 16px; background-color: var(--color-gray-100); border-radius: var(--border-radius-md); border-left: 4px solid {{ $score >= 75 ? 'var(--color-success)' : ($score >= 50 ? 'var(--color-secondary)' : 'var(--color-danger)') }};">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                            <span style="font-weight: 600; color: var(--color-dark);">{{ $meta['title'] }}</span>
                            <span class="badge {{ $statusClass }}">{{ $score }}% Kuasai</span>
                        </div>
                        <div class="progress-bar-container" style="height: 6px; background-color: var(--color-gray-200);">
                            <div class="progress-bar-fill" style="width: {{ $score }}%;"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Attempt History Table -->
<div class="card" style="margin-top: 24px;">
    <div class="card-header">
        <h3 class="card-title">Riwayat Pengerjaan Evaluasi & Kuis</h3>
    </div>
    <div class="card-body" style="padding: 0;">
        <table class="table">
            <thead>
                <tr>
                    <th>Judul Kuis / Evaluasi</th>
                    <th>Pilar Kebangsaan</th>
                    <th>Waktu Mulai</th>
                    <th>Durasi</th>
                    <th>Jawaban Benar</th>
                    <th>Skor Akhir</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attempts as $attempt)
                    <tr>
                        <td style="font-weight: 600;">{{ $attempt->quiz->title }}</td>
                        <td>
                            <span class="badge badge-info">{{ $attempt->quiz->pillar_name }}</span>
                        </td>
                        <td>{{ $attempt->started_at->format('d M Y, H:i') }} WIB</td>
                        <td>
                            @if($attempt->completed_at)
                                {{ $attempt->started_at->diffInMinutes($attempt->completed_at) }} Menit
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            {{ $attempt->score / 10 }} / {{ $attempt->quiz->questions_count ?? 10 }} Soal
                        </td>
                        <td>
                            <span style="font-weight: 700; font-size: 1.05rem; color: {{ $attempt->score >= 75 ? 'var(--color-success)' : 'var(--color-danger)' }};">
                                {{ $attempt->score }}
                            </span>
                        </td>
                        <td>
                            @if($attempt->score >= 75)
                                <span class="badge badge-success">Lulus</span>
                            @else
                                <span class="badge badge-danger">Remedial</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-state">
                            <i class="fi fi-rr-document" style="font-size: 2rem; color: var(--color-gray-400);"></i>
                            <p style="margin-top: 8px;">Siswa ini belum pernah mengerjakan kuis evaluasi.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('competencyRadarChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'radar',
        data: {
            labels: ['Pancasila', 'UUD NRI 1945', 'NKRI', 'Bhinneka Tunggal Ika'],
            datasets: [{
                label: 'Skor Capaian Siswa',
                data: [
                    {{ $pillarScores['pancasila'] ?? 0 }},
                    {{ $pillarScores['uud_1945'] ?? 0 }},
                    {{ $pillarScores['nkri'] ?? 0 }},
                    {{ $pillarScores['bhinneka_tunggal_ika'] ?? 0 }}
                ],
                backgroundColor: 'rgba(220, 38, 38, 0.2)',
                borderColor: '#dc2626',
                pointBackgroundColor: '#dc2626',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: '#dc2626'
            }, {
                label: 'Standar Kelulusan (KKM)',
                data: [75, 75, 75, 75],
                borderColor: 'rgba(255, 193, 7, 0.8)',
                borderDash: [5, 5],
                backgroundColor: 'transparent',
                pointRadius: 0
            }]
        },
        options: {
            scales: {
                r: {
                    angleLines: { color: 'rgba(0, 0, 0, 0.1)' },
                    grid: { color: 'rgba(0, 0, 0, 0.05)' },
                    suggestedMin: 0,
                    suggestedMax: 100,
                    ticks: { stepSize: 25 }
                }
            },
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>
@endsection
