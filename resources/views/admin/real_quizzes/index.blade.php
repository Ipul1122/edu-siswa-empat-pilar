@extends('layouts.admin')

@section('title', 'Manajemen Real Materi - Admin')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h1>Manajemen Real Materi</h1>
        <p>Kelola paket evaluasi resmi Real Materi. Setiap evaluasi hanya dapat dikerjakan 1 kali oleh siswa dan nilainya masuk Leaderboard.</p>
    </div>
    <a href="{{ route('admin.real-materi.create') }}" class="btn btn-primary">
        <i class="fi fi-rr-plus"></i> Tambah Real Materi
    </a>
</div>

<div class="card">
    <div class="card-body">
        @if($quizzes->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Kategori / Pilar</th>
                            <th>Judul Real Materi</th>
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
                                        {{ $quiz->formatted_pillar }}
                                    </span>
                                </td>
                                <td style="font-weight: 600; color: var(--color-dark);">
                                    {{ $quiz->title }}
                                </td>
                                <td>
                                    <span style="font-weight: 600;">{{ $quiz->questions_count }}</span> Soal
                                </td>
                                <td><i class="fi fi-rr-clock" style="margin-right: 4px; font-size: 0.85rem; vertical-align: middle;"></i>{{ $quiz->duration_minutes }} Menit</td>
                                <td>
                                    <div style="display: flex; gap: 8px; justify-content: center;">
                                        <a href="{{ route('admin.real-materi.show', $quiz) }}" class="btn btn-primary btn-sm" style="padding: 6px 10px; background-color: var(--color-info);">
                                            <i class="fi fi-rr-eye"></i> Soal
                                        </a>
                                        <a href="{{ route('admin.real-materi.edit', $quiz) }}" class="btn btn-secondary btn-sm" style="padding: 6px 10px;">
                                            <i class="fi fi-rr-pencil"></i> Edit
                                        </a>
                                        <form action="{{ route('admin.real-materi.destroy', $quiz) }}" method="POST" class="delete-confirm-form" data-item-type="real materi">
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
                <p style="font-size: 2.5rem; margin-bottom: 12px;"><i class="fi fi-rr-diploma" style="color: var(--color-gray-400); font-size: 2.5rem;"></i></p>
                <h3>Belum Ada Evaluasi Real Materi</h3>
                <p style="margin-top: 4px; margin-bottom: 20px;">Silakan buat paket evaluasi Real Materi pertama untuk siswa.</p>
                <a href="{{ route('admin.real-materi.create') }}" class="btn btn-primary">Buat Real Materi Pertama</a>
            </div>
        @endif
    </div>
</div>
@endsection
