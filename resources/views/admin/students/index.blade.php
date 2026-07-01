@extends('layouts.admin')

@section('title', 'Pemantauan Siswa - Admin')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h1>Pemantauan Siswa</h1>
        <p>Pantau kemajuan baca materi dan pencapaian skor kuis kewarganegaraan siswa.</p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Daftar Siswa Aktif</h3>
    </div>
    <div class="card-body">
        @if($students->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Sekolah</th>
                            <th style="width: 250px;">Progres Membaca</th>
                            <th>Kuis Diikuti</th>
                            <th>Rata-rata Skor</th>
                            <th style="width: 120px; text-align: center;">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                            <tr>
                                <td style="font-weight: 600; color: var(--color-dark);">
                                    {{ $student->name }}
                                </td>
                                <td>{{ $student->class_name }}</td>
                                <td>{{ $student->school_name }}</td>
                                <td>
                                    <!-- Reading progress bar -->
                                    @php
                                        $progressPercent = $totalMaterialsCount > 0 
                                            ? round(($student->completed_progress_count / $totalMaterialsCount) * 100) 
                                            : 0;
                                    @endphp
                                    <div style="display: flex; flex-direction: column; gap: 4px;">
                                        <div class="progress-container" style="margin-bottom: 0; height: 8px;">
                                            <div class="progress-bar-fill" style="width: {{ $progressPercent }}%;"></div>
                                        </div>
                                        <span style="font-size: 0.75rem; color: var(--color-gray-600); font-weight: 500;">
                                            {{ $student->completed_progress_count }}/{{ $totalMaterialsCount }} Selesai ({{ $progressPercent }}%)
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <span style="font-weight: 600;">{{ $student->total_quizzes_taken }}</span> Kali
                                </td>
                                <td>
                                    @if($student->average_score !== '-')
                                        <span style="font-weight: 700; font-size: 1.05rem; color: {{ $student->average_score >= 70 ? 'var(--color-success)' : 'var(--color-danger)' }}">
                                            {{ $student->average_score }}
                                        </span>
                                    @else
                                        <span style="color: var(--color-gray-400);">Belum ujian</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="text-align: center;">
                                        <a href="{{ route('admin.students.show', $student) }}" class="btn btn-secondary btn-sm" style="padding: 6px 12px; font-weight: 500;">
                                            <i class="fi fi-rr-eye"></i> Lihat Rapor
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="text-align: center; padding: 60px 20px; color: var(--color-gray-400);">
                <p style="font-size: 2.5rem; margin-bottom: 12px;"><i class="fi fi-rr-box-open" style="color: var(--color-gray-400); font-size: 2.5rem;"></i></p>
                <p>Belum ada siswa yang mendaftar di platform.</p>
            </div>
        @endif
    </div>
</div>
@endsection
