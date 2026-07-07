@extends('layouts.siswa')

@section('title', $material->title . ' - Video Pembelajaran')

@section('content')
<div class="page-header" style="border: none; padding-bottom: 0;">
    <a href="{{ route('siswa.videos.index') }}" class="btn btn-secondary btn-sm">
        ← Daftar Video
    </a>
</div>

<div class="reader-container">
    <!-- Header -->
    <header class="reader-header" style="background: linear-gradient(135deg, var(--color-dark-light), var(--color-dark));">
        <span class="badge {{ $material->pillar }}" style="background-color: var(--color-white); color: var(--color-dark); margin-bottom: 12px; font-weight: 700;">
            {{ $material->formatted_pillar }}
        </span>
        <h1>{{ $material->title }}</h1>
        <div class="reader-meta">
            <span><i class="fi fi-rr-clock" style="margin-right: 4px; vertical-align: middle;"></i>Durasi: <strong>{{ $material->read_time }} menit</strong></span>
            <span><i class="fi fi-rr-calendar" style="margin-right: 4px; vertical-align: middle;"></i>Diperbarui: <strong>{{ $material->updated_at->format('d M Y') }}</strong></span>
        </div>
    </header>

    <!-- Body Content with Video Player -->
    <div style="padding: 30px 48px 0 48px;">
        @if($material->youtube_embed_url)
            <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 100%; border-radius: var(--border-radius-md); box-shadow: var(--shadow-md); background-color: var(--color-black);">
                <iframe src="{{ $material->youtube_embed_url }}" 
                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0;" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                        allowfullscreen>
                </iframe>
            </div>
        @else
            <div style="background-color: var(--color-gray-100); border-radius: var(--border-radius-md); border: 2px dashed var(--color-gray-300); padding: 50px 20px; text-align: center; color: var(--color-gray-400);">
                <i class="fi fi-rr-video-slash" style="font-size: 3rem; margin-bottom: 12px;"></i>
                <p>Tautan video tidak valid atau tidak didukung.</p>
                <p style="font-size: 0.85rem; margin-top: 4px;">URL: {{ $material->video_url }}</p>
            </div>
        @endif
    </div>

    <article class="reader-body" style="padding-top: 24px;">
        <h3 style="margin-top: 0; margin-bottom: 12px; font-size: 1.25rem;">Deskripsi Video</h3>
        {!! $material->content !!}
    </article>

    <!-- Footer Action -->
    <footer class="reader-footer">
        <div>
            @if($isCompleted)
                <span style="color: var(--color-success); font-weight: 600; display: flex; align-items: center; gap: 8px;">
                    <i class="fi fi-rr-checkbox" style="color: var(--color-success); font-size: 1.25rem; vertical-align: middle; margin-right: 4px;"></i> Anda sudah menyelesaikan menonton video ini.
                </span>
            @else
                <span style="color: var(--color-gray-600); font-weight: 500;">
                    Sudah selesai menonton? Tandai video ini agar tercatat dalam rapor progres Anda.
                </span>
            @endif
        </div>
        
        <div>
            @if(!$isCompleted)
                <form action="{{ route('siswa.videos.complete', $material) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary" style="background-color: var(--color-success); color: var(--color-white);">
                        Tandai Selesai Menonton
                    </button>
                </form>
            @else
                <button class="btn btn-secondary" disabled style="cursor: not-allowed; opacity: 0.6;">
                    Selesai Menonton
                </button>
            @endif
        </div>
    </footer>
</div>
@endsection
