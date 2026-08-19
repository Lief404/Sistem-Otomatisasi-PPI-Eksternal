@extends('dosen.app')
@section('title', 'Dashboard Dosen Pembimbing')

@section('content')
<div class="bg-white rounded-xl shadow-[6px_6px_0_0_#1e3a8a] p-6 border-2 border-blue-900">
    <h2 class="text-2xl font-black text-blue-900 mb-2">Verifikasi Kelayakan & Rekap Jam</h2>
    <p class="text-gray-600 border-b-2 border-gray-100 pb-4 mb-6">
        Selamat datang. Di sini Anda dapat memantau logbook mahasiswa bimbingan, menilai Mutu Presentasi (40%), dan Penulisan Makalah (60%).
    </p>
    
    <div class="bg-yellow-50 border-2 border-yellow-400 p-4 rounded-lg">
        <h3 class="font-bold text-yellow-800">Menunggu Verifikasi</h3>
        <p class="text-sm text-yellow-700 mt-1">Saat ini belum ada mahasiswa yang mengajukan laporan akhir.</p>
    </div>
</div>
@endsection