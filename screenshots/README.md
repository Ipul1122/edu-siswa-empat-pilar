# 📸 Dokumentasi Screenshot Antarmuka Aplikasi Edu Siswa Empat Pilar

Repositori ini memuat tangkapan layar (*high-resolution screenshots*) representasi seluruh fitur dan modul pada platform **Seleksi Online Edu Siswa Empat Pilar Kebangsaan MPR RI** (Model Ujian Beregu Sekolah SMA/SMK/MA).

Seluruh gambar disimpan dalam format `.png` dengan resolusi layar desktop jernih (*Retina/High-DPI Scale 1.5x, 1440x900 viewport*). Seluruh tangkapan layar fokus secara khusus pada **tampilan desktop** (*desktop only*).

---

## 🗂️ Daftar Lengkap Screenshot & Modul Aplikasi (36 File PNG)

### 🌐 Bagian 1: Halaman Publik & Autentikasi (6 File)

| No | File PNG | Modul / Halaman | URL Rute | Deskripsi Fitur |
|---|---|---|---|---|
| 01 | `01-landing-page.png` | Beranda Utama (Hero Viewport) | `/` | Tampilan hero landing page modern dengan pengenalan Seleksi Online Empat Pilar MPR RI, CTA Mulai Seleksi & Daftar Akun. |
| 02 | `02-landing-page-full.png` | Beranda Lengkap (Full Page) | `/` | Halaman beranda menyeluruh mencakup modul 4 pilar, keunggulan platform CBT, statistik nasional, hingga footer resmi. |
| 03 | `03-login-siswa.png` | Masuk Akun Siswa / Sekolah | `/login` | Halaman login khusus tim sekolah dengan panel informasi nilai 4 pilar dan formulir interaktif. |
| 04 | `04-register-siswa.png` | Registrasi Akun Sekolah | `/register` | Formulir pendaftaran akun sekolah mencakup nama sekolah, kontak PIC, pemetaan Provinsi & Kabupaten/Kota bertingkat. |
| 05 | `05-lupa-password.png` | Pemulihan Kata Sandi | `/forgot-password` | Alur pemulihan akun sekolah melalui verifikasi kode OTP aman ke email terdaftar. |
| 06 | `06-login-admin.png` | Masuk Administrator MPR RI | `/admin/login` | Halaman autentikasi khusus admin pengelola MPR RI dengan tema gelap elegan (*dark slate/navy*). |

---

### 🎓 Bagian 2: Portal Siswa / Akun Sekolah (School Mode - 13 File)

