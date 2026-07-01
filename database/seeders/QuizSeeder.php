<?php

namespace Database\Seeders;

use App\Models\Quiz;
use App\Models\Question;
use Illuminate\Database\Seeder;

class QuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Kuis Pancasila
        $quizPancasila = Quiz::create([
            'pillar' => 'pancasila',
            'title' => 'Evaluasi Pemahaman Pancasila',
            'description' => 'Uji pemahaman Anda mengenai sejarah, nilai-nilai butir, dan kedudukan Pancasila sebagai dasar negara.',
            'duration_minutes' => 10,
        ]);

        Question::create([
            'quiz_id' => $quizPancasila->id,
            'question_text' => 'Mengembangkan sikap saling menghormati kebebasan menjalankan ibadah sesuai dengan agama dan kepercayaannya masing-masing merupakan pencerminan dari nilai Pancasila, khususnya...',
            'option_a' => 'Sila ke-1',
            'option_b' => 'Sila ke-2',
            'option_c' => 'Sila ke-3',
            'option_d' => 'Sila ke-4',
            'option_e' => 'Sila ke-5',
            'correct_option' => 'a',
            'explanation' => 'Menghormati kebebasan menjalankan ibadah adalah butir pengamalan dari Sila Kesatu (Ketuhanan Yang Maha Esa) yang menekankan aspek ketuhanan dan toleransi beragama.',
        ]);

        Question::create([
            'quiz_id' => $quizPancasila->id,
            'question_text' => 'Pancasila dikatakan sebagai ideologi terbuka karena memiliki kemampuan untuk...',
            'option_a' => 'Menerima kebudayaan asing secara mutlak tanpa seleksi',
            'option_b' => 'Menyesuaikan diri dengan perkembangan zaman tanpa mengubah nilai dasarnya',
            'option_c' => 'Mengubah nilai-nilai dasarnya setiap terjadi pergantian kepemimpinan',
            'option_d' => 'Mengganti bentuk negara kesatuan menjadi serikat',
            'option_e' => 'Membatasi kebebasan berpendapat warga negara',
            'correct_option' => 'b',
            'explanation' => 'Ideologi terbuka dicirikan dengan kemampuannya menyesuaikan diri dengan dinamika perkembangan zaman secara kreatif tanpa kehilangan esensi nilai dasarnya.',
        ]);

        Question::create([
            'quiz_id' => $quizPancasila->id,
            'question_text' => 'Pengakuan terhadap persamaan derajat, hak, dan kewajiban asasi setiap manusia tanpa membeda-bedakan suku, agama, dan keturunan adalah cerminan dari...',
            'option_a' => 'Sila ke-1',
            'option_b' => 'Sila ke-2',
            'option_c' => 'Sila ke-3',
            'option_d' => 'Sila ke-4',
            'option_e' => 'Sila ke-5',
            'correct_option' => 'b',
            'explanation' => 'Persamaan derajat kemanusiaan dan perlindungan hak asasi manusia diatur di dalam Sila Kedua (Kemanusiaan yang Adil dan Beradab).',
        ]);

        // 2. Kuis UUD NRI 1945
        $quizUUD = Quiz::create([
            'pillar' => 'uud_1945',
            'title' => 'Evaluasi Konstitusi UUD NRI 1945',
            'description' => 'Uji pemahaman Anda tentang UUD NRI 1945 sebagai hukum dasar tertulis tertinggi di Indonesia.',
            'duration_minutes' => 10,
        ]);

        Question::create([
            'quiz_id' => $quizUUD->id,
            'question_text' => 'Bagaimanakah kedudukan UUD NRI 1945 dalam sistem peraturan perundang-undangan di Indonesia?',
            'option_a' => 'Sebagai pelengkap undang-undang organik saja',
            'option_b' => 'Sebagai hukum dasar tertulis tertinggi yang menjadi acuan produk hukum di bawahnya',
            'option_c' => 'Memiliki kedudukan yang sejajar dengan ketetapan menteri',
            'option_d' => 'Hanya berlaku mengikat bagi penyelenggara negara saja',
            'option_e' => 'Dapat dianulir oleh peraturan daerah provinsi',
            'correct_option' => 'b',
            'explanation' => 'UUD NRI 1945 berkedudukan sebagai hukum dasar tertulis tertinggi (supreme law), sehingga peraturan perundang-undangan di bawahnya tidak boleh bertentangan dengannya.',
        ]);

        Question::create([
            'quiz_id' => $quizUUD->id,
            'question_text' => 'Berapa kalikah UUD NRI 1945 mengalami perubahan (amandemen) pada periode reformasi 1999-2002?',
            'option_a' => '1 kali',
            'option_b' => '2 kali',
            'option_c' => '3 kali',
            'option_d' => '4 kali',
            'option_e' => '5 kali',
            'correct_option' => 'd',
            'explanation' => 'UUD NRI 1945 telah mengalami empat kali amandemen oleh MPR pada Sidang Umum/Tahunan MPR dari tahun 1999, 2000, 2001, dan 2002.',
        ]);

        Question::create([
            'quiz_id' => $quizUUD->id,
            'question_text' => 'Aturan mengenai mekanisme perubahan atau amandemen UUD NRI 1945 tercantum secara khusus pada...',
            'option_a' => 'Pasal 29 UUD NRI 1945',
            'option_b' => 'Pasal 30 UUD NRI 1945',
            'option_c' => 'Pasal 33 UUD NRI 1945',
            'option_d' => 'Pasal 36 UUD NRI 1945',
            'option_e' => 'Pasal 37 UUD NRI 1945',
            'correct_option' => 'e',
            'explanation' => 'Pasal 37 UUD NRI 1945 mengatur tata cara pengusulan dan pelaksanaan perubahan pasal-pasal undang-undang dasar oleh MPR.',
        ]);

        // 3. Kuis NKRI
        $quizNKRI = Quiz::create([
            'pillar' => 'nkri',
            'title' => 'Evaluasi NKRI dan Wawasan Nusantara',
            'description' => 'Uji pengetahuan Anda tentang konsepsi keutuhan wilayah NKRI, otonomi daerah, dan peran bela negara.',
            'duration_minutes' => 10,
        ]);

        Question::create([
            'quiz_id' => $quizNKRI->id,
            'question_text' => 'Deklarasi Djuanda tanggal 13 Desember 1957 merupakan tonggak penting kedaulatan NKRI karena...',
            'option_a' => 'Membubarkan Republik Indonesia Serikat dan kembali ke NKRI',
            'option_b' => 'Menetapkan batas laut teritorial menjadi 12 mil laut dihitung dari garis pangkal pulau terluar',
            'option_c' => 'Menyatakan kemerdekaan Indonesia dari kekuasaan sekutu',
            'option_d' => 'Meresmikan Pancasila sebagai ideologi bangsa',
            'option_e' => 'Menetapkan sistem ekonomi terpimpin bagi Indonesia',
            'correct_option' => 'b',
            'explanation' => 'Deklarasi Djuanda menegaskan konsep negara kepulauan (Archipelagic State) yang menyatukan wilayah darat dan laut dalam NKRI dengan batas laut teritorial 12 mil laut.',
        ]);

        Question::create([
            'quiz_id' => $quizNKRI->id,
            'question_text' => 'Bagi seorang pelajar SMA/K, keikutsertaan dalam upaya bela negara secara non-fisik dapat diwujudkan melalui...',
            'option_a' => 'Ikut serta dalam latihan kemiliteran sukarela',
            'option_b' => 'Membeli senjata canggih untuk pertahanan sekolah',
            'option_c' => 'Belajar dengan tekun, berprestasi, menjaga nama baik bangsa, dan menolak penyebaran hoaks',
            'option_d' => 'Melakukan demonstrasi anarkis terhadap kebijakan sekolah',
            'option_e' => 'Meninggalkan bangku sekolah untuk menjaga perbatasan',
            'correct_option' => 'c',
            'explanation' => 'Bela negara non-fisik bagi pelajar berupa hal-hal positif seperti giat belajar, meningkatkan ketakwaan, mencetak prestasi, serta menjaga harmoni sosial di tengah masyarakat.',
        ]);

        // 4. Kuis Bhinneka Tunggal Ika
        $quizBhinneka = Quiz::create([
            'pillar' => 'bhinneka_tunggal_ika',
            'title' => 'Evaluasi Bhinneka Tunggal Ika & Integrasi',
            'description' => 'Uji pemahaman Anda tentang semboyan pemersatu bangsa Indonesia di tengah keragaman multikultural.',
            'duration_minutes' => 10,
        ]);

        Question::create([
            'quiz_id' => $quizBhinneka->id,
            'question_text' => 'Kata Bhinneka Tunggal Ika pertama kali ditemukan dalam kitab Kakawin Sutasoma karangan Mpu Tantular pada masa kejayaan kerajaan...',
            'option_a' => 'Sriwijaya',
            'option_b' => 'Kutai',
            'option_c' => 'Tarumanegara',
            'option_d' => 'Majapahit',
            'option_e' => 'Singasari',
            'correct_option' => 'd',
            'explanation' => 'Kitab Kakawin Sutasoma ditulis oleh Mpu Tantular pada masa pemerintahan Raja Hayam Wuruk di Kerajaan Majapahit abad ke-14.',
        ]);

        Question::create([
            'quiz_id' => $quizBhinneka->id,
            'question_text' => 'Paham atau sikap yang menilai kebudayaan suku bangsa lain menggunakan ukuran nilai-nilai kebudayaan suku bangsanya sendiri dan menganggap budayanya paling unggul dinamakan...',
            'option_a' => 'Etnosentrisme',
            'option_b' => 'Chauvinisme',
            'option_c' => 'Intoleransi',
            'option_d' => 'Primordialisme',
            'option_e' => 'Patriotisme',
            'correct_option' => 'a',
            'explanation' => 'Etnosentrisme adalah kecenderungan melihat dunia hanya melalui sudut pandang budaya sendiri serta menilai budaya lain lebih rendah dibanding budayanya sendiri.',
        ]);
    }
}
