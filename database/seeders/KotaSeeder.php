<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KotaSeeder extends Seeder
{
    /**
     * Get data of all 98 Kota in Indonesia.
     */
    public function getData(): array
    {
        return [
            [11, 'Kota Banda Aceh', 'kota'],
            [11, 'Kota Sabang', 'kota'],
            [11, 'Kota Langsa', 'kota'],
            [11, 'Kota Lhokseumawe', 'kota'],
            [11, 'Kota Subulussalam', 'kota'],
            [12, 'Kota Sibolga', 'kota'],
            [12, 'Kota Tanjungbalai', 'kota'],
            [12, 'Kota Pematangsiantar', 'kota'],
            [12, 'Kota Tebing Tinggi', 'kota'],
            [12, 'Kota Medan', 'kota'],
            [12, 'Kota Binjai', 'kota'],
            [12, 'Kota Padangsidimpuan', 'kota'],
            [12, 'Kota Gunungsitoli', 'kota'],
            [13, 'Kota Padang', 'kota'],
            [13, 'Kota Solok', 'kota'],
            [13, 'Kota Sawahlunto', 'kota'],
            [13, 'Kota Padang Panjang', 'kota'],
            [13, 'Kota Bukittinggi', 'kota'],
            [13, 'Kota Payakumbuh', 'kota'],
            [13, 'Kota Pariaman', 'kota'],
            [14, 'Kota Pekanbaru', 'kota'],
            [14, 'Kota Dumai', 'kota'],
            [15, 'Kota Jambi', 'kota'],
            [15, 'Kota Sungai Penuh', 'kota'],
            [16, 'Kota Palembang', 'kota'],
            [16, 'Kota Prabumulih', 'kota'],
            [16, 'Kota Pagar Alam', 'kota'],
            [16, 'Kota Lubuklinggau', 'kota'],
            [17, 'Kota Bengkulu', 'kota'],
            [18, 'Kota Bandar Lampung', 'kota'],
            [18, 'Kota Metro', 'kota'],
            [19, 'Kota Pangkalpinang', 'kota'],
            [21, 'Kota Batam', 'kota'],
            [21, 'Kota Tanjungpinang', 'kota'],
            [31, 'Kota Administrasi Jakarta Selatan', 'kota'],
            [31, 'Kota Administrasi Jakarta Timur', 'kota'],
            [31, 'Kota Administrasi Jakarta Pusat', 'kota'],
            [31, 'Kota Administrasi Jakarta Barat', 'kota'],
            [31, 'Kota Administrasi Jakarta Utara', 'kota'],
            [32, 'Kota Bogor', 'kota'],
            [32, 'Kota Sukabumi', 'kota'],
            [32, 'Kota Bandung', 'kota'],
            [32, 'Kota Cirebon', 'kota'],
            [32, 'Kota Bekasi', 'kota'],
            [32, 'Kota Depok', 'kota'],
            [32, 'Kota Cimahi', 'kota'],
            [32, 'Kota Tasikmalaya', 'kota'],
            [32, 'Kota Banjar', 'kota'],
            [33, 'Kota Magelang', 'kota'],
            [33, 'Kota Surakarta', 'kota'],
            [33, 'Kota Salatiga', 'kota'],
            [33, 'Kota Semarang', 'kota'],
            [33, 'Kota Pekalongan', 'kota'],
            [33, 'Kota Tegal', 'kota'],
            [34, 'Kota Yogyakarta', 'kota'],
            [35, 'Kota Kediri', 'kota'],
            [35, 'Kota Blitar', 'kota'],
            [35, 'Kota Malang', 'kota'],
            [35, 'Kota Probolinggo', 'kota'],
            [35, 'Kota Pasuruan', 'kota'],
            [35, 'Kota Mojokerto', 'kota'],
            [35, 'Kota Madiun', 'kota'],
            [35, 'Kota Surabaya', 'kota'],
            [35, 'Kota Batu', 'kota'],
            [36, 'Kota Tangerang', 'kota'],
            [36, 'Kota Cilegon', 'kota'],
            [36, 'Kota Serang', 'kota'],
            [36, 'Kota Tangerang Selatan', 'kota'],
            [51, 'Kota Denpasar', 'kota'],
            [52, 'Kota Mataram', 'kota'],
            [52, 'Kota Bima', 'kota'],
            [53, 'Kota Kupang', 'kota'],
            [61, 'Kota Pontianak', 'kota'],
            [61, 'Kota Singkawang', 'kota'],
            [62, 'Kota Palangka Raya', 'kota'],
            [63, 'Kota Banjarmasin', 'kota'],
            [63, 'Kota Banjarbaru', 'kota'],
            [64, 'Kota Balikpapan', 'kota'],
            [64, 'Kota Samarinda', 'kota'],
            [64, 'Kota Bontang', 'kota'],
            [65, 'Kota Tarakan', 'kota'],
            [71, 'Kota Manado', 'kota'],
            [71, 'Kota Bitung', 'kota'],
            [71, 'Kota Tomohon', 'kota'],
            [71, 'Kota Kotamobagu', 'kota'],
            [72, 'Kota Palu', 'kota'],
            [73, 'Kota Makassar', 'kota'],
            [73, 'Kota Parepare', 'kota'],
            [73, 'Kota Palopo', 'kota'],
            [74, 'Kota Kendari', 'kota'],
            [74, 'Kota Baubau', 'kota'],
            [75, 'Kota Gorontalo', 'kota'],
            [81, 'Kota Ambon', 'kota'],
            [81, 'Kota Tual', 'kota'],
            [82, 'Kota Ternate', 'kota'],
            [82, 'Kota Tidore Kepulauan', 'kota'],
            [92, 'Kota Jayapura', 'kota'],
            [96, 'Kota Sorong', 'kota'],
        ];
    }

    /**
     * Run the database seeds for Kota.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $raw = $this->getData();

        $kotas = [];
        foreach ($raw as $item) {
            $kotas[] = [
                'province_id' => $item[0],
                'name' => $item[1],
                'type' => $item[2],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Avoid duplicate seeding if already populated
        if (DB::table('regencies')->where('type', 'kota')->count() === 0) {
            foreach (array_chunk($kotas, 100) as $chunk) {
                DB::table('regencies')->insert($chunk);
            }
        }
    }
}
