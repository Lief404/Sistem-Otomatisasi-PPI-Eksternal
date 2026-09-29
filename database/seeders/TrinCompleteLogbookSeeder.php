<?php

namespace Database\Seeders;

use App\Models\{User, ProgramStudi, MataKuliah, Mahasiswa, Dosen, PembimbingIndustri, Logbook, Penilaian};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class TrinCompleteLogbookSeeder extends Seeder
{
    public function run(): void
    {
        $prodi = ProgramStudi::firstOrCreate(['nama_prodi' => 'TRIN']);
        $dosen = Dosen::first(); 
        $mentor = PembimbingIndustri::first();
        
        if (!$dosen || !$mentor) { 
            $this->command->warn('Seed dosen dan mentor terlebih dahulu.'); 
            return; 
        }

        // Memastikan 4 Mata Kuliah terdaftar di Database
        $matkuls = [
            ['PII', 'Praktik Pengalaman Industri'],
            ['SUP', 'Supervisi dan Manajemen Industri'],
            ['K3', 'K3 Industri dan Teknologi'],
            ['LTD', 'Laporan Teknik']
        ];

        foreach ($matkuls as [$kode, $nama]) {
            MataKuliah::firstOrCreate(
                ['kd_mat' => $kode], 
                ['id_prodi' => $prodi->id_prodi, 'nama_komp' => $nama, 'jam_min' => 20]
            );
        }

        $user = User::firstOrCreate(
            ['email' => 'alief.sugata@mhs.polman.id'], 
            ['name' => 'Alief Muhammad Sugata', 'password' => Hash::make('12345678'), 'role' => 'mahasiswa']
        );

        $mhs = Mahasiswa::firstOrCreate(
            ['nim' => '223443026'], 
            ['user_id' => $user->id, 'nama_mhs' => 'Alief Muhammad Sugata', 'kelas' => '3AEC2', 'id_prodi' => $prodi->id_prodi, 'nidn' => $dosen->nidn, 'id_pem' => $mentor->id_pem]
        );

        // Hapus logbook lama agar seed tidak menumpuk duplikat
        Logbook::where('nim', $mhs->nim)->delete();
        $startDate = Carbon::create(2025, 8, 11); // Contoh mulai hari Senin

        for ($week = 1; $week <= 20; $week++) {
            
            // Loop 5 Hari kerja (Senin = 0, Jumat = 4)
            for ($day = 0; $day < 5; $day++) {
                $tanggal = $startDate->copy()->addWeeks($week - 1)->addDays($day)->toDateString();

                if ($day === 0) {
                    // ==========================================
                    // KHUSUS HARI SENIN: 1 JAM K3 & 7 JAM PII
                    // ==========================================
                    
                    // 1. Entry Logbook K3 (08:00 - 09:00)
                    $nilaiK3 = rand(85, 95);
                    Logbook::create([
                        'nim' => $mhs->nim,
                        'kd_mat' => 'K3',
                        'tanggal' => $tanggal,
                        'durasi_mnt' => 60, // 1 Jam
                        'kegiatan' => "Safety Briefing & K3 Industri minggu $week",
                        'status' => 'Dinilai',
                        'jam_mulai' => '08:00',
                        'jam_selesai' => '09:00',
                        'minggu_ke' => $week,
                        'nilai' => $nilaiK3,
                        'predikat_nilai' => $nilaiK3 >= 85 ? 'A' : 'AB',
                    ]);

                    // 2. Entry Logbook PII (09:00 - 16:00)
                    $nilaiPII = rand(85, 95);
                    Logbook::create([
                        'nim' => $mhs->nim,
                        'kd_mat' => 'PII',
                        'tanggal' => $tanggal,
                        'durasi_mnt' => 420, // 7 Jam
                        'kegiatan' => "Praktik Pengalaman Industri minggu $week",
                        'status' => 'Dinilai',
                        'jam_mulai' => '09:00',
                        'jam_selesai' => '16:00',
                        'minggu_ke' => $week,
                        'nilai' => $nilaiPII,
                        'predikat_nilai' => $nilaiPII >= 85 ? 'A' : 'AB',
                    ]);

                } else {
                    // ==========================================
                    // HARI SELASA - JUMAT (Masing-masing 8 Jam)
                    // ==========================================
                    $kodeMatkul = match($day) {
                        1, 2 => 'PII', // Selasa & Rabu
                        3 => 'LTD',    // Kamis
                        4 => 'SUP'     // Jumat
                    };

                    $nilai = rand(85, 95);
                    Logbook::create([
                        'nim' => $mhs->nim,
                        'kd_mat' => $kodeMatkul,
                        'tanggal' => $tanggal,
                        'durasi_mnt' => 480, // Full 8 Jam
                        'kegiatan' => "Kegiatan $kodeMatkul PPI minggu $week",
                        'status' => 'Dinilai',
                        'jam_mulai' => '08:00',
                        'jam_selesai' => '16:00',
                        'minggu_ke' => $week,
                        'nilai' => $nilai,
                        'predikat_nilai' => $nilai >= 85 ? 'A' : 'AB',
                    ]);
                }
            }
        }

        // Memastikan penilaian lengkap (Form Dosen & Form Mentor)
        Penilaian::updateOrCreate(
            ['nim' => $mhs->nim],
            [
                'nidn' => $dosen->nidn,
                'id_pem' => $mentor->id_pem,
                'n_presentasi' => 92.375, // Mengikuti data Excel Alief M. Sugata
                'n_makalah' => 92.375,
                'n_prestasi' => 88,
                'n_supervisi' => 88
            ]
        );
    }
}