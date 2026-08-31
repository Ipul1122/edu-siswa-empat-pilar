@extends('layouts.admin')

@section('title', 'Manajemen Materi Video - Admin')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h1>Manajemen Materi Video</h1>
        <p>Kelola bahan pembelajaran berupa video MP4 atau tautan video interaktif Empat Pilar Kebangsaan untuk siswa.</p>
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
                            <th>Kategori / Pilar</th>
                            <th>Judul Video</th>
                            <th>Tipe Media</th>
                            <th>Durasi</th>
                            <th>Tautan / File</th>
                            <th>Tanggal Dibuat</th>
                            <th style="width: 150px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($materials as $video)
                            <tr>
                                <td>
                                    <span class="badge {{ $video->pillar }}">
                                        {{ $video->formatted_pillar }}
                                    </span>
                                </td>
                                <td style="font-weight: 600; color: var(--color-dark);">
                                    {{ $video->title }}
                                </td>
                                <td>
                                    @if($video->is_direct_video || str_starts_with($video->video_url, 'storage/'))
                                        <span class="badge" style="background-color: rgba(37, 99, 235, 0.1); color: #2563eb; font-weight: 600;">
                                            <i class="fi fi-rr-file-video" style="margin-right: 4px; vertical-align: middle;"></i> File MP4
                                        </span>
                                    @else
                                        <span class="badge" style="background-color: rgba(220, 38, 38, 0.1); color: #dc2626; font-weight: 600;">
                                            <i class="fi fi-rr-play-alt" style="margin-right: 4px; vertical-align: middle;"></i> YouTube/URL
                                        </span>
                                    @endif
                                </td>
                                <td><i class="fi fi-rr-clock" style="margin-right: 4px; font-size: 0.85rem; vertical-align: middle;"></i>{{ $video->read_time }} Menit</td>
                                <td style="font-size: 0.85rem; color: var(--color-gray-600); max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <a href="{{ $video->playable_video_url }}" target="_blank" style="text-decoration: underline;">
                                        <i class="fi fi-rr-link" style="margin-right: 4px;"></i>{{ str_starts_with($video->video_url, 'storage/') ? basename($video->video_url) : 'Buka Link' }}
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
                <p style="margin-top: 4px; margin-bottom: 20px;">Silakan tambahkan video pertama (upload MP4 atau link YouTube) untuk siswa.</p>
                <a href="{{ route('admin.videos.create') }}" class="btn btn-primary">Tambah Video Pertama</a>
            </div>
        @endif
    </div>
</div>
@endsection
