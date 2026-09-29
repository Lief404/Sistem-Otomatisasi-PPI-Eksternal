<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Logbook;
use App\Models\LogbookFoto;
use App\Models\Mahasiswa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
// Import tambahan untuk fitur Approval QR
use App\Models\Approval;
use App\Models\User;
use Illuminate\Support\Str;
use App\Notifications\PengajuanTTDNotification;

class MahasiswaController extends Controller
{
    public function index() 
    {
        $user = Auth::user();
        // Eager load dosen dan pembimbingIndustri untuk keamanan data relasi
        $mahasiswa = Mahasiswa::with(['dosen', 'pembimbingIndustri'])->where('user_id', $user->id)->first();
        
        $logbooks = collect();
        $fotosByDate = [];
        $approval = null; // TAMBAHAN: Inisialisasi variabel status TTD

        if ($mahasiswa) {
            // TAMBAHAN: Ambil data pengajuan TTD terakhir milik mahasiswa
            $approval = Approval::where('mahasiswa_nim', $mahasiswa->nim)
                                ->orderBy('created_at', 'desc')
                                ->first();

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

        // TAMBAHAN: Masukkan 'approval' ke dalam compact
        return view('mahasiswa.dashboard', compact('mahasiswa', 'logbooks', 'fotosByDate', 'perusahaan', 'sarans', 'saranDariMentor', 'parameterSaran', 'approval'));
    }

    public function storeLogbook(Request $request)
    {
        $request->validate([
            'tanggal'                   => 'required|date',
            'status'                    => 'required|string',
            'minggu_ke'                 => 'nullable|integer',
            'kegiatan'                  => 'nullable|string',
            'activities.*.kode'       => 'nullable|string',
            'activities.*.waktu'      => 'nullable|numeric',
            'activities.*.kegiatan'   => 'nullable|string',
            'activities.*.jamMulai'   => 'nullable|string',
            'activities.*.jamSelesai' => 'nullable|string',
            'fotos'                     => 'nullable|array',
            'fotos.*'                   => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
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
        $approvals = collect(); // TAMBAHAN: Inisialisasi awal

        if ($mahasiswa) {
            // Penilaian dari Dosen
            $penilaianDosen = \App\Models\Penilaian::where('nim', $mahasiswa->nim)
                ->whereNotNull('nidn')
                ->first();

            // Penilaian dari Mentor
            $penilaianMentor = \App\Models\Penilaian::where('nim', $mahasiswa->nim)
                ->whereNotNull('id_pem')
                ->first();

            // Logbook
            $logbooks = \App\Models\Logbook::with('mataKuliah')
                ->where('nim', $mahasiswa->nim)
                ->orderBy('tanggal')
                ->get();
                
            $disiplin = \App\Models\DisiplinMahasiswa::where('nim', $mahasiswa->nim)->first();
            $kuisioner = \App\Models\KuisionerMentor::where('nim', $mahasiswa->nim)->first();

            // TAMBAHAN: Ambil data pengajuan TTD, jadikan key (index) berdasarkan jenis_form
            $approvals = \App\Models\Approval::where('mahasiswa_nim', $mahasiswa->nim)
                ->get()
                ->keyBy('jenis_form');
        }

        $parameters = \App\Models\ParameterPenilaian::all();

        // TAMBAHAN: Masukkan variabel 'approvals' ke compact
        return view('mahasiswa.nilai', compact('mahasiswa', 'penilaianDosen', 'penilaianMentor', 'logbooks', 'disiplin', 'kuisioner', 'parameters', 'approvals'));
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
        $approvals = collect(); // TAMBAHAN

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

            // TAMBAHAN: Ambil data TTD
            $approvals = \App\Models\Approval::where('mahasiswa_nim', $mahasiswa->nim)
                ->get()
                ->keyBy('jenis_form');
        }

        $parameters = \App\Models\ParameterPenilaian::all();

        // TAMBAHAN: Masukkan variabel 'approvals' ke compact
        return view('mahasiswa.nilai', compact('mahasiswa', 'penilaianDosen', 'penilaianMentor', 'logbooks', 'disiplin', 'kuisioner', 'parameters', 'approvals'));
    }

    public function apiNilaiLogbook()
    {
        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->firstOrFail();

        $nilaiLogbook = Logbook::query()
            ->where('nim', $mahasiswa->nim)
            ->whereNotNull('nilai')
            ->selectRaw('minggu_ke, AVG(nilai) as nilai')
            ->groupBy('minggu_ke')
            ->orderBy('minggu_ke')
            ->get()
            ->map(function ($logbook) {
                $nilai = round((float) $logbook->nilai, 2);
                $predikat = match (true) {
                    $nilai >= 85 => 'A',
                    $nilai >= 80 => 'AB',
                    $nilai >= 70 => 'B',
                    $nilai >= 65 => 'BC',
                    $nilai >= 55 => 'C',
                    $nilai >= 40 => 'D',
                    default => 'E',
                };

                return [
                    'minggu' => (int) $logbook->minggu_ke,
                    'nilai' => $nilai,
                    'predikat' => $predikat,
                ];
            });

        return response()->json([
            'logbook' => $nilaiLogbook,
            'updated_at' => now()->toIso8601String(),
        ]);
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

    // batalQR
    // batalQR
    public function batalQR(Request $request)
    {
        // Cari data pengajuan berdasarkan TOKEN (hapus pembatasan status 'pending' agar status 'accepted' juga bisa di-reset)
        $approval = \App\Models\Approval::where('token_verifikasi', $request->token)
            ->first();

        if ($approval) {
            $approval->delete(); // Hapus dari database
            return response()->json(['status' => 'success']);
        }

        return response()->json(['status' => 'error', 'message' => 'Pengajuan tidak ditemukan.']);
    }

    // ==========================================
    // FUNGSI MENGAJUKAN TTD QR (DIPERBAIKI)
    // ==========================================
    public function ajukanQR(Request $request)
    {
        $request->validate([
            'jenis_form' => 'required|string',
        ]);

        // Muat data mahasiswa beserta relasi pembimbingnya
        $mahasiswa = Mahasiswa::with(['dosen', 'pembimbingIndustri'])
            ->where('user_id', Auth::id())
            ->first();

        if (!$mahasiswa) {
            return response()->json(['status' => 'error', 'success' => false, 'message' => 'Data mahasiswa tidak ditemukan.'], 404);
        }

        $nim = $mahasiswa->nim;
        $jenisForm = $request->jenis_form;

        // Tentukan pihak penilai
        $pihakPenilai = in_array($jenisForm, ['presentasi', 'makalah']) ? 'dosen' : 'mentor';

        // Ambil ID User dari Dosen atau Mentor terkait untuk kolom penilai_id
        if ($pihakPenilai === 'dosen') {
            $penilaiId = $mahasiswa->dosen->user_id ?? null;
        } else {
            $penilaiId = $mahasiswa->pembimbingIndustri->user_id ?? null;
        }

        if (!$penilaiId) {
            return response()->json(['status' => 'error', 'success' => false, 'message' => 'Data Pembimbing/Mentor belum diatur atau akun User penilai tidak ditemukan.'], 400);
        }

        // Cek apakah sudah ada pengajuan dengan status 'pending' atau 'accepted'
        $existing = Approval::where('mahasiswa_nim', $nim)
            ->where('jenis_form', $jenisForm)
            ->whereIn('status', ['pending', 'accepted'])
            ->first();

        if ($existing) {
            return response()->json(['status' => 'error', 'success' => false, 'message' => 'Pengajuan sedang diproses atau sudah disetujui.'], 400);
        }

        // Hapus sisa data riwayat pengajuan form ini yang berstatus 'rejected' (jika ada) agar bersih murni
        Approval::where('mahasiswa_nim', $nim)
            ->where('jenis_form', $jenisForm)
            ->delete();

        // Buat token unik untuk URL approval (Go to page)
        $token = Str::random(40);

        // Buat data approval baru
        $approval = Approval::create([
            'mahasiswa_nim' => $nim,
            'jenis_form' => $jenisForm,
            'pihak_penilai' => $pihakPenilai,
            'penilai_id' => $penilaiId,
            'status' => 'pending',
            'token_verifikasi' => $token,
            'alasan_reject' => null,
            'qr_code_path' => null 
        ]);

        // Kirim Notifikasi Baru ke bel (lonceng) User Dosen/Mentor
        $penilai = User::find($penilaiId);
        if ($penilai && class_exists(PengajuanTTDNotification::class)) {
            $penilai->notify(new PengajuanTTDNotification($approval));
        }

        $namaPihak = ucfirst($pihakPenilai); // Menjadi 'Dosen' atau 'Mentor'
        return response()->json([
            'status' => 'success', 
            'success' => true, 
            'message' => 'Pengajuan TTD berhasil dikirim ke ' . $namaPihak . '.'
        ]);
    }
}