| No | File PNG | Modul / Halaman | URL Rute | Deskripsi Fitur |
|---|---|---|---|---|
| 07 | `07-siswa-dashboard.png` | Dashboard Sekolah (Viewport) | `/siswa/dashboard` | Ringkasan metrik belajar & seleksi: total paket seleksi, rata-rata skor tim, status hasil tes, sesi Zoom, dan radar chart kompetensi. |
| 08 | `08-siswa-dashboard-full.png` | Dashboard Sekolah (Full Page) | `/siswa/dashboard` | Tampilan menyeluruh dashboard siswa dilengkapi pintasan petunjuk teknis seleksi online CBT dan riwayat pengerjaan. |
| 09 | `09-siswa-tutorial-panduan.png` | Tutorial & Petunjuk Teknis (Viewport) | `/siswa/tutorial` | Panduan resmi teknis seleksi online: aturan 10 siswa dalam 1 laptop, pengawasan Zoom multi-device, dan teknologi auto-save. |
| 10 | `10-siswa-tutorial-panduan-full.png` | Tutorial & Petunjuk Teknis (Full Page) | `/siswa/tutorial` | Panduan lengkap petunjuk teknis mencakup solusi jaringan daerah terpencil (resilience offline cache) dan prosedur susulan. |
| 11 | `11-siswa-katalog-ujian-cbt.png` | Katalog Ujian Seleksi CBT | `/siswa/real-materi` | Daftar kartu paket soal seleksi online resmi (Pancasila, UUD 1945, NKRI, Bhinneka Tunggal Ika, dan TWK Kedinasan). |
| 12 | `12-siswa-konfirmasi-ujian.png` | Konfirmasi & Peraturan Ujian | `/siswa/real-materi/{id}` | Lembar tata tertib integritas ujian seleksi berkunci (hanya 1x pengerjaan) sebelum tombol mulai tes aktif. |
| 13 | `13-siswa-pengerjaan-ujian-cbt.png` | Pengerjaan Ujian CBT (Anti-Cheat) | `/siswa/real-materi/{id}/start` | Antarmuka ujian seleksi resmi layar penuh dengan auto-save asynchronous zero-reload dan deteksi integritas beralih tab. |
| 14 | `14-siswa-hasil-ujian-cbt.png` | Lembar Pengumuman Nilai Ujian | `/siswa/real-attempts/{id}/result` | Pengumuman skor akhir (skor 100), status passing grade, durasi pengerjaan, catatan integritas, serta pembahasan butir soal. |
| 15 | `15-siswa-sesi-zoom-pengawas.png` | Sesi Pengawasan Virtual Zoom | `/siswa/zoom-sessions` | Portal ruang temu Zoom pengawas langsung, status kuota batch (maks. 500 sekolah), fitur ping dan lapor gangguan koneksi. |
| 16 | `16-siswa-hasil-skor-mandiri.png` | Rekapitulasi Skor Mandiri Tim | `/siswa/hasil-tes` | Rekapitulasi riwayat skor mandiri sekolah yang aman & terproteksi kerahasiaannya untuk pemeringkatan Top 9 lolos. |
| 17 | `17-siswa-papan-peringkat.png` | Papan Peringkat (Leaderboard) Siswa | `/siswa/leaderboard` | Papan peringkat nasional perolehan poin seleksi dengan informasi ranking terupdate. |
| 18 | `18-siswa-profil-sekolah.png` | Profil Akun Sekolah (Viewport) | `/siswa/profile` | Informasi profil sekolah, nama kontak PIC/Guru Pembina, data daerah pemilihan (dapil), serta ganti kata sandi. |
| 19 | `19-siswa-profil-sekolah-full.png` | Profil Akun Sekolah (Full Page) | `/siswa/profile` | Formulir lengkap pembaruan biodata tim sekolah dan pengaturan kredensial keamanan akun. |

---

### 🛠️ Bagian 3: Portal Administrator MPR RI (Admin Panel - 17 File)

