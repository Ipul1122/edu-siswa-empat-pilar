@extends('layouts.admin')

@section('title', 'Manajemen Sesi Zoom - Admin Empat Pilar')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h1>Sesi Belajar Tatap Muka (Zoom)</h1>
        <p>Kelola tautan ruang telekonferensi Zoom, pantau kapasitas kuota peserta, dan rekam presensi kehadiran siswa.</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.zoom-sessions.create') }}" class="btn btn-primary">
            <i class="fi fi-rr-plus"></i> Jadwalkan Sesi Zoom Baru
        </a>
    </div>
</div>

<!-- Stats Overview Grid -->
<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); margin-bottom: 28px;">
    <div class="stat-card">
        <div class="stat-icon primary">
            <i class="fi fi-rr-video-camera-alt"></i>
        </div>
        <div class="stat-info">
            <span class="stat-value">{{ $totalSessions }}</span>
            <span class="stat-label">Total Sesi Zoom</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon success">
            <i class="fi fi-rr-check-circle"></i>
        </div>
        <div class="stat-info">
            <span class="stat-value">{{ $activeSessions }}</span>
            <span class="stat-label">Sesi Aktif / Terbuka</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon info">
            <i class="fi fi-rr-users-alt"></i>
        </div>
        <div class="stat-info">
            <span class="stat-value">{{ $totalParticipants }}</span>
            <span class="stat-label">Total Siswa Join</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon warning" style="background-color: rgba(220, 38, 38, 0.1); color: #dc2626;">
            <i class="fi fi-rr-lock"></i>
        </div>
        <div class="stat-info">
            <span class="stat-value">{{ $fullSessionsCount }}</span>
            <span class="stat-label">Sesi Kuota Penuh</span>
        </div>
    </div>
</div>

<!-- Filter & Search Bar -->
<div class="card" style="margin-bottom: 28px;">
    <div class="card-body" style="padding: 20px;">
        <form action="{{ route('admin.zoom-sessions.index') }}" method="GET" style="display: flex; gap: 16px; flex-wrap: wrap; align-items: center;">
            <div style="flex: 1; min-width: 250px; position: relative;">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari judul pertemuan, topik, atau ID rapat..." style="width: 100%; padding: 10px 14px 10px 38px; border-radius: var(--border-radius-sm); border: 1px solid var(--color-gray-300);">
                <i class="fi fi-rr-search" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--color-gray-400);"></i>
            </div>

            <div style="min-width: 180px;">
                <select name="pillar" class="form-control" style="width: 100%; padding: 10px 14px; border-radius: var(--border-radius-sm); border: 1px solid var(--color-gray-300);">
                    <option value="">-- Semua Kategori Pilar --</option>
                    <option value="umum" {{ request('pillar') === 'umum' ? 'selected' : '' }}>Umum / Nasional</option>
                    <option value="pancasila" {{ request('pillar') === 'pancasila' ? 'selected' : '' }}>Pancasila</option>
                    <option value="uud_1945" {{ request('pillar') === 'uud_1945' ? 'selected' : '' }}>UUD NRI 1945</option>
                    <option value="nkri" {{ request('pillar') === 'nkri' ? 'selected' : '' }}>NKRI</option>
                    <option value="bhinneka_tunggal_ika" {{ request('pillar') === 'bhinneka_tunggal_ika' ? 'selected' : '' }}>Bhinneka Tunggal Ika</option>
                    <option value="twk_kedinasan" {{ request('pillar') === 'twk_kedinasan' ? 'selected' : '' }}>TWK Kedinasan</option>
                </select>
            </div>

            <div style="min-width: 160px;">
                <select name="status" class="form-control" style="width: 100%; padding: 10px 14px; border-radius: var(--border-radius-sm); border: 1px solid var(--color-gray-300);">
                    <option value="">-- Semua Status --</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Sesi Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Sesi Nonaktif</option>
                    <option value="full" {{ request('status') === 'full' ? 'selected' : '' }}>Kuota Penuh</option>
                </select>
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn btn-secondary">
                    <i class="fi fi-rr-filter"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'pillar', 'status']))
                    <a href="{{ route('admin.zoom-sessions.index') }}" class="btn btn-secondary" title="Reset Filter">
                        <i class="fi fi-rr-refresh"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Zoom Sessions Grid -->
