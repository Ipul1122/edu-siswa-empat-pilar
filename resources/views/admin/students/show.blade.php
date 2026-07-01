@extends('layouts.admin')

@section('title', 'Rapor Aktivitas Siswa - Admin')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h1>Detail Aktivitas Siswa</h1>
        <p>Rapor perkembangan dan pencapaian akademik: <strong>{{ $student->name }}</strong></p>
    </div>
    <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">
        ← Kembali
    </a>
</div>

<!-- Student Header Card -->
<div class="card" style="background: linear-gradient(135deg, var(--color-dark), var(--color-dark-light)); color: var(--color-white); border: none;">
    <div class="card-body" style="padding: 32px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 24px;">
        <div style="display: flex; align-items: center; gap: 24px;">
            <div style="width: 80px; height: 80px; background: rgba(255,255,255,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid rgba(255,255,255,0.2);">
                <i class="fi fi-rr-user" style="color: var(--color-white); font-size: 2.2rem; line-height: 1;"></i>
            </div>
            <div>
                <h2 style="color: var(--color-white); font-size: 1.75rem; margin-bottom: 4px;">{{ $student->name }}</h2>
                <p style="color: var(--color-gray-300); font-size: 0.95rem;">
                    Kelas: <strong>{{ $student->class_name }}</strong> | Sekolah: <strong>{{ $student->school_name }}</strong>
                </p>
                <p style="color: var(--color-gray-400); font-size: 0.85rem; margin-top: 4px;">Email: {{ $student->email }} | Terdaftar sejak: {{ $student->created_at->format('d M Y') }}</p>
            </div>
        </div>
        
        <div style="display: flex; gap: 32px;">
            <div style="text-align: center;">
                <div style="font-family: var(--font-heading); font-size: 2.25rem; font-weight: 700; color: var(--color-secondary);">
                    {{ $averageScore }}
                </div>
                <div style="font-size: 0.75rem; color: var(--color-gray-300); text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">
                    Rata-rata Skor Kuis
                </div>
            </div>
            
            <div style="text-align: center;">
                <div style="font-family: var(--font-heading); font-size: 2.25rem; font-weight: 700; color: var(--color-info);">
                    {{ $attempts->count() }}
                </div>
                <div style="font-size: 0.75rem; color: var(--color-gray-300); text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">
                    Ujian Diikuti
                </div>
            </div>
        </div>
    </div>
</div>

<div class="dashboard-grid" style="margin-top: 8px;">
    <!-- Reading Progress Details -->
    <div class="card">
        <div class="card-header">
            <h3>Daftar Progres Baca Materi</h3>
        </div>
        <div class="card-body" style="padding: 16px;">
            @if($materials->count() > 0)
                <div class="table-responsive">
                    <table class="table" style="font-size: 0.95rem;">
                        <thead>
                            <tr>
                                <th>Pilar</th>
                                <th>Judul Materi</th>
                                <th>Status Membaca</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($materials as $material)
                                <tr>
                                    <td>
                                        <span class="badge {{ $material->pillar }}" style="font-size: 0.7rem;">
                                            {{ str_replace('_', ' ', $material->pillar) }}
                                        </span>
                                    </td>
                                    <td style="font-weight: 500;">{{ $material->title }}</td>
                                    <td>
                                        @if($material->is_completed_by_student)
                                            <span class="badge completed" style="font-size: 0.7rem;">Selesai Dibaca</span>
                                        @else
                                            <span class="badge pending" style="font-size: 0.7rem;">Belum Dibaca</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p style="text-align: center; color: var(--color-gray-400); padding: 20px;">Belum ada materi pelajaran.</p>
            @endif
        </div>
    </div>

    <!-- Quiz Attempts Details -->
    <div class="card">
        <div class="card-header">
            <h3>Riwayat Pengerjaan Kuis</h3>
        </div>
        <div class="card-body" style="padding: 16px;">
            @if($attempts->count() > 0)
                <div style="display: flex; flex-direction: column; gap: 16px; max-height: 500px; overflow-y: auto; padding: 4px;">
                    @foreach($attempts as $attempt)
                        <div style="border: 1px solid var(--color-gray-200); border-radius: var(--border-radius-sm); padding: 16px; background-color: var(--color-white);">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                <h4 style="font-size: 0.95rem; font-weight: 600;">{{ $attempt->quiz->title }}</h4>
                                <span style="font-weight: 700; font-size: 1.15rem; color: {{ $attempt->score >= 70 ? 'var(--color-success)' : 'var(--color-danger)' }}">
                                    {{ $attempt->score }}
                                </span>
                            </div>
                            
                            <div style="display: flex; flex-wrap: wrap; gap: 12px; font-size: 0.8rem; color: var(--color-gray-600); margin-top: 4px;">
                                <span><i class="fi fi-rr-checkbox" style="font-size: 0.8rem; margin-right: 2px; vertical-align: middle;"></i> {{ $attempt->correct_answers }}/{{ $attempt->total_questions }} Benar</span>
                                <span><i class="fi fi-rr-clock" style="font-size: 0.8rem; margin-right: 2px; vertical-align: middle;"></i> {{ sprintf('%02d:%02d', floor($attempt->duration_seconds_taken / 60), $attempt->duration_seconds_taken % 60) }}</span>
                                <span><i class="fi fi-rr-calendar" style="font-size: 0.8rem; margin-right: 2px; vertical-align: middle;"></i> {{ $attempt->created_at->format('d M Y') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="text-align: center; padding: 40px 20px; color: var(--color-gray-400);">
                    <p style="font-size: 2rem; margin-bottom: 8px;"><i class="fi fi-rr-box-open" style="color: var(--color-gray-400); font-size: 2rem;"></i></p>
                    <p>Siswa belum pernah mengerjakan kuis evaluasi.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
