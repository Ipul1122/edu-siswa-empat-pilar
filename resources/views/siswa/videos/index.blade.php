@extends('layouts.siswa')

@section('title', 'Video Pembelajaran - Empat Pilar')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h1>Video Pembelajaran Empat Pilar</h1>
        <p>Tonton video penjelasan interaktif mengenai pilar kebangsaan Indonesia untuk memperkuat pemahaman Anda.</p>
    </div>
</div>

@php
    $pillarsInfo = [
        'pancasila' => [
            'title' => 'Pancasila', 
            'desc' => 'Materi video dasar negara dan ideologi bangsa', 
            'icon_class' => 'fi fi-rr-shield', 
            'color' => '#ff5252',
            'gradient' => 'linear-gradient(135deg, #ff5252, #c2185b)'
        ],
        'uud_1945' => [
            'title' => 'UUD NRI 1945', 
            'desc' => 'Materi video hukum dasar dan konstitusi negara', 
            'icon_class' => 'fi fi-rr-scroll', 
            'color' => '#ffd740',
            'gradient' => 'linear-gradient(135deg, #ffd740, #f57c00)'
        ],
        'nkri' => [
            'title' => 'NKRI', 
            'desc' => 'Materi video kesatuan negara dan kedaulatan wilayah', 
            'icon_class' => 'fi fi-rr-map', 
            'color' => '#40c4ff',
            'gradient' => 'linear-gradient(135deg, #40c4ff, #1976d2)'
        ],
        'bhinneka_tunggal_ika' => [
            'title' => 'Bhinneka Tunggal Ika', 
            'desc' => 'Materi video harmoni keberagaman dan toleransi', 
            'icon_class' => 'fi fi-rr-handshake', 
            'color' => '#69f0ae',
            'gradient' => 'linear-gradient(135deg, #69f0ae, #388e3c)'
        ],
        'twk_kedinasan' => [
            'title' => 'Simulasi TWK Kedinasan', 
            'desc' => 'Materi video Tes Wawasan Kebangsaan persiapan kedinasan & ujian sekolah', 
            'icon_class' => 'fi fi-rr-diploma', 
            'color' => '#8b5cf6',
            'gradient' => 'linear-gradient(135deg, #8b5cf6, #6d28d9)'
        ]
    ];
@endphp

<div style="display: flex; flex-direction: column; gap: 40px;">
    @foreach($groupedVideos as $pillar => $videosList)
        <div>
            <!-- Pillar Header -->
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px; border-bottom: 2px solid var(--color-gray-200); padding-bottom: 12px;">
                <i class="{{ $pillarsInfo[$pillar]['icon_class'] }}" style="font-size: 1.75rem; color: {{ $pillarsInfo[$pillar]['color'] }}; line-height: 1;"></i>
                <div>
                    <h2 style="font-size: 1.5rem; color: var(--color-dark);">{{ $pillarsInfo[$pillar]['title'] }}</h2>
                    <p style="color: var(--color-gray-600); font-size: 0.85rem; font-weight: 500;">{{ $pillarsInfo[$pillar]['desc'] }}</p>
                </div>
            </div>

            <!-- Videos Grid -->
            @if(count($videosList) > 0)
                <div class="pillars-grid">
                    @foreach($videosList as $video)
                        <div class="card" style="transition: var(--transition-smooth); border: 1px solid var(--color-gray-200); display: flex; flex-direction: column; overflow: hidden;">
                            
                            <!-- Custom CSS-based Thumbnail -->
                            <div style="height: 160px; background: {{ $pillarsInfo[$pillar]['gradient'] }}; position: relative; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                <!-- Overlay subtle dark grid pattern or decoration -->
                                <div style="position: absolute; inset: 0; background: rgba(0,0,0,0.15); display: flex; align-items: center; justify-content: center; transition: var(--transition-smooth);" class="thumbnail-overlay">
                                    <div style="width: 50px; height: 50px; border-radius: 50%; background: var(--color-white); display: flex; align-items: center; justify-content: center; box-shadow: var(--shadow-md); color: var(--color-dark); transition: var(--transition-smooth);" class="play-btn-circle">
                                        <i class="fi fi-rr-play-alt" style="font-size: 1.5rem; margin-left: 4px; display: inline-block;"></i>
                                    </div>
                                </div>
                                <div style="position: absolute; bottom: 12px; left: 12px; display: flex; gap: 6px; flex-wrap: wrap;">
                                    <span class="badge {{ $video->pillar }}" style="font-size: 0.65rem; background: rgba(255,255,255,0.9); color: var(--color-dark); box-shadow: var(--shadow-sm);">
                                        <i class="fi fi-rr-clock" style="margin-right: 4px; font-size: 0.75rem; vertical-align: middle;"></i>{{ $video->read_time }} Menit Durasi
                                    </span>
                                </div>
                            </div>

                            <div class="card-body" style="padding: 20px; display: flex; flex-direction: column; gap: 10px; flex: 1;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <span style="font-size: 0.75rem; font-weight: 600; color: var(--color-gray-600); text-transform: uppercase;">
                                        {{ $video->formatted_pillar }}
                                    </span>
                                    @if($video->is_completed)
                                        <span class="badge completed" style="font-size: 0.65rem;">Selesai</span>
                                    @else
                                        <span class="badge pending" style="font-size: 0.65rem;">Belum Ditonton</span>
                                    @endif
                                </div>
                                <h3 style="font-size: 1.05rem; color: var(--color-dark); font-weight: 600; line-height: 1.4; flex-grow: 1;">
                                    {{ $video->title }}
                                </h3>
                                <p style="font-size: 0.85rem; color: var(--color-gray-600); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.5; margin: 4px 0;">
                                    {!! strip_tags($video->content) !!}
                                </p>
                            </div>
                            <div style="padding: 16px 20px; border-top: 1px solid var(--color-gray-100); background-color: var(--color-gray-100);">
                                <a href="{{ route('siswa.videos.show', $video) }}" class="btn btn-secondary btn-sm" style="width: 100%; justify-content: center; font-weight: 600;">
                                    Tonton Video
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="background-color: var(--color-white); border-radius: var(--border-radius-md); padding: 30px; text-align: center; border: 1px solid var(--color-gray-200); color: var(--color-gray-400);">
                    <p style="font-size: 1.5rem; margin-bottom: 4px;"><i class="fi fi-rr-box-open" style="color: var(--color-gray-400); font-size: 1.5rem;"></i></p>
                    <p>Materi video untuk kategori ini belum ditambahkan oleh Admin.</p>
                </div>
            @endif
        </div>
    @endforeach
</div>

<style>
    .card:hover .thumbnail-overlay {
        background: rgba(0,0,0,0.3) !important;
    }
    .card:hover .play-btn-circle {
        transform: scale(1.15);
        background: rgb(var(--color-primary-rgb)) !important;
        color: var(--color-white) !important;
    }
</style>
@endsection
