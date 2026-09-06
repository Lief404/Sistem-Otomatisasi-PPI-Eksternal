<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\Penilaian;
use Illuminate\Support\Facades\Auth;

class DosenController extends Controller
{
    public function index() 
    {
        $user = Auth::user();
        $dosen = Dosen::where('user_id', $user->id)->first();
        
        if ($dosen) {
            $mahasiswas = Mahasiswa::with(['programStudi', 'pembimbingIndustri', 'penilaians'])
                ->where('nidn', $dosen->nidn)
                ->get();
        } else {
            $mahasiswas = collect();
        }

        // Jika tidak ada mahasiswa yang terasosiasi spesifik dengan NIDN ini, tampilkan semua mahasiswa
        if ($mahasiswas->isEmpty()) {
            $mahasiswas = Mahasiswa::with(['programStudi', 'pembimbingIndustri', 'penilaians'])->get();
        }

        return view('dosen.dashboard', compact('dosen', 'mahasiswas'));
    }

    public function storePenilaian(Request $request)
    {
        $request->validate([
            'nim' => 'required|string',
            'n_presentasi' => 'required|numeric',
            'n_makalah' => 'required|numeric',
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
            ]
        );

        return response()->json(['message' => 'Berhasil menyimpan penilaian.', 'penilaian' => $penilaian]);
    }
}