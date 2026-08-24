<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Akun Admin
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@admin.polman',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Akun Mahasiswa
        User::create([
            'name' => 'Mahasiswa PPI',
            'email' => '223443026@mhs.polman', // Format NIM
            'password' => Hash::make('password'),
            'role' => 'mahasiswa',
        ]);

        // Akun Dosen
        User::create([
            'name' => 'Supriyadi',
            'email' => 'supriyadi@dosen.polman', // Format Nama Dosen
            'password' => Hash::make('password'),
            'role' => 'dosen',
        ]);

        // Akun Mentor Industri
        User::create([
            'name' => 'PT Bukaka Teknik',
            'email' => 'bukaka@industri.id', // Format Industri
            'password' => Hash::make('password'),
            'role' => 'mentor',
        ]);
    }
}