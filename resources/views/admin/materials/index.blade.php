@extends('layouts.admin')

@section('title', 'Manajemen Materi - Admin')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h1>Manajemen Materi</h1>
        <p>Kelola bahan bacaan dan literasi Empat Pilar Kebangsaan untuk siswa.</p>
    </div>
    <a href="{{ route('admin.materials.create') }}" class="btn btn-primary">
        <i class="fi fi-rr-plus"></i> Tambah Materi
    </a>
</div>

<div class="card">
    <div class="card-body">
        @if($materials->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Kategori / Pilar</th>
                            <th>Judul Materi</th>
                            <th>Estimasi Waktu</th>
                            <th>Tanggal Dibuat</th>
                            <th style="width: 150px; text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($materials as $material)
                            <tr>
                                <td>
                                    <span class="badge {{ $material->pillar }}">
                                        {{ $material->formatted_pillar }}
                                    </span>
                                </td>
                                <td style="font-weight: 600; color: var(--color-dark);">
                                    {{ $material->title }}
                                </td>
                                <td><i class="fi fi-rr-clock" style="margin-right: 4px; font-size: 0.85rem; vertical-align: middle;"></i>{{ $material->read_time }} Menit</td>
                                <td style="font-size: 0.85rem; color: var(--color-gray-600);">
                                    {{ $material->created_at->format('d M Y') }}
                                </td>
                                <td>
                                    <div style="display: flex; gap: 8px; justify-content: center;">
                                        <a href="{{ route('admin.materials.edit', $material) }}" class="btn btn-secondary btn-sm" style="padding: 6px 10px;">
                                            <i class="fi fi-rr-pencil"></i> Edit
                                        </a>
                                        <form action="{{ route('admin.materials.destroy', $material) }}" method="POST" class="delete-confirm-form" data-item-type="materi">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" style="padding: 6px 10px;">
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
                <p style="font-size: 2.5rem; margin-bottom: 12px;"><i class="fi fi-rr-box-open" style="color: var(--color-gray-400); font-size: 2.5rem;"></i></p>
                <h3>Belum Ada Materi</h3>
                <p style="margin-top: 4px; margin-bottom: 20px;">Silakan tambahkan materi pertama untuk memulai pembelajaran siswa.</p>
                <a href="{{ route('admin.materials.create') }}" class="btn btn-primary">Tambah Materi Pertama</a>
            </div>
        @endif
    </div>
</div>
@endsection