| No | File PNG | Modul / Halaman | URL Rute | Deskripsi Fitur |
|---|---|---|---|---|
| 20 | `20-admin-dashboard.png` | Dashboard Admin (Viewport) | `/admin/dashboard` | Monitoring sentral: total siswa terdaftar, paket soal seleksi, total ujian dikerjakan, sesi Zoom, dan diagram capaian per pilar. |
| 21 | `21-admin-dashboard-full.png` | Dashboard Admin (Full Page) | `/admin/dashboard` | Tampilan lengkap dashboard dengan tren aktivitas pengerjaan 7 hari terakhir, log pengerjaan terbaru, dan jalan pintas menu. |
| 22 | `22-admin-kelola-soal-seleksi.png` | Kelola Paket Soal Seleksi | `/admin/real-materi` | Tabel master paket seleksi dengan saklar toggle switch buka/tutup akses ujian secara serentak (*toggle-all*) maupun per paket. |
| 23 | `23-admin-tambah-paket-seleksi.png` | Tambah Paket Seleksi Baru | `/admin/real-materi/create` | Formulir pembuatan paket seleksi baru: nama paket, kategori pilar, alokasi waktu, serta penugasan provinsi khusus. |
| 24 | `24-admin-edit-paket-seleksi.png` | Edit Paket Seleksi | `/admin/real-materi/{id}/edit` | Formulir perbaruan pengaturan paket ujian seleksi dan opsi pengacakan butir soal/opsi jawaban. |
| 25 | `25-admin-bank-soal-seleksi.png` | Bank Soal Seleksi & Import | `/admin/quizzes/{id}` | Daftar butir soal seleksi, fitur unduh template CSV/Excel, dan tombol import soal instan bebas eror. |
| 26 | `26-admin-tambah-butir-soal.png` | Tambah Butir Soal Seleksi | `/admin/quizzes/{id}/questions/create` | Formulir pembuatan butir soal dengan input narasi pertanyaan, 5 opsi pilihan (A–E), kunci jawaban, dan teks pembahasan. |
| 27 | `27-admin-kelola-sesi-zoom.png` | Kelola Sesi Pengawasan Zoom | `/admin/zoom-sessions` | Tabel direktori ruang Zoom pengawas, saklar aktif/nonaktif, Meeting ID, passcode, dan statistik kuota 500 peserta. |
| 28 | `28-admin-kelola-sesi-zoom-full.png` | Kelola Sesi Zoom (Full Page) | `/admin/zoom-sessions` | Tampilan utuh manajemen ruang Zoom pengawas mencakup ringkasan sesi penuh dan riwayat jadwal webinar. |
| 29 | `29-admin-tambah-sesi-zoom.png` | Tambah Sesi Zoom Pengawas | `/admin/zoom-sessions/create` | Formulir pembuatan sesi Zoom baru: tautan URL meeting, batas kuota peserta, alokasi pilar, dan waktu pelaksanaan. |
| 30 | `30-admin-edit-sesi-zoom.png` | Edit Sesi Zoom Pengawas | `/admin/zoom-sessions/{id}/edit` | Formulir penyesuaian jadwal dan informasi ruang virtual pengawas yang sudah dijadwalkan. |
| 31 | `31-admin-pemantauan-siswa.png` | Pemantauan Peserta & Sekolah | `/admin/students` | Direktori seluruh akun sekolah terdaftar dengan filter pencarian instan, status pengerjaan, dan tombol aksi detail. |
| 32 | `32-admin-laporan-rekapitulasi.png` | Laporan Rekapitulasi Nilai | `/admin/students/report` | Lembar rekapitulasi nilai ujian komprehensif, capaian rata-rata per pilar, filter wilayah, dan tombol ekspor laporan. |
| 33 | `33-admin-detail-peserta.png` | Detail Akun Peserta & Retest | `/admin/students/{id}` | Histori ujian sekolah, rincian skor, catatan integritas, serta tombol *Grant Retest* untuk memberikan ujian susulan jika ada kendala jaringan. |
| 34 | `34-admin-papan-peringkat.png` | Papan Peringkat Admin (Viewport) | `/admin/leaderboard` | Papan peringkat berjenjang Nasional, per Provinsi, dan per Kab/Kota dengan tombol Ekspor Top 9 Lolos (.CSV) & Spreadsheet. |
| 35 | `35-admin-papan-peringkat-full.png` | Papan Peringkat Admin (Full Page) | `/admin/leaderboard` | Papan peringkat menyeluruh dengan visualisasi podium kehormatan Top 3 dan tabel perankingan lengkap seluruh peserta. |
| 36 | `36-admin-profil.png` | Profil Administrator | `/admin/profile` | Pengaturan akun pengelola sistem: informasi admin MPR RI, email resmi, dan formulir ubah kata sandi. |

---

## 🎨 Panduan Presentasi Canva (Docs-to-Deck)

Bagi Anda yang ingin mengonversi dokumentasi tangkapan layar ini menjadi **Slide Presentasi Canva** secara instan:

