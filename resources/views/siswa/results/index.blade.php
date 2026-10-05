@extends('layouts.siswa')

@section('title', 'Hasil Ujian Seleksi Sekolah - Empat Pilar MPR RI')

@section('content')
<div class="content-wrapper" style="max-width: 1000px; margin: 0 auto; padding-bottom: 60px;">
    <!-- Header Banner -->
    <div style="background: linear-gradient(135deg, var(--color-primary) 0%, #1e1b4b 100%); color: #fff; padding: 32px 28px; border-radius: 16px; margin-bottom: 24px; box-shadow: 0 10px 25px rgba(0,0,0,0.12);">
        <div style="display: inline-flex; align-items: center; gap: 6px; background: rgba(255,255,255,0.15); padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; margin-bottom: 10px;">
            <i class="fi fi-rr-diploma"></i> LEMBAR HASIL SELEKSI RESMI
        </div>
        <h1 style="font-size: 1.75rem; font-family: var(--font-heading); margin-bottom: 6px; font-weight: 800;">
            Hasil Ujian Seleksi Online Tim Sekolah
        </h1>
        <p style="font-size: 0.92rem; opacity: 0.9; margin: 0; line-height: 1.5;">
            Laporan skor real-time hasil pengerjaan tim 10 siswa perwakilan <strong>{{ $school->school_name ?? $school->name }}</strong>.
        </p>
    </div>

    <!-- Official Notice Privacy -->
    <div style="background: #f8fafc; border-left: 4px solid #3b82f6; padding: 14px 18px; border-radius: 8px; margin-bottom: 24px; font-size: 0.86rem; color: #334155; line-height: 1.5;">
        <div style="font-weight: 700; color: #1e40af; margin-bottom: 2px;">
            <i class="fi fi-rr-lock"></i> Ketentuan Kerahasiaan Nilai Seleksi:
        </div>
        Sesuai regulasi panitia Seleksi Nasional Empat Pilar MPR RI, <strong>setiap sekolah hanya dapat melihat hasil nilai milik sekolahnya sendiri</strong>. Papan peringkat keseluruhan peserta bersifat tertutup dan direkap langsung ke spreadsheet panitia pusat. Pengumuman resmi <strong>9 Sekolah Terbaik (Top 9)</strong> yang lolos ke babak berikutnya akan diumumkan pada hari <strong>Rabu</strong>.
    </div>

    @if($attempts->count() > 0)
        <!-- Latest Attempt Highlight Card -->
        @php
            $latest = $latestAttempt;
            $durM = floor($latest->duration_seconds_taken / 60);
            $durS = $latest->duration_seconds_taken % 60;
        @endphp
        <div style="background: var(--color-white); border-radius: 14px; border: 1px solid var(--color-gray-200); box-shadow: var(--shadow-sm); overflow: hidden; margin-bottom: 30px;">
            <div style="padding: 24px; border-bottom: 1px solid var(--color-gray-200); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                <div>
                    <span class="badge" style="background: rgba(var(--color-primary-rgb), 0.1); color: rgb(var(--color-primary-rgb)); font-size: 0.75rem; padding: 4px 10px; border-radius: 6px; font-weight: 700;">
                        {{ $latest->quiz->title ?? 'Ujian Seleksi Resmi' }}
                    </span>
                    @if($latest->is_retest)
                        <span class="badge" style="background: #fef3c7; color: #92400e; font-size: 0.75rem; padding: 4px 10px; border-radius: 6px; font-weight: 700; margin-left: 6px;">
                            🔄 Sesi Tes Ulang
                        </span>
                    @endif
                    <div style="font-size: 0.8rem; color: var(--color-gray-500); margin-top: 6px;">
                        Diselesaikan pada: {{ $latest->created_at->translatedFormat('l, d F Y - H:i') }} WIB
                    </div>
                </div>

                <div style="display: flex; gap: 8px;">
                    <a href="{{ route('siswa.real-materi.result', $latest) }}" class="btn btn-secondary btn-sm" style="display: inline-flex; align-items: center; gap: 6px; font-size: 0.8rem;">
                        <i class="fi fi-rr-eye"></i> Rincian Lembar Jawaban
                    </a>
                </div>
            </div>

            <div style="padding: 24px;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 20px;">
                    <div style="background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.2); padding: 20px; border-radius: 12px; text-align: center;">
                        <div style="font-size: 0.72rem; color: #047857; text-transform: uppercase; font-weight: 700;">Skor Nilai Akhir</div>
                        <div style="font-size: 2.8rem; font-weight: 900; color: #047857; line-height: 1.1; margin: 4px 0;">
                            {{ $latest->score }}
                        </div>
                        <div style="font-size: 0.75rem; color: #047857; font-weight: 600;">Skala 0 - 100</div>
                    </div>

                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 20px; border-radius: 12px; text-align: center;">
                        <div style="font-size: 0.72rem; color: #64748b; text-transform: uppercase; font-weight: 700;">Jawaban Benar</div>
                        <div style="font-size: 2.5rem; font-weight: 800; color: #0f172a; line-height: 1.1; margin: 4px 0;">
                            {{ $latest->correct_answers }}
                        </div>
                        <div style="font-size: 0.75rem; color: #64748b;">dari {{ $latest->total_questions }} Butir Soal</div>
                    </div>

                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 20px; border-radius: 12px; text-align: center;">
                        <div style="font-size: 0.72rem; color: #64748b; text-transform: uppercase; font-weight: 700;">Durasi Pengerjaan</div>
                        <div style="font-size: 2.5rem; font-weight: 800; color: #0f172a; line-height: 1.1; margin: 4px 0;">
                            {{ $durM }}:{{ $durS < 10 ? '0' : '' }}{{ $durS }}
                        </div>
                        <div style="font-size: 0.75rem; color: #64748b;">Menit : Detik</div>
                    </div>

                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 20px; border-radius: 12px; text-align: center;">
                        <div style="font-size: 0.72rem; color: #64748b; text-transform: uppercase; font-weight: 700;">Status Rekap</div>
                        <div style="font-size: 1.6rem; font-weight: 800; color: #059669; line-height: 1.3; margin: 10px 0;">
                            ✓ Terekam
                        </div>
                        <div style="font-size: 0.75rem; color: #64748b;">Database Panitia Pusat</div>
                    </div>
                </div>

                <!-- Identity Table -->
                <table style="width: 100%; font-size: 0.86rem; color: var(--color-dark); border-collapse: collapse; margin-top: 10px;">
                    <tr style="border-bottom: 1px solid var(--color-gray-100);">
                        <td style="padding: 8px 0; color: var(--color-gray-600); width: 200px;">Nama Sekolah:</td>
                        <td style="padding: 8px 0; font-weight: 700;">{{ $school->school_name ?? $school->name }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--color-gray-100);">
                        <td style="padding: 8px 0; color: var(--color-gray-600);">Wilayah Provinsi:</td>
                        <td style="padding: 8px 0; font-weight: 600;">{{ $school->province->name ?? '-' }} ({{ $school->regency->name ?? '-' }})</td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--color-gray-100);">
                        <td style="padding: 8px 0; color: var(--color-gray-600);">Guru Pembina / PIC:</td>
                        <td style="padding: 8px 0; font-weight: 600;">{{ $school->pic_name ?? 'Pembina Sekolah' }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid var(--color-gray-100);">
                        <td style="padding: 8px 0; color: var(--color-gray-600);">WhatsApp Terdaftar:</td>
                        <td style="padding: 8px 0; font-weight: 600;">{{ $school->whatsapp ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        @if($attempts->count() > 1)
            <!-- History of attempts if re-test occurred -->
            <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--color-dark); margin-bottom: 14px;">
                Riwayat Pengerjaan Sekolah:
            </h3>
            <div style="background: var(--color-white); border-radius: 12px; border: 1px solid var(--color-gray-200); overflow: hidden;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                    <thead>
                        <tr style="background: var(--color-gray-100); text-align: left; color: var(--color-gray-600);">
                            <th style="padding: 12px 16px;">Sesi Ujian</th>
                            <th style="padding: 12px 16px;">Waktu</th>
                            <th style="padding: 12px 16px;">Skor</th>
                            <th style="padding: 12px 16px;">Tipe</th>
                            <th style="padding: 12px 16px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attempts as $a)
                            <tr style="border-bottom: 1px solid var(--color-gray-200);">
                                <td style="padding: 12px 16px; font-weight: 600;">{{ $a->quiz->title ?? 'Ujian Seleksi' }}</td>
                                <td style="padding: 12px 16px;">{{ $a->created_at->format('d/m/Y H:i') }} WIB</td>
                                <td style="padding: 12px 16px; font-weight: 800; color: #047857;">{{ $a->score }}</td>
                                <td style="padding: 12px 16px;">
                                    @if($a->is_retest)
                                        <span class="badge" style="background: #fef3c7; color: #92400e; font-size: 0.72rem; padding: 2px 6px;">Tes Susulan</span>
                                    @else
                                        <span class="badge" style="background: #e2e8f0; color: #334155; font-size: 0.72rem; padding: 2px 6px;">Sesi Utama</span>
                                    @endif
                                </td>
                                <td style="padding: 12px 16px;">
                                    <a href="{{ route('siswa.real-materi.result', $a) }}" class="btn btn-secondary btn-sm" style="font-size: 0.75rem; padding: 4px 8px;">
                                        Lihat Hasil
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @else
        <!-- No attempt yet -->
        <div style="background: var(--color-white); border-radius: 14px; border: 1px solid var(--color-gray-200); padding: 40px 20px; text-align: center;">
            <div style="font-size: 3rem; margin-bottom: 12px;">📝</div>
            <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--color-dark); margin-bottom: 8px;">
                Sekolah Belum Mengerjakan Ujian Seleksi
            </h3>
            <p style="font-size: 0.9rem; color: var(--color-gray-600); max-width: 500px; margin: 0 auto 24px auto; line-height: 1.5;">
                Tim 10 siswa perwakilan {{ $school->school_name ?? $school->name }} belum menyelesaikan sesi seleksi online resmi. Silakan langsung masuk ke ruang seleksi untuk memulai ujian.
            </p>
            <div style="display: flex; gap: 12px; justify-content: center;">
                <a href="{{ route('siswa.real-materi.index') }}" class="btn btn-primary" style="padding: 12px 24px; font-weight: 700;">
                    🚀 Masuk Ruang Seleksi CBT
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
