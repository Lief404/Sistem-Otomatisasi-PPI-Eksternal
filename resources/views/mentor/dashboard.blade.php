@extends('mentor.app')
@section('title', 'Dashboard Mentor Industri')

@section('content')
<div class="bg-white rounded-xl shadow-[6px_6px_0_0_#1e3a8a] p-6 border-2 border-blue-900">
    <h2 class="text-2xl font-black text-blue-900 mb-2">Validasi Logbook Harian</h2>
    <p class="text-gray-600 border-b-2 border-gray-100 pb-4 mb-6">
        Silakan cek aktivitas mahasiswa yang ditempatkan di perusahaan Anda. Anda juga bertugas mengisi form Penilaian Disiplin & Supervisi (Hard/Softskill).
    </p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
        <div class="border-2 border-blue-900 rounded-lg p-4 bg-blue-50 hover:bg-blue-100 transition cursor-pointer shadow-[4px_4px_0_0_#1e3a8a]">
            <h3 class="font-bold text-blue-900">Validasi Logbook</h3>
            <p class="text-sm text-gray-600 mt-1">Cek kebenaran jam dan aktivitas yang diinput mahasiswa.</p>
        </div>
        <div class="border-2 border-blue-900 rounded-lg p-4 bg-blue-50 hover:bg-blue-100 transition cursor-pointer shadow-[4px_4px_0_0_#1e3a8a]">
            <h3 class="font-bold text-blue-900">Input Penilaian Industri</h3>
            <p class="text-sm text-gray-600 mt-1">Isi kuisioner penilaian kinerja magang.</p>
        </div>
    </div>
</div>
@endsection