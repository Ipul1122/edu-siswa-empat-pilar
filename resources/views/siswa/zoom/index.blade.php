@extends('layouts.siswa')

@section('title', 'Sesi Belajar Virtual (Zoom) - Empat Pilar')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h1>Sesi Pertemuan Tatap Muka (Zoom)</h1>
        <p>Ikuti sesi webinar dan bimbingan belajar langsung via Zoom bersama fasilitator & pemateri Empat Pilar Kebangsaan.</p>
    </div>
</div>

<!-- Stats Bar -->
<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); margin-bottom: 28px;">
    <div class="stat-card">
        <div class="stat-icon primary">
            <i class="fi fi-rr-video-camera-alt"></i>
        </div>
        <div class="stat-info">
            <span class="stat-value">{{ $totalAvailableSessions }}</span>
            <span class="stat-label">Total Sesi Zoom Tersedia</span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon success">
            <i class="fi fi-rr-check-circle"></i>
        </div>
        <div class="stat-info">
            <span class="stat-value">{{ $myJoinedCount }}</span>
            <span class="stat-label">Sesi yang Anda Ikuti</span>
        </div>
    </div>
</div>

<!-- Filter Bar -->
<div class="card" style="margin-bottom: 28px;">
    <div class="card-body" style="padding: 18px 24px;">
        <form action="{{ route('siswa.zoom-sessions.index') }}" method="GET" style="display: flex; gap: 16px; flex-wrap: wrap; align-items: center;">
            <div style="flex: 1; min-width: 250px; position: relative;">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari topik webinar atau materi Zoom..." style="width: 100%; padding: 10px 14px 10px 38px; border-radius: var(--border-radius-sm); border: 1px solid var(--color-gray-300); font-size: 0.9rem;">
                <i class="fi fi-rr-search" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--color-gray-400);"></i>
            </div>

            <div style="min-width: 200px;">
                <select name="pillar" class="form-control" style="width: 100%; padding: 10px 14px; border-radius: var(--border-radius-sm); border: 1px solid var(--color-gray-300); font-size: 0.9rem;">
                    <option value="">-- Semua Kategori Pilar --</option>
                    <option value="umum" {{ request('pillar') === 'umum' ? 'selected' : '' }}>Umum / Nasional</option>
                    <option value="pancasila" {{ request('pillar') === 'pancasila' ? 'selected' : '' }}>Pancasila</option>
                    <option value="uud_1945" {{ request('pillar') === 'uud_1945' ? 'selected' : '' }}>UUD NRI 1945</option>
                    <option value="nkri" {{ request('pillar') === 'nkri' ? 'selected' : '' }}>NKRI</option>
                    <option value="bhinneka_tunggal_ika" {{ request('pillar') === 'bhinneka_tunggal_ika' ? 'selected' : '' }}>Bhinneka Tunggal Ika</option>
                    <option value="twk_kedinasan" {{ request('pillar') === 'twk_kedinasan' ? 'selected' : '' }}>TWK Kedinasan</option>
                </select>
            </div>

            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn btn-secondary">
                    <i class="fi fi-rr-filter"></i> Filter
                </button>
                @if(request()->hasAny(['search', 'pillar']))
                    <a href="{{ route('siswa.zoom-sessions.index') }}" class="btn btn-secondary" title="Reset Filter">
                        <i class="fi fi-rr-refresh"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Sessions Grid -->
@if($sessions->isEmpty())
    <div class="card" style="padding: 48px 24px; text-align: center;">
        <div style="width: 72px; height: 72px; background: var(--color-gray-100); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; color: var(--color-gray-400); font-size: 2rem;">
            <i class="fi fi-rr-video-camera-alt"></i>
        </div>
        <h3 style="font-size: 1.25rem; color: var(--color-dark); margin-bottom: 8px;">Belum Ada Sesi Zoom yang Tersedia</h3>
        <p style="color: var(--color-gray-500); font-size: 0.95rem; max-width: 500px; margin: 0 auto;">
            @if(request()->hasAny(['search', 'pillar']))
                Tidak ditemukan sesi Zoom yang cocok dengan filter pencarian Anda. Silakan coba kata kunci atau filter lain.
            @else
                Saat ini administrator belum membuka jadwal sesi Zoom baru. Silakan cek kembali secara berkala!
            @endif
        </p>
    </div>
