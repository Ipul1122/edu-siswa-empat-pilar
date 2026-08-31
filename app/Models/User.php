<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Complete mapping of 84 DPR-RI Electoral Districts (Dapil) with their official coverage regions (Kabupaten/Kota).
     */
    public const DAPIL_DETAILS = [
        'ACEH I' => 'Kab. Aceh Barat, Aceh Barat Daya, Aceh Besar, Aceh Jaya, Aceh Selatan, Aceh Singkil, Aceh Tenggara, Gayo Lues, Nagan Raya, Pidie, Pidie Jaya, Simeulue, Kota Banda Aceh, Kota Sabang, Kota Subulussalam',
        'ACEH II' => 'Kab. Aceh Tengah, Aceh Timur, Aceh Utara, Bener Meriah, Bireuen, Aceh Tamiang, Kota Langsa, Kota Lhokseumawe',
        'BALI' => 'Kab. Badung, Bangli, Buleleng, Gianyar, Jembrana, Karangasem, Klungkung, Tabanan, Kota Denpasar',
        'BANTEN I' => 'Kab. Lebak, Kab. Pandeglang',
        'BANTEN II' => 'Kab. Serang, Kota Cilegon, Kota Serang',
        'BANTEN III' => 'Kab. Tangerang, Kota Tangerang, Kota Tangerang Selatan',
        'BENGKULU' => 'Kab. Bengkulu Selatan, Bengkulu Tengah, Bengkulu Utara, Kaur, Kepahiang, Lebong, Mukomuko, Rejang Lebong, Seluma, Kota Bengkulu',
        'DAERAH ISTIMEWA YOGYAKARTA' => 'Kab. Bantul, Gunungkidul, Kulon Progo, Sleman, Kota Yogyakarta',
        'DKI JAKARTA I' => 'Kota Administrasi Jakarta Timur',
        'DKI JAKARTA II' => 'Kota Administrasi Jakarta Pusat, Jakarta Selatan, Luar Negeri',
        'DKI JAKARTA III' => 'Kota Administrasi Jakarta Barat, Jakarta Utara, Kab. Kepulauan Seribu',
        'GORONTALO' => 'Kab. Boalemo, Bone Bolango, Gorontalo, Gorontalo Utara, Pohuwato, Kota Gorontalo',
        'JAMBI' => 'Kab. Batanghari, Bungo, Kerinci, Merangin, Muaro Jambi, Sarolangun, Tanjung Jabung Barat, Tanjung Jabung Timur, Tebo, Kota Jambi, Kota Sungai Penuh',
        'JAWA BARAT I' => 'Kota Bandung, Kota Cimahi',
        'JAWA BARAT II' => 'Kab. Bandung, Kab. Bandung Barat',
        'JAWA BARAT III' => 'Kota Bogor, Kab. Cianjur',
        'JAWA BARAT IV' => 'Kab. Sukabumi, Kota Sukabumi',
        'JAWA BARAT V' => 'Kab. Bogor',
        'JAWA BARAT VI' => 'Kota Bekasi, Kota Depok',
        'JAWA BARAT VII' => 'Kab. Bekasi, Kab. Karawang, Kab. Purwakarta',
        'JAWA BARAT VIII' => 'Kab. Cirebon, Kab. Indramayu, Kota Cirebon',
        'JAWA BARAT IX' => 'Kab. Majalengka, Kab. Sumedang, Kab. Subang',
        'JAWA BARAT X' => 'Kab. Ciamis, Kab. Kuningan, Kab. Pangandaran, Kota Banjar',
        'JAWA BARAT XI' => 'Kab. Garut, Kab. Tasikmalaya, Kota Tasikmalaya',
        'JAWA TENGAH I' => 'Kab. Semarang, Kab. Kendal, Kota Salatiga, Kota Semarang',
        'JAWA TENGAH II' => 'Kab. Kudus, Kab. Jepara, Kab. Demak',
        'JAWA TENGAH III' => 'Kab. Grobogan, Kab. Blora, Kab. Rembang, Kab. Pati',
        'JAWA TENGAH IV' => 'Kab. Wonogiri, Kab. Karanganyar, Kab. Sragen',
        'JAWA TENGAH V' => 'Kab. Boyolali, Kab. Klaten, Kab. Sukoharjo, Kota Surakarta (Solo)',
        'JAWA TENGAH VI' => 'Kab. Magelang, Kab. Purworejo, Kab. Temanggung, Kab. Wonosobo, Kota Magelang',
        'JAWA TENGAH VII' => 'Kab. Purbalingga, Kab. Banjarnegara, Kab. Kebumen',
        'JAWA TENGAH VIII' => 'Kab. Cilacap, Kab. Banyumas (Purwokerto)',
        'JAWA TENGAH IX' => 'Kab. Brebes, Kab. Tegal, Kota Tegal',
        'JAWA TENGAH X' => 'Kab. Batang, Kab. Pekalongan, Kab. Pemalang, Kota Pekalongan',
        'JAWA TIMUR I' => 'Kab. Sidoarjo, Kota Surabaya',
        'JAWA TIMUR II' => 'Kab. Pasuruan, Kab. Probolinggo, Kota Pasuruan, Kota Probolinggo',
        'JAWA TIMUR III' => 'Kab. Banyuwangi, Kab. Bondowoso, Kab. Situbondo',
        'JAWA TIMUR IV' => 'Kab. Lumajang, Kab. Jember',
        'JAWA TIMUR V' => 'Kab. Malang, Kota Batu, Kota Malang',
        'JAWA TIMUR VI' => 'Kab. Blitar, Kab. Kediri, Kab. Tulungagung, Kota Blitar, Kota Kediri',
        'JAWA TIMUR VII' => 'Kab. Pacitan, Kab. Ponorogo, Kab. Trenggalek, Kab. Magetan, Kab. Ngawi',
        'JAWA TIMUR VIII' => 'Kab. Jombang, Kab. Madiun, Kab. Mojokerto, Kab. Nganjuk, Kota Madiun, Kota Mojokerto',
        'JAWA TIMUR IX' => 'Kab. Bojonegoro, Kab. Tuban',
        'JAWA TIMUR X' => 'Kab. Lamongan, Kab. Gresik',
        'JAWA TIMUR XI' => 'Kab. Bangkalan, Kab. Sampang, Kab. Pamekasan, Kab. Sumenep (Madura)',
        'KALIMANTAN BARAT I' => 'Kab. Sambas, Bengkayang, Landak, Mempawah, Sanggau, Sekadau, Kubu Raya, Kota Pontianak, Kota Singkawang',
        'KALIMANTAN BARAT II' => 'Kab. Sintang, Kapuas Hulu, Melawi, Ketapang, Kayong Utara',
        'KALIMANTAN SELATAN I' => 'Kab. Banjar, Barito Kuala, Tapin, Hulu Sungai Selatan, Hulu Sungai Tengah, Hulu Sungai Utara, Tabalong, Balangan',
        'KALIMANTAN SELATAN II' => 'Kab. Tanah Laut, Kotabaru, Tanah Bumbu, Kota Banjarmasin, Kota Banjarbaru',
        'KALIMANTAN TENGAH' => 'Kab. Barito Selatan, Barito Timur, Barito Utara, Gunung Mas, Kapuas, Katingan, Kotawaringin Barat, Kotawaringin Timur, Lamandau, Murung Raya, Pulang Pisau, Sukamara, Seruyan, Kota Palangka Raya',
        'KALIMANTAN TIMUR' => 'Kab. Berau, Kutai Barat, Kutai Kartanegara, Kutai Timur, Mahakam Ulu, Paser, Penajam Paser Utara, Kota Balikpapan, Kota Bontang, Kota Samarinda',
        'KALIMANTAN UTARA' => 'Kab. Bulungan, Malinau, Nunukan, Tana Tidung, Kota Tarakan',
        'KEPULAUAN BANGKA BELITUNG' => 'Kab. Bangka, Bangka Barat, Bangka Selatan, Bangka Tengah, Belitung, Belitung Timur, Kota Pangkalpinang',
        'KEPULAUAN RIAU' => 'Kab. Bintan, Karimun, Kepulauan Anambas, Lingga, Natuna, Kota Batam, Kota Tanjungpinang',
        'LAMPUNG I' => 'Kab. Lampung Selatan, Lampung Barat, Pesawaran, Pesisir Barat, Pringsewu, Tanggamus, Kota Bandar Lampung, Kota Metro',
        'LAMPUNG II' => 'Kab. Lampung Tengah, Lampung Timur, Lampung Utara, Mesuji, Tulang Bawang, Tulang Bawang Barat, Way Kanan',
        'MALUKU' => 'Kab. Buru, Buru Selatan, Kepulauan Aru, Kepulauan Tanimbar, Maluku Barat Daya, Maluku Tengah, Maluku Tenggara, Seram Bagian Barat, Seram Bagian Timur, Kota Ambon, Kota Tual',
        'MALUKU UTARA' => 'Kab. Halmahera Barat, Halmahera Tengah, Halmahera Timur, Halmahera Selatan, Halmahera Utara, Kepulauan Sula, Pulau Morotai, Pulau Taliabu, Kota Ternate, Kota Tidore Kepulauan',
        'NUSA TENGGARA BARAT I' => 'Kab. Bima, Dompu, Sumbawa, Sumbawa Barat, Kota Bima (Pulau Sumbawa)',
        'NUSA TENGGARA BARAT II' => 'Kab. Lombok Barat, Lombok Tengah, Lombok Timur, Lombok Utara, Kota Mataram (Pulau Lombok)',
        'NUSA TENGGARA TIMUR I' => 'Kab. Alor, Lembata, Flores Timur, Sikka, Ende, Ngada, Nagekeo, Manggarai, Manggarai Barat, Manggarai Timur (Pulau Flores)',
        'NUSA TENGGARA TIMUR II' => 'Kab. Kupang, Timor Tengah Selatan, Timor Tengah Utara, Belu, Malaka, Rote Ndao, Sabu Raijua, Sumba Barat, Sumba Timur, Sumba Barat Daya, Sumba Tengah, Kota Kupang (Pulau Timor & Sumba)',
        'PAPUA' => 'Kab. Biak Numfor, Jayapura, Keerom, Kepulauan Yapen, Mamberamo Raya, Sarmi, Supiori, Waropen, Kota Jayapura',
        'PAPUA BARAT' => 'Kab. Fakfak, Kaimana, Manokwari, Manokwari Selatan, Pegunungan Arfak, Teluk Bintuni, Teluk Wondama',
        'PAPUA BARAT DAYA' => 'Kab. Maybrat, Raja Ampat, Sorong, Sorong Selatan, Tambrauw, Kota Sorong',
        'PAPUA PEGUNUNGAN' => 'Kab. Jayawijaya, Lanny Jaya, Mamberamo Tengah, Nduga, Pegunungan Bintang, Tolikara, Yalimo, Yahukimo',
        'PAPUA SELATAN' => 'Kab. Asmat, Boven Digoel, Mappi, Merauke',
        'PAPUA TENGAH' => 'Kab. Deiyai, Dogiyai, Intan Jaya, Mimika, Nabire, Paniai, Puncak, Puncak Jaya',
        'RIAU I' => 'Kab. Bengkalis, Kepulauan Meranti, Rokan Hilir, Rokan Hulu, Siak, Kota Dumai, Kota Pekanbaru',
        'RIAU II' => 'Kab. Indragiri Hilir, Indragiri Hulu, Kampar, Kuantan Singingi, Pelalawan',
        'SULAWESI BARAT' => 'Kab. Majene, Mamasa, Mamuju, Mamuju Tengah, Pasangkayu, Polewali Mandar',
        'SULAWESI SELATAN I' => 'Kab. Bantaeng, Gowa, Jeneponto, Kepulauan Selayar, Takalar, Kota Makassar',
        'SULAWESI SELATAN II' => 'Kab. Barru, Bulukumba, Bone, Maros, Pangkajene dan Kepulauan, Sinjai, Soppeng, Wajo, Kota Parepare',
        'SULAWESI SELATAN III' => 'Kab. Enrekang, Luwu, Luwu Timur, Luwu Utara, Pinrang, Sidenreng Rappang, Tana Toraja, Toraja Utara, Kota Palopo',
        'SULAWESI TENGAH' => 'Kab. Banggai, Banggai Kepulauan, Banggai Laut, Buol, Donggala, Morowali, Morowali Utara, Parigi Moutong, Poso, Sigi, Tojo Una-Una, Tolitoli, Kota Palu',
        'SULAWESI TENGGARA' => 'Kab. Bombana, Buton, Buton Selatan, Buton Tengah, Buton Utara, Kolaka, Kolaka Timur, Kolaka Utara, Konawe, Konawe Kepulauan, Konawe Selatan, Konawe Utara, Muna, Muna Barat, Wakatobi, Kota Baubau, Kota Kendari',
        'SULAWESI UTARA' => 'Kab. Bolaang Mongondow, Bolmong Selatan, Bolmong Timur, Bolmong Utara, Kepulauan Sangihe, Kepulauan Siau Tagulandang Biaro, Kepulauan Talaud, Minahasa, Minahasa Selatan, Minahasa Tenggara, Minahasa Utara, Kota Bitung, Kota Kotamobagu, Kota Manado, Kota Tomohon',
        'SUMATERA BARAT I' => 'Kab. Dharmasraya, Kepulauan Mentawai, Pesisir Selatan, Sijunjung, Solok, Solok Selatan, Tanah Datar, Kota Padang, Kota Padang Panjang, Kota Sawahlunto, Kota Solok',
        'SUMATERA BARAT II' => 'Kab. Agam, Limapuluh Kota, Padang Pariaman, Pasaman, Pasaman Barat, Kota Bukittinggi, Kota Pariaman, Kota Payakumbuh',
        'SUMATERA SELATAN I' => 'Kab. Banyuasin, Musi Banyuasin, Musi Rawas, Musi Rawas Utara, Kota Lubuklinggau, Kota Palembang',
        'SUMATERA SELATAN II' => 'Kab. Empat Lawang, Lahat, Muara Enim, Ogan Ilir, Ogan Komering Ilir, Ogan Komering Ulu, OKU Selatan, OKU Timur, Penukal Abab Lematang Ilir, Kota Pagar Alam, Kota Prabumulih',
        'SUMATERA UTARA I' => 'Kab. Deli Serdang, Serdang Bedagai, Kota Medan, Kota Tebing Tinggi',
        'SUMATERA UTARA II' => 'Kab. Asahan, Dairi, Humbang Hasundutan, Karo, Labuhanbatu, Labuhanbatu Selatan, Labuhanbatu Utara, Mandailing Natal, Nias, Nias Barat, Nias Selatan, Nias Utara, Padang Lawas, Padang Lawas Utara, Pakpak Bharat, Samosir, Simalungun, Tapanuli Selatan, Tapanuli Tengah, Tapanuli Utara, Toba, Kota Gunungsitoli, Kota Padangsidimpuan, Kota Pematangsiantar, Kota Sibolga, Kota Tanjungbalai',
        'SUMATERA UTARA III' => 'Kab. Asahan, Dairi, Karo, Langkat, Pakpak Bharat, Simalungun, Kota Binjai, Kota Pematangsiantar, Kota Tanjungbalai',
    ];

    /**
     * Complete list of 84 DPR-RI Electoral Districts (Dapil) sorted ascending (A-Z).
     */
    public const DAPIL_LIST = [
        'ACEH I',
        'ACEH II',
        'BALI',
        'BANTEN I',
        'BANTEN II',
        'BANTEN III',
        'BENGKULU',
        'DAERAH ISTIMEWA YOGYAKARTA',
        'DKI JAKARTA I',
        'DKI JAKARTA II',
        'DKI JAKARTA III',
        'GORONTALO',
        'JAMBI',
        'JAWA BARAT I',
        'JAWA BARAT II',
        'JAWA BARAT III',
        'JAWA BARAT IV',
        'JAWA BARAT V',
        'JAWA BARAT VI',
        'JAWA BARAT VII',
        'JAWA BARAT VIII',
        'JAWA BARAT IX',
        'JAWA BARAT X',
        'JAWA BARAT XI',
        'JAWA TENGAH I',
        'JAWA TENGAH II',
        'JAWA TENGAH III',
        'JAWA TENGAH IV',
        'JAWA TENGAH V',
        'JAWA TENGAH VI',
        'JAWA TENGAH VII',
        'JAWA TENGAH VIII',
        'JAWA TENGAH IX',
        'JAWA TENGAH X',
        'JAWA TIMUR I',
        'JAWA TIMUR II',
        'JAWA TIMUR III',
        'JAWA TIMUR IV',
        'JAWA TIMUR V',
        'JAWA TIMUR VI',
        'JAWA TIMUR VII',
        'JAWA TIMUR VIII',
        'JAWA TIMUR IX',
        'JAWA TIMUR X',
        'JAWA TIMUR XI',
        'KALIMANTAN BARAT I',
        'KALIMANTAN BARAT II',
        'KALIMANTAN SELATAN I',
        'KALIMANTAN SELATAN II',
        'KALIMANTAN TENGAH',
        'KALIMANTAN TIMUR',
        'KALIMANTAN UTARA',
        'KEPULAUAN BANGKA BELITUNG',
        'KEPULAUAN RIAU',
        'LAMPUNG I',
        'LAMPUNG II',
        'MALUKU',
        'MALUKU UTARA',
        'NUSA TENGGARA BARAT I',
        'NUSA TENGGARA BARAT II',
        'NUSA TENGGARA TIMUR I',
        'NUSA TENGGARA TIMUR II',
        'PAPUA',
        'PAPUA BARAT',
        'PAPUA BARAT DAYA',
        'PAPUA PEGUNUNGAN',
        'PAPUA SELATAN',
        'PAPUA TENGAH',
        'RIAU I',
        'RIAU II',
        'SULAWESI BARAT',
        'SULAWESI SELATAN I',
        'SULAWESI SELATAN II',
        'SULAWESI SELATAN III',
        'SULAWESI TENGAH',
        'SULAWESI TENGGARA',
        'SULAWESI UTARA',
        'SUMATERA BARAT I',
        'SUMATERA BARAT II',
        'SUMATERA SELATAN I',
        'SUMATERA SELATAN II',
        'SUMATERA UTARA I',
        'SUMATERA UTARA II',
        'SUMATERA UTARA III',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'class_name',
        'school_name',
        'image',
        'address',
        'dapil',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Helper to get avatar / photo URL.
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            if (str_starts_with($this->image, 'http')) {
                return $this->image;
            }
            return asset('storage/' . $this->image);
        }

        // Fallback default avatar generator using initials
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=dc2626&color=ffffff&size=200&bold=true';
    }

    /**
     * Helper to get Dapil coverage text.
     */
    public function getDapilCoverageAttribute(): ?string
    {
        return self::DAPIL_DETAILS[$this->dapil] ?? null;
    }

    /**
     * Check if user is Admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is Siswa.
     */
    public function isSiswa(): bool
    {
        return $this->role === 'siswa';
    }

    /**
     * Relationship to student progress.
     */
    public function progress(): HasMany
    {
        return $this->hasMany(StudentProgress::class);
    }

    /**
     * Relationship to quiz attempts.
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }
}
