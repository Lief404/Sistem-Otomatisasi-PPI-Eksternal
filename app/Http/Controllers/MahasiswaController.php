<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Logbook;
use App\Models\Mahasiswa;
use Illuminate\Support\Facades\Auth;

class MahasiswaController extends Controller
{
    public function index() 
    {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::where('user_id', $user->id)->first();
        $logbooks = collect();
        if ($mahasiswa) {
            $logbooks = Logbook::where('nim', $mahasiswa->nim)
                ->orderBy('tanggal', 'asc')
                ->get();
        }

        return view('mahasiswa.dashboard', compact('mahasiswa', 'logbooks'));
    }
    
    public function storeLogbook(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'status' => 'required|string',
            'minggu_ke' => 'nullable|integer',
            'activities' => 'nullable|array',
            'activities.*.kode' => 'nullable|string',
            'activities.*.waktu' => 'nullable|numeric',
            'activities.*.kegiatan' => 'nullable|string',
            'activities.*.jamMulai' => 'nullable|string',
            'activities.*.jamSelesai' => 'nullable|string',
            'kegiatan' => 'nullable|string'
        ]);

        $mahasiswa = Mahasiswa::where('user_id', Auth::id())->first();
        if (!$mahasiswa) {
            return response()->json(['message' => 'Data mahasiswa tidak ditemukan.'], 404);
        }

        // Hapus data logbook di tanggal ini untuk update/replace
        Logbook::where('nim', $mahasiswa->nim)
               ->where('tanggal', $request->tanggal)
               ->delete();

        if ($request->status === 'Kerja' && $request->has('activities') && count($request->activities) > 0) {
            foreach ($request->activities as $act) {
                if (isset($act['waktu']) && $act['waktu'] > 0) {
                    Logbook::create([
                        'nim' => $mahasiswa->nim,
                        'kd_mat' => $act['kode'] ?? 'SUP',
                        'tanggal' => $request->tanggal,
                        'durasi_mnt' => $act['waktu'],
                        'kegiatan' => $act['kegiatan'] ?? '',
                        'status' => 'Kerja',
                        'jam_mulai' => $act['jamMulai'] ?? null,
                        'jam_selesai' => $act['jamSelesai'] ?? null,
                        'minggu_ke' => $request->minggu_ke ?? null,
                    ]);
                }
            }
        } else {
            // Jika Libur / Izin / Sakit, simpan 1 record logbook dengan status
            Logbook::create([
                'nim' => $mahasiswa->nim,
                'kd_mat' => 'SUP',
                'tanggal' => $request->tanggal,
                'durasi_mnt' => 0,
                'kegiatan' => $request->kegiatan ?? '',
                'status' => $request->status,
                'jam_mulai' => null,
                'jam_selesai' => null,
                'minggu_ke' => $request->minggu_ke ?? null,
            ]);
        }

        return response()->json(['message' => 'Berhasil menyimpan logbook.']);
    }

    public function nilai() {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::with(['programStudi', 'dosen', 'pembimbingIndustri'])
            ->where('user_id', $user->id)
            ->first();

        $penilaianDosen = null;
        $penilaianMentor = null;
        $logbooks = collect();

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
            $logbooks = \App\Models\Logbook::where('nim', $mahasiswa->nim)
                ->orderBy('tanggal')
                ->get();
        }

        return view('mahasiswa.nilai', compact('mahasiswa', 'penilaianDosen', 'penilaianMentor', 'logbooks'));
    }
}