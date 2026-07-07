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
        // Clean up duplicate quizzes by title, keeping the first one
        $titles = [
            'Evaluasi Pemahaman Pancasila',
            'Evaluasi Konstitusi UUD NRI 1945',
            'Evaluasi NKRI dan Wawasan Nusantara',
            'Evaluasi Bhinneka Tunggal Ika & Integrasi'
        ];

        foreach ($titles as $title) {
            $quizzes = Quiz::query()->where('title', $title)->get();
            if ($quizzes->count() > 1) {
                $keepId = $quizzes->first()->id;
                Quiz::query()->where('title', $title)->where('id', '!=', $keepId)->delete();
            }
        }

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

        // Clean up duplicate questions for Pancasila Quiz
        $pancasilaQuestionsText = [
            'Mengembangkan sikap saling menghormati kebebasan menjalankan ibadah sesuai dengan agama dan kepercayaannya masing-masing merupakan pencerminan dari nilai Pancasila, khususnya...',
            'Pancasila dikatakan sebagai ideologi terbuka karena memiliki kemampuan untuk...',
            'Pengakuan terhadap persamaan derajat, hak, dan kewajiban asasi setiap manusia tanpa membeda-bedakan suku, agama, dan keturunan adalah cerminan dari...'
        ];
        foreach ($pancasilaQuestionsText as $qText) {
            $qs = Question::query()->where('quiz_id', $quizPancasila->id)->where('question_text', $qText)->get();
            if ($qs->count() > 1) {
                $keepId = $qs->first()->id;
                Question::query()->where('quiz_id', $quizPancasila->id)->where('question_text', $qText)->where('id', '!=', $keepId)->delete();
            }
        }

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
                'explanation' => 'Menghormati kebebasan menjalankan ibadah adalah butir pengamalan dari Sila Kesatu (Ketuhanan Yang Maha Esa) yang menekankan aspek ketuhanan and toleransi beragama.',
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
                'explanation' => 'Persamaan derajat kemanusiaan dan perlindungan hak asasi manusia diatur di dalam Sila Kedua (Kemanusiaan yang Adil dan Beradab).',
            ]
        );

        // 2. Kuis UUD NRI 1945
        $quizUUD = Quiz::updateOrCreate(
            [
                'pillar' => 'uud_1945',
                'title' => 'Evaluasi Konstitusi UUD NRI 1945'
            ],
            [
                'description' => 'Uji pemahaman Anda tentang UUD NRI 1945 sebagai hukum dasar tertulis tertinggi di Indonesia.',
                'duration_minutes' => 10,
                'type' => 'practice',
            ]
        );

        // Clean up duplicate questions for UUD Quiz
        $uudQuestionsText = [
            'Bagaimanakah kedudukan UUD NRI 1945 dalam sistem peraturan perundang-undangan di Indonesia?',
            'Berapa kalikah UUD NRI 1945 mengalami perubahan (amandemen) pada periode reformasi 1999-2002?',
            'Aturan mengenai mekanisme perubahan atau amandemen UUD NRI 1945 tercantum secara khusus pada...'
        ];
        foreach ($uudQuestionsText as $qText) {
            $qs = Question::query()->where('quiz_id', $quizUUD->id)->where('question_text', $qText)->get();
            if ($qs->count() > 1) {
                $keepId = $qs->first()->id;
                Question::query()->where('quiz_id', $quizUUD->id)->where('question_text', $qText)->where('id', '!=', $keepId)->delete();
            }
        }

        Question::updateOrCreate(
            [
                'quiz_id' => $quizUUD->id,
                'question_text' => 'Bagaimanakah kedudukan UUD NRI 1945 dalam sistem peraturan perundang-undangan di Indonesia?'
            ],
            [
                'option_a' => 'Sebagai pelengkap undang-undang organik saja',
                'option_b' => 'Sebagai hukum dasar tertulis tertinggi yang menjadi acuan produk hukum di bawahnya',
                'option_c' => 'Memiliki kedudukan yang sejajar dengan ketetapan menteri',
                'option_d' => 'Hanya berlaku mengikat bagi penyelenggara negara saja',
                'option_e' => 'Dapat dianulir oleh peraturan daerah provinsi',
                'correct_option' => 'b',
                'explanation' => 'UUD NRI 1945 berkedudukan sebagai hukum dasar tertulis tertinggi (supreme law), sehingga peraturan perundang-undangan di bawahnya tidak boleh bertentangan dengannya.',
            ]
        );

        Question::updateOrCreate(
            [
                'quiz_id' => $quizUUD->id,
                'question_text' => 'Berapa kalikah UUD NRI 1945 mengalami perubahan (amandemen) pada periode reformasi 1999-2002?'
            ],
            [
                'option_a' => '1 kali',
                'option_b' => '2 kali',
                'option_c' => '3 kali',
                'option_d' => '4 kali',
                'option_e' => '5 kali',
                'correct_option' => 'd',
                'explanation' => 'UUD NRI 1945 telah mengalami empat kali amandemen oleh MPR pada Sidang Umum/Tahunan MPR dari tahun 1999, 2000, 2001, dan 2002.',
            ]
        );

        Question::updateOrCreate(
            [
                'quiz_id' => $quizUUD->id,
                'question_text' => 'Aturan mengenai mekanisme perubahan atau amandemen UUD NRI 1945 tercantum secara khusus pada...'
            ],
            [
                'option_a' => 'Pasal 29 UUD NRI 1945',
                'option_b' => 'Pasal 30 UUD NRI 1945',
                'option_c' => 'Pasal 33 UUD NRI 1945',
                'option_d' => 'Pasal 36 UUD NRI 1945',
                'option_e' => 'Pasal 37 UUD NRI 1945',
                'correct_option' => 'e',
                'explanation' => 'Pasal 37 UUD NRI 1945 mengatur tata cara pengusulan dan pelaksanaan perubahan pasal-pasal undang-undang dasar oleh MPR.',
            ]
        );

        // 3. Kuis NKRI
        $quizNKRI = Quiz::updateOrCreate(
            [
                'pillar' => 'nkri',
                'title' => 'Evaluasi NKRI dan Wawasan Nusantara'
            ],
            [
                'description' => 'Uji pengetahuan Anda tentang konsepsi keutuhan wilayah NKRI, otonomi daerah, dan peran bela negara.',
                'duration_minutes' => 10,
                'type' => 'practice',
            ]
        );

        // Clean up duplicate questions for NKRI Quiz
        $nkriQuestionsText = [
            'Deklarasi Djuanda tanggal 13 Desember 1957 merupakan tonggak penting kedaulatan NKRI karena...',
            'Bagi seorang pelajar SMA/K, keikutsertaan dalam upaya bela negara secara non-fisik dapat diwujudkan melalui...'
        ];
        foreach ($nkriQuestionsText as $qText) {
            $qs = Question::query()->where('quiz_id', $quizNKRI->id)->where('question_text', $qText)->get();
            if ($qs->count() > 1) {
                $keepId = $qs->first()->id;
                Question::query()->where('quiz_id', $quizNKRI->id)->where('question_text', $qText)->where('id', '!=', $keepId)->delete();
            }
        }

        Question::updateOrCreate(
            [
                'quiz_id' => $quizNKRI->id,
                'question_text' => 'Deklarasi Djuanda tanggal 13 Desember 1957 merupakan tonggak penting kedaulatan NKRI karena...'
            ],
            [
                'option_a' => 'Membubarkan Republik Indonesia Serikat dan kembali ke NKRI',
                'option_b' => 'Menetapkan batas laut teritorial menjadi 12 mil laut dihitung dari garis pangkal pulau terluar',
                'option_c' => 'Menyatakan kemerdekaan Indonesia dari kekuasaan sekutu',
                'option_d' => 'Meresmikan Pancasila sebagai ideologi bangsa',
                'option_e' => 'Menetapkan sistem ekonomi terpimpin bagi Indonesia',
                'correct_option' => 'b',
                'explanation' => 'Deklarasi Djuanda menegaskan konsep negara kepulauan (Archipelagic State) yang menyatukan wilayah darat dan laut dalam NKRI dengan batas laut teritorial 12 mil laut.',
            ]
        );

        Question::updateOrCreate(
            [
                'quiz_id' => $quizNKRI->id,
                'question_text' => 'Bagi seorang pelajar SMA/K, keikutsertaan dalam upaya bela negara secara non-fisik dapat diwujudkan melalui...'
            ],
            [
                'option_a' => 'Ikut serta dalam latihan kemiliteran sukarela',
                'option_b' => 'Membeli senjata canggih untuk pertahanan sekolah',
                'option_c' => 'Belajar dengan tekun, berprestasi, menjaga nama baik bangsa, dan menolak penyebaran hoaks',
                'option_d' => 'Melakukan demonstrasi anarkis terhadap kebijakan sekolah',
                'option_e' => 'Meninggalkan bangku sekolah untuk menjaga perbatasan',
                'correct_option' => 'c',
                'explanation' => 'Bela negara non-fisik bagi pelajar berupa hal-hal positif seperti giat belajar, meningkatkan ketakwaan, mencetak prestasi, serta menjaga harmoni sosial di tengah masyarakat.',
            ]
        );

        // 4. Kuis Bhinneka Tunggal Ika
        $quizBhinneka = Quiz::updateOrCreate(
            [
                'pillar' => 'bhinneka_tunggal_ika',
                'title' => 'Evaluasi Bhinneka Tunggal Ika & Integrasi'
            ],
            [
                'description' => 'Uji pemahaman Anda tentang semboyan pemersatu bangsa Indonesia di tengah keragaman multikultural.',
                'duration_minutes' => 10,
                'type' => 'practice',
            ]
        );

        // Clean up duplicate questions for Bhinneka Quiz
        $bhinnekaQuestionsText = [
            'Kata Bhinneka Tunggal Ika pertama kali ditemukan dalam kitab Kakawin Sutasoma karangan Mpu Tantular pada masa kejayaan kerajaan...',
            'Paham atau sikap yang menilai kebudayaan suku bangsa lain menggunakan ukuran nilai-nilai kebudayaan suku bangsanya sendiri dan menganggap budayanya paling unggul dinamakan...'
        ];
        foreach ($bhinnekaQuestionsText as $qText) {
            $qs = Question::query()->where('quiz_id', $quizBhinneka->id)->where('question_text', $qText)->get();
            if ($qs->count() > 1) {
                $keepId = $qs->first()->id;
                Question::query()->where('quiz_id', $quizBhinneka->id)->where('question_text', $qText)->where('id', '!=', $keepId)->delete();
            }
        }

        Question::updateOrCreate(
            [
                'quiz_id' => $quizBhinneka->id,
                'question_text' => 'Kata Bhinneka Tunggal Ika pertama kali ditemukan dalam kitab Kakawin Sutasoma karangan Mpu Tantular pada masa kejayaan kerajaan...'
            ],
            [
                'option_a' => 'Sriwijaya',
                'option_b' => 'Kutai',
                'option_c' => 'Tarumanegara',
                'option_d' => 'Majapahit',
                'option_e' => 'Singasari',
                'correct_option' => 'd',
                'explanation' => 'Kitab Kakawin Sutasoma ditulis oleh Mpu Tantular pada masa pemerintahan Raja Hayam Wuruk di Kerajaan Majapahit abad ke-14.',
            ]
        );

        Question::updateOrCreate(
            [
                'quiz_id' => $quizBhinneka->id,
                'question_text' => 'Paham atau sikap yang menilai kebudayaan suku bangsa lain menggunakan ukuran nilai-nilai kebudayaan suku bangsanya sendiri dan menganggap budayanya paling unggul dinamakan...'
            ],
            [
                'option_a' => 'Etnosentrisme',
                'option_b' => 'Chauvinisme',
                'option_c' => 'Intoleransi',
                'option_d' => 'Primordialisme',
                'option_e' => 'Patriotisme',
                'correct_option' => 'a',
                'explanation' => 'Etnosentrisme adalah kecenderungan melihat dunia hanya melalui sudut pandang budaya sendiri serta menilai budaya lain lebih rendah dibanding budayanya sendiri.',
            ]
        );

        // Seeding Real Materi Quizzes
        // 1. Real Pancasila
        $realPancasila = Quiz::updateOrCreate(
            [
                'pillar' => 'pancasila',
                'title' => 'Evaluasi Real Pancasila'
            ],
            [
                'description' => 'Uji pemahaman nyata Anda mengenai Pancasila secara keseluruhan. Kuis ini hanya dapat diikuti 1 kali.',
                'duration_minutes' => 10,
                'type' => 'real',
            ]
        );

        Question::updateOrCreate(
            [
                'quiz_id' => $realPancasila->id,
                'question_text' => 'Pancasila memiliki kedudukan yang sangat penting dalam ketatanegaraan Indonesia, yaitu sebagai...'
            ],
            [
                'option_a' => 'Hukum dasar tertulis tertinggi',
                'option_b' => 'Sumber dari segala sumber hukum negara',
                'option_c' => 'Aturan tata tertib kenegaraan',
                'option_d' => 'Konstitusi tertulis daerah',
                'option_e' => 'Lambang kedaulatan pemerintah',
                'correct_option' => 'b',
                'explanation' => 'Pancasila berkedudukan sebagai sumber dari segala sumber hukum negara Indonesia, artinya seluruh peraturan perundang-undangan harus bersumber pada nilai Pancasila.',
            ]
        );

        Question::updateOrCreate(
            [
                'quiz_id' => $realPancasila->id,
                'question_text' => 'Menghargai hasil karya orang lain yang bermanfaat bagi kemajuan dan kesejahteraan bersama adalah pengamalan Pancasila...'
            ],
            [
                'option_a' => 'Sila ke-1',
                'option_b' => 'Sila ke-2',
                'option_c' => 'Sila ke-3',
                'option_d' => 'Sila ke-4',
                'option_e' => 'Sila ke-5',
                'correct_option' => 'e',
                'explanation' => 'Menghargai hasil karya orang lain merupakan salah satu butir pengamalan dari Sila Kelima (Keadilan Sosial bagi Seluruh Rakyat Indonesia).',
            ]
        );

        Question::updateOrCreate(
            [
                'quiz_id' => $realPancasila->id,
                'question_text' => 'Berikut ini yang merupakan perwujudan nilai kemanusiaan yang adil dan beradab adalah...'
            ],
            [
                'option_a' => 'Mengembangkan toleransi antarumat beragama',
                'option_b' => 'Menjunjung tinggi nilai kemanusiaan dan gemar melakukan kegiatan kemanusiaan',
                'option_c' => 'Bangga sebagai bangsa Indonesia yang bertanah air satu',
                'option_d' => 'Mengutamakan musyawarah dalam menyelesaikan masalah',
                'option_e' => 'Bersikap adil dan suka menolong sesama',
                'correct_option' => 'b',
                'explanation' => 'Menjunjung tinggi nilai kemanusiaan dan gemar melakukan kegiatan kemanusiaan adalah bentuk konkret pengamalan dari Sila Kedua.',
            ]
        );

        // 2. Real UUD 1945
        $realUUD = Quiz::updateOrCreate(
            [
                'pillar' => 'uud_1945',
                'title' => 'Evaluasi Real UUD NRI 1945'
            ],
            [
                'description' => 'Uji pemahaman nyata Anda mengenai Undang-Undang Dasar NRI 1945. Kuis ini hanya dapat diikuti 1 kali.',
                'duration_minutes' => 10,
                'type' => 'real',
            ]
        );

        Question::updateOrCreate(
            [
                'quiz_id' => $realUUD->id,
                'question_text' => 'Kekuasaan kehakiman di Indonesia merupakan kekuasaan yang merdeka untuk menyelenggarakan peradilan guna menegakkan hukum dan keadilan, hal ini diatur dalam UUD 1945 pasal...'
            ],
            [
                'option_a' => 'Pasal 24 ayat 1',
                'option_b' => 'Pasal 25',
                'option_c' => 'Pasal 26 ayat 1',
                'option_d' => 'Pasal 27 ayat 2',
                'option_e' => 'Pasal 28',
                'correct_option' => 'a',
                'explanation' => 'Pasal 24 ayat (1) menegaskan bahwa kekuasaan kehakiman merupakan kekuasaan yang merdeka untuk menyelenggarakan peradilan guna menegakkan hukum dan keadilan.',
            ]
        );

        Question::updateOrCreate(
            [
                'quiz_id' => $realUUD->id,
                'question_text' => 'Lembaga negara baru yang dibentuk setelah amandemen UUD NRI 1945 yang berwenang mengadili pada tingkat pertama dan terakhir yang putusannya bersifat final untuk menguji undang-undang terhadap Undang-Undang Dasar adalah...'
            ],
            [
                'option_a' => 'Mahkamah Agung',
                'option_b' => 'Komisi Yudisial',
                'option_c' => 'Mahkamah Konstitusi',
                'option_d' => 'Dewan Perwakilan Daerah',
                'option_e' => 'Badan Pemeriksa Keuangan',
                'correct_option' => 'c',
                'explanation' => 'Mahkamah Konstitusi berwenang mengadili pada tingkat pertama dan terakhir yang putusannya bersifat final untuk menguji undang-undang terhadap UUD.',
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

        Question::updateOrCreate(
            [
                'quiz_id' => $realNKRI->id,
                'question_text' => 'Wilayah NKRI dibagi atas daerah-daerah provinsi dan daerah provinsi itu dibagi atas kabupaten dan kota, yang tiap-tiap provinsi, kabupaten, dan kota itu mempunyai pemerintahan daerah, yang diatur dengan undang-undang. Hal ini merupakan isi UUD 1945...'
            ],
            [
                'option_a' => 'Pasal 17 UUD 1945',
                'option_b' => 'Pasal 18 ayat 1 UUD 1945',
                'option_c' => 'Pasal 19 UUD 1945',
                'option_d' => 'Pasal 20 UUD 1945',
                'option_e' => 'Pasal 21 UUD 1945',
                'correct_option' => 'b',
                'explanation' => 'Pasal 18 ayat (1) UUD 1945 mengatur pembagian wilayah NKRI menjadi provinsi, kabupaten, dan kota beserta pemerintahan daerahnya.',
            ]
        );

        Question::updateOrCreate(
            [
                'quiz_id' => $realNKRI->id,
                'question_text' => 'Salah satu tujuan dibentuknya Pemerintahan Negara Indonesia yang tercantum dalam Pembukaan UUD NRI 1945 alinea keempat adalah...'
            ],
            [
                'option_a' => 'Mewujudkan perdamaian abadi yang menguntungkan blok tertentu',
                'option_b' => 'Mencerdaskan kehidupan bangsa',
                'option_c' => 'Memperbanyak utang luar negeri untuk pembangunan',
                'option_d' => 'Menjajah bangsa lain yang belum merdeka',
                'option_e' => 'Membatasi perdagangan dengan dunia luar',
                'correct_option' => 'b',
                'explanation' => 'Mencerdaskan kehidupan bangsa merupakan salah satu dari empat tujuan negara Indonesia yang termaktub dalam Pembukaan UUD 1945 alinea keempat.',
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

        Question::updateOrCreate(
            [
                'quiz_id' => $realBhinneka->id,
                'question_text' => 'Sikap toleransi dalam keberagaman suku, ras, agama, dan antargolongan dapat diwujudkan dengan cara...'
            ],
            [
                'option_a' => 'Menganggap ajaran agama sendiri yang paling benar dan menjelekkan agama lain',
                'option_b' => 'Menghargai dan menghormati perayaan hari besar keagamaan umat lain',
                'option_c' => 'Hanya mau bergaul dengan orang yang sukunya sama',
                'option_d' => 'Memaksa kehendak pribadi kepada orang lain dalam diskusi',
                'option_e' => 'Membantu orang lain hanya jika seagama',
                'correct_option' => 'b',
                'explanation' => 'Toleransi diwujudkan dengan cara menghargai, menghormati, dan memberikan kebebasan kepada pemeluk agama lain untuk merayakan hari besar keagamaan mereka.',
            ]
        );

        Question::updateOrCreate(
            [
                'quiz_id' => $realBhinneka->id,
                'question_text' => 'Yang bukan merupakan contoh integrasi sosial di tengah kemajemukan masyarakat Indonesia adalah...'
            ],
            [
                'option_a' => 'Gotong royong membersihkan lingkungan tanpa membedakan suku',
                'option_b' => 'Pelaksanaan musyawarah warga untuk mufakat',
                'option_c' => 'Tawuran antarkelompok karena perbedaan etnis',
                'option_d' => 'Saling berkunjung saat hari raya keagamaan',
                'option_e' => 'Pernikahan antarsuku bangsa yang harmonis',
                'correct_option' => 'c',
                'explanation' => 'Tawuran antarkelompok merupakan bentuk konflik sosial dan disintegrasi, bukan integrasi sosial.',
            ]
        );
    }
}
