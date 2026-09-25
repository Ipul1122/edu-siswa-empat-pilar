<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    /**
     * Ambil object ProvincesSeeder.
     */
    public function provincesSeeder(): ProvincesSeeder
    {
        return new ProvincesSeeder();
    }

    /**
     * Ambil object KabupatenSeeder.
     */
    public function kabupatenSeeder(): KabupatenSeeder
    {
        return new KabupatenSeeder();
    }

    /**
     * Ambil object KotaSeeder.
     */
    public function kotaSeeder(): KotaSeeder
    {
        return new KotaSeeder();
    }

    /**
     * Run the database seeds by calling modular region seeders:
     * 1. ProvincesSeeder (38 Provinsi)
     * 2. KabupatenSeeder (416 Kabupaten)\
     * 3. KotaSeeder (98 Kota)
     */
    public function run(): void
    {
        $this->call([
            ProvincesSeeder::class,
            KabupatenSeeder::class,
            KotaSeeder::class,
        ]);
    }
}
