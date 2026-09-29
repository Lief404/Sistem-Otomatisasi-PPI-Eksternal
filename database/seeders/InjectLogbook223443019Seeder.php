<?php

namespace Database\Seeders;

use App\Models\{User, ProgramStudi, MataKuliah, Mahasiswa, Dosen, PembimbingIndustri, Logbook, Penilaian};
use App\Models\{DisiplinMahasiswa, KuisionerMentor, SaranMahasiswa};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class InjectLogbook223443019Seeder extends Seeder
{
    public function run(): void
    {
        $nimTarget = '223443019';
        $emailTarget = 'trin@mhs.polman'; // Sesuai dengan data tabel users di database Anda
        $namaTarget = 'Mahasiswa TRIN';
        $kelasTarget = '4 AEC 1';

        $prodi = ProgramStudi::firstOrCreate(['nama_prodi' => 'TRIN']);
        $dosen = Dosen::first(); 
        $mentor = PembimbingIndustri::first();
        
        if (!$dosen || !$mentor) { 
            $this->command->warn('Seed dosen dan mentor terlebih dahulu.'); 
            return; 
        }

        // 1. Setup Mata Kuliah
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

        // 2. Setup / Update Akun User (Mencari berdasarkan email, bukan username)
        $user = User::firstOrCreate(
            ['email' => $emailTarget], 
            [
                'name' => $namaTarget, 
                'password' => Hash::make('12345678'), 
                'role' => 'mahasiswa'
            ]
        );

        // 3. Setup Mahasiswa
        $mhs = Mahasiswa::updateOrCreate(
            ['nim' => $nimTarget],
            [
                'user_id' => $user->id, 
                'nama_mhs' => $namaTarget, 
                'kelas' => $kelasTarget, 
                'id_prodi' => $prodi->id_prodi, 
                'nidn' => $dosen->nidn, 
                'id_pem' => $mentor->id_pem
            ]
        );

        // 4. Bersihkan Logbook Lama & Generate 20 Minggu
        Logbook::where('nim', $mhs->nim)->delete();
        $startDate = Carbon::create(2026, 2, 2); 

        for ($week = 1; $week <= 20; $week++) {
            for ($day = 0; $day < 5; $day++) {
                $tanggal = $startDate->copy()->addWeeks($week - 1)->addDays($day)->toDateString();

                if ($day === 0) {
                    // HARI SENIN: 1 JAM K3 & 7 JAM PII
                    Logbook::create([
                        'nim' => $mhs->nim, 'kd_mat' => 'K3', 'tanggal' => $tanggal,
                        'durasi_mnt' => 60, 'kegiatan' => "Safety Briefing K3 & Pemakaian APD minggu $week",
                        'status' => 'Dinilai', 'jam_mulai' => '08:00', 'jam_selesai' => '09:00',
                        'minggu_ke' => $week, 'nilai' => rand(85, 95), 'predikat_nilai' => 'A'
                    ]);

                    Logbook::create([
                        'nim' => $mhs->nim, 'kd_mat' => 'PII', 'tanggal' => $tanggal,
                        'durasi_mnt' => 420, 'kegiatan' => "Melakukan perancangan dan setup PLC minggu $week",
                        'status' => 'Dinilai', 'jam_mulai' => '09:00', 'jam_selesai' => '16:00',
                        'minggu_ke' => $week, 'nilai' => rand(85, 95), 'predikat_nilai' => 'A'
                    ]);
                } else {
                    // SELASA - JUMAT (Masing-masing 8 Jam)
                    $kodeMatkul = match($day) { 1, 2 => 'PII', 3 => 'LTD', 4 => 'SUP' };
                    $kegiatan = match($kodeMatkul) {
                        'PII' => "Pengerjaan projek industri dan otomasi perangkat minggu $week",
                        'LTD' => "Penyusunan laporan harian dan teknis dokumentasi alat minggu $week",
                        'SUP' => "Supervisi lapangan dan monitoring proses produksi minggu $week"
                    };

                    Logbook::create([
                        'nim' => $mhs->nim, 'kd_mat' => $kodeMatkul, 'tanggal' => $tanggal,
                        'durasi_mnt' => 480, 'kegiatan' => $kegiatan,
                        'status' => 'Dinilai', 'jam_mulai' => '08:00', 'jam_selesai' => '16:00',
                        'minggu_ke' => $week, 'nilai' => rand(85, 95), 'predikat_nilai' => 'A'
                    ]);
                }
            }
        }
    }
}