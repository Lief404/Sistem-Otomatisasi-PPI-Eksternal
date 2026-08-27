<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MahasiswaController extends Controller
{
    public function index() 
    {
        return view('mahasiswa.dashboard');
    }
    public function nilai() {
        return view('mahasiswa.nilai');
    }
}