@else
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px; margin-bottom: 40px;">
        @foreach($sessions as $session)
            @php
                $hasJoined = in_array($session->id, $joinedSessionIds);
                $isFull = $session->is_full;
                $pct = $session->fill_percentage;
            @endphp
            <div class="card" style="display: flex; flex-direction: column; justify-content: space-between; position: relative; overflow: hidden; border-top: 4px solid {{ $hasJoined ? '#10b981' : ($isFull ? '#dc2626' : 'var(--color-primary)') }};">
                <div class="card-body" style="padding: 24px;">
                    <!-- Badges Row -->
                    <div style="display: flex; justify-content: space-between; align-items: center; gap: 8px; margin-bottom: 14px;">
                        <span class="badge" style="background-color: #fee2e2; color: #dc2626; font-size: 0.75rem; font-weight: 600;">
                            {{ $session->formatted_pillar }}
                        </span>

                        @if($hasJoined)
                            <span class="badge" style="background-color: #dcfce7; color: #15803d; font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                                <i class="fi fi-rr-check"></i> Sudah Bergabung
                            </span>
                        @elseif($isFull)
                            <span class="badge" style="background-color: #fee2e2; color: #dc2626; font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; gap: 4px;">
                                <i class="fi fi-rr-lock"></i> Kuota Penuh
                            </span>
                        @else
                            <span class="badge" style="background-color: #ecfdf5; color: #059669; font-size: 0.75rem; font-weight: 600;">
                                ● Kursi Tersedia
                            </span>
                        @endif
                    </div>

                    <!-- Title -->
                    <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--color-dark); margin: 0 0 10px 0; line-height: 1.4;">
                        {{ $session->title }}
                    </h3>

                    <!-- Description -->
                    <p style="color: var(--color-gray-600); font-size: 0.85rem; line-height: 1.5; margin-bottom: 20px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                        {{ $session->description ?: 'Bimbingan tatap muka interaktif melalui Zoom Meeting.' }}
                    </p>

                    <!-- Time & Schedule -->
                    <div style="background-color: var(--color-gray-50); border-radius: var(--border-radius-sm); padding: 12px 14px; margin-bottom: 18px;">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
                            <i class="fi fi-rr-calendar" style="color: var(--color-primary); font-size: 0.95rem;"></i>
                            <span style="font-size: 0.85rem; font-weight: 600; color: var(--color-dark);">
                                {{ $session->start_time->format('d M Y, H:i') }} WIB
                            </span>
                        </div>
                        @if($session->end_time)
                            <div style="display: flex; align-items: center; gap: 10px; font-size: 0.8rem; color: var(--color-gray-500); padding-left: 24px;">
                                <span>s/d {{ $session->end_time->format('H:i') }} WIB</span>
                            </div>
                        @endif
                    </div>

                    <!-- CAPACITY & PARTICIPANTS COUNTER (CRITICAL REQUIREMENT) -->
                    <div style="background: #fff; border: 1px solid var(--color-gray-200); border-radius: var(--border-radius-sm); padding: 14px 16px; margin-bottom: 20px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                            <span style="font-size: 0.8rem; font-weight: 600; color: var(--color-gray-600); text-transform: uppercase; letter-spacing: 0.5px;">
                                <i class="fi fi-rr-users" style="margin-right: 4px;"></i> Kapasitas Peserta
                            </span>
                            <strong style="font-size: 0.95rem; color: {{ $isFull ? '#dc2626' : 'var(--color-dark)' }};">
                                {{ $session->participants_count }} / {{ $session->capacity }}
                            </strong>
                        </div>

                        <!-- Dynamic Progress Bar -->
                        <div style="height: 8px; width: 100%; background-color: var(--color-gray-200); border-radius: 4px; overflow: hidden; margin-bottom: 8px;">
                            <div style="height: 100%; width: {{ $pct }}%; background-color: {{ $pct >= 100 ? '#dc2626' : ($pct >= 70 ? '#f59e0b' : '#10b981') }}; border-radius: 4px; transition: width 0.3s ease;"></div>
                        </div>

                        <!-- Status Notice -->
                        <div style="font-size: 0.78rem;">
                            @if($isFull)
                                <span style="color: #dc2626; font-weight: 600;">
                                    <i class="fi fi-rr-cross-circle" style="vertical-align: middle;"></i> Kuota ruangan ini telah penuh.
                                </span>
                            @else
                                <span style="color: #059669; font-weight: 600;">
                                    <i class="fi fi-rr-check" style="vertical-align: middle;"></i> Tersisa {{ $session->remaining_seats }} kursi lagi.
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Meeting Details if Joined -->
                    @if($hasJoined)
                        <div style="background-color: #f0fdf4; border: 1px dashed #86efac; border-radius: var(--border-radius-sm); padding: 12px 14px; margin-bottom: 20px;">
                            <div style="font-size: 0.8rem; color: #166534; font-weight: 600; margin-bottom: 4px;">
                                Kredensial Masuk Zoom:
                            </div>
                            <div style="font-size: 0.82rem; color: #14532d; display: flex; justify-content: space-between; margin-bottom: 2px;">
                                <span>Meeting ID:</span>
                                <strong>{{ $session->meeting_id ?: '-' }}</strong>
                            </div>
                            <div style="font-size: 0.82rem; color: #14532d; display: flex; justify-content: space-between;">
                                <span>Passcode:</span>
                                <strong>{{ $session->passcode ?: '-' }}</strong>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Card Actions -->
                <div style="padding: 16px 24px 20px; border-top: 1px solid var(--color-gray-100); background-color: var(--color-gray-50);">
                    @if($hasJoined)
                        <!-- Case 1: Student is already registered -->
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <a href="{{ $session->zoom_link }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary" style="width: 100%; justify-content: center; font-weight: 600; background: linear-gradient(135deg, #10b981, #059669); border-color: #059669;">
                                <i class="fi fi-rr-arrow-up-right-from-square"></i> Buka Ruang Zoom
                            </a>
                            <form action="{{ route('siswa.zoom-sessions.leave', $session) }}" method="POST" onsubmit="return confirmLeaveSession(event, this, '{{ addslashes($session->title) }}')">
                                @csrf
                                <button type="submit" class="btn btn-secondary btn-sm" style="width: 100%; justify-content: center; font-size: 0.8rem; color: var(--color-gray-600);">
                                    Batalkan Keikutsertaan
                                </button>
                            </form>
                        </div>
                    @elseif($isFull)
                        <!-- Case 2: Room is FULL and student has not joined -->
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <button type="button" class="btn btn-secondary" disabled style="width: 100%; justify-content: center; opacity: 0.7; cursor: not-allowed; background-color: #fee2e2; border-color: #fca5a5; color: #dc2626; font-weight: 600;">
                                <i class="fi fi-rr-lock"></i> Kuota Penuh
                            </button>
                            @if($alternativeSessions->isNotEmpty())
                                <a href="#alternatives-section" class="btn btn-secondary btn-sm" style="width: 100%; justify-content: center; font-size: 0.8rem; color: var(--color-primary); border-color: var(--color-primary); background-color: #fff;">
                                    <i class="fi fi-rr-arrow-down"></i> Cari Sesi Alternatif Lainnya
                                </a>
                            @endif
                        </div>
                    @else
                        <!-- Case 3: Available and student has not joined -->
                        <form action="{{ route('siswa.zoom-sessions.join', $session) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; font-weight: 600;">
                                <i class="fi fi-rr-sign-in-alt"></i> Gabung Sesi Zoom
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div style="margin-bottom: 40px;">
        {{ $sessions->links() }}
    </div>
