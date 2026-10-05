@extends('layouts.admin')

@section('title', 'Detail Siswa: ' . $student->name . ' - Admin Empat Pilar')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h1>Rapor & Evaluasi Siswa</h1>
        <p>Analisis capaian kompetensi Empat Pilar Kebangsaan dan rekam jejak evaluasi.</p>
    </div>
    <div class="page-actions" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
        <button type="button" class="btn" onclick="document.getElementById('retestModal').style.display='flex'" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white; display: inline-flex; align-items: center; gap: 6px; font-weight: 600;">
            <i class="fi fi-rr-refresh"></i> Izinkan Tes Ulang (Reset Sesi)
        </button>
        <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">
            ← Kembali ke Daftar Siswa
        </a>
    </div>
</div>

@if(session('success'))
    <div style="background: #ecfdf5; border: 1px solid #10b981; color: #065f46; padding: 12px 18px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
        <i class="fi fi-rr-check-circle" style="font-size: 1.2rem;"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if($student->is_troubled || $student->trouble_notes)
    <div style="background: #fff7ed; border: 1px solid #f97316; color: #9a3412; padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: flex-start; gap: 12px;">
        <i class="fi fi-rr-exclamation" style="font-size: 1.3rem; margin-top: 2px; color: #ea580c;"></i>
        <div>
            <strong style="font-size: 0.95rem;">Catatan Kendala Jaringan / Teknis:</strong>
            <p style="margin: 4px 0 0 0; font-size: 0.88rem;">{{ $student->trouble_notes ?? 'Sekolah mengalami kendala jaringan saat sesi ujian.' }}</p>
        </div>
    </div>
@endif

