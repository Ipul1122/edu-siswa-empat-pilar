@extends('layouts.siswa')

@section('title', 'Tutorial & Petunjuk Teknis Seleksi - Empat Pilar MPR RI')

@section('content')
<div class="content-wrapper" style="max-width: 1050px; margin: 0 auto; padding-bottom: 60px;">
    <!-- Page Header -->
    <div style="background: linear-gradient(135deg, var(--color-primary) 0%, #1e1b4b 100%); color: #fff; padding: 32px 28px; border-radius: 16px; margin-bottom: 28px; box-shadow: 0 10px 25px rgba(0,0,0,0.12); position: relative; overflow: hidden;">
        <div style="position: absolute; right: -20px; top: -20px; opacity: 0.08; font-size: 15rem; font-family: sans-serif; font-weight: 900; pointer-events: none;">4</div>
        <div style="position: relative; z-index: 2;">
            <div style="display: inline-flex; align-items: center; gap: 8px; background: rgba(255,255,255,0.15); padding: 4px 12px; border-radius: 20px; font-size: 0.78rem; font-weight: 600; margin-bottom: 12px; backdrop-filter: blur(8px);">
                <i class="fi fi-rr-book-alt"></i> PANDUAN RESMI SELEKSI NASIONAL 2026
            </div>
            <h1 style="font-size: 1.85rem; font-family: var(--font-heading); margin-bottom: 8px; font-weight: 800;">
                Tutorial Penggunaan & Petunjuk Teknis Tes Online
            </h1>
            <p style="font-size: 0.95rem; opacity: 0.9; max-width: 720px; line-height: 1.5; margin: 0;">
                Petunjuk teknis pengerjaan tes beregu (10 siswa dalam 1 perangkat), panduan ruang pengawas Zoom, toleransi jaringan internet daerah, dan prosedur tes susulan.
            </p>
        </div>
    </div>

    <!-- Stepper 4 Langkah Utama -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 30px;">
        <div style="background: var(--color-white); padding: 20px; border-radius: 12px; border: 1px solid var(--color-gray-200); box-shadow: var(--shadow-sm); display: flex; gap: 14px; align-items: flex-start;">
            <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(var(--color-primary-rgb), 0.1); color: rgb(var(--color-primary-rgb)); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.1rem; flex-shrink: 0;">
                1
            </div>
            <div>
                <h4 style="font-size: 0.92rem; font-weight: 700; margin-bottom: 4px; color: var(--color-dark);">Tim 10 Siswa, 1 Layar</h4>
                <p style="font-size: 0.8rem; color: var(--color-gray-600); margin: 0; line-height: 1.4;">
                    Tes dikerjakan secara kolaboratif beregu menggunakan 1 Laptop/PC resmi sekolah.
                </p>
            </div>
        </div>

        <div style="background: var(--color-white); padding: 20px; border-radius: 12px; border: 1px solid var(--color-gray-200); box-shadow: var(--shadow-sm); display: flex; gap: 14px; align-items: flex-start;">
            <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(59, 130, 246, 0.1); color: #2563eb; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.1rem; flex-shrink: 0;">
                2
            </div>
            <div>
                <h4 style="font-size: 0.92rem; font-weight: 700; margin-bottom: 4px; color: var(--color-dark);">Zoom di Device Lain</h4>
                <p style="font-size: 0.8rem; color: var(--color-gray-600); margin: 0; line-height: 1.4;">
                    Kamera pengawas Zoom dapat dibuka via HP/tablet terpisah (maks 500 sekolah per batch).
                </p>
            </div>
        </div>

        <div style="background: var(--color-white); padding: 20px; border-radius: 12px; border: 1px solid var(--color-gray-200); box-shadow: var(--shadow-sm); display: flex; gap: 14px; align-items: flex-start;">
            <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(16, 185, 129, 0.1); color: #059669; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.1rem; flex-shrink: 0;">
                3
            </div>
            <div>
                <h4 style="font-size: 0.92rem; font-weight: 700; margin-bottom: 4px; color: var(--color-dark);">Auto-Save Aman</h4>
                <p style="font-size: 0.8rem; color: var(--color-gray-600); margin: 0; line-height: 1.4;">
                    Setiap pilihan tersimpan otomatis di perangkat dan cloud. Aman jika sinyal drop sejenak.
                </p>
            </div>
        </div>

        <div style="background: var(--color-white); padding: 20px; border-radius: 12px; border: 1px solid var(--color-gray-200); box-shadow: var(--shadow-sm); display: flex; gap: 14px; align-items: flex-start;">
            <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(245, 158, 11, 0.1); color: #d97706; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.1rem; flex-shrink: 0;">
                4
            </div>
            <div>
                <h4 style="font-size: 0.92rem; font-weight: 700; margin-bottom: 4px; color: var(--color-dark);">Skor Real-Time</h4>
                <p style="font-size: 0.8rem; color: var(--color-gray-600); margin: 0; line-height: 1.4;">
                    Skor mandiri sekolah langsung keluar saat submit untuk penentuan Top 9 Lolos.
                </p>
            </div>
        </div>
    </div>

    <!-- Detail Accordion / Cards -->
    <div style="display: flex; flex-direction: column; gap: 20px;">
        <!-- Aturan Ujian Beregu -->
        <div class="card" style="background: var(--color-white); border-radius: 12px; border: 1px solid var(--color-gray-200); padding: 24px; box-shadow: var(--shadow-sm);">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 14px;">
                <i class="fi fi-rr-users" style="font-size: 1.3rem; color: rgb(var(--color-primary-rgb));"></i>
                <h3 style="font-size: 1.15rem; font-weight: 700; margin: 0; color: var(--color-dark);">1. Ketentuan Ujian Tim Sekolah (10 Siswa, 1 Device)</h3>
            </div>
            <ul style="padding-left: 20px; color: var(--color-gray-700); font-size: 0.9rem; line-height: 1.7; margin: 0;">
                <li>Ujian seleksi ini bersifat <strong>beregu</strong>: 1 sekolah diwakili oleh 10 anak siswa SMA/SMK/MA terpilih.</li>
                <li>Hanya diperbolehkan menggunakan <strong>1 laptop atau komputer utama</strong> untuk menjawab soal ujian di aplikasi.</li>
                <li>Guru Pembina / PIC mendampingi di ruangan untuk memastikan kelancaran teknis dan pengawasan perangkat.</li>
                <li>Masing-masing soal berbentuk <strong>Pilihan Ganda (A, B, C, D, E)</strong> dengan pengacakan butir soal dan urutan opsi yang unik untuk setiap sekolah.</li>
            </ul>
        </div>

        <!-- Ketentuan Ruang Zoom -->
        <div class="card" style="background: var(--color-white); border-radius: 12px; border: 1px solid var(--color-gray-200); padding: 24px; box-shadow: var(--shadow-sm);">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 14px;">
                <i class="fi fi-rr-video-camera-alt" style="font-size: 1.3rem; color: #2563eb;"></i>
                <h3 style="font-size: 1.15rem; font-weight: 700; margin: 0; color: var(--color-dark);">2. Tata Cara Pengawasan Zoom Virtual (Maks. 500 Sekolah/Batch)</h3>
            </div>
            <ul style="padding-left: 20px; color: var(--color-gray-700); font-size: 0.9rem; line-height: 1.7; margin: 0;">
                <li>Sesi Zoom dibagi ke dalam <strong>Batch</strong> dengan batas kuota maksimal <strong>500 sekolah per room</strong>.</li>
                <li><strong>Multi-Device Ready:</strong> Tim sekolah disarankan menyalakan kamera Zoom menggunakan perangkat kedua (Smartphone atau Tablet) yang diletakkan di sudut ruangan sehingga menyorot ke-10 siswa dan layar pengerjaan secara jelas.</li>
                <li>Sistem platform otomatis mendeteksi kehadiran Zoom sekolah Anda. Jika terjadi putus koneksi Zoom, pengawas akan melihat status peringatan.</li>
            </ul>
        </div>

        <!-- Solusi Internet Daerah Terpencil (Resilience) -->
        <div class="card" style="background: var(--color-white); border-radius: 12px; border: 1px solid var(--color-gray-200); padding: 24px; box-shadow: var(--shadow-sm);">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 14px;">
                <i class="fi fi-rr-wifi-slash" style="font-size: 1.3rem; color: #d97706;"></i>
                <h3 style="font-size: 1.15rem; font-weight: 700; margin: 0; color: var(--color-dark);">3. Optimalisasi Jaringan Terbatas & Solusi Kendala Internet Daerah</h3>
            </div>
            <p style="font-size: 0.9rem; color: var(--color-gray-700); line-height: 1.6; margin-bottom: 12px;">
                Aplikasi ini dirancang khusus dengan teknologi <strong>Low-Bandwidth & Offline-Resilient Caching</strong> yang sangat ramah terhadap sinyal terbatas di wilayah terpencil:
            </p>
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 14px 16px; margin-bottom: 14px;">
                <div style="display: flex; align-items: center; gap: 8px; font-weight: 700; font-size: 0.88rem; color: #0f172a; margin-bottom: 4px;">
                    <i class="fi fi-rr-check-circle" style="color: #10b981;"></i> Fitur Penyimpanan Cadangan Otomatis (Local Cache):
                </div>
                <p style="font-size: 0.84rem; color: #475569; margin: 0; line-height: 1.5;">
                    Setiap opsi yang diklik tim sekolah langsung disimpan di memori peramban secara instan (< 10 ms). Jika koneksi internet terputus di tengah tes, layar <strong>tidak akan tertutup/blank</strong>. Siswa tetap dapat melanjutkan membaca dan memilih jawaban. Begitu sinyal kembali normal, sistem secara otomatis menyinkronkan seluruh jawaban ke server.
                </p>
            </div>
            <div style="background: #fffbeb; border: 1px solid #fef3c7; border-radius: 8px; padding: 14px 16px;">
                <div style="display: flex; align-items: center; gap: 8px; font-weight: 700; font-size: 0.88rem; color: #b45309; margin-bottom: 4px;">
                    <i class="fi fi-rr-refresh" style="color: #b45309;"></i> Kebijakan Tes Ulang (Force Majeure):
                </div>
                <p style="font-size: 0.84rem; color: #92400e; margin: 0; line-height: 1.5;">
                    Apabila terjadi gangguan fatal pada hari tes (seperti pemadaman listrik total atau jaringan kabel optik daerah terputus), Pembina Sekolah dapat segera melapor melalui <strong>Chatbot CS / Hotline WhatsApp Resmi</strong>. Admin panitia memiliki wewenang untuk memberikan status <strong>[Izinkan Tes Ulang]</strong> agar sekolah dapat mengikuti sesi susulan.
                </p>
            </div>
        </div>

        <!-- Sistem Keamanan Anti-Screenshot -->
        <div class="card" style="background: var(--color-white); border-radius: 12px; border: 1px solid var(--color-gray-200); padding: 24px; box-shadow: var(--shadow-sm);">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 14px;">
                <i class="fi fi-rr-shield-check" style="font-size: 1.3rem; color: var(--color-danger);"></i>
                <h3 style="font-size: 1.15rem; font-weight: 700; margin: 0; color: var(--color-dark);">4. Proteksi Anti-Screenshot & Integritas Layar</h3>
            </div>
            <ul style="padding-left: 20px; color: var(--color-gray-700); font-size: 0.9rem; line-height: 1.7; margin: 0;">
                <li><strong>Watermark Forensik Dinamis:</strong> Layar soal dilindungi watermark transparan berisi nama sekolah, provinsi, IP, dan jam realtime WIB. Memotret layar menggunakan HP tetap akan menampilkan identitas lengkap sekolah.</li>
                <li><strong>Deteksi Pindah Jendela (Blackout):</strong> Dilarang membuka tab baru, mencari di Google, atau beralih aplikasi. Layar otomatis tertutup layar hitam pekat dan mencatat pelanggaran. Batas toleransi maksimal 3 kali sebelum ujian dihentikan.</li>
                <li><strong>Blokir Fungsi:</strong> Klik kanan, pintasan cetak (Ctrl+P), inspect element (F12), dan capture print screen otomatis diblokir sistem.</li>
            </ul>
        </div>
    </div>

    <!-- Action Buttons -->
    <div style="margin-top: 32px; display: flex; gap: 14px; flex-wrap: wrap;">
        <a href="{{ route('siswa.real-materi.index') }}" class="btn btn-primary" style="padding: 12px 28px; font-weight: 700; display: inline-flex; align-items: center; gap: 8px;">
            <i class="fi fi-rr-document-signed"></i> Langsung Masuk Ruang Seleksi CBT ➔
        </a>
    </div>
</div>
@endsection
