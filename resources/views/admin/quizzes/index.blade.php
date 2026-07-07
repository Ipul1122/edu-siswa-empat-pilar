@extends('layouts.admin')

@section('title', 'Manajemen Kuis - Admin')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h1>Manajemen Kuis</h1>
        <p>Kelola kuis evaluasi untuk masing-masing Pilar Kebangsaan.</p>
    </div>
    <a href="{{ route('admin.quizzes.create') }}" class="btn btn-primary">
        <i class="fi fi-rr-plus"></i> Buat Kuis Baru
    </a>
</div>

<div class="card">
    <div class="card-body">
        @if($quizzes->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Pilar</th>
                            <th>Judul Kuis</th>
                            <th>Tipe</th>
                            <th>Jumlah Soal</th>
                            <th>Durasi</th>
                            <th style="width: 250px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quizzes as $quiz)
                            <tr>
                                <td>
                                    <span class="badge {{ $quiz->pillar }}">
                                        {{ str_replace('_', ' ', $quiz->pillar) }}
                                    </span>
                                </td>
                                <td style="font-weight: 600; color: var(--color-dark);">
                                    {{ $quiz->title }}
                                </td>
                                <td>
                                    @if($quiz->type === 'real')
                                        <span class="badge admin" style="background-color: #d32f2f; color: white;">Real Materi</span>
                                    @else
                                        <span class="badge student" style="background-color: #0288d1; color: white;">Latihan Kuis</span>
                                    @endif
                                </td>
                                <td>
                                    <span style="font-weight: 600;">{{ $quiz->questions_count }}</span> Soal
                                </td>
                                <td><i class="fi fi-rr-clock" style="margin-right: 4px; font-size: 0.85rem; vertical-align: middle;"></i>{{ $quiz->duration_minutes }} Menit</td>
                                <td>
                                    <div style="display: flex; gap: 8px; justify-content: center;">
                                        <a href="{{ route('admin.quizzes.show', $quiz) }}" class="btn btn-primary btn-sm" style="padding: 6px 10px; background-color: var(--color-info);">
                                            <i class="fi fi-rr-eye"></i> Soal
                                        </a>
                                        <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="btn btn-secondary btn-sm" style="padding: 6px 10px;">
                                            <i class="fi fi-rr-pencil"></i> Edit
                                        </a>
                                        <form action="{{ route('admin.quizzes.destroy', $quiz) }}" method="POST" class="delete-confirm-form" data-item-type="kuis">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" style="padding: 6px 10px;">
                                                <i class="fi fi-rr-trash"></i> Hapus
                                            </button>
                                        </form>
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
                <h3>Belum Ada Kuis</h3>
                <p style="margin-top: 4px; margin-bottom: 20px;">Silakan buat kuis pertama untuk memulai asesmen siswa.</p>
                <a href="{{ route('admin.quizzes.create') }}" class="btn btn-primary">Buat Kuis Pertama</a>
            </div>
        @endif
    </div>
</div>
@endsection
