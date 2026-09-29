@extends('layouts.admin')

@section('title', 'Detail Sesi Zoom: ' . $zoom_session->title . ' - Admin Empat Pilar')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h1>Detail Pertemuan Zoom & Presensi</h1>
        <p>Pantau keterisian kuota ruangan, kelola tautan rapat, dan data siswa yang telah bergabung.</p>
    </div>
    <div class="page-actions" style="display: flex; gap: 8px; flex-wrap: wrap;">
        <a href="{{ route('admin.zoom-sessions.index') }}" class="btn btn-secondary">
            ← Kembali ke Daftar Sesi
        </a>
        <a href="{{ route('admin.zoom-sessions.edit', $zoom_session) }}" class="btn btn-secondary">
            <i class="fi fi-rr-edit"></i> Edit Sesi
        </a>
        <a href="{{ route('admin.zoom-sessions.export', $zoom_session) }}" class="btn btn-secondary" style="color: #059669; border-color: #a7f3d0; background-color: #ecfdf5;">
            <i class="fi fi-rr-file-excel"></i> Unduh Presensi (CSV)
        </a>
    </div>
</div>

<!-- Main Top Info Card -->
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-bottom: 28px;">
    <!-- Left Column: Session Details -->
    <div class="card">
        <div class="card-body" style="padding: 28px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 16px;">
                <div>
                    <div style="display: flex; gap: 8px; align-items: center; margin-bottom: 8px;">
                        <span class="badge" style="background-color: #fee2e2; color: #dc2626; font-size: 0.75rem; font-weight: 600;">
                            {{ $zoom_session->formatted_pillar }}
                        </span>
                        @if($zoom_session->is_active)
                            <span class="badge" style="background-color: #dcfce7; color: #15803d; font-size: 0.75rem; font-weight: 600;">
                                ● Sesi Terbuka
                            </span>
                        @else
                            <span class="badge" style="background-color: #f3f4f6; color: #6b7280; font-size: 0.75rem; font-weight: 600;">
                                ○ Nonaktif / Tertutup
                            </span>
                        @endif
                    </div>
                    <h2 style="font-size: 1.4rem; font-weight: 700; color: var(--color-dark); margin: 0 0 10px 0; line-height: 1.4;">
                        {{ $zoom_session->title }}
                    </h2>
                </div>
            </div>

            <!-- Description -->
            <p style="color: var(--color-gray-600); font-size: 0.95rem; line-height: 1.6; margin-bottom: 24px; background: var(--color-gray-50); padding: 14px 18px; border-radius: var(--border-radius-sm); border-left: 3px solid var(--color-primary);">
                {{ $zoom_session->description ?: 'Tidak ada deskripsi atau petunjuk tambahan untuk sesi Zoom ini.' }}
            </p>

            <!-- Zoom Link Box with Copy Button -->
            <div style="background-color: #fff; border: 1px solid var(--color-gray-200); border-radius: var(--border-radius-sm); padding: 16px 20px; margin-bottom: 20px;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; color: var(--color-gray-500); letter-spacing: 0.5px; margin-bottom: 6px;">
                    Tautan Masuk Zoom Meeting
                </label>
                <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 250px; background: var(--color-gray-100); padding: 8px 12px; border-radius: var(--border-radius-sm); font-family: monospace; font-size: 0.9rem; color: #1e40af; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                        <i class="fi fi-rr-link" style="margin-right: 6px; color: var(--color-gray-500);"></i>
                        <span id="zoomUrlText">{{ $zoom_session->zoom_link }}</span>
                    </div>
                    <button type="button" onclick="copyZoomLink()" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fi fi-rr-copy"></i> Salin Link
                    </button>
                    <a href="{{ $zoom_session->zoom_link }}" target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-sm" style="display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fi fi-rr-arrow-up-right-from-square"></i> Buka Ruang Zoom
                    </a>
                </div>
            </div>

            <!-- Credentials Grid -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                <div style="background-color: var(--color-gray-50); padding: 12px 16px; border-radius: var(--border-radius-sm);">
                    <span style="font-size: 0.8rem; color: var(--color-gray-500); display: block;">Meeting ID</span>
                    <strong style="font-size: 1rem; color: var(--color-dark); letter-spacing: 0.5px; font-family: monospace;">
                        {{ $zoom_session->meeting_id ?: '-' }}
                    </strong>
                </div>
                <div style="background-color: var(--color-gray-50); padding: 12px 16px; border-radius: var(--border-radius-sm);">
                    <span style="font-size: 0.8rem; color: var(--color-gray-500); display: block;">Passcode / Sandi Masuk</span>
                    <strong style="font-size: 1rem; color: var(--color-dark); font-family: monospace;">
                        {{ $zoom_session->passcode ?: '-' }}
                    </strong>
                </div>
                <div style="background-color: var(--color-gray-50); padding: 12px 16px; border-radius: var(--border-radius-sm);">
                    <span style="font-size: 0.8rem; color: var(--color-gray-500); display: block;">Jadwal Mulai</span>
                    <strong style="font-size: 0.95rem; color: var(--color-dark);">
                        {{ $zoom_session->start_time->format('d M Y, H:i') }} WIB
                    </strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Capacity Status & Quick Action Card -->
    <div style="display: flex; flex-direction: column; gap: 20px;">
        <div class="card">
            <div class="card-body" style="padding: 24px;">
                <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--color-dark); margin: 0 0 16px 0; display: flex; align-items: center; justify-content: space-between;">
                    <span>Kapasitas Peserta</span>
                    @if($zoom_session->is_full)
                        <span class="badge" style="background-color: #fee2e2; color: #dc2626; font-size: 0.75rem;">PENUH</span>
                    @else
                        <span class="badge" style="background-color: #ecfdf5; color: #059669; font-size: 0.75rem;">TERSEDIA</span>
                    @endif
                </h3>

                <!-- Big Numbers -->
                <div style="text-align: center; margin-bottom: 16px; padding: 12px; background: var(--color-gray-50); border-radius: var(--border-radius-sm);">
                    <div style="font-size: 2.2rem; font-weight: 800; color: {{ $zoom_session->is_full ? '#dc2626' : 'var(--color-primary)' }};">
                        {{ $zoom_session->participants_count }} <span style="font-size: 1.1rem; color: var(--color-gray-500); font-weight: 500;">/ {{ $zoom_session->capacity }}</span>
                    </div>
                    <span style="font-size: 0.85rem; color: var(--color-gray-600); font-weight: 500;">
                        {{ $zoom_session->remaining_seats }} kursi tersisa
                    </span>
                </div>

                <!-- Progress Bar -->
                <div style="margin-bottom: 20px;">
                    <div style="height: 10px; width: 100%; background-color: var(--color-gray-200); border-radius: 6px; overflow: hidden;">
                        <div style="height: 100%; width: {{ $zoom_session->fill_percentage }}%; background-color: {{ $zoom_session->fill_percentage >= 90 ? '#dc2626' : ($zoom_session->fill_percentage >= 70 ? '#f59e0b' : '#10b981') }}; border-radius: 6px; transition: width 0.4s ease;"></div>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: var(--color-gray-500); margin-top: 6px;">
                        <span>0%</span>
                        <span>{{ $zoom_session->fill_percentage }}% Terisi</span>
                        <span>100%</span>
                    </div>
                </div>

                <hr style="border: 0; border-top: 1px solid var(--color-gray-200); margin: 16px 0;">

                <!-- Status Switch & Delete Actions -->
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <form action="{{ route('admin.zoom-sessions.toggle', $zoom_session) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-secondary" style="width: 100%; justify-content: center; font-size: 0.85rem;">
                            <i class="fi {{ $zoom_session->is_active ? 'fi-rr-cross-circle' : 'fi-rr-check-circle' }}"></i>
                            {{ $zoom_session->is_active ? 'Nonaktifkan Sesi' : 'Aktifkan Sesi Kembali' }}
                        </button>
                    </form>

                    <form action="{{ route('admin.zoom-sessions.destroy', $zoom_session) }}" method="POST" onsubmit="return confirmDelete(event, this)">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-secondary" style="width: 100%; justify-content: center; font-size: 0.85rem; color: #dc2626; border-color: #fecaca; background: #fff5f5;">
                            <i class="fi fi-rr-trash"></i> Hapus Sesi Zoom Ini
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Manual Add Student Box -->
        <div class="card">
            <div class="card-body" style="padding: 20px;">
                <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--color-dark); margin: 0 0 12px 0; display: flex; align-items: center; gap: 8px;">
                    <i class="fi fi-rr-user-add" style="color: var(--color-primary);"></i> Tambah Siswa Manual
                </h4>
                
                @if($zoom_session->is_full)
                    <div style="background-color: #fee2e2; border-left: 3px solid #dc2626; padding: 10px 12px; border-radius: var(--border-radius-sm); font-size: 0.8rem; color: #991b1b;">
                        Kapasitas sesi sudah penuh ({{ $zoom_session->capacity }} peserta). Tingkatkan kapasitas di menu edit jika ingin mendaftarkan peserta baru.
                    </div>
                @else
                    <form action="{{ route('admin.zoom-sessions.participants.add', $zoom_session) }}" method="POST">
                        @csrf
                        <div class="form-group" style="margin-bottom: 12px;">
                            <label for="user_id" style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 6px; color: var(--color-dark);">
                                Pilih Siswa
                            </label>
                            <select name="user_id" id="user_id" class="form-control" required style="width: 100%; padding: 8px 12px; border-radius: var(--border-radius-sm); border: 1px solid var(--color-gray-300); font-size: 0.85rem;">
                                <option value="">-- Pilih Akun Siswa --</option>
                                @foreach($availableStudents as $student)
                                    <option value="{{ $student->id }}">
                                        {{ $student->name }} ({{ $student->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group" style="margin-bottom: 12px;">
                            <label for="notes" style="display: block; font-size: 0.8rem; font-weight: 600; margin-bottom: 6px; color: var(--color-dark);">
                                Keterangan / Catatan
                            </label>
                            <input type="text" name="notes" id="notes" class="form-control" placeholder="Contoh: Peserta undangan dapil" style="width: 100%; padding: 8px 12px; border-radius: var(--border-radius-sm); border: 1px solid var(--color-gray-300); font-size: 0.85rem;">
                        </div>

                        <button type="submit" class="btn btn-primary btn-sm" style="width: 100%; justify-content: center;">
                            <i class="fi fi-rr-plus"></i> Daftarkan Siswa ke Sesi
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Participant Table Card -->
<div class="card">
    <div class="card-header" style="padding: 20px 24px; border-bottom: 1px solid var(--color-gray-200); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--color-dark); margin: 0;">
                Daftar Siswa yang Bergabung ({{ $participants->total() }} Siswa)
            </h3>
            <span style="font-size: 0.85rem; color: var(--color-gray-500);">
                Daftar seluruh siswa yang telah mengklaim kursi dan hadir di sesi Zoom ini.
            </span>
        </div>

        <!-- Search Participant Form -->
        <form action="{{ route('admin.zoom-sessions.show', $zoom_session) }}" method="GET" style="display: flex; gap: 8px;">
            <div style="position: relative;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, sekolah..." style="padding: 8px 12px 8px 34px; border-radius: var(--border-radius-sm); border: 1px solid var(--color-gray-300); font-size: 0.85rem; min-width: 240px;">
                <i class="fi fi-rr-search" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--color-gray-400); font-size: 0.8rem;"></i>
            </div>
            <button type="submit" class="btn btn-secondary btn-sm">Cari</button>
            @if(request('search'))
                <a href="{{ route('admin.zoom-sessions.show', $zoom_session) }}" class="btn btn-secondary btn-sm" title="Reset">
                    <i class="fi fi-rr-refresh"></i>
                </a>
            @endif
        </form>
    </div>

    <div class="card-body" style="padding: 0;">
        @if($participants->isEmpty())
            <div style="text-align: center; padding: 48px 24px;">
                <div style="width: 64px; height: 64px; background: var(--color-gray-100); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; color: var(--color-gray-400); font-size: 1.8rem;">
                    <i class="fi fi-rr-users"></i>
                </div>
                <h4 style="font-size: 1.1rem; color: var(--color-dark); margin-bottom: 6px;">Belum Ada Siswa yang Bergabung</h4>
                <p style="color: var(--color-gray-500); font-size: 0.9rem; max-width: 450px; margin: 0 auto;">
                    @if(request('search'))
                        Tidak ada siswa yang cocok dengan kata pencarian "{{ request('search') }}".
                    @else
                        Sesi ini masih kosong. Siswa dapat mengklik tombol "Gabung Zoom" di dashboard mereka atau admin dapat mendaftarkannya secara manual.
                    @endif
                </p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: var(--color-gray-50); border-bottom: 1px solid var(--color-gray-200); text-align: left; font-size: 0.8rem; color: var(--color-gray-600); text-transform: uppercase; letter-spacing: 0.5px;">
                            <th style="padding: 14px 20px; width: 50px;">No</th>
                            <th style="padding: 14px 20px;">Nama Siswa</th>
                            <th style="padding: 14px 20px;">Sekolah & Kelas</th>
                            <th style="padding: 14px 20px;">Wilayah / Dapil</th>
                            <th style="padding: 14px 20px;">Waktu Join</th>
                            <th style="padding: 14px 20px;">Catatan</th>
                            <th style="padding: 14px 20px; text-align: right; width: 100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($participants as $index => $student)
                            <tr style="border-bottom: 1px solid var(--color-gray-200); font-size: 0.9rem;">
                                <td style="padding: 16px 20px; color: var(--color-gray-500);">
                                    {{ $participants->firstItem() + $index }}
                                </td>
                                <td style="padding: 16px 20px;">
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <div style="width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, var(--color-primary), var(--color-secondary)); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.9rem; flex-shrink: 0;">
                                            {{ strtoupper(substr($student->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <strong style="display: block; color: var(--color-dark);">{{ $student->name }}</strong>
                                            <span style="font-size: 0.8rem; color: var(--color-gray-500);">{{ $student->email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 16px 20px;">
                                    <div style="color: var(--color-dark); font-weight: 500;">
                                        {{ $student->school_name ?: '-' }}
                                    </div>
                                    <span style="font-size: 0.8rem; color: var(--color-gray-500);">
                                        {{ $student->class_name ?: '-' }}
                                    </span>
                                </td>
                                <td style="padding: 16px 20px;">
                                    <div style="color: var(--color-dark);">
                                        {{ $student->dapil ? 'Dapil ' . $student->dapil : '-' }}
                                    </div>
                                    <span style="font-size: 0.8rem; color: var(--color-gray-500);">
                                        {{ $student->regency->name ?? '-' }}
                                    </span>
                                </td>
                                <td style="padding: 16px 20px;">
                                    <div style="color: var(--color-dark); font-weight: 500;">
                                        {{ $student->pivot->joined_at ? \Carbon\Carbon::parse($student->pivot->joined_at)->timezone('Asia/Jakarta')->format('d M Y') : '-' }}
                                    </div>
                                    <span style="font-size: 0.8rem; color: var(--color-gray-500);">
                                        {{ $student->pivot->joined_at ? \Carbon\Carbon::parse($student->pivot->joined_at)->timezone('Asia/Jakarta')->format('H:i:s') . ' WIB' : '-' }}
                                    </span>
                                </td>
                                <td style="padding: 16px 20px;">
                                    <span class="badge" style="background-color: var(--color-gray-100); color: var(--color-gray-700); font-size: 0.75rem;">
                                        {{ $student->pivot->notes ?: 'Hadir' }}
                                    </span>
                                </td>
                                <td style="padding: 16px 20px; text-align: right;">
                                    <form action="{{ route('admin.zoom-sessions.participants.remove', [$zoom_session, $student]) }}" method="POST" onsubmit="return confirmRemoveStudent(event, this, '{{ addslashes($student->name) }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-secondary btn-sm" style="color: #dc2626; border-color: #fecaca; background: #fff5f5; padding: 6px 10px;" title="Keluarkan Peserta">
                                            <i class="fi fi-rr-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div style="padding: 16px 24px; border-top: 1px solid var(--color-gray-200);">
                {{ $participants->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function copyZoomLink() {
    const text = document.getElementById('zoomUrlText').innerText;
    navigator.clipboard.writeText(text).then(function() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Tautan Zoom berhasil disalin!',
                showConfirmButton: false,
                timer: 2000
            });
        } else {
            alert('Tautan Zoom berhasil disalin ke clipboard!');
        }
    });
}

function confirmDelete(e, form) {
    e.preventDefault();
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Hapus Sesi Zoom?',
            text: 'Semua data sesi dan riwayat presensi siswa yang bergabung pada sesi ini akan dihapus secara permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus Sekarang',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    } else {
        if (confirm('Apakah Anda yakin ingin menghapus sesi Zoom ini?')) {
            form.submit();
        }
    }
    return false;
}

function confirmRemoveStudent(e, form, studentName) {
    e.preventDefault();
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Keluarkan Siswa?',
            text: `Apakah Anda yakin ingin menghapus "${studentName}" dari sesi Zoom ini? Kuota akan bertambah 1 kursi.`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Keluarkan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    } else {
        if (confirm(`Apakah Anda yakin ingin menghapus "${studentName}" dari sesi Zoom ini?`)) {
            form.submit();
        }
    }
    return false;
}
</script>
@endpush
@endsection
