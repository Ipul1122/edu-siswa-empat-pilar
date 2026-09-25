@extends('layouts.siswa')

@section('title', 'Papan Peringkat - Empat Pilar')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h1>🏆 Papan Peringkat Siswa</h1>
        <p>Lihat peringkat Anda dan bersainglah secara sehat dengan teman-teman Anda untuk mengamalkan nilai Empat Pilar Kebangsaan!</p>
    </div>
</div>

<!-- Personal Performance Ribbon -->
@if($currentUserRank && $currentUserData)
    <div class="card" style="margin-bottom: 32px; background: linear-gradient(135deg, var(--color-primary) 0%, #c62828 100%); color: var(--color-white); border: none; box-shadow: var(--box-shadow-md);">
        <div class="card-body" style="padding: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
            <div style="display: flex; align-items: center; gap: 16px;">
                <div style="background-color: rgba(255,255,255,0.2); width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; font-weight: 800;">
                    #{{ $currentUserRank }}
                </div>
                <div>
                    <h3 style="color: var(--color-white); font-size: 1.2rem; margin-bottom: 4px;">Peringkat Anda saat ini</h3>
                    <p style="color: rgba(255,255,255,0.85); font-size: 0.9rem;">Terus tingkatkan nilai kuis dan pelajari semua materi!</p>
                </div>
            </div>
            <div style="display: flex; gap: 24px;">
                <div style="text-align: center;">
                    <div style="font-size: 1.5rem; font-weight: 700; color: #ffd740;">{{ $currentUserData->points }}</div>
                    <div style="font-size: 0.75rem; color: rgba(255,255,255,0.85); font-weight: 500; text-transform: uppercase;">Total Poin</div>
                </div>
                <div style="text-align: center; border-left: 1px solid rgba(255,255,255,0.2); padding-left: 24px;">
                    <div style="font-size: 1.5rem; font-weight: 700;">{{ $currentUserData->materials_read }}</div>
                    <div style="font-size: 0.75rem; color: rgba(255,255,255,0.85); font-weight: 500; text-transform: uppercase;">Materi Dibaca</div>
                </div>
                <div style="text-align: center; border-left: 1px solid rgba(255,255,255,0.2); padding-left: 24px;">
                    <div style="font-size: 1.5rem; font-weight: 700;">{{ $currentUserData->average_score }}%</div>
                    <div style="font-size: 0.75rem; color: rgba(255,255,255,0.85); font-weight: 500; text-transform: uppercase;">Rata-rata Nilai</div>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Regional Filter Bar (Provinsi & Kabupaten/Kota) -->
