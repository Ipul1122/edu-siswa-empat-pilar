@extends('layouts.admin')

@section('title', 'Manajemen Materi Video - Admin')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h1>Manajemen Materi Video</h1>
        <p>Kelola bahan pembelajaran berupa video interaktif Empat Pilar Kebangsaan untuk siswa.</p>
    </div>
    <a href="{{ route('admin.videos.create') }}" class="btn btn-primary">
        <i class="fi fi-rr-plus"></i> Tambah Materi Video
    </a>
</div>

<div class="card">
    <div class="card-body">
        @if($materials->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Pilar Kebangsaan</th>
                            <th>Judul Video</th>
                            <th>Durasi Video</th>
                            <th>Tautan Video</th>
                            <th>Tanggal Dibuat</th>
                            <th style="width: 150px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($materials as $video)
                            <tr>
                                <td>
                                    <span class="badge {{ $video->pillar }}">
                                        {{ str_replace('_', ' ', $video->pillar) }}
                                    </span>
                                </td>
                                <td style="font-weight: 600; color: var(--color-dark);">
                                    {{ $video->title }}
                                </td>
                                <td><i class="fi fi-rr-clock" style="margin-right: 4px; font-size: 0.85rem; vertical-align: middle;"></i>{{ $video->read_time }} Menit</td>
                                <td style="font-size: 0.85rem; color: var(--color-gray-600); max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <a href="{{ $video->video_url }}" target="_blank" style="text-decoration: underline;">
                                        <i class="fi fi-rr-link" style="margin-right: 4px;"></i>Buka Tautan
                                    </a>
                                </td>
                                <td style="font-size: 0.85rem; color: var(--color-gray-600);">
                                    {{ $video->created_at->format('d M Y') }}
                                </td>
                                <td>
                                    <div style="display: flex; gap: 8px; justify-content: center;">
                                        <a href="{{ route('admin.videos.edit', $video) }}" class="btn btn-secondary btn-sm" style="padding: 6px 10px;">
                                            <i class="fi fi-rr-pencil"></i> Edit
                                        </a>
                                        <form action="{{ route('admin.videos.destroy', $video) }}" method="POST" class="delete-confirm-form" data-item-type="video">
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
                <p style="font-size: 2.5rem; margin-bottom: 12px;"><i class="fi fi-rr-video-slash" style="color: var(--color-gray-400); font-size: 2.5rem;"></i></p>
                <h3>Belum Ada Materi Video</h3>
                <p style="margin-top: 4px; margin-bottom: 20px;">Silakan tambahkan video pertama untuk mendukung pembelajaran interaktif siswa.</p>
                <a href="{{ route('admin.videos.create') }}" class="btn btn-primary">Tambah Video Pertama</a>
            </div>
        @endif
    </div>
</div>
@endsection
