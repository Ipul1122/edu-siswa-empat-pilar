@extends('layouts.siswa')

@section('title', 'Dashboard Siswa - Empat Pilar')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h1>Selamat Datang, Tim {{ Auth::user()->school_name ?? Auth::user()->name }}!</h1>
        <p>Akun Resmi Seleksi Online Empat Pilar MPR RI • Tim 10 Siswa (1 Perangkat) • {{ Auth::user()->province->name ?? 'Indonesia' }}</p>
    </div>
</div>

<!-- Quick Feature Cards (Tutorial & Ujian Seleksi CBT) -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; margin-bottom: 24px;">
    <a href="{{ route('siswa.tutorial') }}" style="text-decoration: none; color: inherit; background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #fff; padding: 18px 20px; border-radius: 12px; display: flex; align-items: center; justify-content: space-between; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
        <div>
            <div style="font-size: 0.75rem; text-transform: uppercase; color: #94a3b8; font-weight: 700;">Petunjuk Teknis</div>
            <div style="font-size: 1.05rem; font-weight: 800; margin-top: 2px;">Tutorial Pengerjaan Tim & Sinyal ➔</div>
            <div style="font-size: 0.78rem; color: #cbd5e1; margin-top: 4px;">Aturan 10 anak 1 laptop, pengawas Zoom, toleransi offline</div>
        </div>
        <i class="fi fi-rr-book-alt" style="font-size: 1.8rem; color: #60a5fa; opacity: 0.85;"></i>
    </a>

    <a href="{{ route('siswa.real-materi.index') }}" style="text-decoration: none; color: inherit; background: linear-gradient(135deg, #b91c1c 0%, #991b1b 100%); color: #fff; padding: 18px 20px; border-radius: 12px; display: flex; align-items: center; justify-content: space-between; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
        <div>
            <div style="font-size: 0.75rem; text-transform: uppercase; color: #fecaca; font-weight: 700;">Seleksi Nasional</div>
            <div style="font-size: 1.05rem; font-weight: 800; margin-top: 2px;">Ujian Seleksi Online CBT ➔</div>
            <div style="font-size: 0.78rem; color: #fee2e2; margin-top: 4px;">Langsung kerjakan paket soal resmi Empat Pilar MPR RI</div>
        </div>
        <i class="fi fi-rr-document-signed" style="font-size: 1.8rem; color: #fca5a5; opacity: 0.85;"></i>
    </a>
</div>

<!-- Stats Grid -->
<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); margin-bottom: 28px;">
    <!-- Stat 1: Seleksi Selesai -->
    <div class="stat-card">
        <div class="stat-icon primary"><i class="fi fi-rr-document-signed"></i></div>
        <div class="stat-info" style="flex: 1;">
            <div style="display: flex; justify-content: space-between; align-items: baseline;">
                <span class="stat-value">{{ $completedSeleksiCount }}/{{ $totalSeleksiPackages }}</span>
                <span style="font-size: 0.8rem; color: var(--color-gray-600); font-weight: 600;">{{ $seleksiProgress }}%</span>
            </div>
            <div class="progress-container" style="height: 6px; margin-bottom: 0; margin-top: 6px;">
                <div class="progress-bar-fill" style="width: {{ $seleksiProgress }}%;"></div>
            </div>
            <span class="stat-label" style="font-size: 0.75rem; margin-top: 4px;">Paket Seleksi Selesai</span>
        </div>
    </div>
    
    <!-- Stat 2: Rata-rata Skor -->
    <div class="stat-card">
        <div class="stat-icon success" style="background-color: rgba(16, 185, 129, 0.1); color: var(--color-success);"><i class="fi fi-rr-target"></i></div>
        <div class="stat-info">
            <span class="stat-value">{{ $averageScore }}</span>
            <span class="stat-label">Rata-rata Skor Sekolah</span>
        </div>
    </div>
    
    <!-- Stat 3: Hasil Skor Mandiri -->
    <div class="stat-card">
        <div class="stat-icon warning" style="background-color: rgba(245, 158, 11, 0.1); color: #d97706;"><i class="fi fi-rr-diploma"></i></div>
        <div class="stat-info">
            <span class="stat-value">{{ $completedSeleksiCount > 0 ? '✓ Ada' : '-' }}</span>
            <a href="{{ route('siswa.my-results') }}" class="stat-label" style="text-decoration: none; color: inherit;">
                Lembar Hasil Skor Tim →
            </a>
        </div>
    </div>

    <!-- Stat 4: Sesi Zoom -->
    <div class="stat-card">
        <div class="stat-icon info" style="background-color: rgba(59, 130, 246, 0.1); color: #2563eb;"><i class="fi fi-rr-video-camera-alt"></i></div>
        <div class="stat-info">
            <span class="stat-value">{{ $activeZoomCount }}</span>
            <a href="{{ route('siswa.zoom-sessions.index') }}" class="stat-label" style="text-decoration: none; color: inherit;">
                Sesi Zoom (Maks 500) →
            </a>
        </div>
    </div>