@if($sessions->count() > 0)
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(360px, 1fr)); gap: 24px;">
        @foreach($sessions as $session)
            @php
                $joined = $session->participants_count;
                $capacity = $session->capacity;
                $percent = $session->fill_percentage;
                $isFull = $session->is_full;
                $barColor = $isFull ? '#dc2626' : ($percent >= 80 ? '#ea580c' : '#16a34a');
            @endphp
            <div class="card" style="display: flex; flex-direction: column; justify-content: space-between; border-radius: var(--border-radius-md); overflow: hidden; border: 1px solid var(--color-gray-200); position: relative; transition: var(--transition-smooth); box-shadow: var(--shadow-sm);">
                <!-- Top Accent Line -->
                <div style="height: 4px; background: {{ $isFull ? 'var(--color-danger)' : 'var(--color-primary)' }};"></div>

                <div class="card-body" style="padding: 24px; display: flex; flex-direction: column; gap: 16px; flex: 1;">
                    <!-- Badge row -->
                    <div style="display: flex; justify-content: space-between; align-items: center; gap: 8px; flex-wrap: wrap;">
                        <span class="badge {{ $session->pillar }}" style="font-size: 0.75rem;">
                            {{ $session->formatted_pillar }}
                        </span>

                        <div style="display: flex; gap: 6px; align-items: center;">
                            @if($isFull)
                                <span class="badge" style="background-color: #fee2e2; color: #dc2626; font-weight: 700; font-size: 0.72rem; border: 1px solid #fca5a5;">
                                    <i class="fi fi-rr-lock" style="font-size: 0.7rem; margin-right: 2px;"></i> KUOTA PENUH
                                </span>
                            @else
                                <span class="badge {{ $session->status_label['class'] }}" style="font-size: 0.72rem;">
                                    {{ $session->status_label['label'] }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Title -->
                    <div>
                        <h3 style="font-size: 1.15rem; color: var(--color-dark); font-weight: 700; line-height: 1.4; margin-bottom: 6px;">
                            <a href="{{ route('admin.zoom-sessions.show', $session) }}" style="color: inherit; text-decoration: none;">
                                {{ $session->title }}
                            </a>
                        </h3>
                        @if($session->description)
                            <p style="color: var(--color-gray-600); font-size: 0.85rem; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ $session->description }}
                            </p>
                        @endif
                    </div>

                    <!-- Schedule Info -->
                    <div style="background-color: var(--color-gray-100); padding: 12px 16px; border-radius: var(--border-radius-sm); font-size: 0.85rem; display: flex; flex-direction: column; gap: 6px;">
                        <div style="display: flex; align-items: center; gap: 8px; color: var(--color-dark);">
                            <i class="fi fi-rr-calendar" style="color: var(--color-primary); font-size: 0.95rem;"></i>
                            <strong>{{ $session->start_time->isoFormat('dddd, D MMMM Y') }}</strong>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px; color: var(--color-gray-600);">
                            <i class="fi fi-rr-clock" style="color: var(--color-primary); font-size: 0.95rem;"></i>
                            <span>{{ $session->start_time->format('H:i') }} WIB @if($session->end_time) - {{ $session->end_time->format('H:i') }} WIB @endif</span>
                        </div>
                        @if($session->meeting_id)
                            <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem; color: var(--color-gray-600); border-top: 1px dashed var(--color-gray-300); padding-top: 6px; margin-top: 2px;">
                                <span>ID: <code style="color: var(--color-dark); font-weight: 600;">{{ $session->meeting_id }}</code></span>
                                @if($session->passcode)
                                    <span>Passcode: <code style="color: var(--color-dark); font-weight: 600;">{{ $session->passcode }}</code></span>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- Capacity & Attendance Progress (Requirement Utama) -->
                    <div style="margin-top: auto; padding-top: 8px;">
                        <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 6px;">
                            <span style="font-size: 0.82rem; font-weight: 600; color: var(--color-dark);">
                                <i class="fi fi-rr-users" style="margin-right: 4px; color: var(--color-gray-500);"></i> Kapasitas Ruangan
                            </span>
                            <span style="font-size: 0.85rem; font-weight: 700; color: {{ $barColor }};">
                                {{ $joined }} / {{ $capacity }} Peserta
                                @if($isFull)
                                    <span style="font-size: 0.72rem; color: #dc2626;">(100% Penuh)</span>
                                @else
                                    <span style="font-size: 0.75rem; font-weight: 500; color: var(--color-gray-600);">(Sisa {{ $session->remaining_seats }})</span>
                                @endif
                            </span>
                        </div>
                        <div style="height: 8px; width: 100%; background-color: var(--color-gray-200); border-radius: 999px; overflow: hidden;">
                            <div style="height: 100%; width: {{ $percent }}%; background-color: {{ $barColor }}; border-radius: 999px; transition: width 0.4s ease;"></div>
                        </div>
                    </div>
                </div>

                <!-- Footer Action Buttons -->
                <div style="padding: 14px 20px; background-color: #fafafa; border-top: 1px solid var(--color-gray-200); display: flex; justify-content: space-between; align-items: center; gap: 8px;">
                    <div style="display: flex; gap: 6px;">
                        <form action="{{ route('admin.zoom-sessions.toggle', $session) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-secondary btn-sm" style="padding: 6px 10px;" title="{{ $session->is_active ? 'Nonaktifkan Sesi' : 'Aktifkan Sesi' }}">
                                <i class="fi {{ $session->is_active ? 'fi-rr-eye' : 'fi-rr-eye-crossed' }}"></i>
                            </button>
                        </form>
                        <a href="{{ route('admin.zoom-sessions.edit', $session) }}" class="btn btn-secondary btn-sm" style="padding: 6px 10px;" title="Edit Sesi Zoom">
                            <i class="fi fi-rr-pencil"></i>
                        </a>
                        <form action="{{ route('admin.zoom-sessions.destroy', $session) }}" method="POST" class="delete-confirm-form" data-item-type="sesi Zoom">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" style="padding: 6px 10px;" title="Hapus Sesi Zoom">
                                <i class="fi fi-rr-trash"></i>
                            </button>
                        </form>
                    </div>

                    <a href="{{ route('admin.zoom-sessions.show', $session) }}" class="btn btn-primary btn-sm" style="font-weight: 600; padding: 6px 14px;">
                        <i class="fi fi-rr-users-alt"></i> Detail & Peserta ({{ $joined }})
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    <div style="margin-top: 32px;">
        {{ $sessions->links() }}
    </div>
@else
    <div class="card">
        <div class="card-body" style="padding: 60px 20px; text-align: center;">
            <div style="width: 80px; height: 80px; margin: 0 auto 16px; background-color: rgba(234, 88, 12, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--color-primary);">
                <i class="fi fi-rr-video-camera-alt" style="font-size: 2.5rem;"></i>
            </div>
            <h3 style="font-size: 1.25rem; color: var(--color-dark); margin-bottom: 8px;">Belum Ada Sesi Zoom Terjadwal</h3>
            <p style="color: var(--color-gray-600); max-width: 480px; margin: 0 auto 24px;">
                Buat link pertemuan Zoom untuk pembelajaran tatap muka daring, tentukan batas kuota ruangan, dan undang siswa untuk bergabung.
            </p>
            <a href="{{ route('admin.zoom-sessions.create') }}" class="btn btn-primary">
                <i class="fi fi-rr-plus"></i> Buat Sesi Zoom Pertama
            </a>
        </div>
    </div>
@endif
@endsection
