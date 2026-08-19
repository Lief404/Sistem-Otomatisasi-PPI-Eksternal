@extends('admin.app')

@section('title', 'Dashboard Administrator')

@section('content')
<div class="bg-white rounded-lg shadow-sm p-6 border-t-4 border-blue-600">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Selamat Datang di Dashboard Admin</h2>
    <p class="text-gray-600">
        Ini adalah tampilan awal untuk Administrator. Sesuai dengan batasan sistem, Admin memiliki akses penuh untuk melakukan manajemen akun, mengatur role, dan mengontrol data dasar. Kosongan dulu untuk tempat CRUD akun nantinya.
    </p>
    
    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Card Dummy -->
        <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
            <h3 class="font-bold text-blue-800">Total Mahasiswa</h3>
            <p class="text-3xl font-extrabold text-blue-600 mt-2">0</p>
        </div>
        <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
            <h3 class="font-bold text-blue-800">Total Dosen</h3>
            <p class="text-3xl font-extrabold text-blue-600 mt-2">0</p>
        </div>
        <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
            <h3 class="font-bold text-blue-800">Total Mentor PT</h3>
            <p class="text-3xl font-extrabold text-blue-600 mt-2">0</p>
        </div>
    </div>
</div>
@endsection