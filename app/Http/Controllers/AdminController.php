<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\PembimbingIndustri;
use App\Models\ProgramStudi;
use App\Models\Perusahaan;
use App\Models\MataKuliah;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\JadwalMonitoring;

class AdminController extends Controller
{
    public function index() {
        $dosens = Dosen::all();
        $perusahaans = Perusahaan::all();
        $prodis = ProgramStudi::with('mataKuliahs')->get();
        $mentors = PembimbingIndustri::all();
        $mahasiswas = Mahasiswa::with(['programStudi', 'pembimbingIndustri'])->get();
        $jadwals = JadwalMonitoring::all();
        return view('admin.dashboard', compact('dosens', 'perusahaans', 'prodis', 'mentors', 'mahasiswas', 'jadwals'));
    }

    public function storeMatkul(Request $request)
    {
        $request->validate([
            'id_prodi' => 'required|exists:program_studis,id_prodi',
            'kd_mat' => 'required|string',
            'nama_komp' => 'required|string',
            'jam_min' => 'required|numeric',
        ]);

        MataKuliah::create($request->all());

        return response()->json(['message' => 'Mata Kuliah berhasil ditambahkan']);
    }

    public function updateMatkul(Request $request, $id)
    {
        $request->validate([
            'kd_mat' => 'required|string',
            'nama_komp' => 'required|string',
            'jam_min' => 'required|numeric',
        ]);

        $matkul = MataKuliah::findOrFail($id);
        $matkul->update($request->only(['kd_mat', 'nama_komp', 'jam_min']));

        return response()->json(['message' => 'Mata Kuliah berhasil diperbarui']);
    }

    public function deleteMatkul($id)
    {
        $matkul = MataKuliah::findOrFail($id);
        $matkul->delete();

        return response()->json(['message' => 'Mata Kuliah berhasil dihapus']);
    }

