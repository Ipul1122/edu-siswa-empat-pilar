@extends('layouts.siswa')

@section('title', 'Detail Real Materi - Empat Pilar')

@section('content')
<div class="page-header" style="border: none; padding-bottom: 0;">
    <a href="{{ route('siswa.real-materi.index') }}" class="btn btn-secondary btn-sm">
        ← Kembali
    </a>
</div>

<div class="card" style="max-width: 600px; margin: 0 auto; width: 100%; box-shadow: var(--shadow-md); border-radius: var(--border-radius-lg);">
    <div class="card-body" style="padding: 40px; text-align: center; display: flex; flex-direction: column; gap: 24px;">
        <div>
            <span class="badge {{ $quiz->pillar }}" style="font-size: 0.85rem; margin-bottom: 12px; font-weight: 700; padding: 6px 14px;">
                Evaluasi Real {{ $quiz->formatted_pillar }}
            </span>
            <h2 style="font-size: 1.75rem; color: var(--color-dark); font-family: var(--font-heading);">{{ $quiz->title }}</h2>
        </div>

        <p style="color: var(--color-gray-600); line-height: 1.6; font-size: 1rem;">
            {{ $quiz->description ?? 'Uji pemahaman nyata Anda mengenai pilar kebangsaan ini melalui beberapa butir soal pilihan ganda.' }}
        </p>

        <!-- Quiz Meta Details -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin: 8px 0; background-color: var(--color-gray-100); padding: 20px; border-radius: var(--border-radius-md);">
            <div style="text-align: center; border-right: 1px solid var(--color-gray-200);">
                <div style="font-size: 1.75rem; font-weight: 700; color: var(--color-dark); display: flex; align-items: center; justify-content: center; gap: 6px;"><i class="fi fi-rr-clock" style="font-size: 1.5rem; line-height: 1;"></i> {{ $quiz->duration_minutes }}</div>
                <div style="font-size: 0.75rem; color: var(--color-gray-600); font-weight: 600; text-transform: uppercase; margin-top: 4px;">Menit Pengerjaan</div>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 1.75rem; font-weight: 700; color: var(--color-dark); display: flex; align-items: center; justify-content: center; gap: 6px;"><i class="fi fi-rr-list" style="font-size: 1.5rem; line-height: 1;"></i> {{ $quiz->questions_count }}</div>
                <div style="font-size: 0.75rem; color: var(--color-gray-600); font-weight: 600; text-transform: uppercase; margin-top: 4px;">Jumlah Pertanyaan</div>
            </div>
        </div>

        <div style="background-color: #fee2e2; border-left: 4px solid var(--color-danger); padding: 12px 16px; text-align: left; font-size: 0.85rem; color: #991b1b; border-radius: 0 var(--border-radius-sm) var(--border-radius-sm) 0;">
            <strong>⚠️ PENTING: Perhatian Sebelum Memulai!</strong>
            <ul style="margin-left: 20px; margin-top: 4px;">
                <li>Kuis Real Materi ini <strong>hanya dapat diambil 1 kali saja</strong>.</li>
                <li>Setelah menekan tombol mulai, waktu pengerjaan akan terus berjalan dan tidak dapat dihentikan.</li>
                <li>Pastikan koneksi internet Anda stabil and Anda siap mengerjakan.</li>
            </ul>
        </div>

        <div style="margin-top: 8px;">
            <button id="btn-start-real-materi" class="btn btn-primary" style="width: 100%; padding: 14px; font-size: 1.05rem; font-weight: 700; letter-spacing: 0.5px;">
                Mulai Real Materi Sekarang
            </button>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const btnStart = document.getElementById('btn-start-real-materi');
        if (btnStart) {
            btnStart.addEventListener('click', function() {
                Swal.fire({
                    title: 'Mulai Real Materi?',
                    text: 'Apakah anda sudah membaca terkait materi?',
                    icon: 'question',
                    showCancelButton: true,
                    showDenyButton: true,
                    confirmButtonText: 'Sudah',
                    denyButtonText: 'Belum',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#3085d6',
                    denyButtonColor: '#d32f2f',
                    cancelButtonColor: '#aaa',
                }).then((result) => {
                    if (result.isConfirmed) {
                        // sudah => lanjut kerjakan real materi
                        window.location.href = "{{ route('siswa.real-materi.start', $quiz) }}";
                    } else if (result.isDenied) {
                        // pergi ke halaman materi jika Belum
                        window.location.href = "{{ route('siswa.materials.index') }}";
                    } else {
                        // batal => back
                        window.location.href = "{{ route('siswa.real-materi.index') }}";
                    }
                });
            });
        }
    });
</script>
@endsection