@endif

<!-- ALTERNATIVE SESSIONS SHOWCASE (CRITICAL USER REQUIREMENT) -->
@if($alternativeSessions->isNotEmpty())
    <div id="alternatives-section" style="margin-top: 48px; padding-top: 32px; border-top: 2px dashed var(--color-gray-300);">
        <div style="margin-bottom: 24px;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
                <span style="background: rgba(234, 88, 12, 0.1); color: var(--color-primary); padding: 4px 10px; border-radius: var(--border-radius-sm); font-size: 0.8rem; font-weight: 700;">
                    SESI ALTERNATIF
                </span>
                <h2 style="font-size: 1.35rem; color: var(--color-dark); margin: 0;">
                    Tautan Zoom Lainnya yang Masih Tersedia
                </h2>
            </div>
            <p style="color: var(--color-gray-600); font-size: 0.95rem; margin: 0;">
                Jika sesi pilihan Anda telah penuh kuotanya, Anda dapat langsung bergabung ke sesi-sesi berikut yang masih membuka pendaftaran:
            </p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
            @foreach($alternativeSessions as $altSession)
                <div class="card" style="border-left: 4px solid var(--color-primary); display: flex; flex-direction: column; justify-content: space-between;">
                    <div class="card-body" style="padding: 20px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                            <span class="badge" style="background-color: #fee2e2; color: #dc2626; font-size: 0.75rem;">
                                {{ $altSession->formatted_pillar }}
                            </span>
                            <span style="font-size: 0.8rem; color: #059669; font-weight: 600;">
                                <i class="fi fi-rr-check"></i> {{ $altSession->remaining_seats }} kursi tersisa
                            </span>
                        </div>
                        <h4 style="font-size: 1.05rem; font-weight: 700; color: var(--color-dark); margin: 0 0 8px 0; line-height: 1.35;">
                            {{ $altSession->title }}
                        </h4>
                        <div style="font-size: 0.82rem; color: var(--color-gray-600); margin-bottom: 12px;">
                            <i class="fi fi-rr-calendar" style="margin-right: 4px; color: var(--color-primary);"></i>
                            {{ $altSession->start_time->format('d M Y, H:i') }} WIB
                        </div>

                        <!-- Progress Mini -->
                        <div style="height: 6px; width: 100%; background-color: var(--color-gray-200); border-radius: 3px; overflow: hidden; margin-bottom: 6px;">
                            <div style="height: 100%; width: {{ $altSession->fill_percentage }}%; background-color: #10b981; border-radius: 3px;"></div>
                        </div>
                        <span style="font-size: 0.75rem; color: var(--color-gray-500);">
                            {{ $altSession->participants_count }} dari {{ $altSession->capacity }} siswa telah bergabung
                        </span>
                    </div>

                    <div style="padding: 12px 20px; border-top: 1px solid var(--color-gray-100); background-color: var(--color-gray-50);">
                        <form action="{{ route('siswa.zoom-sessions.join', $altSession) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm" style="width: 100%; justify-content: center; font-weight: 600;">
                                <i class="fi fi-rr-sign-in-alt"></i> Gabung Sesi Alternatif Ini
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