    public function users() {
        $users = User::with(['mahasiswa.programStudi', 'mahasiswa.dosen', 'mahasiswa.pembimbingIndustri', 'dosen', 'pembimbingIndustri'])->get();
        
        $mappedUsers = $users->map(function ($user) {
            $identifier = null;
            $prodi = null;
            $kelas = null;
            $pt = null;
            $nim = null;
            $nidn = null;
            $id_pem = null;
            $dosen_name = null;
            $mentor_name = null;
            
            if ($user->role === 'mahasiswa' && $user->mahasiswa) {
                $nim = $user->mahasiswa->nim;
                $identifier = 'NIM: ' . $nim;
                $prodiName = $user->mahasiswa->programStudi->nama_prodi ?? '';
                if (in_array($prodiName, ['TRIN', 'TRO', 'TRMO'])) {
                    $prodi = $prodiName;
                } elseif (stripos($prodiName, 'Mekatronika') !== false) {
                    $prodi = 'TRMO';
                } elseif (stripos($prodiName, 'Manufaktur') !== false && stripos($prodiName, 'Perancangan') !== false) {
                    $prodi = 'TRIN';
                } else {
                    $prodi = 'TRO';
                }
                $kelas = $user->mahasiswa->kelas;
                $nidn = $user->mahasiswa->nidn;
                $id_pem = $user->mahasiswa->id_pem;
                $dosen_name = $user->mahasiswa->dosen->nama_dosen ?? null;
                $mentor_name = $user->mahasiswa->pembimbingIndustri->nama_pem ?? null;
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
                'nim' => $nim,
                'prodi' => $prodi,
                'kelas' => $kelas,
                'email' => $user->email,
                'plain_password' => $user->plain_password,
                'pt' => $pt,
                'nidn' => $nidn,
                'id_pem' => $id_pem,
                'dosen_name' => $dosen_name,
                'mentor_name' => $mentor_name,
            ];
        });

        // Ambil data perusahaan dari tabel perusahaans & pembimbing_industris
        $perusahaanList = Perusahaan::all();
        $companiesFromDb = $perusahaanList->pluck('nama_perusahaan')->toArray();
        $companiesFromMentor = PembimbingIndustri::pluck('perusahaan')->unique()->toArray();
        $companies = array_values(array_unique(array_filter(array_merge($companiesFromDb, $companiesFromMentor))));

        // Ambil data Dosen & Mentor untuk pilihan pembimbing
        $dosens = Dosen::all(['nidn', 'nama_dosen']);
        $mentors = PembimbingIndustri::all(['id_pem', 'nama_pem', 'perusahaan']);

        return view('admin.users', [
            'mappedUsers' => json_encode($mappedUsers),
            'companies' => json_encode($companies),
            'perusahaans' => json_encode($perusahaanList),
            'dosens' => json_encode($dosens),
            'mentors' => json_encode($mentors),
        ]);
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'role' => 'required|in:admin,mahasiswa,dosen,mentor',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
        ]);

        DB::beginTransaction();
        try {
            // 1. Create User
            $plainPass = $request->plain_password ?: '123';
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($plainPass),
                'plain_password' => $plainPass,
                'role' => $request->role,
            ]);

            // 2. Create Profile based on Role
            if ($request->role === 'mahasiswa') {
                $namaProdi = $request->prodi ?? 'TRO';
                $prodi = ProgramStudi::firstOrCreate(['nama_prodi' => $namaProdi]);

                if ($request->pt) {
                    Perusahaan::firstOrCreate(['nama_perusahaan' => $request->pt]);
                }

                Mahasiswa::create([
                    'nim' => $request->identifier,
                    'user_id' => $user->id,
                    'nama_mhs' => $request->name,
                    'kelas' => $request->kelas,
                    'id_prodi' => $prodi->id_prodi,
                    'nidn' => $request->nidn ?: null,
                    'id_pem' => $request->id_pem ?: null,
                ]);
            } elseif ($request->role === 'dosen') {
                Dosen::create([
                    'nidn' => $request->identifier,
                    'user_id' => $user->id,
                    'nama_dosen' => $request->name,
                ]);
            } elseif ($request->role === 'mentor') {
                if ($request->pt) {
                    Perusahaan::firstOrCreate(['nama_perusahaan' => $request->pt]);
                }

                PembimbingIndustri::create([
                    'user_id' => $user->id,
                    'nama_pem' => $request->name,
                    'perusahaan' => $request->pt,
                ]);
            } elseif ($request->role === 'kaprodi') {
                // Kaprodi just needs a user account for now
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Akun ' . ucfirst($request->role) . ' berhasil ditambahkan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal menambahkan akun: ' . $e->getMessage()], 500);
        }
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$id,
        ]);

        DB::beginTransaction();
        try {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            if ($user->role === 'mahasiswa') {
                $mahasiswa = Mahasiswa::where('user_id', $user->id)->first();
                if ($mahasiswa) {
                    $namaProdi = $request->prodi ?? 'TRO';
                    $prodi = ProgramStudi::firstOrCreate(['nama_prodi' => $namaProdi]);

                    if ($request->pt) {
                        Perusahaan::firstOrCreate(['nama_perusahaan' => $request->pt]);
                    }

                    $mahasiswa->update([
                        'nim' => $request->identifier,
                        'nama_mhs' => $request->name,
                        'kelas' => $request->kelas,
                        'id_prodi' => $prodi->id_prodi,
                    ]);
                }
            } elseif ($user->role === 'dosen') {
                $dosen = Dosen::where('user_id', $user->id)->first();
                if ($dosen) {
                    $dosen->update([
                        'nidn' => $request->identifier,
                        'nama_dosen' => $request->name,
                    ]);
                }
            } elseif ($user->role === 'mentor') {
                if ($request->pt) {
                    Perusahaan::firstOrCreate(['nama_perusahaan' => $request->pt]);
                }

                $mentor = PembimbingIndustri::where('user_id', $user->id)->first();
                if ($mentor) {
                    $mentor->update([
                        'nama_pem' => $request->name,
                        'perusahaan' => $request->pt,
                    ]);
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Akun berhasil diperbarui']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui akun: ' . $e->getMessage()], 500);
        }
    }

    public function changePassword(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'password' => 'required|string|min:1',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
            'plain_password' => $request->password,
        ]);

        return response()->json(['success' => true, 'message' => 'Password berhasil diubah']);
    }

    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        
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

    public function storePerusahaan(Request $request)
    {
        $request->validate([
            'nama_perusahaan' => 'required|string|max:255|unique:perusahaans,nama_perusahaan',
            'alamat' => 'nullable|string',
            'kontak' => 'nullable|string|max:255',
        ]);

        try {
            $perusahaan = Perusahaan::create([
                'nama_perusahaan' => $request->nama_perusahaan,
                'alamat' => $request->alamat,
                'kontak' => $request->kontak,
            ]);

            return response()->json(['success' => true, 'message' => 'Perusahaan berhasil ditambahkan', 'data' => $perusahaan]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menambahkan perusahaan: ' . $e->getMessage()], 500);
        }
    }

    public function destroyPerusahaan($id)
    {
        try {
            $perusahaan = Perusahaan::findOrFail($id);
            $perusahaan->delete();

            return response()->json(['success' => true, 'message' => 'Perusahaan berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus perusahaan: ' . $e->getMessage()], 500);
        }
    }

    public function assignPembimbing(Request $request)
    {
        $request->validate([
            'nim' => 'required|exists:mahasiswas,nim',
            'nidn' => 'nullable|exists:dosens,nidn',
            'id_pem' => 'nullable|exists:pembimbing_industris,id_pem',
        ]);

        try {
            $mahasiswa = Mahasiswa::where('nim', $request->nim)->firstOrFail();
            $mahasiswa->nidn = $request->nidn ?: null;
            $mahasiswa->id_pem = $request->id_pem ?: null;
            $mahasiswa->save();

            $dosen = $mahasiswa->dosen;
            $mentor = $mahasiswa->pembimbingIndustri;

            return response()->json([
                'success' => true,
                'message' => 'Pembimbing berhasil diperbarui',
                'dosen_name' => $dosen ? $dosen->nama_dosen : null,
                'mentor_name' => $mentor ? $mentor->nama_pem : null,
                'pt' => $mentor ? $mentor->perusahaan : null,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui pembimbing: ' . $e->getMessage()], 500);
        }
    }

    public function storeJadwal(Request $request)
    {
        $request->validate([
            'nidn' => 'required|exists:dosens,nidn',
            'perusahaan' => 'required|exists:perusahaans,nama_perusahaan',
            'tanggal' => 'required|date',
        ]);

        try {
            $jadwal = JadwalMonitoring::create([
                'nidn' => $request->nidn,
                'perusahaan' => $request->perusahaan,
                'tanggal' => $request->tanggal,
            ]);

            return response()->json(['success' => true, 'message' => 'Jadwal berhasil ditambahkan', 'data' => $jadwal]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menambahkan jadwal: ' . $e->getMessage()], 500);
        }
    }

    public function destroyJadwal($id)
    {
        try {
            $jadwal = JadwalMonitoring::findOrFail($id);
            $jadwal->delete();
            return response()->json(['success' => true, 'message' => 'Jadwal berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus jadwal: ' . $e->getMessage()], 500);
        }
    }
}