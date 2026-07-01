@extends('layouts.siswa')

@section('title', 'Materi Belajar - Empat Pilar')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h1>Modul Belajar Empat Pilar</h1>
        <p>Silakan pilih materi di bawah untuk mulai membaca dan memahami pilar kebangsaan kita.</p>
    </div>
</div>

@php
    $pillarsInfo = [
        'pancasila' => ['title' => 'Pancasila', 'desc' => 'Dasar Negara dan Ideologi Bangsa', 'icon_class' => 'fi fi-rr-shield', 'color' => '#ff5252'],
        'uud_1945' => ['title' => 'UUD NRI 1945', 'desc' => 'Hukum Dasar Tertulis Tertinggi Konstitusi', 'icon_class' => 'fi fi-rr-scroll', 'color' => '#ffd740'],
        'nkri' => ['title' => 'NKRI', 'desc' => 'Negara Kesatuan Republik Indonesia', 'icon_class' => 'fi fi-rr-map', 'color' => '#40c4ff'],
        'bhinneka_tunggal_ika' => ['title' => 'Bhinneka Tunggal Ika', 'desc' => 'Harmoni Keberagaman Semboyan Bangsa', 'icon_class' => 'fi fi-rr-handshake', 'color' => '#69f0ae']
    ];
@endphp

<div style="display: flex; flex-direction: column; gap: 40px;">
    @foreach($groupedMaterials as $pillar => $materialsList)
        <div>
            <!-- Pillar Header -->
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px; border-bottom: 2px solid var(--color-gray-200); padding-bottom: 12px;">
                <i class="{{ $pillarsInfo[$pillar]['icon_class'] }}" style="font-size: 1.75rem; color: {{ $pillarsInfo[$pillar]['color'] }}; line-height: 1;"></i>
                <div>
                    <h2 style="font-size: 1.5rem; color: var(--color-dark);">{{ $pillarsInfo[$pillar]['title'] }}</h2>
                    <p style="color: var(--color-gray-600); font-size: 0.85rem; font-weight: 500;">{{ $pillarsInfo[$pillar]['desc'] }}</p>
                </div>
            </div>

            <!-- Materials Grid -->
            @if(count($materialsList) > 0)
                <div class="pillars-grid">
                    @foreach($materialsList as $material)
                        <div class="card" style="transition: var(--transition-smooth); border: 1px solid var(--color-gray-200); display: flex; flex-direction: column; justify-content: space-between;">
                            <div class="card-body" style="padding: 24px; display: flex; flex-direction: column; gap: 12px; flex: 1;">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                    <span class="badge {{ $material->pillar }}" style="font-size: 0.7rem;"><i class="fi fi-rr-clock" style="margin-right: 4px; font-size: 0.75rem; vertical-align: middle;"></i>{{ $material->read_time }} Menit Baca</span>
                                    @if($material->is_completed)
                                        <span class="badge completed" style="font-size: 0.7rem;">Selesai</span>
                                    @else
                                        <span class="badge pending" style="font-size: 0.7rem;">Belum Baca</span>
                                    @endif
                                </div>
                                <h3 style="font-size: 1.1rem; color: var(--color-dark); font-weight: 600; line-height: 1.4; flex-grow: 1;">
                                    {{ $material->title }}
                                </h3>
                            </div>
                            <div style="padding: 16px 24px; border-top: 1px solid var(--color-gray-100); background-color: var(--color-gray-100);">
                                <a href="{{ route('siswa.materials.show', $material) }}" class="btn btn-secondary btn-sm" style="width: 100%; justify-content: center; font-weight: 600;">
                                    Mulai Baca
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div style="background-color: var(--color-white); border-radius: var(--border-radius-md); padding: 30px; text-align: center; border: 1px solid var(--color-gray-200); color: var(--color-gray-400);">
                    <p style="font-size: 1.5rem; margin-bottom: 4px;"><i class="fi fi-rr-box-open" style="color: var(--color-gray-400); font-size: 1.5rem;"></i></p>
                    <p>Materi untuk pilar ini belum ditambahkan oleh Admin.</p>
                </div>
            @endif
        </div>
    @endforeach
</div>
@endsection
