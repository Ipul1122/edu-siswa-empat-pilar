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
            ]
        );

        // Siswa Account
        User::updateOrCreate(
            ['email' => 'siswa@gmail.com'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password'),
                'role' => 'siswa',
                'class_name' => 'XII',
                'school_name' => 'SMK Negeri 1 Jakarta',
            ]
        );
    }
}