<!-- Student Header Card -->
<div class="card" style="background: linear-gradient(135deg, var(--color-dark), var(--color-dark-light)); color: var(--color-white); border: none;">
    <div class="card-body" style="padding: 32px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 24px;">
        <div style="display: flex; align-items: center; gap: 24px;">
            <img src="{{ $student->image_url }}" alt="{{ $student->school_name ?? $student->name }}" style="width: 84px; height: 84px; border-radius: 50%; object-fit: cover; border: 3px solid rgba(255,255,255,0.4); box-shadow: 0 4px 12px rgba(0,0,0,0.2); flex-shrink: 0; background: #fff;">
            <div>
                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <h2 style="color: var(--color-white); font-size: 1.75rem; margin-bottom: 0;">{{ $student->school_name ?? $student->name }}</h2>
                    <span class="badge" style="background: rgba(37, 99, 235, 0.4); color: #93c5fd; border: 1px solid #3b82f6;">{{ $student->class_name ?? 'Tim 10 Siswa' }}</span>
                </div>
                <p style="color: var(--color-gray-300); font-size: 0.95rem; margin-top: 6px;">
                    Wilayah: <strong>{{ $student->regency->name ?? '-' }}, {{ $student->province->name ?? '-' }}</strong>
                    @if($student->dapil)
                        | Dapil: <span class="badge" style="background-color: var(--color-secondary); color: var(--color-dark); font-weight: 700; font-size: 0.75rem; vertical-align: middle;">{{ $student->dapil }}</span>
                    @endif
                </p>
                <div style="color: var(--color-gray-300); font-size: 0.88rem; margin-top: 6px; display: flex; gap: 16px; flex-wrap: wrap;">
                    <span><i class="fi fi-rr-user"></i> PIC: <strong>{{ $student->pic_name ?? '-' }}</strong></span>
                    @if($student->whatsapp)
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $student->whatsapp) }}" target="_blank" style="color: #6ee7b7; text-decoration: none;">
                            <i class="fi fi-rr-phone-call"></i> WA: <strong>{{ $student->whatsapp }}</strong>
                        </a>
                    @endif
                    <span><i class="fi fi-rr-envelope"></i> {{ $student->email }}</span>
                </div>
                @if($student->address)
                    <p style="color: var(--color-gray-300); font-size: 0.85rem; margin-top: 6px; display: flex; align-items: flex-start; gap: 4px;">
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
                <span style="font-size: 0.8rem; color: var(--color-gray-300); text-transform: uppercase; letter-spacing: 0.5px;">Seleksi Selesai</span>
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
        <h3 class="card-title">Riwayat Pengerjaan Ujian Seleksi</h3>
    </div>
    <div class="card-body" style="padding: 0;">
        <table class="table">
            <thead>
                <tr>
                    <th>Paket Seleksi</th>
                    <th>Pilar Kebangsaan</th>
                    <th>Waktu Mulai</th>
                    <th>Durasi</th>
                    <th>Jawaban Benar</th>
                    <th>Skor Akhir</th>
                    <th>Integritas Layar</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attempts as $attempt)
                    <tr>
                        <td style="font-weight: 600;">{{ $attempt->quiz->title ?? '-' }}</td>
                        <td>
                            <span class="badge {{ $attempt->quiz->pillar ?? 'pancasila' }}">{{ $attempt->quiz->formatted_pillar ?? '-' }}</span>
                        </td>
                        <td>{{ $attempt->created_at ? $attempt->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') : '-' }} WIB</td>
                        <td>
                            {{ $attempt->duration_seconds_taken ? ceil($attempt->duration_seconds_taken / 60) . ' Menit' : '-' }}
                        </td>
                        <td>
                            {{ $attempt->correct_answers }} / {{ $attempt->total_questions }} Soal
                        </td>
                        <td>
                            <span style="font-weight: 700; font-size: 1.05rem; color: {{ $attempt->score >= 75 ? 'var(--color-success)' : 'var(--color-danger)' }};">
                                {{ $attempt->score }}
                            </span>
                        </td>
                        <td>
                            @if(($attempt->violations_count ?? 0) === 0)
                                <span class="badge badge-success" title="Pengerjaan bersih tanpa beralih layar">
                                    <i class="fi fi-rr-shield-check"></i> 0 Pelanggaran
                                </span>
                            @elseif(($attempt->violations_count ?? 0) < 3)
                                <span class="badge" style="background: rgba(245, 158, 11, 0.15); color: #d97706; border: 1px solid #fde68a;" title="Terdeteksi beralih jendela/tab {{ $attempt->violations_count }} kali">
                                    ⚠️ {{ $attempt->violations_count }}x Beralih
                                </span>
                            @else
                                <span class="badge" style="background: rgba(239, 68, 68, 0.15); color: #dc2626; border: 1px solid #fecaca;" title="Diskualifikasi / Auto-Submit mencapai 3 kali pelanggaran">
                                    ⛔ {{ $attempt->violations_count }}x Auto-Submit
                                </span>
                            @endif
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
                        <td colspan="8" class="empty-state">
                            <i class="fi fi-rr-document" style="font-size: 2rem; color: var(--color-gray-400);"></i>
                            <p style="margin-top: 8px;">Siswa ini belum pernah mengerjakan ujian seleksi.</p>
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

<!-- Modal Izinkan Tes Ulang -->
<div id="retestModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 9999; justify-content: center; align-items: center; padding: 20px;">
    <div style="background: white; border-radius: 12px; width: 100%; max-width: 520px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2); overflow: hidden; animation: modalPop 0.2s ease-out;">
        <div style="background: linear-gradient(135deg, #f59e0b, #d97706); padding: 18px 24px; color: white; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                <i class="fi fi-rr-refresh"></i> Konfirmasi Izin Tes Ulang
            </h3>
            <button type="button" onclick="document.getElementById('retestModal').style.display='none'" style="background: none; border: none; color: white; font-size: 1.2rem; cursor: pointer; line-height: 1;">✕</button>
        </div>
        <form action="{{ route('admin.students.grant-retest', $student) }}" method="POST" style="padding: 24px;">
            @csrf
            <p style="font-size: 0.9rem; color: #475569; margin-top: 0; line-height: 1.5;">
                Anda akan memberikan izin sesi <strong>Tes Ulang (Susulan)</strong> untuk sekolah <strong>{{ $student->school_name ?? $student->name }}</strong>.
            </p>
            <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 12px; margin-bottom: 18px; font-size: 0.83rem; color: #92400e;">
                ⚠️ <strong>Perhatian:</strong> Riwayat jawaban sebelumnya akan direset sehingga tim sekolah dapat memulai tes kembali dengan set soal yang diacak ulang. Notifikasi persetujuan resmi juga akan dikirimkan ke email PIC sekolah.
            </div>

            <div style="margin-bottom: 20px;">
                <label for="reason" style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 6px;">
                    Alasan Izin Tes Ulang <span style="color: #ef4444;">*</span>
                </label>
                <textarea name="reason" id="reason" rows="3" required class="form-control" style="width: 100%; border-radius: 8px; border: 1px solid #cbd5e1; padding: 10px; font-size: 0.88rem; box-sizing: border-box;" placeholder="Contoh: Terjadi gangguan jaringan internet di wilayah NTT selama 40 menit pada saat tes berlangsung."></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="document.getElementById('retestModal').style.display='none'" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn" style="background: #d97706; color: white; font-weight: 600;">Ya, Berikan Izin Tes Ulang</button>
            </div>
        </form>
    </div>
</div>
@endsection
