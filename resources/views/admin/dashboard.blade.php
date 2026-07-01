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
<div class="stats-grid">
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
            <span class="stat-label">Total Materi</span>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon success"><i class="fi fi-rr-clipboard-list"></i></div>
        <div class="stat-info">
            <span class="stat-value">{{ $totalQuizzes }}</span>
            <span class="stat-label">Total Kuis</span>
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
@endsection
