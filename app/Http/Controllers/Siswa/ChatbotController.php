<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatbotController extends Controller
{
    /**
     * Process query from student/school and return instant, accurate response.
     */
    public function ask(Request $request)
    {
        $question = trim(strtolower((string) $request->input('message', '')));
        $user = Auth::user();
        $userProvince = $user && $user->province ? $user->province->name : 'Indonesia';
        $userSchool = $user ? ($user->school_name ?? $user->name) : 'Sekolah';
        $userPic = $user ? ($user->pic_name ?? 'Bpk/Ibu Guru') : 'Pembina';

        if (empty($question)) {
            return response()->json([
                'success' => false,
                'reply' => 'Halo! Ada yang bisa kami bantu seputar Seleksi Online Empat Pilar MPR RI?'
            ]);
        }

        $reply = $this->generateAnswer($question, $userProvince, $userSchool, $userPic);

        return response()->json([
            'success' => true,
            'reply' => $reply['text'],
            'suggestions' => $reply['suggestions'] ?? [],
            'hotline_wa' => 'https://wa.me/6281234567890?text=' . urlencode("Halo Tim CS Empat Pilar MPR RI, saya {$userPic} dari {$userSchool} ({$userProvince}). Ingin konsultasi mengenai: {$question}"),
        ]);
    }

    /**
     * Intelligent rules & knowledge base engine covering all provinces and topics.
     */
    protected function generateAnswer(string $q, string $province, string $school, string $pic): array
    {
        // 1. Internet connection / trouble in remote provinces
        if (str_contains($q, 'internet') || str_contains($q, 'sinyal') || str_contains($q, 'putus') || str_contains($q, 'offline') || str_contains($q, 'jaringan') || str_contains($q, 'lemot') || str_contains($q, 'mati listrik')) {
            return [
                'text' => "📶 **Panduan Sinyal & Internet Daerah Terpencil:**\n\n1. **Aman & Tidak Hilang:** Seluruh jawaban yang diklik tim sekolah tersimpan secara instan di memori perangkat (*Local Cache*).\n2. **Tetap Lanjutkan Soal:** Jika koneksi terputus, jangan panik dan jangan tutup peramban. Soal yang telah termuat tetap bisa dibaca dan dijawab.\n3. **Auto-Sync:** Begitu koneksi internet tersambung kembali, seluruh jawaban akan tersinkron otomatis ke server panitia.\n4. **Kendala Fatal (Mati Listrik/Sinyal Total):** Jika terjadi putus total berjam-jam pada hari-H, Guru Pembina berhak mengajukan **Tes Ulang (Susulan)** dengan menghubungi panitia CS.",
                'suggestions' => ['Bagaimana cara mengajukan tes ulang?', 'Berapa batas waktu ujian?', 'Ketentuan sesi Zoom']
            ];
        }

        // 2. Re-test / Kendala hari-H
        if (str_contains($q, 'tes ulang') || str_contains($q, 'ulang') || str_contains($q, 'susulan') || str_contains($q, 'trouble') || str_contains($q, 'reset') || str_contains($q, 'kendala')) {
            return [
                'text' => "🔄 **Prosedur Pengajuan Tes Ulang (Sesi Susulan):**\n\n- Sesuai peraturan panitia, jika sekolah Anda (**{$school}**) mengalami kendala teknis *force majeure* (gangguan jaringan massal, cuaca buruk di kepulauan, atau pemadaman listrik PLN) saat hari pelaksanaan seleksi:\n- **Langkah 1:** Guru Pembina ({$pic}) segera melaporkan ke Call Center / WhatsApp CS resmi dengan bukti foto/video kondisi setempat.\n- **Langkah 2:** Admin panitia akan memverifikasi dan mengaktifkan tombol **[Izinkan Tes Ulang]** di sistem.\n- **Langkah 3:** Tim sekolah akan dijadwalkan kembali mengikuti sesi tes susulan resmi.",
                'suggestions' => ['Hubungi Call Center WA', 'Ketentuan perangkat tim', 'Jadwal seleksi']
            ];
        }

        // 3. User & Device Rules (10 Anak, 1 Device)
        if (str_contains($q, '10') || str_contains($q, 'anak') || str_contains($q, 'device') || str_contains($q, 'perangkat') || str_contains($q, 'laptop') || str_contains($q, 'beregu') || str_contains($q, 'siswa') || str_contains($q, 'tim')) {
            return [
                'text' => "👥 **Ketentuan Peserta & Perangkat Ujian:**\n\n- **1 Sekolah = Tim 10 Siswa:** Setiap sekolah diwakili oleh 1 tim yang terdiri dari 10 siswa aktif SMA/SMK/MA.\n- **1 Device Pengerjaan:** Seluruh tim 10 siswa berdiskusi dan menjawab soal bersama-sama di depan **1 Laptop atau PC utama**.\n- **Device Kedua (Opsional untuk Zoom):** Kamera pengawas Zoom disarankan dibuka menggunakan HP atau tablet terpisah agar tidak mengganggu layar pengerjaan laptop utama.",
                'suggestions' => ['Bagaimana cara membuka Zoom?', 'Berapa jumlah soal?', 'Proteksi anti-screenshot']
            ];
        }

        // 4. Zoom Virtual & Kapasitas 500
        if (str_contains($q, 'zoom') || str_contains($q, 'kamera') || str_contains($q, 'pengawas') || str_contains($q, 'batch') || str_contains($q, 'link zoom')) {
            return [
                'text' => "📹 **Pengawasan Zoom Virtual Seleksi:**\n\n- **Kapasitas Batch:** Setiap sesi Zoom dibatasi maksimal **500 sekolah per batch** untuk menjaga kelancaran koneksi.\n- **Tersambung Otomatis:** Saat sekolah berhasil masuk sesi, sistem otomatis mencatat status kehadiran (*Join*).\n- **Device Terpisah:** Anda boleh membuka ruang Zoom di HP terpisah, laptop pengerjaan tetap terhubung dengan sistem seleksi.\n- **Terkendala Keluar:** Jika Zoom terputus karena sinyal di daerah, segera sambungkan kembali tanpa perlu menghentikan pengerjaan soal CBT.",
                'suggestions' => ['Lihat jadwal Zoom sekolah saya', 'Panduan internet lemah', 'Peraturan anti-curang']
            ];
        }

        // 5. Jadwal & Waktu Seleksi (WIB, WITA, WIT, Maret)
        if (str_contains($q, 'jadwal') || str_contains($q, 'kapan') || str_contains($q, 'maret') || str_contains($q, 'waktu') || str_contains($q, 'jam') || str_contains($q, 'wib') || str_contains($q, 'wita') || str_contains($q, 'wit') || str_contains($q, 'senin') || str_contains($q, 'rabu')) {
            return [
                'text' => "📅 **Jadwal Pelaksanaan Seleksi Online (Mulai Maret 2026):**\n\n- **Masa Seleksi:** Berlangsung selama 13 minggu (3,5 s/d 4 bulan).\n- **Jadwal Pelaksanaan Mingguan:**\n  * **Senin:** Pelaksanaan Ujian Seleksi Online serentak per batch wilayah.\n  * **Rabu:** Pengumuman resmi **9 Sekolah Terbaik (Top 9)** yang lolos ke babak berikutnya.\n  * **2 Minggu Kemudian:** Pelaksanaan babak kompetisi lanjutan.\n- **Zona Waktu:** Sistem otomatis menyesuaikan dengan zona waktu provinsi Anda (**{$province}**). Harap standby di ruang Zoom 30 menit sebelum ujian dimulai.",
                'suggestions' => ['Bagaimana penentuan Top 9?', 'Mulai Ujian Seleksi CBT', 'Berapa durasi tes?']
            ];
        }

        // 6. Penentuan Top 9 & Skor Real-Time
        if (str_contains($q, 'top 9') || str_contains($q, 'skor') || str_contains($q, 'nilai') || str_contains($q, 'lolos') || str_contains($q, 'peringkat') || str_contains($q, 'babak')) {
            return [
                'text' => "🏆 **Kalkulasi Nilai & Penentuan Top 9 Sekolah:**\n\n- **Skor Real-Time:** Begitu tim mengklik tombol *Selesai*, nilai skor akhir sekolah Anda langsung muncul detik itu juga di layar.\n- **Privasi:** Hasil nilai hanya dapat dilihat oleh tim sekolah Anda sendiri (peserta lain tidak dapat melihat skor Anda).\n- **Kriteria Top 9 Lolos:**\n  1. Nilai Skor Tertinggi (0-100)\n  2. Durasi Waktu Pengerjaan Tercepat (*tie-breaker*)\n  3. Waktu Submit Terdahulu\n- Hasil rekap seluruh peserta akan diarsipkan ke spreadsheet resmi panitia MPR RI.",
                'suggestions' => ['Lihat hasil tes sekolah saya', 'Aturan pengerjaan tim', 'Hubungi CS']
            ];
        }

        // 7. Keamanan & Anti-Screenshot
        if (str_contains($q, 'screenshot') || str_contains($q, 'foto') || str_contains($q, 'rekam') || str_contains($q, 'curang') || str_contains($q, 'pindah tab') || str_contains($q, 'blackout')) {
            return [
                'text' => "🛡️ **Sistem Perlindungan Keamanan Layar:**\n\n- **Watermark Forensik:** Seluruh soal dilapisi watermark transparan dinamis berisi: `{$school} • {$province} • IP Publik • Jam Real-Time WIB`. Memotret layar dengan HP luar tetap akan merekam identitas sekolah.\n- **Anti-Pindah Tab:** Dilarang beralih jendela/tab peramban. Layar otomatis ditutup overlay peringatan hitam (*blackout*). Maksimal 3 pelanggaran sebelum form disubmit paksa otomatis.\n- **Pencegahan Cetak:** Klik kanan, Ctrl+P, Ctrl+S, dan F12 otomatis diblokir.",
                'suggestions' => ['Ketentuan perangkat tim', 'Jadwal seleksi', 'Mulai Ujian Seleksi CBT']
            ];
        }

        // 8. Specific province detection (Sumatera, Jawa, Bali, NTT, Papua, etc.)
        $provincesList = ['aceh', 'sumatera', 'riau', 'jambi', 'bengkulu', 'lampung', 'bangka', 'jakarta', 'jawa', 'banten', 'yogyakarta', 'bali', 'nusa tenggara', 'ntb', 'ntt', 'kalimantan', 'sulawesi', 'gorontalo', 'maluku', 'papua'];
        foreach ($provincesList as $provKeyword) {
            if (str_contains($q, $provKeyword)) {
                return [
                    'text' => "📍 **Informasi Khusus Wilayah " . strtoupper($provKeyword) . ":**\n\n- Sekolah dari wilayah **{$province}** mengikuti jadwal seleksi regional dengan pembagian Batch Zoom terkoordinasi (maks 500 sekolah).\n- Jika sekolah Anda berada di area dengan tantangan infrastruktur telekomunikasi, sistem *offline caching* otomatis aktif untuk mengamankan setiap butir jawaban tim.\n- Pastikan PIC ({$pic}) telah bergabung di kanal pengumuman blast WhatsApp panitia MPR RI.",
                    'suggestions' => ['Jadwal seleksi zona waktu', 'Panduan internet lemah', 'Kontak Call Center WA']
                ];
            }
        }

        // Default response
        return [
            'text' => "Halo Tim **{$school}** ({$province})!\n\nKami siap membantu kelancaran persiapan Seleksi Empat Pilar MPR RI. Anda dapat menanyakan tentang:\n- 📅 Jadwal & Waktu Seleksi (Senin Tes, Rabu Rilis Top 9)\n- 📶 Panduan Sinyal Lemah & Local Auto-Save di Daerah 3T\n- 👥 Ketentuan Tim 10 Siswa dalam 1 Device\n- 📹 Ruang Zoom Pengawas (Maks 500 sekolah/batch)\n- 🔄 Prosedur Pengajuan Tes Ulang jika kendala fatal di hari-H\n\nJika butuh bantuan darurat, silakan klik tombol **WhatsApp Hotline CS** di bawah.",
            'suggestions' => [
                'Bagaimana jika internet daerah putus saat tes?',
                'Aturan tim 10 siswa 1 perangkat',
                'Ketentuan ruang Zoom pengawas',
                'Jadwal seleksi dan Top 9'
            ]
        ];
    }
}
