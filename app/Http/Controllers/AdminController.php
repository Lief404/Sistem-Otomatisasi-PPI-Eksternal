<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class AdminController extends Controller
{
    public function index() {
        return view('admin.dashboard');
    }

    public function users() {
        // Mengambil data asli dari Database menggunakan JOIN yang sesuai dengan kolom Migration/ERD
        $dbUsers = DB::table('users')
            ->leftJoin('mahasiswas', 'users.id', '=', 'mahasiswas.user_id')
            ->leftJoin('dosens', 'users.id', '=', 'dosens.user_id')
            ->leftJoin('mentors', 'users.id', '=', 'mentors.user_id')
            ->select(
                'users.id', 'users.role', 'users.name', 'users.username', 'users.email',
                'mahasiswas.nim', 'mahasiswas.prodi', 'mahasiswas.kelas',
                'dosens.nidn', // Perbaikan: Sesuai ERD menggunakan nidn
                'mentors.perusahaan as pt_mentor' // Perbaikan: Sesuai ERD menggunakan perusahaan
            )
            ->get()
            ->map(function($user) {
                $identifier = null;
                $pt = null;
                
                if($user->role === 'mahasiswa') { 
                    $identifier = 'NIM: ' . $user->nim; 
                    $pt = 'Belum Ditetapkan'; 
                } elseif($user->role === 'dosen') { 
                    $identifier = 'NIP/NIDN: ' . $user->nidn; // Perbaikan mapping
                } elseif($user->role === 'mentor') { 
                    $pt = $user->pt_mentor; 
                    $identifier = '-';
                }

                return [
                    'id' => $user->id,
                    'role' => $user->role,
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'identifier' => $identifier,
                    'prodi' => $user->prodi,
                    'kelas' => $user->kelas,
                    'pt' => $pt
                ];
            });

        return view('admin.users', ['dbUsers' => $dbUsers]);
    }

    // WADAH INPUT MANUAL KE DB
    public function storeUser(Request $request)
    {
        $role = $request->role;
        $username = '';
        $password = '';

        if ($role === 'mahasiswa') {
            $username = $request->nim;
            $password = Hash::make($request->tgl_lahir);
        } elseif ($role === 'dosen') {
            $username = $request->nip; // Form UI mengirim nip
            $password = Hash::make($request->tgl_lahir);
        } elseif ($role === 'mentor') {
            $username = $request->pt;
            $password = Hash::make($request->pt);
        }

        // 1. Insert ke tabel Users
        $user = User::create([
            'name'     => $request->name,
            'username' => $username,
            'email'    => $username . ($role==='mahasiswa' ? '@mhs.polman' : ($role==='dosen' ? '@dosen.polman' : '@industri.id')),
            'password' => $password,
            'role'     => $role,
        ]);

        // 2. Insert ke tabel profil masing-masing sesuai ERD
        if ($role === 'mahasiswa') {
            DB::table('mahasiswas')->insert([
                'user_id' => $user->id,
                'nim' => $request->nim,
                'tgl_lahir' => $request->tgl_lahir, // Jika tidak ada di DB, hapus baris ini
                'prodi' => $request->prodi,
                'kelas' => $request->kelas,
            ]);
        } elseif ($role === 'dosen') {
            DB::table('dosens')->insert([
                'user_id' => $user->id,
                'nidn' => $request->nip, // Perbaikan: Simpan sebagai nidn
            ]);
        } elseif ($role === 'mentor') {
            DB::table('mentors')->insert([
                'user_id' => $user->id,
                'perusahaan' => $request->pt, // Perbaikan: Simpan sebagai perusahaan
            ]);
        }

        return back()->with('success', 'Akun berhasil ditambahkan ke Database!');
    }

    // WADAH IMPORT EXCEL (CSV, XLS, XLSX) KE DB
    public function importExcel(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:csv,txt,xlsx,xls' 
        ]);

        $file = $request->file('file_excel');
        
        $spreadsheet = IOFactory::load($file->getPathname());
        $rows = $spreadsheet->getActiveSheet()->toArray();

        $isHeader = true;
        foreach ($rows as $row) {
            if ($isHeader) {
                $isHeader = false; 
                continue;
            }

            // Pastikan baris tidak kosong (minimal nama dan NIM ada)
            if (empty($row[0]) || empty($row[1])) continue;

            // Cek apakah user sudah ada berdasarkan NIM (username)
            $exists = User::where('username', $row[1])->first();
            if($exists) continue; 

            // Format tanggal lahir untuk password (kolom index 2)
            $tglLahir = $row[2] ?? '2000-01-01';

            // 1. Buat User Mahasiswa
            $user = User::create([
                'name'     => $row[0],
                'username' => $row[1], // NIM
                'email'    => $row[5] ?? ($row[1] . '@mhs.polman'),
                'password' => Hash::make($tglLahir), // Password dari Tgl Lahir
                'role'     => 'mahasiswa',
            ]);

            // 2. Buat Profil Mahasiswa (Tanpa tgl_lahir untuk menghindari error kolom)
            DB::table('mahasiswas')->insert([
                'user_id'   => $user->id,
                'nim'       => $row[1],
                'prodi'     => $row[3],
                'kelas'     => $row[4],
            ]);
        }

        return back()->with('success', 'Data Mahasiswa dari Excel berhasil di-import ke Database!');
    }
}