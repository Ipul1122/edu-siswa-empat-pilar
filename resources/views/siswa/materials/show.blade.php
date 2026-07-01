@extends('layouts.siswa')

@section('title', $material->title . ' - Belajar Empat Pilar')

@section('content')
<div class="page-header" style="border: none; padding-bottom: 0;">
    <a href="{{ route('siswa.materials.index') }}" class="btn btn-secondary btn-sm">
        ← Daftar Modul
    </a>
</div>

<div class="reader-container">
    <!-- Header -->
    <header class="reader-header">
        <span class="badge {{ $material->pillar }}" style="background-color: var(--color-white); color: var(--color-dark); margin-bottom: 12px; font-weight: 700;">
            {{ $material->formatted_pillar }}
        </span>
        <h1>{{ $material->title }}</h1>
        <div class="reader-meta">
            <span><i class="fi fi-rr-clock" style="margin-right: 4px; vertical-align: middle;"></i>Estimasi Baca: <strong>{{ $material->read_time }} menit</strong></span>
            <span><i class="fi fi-rr-calendar" style="margin-right: 4px; vertical-align: middle;"></i>Diperbarui: <strong>{{ $material->updated_at->format('d M Y') }}</strong></span>
        </div>
    </header>

    <!-- Body Content -->
    <article class="reader-body">
        {!! $material->content !!}
    </article>

    <!-- Footer Action -->
    <footer class="reader-footer">
        <div>
            @if($isCompleted)
                <span style="color: var(--color-success); font-weight: 600; display: flex; align-items: center; gap: 8px;">
                    <i class="fi fi-rr-checkbox" style="color: var(--color-success); font-size: 1.25rem; vertical-align: middle; margin-right: 4px;"></i> Anda sudah menandai materi ini selesai dibaca.
                </span>
            @else
                <span style="color: var(--color-gray-600); font-weight: 500;">
                    Sudah selesai membaca? Tandai materi ini agar tercatat dalam rapor progres Anda.
                </span>
            @endif
        </div>
        
        <div>
            @if(!$isCompleted)
                <form action="{{ route('siswa.materials.complete', $material) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary" style="background-color: var(--color-success); color: var(--color-white);">
                        Tandai Selesai Dibaca
                    </button>
                </form>
            @else
                <button class="btn btn-secondary" disabled style="cursor: not-allowed; opacity: 0.6;">
                    Selesai Dibaca
                </button>
            @endif
        </div>
    </footer>
</div>
@endsection
