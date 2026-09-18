<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SaranPerusahaan;

class KaprodiController extends Controller
{
    public function index()
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $idProdi = $user->kaprodi ? $user->kaprodi->id_prodi : null;

        $sarans = SaranPerusahaan::with(['mahasiswa.programStudi'])
            ->when($idProdi, function ($query, $idProdi) {
                return $query->whereHas('mahasiswa', function ($q) use ($idProdi) {
                    $q->where('id_prodi', $idProdi);
                });
            })
            ->orderBy('tanggal', 'desc')->get();

        $disiplins = \App\Models\DisiplinMahasiswa::with(['mahasiswa.programStudi', 'mentor'])
            ->when($idProdi, function ($query, $idProdi) {
                return $query->whereHas('mahasiswa', function ($q) use ($idProdi) {
                    $q->where('id_prodi', $idProdi);
                });
            })
            ->orderBy('tanggal', 'desc')->get();

        $kuisioners = \App\Models\KuisionerMentor::with(['mahasiswa.programStudi', 'mentor'])
            ->when($idProdi, function ($query, $idProdi) {
                return $query->whereHas('mahasiswa', function ($q) use ($idProdi) {
                    $q->where('id_prodi', $idProdi);
                });
            })
            ->orderBy('tanggal', 'desc')->get();

        $saranMentors = \App\Models\SaranMahasiswa::with(['mahasiswa.programStudi', 'pembimbingIndustri'])
            ->when($idProdi, function ($query, $idProdi) {
                return $query->whereHas('mahasiswa', function ($q) use ($idProdi) {
                    $q->where('id_prodi', $idProdi);
                });
            })
            ->orderBy('tanggal', 'desc')->get();

        return view('kaprodi.dashboard', compact('sarans', 'disiplins', 'kuisioners', 'saranMentors', 'user'));
    }

    public function parameter()
    {
        $parameters = \App\Models\ParameterPenilaian::all();
        return view('kaprodi.parameter', compact('parameters'));
    }

    public function storeParameter(Request $request)
    {
        $request->validate([
            'jenis' => 'required|in:presentasi,makalah,saran_mahasiswa,saran_mentor,disiplin_prestasi,kuisioner_mentor',
            'sub_kategori' => 'required|string',
            'indikator' => 'required|array',
        ]);

        \App\Models\ParameterPenilaian::create([
            'jenis' => $request->jenis,
            'sub_kategori' => $request->sub_kategori,
            'indikator' => $request->indikator,
        ]);

        return redirect()->back()->with('success', 'Parameter penilaian berhasil ditambahkan.');
    }

    public function updateParameter(Request $request, $id)
    {
        $request->validate([
            'jenis' => 'required|in:presentasi,makalah,saran_mahasiswa,saran_mentor,disiplin_prestasi,kuisioner_mentor',
            'sub_kategori' => 'required|string',
            'indikator' => 'required|array',
        ]);

        $parameter = \App\Models\ParameterPenilaian::findOrFail($id);
        $parameter->update([
            'jenis' => $request->jenis,
            'sub_kategori' => $request->sub_kategori,
            'indikator' => $request->indikator,
        ]);

        return redirect()->back()->with('success', 'Parameter penilaian berhasil diubah.');
    }

    public function destroyParameter($id)
    {
        $parameter = \App\Models\ParameterPenilaian::findOrFail($id);
        $parameter->delete();

        return redirect()->back()->with('success', 'Parameter penilaian berhasil dihapus.');
    }
}
