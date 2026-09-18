<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PembimbingIndustri;
use App\Models\Mahasiswa;
use Illuminate\Support\Facades\Auth;

class MentorController extends Controller
{
    public function index() 
    {
        $user = Auth::user();
        $mentor = PembimbingIndustri::where('user_id', $user->id)->first();
        $mahasiswas = collect();
        if ($mentor) {
            $mahasiswas = Mahasiswa::with(['logbooks', 'penilaians', 
            'saranMahasiswas' => function($query) use ($mentor) {
                $query->where('id_pem', $mentor->id_pem);
            },
            'disiplinMahasiswas' => function($query) use ($mentor) {
                $query->where('id_pem', $mentor->id_pem);
            },
            'kuisionerMentors' => function($query) use ($mentor) {
                $query->where('id_pem', $mentor->id_pem);
            }])
                ->where('id_pem', $mentor->id_pem)
                ->get();
        }
        $parameterSaranMentor = \App\Models\ParameterPenilaian::where('jenis', 'saran_mentor')->get();
        $parameterDisiplin = \App\Models\ParameterPenilaian::where('jenis', 'disiplin_prestasi')->get();
        $parameterKuisioner = \App\Models\ParameterPenilaian::where('jenis', 'kuisioner_mentor')->get();
        return view('mentor.dashboard', compact('mentor', 'mahasiswas', 'parameterSaranMentor', 'parameterDisiplin', 'parameterKuisioner'));
    }

    public function storeSaran(Request $request)
    {
        $request->validate([
            'nim' => 'required|string',
            'tanggal' => 'required|date',
            'saran' => 'required|string',
        ]);

        $mentor = PembimbingIndustri::where('user_id', Auth::id())->first();
        if (!$mentor) {
            return response()->json(['message' => 'Data mentor tidak ditemukan.'], 404);
        }

        // Cek apakah mentor sudah pernah memberikan saran ke mahasiswa ini
        $existingSaran = \App\Models\SaranMahasiswa::where('nim', $request->nim)
            ->where('id_pem', $mentor->id_pem)
            ->first();

        if ($existingSaran) {
            return response()->json(['message' => 'Anda sudah pernah memberikan saran untuk mahasiswa ini.'], 400);
        }

        \App\Models\SaranMahasiswa::create([
            'nim' => $request->nim,
            'id_pem' => $mentor->id_pem,
            'tanggal' => $request->tanggal,
            'saran' => $request->saran,
        ]);

        return response()->json(['message' => 'Saran dan masukan berhasil dikirim.']);
    }
    public function storeDisiplin(Request $request)
    {
        $mentor = PembimbingIndustri::where('user_id', Auth::id())->first();
        if (!$mentor) return response()->json(['message' => 'Unauthorized'], 403);

        $request->validate([
            'nim' => 'required|exists:mahasiswas,nim',
            'penilaian' => 'required|string',
        ]);

        \App\Models\DisiplinMahasiswa::updateOrCreate(
            ['nim' => $request->nim, 'id_pem' => $mentor->id_pem],
            [
                'penilaian' => $request->penilaian,
                'tanggal' => now()->toDateString()
            ]
        );

        return response()->json(['success' => true, 'message' => 'Penilaian Disiplin/Prestasi berhasil disimpan.']);
    }

    public function storeKuisioner(Request $request)
    {
        $mentor = PembimbingIndustri::where('user_id', Auth::id())->first();
        if (!$mentor) return response()->json(['message' => 'Unauthorized'], 403);

        $request->validate([
            'nim' => 'required|exists:mahasiswas,nim',
            'penilaian' => 'required|string',
        ]);

        \App\Models\KuisionerMentor::updateOrCreate(
            ['nim' => $request->nim, 'id_pem' => $mentor->id_pem],
            [
                'penilaian' => $request->penilaian,
                'tanggal' => now()->toDateString()
            ]
        );

        return response()->json(['success' => true, 'message' => 'Kuisioner berhasil disimpan.']);
    }

    public function storeLogbook(Request $request)
    {
        $mentor = PembimbingIndustri::where('user_id', Auth::id())->first();
        if (!$mentor) return response()->json(['message' => 'Unauthorized'], 403);

        $data = $request->validate([
            'nim' => 'required|exists:mahasiswas,nim',
            'logbooks' => 'required|array',
            'logbooks.*.id' => 'required|exists:logbooks,id_log',
            'logbooks.*.status' => 'nullable|string',
            'logbooks.*.catatan_mentor' => 'nullable|string',
            'logbooks.*.nilai' => 'nullable|numeric|min:0|max:100',
        ]);

        foreach ($data['logbooks'] as $l) {
            if (isset($l['nilai']) || !empty($l['status']) || !empty($l['catatan_mentor'])) {
                \App\Models\Logbook::where('id_log', $l['id'])->update([
                    'status' => 'Dinilai',
                    'nilai' => $l['nilai'] ?? null,
                    'catatan_mentor' => $l['catatan_mentor'] ?? ''
                ]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Evaluasi Logbook berhasil disimpan.']);
    }
}