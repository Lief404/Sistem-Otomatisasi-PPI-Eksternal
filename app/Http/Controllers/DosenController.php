<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\Penilaian;
use App\Models\Approval; 
use Illuminate\Support\Facades\Auth;

class DosenController extends Controller
{
    public function index() 
    {
        $user = Auth::user();
        $dosen = Dosen::where('user_id', $user->id)->first();
        
        $jadwals = collect();
        $notifikasiTTD = collect(); // Inisialisasi awal notifikasi

        if ($dosen) {
            // Hanya tampilkan mahasiswa yang sudah ditugaskan oleh admin (nidn sesuai)
            $mahasiswas = Mahasiswa::with(['programStudi', 'pembimbingIndustri', 'penilaians'])
                ->where('nidn', $dosen->nidn)
                ->get();
            
            // Ambil jadwal penugasan dari admin
            $jadwals = \App\Models\JadwalMonitoring::where('nidn', $dosen->nidn)
                ->orderBy('tanggal', 'asc')
                ->get();

            // TAMBAHAN: Ambil pengajuan TTD (Presentasi & Makalah) yang berelasi dengan mahasiswa bimbingan dosen ini
            $notifikasiTTD = Approval::with('mahasiswa')
                ->whereHas('mahasiswa', function($query) use ($dosen) {
                    $query->where('nidn', $dosen->nidn); 
                })
                ->whereIn('jenis_form', ['presentasi', 'makalah'])
                ->where('status', 'pending')
                ->get();
        } else {
            $mahasiswas = collect();
        }
        
        $parameters = \App\Models\ParameterPenilaian::all();

        // Mengirim notifikasiTTD ke view
        return view('dosen.dashboard', compact('dosen', 'mahasiswas', 'jadwals', 'parameters', 'notifikasiTTD'));
    }

    public function riwayatTtd()
    {
    // Mengambil data approval yang statusnya sudah BUKAN pending (sudah di-acc/reject)
    // Sesuaikan nama kolom mentor_id / dosen_id dengan database Anda
    $riwayat = Approval::with('mahasiswa') 
                ->where('penilai_id', auth()->user()->id)
                ->whereIn('status', ['accepted', 'rejected'])
                ->orderBy('updated_at', 'desc')
                ->paginate(15);

    return view('dosen.riwayat_ttd', compact('riwayat'));
    }

    public function storePenilaian(Request $request)
    {
        $request->validate([
            'nim' => 'required|string',
            'n_presentasi' => 'required|numeric',
            'n_makalah' => 'required|numeric',
            'detail_presentasi' => 'nullable|array',
            'detail_makalah' => 'nullable|array',
        ]);

        $user = Auth::user();
        $dosen = Dosen::where('user_id', $user->id)->first();
        if (!$dosen) {
            return response()->json(['message' => 'Data dosen tidak ditemukan.'], 404);
        }

        // Simpan atau update penilaian dosen
        $penilaian = Penilaian::updateOrCreate(
            ['nim' => $request->nim, 'nidn' => $dosen->nidn],
            [
                'n_presentasi' => $request->n_presentasi,
                'n_makalah' => $request->n_makalah,
                'detail_presentasi' => $request->detail_presentasi,
                'detail_makalah' => $request->detail_makalah,
            ]
        );

        return response()->json(['message' => 'Berhasil menyimpan penilaian.', 'penilaian' => $penilaian]);
    }
}