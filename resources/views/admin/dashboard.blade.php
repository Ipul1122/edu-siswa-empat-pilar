@extends('layouts.admin')

@section('title', 'Dashboard Admin - Empat Pilar')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h1>Dashboard Admin</h1>
        <p>Ringkasan data aktivitas belajar dan evaluasi siswa mengenai Empat Pilar Kebangsaan.</p>
    </div>
</div>

<!-- Stats Grid -->
<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
    <div class="stat-card">
        <div class="stat-icon primary"><i class="fi fi-rr-users-alt"></i></div>
        <div class="stat-info">
            <span class="stat-value">{{ $totalStudents }}</span>
            <span class="stat-label">Siswa Terdaftar</span>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon info"><i class="fi fi-rr-book-alt"></i></div>
        <div class="stat-info">
            <span class="stat-value">{{ $totalMaterials }}</span>
            <span class="stat-label">Total Materi & Video</span>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon success"><i class="fi fi-rr-clipboard-list"></i></div>
        <div class="stat-info">
            <span class="stat-value">{{ $totalPracticeQuizzes }}</span>
            <span class="stat-label">Latihan Kuis</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon warning" style="background-color: rgba(239, 68, 68, 0.1); color: #dc2626;"><i class="fi fi-rr-diploma"></i></div>
        <div class="stat-info">
            <span class="stat-value">{{ $totalRealQuizzes }}</span>
            <span class="stat-label">Real Materi</span>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon secondary"><i class="fi fi-rr-edit"></i></div>
        <div class="stat-info">
            <span class="stat-value">{{ $totalAttempts }}</span>
            <span class="stat-label">Kuis Dikerjakan</span>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="dashboard-grid" style="margin-bottom: 32px; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 32px;">
    <!-- Pillar Average Scores Chart -->
    <div class="card">
        <div class="card-header">
            <h3>Rata-rata Nilai per Pilar</h3>
        </div>
        <div class="card-body" style="padding: 20px;">
            <div style="position: relative; height: 220px; width: 100%;">
                <canvas id="adminPillarChart"></canvas>
            </div>
        </div>
    </div>

    <!-- 7 Days Activity Chart -->
    <div class="card">
        <div class="card-header">
            <h3>Aktivitas Pengerjaan Kuis (7 Hari Terakhir)</h3>
        </div>
        <div class="card-body" style="padding: 20px;">
            <div style="position: relative; height: 220px; width: 100%;">
                <canvas id="adminActivityChart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="dashboard-grid">
    <!-- Recent Attempts -->
    <div class="card">
        <div class="card-header">
            <h3>Pengerjaan Kuis Terbaru</h3>
            <a href="{{ route('admin.students.index') }}" style="font-size: 0.85rem; font-weight: 600;">Lihat Semua Siswa →</a>
        </div>
        <div class="card-body">
            @if($recentAttempts->count() > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Siswa</th>
                                <th>Kuis / Pilar</th>
                                <th>Skor</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentAttempts as $attempt)
                                <tr>
                                    <td>
                                        <div style="font-weight: 600; color: var(--color-dark);">{{ $attempt->user->name }}</div>
                                        <div style="font-size: 0.75rem; color: var(--color-gray-400);">{{ $attempt->user->class_name }} - {{ $attempt->user->school_name }}</div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 500;">{{ $attempt->quiz->title }}</div>
                                        <span class="badge {{ $attempt->quiz->pillar }}">{{ str_replace('_', ' ', $attempt->quiz->pillar) }}</span>
                                    </td>
                                    <td>
                                        <span style="font-weight: 700; font-size: 1.1rem; color: {{ $attempt->score >= 70 ? 'var(--color-success)' : 'var(--color-danger)' }}">
                                            {{ $attempt->score }}
                                        </span>
                                    </td>
                                    <td style="font-size: 0.85rem; color: var(--color-gray-600);">
                                        {{ $attempt->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="text-align: center; padding: 40px 20px; color: var(--color-gray-400);">
                    <p style="font-size: 1.5rem; margin-bottom: 8px;"><i class="fi fi-rr-box-open" style="color: var(--color-gray-400); font-size: 2rem;"></i></p>
                    <p>Belum ada siswa yang mengerjakan kuis.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Quick Navigation Links -->
    <div class="card">
        <div class="card-header">
            <h3>Pintasan Menu</h3>
        </div>
        <div class="card-body" style="display: flex; flex-direction: column; gap: 16px;">
            <a href="{{ route('admin.materials.create') }}" class="btn btn-primary" style="width: 100%; text-align: left; justify-content: flex-start;">
                <i class="fi fi-rr-plus"></i> Tambah Materi Baru
            </a>
            <a href="{{ route('admin.quizzes.create') }}" class="btn btn-secondary" style="width: 100%; text-align: left; justify-content: flex-start; background: #f8fafc;">
                <i class="fi fi-rr-plus"></i> Buat Kuis Baru
            </a>
            <hr style="border: 0; border-top: 1px solid var(--color-gray-200);">
            <div style="background-color: var(--color-gray-100); padding: 16px; border-radius: var(--border-radius-sm); font-size: 0.85rem; color: var(--color-gray-600);">
                <p style="font-weight: 600; color: var(--color-dark); margin-bottom: 4px;"><i class="fi fi-rr-info" style="margin-right: 4px; vertical-align: middle;"></i> Informasi Guru/Admin:</p>
                Anda dapat menambahkan materi di menu <strong>Materi Belajar</strong> dan membuat soal latihan di menu <strong>Kuis & Soal</strong> untuk menguji kompetensi PPKN siswa.
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // 1. Pillar Average Chart
        const pillarCtx = document.getElementById('adminPillarChart').getContext('2d');
        new Chart(pillarCtx, {
            type: 'bar',
            data: {
                labels: ['Pancasila', 'UUD 1945', 'NKRI', 'Bhinneka'],
                datasets: [{
                    label: 'Nilai Rata-rata',
                    data: [
                        {{ $chartData['pancasila'] }},
                        {{ $chartData['uud_1945'] }},
                        {{ $chartData['nkri'] }},
                        {{ $chartData['bhinneka_tunggal_ika'] }}
                    ],
                    backgroundColor: [
                        'rgba(239, 68, 68, 0.75)',   // Pancasila (red)
                        'rgba(245, 158, 11, 0.75)',  // UUD (yellow)
                        'rgba(59, 130, 246, 0.75)',  // NKRI (blue)
                        'rgba(16, 185, 129, 0.75)'   // Bhinneka (green)
                    ],
                    borderColor: [
                        'rgba(239, 68, 68, 1)',
                        'rgba(245, 158, 11, 1)',
                        'rgba(59, 130, 246, 1)',
                        'rgba(16, 185, 129, 1)'
                    ],
                    borderWidth: 1.5,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            color: 'var(--color-gray-500)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: 'var(--color-gray-700)',
                            font: {
                                weight: '600'
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ' Rata-rata Skor: ' + context.formattedValue + ' / 100';
                            }
                        }
                    }
                }
            }
        });

        // 2. Activity Chart (Line Chart)
        const activityCtx = document.getElementById('adminActivityChart').getContext('2d');
        new Chart(activityCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_keys($activityLast7Days)) !!},
                datasets: [{
                    label: 'Jumlah Percobaan',
                    data: {!! json_encode(array_values($activityLast7Days)) !!},
                    borderColor: 'rgba(99, 102, 241, 1)',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: 'rgba(99, 102, 241, 1)',
                    pointBorderColor: '#fff',
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            precision: 0,
                            color: 'var(--color-gray-500)'
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: 'var(--color-gray-700)',
                            font: {
                                weight: '500'
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    });
</script>
@endsection