</div>

<div class="dashboard-grid">
    <!-- Left Column: Recommendation & Recent attempts -->
    <div style="display: flex; flex-direction: column; gap: 32px;">
        <!-- Recommendation Card for Seleksi -->
        @if($nextSeleksi)
            <div class="card" style="border-left: 5px solid rgb(var(--color-primary-rgb)); background: linear-gradient(to right, rgba(211, 47, 47, 0.03), #fff);">
                <div class="card-body" style="padding: 28px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
                    <div style="flex: 1; min-width: 250px;">
                        <span class="badge {{ $nextSeleksi->pillar }}" style="margin-bottom: 8px;">
                            {{ $nextSeleksi->formatted_pillar }}
                        </span>
                        <h3 style="font-size: 1.3rem; margin-bottom: 6px; color: var(--color-dark);">{{ $nextSeleksi->title }}</h3>
                        <p style="color: var(--color-gray-600); font-size: 0.92rem; margin-bottom: 6px;">
                            {{ Str::limit(strip_tags($nextSeleksi->description ?: 'Uji pemahaman nyata Anda mengenai Empat Pilar melalui paket seleksi evaluasi resmi ini.'), 100) }}
                        </p>
                        <div style="display: flex; gap: 14px; font-size: 0.8rem; color: var(--color-gray-500); font-weight: 500;">
                            <span><i class="fi fi-rr-clock"></i> {{ $nextSeleksi->duration_minutes }} Menit</span>
                            <span><i class="fi fi-rr-document"></i> {{ $nextSeleksi->questions_count }} Butir Soal</span>
                            <span style="color: #dc2626;"><i class="fi fi-rr-shield-exclamation"></i> 1x Pengerjaan</span>
                        </div>
                    </div>
                    <a href="{{ route('siswa.real-materi.show', $nextSeleksi) }}" class="btn btn-primary" style="white-space: nowrap; padding: 12px 24px; font-weight: 600;">
                        <i class="fi fi-rr-play" style="margin-right: 6px;"></i> Mulai Seleksi
                    </a>
                </div>
            </div>
        @elseif($completedSeleksiCount > 0 && $completedSeleksiCount >= $totalSeleksiPackages)
            <div class="card" style="border-left: 5px solid var(--color-success); background-color: rgba(16, 185, 129, 0.03);">
                <div class="card-body" style="padding: 28px; text-align: center;">
                    <span style="font-size: 2.2rem; color: var(--color-warning);"><i class="fi fi-rr-trophy"></i></span>
                    <h3 style="font-size: 1.25rem; margin-top: 8px; margin-bottom: 4px; color: var(--color-dark);">Luar Biasa! Semua Ujian Seleksi Telah Selesai</h3>
                    <p style="color: var(--color-gray-600); font-size: 0.95rem; max-width: 600px; margin: 0 auto 16px auto;">
                        Anda telah menyelesaikan seluruh paket evaluasi resmi Empat Pilar Kebangsaan. Perolehan skor Anda langsung tercatat secara permanen di Papan Peringkat Nasional & Wilayah.
                    </p>
                    <a href="{{ route('siswa.my-results') }}" class="btn btn-primary" style="padding: 10px 20px;">
                        <i class="fi fi-rr-diploma" style="margin-right: 6px;"></i> Lihat Lembar Hasil Skor Tim
                    </a>
                </div>
            </div>
        @else
            <div class="card" style="border-left: 5px solid var(--color-info); background-color: rgba(59, 130, 246, 0.03);">
                <div class="card-body" style="padding: 24px; text-align: center;">
                    <span style="font-size: 2rem; color: #2563eb;"><i class="fi fi-rr-diploma"></i></span>
                    <h3 style="font-size: 1.2rem; margin-top: 8px; margin-bottom: 4px;">Paket Seleksi Baru Akan Segera Hadir</h3>
                    <p style="color: var(--color-gray-600); font-size: 0.92rem; margin-bottom: 12px;">Pantau jadwal ujian seleksi resmi dan sesi pengawasan Zoom melalui menu navigasi.</p>
                    <a href="{{ route('siswa.real-materi.index') }}" class="btn btn-secondary btn-sm">Lihat Daftar Seleksi</a>
                </div>
            </div>
        @endif

        <!-- Recent Attempts -->
        <div class="card">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0;">Riwayat Ujian Seleksi</h3>
                <a href="{{ route('siswa.real-materi.index') }}" style="font-size: 0.85rem; font-weight: 600; text-decoration: none;">Lihat Semua Paket →</a>
            </div>
            <div class="card-body" style="padding: 16px;">
                @if($recentAttempts->count() > 0)
                    <div class="table-responsive">
                        <table class="table" style="font-size: 0.95rem; margin-bottom: 0;">
                            <thead>
                                <tr>
                                    <th>Paket Seleksi</th>
                                    <th>Pilar</th>
                                    <th>Skor</th>
                                    <th>Tanggal</th>
                                    <th style="width: 100px; text-align: center;">Hasil</th>
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
                                            {{ $attempt->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }}
                                        </td>
                                        <td>
                                            <div style="text-align: center;">
                                                <a href="{{ route('siswa.real-materi.result', $attempt) }}" class="btn btn-secondary btn-sm" style="padding: 4px 10px; font-size: 0.75rem;">
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
                    <div style="text-align: center; padding: 36px 20px; color: var(--color-gray-400);">
                        <p style="font-size: 2rem; margin-bottom: 8px;"><i class="fi fi-rr-document-signed" style="color: var(--color-gray-300);"></i></p>
                        <p style="font-weight: 500; color: var(--color-gray-600);">Anda belum pernah mengerjakan ujian seleksi.</p>
                        <a href="{{ route('siswa.real-materi.index') }}" class="btn btn-primary btn-sm" style="margin-top: 10px;">
                            Pilih Paket Seleksi Sekarang
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Column: Info & Tips -->
    <div style="display: flex; flex-direction: column; gap: 32px;">
        <!-- Radar Chart Card -->
        <div class="card">
            <div class="card-header">
                <h3>Analisis Capaian Kompetensi Seleksi</h3>
            </div>
            <div class="card-body" style="padding: 20px; text-align: center;">
                @if($totalSeleksiTaken > 0)
                    <div style="position: relative; height: 230px; width: 100%;">
                        <canvas id="pillarRadarChart"></canvas>
                    </div>
                @else
                    <div style="padding: 36px 20px; color: var(--color-gray-400); font-size: 0.9rem;">
                        <span style="font-size: 2.2rem; display: block; margin-bottom: 12px; color: var(--color-gray-300);"><i class="fi fi-rr-chart-radar"></i></span>
                        Belum ada data seleksi untuk dianalisis. Selesaikan paket seleksi untuk melihat grafik kemampuan Anda.
                    </div>
                @endif
            </div>
        </div>

        <!-- Empat Pilar Overview Card -->
        <div class="card" style="background: radial-gradient(circle at 10% 20%, rgba(211, 47, 47, 0.04) 0%, rgba(255, 193, 7, 0.02) 90%);">
            <div class="card-header" style="background: none; border: none; padding-bottom: 0;">
                <h3>Empat Pilar Kebangsaan</h3>
            </div>
            <div class="card-body" style="display: flex; flex-direction: column; gap: 12px; font-size: 0.9rem; line-height: 1.5; color: var(--color-gray-600);">
                <p>Empat Pilar Kebangsaan merupakan empat landasan utama dalam menjaga keutuhan dan keberlanjutan Negara Kesatuan Republik Indonesia:</p>
                <div style="display: flex; flex-direction: column; gap: 8px; margin-top: 4px;">
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
                <div style="margin-top: 8px; padding: 12px; background: rgba(0,0,0,0.03); border-radius: var(--border-radius-sm); font-size: 0.8rem; color: var(--color-gray-600);">
                    <i class="fi fi-rr-info" style="color: var(--color-primary); margin-right: 4px;"></i>
                    Integritas ujian seleksi diawasi secara digital. Dilarang berpindah jendela atau tab saat pengerjaan.
                </div>
            </div>
        </div>
    </div>
