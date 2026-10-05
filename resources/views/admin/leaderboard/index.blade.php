@extends('layouts.admin')

@section('title', 'Papan Peringkat Siswa - Admin Empat Pilar')

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
    <div class="page-title">
        <h1>🏆 Papan Peringkat Siswa</h1>
        <p>Pantau akumulasi skor seleksi, perolehan poin, dan peringkat siswa se-Indonesia secara realtime berdasarkan Provinsi dan Kabupaten/Kota.</p>
    </div>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <a href="{{ route('admin.leaderboard.export-top9', request()->query()) }}" class="btn" style="background: linear-gradient(135deg, #10b981, #059669); color: white; display: inline-flex; align-items: center; gap: 8px; font-weight: 600; box-shadow: 0 2px 6px rgba(16, 185, 129, 0.3);">
            <i class="fi fi-rr-trophy"></i> 🏆 Ekspor Top 9 Lolos (.CSV)
        </a>
        <a href="{{ route('admin.leaderboard.export-spreadsheet', request()->query()) }}" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px; font-weight: 600;">
            <i class="fi fi-rr-document"></i> Ekspor Spreadsheet Lengkap (.CSV)
        </a>
        <a href="{{ route('admin.students.report') }}" target="_blank" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px;">
            <i class="fi fi-rr-print"></i> PDF Laporan
        </a>
    </div>
</div>

<!-- Stats KPI Cards -->
<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); margin-bottom: 24px;">
    <div class="stat-card">
        <div class="stat-icon primary"><i class="fi fi-rr-users-alt"></i></div>
        <div class="stat-info">
            <span class="stat-value">{{ $totalRankedStudents }}</span>
            <span class="stat-label">Siswa Terdaftar</span>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon warning" style="background-color: rgba(245, 158, 11, 0.1); color: #d97706;">
            <i class="fi fi-rr-trophy"></i>
        </div>
        <div class="stat-info">
            <span class="stat-value">{{ $highestPoints }}</span>
            <span class="stat-label">Poin Tertinggi</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon success"><i class="fi fi-rr-chart-histogram"></i></div>
        <div class="stat-info">
            <span class="stat-value">{{ $averageScore }}%</span>
            <span class="stat-label">Rata-rata Nilai Siswa</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon info"><i class="fi fi-rr-diploma"></i></div>
        <div class="stat-info">
            <span class="stat-value">{{ $totalRealMateri }}</span>
            <span class="stat-label">Total Paket Seleksi</span>
        </div>
    </div>
</div>

