@extends('layouts.admin')

@section('title', 'Pemantauan Siswa - Admin Empat Pilar')

@section('content')
<style>
    .filter-card {
        background-color: var(--color-white);
        border: 1px solid var(--color-gray-200);
        border-radius: var(--border-radius-lg);
        padding: 18px 20px;
        margin-bottom: 20px;
        box-shadow: var(--shadow-sm);
    }
    .filter-grid {
        display: grid;
        grid-template-columns: 2fr 1.5fr 1.2fr 1fr auto;
        gap: 12px;
        align-items: flex-end;
    }
    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .filter-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--color-gray-700);
    }
    .filter-input, .filter-select {
        height: 40px;
        padding: 6px 12px;
        border: 1.5px solid var(--color-gray-300);
        border-radius: var(--border-radius-md);
        font-size: 0.88rem;
        color: var(--color-dark);
        background-color: var(--color-white);
        outline: none;
        transition: var(--transition-smooth);
        width: 100%;
        box-sizing: border-box;
    }
    .filter-input:focus, .filter-select:focus {
        border-color: rgb(var(--color-primary-rgb));
        box-shadow: 0 0 0 3px rgba(var(--color-primary-rgb), 0.1);
    }
    .filter-actions {
        display: flex;
        gap: 8px;
    }
    
    /* Custom Pagination Styling */
    .pagination-wrapper {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        padding: 16px 20px;
        background-color: var(--color-white);
        border-top: 1px solid var(--color-gray-200);
    }
    .pagination-info {
        font-size: 0.85rem;
        color: var(--color-gray-600);
    }
    .pagination-info strong {
        color: var(--color-dark);
    }
    .custom-pagination {
        display: flex;
        align-items: center;
        list-style: none;
        margin: 0;
        padding: 0;
        gap: 6px;
    }
    .custom-pagination .page-item {
        margin: 0;
    }
    .custom-pagination .page-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        padding: 0 10px;
        border-radius: var(--border-radius-md);
        border: 1px solid var(--color-gray-300);
        background-color: var(--color-white);
        color: var(--color-gray-700);
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
        transition: var(--transition-smooth);
    }
    .custom-pagination .page-link:hover {
        background-color: var(--color-gray-100);
        border-color: var(--color-gray-400);
        color: var(--color-dark);
    }
    .custom-pagination .page-item.active .page-link {
        background-color: rgb(var(--color-primary-rgb));
        border-color: rgb(var(--color-primary-rgb));
        color: var(--color-white);
        box-shadow: 0 2px 6px rgba(var(--color-primary-rgb), 0.3);
    }
    .custom-pagination .page-item.disabled .page-link {
        background-color: var(--color-gray-100);
        border-color: var(--color-gray-200);
        color: var(--color-gray-400);
        cursor: not-allowed;
    }
    
    @media (max-width: 992px) {
        .filter-grid {
            grid-template-columns: 1fr 1fr;
        }
        .filter-actions {
            grid-column: span 2;
            justify-content: flex-end;
        }
    }
    @media (max-width: 576px) {
        .filter-grid {
            grid-template-columns: 1fr;
        }
        .filter-actions {
            grid-column: span 1;
        }
        .pagination-wrapper {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
    }
</style>

<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
    <div class="page-title">
        <h1>Pemantauan Data Siswa</h1>
        <p>Pantau data kependudukan (Dapil & Asal Sekolah), kemajuan materi, dan capaian skor evaluasi siswa.</p>
    </div>
    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
        <button type="button" class="btn" onclick="document.getElementById('broadcastModal').style.display='flex'" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; display: inline-flex; align-items: center; gap: 6px; font-weight: 600; box-shadow: 0 2px 6px rgba(59, 130, 246, 0.3);">
            <i class="fi fi-rr-bullhorn"></i> Blast Pengumuman (Email)
        </button>
        <a href="{{ route('admin.leaderboard') }}" class="btn btn-secondary" style="background-color: #fffbeb; border: 1px solid #fde68a; color: #b45309; display: inline-flex; align-items: center; gap: 6px;">
            <i class="fi fi-rr-trophy" style="color: #d97706;"></i> Papan Peringkat
        </a>
        <a href="{{ route('admin.students.export') }}" class="btn btn-secondary" style="background-color: var(--color-white); border: 1px solid var(--color-gray-300); color: var(--color-gray-700);">
            <i class="fi fi-rr-download" style="margin-right: 4px; vertical-align: middle;"></i> Ekspor CSV
        </a>
        <a href="{{ route('admin.students.report') }}" target="_blank" class="btn btn-primary">
            <i class="fi fi-rr-print" style="margin-right: 4px; vertical-align: middle;"></i> Cetak Laporan
        </a>
    </div>
</div>

@if(session('success'))
    <div style="background: #ecfdf5; border: 1px solid #10b981; color: #065f46; padding: 12px 18px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
        <i class="fi fi-rr-check-circle" style="font-size: 1.2rem;"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

<!-- Filter Toolbar (Search, Dapil, Sort, ASC/DESC, Per-Page) - Auto-apply on change -->
<div class="filter-card">
    <form method="GET" action="{{ route('admin.students.index') }}" id="filter-form">
        <div class="filter-grid">
            <!-- Search -->
            <div class="filter-group">
                <label for="search" class="filter-label"><i class="fi fi-rr-search"></i> Cari Siswa / Sekolah</label>
                <div style="position: relative;">
                    <input type="text" name="search" id="search" class="filter-input" placeholder="Cari nama, email, sekolah (Tekan Enter)..." value="{{ request('search') }}">
                    @if(request('search'))
                        <button type="button" onclick="document.getElementById('search').value=''; document.getElementById('filter-form').submit();" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--color-gray-400); cursor: pointer; font-size: 0.9rem;" title="Hapus pencarian">
                            ✕
                        </button>
                    @endif
                </div>
            </div>

            <!-- Filter by Province -->
            <div class="filter-group">
                <label for="admin_province_id" class="filter-label"><i class="fi fi-rr-map-marker"></i> Filter Provinsi</label>
                <select name="province_id" id="admin_province_id" class="filter-select" onchange="onAdminProvinceChange(this.value)">
                    <option value="">-- Semua Provinsi (38) --</option>
                    @foreach($provinces as $prov)
                        <option value="{{ $prov->id }}" {{ (string)request('province_id') === (string)$prov->id ? 'selected' : '' }}>
                            {{ $prov->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter by Regency / City -->
            <div class="filter-group">
                <label for="admin_regency_id" class="filter-label"><i class="fi fi-rr-building"></i> Filter Kab / Kota</label>
                <select name="regency_id" id="admin_regency_id" class="filter-select" {{ request('province_id') ? '' : 'disabled' }} onchange="document.getElementById('filter-form').submit()">
                    <option value="">-- Semua Kab / Kota --</option>
                    @foreach($regencies as $reg)
                        <option value="{{ $reg->id }}" {{ (string)request('regency_id') === (string)$reg->id ? 'selected' : '' }}>
                            {{ $reg->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Sort By Column -->
            <div class="filter-group">
                <label for="sort_by" class="filter-label"><i class="fi fi-rr-sort-alt"></i> Urutkan Berdasarkan</label>
                <select name="sort_by" id="sort_by" class="filter-select" onchange="document.getElementById('filter-form').submit()">
                    <option value="name" {{ ($sortBy ?? '') === 'name' ? 'selected' : '' }}>Nama Siswa</option>
                    <option value="school_name" {{ ($sortBy ?? '') === 'school_name' ? 'selected' : '' }}>Asal Sekolah</option>
                    <option value="average_score" {{ ($sortBy ?? '') === 'average_score' ? 'selected' : '' }}>Rerata Skor</option>
                    <option value="total_quizzes_taken" {{ ($sortBy ?? '') === 'total_quizzes_taken' ? 'selected' : '' }}>Total Ujian Seleksi</option>
                    <option value="completed_progress_count" {{ ($sortBy ?? '') === 'completed_progress_count' ? 'selected' : '' }}>Paket Seleksi Selesai</option>
                    <option value="created_at" {{ ($sortBy ?? '') === 'created_at' ? 'selected' : '' }}>Waktu Pendaftaran</option>
                </select>
            </div>

            <!-- Order (ASC - DESC) & Per Page -->
            <div class="filter-group">
                <label for="order" class="filter-label"><i class="fi fi-rr-exchange"></i> Arah & Baris</label>
                <div style="display: flex; gap: 6px;">
                    <select name="order" id="order" class="filter-select" style="flex: 1.2;" onchange="document.getElementById('filter-form').submit()">
                        <option value="asc" {{ ($order ?? 'asc') === 'asc' ? 'selected' : '' }}>ASC (A-Z ↑)</option>
                        <option value="desc" {{ ($order ?? '') === 'desc' ? 'selected' : '' }}>DESC (Z-A ↓)</option>
                    </select>
                    <select name="per_page" id="per_page" class="filter-select" style="flex: 0.8;" title="Jumlah data per halaman" onchange="document.getElementById('filter-form').submit()">
                        <option value="10" {{ ($perPage ?? 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ ($perPage ?? 10) == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ ($perPage ?? 10) == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ ($perPage ?? 10) == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
            </div>

            @if(request()->hasAny(['search', 'province_id', 'regency_id', 'dapil', 'sort_by', 'order', 'per_page']))
                <!-- Reset Button if filtered -->
                <div class="filter-actions" style="margin-bottom: 0;">
                    <a href="{{ route('admin.students.index') }}" class="btn btn-secondary" style="height: 40px; padding: 0 14px; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap;" title="Reset Filter ke Default">
                        <i class="fi fi-rr-rotate-left"></i> Reset
                    </a>
                </div>
            @endif
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <h3 class="card-title">
            Daftar Siswa (Total {{ $students->total() }} Siswa)
            @if(request('province_id') && $provinces->firstWhere('id', request('province_id')))
                <span class="badge" style="background-color: rgba(37, 99, 235, 0.1); color: #2563eb; font-weight: 600; font-size: 0.78rem; margin-left: 6px;">
                    Provinsi: {{ $provinces->firstWhere('id', request('province_id'))->name }}
                </span>
            @endif
            @if(request('regency_id') && $regencies->firstWhere('id', request('regency_id')))
                <span class="badge" style="background-color: rgba(16, 185, 129, 0.1); color: #10b981; font-weight: 600; font-size: 0.78rem; margin-left: 6px;">
                    Kab/Kota: {{ $regencies->firstWhere('id', request('regency_id'))->name }}
                </span>
            @endif
        </h3>
    </div>
    <div class="card-body" style="padding: 0;">
        @if($students->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 45px; text-align: center;">No</th>
                            <th>Sekolah / Akun Tim</th>
                            <th>Guru Pembina (PIC)</th>
                            <th>Wilayah (Prov / Kota)</th>
                            <th>Status</th>
                            <th style="width: 170px;">Progres Seleksi</th>
                            <th style="text-align: center;">Ujian & Skor</th>
                            <th style="width: 175px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $index => $student)
                            <tr>
                                <td style="text-align: center; font-weight: 600; color: var(--color-gray-500);">
                                    {{ ($students->currentPage() - 1) * $students->perPage() + $loop->iteration }}
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <img src="{{ $student->image_url }}" alt="{{ $student->school_name ?? $student->name }}" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid var(--color-gray-200); background: #f8fafc; flex-shrink: 0;">
                                        <div>
                                            <div style="font-weight: 600; color: var(--color-dark);">{{ $student->school_name ?? $student->name }}</div>
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
                                        <div style="font-weight: 600; font-size: 0.82rem; color: var(--color-dark);">
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
                                <td>
                                    @if($student->is_troubled)
                                        <span class="badge" style="background-color: #fee2e2; color: #b91c1c; font-weight: 700; font-size: 0.75rem; padding: 4px 8px; border-radius: 20px; display: inline-flex; align-items: center; gap: 4px;" title="{{ $student->trouble_notes }}">
                                            ⚠️ Kendala Sinyal
                                        </span>
                                    @else
                                        <span class="badge" style="background-color: #dcfce7; color: #15803d; font-weight: 600; font-size: 0.75rem; padding: 4px 8px; border-radius: 20px; display: inline-flex; align-items: center; gap: 4px;">
                                            ✓ Normal
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <!-- Real Materi progress bar -->
                                    @php
                                        $progressPercent = $totalRealMateriCount > 0 
                                            ? round(($student->completed_progress_count / $totalRealMateriCount) * 100) 
                                            : 0;
                                    @endphp
                                    <div style="display: flex; flex-direction: column; gap: 4px;">
                                        <div class="progress-container" style="margin-bottom: 0; height: 7px;">
                                            <div class="progress-bar-fill" style="width: {{ $progressPercent }}%;"></div>
                                        </div>
                                        <span style="font-size: 0.72rem; color: var(--color-gray-600); font-weight: 500;">
                                            {{ $student->completed_progress_count }}/{{ $totalRealMateriCount }} Paket ({{ $progressPercent }}%)
                                        </span>
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <div style="font-weight: 700; font-size: 0.95rem; color: {{ is_numeric($student->average_score) && $student->average_score >= 70 ? 'var(--color-success)' : (is_numeric($student->average_score) ? 'var(--color-danger)' : 'var(--color-gray-400)') }};">
                                        {{ $student->average_score !== '-' ? $student->average_score . '%' : '-' }}
                                    </div>
                                    <div style="font-size: 0.72rem; color: var(--color-gray-500);">
                                        {{ $student->total_quizzes_taken }}x Ujian
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <div style="display: flex; gap: 6px; justify-content: center; align-items: center;">
                                        <a href="{{ route('admin.students.show', $student) }}" class="btn btn-secondary btn-sm" style="padding: 6px 10px; font-weight: 500; font-size: 0.78rem; display: inline-flex; align-items: center; gap: 4px;" title="Lihat Rapor">
                                            <i class="fi fi-rr-eye"></i> Rapor
                                        </a>
                                        <button type="button" onclick="openRetestModal({{ $student->id }}, '{{ addslashes($student->school_name ?? $student->name) }}')" class="btn btn-sm" style="padding: 6px 10px; font-size: 0.78rem; background: #fff7ed; border: 1px solid #fdba74; color: #c2410c; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;" title="Izinkan Tes Ulang (Reset Sesi Bermasalah)">
                                            <i class="fi fi-rr-refresh"></i> Tes Ulang
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Custom Pagination Links -->
            <div class="pagination-wrapper">
                <div class="pagination-info">
                    Menampilkan <strong>{{ $students->firstItem() ?? 0 }}</strong> sampai <strong>{{ $students->lastItem() ?? 0 }}</strong> dari total <strong>{{ $students->total() }}</strong> siswa
                </div>

                @if($students->hasPages())
                    <ul class="custom-pagination">
                        {{-- Previous Page Link --}}
                        @if ($students->onFirstPage())
                            <li class="page-item disabled" aria-disabled="true">
                                <span class="page-link"><i class="fi fi-rr-angle-small-left"></i></span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $students->previousPageUrl() }}" rel="prev"><i class="fi fi-rr-angle-small-left"></i></a>
                            </li>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach ($students->getUrlRange(1, $students->lastPage()) as $page => $url)
                            @if ($page == $students->currentPage())
                                <li class="page-item active" aria-current="page">
                                    <span class="page-link">{{ $page }}</span>
                                </li>
                            @elseif ($page == 1 || $page == $students->lastPage() || ($page >= $students->currentPage() - 2 && $page <= $students->currentPage() + 2))
                                <li class="page-item">
                                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                </li>
                            @elseif ($page == $students->currentPage() - 3 || $page == $students->currentPage() + 3)
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            @endif
                        @endforeach

                        {{-- Next Page Link --}}
                        @if ($students->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $students->nextPageUrl() }}" rel="next"><i class="fi fi-rr-angle-small-right"></i></a>
                            </li>
                        @else
                            <li class="page-item disabled" aria-disabled="true">
                                <span class="page-link"><i class="fi fi-rr-angle-small-right"></i></span>
                            </li>
                        @endif
                    </ul>
                @endif
            </div>
        @else
            <div style="text-align: center; padding: 60px 20px; color: var(--color-gray-400);">
                <p style="font-size: 2.5rem; margin-bottom: 12px;"><i class="fi fi-rr-search-alt" style="color: var(--color-gray-400); font-size: 2.5rem;"></i></p>
                <p style="font-size: 1rem; color: var(--color-gray-600); font-weight: 500;">Tidak ditemukan data siswa yang cocok dengan filter.</p>
                @if(request()->hasAny(['search', 'dapil', 'sort_by', 'order']))
                    <a href="{{ route('admin.students.index') }}" class="btn btn-secondary btn-sm" style="margin-top: 12px; display: inline-flex; align-items: center; gap: 4px;">
                        <i class="fi fi-rr-rotate-left"></i> Reset Filter
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

<script>
    const adminRegenciesData = @json($allRegencies);

    function onAdminProvinceChange(provId) {
        const regSelect = document.getElementById('admin_regency_id');
        regSelect.innerHTML = '<option value="">-- Semua Kab / Kota --</option>';

        if (!provId) {
            regSelect.disabled = true;
            document.getElementById('filter-form').submit();
            return;
        }

        const filtered = adminRegenciesData.filter(r => String(r.province_id) === String(provId));
        filtered.forEach(r => {
            const opt = document.createElement('option');
            opt.value = r.id;
            opt.textContent = r.name;
            regSelect.appendChild(opt);
        });

        regSelect.disabled = false;
        document.getElementById('filter-form').submit();
    }

    function openRetestModal(studentId, schoolName) {
        document.getElementById('retest_school_name').textContent = schoolName;
        document.getElementById('retestForm').action = "{{ url('/admin/students') }}/" + studentId + "/grant-retest";
        document.getElementById('retestModal').style.display = 'flex';
    }

    function openBroadcastModal() {
        document.getElementById('broadcastModal').style.display = 'flex';
    }
</script>

<!-- Modal Izinkan Tes Ulang -->
<div id="retestModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 9999; justify-content: center; align-items: center; padding: 20px;">
    <div style="background: white; border-radius: 12px; width: 100%; max-width: 520px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2); overflow: hidden;">
        <div style="background: linear-gradient(135deg, #f59e0b, #d97706); padding: 18px 24px; color: white; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                <i class="fi fi-rr-refresh"></i> Izin Tes Ulang (Susulan)
            </h3>
            <button type="button" onclick="document.getElementById('retestModal').style.display='none'" style="background: none; border: none; color: white; font-size: 1.2rem; cursor: pointer; line-height: 1;">✕</button>
        </div>
        <form id="retestForm" method="POST" style="padding: 24px;">
            @csrf
            <p style="font-size: 0.9rem; color: #475569; margin-top: 0; line-height: 1.5;">
                Berikan persetujuan sesi <strong>Tes Ulang (Susulan)</strong> untuk sekolah: <br>
                <strong id="retest_school_name" style="color: #1e293b; font-size: 1rem;">-</strong>
            </p>
            <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 12px; margin-bottom: 18px; font-size: 0.83rem; color: #92400e;">
                ⚠️ <strong>Perhatian Panitia:</strong> Riwayat lembar jawaban sebelumnya akan dihapus sehingga perangkat sekolah dapat kembali login dan mengerjakan tes dari nomor 1 secara adil (soal diacak otomatis). Notifikasi akan otomatis terkirim ke email PIC.
            </div>

            <div style="margin-bottom: 20px;">
                <label for="retest_reason" style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 6px;">
                    Alasan / Keterangan Kendala <span style="color: #ef4444;">*</span>
                </label>
                <textarea name="reason" id="retest_reason" rows="3" required class="form-control" style="width: 100%; border-radius: 8px; border: 1px solid #cbd5e1; padding: 10px; font-size: 0.88rem; box-sizing: border-box;" placeholder="Contoh: Gangguan jaringan internet kabel putus dan pemadaman PLN di wilayah setempat."></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="document.getElementById('retestModal').style.display='none'" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn" style="background: #d97706; color: white; font-weight: 600;">Setujui & Buka Sesi Ulang</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Blast Pengumuman / Info -->
<div id="broadcastModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 9999; justify-content: center; align-items: center; padding: 20px;">
    <div style="background: white; border-radius: 12px; width: 100%; max-width: 600px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2); overflow: hidden;">
        <div style="background: linear-gradient(135deg, #2563eb, #1d4ed8); padding: 18px 24px; color: white; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 1.15rem; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                <i class="fi fi-rr-bullhorn"></i> Blast Pengumuman & Jadwal Seleksi
            </h3>
            <button type="button" onclick="document.getElementById('broadcastModal').style.display='none'" style="background: none; border: none; color: white; font-size: 1.2rem; cursor: pointer; line-height: 1;">✕</button>
        </div>
        <form action="{{ route('admin.students.broadcast') }}" method="POST" style="padding: 24px;">
            @csrf
            <div style="margin-bottom: 16px;">
                <label for="broadcast_province_id" style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 6px;">
                    Target Wilayah Penerima
                </label>
                <select name="province_id" id="broadcast_province_id" class="form-control" style="width: 100%; border-radius: 8px; border: 1px solid #cbd5e1; padding: 10px; font-size: 0.88rem; box-sizing: border-box;">
                    <option value="">🇮🇩 Seluruh Indonesia (Semua Sekolah Terdaftar)</option>
                    @foreach($provinces as $prov)
                        <option value="{{ $prov->id }}">{{ $prov->name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom: 16px;">
                <label for="broadcast_subject" style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 6px;">
                    Judul Pengumuman <span style="color: #ef4444;">*</span>
                </label>
                <input type="text" name="subject" id="broadcast_subject" required class="form-control" style="width: 100%; border-radius: 8px; border: 1px solid #cbd5e1; padding: 10px; font-size: 0.88rem; box-sizing: border-box;" placeholder="Contoh: Jadwal Sesi Zoom Batch 1 & Petunjuk Ujian Seleksi Maret 2026">
            </div>

            <div style="margin-bottom: 20px;">
                <label for="broadcast_message" style="display: block; font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 6px;">
                    Isi Pesan Pengumuman <span style="color: #ef4444;">*</span>
                </label>
                <textarea name="message" id="broadcast_message" rows="5" required class="form-control" style="width: 100%; border-radius: 8px; border: 1px solid #cbd5e1; padding: 10px; font-size: 0.88rem; box-sizing: border-box; line-height: 1.5;" placeholder="Tuliskan informasi teknis, tautan zoom, petunjuk tryout simulasi, atau instruksi seleksi resmi..."></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" onclick="document.getElementById('broadcastModal').style.display='none'" class="btn btn-secondary">Batal</button>
                <button type="submit" class="btn btn-primary" style="font-weight: 600;">Kirim Blast Sekarang</button>
            </div>
        </form>
    </div>
</div>
@endsection
