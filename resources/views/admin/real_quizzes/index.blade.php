@extends('layouts.admin')

@section('title', 'Manajemen Real Materi - Admin Empat Pilar')

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
    <div class="page-title">
        <h1>Manajemen Real Materi</h1>
        <p>Kelola paket evaluasi resmi Real Materi dan kontrol akses pengerjaan serentak untuk seluruh siswa.</p>
    </div>
    <a href="{{ route('admin.real-materi.create') }}" class="btn btn-primary">
        <i class="fi fi-rr-plus"></i> Tambah Real Materi
    </a>
</div>

<!-- Master Global Control Switch Card -->
@if($totalRealCount > 0)
    <div class="card" style="border: 2px solid {{ $isAllClosed ? '#ef4444' : '#10b981' }}; background: {{ $isAllClosed ? 'rgba(239, 68, 68, 0.04)' : 'rgba(16, 185, 129, 0.04)' }}; margin-bottom: 24px;">
        <div class="card-body" style="padding: 20px 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
            <div>
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px;">
                    @if($isAllClosed)
                        <span class="badge" style="background-color: #ef4444; color: #fff; font-weight: 700; font-size: 0.82rem; padding: 5px 12px; border-radius: 20px;">
                            <i class="fi fi-rr-lock" style="margin-right: 4px;"></i> STATUS: SELURUH REAL MATERI DITUTUP
                        </span>
                    @else
                        <span class="badge" style="background-color: #10b981; color: #fff; font-weight: 700; font-size: 0.82rem; padding: 5px 12px; border-radius: 20px;">
                            <i class="fi fi-rr-unlock" style="margin-right: 4px;"></i> STATUS: REAL MATERI TERBUKA ({{ $activeRealCount }}/{{ $totalRealCount }} Aktif)
                        </span>
                    @endif
                </div>
                <p style="margin: 0; font-size: 0.88rem; color: var(--color-gray-700);">
                    @if($isAllClosed)
                        <strong>Akses Ditutup Total:</strong> Seluruh siswa <strong>tidak dapat</strong> mengakses, memulai, atau mengerjakan paket evaluasi Real Materi.
                    @else
                        <strong>Akses Terbuka:</strong> Siswa yang belum ujian dapat mengakses dan mengerjakan kuis Real Materi.
                    @endif
                </p>
            </div>

            <!-- Master Toggle Button -->
            <div style="display: flex; gap: 10px;">
                @if(!$isAllClosed)
                    <form action="{{ route('admin.real-materi.toggle-all') }}" method="POST" id="form-close-all">
                        @csrf
                        <input type="hidden" name="status" value="0">
                        <button type="button" class="btn btn-danger" style="padding: 10px 18px; font-weight: 600; box-shadow: var(--shadow-sm); display: inline-flex; align-items: center; gap: 6px;" onclick="confirmCloseAll()">
                            <i class="fi fi-rr-lock"></i> Tutup Semua Real Materi
                        </button>
                    </form>
                @endif

                @if(!$isAllOpen)
                    <form action="{{ route('admin.real-materi.toggle-all') }}" method="POST" id="form-open-all">
                        @csrf
                        <input type="hidden" name="status" value="1">
                        <button type="button" class="btn btn-success" style="padding: 10px 18px; font-weight: 600; box-shadow: var(--shadow-sm); display: inline-flex; align-items: center; gap: 6px;" onclick="confirmOpenAll()">
                            <i class="fi fi-rr-unlock"></i> Buka Semua Real Materi
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endif

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Paket Real Materi ({{ $totalRealCount }} Paket)</h3>
    </div>
    <div class="card-body">
        @if($quizzes->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Kategori / Pilar</th>
                            <th>Judul Real Materi</th>
                            <th>Status Akses</th>
                            <th>Jumlah Soal</th>
                            <th>Durasi</th>
                            <th style="width: 220px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quizzes as $quiz)
                            <tr>
                                <td>
                                    <span class="badge {{ $quiz->pillar }}">
                                        {{ $quiz->formatted_pillar }}
                                    </span>
                                </td>
                                <td style="font-weight: 600; color: var(--color-dark);">
                                    {{ $quiz->title }}
                                </td>
                                <td>
                                    @if($quiz->is_active)
                                        <span class="badge" style="background-color: rgba(16, 185, 129, 0.1); color: #10b981; font-weight: 600; font-size: 0.78rem; display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 20px;">
                                            <i class="fi fi-rr-unlock" style="font-size: 0.75rem;"></i> Terbuka
                                        </span>
                                    @else
                                        <span class="badge" style="background-color: rgba(239, 68, 68, 0.1); color: #ef4444; font-weight: 600; font-size: 0.78rem; display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 20px;">
                                            <i class="fi fi-rr-lock" style="font-size: 0.75rem;"></i> Ditutup
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span style="font-weight: 600;">{{ $quiz->questions_count }}</span> Soal
                                </td>
                                <td><i class="fi fi-rr-clock" style="margin-right: 4px; font-size: 0.85rem; vertical-align: middle;"></i>{{ $quiz->duration_minutes }} Menit</td>
                                <td>
                                    <div style="display: flex; gap: 8px; justify-content: center;">
                                        <a href="{{ route('admin.real-materi.show', $quiz) }}" class="btn btn-primary btn-sm" style="padding: 6px 12px; background-color: var(--color-info);" title="Kelola Butir Soal">
                                            <i class="fi fi-rr-eye"></i> Soal
                                        </a>
                                        <a href="{{ route('admin.real-materi.edit', $quiz) }}" class="btn btn-secondary btn-sm" style="padding: 6px 12px;" title="Edit Pengaturan">
                                            <i class="fi fi-rr-pencil"></i> Edit
                                        </a>
                                        <form action="{{ route('admin.real-materi.destroy', $quiz) }}" method="POST" class="delete-confirm-form" data-item-type="real materi">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" style="padding: 6px 12px;" title="Hapus Real Materi">
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
                <p style="font-size: 2.5rem; margin-bottom: 12px;"><i class="fi fi-rr-diploma" style="color: var(--color-gray-400); font-size: 2.5rem;"></i></p>
                <h3>Belum Ada Evaluasi Real Materi</h3>
                <p style="margin-top: 4px; margin-bottom: 20px;">Silakan buat paket evaluasi Real Materi pertama untuk siswa.</p>
                <a href="{{ route('admin.real-materi.create') }}" class="btn btn-primary">Buat Real Materi Pertama</a>
            </div>
        @endif
    </div>
</div>

<script>
    function confirmCloseAll() {
        Swal.fire({
            title: 'Tutup Seluruh Real Materi?',
            text: 'Seluruh siswa tidak akan bisa mengakses, memulai, atau mengerjakan kuis Real Materi sampai Anda membukanya kembali.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Tutup Semua!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-close-all').submit();
            }
        });
    }

    function confirmOpenAll() {
        Swal.fire({
            title: 'Buka Seluruh Real Materi?',
            text: 'Seluruh paket evaluasi Real Materi akan dibuka dan siswa dapat mengerjakan ujian.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Buka Semua!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-open-all').submit();
            }
        });
    }
</script>
@endsection
