<?php

namespace Database\Seeders;

use App\Models\Material;
use Illuminate\Database\Seeder;

class PillarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clean up existing duplicates of seeded materials if any, keeping only the first one
        $titles = [
            'Pancasila Sebagai Dasar Negara dan Ideologi Bangsa',
            'Undang-Undang Dasar Negara Republik Indonesia Tahun 1945',
            'Negara Kesatuan Republik Indonesia (NKRI) dan Kedaulatan Wilayah',
            'Bhinneka Tunggal Ika: Harmoni dalam Keberagaman Bangsa'
        ];

        foreach ($titles as $title) {
            $materials = Material::query()->where('title', $title)->get();
            if ($materials->count() > 1) {
                $keepId = $materials->first()->id;
                Material::query()->where('title', $title)->where('id', '!=', $keepId)->delete();
            }
        }

        // 1. Pancasila
        Material::updateOrCreate(
            [
                'pillar' => 'pancasila',
                'title' => 'Pancasila Sebagai Dasar Negara dan Ideologi Bangsa'
            ],
            [
                'content' => '<h3>Pengertian Pancasila</h3>
<p>Pancasila berasal dari bahasa Sanskerta, yaitu "Panca" yang berarti lima dan "Sila" yang berarti dasar, sendi, atau asas. Secara istilah, Pancasila merupakan lima dasar negara Indonesia yang dirumuskan oleh para pendiri bangsa pada sidang BPUPKI pertama tahun 1945.</p>

<h3>Nilai-Nilai Pancasila</h3>
<ul>
    <li><strong>Sila Kesatu (Ketuhanan Yang Maha Esa):</strong> Menjamin kebebasan beragama, saling menghormati antarumat beragama, dan tidak memaksakan suatu agama kepada orang lain.</li>
    <li><strong>Sila Kedua (Kemanusiaan yang Adil dan Beradab):</strong> Menjunjung tinggi persamaan derajat, hak, dan kewajiban manusia tanpa membedakan suku, keturunan, agama, jenis kelamin, kelas sosial, maupun warna kulit.</li>
    <li><strong>Sila Ketiga (Persatuan Indonesia):</strong> Menempatkan persatuan, kesatuan, kepentingan, dan keselamatan bangsa di atas kepentingan pribadi atau golongan (nasionalisme).</li>
    <li><strong>Sila Keempat (Kerakyatan yang Dipimpin oleh Hikmat Kebijaksanaan dalam Permusyawaratan/Perwakilan):</strong> Mengutamakan musyawarah untuk mufakat dalam pengambilan keputusan bersama serta menghargai pendapat orang lain.</li>
    <li><strong>Sila Kelima (Keadilan Sosial bagi Seluruh Rakyat Indonesia):</strong> Mengembangkan perbuatan luhur yang mencerminkan sikap dan suasana kekeluargaan, kegotongroyongan, serta menjaga keseimbangan antara hak dan kewajiban.</li>
</ul>

<h3>Pancasila sebagai Ideologi Terbuka</h3>
<p>Pancasila merupakan ideologi terbuka yang mampu menyesuaikan diri dengan perkembangan zaman tanpa mengubah nilai-nilai dasarnya. Ideologi terbuka memiliki nilai dasar (tetap), nilai instrumental (penjabaran berupa undang-undang), dan nilai praksis (realisasi dalam kehidupan sehari-hari).</p>',
                'read_time' => 7,
            ]
        );

        // 2. UUD 1945
        Material::updateOrCreate(
            [
                'pillar' => 'uud_1945',
                'title' => 'Undang-Undang Dasar Negara Republik Indonesia Tahun 1945'
            ],
            [
                'content' => '<h3>Kedudukan UUD NRI 1945</h3>
<p>UUD NRI 1945 berkedudukan sebagai hukum dasar tertulis tertinggi di Indonesia. Artinya, seluruh peraturan perundang-undangan di bawahnya (seperti UU, Perpu, PP, Perpres, hingga Perda) tidak boleh bertentangan dengan ketentuan-ketentuan yang terkandung di dalam UUD NRI 1945.</p>

<h3>Sifat UUD NRI 1945</h3>
<ul>
    <li><strong>Tertulis:</strong> Rumusannya jelas dan mengikat bagi pemerintah maupun warga negara.</li>
    <li><strong>Singkat dan Supel:</strong> Memuat aturan-aturan pokok yang dapat dikembangkan sesuai perkembangan zaman.</li>
    <li><strong>Rigid (Kaku):</strong> Membutuhkan prosedur khusus/sulit untuk mengubahnya (sesuai Pasal 37 UUD NRI 1945).</li>
</ul>

<h3>Amandemen UUD NRI 1945</h3>
<p>Amandemen atau perubahan UUD NRI 1945 telah dilakukan sebanyak empat kali oleh MPR pada kurun waktu 1999 hingga 2002. Tujuan amandemen ini adalah untuk menyempurnakan aturan dasar penyelenggaraan negara, kedaulatan rakyat, pembagian kekuasaan (check and balances), serta perlindungan Hak Asasi Manusia (HAM).</p>',
                'read_time' => 6,
            ]
        );

        // 3. NKRI
        Material::updateOrCreate(
            [
                'pillar' => 'nkri',
                'title' => 'Negara Kesatuan Republik Indonesia (NKRI) dan Kedaulatan Wilayah'
            ],
            [
                'content' => '<h3>Bentuk Negara Kesatuan</h3>
<p>Sesuai dengan Pasal 1 Ayat 1 UUD NRI 1945, "Negara Indonesia ialah Negara Kesatuan, yang berbentuk Republik". Kekuasaan tertinggi atas pemerintahan negara berada di tangan pemerintah pusat, yang melimpahkan sebagian wewenang kepada pemerintah daerah melalui asas otonomi daerah.</p>

<h3>Deklarasi Djuanda (1957)</h3>
<p>Deklarasi Djuanda yang dicetuskan pada tanggal 13 Desember 1957 oleh Perdana Menteri Djuanda Kartawidjaja menegaskan bahwa laut di antara dan di dalam kepulauan Indonesia menjadi satu kesatuan wilayah NKRI. Hal ini mengubah konsepsi laut teritorial dari batas 3 mil laut menjadi 12 mil laut dari garis pangkal terluar, yang diakui dunia internasional melalui Konvensi Hukum Laut PBB (UNCLOS 1982).</p>

<h3>Upaya Bela Negara</h3>
<p>Bela negara adalah sikap, tekad, dan perilaku warga negara yang dijiwai oleh kecintaannya kepada NKRI. Bela negara dapat dilakukan secara fisik (militer/pertahanan) maupun non-fisik (prestasi, menjaga persatuan, menolak hoaks, melestarikan budaya, dan belajar dengan tekun).</p>',
                'read_time' => 5,
            ]
        );

        // 4. Bhinneka Tunggal Ika
        Material::updateOrCreate(
            [
                'pillar' => 'bhinneka_tunggal_ika',
                'title' => 'Bhinneka Tunggal Ika: Harmoni dalam Keberagaman Bangsa'
            ],
            [
                'content' => '<h3>Sejarah Semboyan Negara</h3>
<p>Semboyan "Bhinneka Tunggal Ika" diambil dari kitab Kakawin Sutasoma karangan Mpu Tantular yang ditulis pada masa Kerajaan Majapahit abad ke-14. Arti kata Bhinneka adalah "beraneka ragam", Tunggal berarti "satu", dan Ika berarti "itu". Secara harfiah diterjemahkan sebagai "Berbeda-beda tetapi tetap satu jua".</p>

<h3>Keberagaman Indonesia</h3>
<p>Indonesia memiliki keberagaman suku bangsa (lebih dari 1.300 suku), bahasa daerah, agama (6 agama resmi), ras, dan golongan. Keberagaman ini disatukan oleh komitmen bersama bangsa Indonesia yang dicetuskan dalam Sumpah Pemuda pada 28 Oktober 1928.</p>

<h3>Tantangan dan Solusi Integrasi Nasional</h3>
<p>Tantangan integrasi nasional meliputi etnosentrisme (menganggap suku sendiri paling baik), primordialisme, intoleransi, dan radikalisme. Untuk mengatasinya, diperlukan sikap toleransi, moderasi beragama, dialog lintas budaya, serta penegakan hukum yang adil bagi setiap warga negara tanpa memandang latar belakang.</p>',
                'read_time' => 6,
                'type' => 'text',
            ]
        );

        // Seed Video Materials
        // 1. Pancasila Video
        Material::updateOrCreate(
            [
                'pillar' => 'pancasila',
                'title' => 'Video Pembelajaran: Nilai-Nilai Luhur Pancasila'
            ],
            [
                'type' => 'video',
                'video_url' => 'https://www.youtube.com/watch?v=XaMRSuZSt0E',
                'read_time' => 8, // Duration in minutes
                'content' => '<p>Video ini menjelaskan tentang nilai-nilai luhur yang terkandung dalam setiap sila Pancasila dan bagaimana menerapkannya dalam kehidupan sehari-hari sebagai pelajar Pancasila yang baik.</p>'
            ]
        );

        // 2. UUD 1945 Video
        Material::updateOrCreate(
            [
                'pillar' => 'uud_1945',
                'title' => 'Video Pembelajaran: Sejarah Amandemen UUD NRI 1945'
            ],
            [
                'type' => 'video',
                'video_url' => 'https://www.youtube.com/watch?v=MsLJ34Xl1Pc',
                'read_time' => 10,
                'content' => '<p>Video edukasi mengenai sejarah perumusan UUD 1945, kedudukannya dalam tata hukum di Indonesia, serta jalannya proses amandemen ke-1 hingga ke-4 oleh MPR.</p>'
            ]
        );

        // 3. NKRI Video
        Material::updateOrCreate(
            [
                'pillar' => 'nkri',
                'title' => 'Video Pembelajaran: Deklarasi Djuanda & Kedaulatan Wilayah NKRI'
            ],
            [
                'type' => 'video',
                'video_url' => 'https://www.youtube.com/watch?v=pgyx_OtnuEQ',
                'read_time' => 7,
                'content' => '<p>Mempelajari pentingnya Deklarasi Djuanda tahun 1957 dalam memperjuangkan kedaulatan laut pedalaman Indonesia hingga diakui oleh UNCLOS 1982.</p>'
            ]
        );

        // 4. Bhinneka Tunggal Ika Video
        Material::updateOrCreate(
            [
                'pillar' => 'bhinneka_tunggal_ika',
                'title' => 'Video Pembelajaran: Harmoni Keberagaman Indonesia'
            ],
            [
                'type' => 'video',
                'video_url' => 'https://www.youtube.com/watch?v=UVw3fcInSuc',
                'read_time' => 6,
                'content' => '<p>Memahami kekayaan keragaman budaya, suku, dan agama di Indonesia, serta pentingnya toleransi untuk menjaga kerukunan nasional.</p>'
            ]
        );
    }
}
