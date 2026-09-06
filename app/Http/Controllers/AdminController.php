<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\PembimbingIndustri;
use App\Models\ProgramStudi;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index() {
        return view('admin.dashboard');
    }

    public function users() {
        $users = User::with(['mahasiswa.programStudi', 'mahasiswa.pembimbingIndustri', 'dosen', 'pembimbingIndustri'])->get();
        
        $mappedUsers = $users->map(function ($user) {
            $identifier = null;
            $prodi = null;
            $kelas = null;
            $pt = null;
            
            if ($user->role === 'mahasiswa' && $user->mahasiswa) {
                $identifier = 'NIM: ' . $user->mahasiswa->nim;
                // Asumsi nama prodi seperti "Teknik Informatika" diubah jadi singkatan TRIN, dsb.
                // Tapi kita bisa mengirimkan nama aslinya atau ID-nya. 
                // Di frontend ada TRIN, TRO, TRMO. Kita cocokkan:
                $prodiName = $user->mahasiswa->programStudi->nama_prodi ?? '';
                if (stripos($prodiName, 'Mekatronika') !== false) {
                    $prodi = 'TRMO';
                } elseif (stripos($prodiName, 'Manufaktur') !== false && stripos($prodiName, 'Perancangan') !== false) {
                    $prodi = 'TRIN'; // Placeholder untuk TRIN/TRPM di frontend dummy
                } else {
                    $prodi = 'TRO'; // Placeholder lainnya
                }
                $kelas = $user->mahasiswa->kelas;
                $pt = $user->mahasiswa->pembimbingIndustri->perusahaan ?? null;
            } elseif ($user->role === 'dosen' && $user->dosen) {
                $identifier = 'NIP: ' . $user->dosen->nidn;
            } elseif ($user->role === 'mentor' && $user->pembimbingIndustri) {
                $pt = $user->pembimbingIndustri->perusahaan;
            }

            return [
                'id' => $user->id,
                'role' => $user->role,
                'name' => $user->name,
                'identifier' => $identifier,
                'prodi' => $prodi,
                'kelas' => $kelas,
                'email' => $user->email,
                'pt' => $pt
            ];
        });

        // Ambil daftar PT dari tabel pembimbing_industris unik
        $companies = PembimbingIndustri::pluck('perusahaan')->unique()->values()->all();

        return view('admin.users', [
            'mappedUsers' => json_encode($mappedUsers),
            'companies' => json_encode($companies)
        ]);
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'role' => 'required|in:admin,mahasiswa,dosen,mentor',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            // fields specific to roles
        ]);

        DB::beginTransaction();
        try {
            // 1. Create User
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make('password123'), // Default password
                'role' => $request->role,
            ]);

            // 2. Create Profile based on Role
            if ($request->role === 'mahasiswa') {
                // Find prodi
                $prodiMap = [
                    'TRIN' => 'Teknik Rekayasa Perancangan Manufaktur',
                    'TRO' => 'Teknik Manufaktur',
                    'TRMO' => 'Teknik Mekatronika',
                ];
                $namaProdi = $prodiMap[$request->prodi] ?? 'Teknik Manufaktur';
                $prodi = ProgramStudi::firstOrCreate(['nama_prodi' => $namaProdi]);

                // Create Mahasiswa
                Mahasiswa::create([
                    'nim' => $request->identifier,
                    'user_id' => $user->id,
                    'nama_mhs' => $request->name,
                    'kelas' => $request->kelas,
                    'id_prodi' => $prodi->id_prodi,
                    // nidn dan id_pem dibiarkan null dulu
                ]);
            } elseif ($request->role === 'dosen') {
                Dosen::create([
                    'nidn' => $request->identifier,
                    'user_id' => $user->id,
                    'nama_dosen' => $request->name,
                ]);
            } elseif ($request->role === 'mentor') {
                PembimbingIndustri::create([
                    'user_id' => $user->id,
                    'nama_pem' => $request->name,
                    'perusahaan' => $request->pt,
                ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'User berhasil ditambahkan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal menambahkan user: ' . $e->getMessage()], 500);
        }
    }

    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        
        // Since migrations have cascadeOnDelete for user_id (usually), 
        // deleting user might automatically delete profiles, but to be safe:
        if ($user->role === 'mahasiswa') {
            Mahasiswa::where('user_id', $user->id)->delete();
        } elseif ($user->role === 'dosen') {
            Dosen::where('user_id', $user->id)->delete();
        } elseif ($user->role === 'mentor') {
            PembimbingIndustri::where('user_id', $user->id)->delete();
        }
        
        $user->delete();

        return response()->json(['success' => true, 'message' => 'User berhasil dihapus']);
    }
}