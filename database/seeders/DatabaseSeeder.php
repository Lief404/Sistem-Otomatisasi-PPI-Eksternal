<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ProgramStudi;
use App\Models\MataKuliah;
use App\Models\Dosen;
use App\Models\PembimbingIndustri;
use App\Models\Perusahaan;
use App\Models\Mahasiswa;
use App\Models\Logbook;
use App\Models\Penilaian;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Program Studi (Sistem digunakan oleh 3 prodi)
        $prodi1 = ProgramStudi::firstOrCreate(['nama_prodi' => 'TRIN']);
        $prodi2 = ProgramStudi::firstOrCreate(['nama_prodi' => 'TRO']);
        $prodi3 = ProgramStudi::firstOrCreate(['nama_prodi' => 'TRMO']);

        // 2. Mata Kuliah (Sesuai dengan yang ada di Frontend)
        $mkPii = MataKuliah::firstOrCreate(['kd_mat' => 'PII'], ['nama_komp' => 'Praktik Pengalaman Industri', 'jam_min' => 144]);
        $mkSup = MataKuliah::firstOrCreate(['kd_mat' => 'SUP'], ['nama_komp' => 'Supervisi', 'jam_min' => 20]);
        $mkK3 = MataKuliah::firstOrCreate(['kd_mat' => 'K3'], ['nama_komp' => 'K3 IT', 'jam_min' => 10]);
        $mkLtd = MataKuliah::firstOrCreate(['kd_mat' => 'LTD'], ['nama_komp' => 'Laporan Teknik', 'jam_min' => 30]);

        // 3. Users & Profil Dosen, Mentor, Mahasiswa
        
        // Akun Admin
        User::firstOrCreate(
            ['email' => 'admin@admin.polman'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // Akun KPS (Kaprodi)
        User::firstOrCreate(
            ['email' => 'kps@polman.id'],
            [
                'name' => 'Kepala Program Studi',
                'password' => Hash::make('password'),
                'role' => 'kaprodi',
            ]
        );

        // Akun Dosen
        $userDosen = User::create([
            'name' => 'Supriyadi',
            'email' => 'supriyadi@dosen.polman',
            'password' => Hash::make('password'),
            'role' => 'dosen',
        ]);
        $dosen = Dosen::create([
            'nidn' => '1234567890',
            'user_id' => $userDosen->id,
            'nama_dosen' => 'Supriyadi, S.T., M.T.'
        ]);

        // Seed Master Data Perusahaan
        Perusahaan::firstOrCreate(['nama_perusahaan' => 'PT Bukaka Teknik'], ['alamat' => 'Bogor, Jawa Barat', 'kontak' => '021-8230123']);
        Perusahaan::firstOrCreate(['nama_perusahaan' => 'PT Solusi Intek Indonesia'], ['alamat' => 'Bandung, Jawa Barat', 'kontak' => '022-7561234']);
        Perusahaan::firstOrCreate(['nama_perusahaan' => 'PT Pindad Persero'], ['alamat' => 'Bandung, Jawa Barat', 'kontak' => '022-7312041']);

        // Akun Mentor Industri
        $userMentor = User::create([
            'name' => 'Budi Santoso', // Mentor Industri
            'email' => 'bukaka@industri.id',
            'password' => Hash::make('password'),
            'role' => 'mentor',
        ]);
        $mentor = PembimbingIndustri::create([
            'user_id' => $userMentor->id,
            'nama_pem' => 'Budi Santoso',
            'perusahaan' => 'PT Bukaka Teknik'
        ]);

        // Akun Mahasiswa 1
        $userMhs = User::create([
            'name' => 'Mahasiswa PPI',
            'email' => '223443026@mhs.polman',
            'password' => Hash::make('password'),
            'role' => 'mahasiswa',
        ]);
        $mahasiswa = Mahasiswa::create([
            'nim' => '223443026',
            'user_id' => $userMhs->id,
            'nama_mhs' => 'Mahasiswa PPI',
            'kelas' => 'AE-3A',
            'id_prodi' => $prodi3->id_prodi, // Teknik Mekatronika
            'nidn' => $dosen->nidn,
            'id_pem' => $mentor->id_pem
        ]);

        // Akun Mahasiswa 2
        $userMhs2 = User::create([
            'name' => 'Budi Santoso (Mhs)',
            'email' => '223443027@mhs.polman',
            'password' => Hash::make('password'),
            'role' => 'mahasiswa',
        ]);
        $mahasiswa2 = Mahasiswa::create([
            'nim' => '223443027',
            'user_id' => $userMhs2->id,
            'nama_mhs' => 'Budi Santoso',
            'kelas' => 'AE-3B',
            'id_prodi' => $prodi3->id_prodi,
            'nidn' => $dosen->nidn,
            'id_pem' => $mentor->id_pem
        ]);

        // Akun Mahasiswa 3
        $userMhs3 = User::create([
            'name' => 'Rina Melati',
            'email' => '223443028@mhs.polman',
            'password' => Hash::make('password'),
            'role' => 'mahasiswa',
        ]);
        $mahasiswa3 = Mahasiswa::create([
            'nim' => '223443028',
            'user_id' => $userMhs3->id,
            'nama_mhs' => 'Rina Melati',
            'kelas' => 'AE-3A',
            'id_prodi' => $prodi2->id_prodi,
            'nidn' => $dosen->nidn,
            'id_pem' => $mentor->id_pem
        ]);

        // 4. Dummy Logbook
        Logbook::create([
            'nim' => $mahasiswa->nim,
            'kd_mat' => $mkPii->kd_mat,
            'tanggal' => Carbon::now()->subDays(2)->format('Y-m-d'),
            'durasi_mnt' => 480,
            'kegiatan' => 'Melakukan perakitan mesin CNC',
            'status' => 'Kerja',
            'jam_mulai' => '08:00',
            'jam_selesai' => '16:00',
            'minggu_ke' => 1
        ]);

        Logbook::create([
            'nim' => $mahasiswa->nim,
            'kd_mat' => $mkSup->kd_mat,
            'tanggal' => Carbon::now()->subDay()->format('Y-m-d'),
            'durasi_mnt' => 480,
            'kegiatan' => 'Troubleshooting sensor jarak',
            'status' => 'Kerja',
            'jam_mulai' => '08:00',
            'jam_selesai' => '16:00',
            'minggu_ke' => 1
        ]);

        // 5. Dummy Penilaian (Satu dari dosen, satu dari mentor)
        Penilaian::create([
            'nim' => $mahasiswa->nim,
            'nidn' => $dosen->nidn,
            'id_pem' => null,
            'n_presentasi' => 85,
            'n_makalah' => 88,
            'n_prestasi' => 90,
            'n_supervisi' => 87,
        ]);

        Penilaian::create([
            'nim' => $mahasiswa->nim,
            'nidn' => null,
            'id_pem' => $mentor->id_pem,
            'n_presentasi' => 86,
            'n_makalah' => 85,
            'n_prestasi' => 92,
            'n_supervisi' => 88,
        ]);
    }
}