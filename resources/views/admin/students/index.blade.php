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
        <a href="{{ route('admin.students.export') }}" class="btn btn-secondary" style="background-color: var(--color-white); border: 1px solid var(--color-gray-300); color: var(--color-gray-700);">
            <i class="fi fi-rr-download" style="margin-right: 4px; vertical-align: middle;"></i> Ekspor CSV
        </a>
        <a href="{{ route('admin.students.report') }}" target="_blank" class="btn btn-primary">
            <i class="fi fi-rr-print" style="margin-right: 4px; vertical-align: middle;"></i> Cetak Laporan
        </a>
    </div>
</div>

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

            <!-- Filter by Dapil -->
            <div class="filter-group">
                <label for="dapil" class="filter-label"><i class="fi fi-rr-map-marker"></i> Filter Daerah Pemilihan (Dapil)</label>
                <select name="dapil" id="dapil" class="filter-select" onchange="document.getElementById('filter-form').submit()">
                    <option value="">-- Semua Dapil (84 Dapil) --</option>
                    @foreach($dapilList as $dapilOption)
                        <option value="{{ $dapilOption }}" {{ request('dapil') === $dapilOption ? 'selected' : '' }}>
                            {{ $dapilOption }}
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
                    <option value="dapil" {{ ($sortBy ?? '') === 'dapil' ? 'selected' : '' }}>Daerah Pemilihan (Dapil)</option>
                    <option value="average_score" {{ ($sortBy ?? '') === 'average_score' ? 'selected' : '' }}>Rerata Skor</option>
                    <option value="total_quizzes_taken" {{ ($sortBy ?? '') === 'total_quizzes_taken' ? 'selected' : '' }}>Total Kuis</option>
                    <option value="completed_progress_count" {{ ($sortBy ?? '') === 'completed_progress_count' ? 'selected' : '' }}>Real Materi Selesai</option>
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

            @if(request()->hasAny(['search', 'dapil', 'sort_by', 'order', 'per_page']))
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
            @if(request('dapil'))
                <span class="badge" style="background-color: rgba(37, 99, 235, 0.1); color: #2563eb; font-weight: 600; font-size: 0.78rem; margin-left: 6px;">
                    Dapil: {{ request('dapil') }}
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
                            <th style="width: 50px; text-align: center;">No</th>
                            <th>Siswa</th>
                            <th>Sekolah</th>
                            <th>(Dapil)</th>
                            <th style="width: 200px;">Progres</th>
                            <th style="text-align: center;">Kuis</th>
                            <th style="text-align: center;">Skor</th>
                            <th style="width: 120px; text-align: center;">Aksi</th>
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
                                        <img src="{{ $student->image_url }}" alt="{{ $student->name }}" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid var(--color-gray-200); background: #f8fafc; flex-shrink: 0;">
                                        <div>
                                            <div style="font-weight: 600; color: var(--color-dark);">{{ $student->name }}</div>
                                            <div style="font-size: 0.78rem; color: var(--color-gray-500);">{{ $student->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span style="font-weight: 500; color: var(--color-gray-800);">
                                        {{ $student->school_name ?? '-' }}
                                    </span>
                                </td>
                                <td>
                                    @if($student->dapil)
                                        <span class="badge" style="background-color: rgba(37, 99, 235, 0.1); color: #2563eb; font-weight: 600; font-size: 0.78rem; padding: 4px 10px; border-radius: 20px;">
                                            <i class="fi fi-rr-map-marker" style="margin-right: 3px; font-size: 0.75rem;"></i> {{ $student->dapil }}
                                        </span>
                                    @else
                                        <span style="color: var(--color-gray-400); font-size: 0.82rem;">-</span>
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
                                            {{ $student->completed_progress_count }}/{{ $totalRealMateriCount }} Real Materi ({{ $progressPercent }}%)
                                        </span>
                                    </div>
                                </td>
                                <td style="text-align: center;">
                                    <span style="font-weight: 600;">{{ $student->total_quizzes_taken }}</span> <span style="font-size: 0.75rem; color: var(--color-gray-500);">Ujian</span>
                                </td>
                                <td style="text-align: center;">
                                    @if($student->average_score !== '-')
                                        <span style="font-weight: 700; font-size: 1rem; color: {{ $student->average_score >= 70 ? 'var(--color-success)' : 'var(--color-danger)' }}">
                                            {{ $student->average_score }}
                                        </span>
                                    @else
                                        <span style="color: var(--color-gray-400); font-size: 0.8rem;">-</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    <a href="{{ route('admin.students.show', $student) }}" class="btn btn-secondary btn-sm" style="padding: 6px 12px; font-weight: 500; font-size: 0.8rem; display: inline-flex; align-items: center; gap: 4px;">
                                        <i class="fi fi-rr-eye"></i> Rapor
                                    </a>
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
@endsection
