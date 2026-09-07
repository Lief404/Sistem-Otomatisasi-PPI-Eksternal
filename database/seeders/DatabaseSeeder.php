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
        // Membuat Akun Admin Default
        User::create([
            'name'     => 'Super Administrator',
            'username' => 'admin', // Bisa login pakai username 'admin'
            'email'    => 'admin@admin', // Atau login pakai email ini
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
        ]);
        
        // (Opsional) Anda juga bisa menambahkan dummy akun mahasiswa di sini nantinya
    }
}