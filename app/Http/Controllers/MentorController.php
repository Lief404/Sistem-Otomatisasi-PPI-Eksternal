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
            $mahasiswas = Mahasiswa::with(['logbooks', 'penilaians'])
                ->where('id_pem', $mentor->id_pem)
                ->get();
        }
        return view('mentor.dashboard', compact('mentor', 'mahasiswas'));
    }
}