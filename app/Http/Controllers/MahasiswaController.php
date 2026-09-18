<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Logbook;
use App\Models\LogbookFoto;
use App\Models\Mahasiswa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MahasiswaController extends Controller
{
    public function index() 
    {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::where('user_id', $user->id)->first();
        $logbooks = collect();
        $fotosByDate = [];

        if ($mahasiswa) {
            $logbooks = Logbook::where('nim', $mahasiswa->nim)
                ->orderBy('tanggal', 'asc')
                ->get();

            // Ambil semua foto, group by tanggal
            $fotos = LogbookFoto::where('nim', $mahasiswa->nim)->get();
            foreach ($fotos as $foto) {
                $tanggal = $foto->tanggal instanceof \Carbon\Carbon
                    ? $foto->tanggal->format('Y-m-d')
                    : (string) $foto->tanggal;
                if (!isset($fotosByDate[$tanggal])) {
                    $fotosByDate[$tanggal] = [];
                }
                $fotosByDate[$tanggal][] = [
                    'id'       => $foto->id,
                    'url'      => '/storage/' . $foto->foto_path,
                    'path'     => $foto->foto_path,
                    'filename' => basename($foto->foto_path),
                ];
            }
        }

        $perusahaan = null;
        $sarans = collect();
        $saranDariMentor = null;
        $parameterSaran = \App\Models\ParameterPenilaian::where('jenis', 'saran_mahasiswa')->get();

        if ($mahasiswa && $mahasiswa->pembimbingIndustri) {
            $perusahaan = \App\Models\Perusahaan::where('nama_perusahaan', $mahasiswa->pembimbingIndustri->perusahaan)->first();
            $sarans = \App\Models\SaranPerusahaan::where('nim', $mahasiswa->nim)->orderBy('tanggal', 'desc')->get();
            $saranDariMentor = \App\Models\SaranMahasiswa::where('nim', $mahasiswa->nim)->orderBy('tanggal', 'desc')->first();
        }

        return view('mahasiswa.dashboard', compact('mahasiswa', 'logbooks', 'fotosByDate', 'perusahaan', 'sarans', 'saranDariMentor', 'parameterSaran'));
    }

    public function storeLogbook(Request $request)
    {
        $request->validate([
            'tanggal'                 => 'required|date',
            'status'                  => 'required|string',
            'minggu_ke'               => 'nullable|integer',
            'kegiatan'                => 'nullable|string',
            'activities.*.kode'       => 'nullable|string',
            'activities.*.waktu'      => 'nullable|numeric',
            'activities.*.kegiatan'   => 'nullable|string',
            'activities.*.jamMulai'   => 'nullable|string',
            'activities.*.jamSelesai' => 'nullable|string',
            'fotos'                   => 'nullable|array',
            'fotos.*'                 => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();
        if (!$mahasiswa) {
            return response()->json(['message' => 'Data mahasiswa tidak ditemukan.'], 404);
        }

        // Hapus data logbook di tanggal ini untuk update/replace
        // Pertahankan nilai dan catatan mentor jika sudah dinilai sebelumnya
        $existingLogbook = Logbook::where('nim', $mahasiswa->nim)
               ->where('tanggal', $request->tanggal)
               ->first();
        $nilai = $existingLogbook->nilai ?? null;
        $catatan_mentor = $existingLogbook->catatan_mentor ?? '';
        $finalStatus = $nilai !== null ? 'Dinilai' : $request->status;

        Logbook::where('nim', $mahasiswa->nim)
               ->where('tanggal', $request->tanggal)
               ->delete();

        // Parse activities dari JSON string (dikirim sebagai FormData)
        $activities = [];
        if ($request->has('activities_json')) {
            $activities = json_decode($request->input('activities_json'), true) ?? [];
        }

        if ($request->status === 'Kerja' && count($activities) > 0) {
            foreach ($activities as $act) {
                if (isset($act['waktu']) && $act['waktu'] > 0) {
                    Logbook::create([
                        'nim'         => $mahasiswa->nim,
                        'kd_mat'      => $act['kode'] ?? 'SUP',
                        'tanggal'     => $request->tanggal,
                        'durasi_mnt'  => $act['waktu'],
                        'kegiatan'    => $act['kegiatan'] ?? '',
                        'status'      => $nilai !== null ? 'Dinilai' : 'Kerja',
                        'jam_mulai'   => $act['jamMulai'] ?? null,
                        'jam_selesai' => $act['jamSelesai'] ?? null,
                        'minggu_ke'   => $request->minggu_ke ?? null,
                        'nilai'       => $nilai,
                        'catatan_mentor' => $catatan_mentor,
                    ]);
                }
            }
        } else {
            Logbook::create([
                'nim'         => $mahasiswa->nim,
                'kd_mat'      => 'SUP',
                'tanggal'     => $request->tanggal,
                'durasi_mnt'  => 0,
                'kegiatan'    => $request->kegiatan ?? '',
                'status'      => $finalStatus,
                'jam_mulai'   => null,
                'jam_selesai' => null,
                'minggu_ke'   => $request->minggu_ke ?? null,
                'nilai'       => $nilai,
                'catatan_mentor' => $catatan_mentor,
            ]);
        }

        // Simpan foto-foto baru yang diupload
        $newFotos = [];
        if ($request->hasFile('fotos')) {
            foreach ($request->file('fotos') as $file) {
                $path = $file->store('logbook_foto', 'public');
                $record = LogbookFoto::create([
                    'nim'       => $mahasiswa->nim,
                    'tanggal'   => $request->tanggal,
                    'foto_path' => $path,
                ]);
                $newFotos[] = [
                    'id'       => $record->id,
                    'url'      => '/storage/' . $path,
                    'path'     => $path,
                    'filename' => basename($path),
                ];
            }
        }

        return response()->json([
            'message'   => 'Berhasil menyimpan logbook.',
            'new_fotos' => $newFotos,
        ]);
    }

    /**
     * Hapus 1 foto berdasarkan ID.
     */
    public function deleteLogbookFoto(Request $request, $id)
    {
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();
        if (!$mahasiswa) {
            return response()->json(['message' => 'Data mahasiswa tidak ditemukan.'], 404);
        }

        $foto = LogbookFoto::where('id', $id)
                            ->where('nim', $mahasiswa->nim)
                            ->firstOrFail();

        if (Storage::disk('public')->exists($foto->foto_path)) {
            Storage::disk('public')->delete($foto->foto_path);
        }

        $foto->delete();

        return response()->json(['message' => 'Foto berhasil dihapus.']);
    }

    public function nilai() {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::with(['programStudi', 'dosen', 'pembimbingIndustri'])
            ->where('user_id', $user->id)
            ->first();

        $penilaianDosen = null;
        $penilaianMentor = null;
        $logbooks = collect();
        $disiplin = null;
        $kuisioner = null;

        if ($mahasiswa) {
            // Penilaian dari Dosen (nidn tidak null)
            $penilaianDosen = \App\Models\Penilaian::where('nim', $mahasiswa->nim)
                ->whereNotNull('nidn')
                ->first();

            // Penilaian dari Mentor (id_pem tidak null)
            $penilaianMentor = \App\Models\Penilaian::where('nim', $mahasiswa->nim)
                ->whereNotNull('id_pem')
                ->first();

            // Logbook yang sudah ada
            $logbooks = \App\Models\Logbook::with('mataKuliah')
                ->where('nim', $mahasiswa->nim)
                ->orderBy('tanggal')
                ->get();
                
            // Rincian Penilaian Mentor
            $disiplin = \App\Models\DisiplinMahasiswa::where('nim', $mahasiswa->nim)->first();
            $kuisioner = \App\Models\KuisionerMentor::where('nim', $mahasiswa->nim)->first();
        }

        $parameters = \App\Models\ParameterPenilaian::all();

        return view('mahasiswa.nilai', compact('mahasiswa', 'penilaianDosen', 'penilaianMentor', 'logbooks', 'disiplin', 'kuisioner', 'parameters'));
    }

    public function transkrip($nim) {
        $mahasiswa = Mahasiswa::with(['programStudi', 'dosen', 'pembimbingIndustri'])
            ->where('nim', $nim)
            ->firstOrFail();

        $penilaianDosen = null;
        $penilaianMentor = null;
        $logbooks = collect();
        $disiplin = null;
        $kuisioner = null;

        if ($mahasiswa) {
            $penilaianDosen = \App\Models\Penilaian::where('nim', $mahasiswa->nim)
                ->whereNotNull('nidn')
                ->first();

            $penilaianMentor = \App\Models\Penilaian::where('nim', $mahasiswa->nim)
                ->whereNotNull('id_pem')
                ->first();

            $logbooks = \App\Models\Logbook::with('mataKuliah')
                ->where('nim', $mahasiswa->nim)
                ->orderBy('tanggal')
                ->get();
                
            $disiplin = \App\Models\DisiplinMahasiswa::where('nim', $mahasiswa->nim)->first();
            $kuisioner = \App\Models\KuisionerMentor::where('nim', $mahasiswa->nim)->first();
        }

        $parameters = \App\Models\ParameterPenilaian::all();

        return view('mahasiswa.nilai', compact('mahasiswa', 'penilaianDosen', 'penilaianMentor', 'logbooks', 'disiplin', 'kuisioner', 'parameters'));
    }

    public function apiRekapanJam($nim) {
        $logbooks = \App\Models\Logbook::with('mataKuliah')
            ->where('nim', $nim)
            ->orderBy('tanggal')
            ->get();
            
        $totalJamKeseluruhan = round($logbooks->sum('durasi_mnt') / 60, 1);
        
        $rekapanJam = [];
        foreach($logbooks as $lb) {
            $matkulKode = $lb->kd_mat ?? 'Lainnya';
            $matkulNama = $lb->mataKuliah ? $lb->mataKuliah->nama_komp : $matkulKode;
            $minggu = $lb->minggu_ke;
            $durasiMnt = $lb->durasi_mnt;
            
            if (!isset($rekapanJam[$matkulKode])) {
                $rekapanJam[$matkulKode] = [
                    'kode' => $matkulKode,
                    'nama' => $matkulNama,
                    'total_mnt' => 0,
                    'mingguan_mnt' => array_fill(1, 20, 0)
                ];
            }
            
            if ($minggu >= 1 && $minggu <= 20) {
                $rekapanJam[$matkulKode]['mingguan_mnt'][$minggu] += $durasiMnt;
            }
            $rekapanJam[$matkulKode]['total_mnt'] += $durasiMnt;
        }
        
        foreach ($rekapanJam as $k => $v) {
            $rekapanJam[$k]['total'] = round($v['total_mnt'] / 60, 1);
            $rekapanJam[$k]['mingguan'] = [];
            for ($i = 1; $i <= 20; $i++) {
                $rekapanJam[$k]['mingguan'][$i] = round($v['mingguan_mnt'][$i] / 60, 1);
            }
            unset($rekapanJam[$k]['total_mnt']);
            unset($rekapanJam[$k]['mingguan_mnt']);
        }

        return response()->json([
            'totalJamKeseluruhan' => $totalJamKeseluruhan,
            'rekapanJam' => array_values($rekapanJam)
        ]);
    }

    public function storeSaran(Request $request)
    {
        $request->validate([
            'perusahaan' => 'required|string',
            'tanggal' => 'required|date',
            'saran' => 'required|string',
        ]);

        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();
        if (!$mahasiswa) {
            return response()->json(['message' => 'Data mahasiswa tidak ditemukan.'], 404);
        }

        // Cek apakah mahasiswa sudah pernah submit saran
        $existingSaran = \App\Models\SaranPerusahaan::where('nim', $mahasiswa->nim)
            ->where('perusahaan', $request->perusahaan)
            ->first();

        if ($existingSaran) {
            return response()->json(['message' => 'Anda sudah pernah memberikan saran untuk perusahaan ini.'], 400);
        }

        \App\Models\SaranPerusahaan::create([
            'nim' => $mahasiswa->nim,
            'perusahaan' => $request->perusahaan,
            'tanggal' => $request->tanggal,
            'saran' => $request->saran,
        ]);

        return response()->json(['message' => 'Saran dan masukan berhasil dikirim.']);
    }
}