1. Buka [Canva](https://www.canva.com) dan buat dokumen baru: **Canva Docs**.
2. Salin teks per bab di bawah ini ke dalam Canva Docs tersebut.
3. Tempelkan (*drag-and-drop*) file gambar PNG yang sesuai dari folder `screenshots/` tepat di bawah masing-masing judul slide.
4. Klik tombol **Convert** / **Ubah ke Presentasi** di pojok kanan atas Canva Docs.
5. Pilih tema desain presentasi yang Anda sukai, lalu klik **Create My Presentation**.
6. Selesai! Slide presentasi profesional beresolusi tinggi tersusun rapi dengan gambar tangkapan layar asli aplikasi.

---

### 📝 Naskah Siap Salin untuk Canva Docs:

```markdown
# Seleksi Online Empat Pilar MPR RI
Platform Digital Seleksi Nasional, Pengawasan Virtual, dan Evaluasi Literasi Kebangsaan untuk Pelajar SMA/SMK/MA se-Indonesia.

---

# 01. Gerbang Seleksi: Beranda Utama
- Portal resmi pendaftaran dan pelaksanaan Seleksi Online Empat Pilar MPR RI.
- Menyediakan informasi alur seleksi, panduan teknis beregu, dan pengenalan esensi Empat Pilar Kebangsaan.
- Dirancang dengan antarmuka desktop modern, bersih, dan berwibawa.
[TEMPELKAN GAMBAR: 01-landing-page.png DI SINI]

---

# 02. Ekosistem Seleksi Terintegrasi
- Tampilan menyeluruh beranda dari ringkasan pilar hingga statistik peserta nasional.
- Menghubungkan pelajar dengan pilar Pancasila, UUD NRI 1945, NKRI, dan Bhinneka Tunggal Ika.
- Menjamin kemudahan navigasi bagi sekolah di seluruh pelosok Indonesia.
[TEMPELKAN GAMBAR: 02-landing-page-full.png DI SINI]

---

# 03. Autentikasi Khusus Tim Sekolah
- Gerbang masuk resmi bagi tim perwakilan sekolah (10 siswa dalam 1 akun resmi).
- Dilengkapi panel edukasi ringkas esensi Empat Pilar Kebangsaan untuk membakar semangat kompetisi.
- Sistem keamanan login dengan proteksi enkripsi sesi dan validasi terpusat.
[TEMPELKAN GAMBAR: 03-login-siswa.png DI SINI]

---

# 04. Pendaftaran Akun Sekolah & Pemetaan Wilayah
- Formulir pendaftaran resmi sekolah mencakup identitas PIC pendamping dan data sekolah.
- Dropdown wilayah bertingkat (38 Provinsi hingga Kabupaten/Kota) untuk akurasi data daerah.
- Pemetaan data wilayah terhubung langsung dengan algoritma pengacakan paket regional.
[TEMPELKAN GAMBAR: 04-register-siswa.png DI SINI]

---

# 05. Pusat Kendali Sekolah: Dashboard Peserta
- Panel ringkasan metrik: paket seleksi terselesaikan, rata-rata skor tim, dan jadwal sesi Zoom pengawas.
- Diagram radar capaian kompetensi per pilar kebangsaan secara visual dan terukur.
- Akses instan langsung ke petunjuk teknis ujian beregu dan modul Ujian Seleksi Online CBT.
[TEMPELKAN GAMBAR: 07-siswa-dashboard.png DI SINI]

---

# 06. Petunjuk Teknis & Ketentuan Ujian Beregu
- Modul tutorial resmi pelaksanaan seleksi: sistem 10 anak menggunakan 1 laptop sekolah utama.
- Prosedur pengawasan multi-device menggunakan kamera Zoom di perangkat kedua (HP/Tablet).
- Penjelasan teknologi *Offline-Resilient Caching* yang ramah terhadap sinyal daerah terpencil.
[TEMPELKAN GAMBAR: 09-siswa-tutorial-panduan.png DI SINI]

---

# 07. Katalog Modul Ujian Seleksi Online CBT
- Seluruh modul evaluasi resmi 4 pilar langsung tersedia tanpa tahap simulasi berbelit.
- Sistem kunci 1x pengerjaan (*one-attempt lock*) untuk menjamin objektivitas seleksi.
- Kategori terdistribusi: Pancasila, UUD NRI 1945, NKRI, Bhinneka Tunggal Ika, dan TWK Kedinasan.
[TEMPELKAN GAMBAR: 11-siswa-katalog-ujian-cbt.png DI SINI]

---

# 08. Pelaksanaan Ujian Seleksi Online CBT Layar Penuh
- Antarmuka CBT mode layar penuh (*fullpage*) dilengkapi deteksi integritas anti-cheat.
- Teknologi auto-save asynchronous zero-reload: jawaban tersimpan instan tanpa membebani kuota internet.
- Algoritma *Deterministic Shuffling* mengacak nomor soal dan opsi jawaban secara unik tiap sekolah.
[TEMPELKAN GAMBAR: 13-siswa-pengerjaan-ujian-cbt.png DI SINI]

---

# 09. Pengumuman Hasil Skor & Pembahasan Analisis
- Transparansi penilaian instan: perolehan nilai akhir (skor 100), durasi waktu, dan jumlah jawaban benar.
- Indikator kepatuhan integritas layar (0 pelanggaran / riwayat beralih layar).
- Tinjauan butir soal dan kunci pembahasan resmi untuk penguatan pemahaman kebangsaan.
[TEMPELKAN GAMBAR: 14-siswa-hasil-ujian-cbt.png DI SINI]

---

# 10. Ruang Virtual Pengawasan Zoom Terpadu
- Fasilitas pengawasan terintegrasi per batch dengan kapasitas hingga 500 sekolah per room.
- Fitur live ping koneksi dan tombol pelaporan gangguan jaringan langsung ke tim pengawas.
- Menjamin transparansi dan akuntabilitas seleksi nasional secara visual.
[TEMPELKAN GAMBAR: 15-siswa-sesi-zoom-pengawas.png DI SINI]

---

# 11. Dashboard Monitoring Administrator MPR RI
- Pusat monitoring utama pengelola seleksi nasional untuk memantau aktivitas seluruh sekolah se-Indonesia.
- Grafik batang capaian nilai per pilar dan grafik analitik aktivitas pengerjaan 7 hari terakhir.
- Ringkasan statistik real-time: total peserta terdaftar, paket soal terbit, dan sesi ujian aktif.
[TEMPELKAN GAMBAR: 20-admin-dashboard.png DI SINI]

---

# 12. Manajemen Paket Ujian & Saklar Kontrol Serentak
- Kelola master paket soal seleksi dengan saklar toggle buka/tutup akses ujian (*live toggle switch*).
- Opsi kontrol serentak (*toggle-all*) atau pengaktifan spesifik per sesi dan per wilayah.
- Fleksibilitas penetapan alokasi durasi menit dan penugasan provinsi khusus.
[TEMPELKAN GAMBAR: 22-admin-kelola-soal-seleksi.png DI SINI]

---

# 13. Bank Soal Seleksi & Fasilitas Import Excel/CSV
- Manajemen butir soal terstruktur dilengkapi opsi unduh template resmi format Excel/CSV.
- Fitur import soal massal instan dengan validasi duplikasi otomatis dan deteksi pembatas baris.
- Mendukung penambahan soal satuan lengkap dengan 5 opsi pilihan dan uraian pembahasan materi.
[TEMPELKAN GAMBAR: 25-admin-bank-soal-seleksi.png DI SINI]

---

# 14. Pemantauan Direktori Peserta & Hak Ujian Susulan
- Direktori lengkap sekolah peserta seleksi se-Indonesia dengan filter pencarian multi-kolom.
- Halaman detail riwayat ujian sekolah beserta rekaman waktu submit dan tingkat pelanggaran layar.
- Fitur *Grant Retest* resmi bagi administrator untuk memberikan token ujian susulan jika terjadi kendala teknis darurat.
[TEMPELKAN GAMBAR: 31-admin-pemantauan-siswa.png DI SINI]

---

# 15. Papan Peringkat Berjenjang & Penentuan Top 9 Lolos
- Papan peringkat berjenjang dengan filter Nasional, 38 Provinsi, hingga tingkat Kabupaten/Kota.
- Visualisasi podium kehormatan Top 3 dan tabel ranking otomatis berbasis skor tertinggi dan waktu tercepat.
- Fitur ekspor data instan: **Ekspor Top 9 Lolos (.CSV)** dan **Ekspor Spreadsheet Lengkap (.CSV)** untuk rekapitulasi nasional.
[TEMPELKAN GAMBAR: 34-admin-papan-peringkat.png DI SINI]
```

---

*Dokumentasi ini dibuat otomatis dan disinkronkan secara konsisten dengan struktur aplikasi Edu Siswa Empat Pilar MPR RI.*