<!-- Regional Filter & Search Bar -->
<div class="card" style="margin-bottom: 28px; border: 1px solid var(--color-gray-200);">
    <div class="card-body" style="padding: 20px 24px;">
        <form action="{{ route('admin.leaderboard') }}" method="GET" id="admin-leaderboard-filter-form" style="display: flex; gap: 14px; align-items: flex-end; flex-wrap: wrap;">
            <!-- Filter Provinsi -->
            <div style="flex: 1.2; min-width: 220px;">
                <label for="filter_province_id" style="font-size: 0.82rem; font-weight: 600; color: var(--color-dark); margin-bottom: 6px; display: block;">
                    <i class="fi fi-rr-map-marker" style="color: rgb(var(--color-primary-rgb));"></i> Wilayah Provinsi
                </label>
                <select name="province_id" id="filter_province_id" class="form-control" onchange="onAdminProvinceFilterChange(this.value)">
                    <option value="">🇮🇩 Seluruh Indonesia (Nasional)</option>
                    @foreach($provinces as $prov)
                        <option value="{{ $prov->id }}" {{ (string)$selectedProvinceId === (string)$prov->id ? 'selected' : '' }}>
                            {{ $prov->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Kabupaten / Kota -->
            <div style="flex: 1.2; min-width: 220px;">
                <label for="filter_regency_id" style="font-size: 0.82rem; font-weight: 600; color: var(--color-dark); margin-bottom: 6px; display: block;">
                    <i class="fi fi-rr-building" style="color: rgb(var(--color-primary-rgb));"></i> Kabupaten / Kota
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

            <!-- Search Field -->
            <div style="flex: 1.5; min-width: 220px;">
                <label for="search" style="font-size: 0.82rem; font-weight: 600; color: var(--color-dark); margin-bottom: 6px; display: block;">
                    <i class="fi fi-rr-search" style="color: rgb(var(--color-primary-rgb));"></i> Cari Siswa / Sekolah
                </label>
                <input type="text" name="search" id="search" class="form-control" placeholder="Ketik nama siswa atau sekolah..." value="{{ $search }}">
            </div>

            <!-- Toggle Top 9 Only -->
            <div style="min-width: 170px; display: flex; align-items: flex-end; padding-bottom: 2px;">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 0.82rem; font-weight: 700; color: {{ !empty($filterTop9) ? '#065f46' : 'var(--color-dark)' }}; margin: 0; background: {{ !empty($filterTop9) ? '#d1fae5' : '#f8fafc' }}; padding: 9px 14px; border-radius: 8px; border: 1px solid {{ !empty($filterTop9) ? '#10b981' : 'var(--color-gray-300)' }}; transition: all 0.2s;">
                    <input type="checkbox" name="top9_only" value="1" {{ !empty($filterTop9) ? 'checked' : '' }} onchange="document.getElementById('admin-leaderboard-filter-form').submit()">
                    <span>🏆 Top 9 Lolos Saja</span>
                </label>
            </div>

            <!-- Actions -->
            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn btn-primary" style="padding: 10px 20px; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fi fi-rr-filter"></i> Terapkan
                </button>
                @if($selectedProvinceId || $selectedRegencyId || $search)
                    <a href="{{ route('admin.leaderboard') }}" class="btn btn-secondary" style="padding: 10px 16px; display: inline-flex; align-items: center; gap: 6px;" title="Reset Filter">
                        <i class="fi fi-rr-refresh"></i> Reset
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Podium Top 3 (Jika Ada Siswa) -->
@if($topThree->count() > 0 && !$search)
<div class="card" style="margin-bottom: 28px; background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%); border: 1px solid var(--color-gray-200); overflow: hidden;">
    <div class="card-header" style="text-align: center; border-bottom: 1px solid var(--color-gray-100); padding: 16px 20px;">
        <h3 style="font-size: 1.1rem; color: var(--color-dark); margin: 0; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fi fi-rr-crown" style="color: #f59e0b;"></i> Podium Kehormatan (Top 3)
        </h3>
    </div>
    <div class="card-body" style="padding: 30px 20px 20px 20px;">
        <div style="display: flex; justify-content: center; align-items: flex-end; gap: 20px; max-width: 800px; margin: 0 auto; flex-wrap: wrap;">
            
            <!-- JUARA 2 (SILVER) -->
            @if($topThree->count() >= 2)
            @php $second = $topThree->get(1); @endphp
            <div style="flex: 1; min-width: 180px; max-width: 220px; display: flex; flex-direction: column; align-items: center; text-align: center;">
                <div style="position: relative; margin-bottom: 12px;">
                    <img src="{{ $second->image_url }}" alt="{{ $second->name }}" style="width: 72px; height: 72px; border-radius: 50%; object-fit: cover; border: 3px solid #94a3b8; box-shadow: 0 4px 10px rgba(0,0,0,0.1); background: #fff;">
                    <div style="position: absolute; bottom: -8px; left: 50%; transform: translateX(-50%); background: #94a3b8; color: #fff; font-weight: 800; font-size: 0.75rem; padding: 2px 10px; border-radius: 12px; white-space: nowrap; box-shadow: 0 2px 4px rgba(0,0,0,0.15);">
                        🥈 JUARA 2
                    </div>
                </div>
                <div style="font-weight: 700; color: var(--color-dark); font-size: 0.95rem; margin-top: 6px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;">
                    {{ $second->name }}
                </div>
                <div style="font-size: 0.75rem; color: var(--color-gray-500); margin-bottom: 6px;">{{ $second->school_name ?? '-' }}</div>
                <div style="background: rgba(148, 163, 184, 0.15); border-radius: 8px 8px 0 0; width: 100%; padding: 18px 10px; margin-top: 8px; border: 1px solid #cbd5e1; border-bottom: none;">
                    <div style="font-size: 1.25rem; font-weight: 800; color: #475569;">{{ $second->points }}</div>
                    <div style="font-size: 0.7rem; color: #64748b; font-weight: 600; text-transform: uppercase;">Total Poin</div>
                    <div style="font-size: 0.75rem; color: var(--color-gray-500); margin-top: 4px;">Rerata {{ $second->average_score }}%</div>
                </div>
            </div>
            @endif

            <!-- JUARA 1 (GOLD) -->
            @php $first = $topThree->get(0); @endphp
            <div style="flex: 1.1; min-width: 200px; max-width: 240px; display: flex; flex-direction: column; align-items: center; text-align: center; order: -1;">
                <div style="position: relative; margin-bottom: 12px;">
                    <div style="position: absolute; top: -20px; left: 50%; transform: translateX(-50%); font-size: 1.5rem; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));">
                        👑
                    </div>
                    <img src="{{ $first->image_url }}" alt="{{ $first->name }}" style="width: 86px; height: 86px; border-radius: 50%; object-fit: cover; border: 4px solid #f59e0b; box-shadow: 0 6px 14px rgba(245, 158, 11, 0.3); background: #fff;">
                    <div style="position: absolute; bottom: -8px; left: 50%; transform: translateX(-50%); background: linear-gradient(135deg, #f59e0b, #d97706); color: #fff; font-weight: 800; font-size: 0.8rem; padding: 3px 12px; border-radius: 12px; white-space: nowrap; box-shadow: 0 2px 6px rgba(217, 119, 6, 0.4);">
                        🥇 JUARA 1
                    </div>
                </div>
                <div style="font-weight: 800; color: var(--color-dark); font-size: 1.05rem; margin-top: 6px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;">
                    {{ $first->name }}
                </div>
                <div style="font-size: 0.8rem; color: var(--color-gray-500); margin-bottom: 6px;">{{ $first->school_name ?? '-' }}</div>
                <div style="background: rgba(245, 158, 11, 0.15); border-radius: 10px 10px 0 0; width: 100%; padding: 28px 10px 20px 10px; margin-top: 8px; border: 2px solid #fcd34d; border-bottom: none;">
                    <div style="font-size: 1.5rem; font-weight: 800; color: #b45309;">{{ $first->points }}</div>
                    <div style="font-size: 0.72rem; color: #92400e; font-weight: 700; text-transform: uppercase;">Total Poin</div>
                    <div style="font-size: 0.78rem; color: var(--color-gray-600); margin-top: 4px;">Rerata {{ $first->average_score }}%</div>
                </div>
            </div>

            <!-- JUARA 3 (BRONZE) -->
            @if($topThree->count() >= 3)
            @php $third = $topThree->get(2); @endphp
            <div style="flex: 1; min-width: 180px; max-width: 220px; display: flex; flex-direction: column; align-items: center; text-align: center;">
                <div style="position: relative; margin-bottom: 12px;">
                    <img src="{{ $third->image_url }}" alt="{{ $third->name }}" style="width: 72px; height: 72px; border-radius: 50%; object-fit: cover; border: 3px solid #b45309; box-shadow: 0 4px 10px rgba(0,0,0,0.1); background: #fff;">
                    <div style="position: absolute; bottom: -8px; left: 50%; transform: translateX(-50%); background: #b45309; color: #fff; font-weight: 800; font-size: 0.75rem; padding: 2px 10px; border-radius: 12px; white-space: nowrap; box-shadow: 0 2px 4px rgba(0,0,0,0.15);">
                        🥉 JUARA 3
                    </div>
                </div>
                <div style="font-weight: 700; color: var(--color-dark); font-size: 0.95rem; margin-top: 6px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;">
                    {{ $third->name }}
                </div>
                <div style="font-size: 0.75rem; color: var(--color-gray-500); margin-bottom: 6px;">{{ $third->school_name ?? '-' }}</div>
                <div style="background: rgba(180, 83, 9, 0.12); border-radius: 8px 8px 0 0; width: 100%; padding: 14px 10px; margin-top: 8px; border: 1px solid #fed7aa; border-bottom: none;">
                    <div style="font-size: 1.25rem; font-weight: 800; color: #9a3412;">{{ $third->points }}</div>
                    <div style="font-size: 0.7rem; color: #9a3412; font-weight: 600; text-transform: uppercase;">Total Poin</div>
                    <div style="font-size: 0.75rem; color: var(--color-gray-500); margin-top: 4px;">Rerata {{ $third->average_score }}%</div>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endif

<!-- Full Leaderboard Table -->
<div class="card" style="border: 1px solid var(--color-gray-200);">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; padding: 18px 24px;">
        <div>
            <h3 style="margin-bottom: 2px;">Daftar Peringkat Siswa Lengkap</h3>
            <div style="font-size: 0.8rem; color: var(--color-gray-500);">
                @if($selectedRegencyId && $regencies->firstWhere('id', $selectedRegencyId))
                    Menampilkan peringkat untuk wilayah: <strong style="color: rgb(var(--color-primary-rgb));">{{ $regencies->firstWhere('id', $selectedRegencyId)->name }}</strong>
                @elseif($selectedProvinceId && $provinces->firstWhere('id', $selectedProvinceId))
                    Menampilkan peringkat untuk Provinsi: <strong style="color: rgb(var(--color-primary-rgb));">{{ $provinces->firstWhere('id', $selectedProvinceId)->name }}</strong>
                @else
                    Menampilkan peringkat: <strong style="color: rgb(var(--color-primary-rgb));">Nasional (Seluruh Indonesia)</strong>
                @endif
                @if($search)
                    — Hasil pencarian: <em>"{{ $search }}"</em>
                @endif
            </div>
        </div>
        <span style="font-size: 0.8rem; color: var(--color-gray-500); font-weight: 500;">
            Sistem Skor: Akumulasi Nilai Ujian Seleksi Resmi
        </span>
    </div>

    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table table-hover" style="margin-bottom: 0; vertical-align: middle;">
                <thead>
                    <tr style="background-color: var(--color-gray-100);">
                        <th style="width: 80px; text-align: center;">Peringkat</th>
                        <th style="width: 140px; text-align: center;">Status Seleksi</th>
                        <th>Sekolah / Akun Tim</th>
                        <th>Guru Pembina (PIC)</th>
                        <th>Asal Wilayah</th>
                        <th style="text-align: center;">Seleksi Selesai</th>
                        <th style="text-align: center;">Rerata Nilai</th>
                        <th style="text-align: center; width: 110px;">Total Skor</th>
                        <th style="text-align: center; width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leaderboard as $index => $student)
                        @php
                            $rank = $student->calculated_rank ?? ($index + 1);
                        @endphp
                        <tr style="{{ $rank <= 3 ? 'background-color: rgba(254, 243, 199, 0.2);' : ($rank <= 9 ? 'background-color: rgba(209, 250, 229, 0.15);' : '') }}">
                            <td style="text-align: center;">
                                @if($rank == 1)
                                    <div style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white; width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; font-weight: 800; font-size: 0.9rem; box-shadow: 0 2px 6px rgba(245, 158, 11, 0.4);">
                                        🥇
                                    </div>
                                @elseif($rank == 2)
                                    <div style="background: linear-gradient(135deg, #94a3b8, #64748b); color: white; width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; font-weight: 800; font-size: 0.9rem; box-shadow: 0 2px 6px rgba(100, 116, 139, 0.3);">
                                        🥈
                                    </div>
                                @elseif($rank == 3)
                                    <div style="background: linear-gradient(135deg, #b45309, #78350f); color: white; width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; font-weight: 800; font-size: 0.9rem; box-shadow: 0 2px 6px rgba(180, 83, 9, 0.3);">
                                        🥉
                                    </div>
                                @else
                                    <span style="font-size: 0.95rem; font-weight: 700; color: var(--color-gray-500);">
                                        #{{ $rank }}
                                    </span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                @if($rank <= 9)
                                    <span class="badge" style="background-color: #d1fae5; color: #065f46; font-weight: 800; font-size: 0.76rem; padding: 4px 10px; border-radius: 20px; border: 1px solid #6ee7b7; display: inline-flex; align-items: center; gap: 4px;">
                                        ⭐ TOP 9 LOLOS
                                    </span>
                                @elseif($rank <= 11)
                                    <span class="badge" style="background-color: #fef3c7; color: #92400e; font-weight: 700; font-size: 0.74rem; padding: 4px 10px; border-radius: 20px; border: 1px solid #fcd34d;">
                                        CADANGAN
                                    </span>
                                @else
                                    <span class="badge" style="background-color: #f1f5f9; color: #64748b; font-weight: 600; font-size: 0.74rem; padding: 4px 8px; border-radius: 20px;">
                                        Peserta
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <img src="{{ $student->image_url }}" alt="{{ $student->school_name ?? $student->name }}" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid var(--color-gray-200); background: #f8fafc; flex-shrink: 0;">
                                    <div>
                                        <div style="font-weight: 700; color: var(--color-dark); font-size: 0.92rem;">
                                            {{ $student->school_name ?? $student->name }}
                                        </div>
                                        <div style="font-size: 0.78rem; color: var(--color-gray-500);">
                                            {{ $student->email }} • <span style="color: #2563eb;">{{ $student->class_name ?? 'Tim 10 Siswa' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: var(--color-dark); font-size: 0.85rem;">
                                    {{ $student->pic_name ?? '-' }}
                                </div>
                                @if($student->whatsapp)
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $student->whatsapp) }}" target="_blank" style="font-size: 0.76rem; color: #059669; text-decoration: none; display: inline-flex; align-items: center; gap: 3px;">
                                        <i class="fi fi-rr-phone-call"></i> {{ $student->whatsapp }}
                                    </a>
                                @else
                                    <span style="font-size: 0.75rem; color: var(--color-gray-400);">-</span>
                                @endif
                            </td>
                            <td>
                                @if($student->regency || $student->province)
                                    <div style="font-weight: 600; font-size: 0.85rem; color: var(--color-dark);">
                                        {{ $student->regency->name ?? '-' }}
                                    </div>
                                    <div style="font-size: 0.72rem; color: var(--color-gray-500); text-transform: uppercase;">
                                        {{ $student->province->name ?? '-' }}
                                    </div>
                                @elseif($student->dapil)
                                    <span class="badge" style="background-color: rgba(37, 99, 235, 0.1); color: #2563eb; font-weight: 600; font-size: 0.78rem; padding: 4px 10px; border-radius: 20px;">
                                        <i class="fi fi-rr-map-marker" style="margin-right: 3px; font-size: 0.75rem;"></i> {{ $student->dapil }}
                                    </span>
                                @else
                                    <span style="color: var(--color-gray-400); font-size: 0.82rem;">-</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <span class="badge" style="background-color: rgba(16, 185, 129, 0.1); color: #059669; font-weight: 600; font-size: 0.82rem; padding: 4px 10px; border-radius: 20px;">
                                    <i class="fi fi-rr-check" style="font-size: 0.7rem; margin-right: 2px;"></i> {{ $student->completed_seleksi_count }}/{{ $totalRealMateri }} Paket
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <div style="font-weight: 700; font-size: 0.95rem; color: {{ $student->average_score >= 70 ? 'var(--color-success)' : ($student->average_score > 0 ? 'var(--color-danger)' : 'var(--color-gray-400)') }};">
                                    {{ $student->average_score > 0 ? $student->average_score . '%' : '-' }}
                                </div>
                                <div style="font-size: 0.72rem; color: var(--color-gray-500);">
                                    {{ $student->quizzes_count }}x Ujian
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <div style="font-size: 1.15rem; font-weight: 800; color: rgb(var(--color-primary-rgb));">
                                    {{ $student->points }}
                                </div>
                                <div style="font-size: 0.7rem; color: var(--color-gray-400); font-weight: 600; text-transform: uppercase;">
                                    Poin
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <a href="{{ route('admin.students.show', $student) }}" class="btn btn-secondary btn-sm" style="padding: 6px 12px; font-weight: 500; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px;">
                                    <i class="fi fi-rr-eye"></i> Rapor
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 48px 20px;">
                                <div style="display: flex; flex-direction: column; align-items: center; gap: 8px;">
                                    <i class="fi fi-rr-search-alt" style="font-size: 2.4rem; color: var(--color-gray-400);"></i>
                                    <h4 style="color: var(--color-dark); margin: 0;">Tidak Ada Siswa Ditemukan</h4>
                                    <p style="color: var(--color-gray-500); font-size: 0.85rem; margin: 0;">Belum ada siswa terdaftar pada wilayah yang dipilih atau filter yang dimasukkan.</p>
                                    <a href="{{ route('admin.leaderboard') }}" class="btn btn-secondary btn-sm" style="margin-top: 8px;">
                                        <i class="fi fi-rr-refresh"></i> Tampilkan Semua Siswa Nasional
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Preloaded regencies data for zero-latency client filtering
    const allRegenciesData = @json($allRegencies);

    function onAdminProvinceFilterChange(provinceId) {
        const regencySelect = document.getElementById('filter_regency_id');
        regencySelect.innerHTML = '<option value="">Semua Kabupaten / Kota</option>';

        if (!provinceId) {
            regencySelect.disabled = true;
            return;
        }

        const filtered = allRegenciesData.filter(r => String(r.province_id) === String(provinceId));
        filtered.forEach(r => {
            const opt = document.createElement('option');
            opt.value = r.id;
            opt.textContent = r.name;
            regencySelect.appendChild(opt);
        });

        regencySelect.disabled = false;
    }
</script>
@endsection
