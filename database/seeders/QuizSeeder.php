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
        // ----------------------------------------------------
        // LATIHAN KUIS (PRACTICE QUIZZES)
        // ----------------------------------------------------

        // 1. Kuis Pancasila
        $quizPancasila = Quiz::updateOrCreate(
            [
                'pillar' => 'pancasila',
                'title' => 'Evaluasi Pemahaman Pancasila'
            ],
            [
                'description' => 'Uji pemahaman Anda mengenai sejarah, nilai-nilai butir, dan kedudukan Pancasila sebagai dasar negara.',
                'duration_minutes' => 10,
                'type' => 'practice',
            ]
        );

        Question::updateOrCreate(
            [
                'quiz_id' => $quizPancasila->id,
                'question_text' => 'Mengembangkan sikap saling menghormati kebebasan menjalankan ibadah sesuai dengan agama dan kepercayaannya masing-masing merupakan pencerminan dari nilai Pancasila, khususnya...'
            ],
            [
                'option_a' => 'Sila ke-1',
                'option_b' => 'Sila ke-2',
                'option_c' => 'Sila ke-3',
                'option_d' => 'Sila ke-4',
                'option_e' => 'Sila ke-5',
                'correct_option' => 'a',
                'explanation' => 'Menghormati kebebasan menjalankan ibadah adalah butir pengamalan dari Sila Kesatu (Ketuhanan Yang Maha Esa) yang menekankan aspek ketuhanan dan toleransi antarumat beragama.',
            ]
        );

        Question::updateOrCreate(
            [
                'quiz_id' => $quizPancasila->id,
                'question_text' => 'Pancasila dikatakan sebagai ideologi terbuka karena memiliki kemampuan untuk...'
            ],
            [
                'option_a' => 'Menerima kebudayaan asing secara mutlak tanpa seleksi',
                'option_b' => 'Menyesuaikan diri dengan perkembangan zaman tanpa mengubah nilai dasarnya',
                'option_c' => 'Mengubah nilai-nilai dasarnya setiap terjadi pergantian kepemimpinan',
                'option_d' => 'Mengganti bentuk negara kesatuan menjadi serikat',
                'option_e' => 'Membatasi kebebasan berpendapat warga negara',
                'correct_option' => 'b',
                'explanation' => 'Ideologi terbuka dicirikan dengan kemampuannya menyesuaikan diri dengan dinamika perkembangan zaman secara kreatif tanpa kehilangan esensi nilai dasarnya.',
            ]
        );

        Question::updateOrCreate(
            [
                'quiz_id' => $quizPancasila->id,
                'question_text' => 'Pengakuan terhadap persamaan derajat, hak, dan kewajiban asasi setiap manusia tanpa membeda-bedakan suku, agama, dan keturunan adalah cerminan dari...'
            ],
            [
                'option_a' => 'Sila ke-1',
                'option_b' => 'Sila ke-2',
                'option_c' => 'Sila ke-3',
                'option_d' => 'Sila ke-4',
                'option_e' => 'Sila ke-5',
                'correct_option' => 'b',
                'explanation' => 'Sila Kedua (Kemanusiaan yang Adil dan Beradab) mengajarkan pengakuan persamaan derajat manusia dan perlindungan hak asasi manusia.',
            ]
        );

        // 2. Kuis UUD 1945
        $quizUUD = Quiz::updateOrCreate(
            [
                'pillar' => 'uud_1945',
                'title' => 'Evaluasi Konstitusi UUD NRI 1945'
            ],
            [
                'description' => 'Uji wawasan Anda mengenai struktur ketatanegaraan, pasal-pasal penting, dan amandemen UUD NRI 1945.',
                'duration_minutes' => 15,
                'type' => 'practice',
            ]
        );

        Question::updateOrCreate(
            [
                'quiz_id' => $quizUUD->id,
                'question_text' => 'Berdasarkan Pasal 1 Ayat 3 UUD NRI 1945, Negara Indonesia adalah negara...'
            ],
            [
                'option_a' => 'Kekuasaan',
                'option_b' => 'Hukum (Rechtsstaat)',
                'option_c' => 'Monarki',
                'option_d' => 'Otoriter',
                'option_e' => 'Federal',
                'correct_option' => 'b',
                'explanation' => 'Pasal 1 ayat (3) UUD NRI 1945 hasil amandemen ketiga menegaskan secara eksplisit bahwa "Negara Indonesia adalah negara hukum".',
            ]
        );

        Question::updateOrCreate(
            [
                'quiz_id' => $quizUUD->id,
                'question_text' => 'Lembaga negara yang berwenang menguji undang-undang terhadap Undang-Undang Dasar (Judicial Review) adalah...'
            ],
            [
                'option_a' => 'Mahkamah Agung (MA)',
                'option_b' => 'Mahkamah Konstitusi (MK)',
                'option_c' => 'Komisi Yudisial (KY)',
                'option_d' => 'Dewan Perwakilan Rakyat (DPR)',
                'option_e' => 'Majelis Permusyawaratan Rakyat (MPR)',
                'correct_option' => 'b',
                'explanation' => 'Kewenangan menguji UU terhadap UUD (judicial review) berada pada Mahkamah Konstitusi (MK) sesuai Pasal 24C Ayat 1 UUD 1945.',
            ]
        );

        Question::updateOrCreate(
            [
                'quiz_id' => $quizUUD->id,
                'question_text' => 'Hak setiap warga negara untuk mendapatkan pendidikan dijamin secara konstitusional dalam UUD NRI 1945...'
            ],
            [
                'option_a' => 'Pasal 27 ayat 1',
                'option_b' => 'Pasal 28E ayat 3',
                'option_c' => 'Pasal 30 ayat 1',
                'option_d' => 'Pasal 31 ayat 1',
                'option_e' => 'Pasal 33 ayat 1',
                'correct_option' => 'd',
                'explanation' => 'Pasal 31 ayat (1) UUD NRI 1945 menyatakan bahwa "Setiap warga negara berhak mendapat pendidikan".',
            ]
        );

        // 3. Kuis NKRI
        $quizNKRI = Quiz::updateOrCreate(
            [
                'pillar' => 'nkri',
                'title' => 'Evaluasi NKRI dan Wawasan Nusantara'
            ],
            [
                'description' => 'Evaluasi pengetahuan mengenai keutuhan wilayah kesatuan, bela negara, dan geopolitik Indonesia.',
                'duration_minutes' => 15,
                'type' => 'practice',
            ]
        );

        Question::updateOrCreate(
            [
                'quiz_id' => $quizNKRI->id,
                'question_text' => 'Deklarasi Djuanda yang dicetuskan pada tanggal 13 Desember 1957 mempunyai arti yang sangat penting bagi kedaulatan NKRI, yaitu...'
            ],
            [
                'option_a' => 'Menetapkan batas laut wilayah Indonesia dari 3 mil menjadi 12 mil dari garis dasar',
                'option_b' => 'Menyerahkan kepulauan luar kepada pengawasan PBB',
                'option_c' => 'Membentuk persekutuan militer dengan negara-negara tetangga',
                'option_d' => 'Membagi wilayah Indonesia menjadi beberapa bagian otonom',
                'option_e' => 'Menjadikan laut pedalaman bebas dilalui kapal asing tanpa izin',
                'correct_option' => 'a',
                'explanation' => 'Deklarasi Djuanda 13 Desember 1957 menyatakan bahwa laut teritorial Indonesia ditarik selebar 12 mil laut dari garis pangkal pulau-pulau terluar, menyatukan seluruh kepulauan Indonesia.',
            ]
        );

        Question::updateOrCreate(
            [
                'quiz_id' => $quizNKRI->id,
                'question_text' => 'Hak dan kewajiban setiap warga negara untuk ikut serta dalam usaha pertahanan dan keamanan negara diatur dalam UUD NRI 1945...'
            ],
            [
                'option_a' => 'Pasal 27 ayat 3',
                'option_b' => 'Pasal 30 ayat 1',
                'option_c' => 'Pasal 28A',
                'option_d' => 'Pasal 34 ayat 1',
                'option_e' => 'Pasal 36A',
                'correct_option' => 'b',
                'explanation' => 'Pasal 30 ayat (1) UUD NRI 1945 menyatakan: "Tiap-tiap warga negara berhak dan wajib ikut serta dalam usaha pertahanan dan keamanan negara". Sedangkan Pasal 27 ayat 3 mengatur upaya pembelaan negara.',
            ]
        );

        // 4. Kuis Bhinneka Tunggal Ika
        $quizBhinneka = Quiz::updateOrCreate(
            [
                'pillar' => 'bhinneka_tunggal_ika',
                'title' => 'Evaluasi Bhinneka Tunggal Ika & Integrasi'
            ],
            [
                'description' => 'Evaluasi konsep keberagaman, toleransi multikultural, dan pemersatu bangsa.',
                'duration_minutes' => 10,
                'type' => 'practice',
            ]
        );

        Question::updateOrCreate(
            [
                'quiz_id' => $quizBhinneka->id,
                'question_text' => 'Semboyan "Bhinneka Tunggal Ika" pertama kali ditemukan dalam kitab Kakawin Sutasoma yang dikarang oleh...'
            ],
            [
                'option_a' => 'Mpu Sedah',
                'option_b' => 'Mpu Panuluh',
                'option_c' => 'Mpu Prapanca',
                'option_d' => 'Mpu Tantular',
                'option_e' => 'Mpu Gandring',
                'correct_option' => 'd',
                'explanation' => 'Semboyan Bhinneka Tunggal Ika termaktub dalam kitab Sutasoma karya Mpu Tantular pada zaman kejayaan Majapahit abad ke-14.',
            ]
        );

        Question::updateOrCreate(
            [
                'quiz_id' => $quizBhinneka->id,
                'question_text' => 'Sikap memandang kebudayaan suku bangsanya sendiri lebih tinggi dan meremehkan kebudayaan suku bangsa lain disebut...'
            ],
            [
                'option_a' => 'Nasionalisme',
                'option_b' => 'Patriotisme',
                'option_c' => 'Etnosentrisme',
                'option_d' => 'Pluralisme',
                'option_e' => 'Chauvinisme',
                'correct_option' => 'c',
                'explanation' => 'Etnosentrisme adalah sikap atau kecenderungan mengukur budaya kelompok lain berdasarkan norma budaya sukunya sendiri dan menganggap budayanya paling superior.',
            ]
        );

        // 5. Try Out Simulasi TWK Kedinasan / CPNS (Latihan)
        $quizTWK = Quiz::updateOrCreate(
            [
                'pillar' => 'twk_kedinasan',
                'title' => 'Simulasi Try Out TWK Kedinasan & Ujian Sekolah PPKn'
            ],
            [
                'description' => 'Paket Try Out Tes Wawasan Kebangsaan (TWK) standar SKD Masuk Sekolah Kedinasan (STAN, IPDN, STIS, Akpol/Akmil) & Ujian Akhir SMA. Passing Grade: 65.',
                'duration_minutes' => 20,
                'type' => 'practice',
            ]
        );

        Question::updateOrCreate(
            [
                'quiz_id' => $quizTWK->id,
                'question_text' => 'Dalam sidang BPUPKI tanggal 1 Juni 1945, Ir. Soekarno mengusulkan dasar negara Indonesia. Apabila Pancasila diperas menjadi Trisila, maka unsurnya adalah...'
            ],
            [
                'option_a' => 'Ketuhanan, Kemanusiaan, Keadilan',
                'option_b' => 'Sosio-nasionalisme, Sosio-demokrasi, dan Ketuhanan yang berkebudayaan',
                'option_c' => 'Nasionalisme, Patriotisme, Demokrasi',
                'option_d' => 'Musyawarah, Kedaulatan, Kemakmuran',
                'option_e' => 'Gotong royong, Keadilan, Persatuan',
                'correct_option' => 'b',
                'explanation' => 'Berdasarkan pidato Ir. Soekarno 1 Juni 1945, Pancasila dapat diperas menjadi Trisila (Sosio-nasionalisme, Sosio-demokrasi, Ketuhanan), dan Trisila dapat diperas lagi menjadi Ekasila yaitu Gotong Royong.',
            ]
        );

        Question::updateOrCreate(
            [
                'quiz_id' => $quizTWK->id,
                'question_text' => 'Seorang ASN atau prajurit menolak gratifikasi dan tidak menyalahgunakan wewenang jabatannya demi kepentingan pribadi atau keluarga. Tindakan ini mencerminkan pilar integritas nasional yang dijiwai oleh...'
            ],
            [
                'option_a' => 'Sila ke-1 dan ke-2',
                'option_b' => 'Sila ke-2 dan ke-5',
                'option_c' => 'Sila ke-1, ke-2, dan ke-5',
                'option_d' => 'Sila ke-3 saja',
                'option_e' => 'Sila ke-4 saja',
                'correct_option' => 'c',
                'explanation' => 'Integritas menolak gratifikasi berakar dari moral ketuhanan (Sila 1), kemanusiaan yang beradab (Sila 2), dan keadilan sosial tanpa korupsi hak masyarakat (Sila 5).',
            ]
        );

        Question::updateOrCreate(
            [
                'quiz_id' => $quizTWK->id,
                'question_text' => 'Perjanjian Renville (1948) menimbulkan kerugian teritorial bagi wilayah Indonesia karena berlakunya garis demarkasi buatan Belanda yang dinamakan...'
            ],
            [
                'option_a' => 'Garis Curzon',
                'option_b' => 'Garis Van Mook',
                'option_c' => 'Garis Wallace',
                'option_d' => 'Garis Weber',
                'option_e' => 'Garis Khatulistiwa',
                'correct_option' => 'b',
                'explanation' => 'Garis Van Mook adalah garis demarkasi perbatasan sepihak yang dibuat oleh Hubertus van Mook setelah Agresi Militer Belanda I, yang secara tragis harus diakui RI dalam Perjanjian Renville.',
            ]
        );

        Question::updateOrCreate(
            [
                'quiz_id' => $quizTWK->id,
                'question_text' => 'Sesuai amandemen Pasal 23E UUD NRI 1945, pemeriksaan pengelolaan dan tanggung jawab tentang keuangan negara dilakukan oleh satu Badan Pemeriksa Keuangan (BPK) yang hasilnya diserahkan kepada...'
            ],
            [
                'option_a' => 'Presiden, DPR, dan MA',
                'option_b' => 'DPR, DPD, dan DPRD',
                'option_c' => 'KPK, Kejaksaan, dan Kepolisian',
                'option_d' => 'MPR dan Presiden saja',
                'option_e' => 'Mahkamah Konstitusi',
                'correct_option' => 'b',
                'explanation' => 'Pasal 23E ayat (2) UUD NRI 1945 menegaskan bahwa hasil pemeriksaan keuangan negara diserahkan kepada DPR, DPD, dan DPRD sesuai dengan kewenangannya.',
            ]
        );

        // ----------------------------------------------------
        // REAL MATERI / UJIAN RESMI (1X PENGERJAAN)
        // ----------------------------------------------------

        // 1. Real Pancasila
        $realPancasila = Quiz::updateOrCreate(
            [
                'pillar' => 'pancasila',
                'title' => 'Evaluasi Real Pancasila'
            ],
            [
                'description' => 'Uji pemahaman nyata Anda mengenai Pancasila. Kuis ini hanya dapat diikuti 1 kali dan nilainya dicatat pada leaderboard.',
                'duration_minutes' => 10,
                'type' => 'real',
            ]
        );

        Question::updateOrCreate(
            [
                'quiz_id' => $realPancasila->id,
                'question_text' => 'Rumusan Pancasila yang sah dan resmi sebagai dasar negara tercantum di dalam...'
            ],
            [
                'option_a' => 'Piagam Jakarta (Jakarta Charter)',
                'option_b' => 'Pembukaan UUD 1945 alinea ke-4',
                'option_c' => 'Batang Tubuh UUD 1945',
                'option_d' => 'Dekrit Presiden 5 Juli 1959',
                'option_e' => 'Ketetapan MPRS No. XX/MPRS/1966',
                'correct_option' => 'b',
                'explanation' => 'Rumusan Pancasila yang sah, final, dan berkekuatan hukum tetap tercantum dalam Pembukaan UUD NRI 1945 alinea keempat yang disahkan oleh PPKI pada 18 Agustus 1945.',
            ]
        );

        Question::updateOrCreate(
            [
                'quiz_id' => $realPancasila->id,
                'question_text' => 'Nilai praksis Pancasila merupakan perwujudan nilai instrumental dalam kehidupan sehari-hari. Contoh penerapan nilai praksis sila ke-3 adalah...'
            ],
            [
                'option_a' => 'Rela berkorban untuk kepentingan bangsa dan negara',
                'option_b' => 'Menghormati hak orang lain dalam beribadah',
                'option_c' => 'Tidak memaksakan kehendak dalam rapat',
                'option_d' => 'Menghargai hasil karya orang lain',
                'option_e' => 'Menjunjung tinggi kesetaraan gender',
                'correct_option' => 'a',
                'explanation' => 'Rela berkorban demi kehormatan dan keutuhan bangsa adalah salah satu butir pengamalan praksis Sila Ketiga (Persatuan Indonesia).',
            ]
        );

        // 2. Real UUD 1945
        $realUUD = Quiz::updateOrCreate(
            [
                'pillar' => 'uud_1945',
                'title' => 'Evaluasi Real UUD NRI 1945'
            ],
            [
                'description' => 'Uji pemahaman nyata Anda mengenai Konstitusi UUD NRI 1945. Kuis ini hanya dapat diikuti 1 kali.',
                'duration_minutes' => 10,
                'type' => 'real',
            ]
        );

        Question::updateOrCreate(
            [
                'quiz_id' => $realUUD->id,
                'question_text' => 'Berdasarkan Pasal 1 ayat (2) UUD NRI 1945 setelah amandemen, kedaulatan berada di tangan rakyat dan dilaksanakan menurut...'
            ],
            [
                'option_a' => 'Ketetapan MPR',
                'option_b' => 'Undang-Undang Dasar',
                'option_c' => 'Keputusan Presiden',
                'option_d' => 'Peraturan Pemerintah',
                'option_e' => 'Kehendak Dewan Perwakilan Rakyat',
                'correct_option' => 'b',
                'explanation' => 'Setelah amandemen, bunyi Pasal 1 ayat (2) adalah "Kedaulatan berada di tangan rakyat dan dilaksanakan menurut Undang-Undang Dasar".',
            ]
        );

        // 3. Real NKRI
        $realNKRI = Quiz::updateOrCreate(
            [
                'pillar' => 'nkri',
                'title' => 'Evaluasi Real NKRI'
            ],
            [
                'description' => 'Uji pemahaman nyata Anda mengenai NKRI dan keutuhan wilayah. Kuis ini hanya dapat diikuti 1 kali.',
                'duration_minutes' => 10,
                'type' => 'real',
            ]
        );

        Question::updateOrCreate(
            [
                'quiz_id' => $realNKRI->id,
                'question_text' => 'Bentuk negara Indonesia adalah Kesatuan, sedangkan bentuk pemerintahannya adalah Republik. Hal ini ditegaskan dalam UUD 1945...'
            ],
            [
                'option_a' => 'Pasal 1 ayat 1',
                'option_b' => 'Pasal 1 ayat 2',
                'option_c' => 'Pasal 1 ayat 3',
                'option_d' => 'Pasal 2 ayat 1',
                'option_e' => 'Pasal 3 ayat 1',
                'correct_option' => 'a',
                'explanation' => 'Pasal 1 ayat (1) UUD 1945 menyatakan bahwa Negara Indonesia ialah Negara Kesatuan, yang berbentuk Republik.',
            ]
        );

        // 4. Real Bhinneka Tunggal Ika
        $realBhinneka = Quiz::updateOrCreate(
            [
                'pillar' => 'bhinneka_tunggal_ika',
                'title' => 'Evaluasi Real Bhinneka Tunggal Ika'
            ],
            [
                'description' => 'Uji pemahaman nyata Anda mengenai Bhinneka Tunggal Ika. Kuis ini hanya dapat diikuti 1 kali.',
                'duration_minutes' => 10,
                'type' => 'real',
            ]
        );

        Question::updateOrCreate(
            [
                'quiz_id' => $realBhinneka->id,
                'question_text' => 'Semboyan Bhinneka Tunggal Ika bagi bangsa Indonesia memiliki makna...'
            ],
            [
                'option_a' => 'Keberagaman harus dilebur menjadi satu kebudayaan yang sama',
                'option_b' => 'Walaupun berbeda-beda tetapi pada hakikatnya bangsa Indonesia tetap satu kesatuan',
                'option_c' => 'Saling menonjolkan kelebihan suku masing-masing',
                'option_d' => 'Membagi wilayah Indonesia berdasarkan ras dan agama',
                'option_e' => 'Menyatukan seluruh negara di Asia Tenggara',
                'correct_option' => 'b',
                'explanation' => 'Bhinneka Tunggal Ika berarti berbeda-beda tetapi tetap satu jua, melambangkan kesatuan dalam kemajemukan bangsa Indonesia.',
            ]
        );

        // 5. Real TWK Kedinasan (Ujian Resmi 1x)
        $realTWK = Quiz::updateOrCreate(
            [
                'pillar' => 'twk_kedinasan',
                'title' => 'Simulasi Real Ujian TWK Masuk Kedinasan & Ujian Sekolah'
            ],
            [
                'description' => 'Ujian simulasi resmi Tes Wawasan Kebangsaan (TWK) berskala riil dengan Passing Grade 65. Hanya dapat dikerjakan 1 kali.',
                'duration_minutes' => 20,
                'type' => 'real',
            ]
        );

        Question::updateOrCreate(
            [
                'quiz_id' => $realTWK->id,
                'question_text' => 'Organisasi pergerakan nasional pertama yang bersifat modern dan didirikan pada 20 Mei 1908 oleh para mahasiswa STOVIA adalah...'
            ],
            [
                'option_a' => 'Sarekat Islam',
                'option_b' => 'Budi Utomo',
                'option_c' => 'Indische Partij',
                'option_d' => 'Perhimpunan Indonesia',
                'option_e' => 'Partai Nasional Indonesia',
                'correct_option' => 'b',
                'explanation' => 'Budi Utomo didirikan pada 20 Mei 1908 oleh dr. Soetomo dan mahasiswa STOVIA atas dorongan dr. Wahidin Soedirohoesodo, yang menjadi tonggak Hari Kebangkitan Nasional.',
            ]
        );

        Question::updateOrCreate(
            [
                'quiz_id' => $realTWK->id,
                'question_text' => 'Pasal dalam UUD NRI 1945 yang secara tegas melarang bentuk negara Indonesia diubah dalam mekanisme amandemen adalah...'
            ],
            [
                'option_a' => 'Pasal 37 ayat (1)',
                'option_b' => 'Pasal 37 ayat (3)',
                'option_c' => 'Pasal 37 ayat (5)',
                'option_d' => 'Pasal 1 ayat (1)',
                'option_e' => 'Aturan Peralihan Pasal I',
                'correct_option' => 'c',
                'explanation' => 'Pasal 37 ayat (5) UUD NRI 1945 hasil amandemen menegaskan: "Khusus mengenai bentuk Negara Kesatuan Republik Indonesia tidak dapat dilakukan perubahan".',
            ]
        );

        Question::updateOrCreate(
            [
                'quiz_id' => $realTWK->id,
                'question_text' => 'Sikap cinta tanah air yang berlebihan hingga memandang bangsa lain rendah dan bermusuhan disebut...'
            ],
            [
                'option_a' => 'Patriotisme',
                'option_b' => 'Chauvinisme',
                'option_c' => 'Nasionalisme',
                'option_d' => 'Etnosentrisme',
                'option_e' => 'Kosmopolitanisme',
                'correct_option' => 'b',
                'explanation' => 'Chauvinisme adalah rasa cinta tanah air yang sempit dan berlebihan (ekstrem) hingga merendahkan bangsa-bangsa lain.',
            ]
        );
    }
}
