<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SaranPerusahaan;

class KaprodiController extends Controller
{
    public function index()
    {
        $sarans = SaranPerusahaan::with(['mahasiswa.programStudi'])->orderBy('tanggal', 'desc')->get();
        $disiplins = \App\Models\DisiplinMahasiswa::with(['mahasiswa.programStudi', 'mentor'])->orderBy('tanggal', 'desc')->get();
        $kuisioners = \App\Models\KuisionerMentor::with(['mahasiswa.programStudi', 'mentor'])->orderBy('tanggal', 'desc')->get();
        return view('kaprodi.dashboard', compact('sarans', 'disiplins', 'kuisioners'));
    }
}