<div class="card" style="margin-bottom: 24px;">
    <div class="card-body" style="padding: 18px 24px;">
        <form action="{{ route('siswa.leaderboard') }}" method="GET" id="leaderboard-filter-form" style="display: flex; gap: 14px; align-items: flex-end; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 220px;">
                <label for="filter_province_id" style="font-size: 0.82rem; font-weight: 600; color: var(--color-dark); margin-bottom: 6px; display: block;">
                    <i class="fi fi-rr-map-marker" style="color: rgb(var(--color-primary-rgb));"></i> Filter Provinsi
                </label>
                <select name="province_id" id="filter_province_id" class="form-control" onchange="onProvinceFilterChange(this.value)">
                    <option value="">🇮🇩 Seluruh Indonesia (Nasional)</option>
                    @foreach($provinces as $prov)
                        <option value="{{ $prov->id }}" {{ (string)$selectedProvinceId === (string)$prov->id ? 'selected' : '' }}>
                            {{ $prov->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="flex: 1; min-width: 220px;">
                <label for="filter_regency_id" style="font-size: 0.82rem; font-weight: 600; color: var(--color-dark); margin-bottom: 6px; display: block;">
                    <i class="fi fi-rr-building" style="color: rgb(var(--color-primary-rgb));"></i> Filter Kabupaten / Kota
                </label>
                <select name="regency_id" id="filter_regency_id" class="form-control" {{ $selectedProvinceId ? '' : 'disabled' }}>
                    <option value="">Semua Kabupaten / Kota</option>
                    @foreach($regencies as $reg)
                        <option value="{{ $reg->id }}" {{ (string)$selectedRegencyId === (string)$reg->id ? 'selected' : '' }}>
                            {{ $reg->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn btn-primary" style="padding: 10px 20px;">
                    <i class="fi fi-rr-filter"></i> Terapkan
                </button>
                @if($selectedProvinceId || $selectedRegencyId)
                    <a href="{{ route('siswa.leaderboard') }}" class="btn btn-secondary" style="padding: 10px 16px;" title="Reset Filter">
                        <i class="fi fi-rr-refresh"></i> Reset
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Leaderboard List -->
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
        <div>
            <h3 style="margin-bottom: 2px;">Daftar Peringkat Belajar</h3>
            <div style="font-size: 0.8rem; color: var(--color-gray-500);">
                @if($selectedRegencyId && $regencies->firstWhere('id', $selectedRegencyId))
                    Menampilkan peringkat untuk wilayah: <strong style="color: rgb(var(--color-primary-rgb));">{{ $regencies->firstWhere('id', $selectedRegencyId)->name }}</strong>
                @elseif($selectedProvinceId && $provinces->firstWhere('id', $selectedProvinceId))
                    Menampilkan peringkat untuk Provinsi: <strong style="color: rgb(var(--color-primary-rgb));">{{ $provinces->firstWhere('id', $selectedProvinceId)->name }}</strong>
                @else
                    Menampilkan peringkat: <strong style="color: rgb(var(--color-primary-rgb));">Nasional (Seluruh Indonesia)</strong>
                @endif
            </div>
        </div>
        <span style="font-size: 0.8rem; color: var(--color-gray-500); font-weight: 500;">Sistem poin: (Materi Dibaca x 10 Poin) + Total Nilai Kuis</span>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table table-hover" style="margin-bottom: 0; vertical-align: middle;">
                <thead>
                    <tr style="background-color: var(--color-gray-100);">
                        <th style="width: 80px; text-align: center;">Peringkat</th>
                        <th>Siswa</th>
                        <th>Asal Wilayah</th>
                        <th>Kelas & Sekolah</th>
                        <th style="text-align: center;">Materi Dibaca</th>
                        <th style="text-align: center;">Rerata Nilai</th>
                        <th style="text-align: center; width: 120px;">Total Poin</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leaderboard as $index => $student)
                        @php $rank = $index + 1; @endphp
                        <tr style="{{ $student->id === Auth::id() ? 'background-color: rgba(239, 68, 68, 0.04); font-weight: 600;' : '' }}">
                            <td style="text-align: center;">
                                @if($rank === 1)
                                    <span style="font-size: 1.5rem; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));">🥇</span>
                                @elseif($rank === 2)
                                    <span style="font-size: 1.5rem; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));">🥈</span>
                                @elseif($rank === 3)
                                    <span style="font-size: 1.5rem; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));">🥉</span>
                                @else
                                    <span style="color: var(--color-gray-500); font-weight: 700;">#{{ $rank }}</span>
                                @endif
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="background-color: {{ $rank <= 3 ? 'var(--color-primary)' : 'var(--color-gray-300)' }}; color: var(--color-white); width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.85rem;">
                                        {{ strtoupper(substr($student->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <span style="color: var(--color-dark); font-weight: 600;">
                                            {{ $student->name }}
                                        </span>
                                        @if($student->id === Auth::id())
                                            <span class="badge" style="background-color: rgba(239, 68, 68, 0.1); color: var(--color-primary); font-size: 0.65rem; margin-left: 4px; padding: 2px 6px;">Anda</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="font-size: 0.85rem; font-weight: 600; color: var(--color-dark);">
                                    {{ $student->regency->name ?? '-' }}
                                </div>
                                <div style="font-size: 0.72rem; color: var(--color-gray-500); text-transform: uppercase;">
                                    {{ $student->province->name ?? ($student->dapil ?? '-') }}
                                </div>
                            </td>
                            <td>
                                <div style="font-size: 0.88rem; color: var(--color-gray-700);">Kelas {{ $student->class_name }}</div>
                                <div style="font-size: 0.75rem; color: var(--color-gray-500);">{{ $student->school_name }}</div>
                            </td>
                            <td style="text-align: center; font-size: 0.95rem; color: var(--color-gray-700);">
                                <span style="font-weight: 600;">{{ $student->materials_read }}</span> materi
                            </td>
                            <td style="text-align: center;">
                                <span style="font-weight: 700; color: {{ $student->average_score >= 70 ? 'var(--color-success)' : ($student->average_score > 0 ? 'var(--color-danger)' : 'var(--color-gray-400)') }}">
                                    {{ $student->average_score > 0 ? $student->average_score . '%' : '-' }}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge" style="background-color: {{ $rank <= 3 ? 'rgba(255, 193, 7, 0.15)' : 'var(--color-gray-200)' }}; color: {{ $rank <= 3 ? '#b7791f' : 'var(--color-gray-700)' }}; font-weight: 800; font-size: 0.95rem; padding: 6px 12px; border-radius: 20px;">
                                    {{ $student->points }} pts
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px 20px; color: var(--color-gray-500);">
                                <div style="font-size: 2rem; margin-bottom: 8px;">📍</div>
                                <p style="font-size: 0.95rem; font-weight: 500;">Belum ada peserta di wilayah yang dipilih.</p>
                                <a href="{{ route('siswa.leaderboard') }}" class="btn btn-secondary btn-sm" style="margin-top: 10px;">Lihat Peringkat Nasional</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    const allRegenciesData = @json($allRegencies);

    function onProvinceFilterChange(provId) {
        const regSelect = document.getElementById('filter_regency_id');
        regSelect.innerHTML = '<option value="">Semua Kabupaten / Kota</option>';

        if (!provId) {
            regSelect.disabled = true;
            return;
        }

        const filtered = allRegenciesData.filter(r => String(r.province_id) === String(provId));
        filtered.forEach(r => {
            const opt = document.createElement('option');
            opt.value = r.id;
            opt.textContent = r.name;
            regSelect.appendChild(opt);
        });

        regSelect.disabled = false;
    }
</script>
@endsection