</div>

@if($totalSeleksiTaken > 0)
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('pillarRadarChart').getContext('2d');
        new Chart(ctx, {
            type: 'radar',
            data: {
                labels: ['Pancasila', 'UUD 1945', 'NKRI', 'Bhinneka'],
                datasets: [{
                    label: 'Skor Rata-rata',
                    data: [
                        {{ $chartData['pancasila'] }},
                        {{ $chartData['uud_1945'] }},
                        {{ $chartData['nkri'] }},
                        {{ $chartData['bhinneka_tunggal_ika'] }}
                    ],
                    backgroundColor: 'rgba(211, 47, 47, 0.15)',
                    borderColor: 'rgba(211, 47, 47, 0.8)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgba(211, 47, 47, 1)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(211, 47, 47, 1)',
                    pointRadius: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    r: {
                        angleLines: {
                            display: true,
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        suggestedMin: 0,
                        suggestedMax: 100,
                        ticks: {
                            stepSize: 20,
                            backdropColor: 'transparent',
                            color: 'var(--color-gray-400)',
                            font: {
                                size: 9
                            }
                        },
                        pointLabels: {
                            color: 'var(--color-dark)',
                            font: {
                                family: 'Outfit, sans-serif',
                                weight: '600',
                                size: 11
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
                                return ' Rata-rata: ' + context.formattedValue + ' / 100';
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endif
@endsection
