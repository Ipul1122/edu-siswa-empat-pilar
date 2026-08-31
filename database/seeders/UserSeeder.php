<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin Account
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin Empat Pilar',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'class_name' => null,
                'school_name' => null,
                'image' => null,
                'address' => null,
                'dapil' => null,
            ]
        );

        // Siswa Account
        User::updateOrCreate(
            ['email' => 'siswa@gmail.com'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password'),
                'role' => 'siswa',
                'class_name' => 'SMA/SMK',
                'school_name' => 'SMK Negeri 1 Jakarta',
                'image' => null,
                'dapil' => 'DKI JAKARTA I',
                'address' => 'Jl. Merdeka No. 45, Gambir, Jakarta Pusat',
            ]
        );
    }
}
