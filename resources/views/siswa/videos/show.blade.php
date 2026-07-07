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

            <!-- YouTube Direct Link Notification and Button -->
            <div style="margin-top: 20px; padding: 16px; background-color: rgba(220, 38, 38, 0.04); border: 1px solid rgba(220, 38, 38, 0.15); border-radius: var(--border-radius-md); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
                <div style="display: flex; align-items: center; gap: 12px; min-width: 280px; flex: 1;">
                    <div style="background-color: rgba(220, 38, 38, 0.1); width: 42px; height: 42px; border-radius: var(--border-radius-full); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="fi fi-rr-play-alt" style="color: #dc2626; font-size: 1.2rem; display: flex; align-items: center; justify-content: center;"></i>
                    </div>
                    <div>
                        <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--color-dark); margin: 0 0 2px 0; font-family: var(--font-heading);">
                            Video tidak dapat diputar atau "tidak tersedia"?
                        </h4>
                        <p style="font-size: 0.85rem; color: var(--color-gray-600); margin: 0; line-height: 1.4;">
                            Beberapa pencipta membatasi video untuk diputar langsung di situs web lain. Klik tombol di samping untuk menonton langsung di YouTube.
                        </p>
                    </div>
                </div>
                <div>
                    <a href="{{ $material->video_url }}" target="_blank" class="btn btn-primary" style="background: linear-gradient(135deg, #ff0000, #cc0000); border: none; color: var(--color-white); box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3); font-size: 0.85rem; padding: 10px 18px; display: inline-flex; align-items: center; gap: 8px; font-weight: 700; white-space: nowrap; border-radius: var(--border-radius-sm); text-decoration: none;">
                        <span>Tonton di YouTube</span>
                        <i class="fi fi-rr-play-alt" style="font-size: 0.85rem; color: var(--color-white); transform: scale(0.9);"></i>
                    </a>
                </div>
            </div>
        @else
            <div style="background-color: var(--color-gray-100); border-radius: var(--border-radius-md); border: 2px dashed var(--color-gray-300); padding: 50px 20px; text-align: center; color: var(--color-gray-400);">
                <i class="fi fi-rr-video-slash" style="font-size: 3rem; margin-bottom: 12px;"></i>
                <p>Tautan video tidak valid atau tidak didukung.</p>
                <p style="font-size: 0.85rem; margin-top: 4px; margin-bottom: 16px;">URL: {{ $material->video_url }}</p>
                <a href="{{ $material->video_url }}" target="_blank" class="btn btn-secondary" style="border: 1px solid var(--color-gray-300); text-decoration: none;">
                    <i class="fi fi-rr-play-alt" style="color: #dc2626; margin-right: 4px; vertical-align: middle;"></i> Tonton Langsung di YouTube
                </a>
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