@push('scripts')
<script>
function confirmLeaveSession(e, form, title) {
    e.preventDefault();
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Batalkan Keikutsertaan?',
            text: `Apakah Anda yakin ingin membatalkan keikutsertaan di "${title}"? Kursi Anda akan dilepaskan untuk siswa lain.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Batalkan',
            cancelButtonText: 'Kembali'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    } else {
        if (confirm(`Apakah Anda yakin ingin membatalkan keikutsertaan di "${title}"?`)) {
            form.submit();
        }
    }
    return false;
}

// Auto popup SweetAlert if joined or full
document.addEventListener('DOMContentLoaded', function() {
    @if(session('open_zoom_url'))
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: '{{ session('swal_title') }}',
                text: '{{ session('swal_text') }}',
                icon: '{{ session('swal_icon', 'success') }}',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="fi fi-rr-arrow-up-right-from-square"></i> Buka Zoom Sekarang',
                cancelButtonText: 'Tutup'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.open('{{ session('open_zoom_url') }}', '_blank');
                }
            });
        }
    @elseif(session('swal_title'))
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: '{{ session('swal_title') }}',
                text: '{{ session('swal_text') }}',
                icon: '{{ session('swal_icon', 'info') }}',
                confirmButtonColor: 'var(--color-primary)',
                confirmButtonText: 'Mengerti'
            });
        }
    @endif
});
</script>
@endpush
@endsection
