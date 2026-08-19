<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Akun Admin
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@polman.edu',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Akun Mahasiswa
        User::create([
            'name' => 'Mahasiswa PPI',
            'email' => 'mahasiswa@polman.edu',
            'password' => Hash::make('password'),
            'role' => 'mahasiswa',
        ]);

        // Akun Dosen
        User::create([
            'name' => 'Dosen Pembimbing',
            'email' => 'dosen@polman.edu',
            'password' => Hash::make('password'),
            'role' => 'dosen',
        ]);

        // Akun Mentor Industri
        User::create([
            'name' => 'Mentor Industri',
            'email' => 'mentor@industri.com',
            'password' => Hash::make('password'),
            'role' => 'mentor',
        ]);
    }
}