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
        return view('mentor.dashboard', compact('mentor', 'mahasiswas'));
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

        $data = $request->validate([
            'nim' => 'required|exists:mahasiswas,nim',
            'p1' => 'nullable|numeric', 'p2' => 'nullable|numeric', 'p3' => 'nullable|numeric', 'p4' => 'nullable|numeric', 'p5' => 'nullable|numeric',
            's3' => 'nullable|numeric', 's5' => 'nullable|numeric', 's6' => 'nullable|numeric', 's7' => 'nullable|numeric', 's8' => 'nullable|numeric',
        ]);

        foreach (['p1','p2','p3','p4','p5','s3','s5','s6','s7','s8'] as $f) {
            $data[$f] = $data[$f] ?? 0;
        }

        \App\Models\DisiplinMahasiswa::updateOrCreate(
            ['nim' => $data['nim'], 'id_pem' => $mentor->id_pem],
            array_merge($data, ['tanggal' => now()->toDateString()])
        );

        return response()->json(['success' => true, 'message' => 'Penilaian Disiplin/Prestasi berhasil disimpan.']);
    }

    public function storeKuisioner(Request $request)
    {
        $mentor = PembimbingIndustri::where('user_id', Auth::id())->first();
        if (!$mentor) return response()->json(['message' => 'Unauthorized'], 403);

        $data = $request->validate([
            'nim' => 'required|exists:mahasiswas,nim',
            'h1a' => 'nullable|numeric', 'h1b' => 'nullable|numeric', 'h1c' => 'nullable|numeric', 'h1d' => 'nullable|numeric', 'h2' => 'nullable|numeric', 'h3' => 'nullable|numeric', 'h4' => 'nullable|numeric',
            's1' => 'nullable|numeric', 's2' => 'nullable|numeric', 's3' => 'nullable|numeric', 's4' => 'nullable|numeric', 's5' => 'nullable|numeric', 's6' => 'nullable|numeric', 's7' => 'nullable|numeric', 's8' => 'nullable|numeric',
        ]);

        foreach (['h1a','h1b','h1c','h1d','h2','h3','h4','s1','s2','s3','s4','s5','s6','s7','s8'] as $f) {
            $data[$f] = $data[$f] ?? 0;
        }

        \App\Models\KuisionerMentor::updateOrCreate(
            ['nim' => $data['nim'], 'id_pem' => $mentor->id_pem],
            array_merge($data, ['tanggal' => now()->toDateString()])
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
        ]);

        foreach ($data['logbooks'] as $l) {
            if (!empty($l['status']) || !empty($l['catatan_mentor'])) {
                \App\Models\Logbook::where('id_log', $l['id'])->update([
                    'status' => $l['status'] ?? 'Pending',
                    'catatan_mentor' => $l['catatan_mentor'] ?? ''
                ]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Evaluasi Logbook berhasil disimpan.']);
    }
}