<!DOCTYPE html>
<!-- Tambahkan scroll-smooth agar transisi anchor link halus -->
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Penilaian PPI Eksternal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 flex flex-col min-h-screen text-gray-800" x-data="{ modalLogin: false }" :class="modalLogin ? 'overflow-hidden' : ''">
    
    <!-- Navbar 3D (Dibuat sticky agar selalu di atas saat di-scroll) -->
    <nav class="bg-blue-600 text-white border-b-4 border-blue-900 shadow-[0_6px_0_0_#1e3a8a] sticky top-0 z-40 transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                
                <!-- Logo -->
                <a href="#" class="font-extrabold text-2xl tracking-wider flex items-center gap-2 group cursor-pointer">
                    <span class="text-white drop-shadow-md">PPI POLMAN</span>
                </a>

                <!-- Navigasi Menu (Sembunyi di mobile, muncul di desktop) -->
                <div class="hidden md:flex space-x-8 font-semibold items-center text-blue-100">
                    <a href="#about" class="hover:text-white hover:-translate-y-1 transition-transform">Tentang</a>
                    <a href="#matkul" class="hover:text-white hover:-translate-y-1 transition-transform">Mata Kuliah</a>
                    <a href="#tujuan" class="hover:text-white hover:-translate-y-1 transition-transform">Tujuan</a>
                </div>

                <!-- Tombol Login / Dashboard Cerdas -->
                <div>
                    @auth
                        @php
                            $url = '/mahasiswa/dashboard';
                            if(Auth::user()->role === 'admin') $url = '/admin/dashboard';
                            elseif(Auth::user()->role === 'dosen') $url = '/dosen/dashboard';
                            elseif(Auth::user()->role === 'mentor') $url = '/mentor/dashboard';
                        @endphp
                        
                        <a href="{{ $url }}" class="bg-yellow-400 text-blue-900 font-bold px-6 py-2 border-2 border-blue-900 rounded-lg shadow-[4px_4px_0_0_#1e3a8a] hover:translate-y-1 hover:translate-x-1 hover:shadow-none transition-all duration-200 inline-block">
                            Ke Dashboard
                        </a>
                    @else
                        <button @click="modalLogin = true" class="bg-white text-blue-800 font-bold px-6 py-2 border-2 border-blue-900 rounded-lg shadow-[4px_4px_0_0_#1e3a8a] hover:translate-y-1 hover:translate-x-1 hover:shadow-none transition-all duration-200 active:bg-blue-50">
                            Login
                        </button>
                    @endauth
                </div>
                
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-blue-100 relative overflow-hidden">
        <!-- Ornamen Background -->
        <div class="absolute top-10 left-10 w-32 h-32 bg-blue-200 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-pulse"></div>
        <div class="absolute bottom-10 right-10 w-48 h-48 bg-blue-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-pulse" style="animation-delay: 2s;"></div>

        <div class="text-center px-4 relative z-10 -mt-20">
            <h1 class="text-4xl font-extrabold text-blue-900 sm:text-5xl md:text-7xl drop-shadow-sm">
                Sistem Penilaian <br> <span class="text-blue-600">PPI Eksternal</span>
            </h1>
            <p class="mt-6 text-lg md:text-xl text-gray-600 max-w-3xl mx-auto font-medium">
                Platform terintegrasi untuk mahasiswa, dosen, dan mentor industri dalam mengelola dan memonitoring nilai Program Praktik Industri secara *real-time*.
            </p>
            <div class="mt-10 flex justify-center gap-4">
                
                @auth
                    @php
                        $url = '/mahasiswa/dashboard';
                        if(Auth::user()->role === 'admin') $url = '/admin/dashboard';
                        elseif(Auth::user()->role === 'dosen') $url = '/dosen/dashboard';
                        elseif(Auth::user()->role === 'mentor') $url = '/mentor/dashboard';
                    @endphp
                    <a href="{{ $url }}" class="bg-blue-600 text-white font-bold py-3 px-8 rounded-xl border-2 border-blue-900 shadow-[6px_6px_0_0_#1e3a8a] hover:translate-y-1 hover:translate-x-1 hover:shadow-[2px_2px_0_0_#1e3a8a] transition-all duration-200 text-lg inline-block">
                        Lanjut ke Dashboard
                    </a>
                @else
                    <button @click="modalLogin = true" class="bg-blue-600 text-white font-bold py-3 px-8 rounded-xl border-2 border-blue-900 shadow-[6px_6px_0_0_#1e3a8a] hover:translate-y-1 hover:translate-x-1 hover:shadow-[2px_2px_0_0_#1e3a8a] transition-all duration-200 text-lg">
                        Mulai Sekarang
                    </button>
                @endauth

                <a href="#about" class="bg-white text-blue-800 font-bold py-3 px-8 rounded-xl border-2 border-blue-900 shadow-[6px_6px_0_0_#1e3a8a] hover:translate-y-1 hover:translate-x-1 hover:shadow-[2px_2px_0_0_#1e3a8a] transition-all duration-200 text-lg flex items-center gap-2">
                    Pelajari <svg class="w-5 h-5 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Section About (Tentang PPI) -->
    <section id="about" class="py-24 bg-white border-t-4 border-blue-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div>
                    <h2 class="text-3xl font-extrabold text-blue-900 mb-6 relative inline-block">
                        Apa itu PPI Eksternal?
                        <span class="absolute bottom-1 left-0 w-full h-3 bg-blue-200 -z-10 transform -rotate-1"></span>
                    </h2>
                    <p class="text-lg text-gray-600 mb-4 leading-relaxed">
                        Program Praktik Industri (PPI) Eksternal adalah program magang wajib bagi mahasiswa Politeknik Manufaktur Bandung yang dilaksanakan langsung di perusahaan atau industri terkait.
                    </p>
                    <p class="text-lg text-gray-600 leading-relaxed">
                        Sistem ini dirancang untuk memudahkan proses pengisian laporan harian (Akasia), monitoring mingguan, hingga penilaian akhir kolaboratif antara Dosen Pembimbing Polman dan Pembimbing/Mentor dari pihak Industri.
                    </p>
                </div>
                <!-- Gambar Dummy dengan efek 3D -->
                <div class="relative">
                    <div class="absolute inset-0 bg-blue-600 rounded-2xl transform translate-x-4 translate-y-4 border-2 border-blue-900"></div>
                    <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Diskusi Industri" class="relative z-10 rounded-2xl border-2 border-blue-900 shadow-lg object-cover h-80 w-full grayscale hover:grayscale-0 transition-all duration-500">
                </div>
            </div>
        </div>
    </section>

    <!-- Section Mata Kuliah -->
    <section id="matkul" class="py-24 bg-blue-50 border-t-4 border-blue-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-extrabold text-blue-900">Penilaian Terintegrasi</h2>
                <p class="mt-4 text-xl text-gray-600">Terdiri dari 4 komponen mata kuliah utama yang dinilai selama magang.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Card PII -->
                <div class="bg-white p-8 rounded-xl border-2 border-blue-900 shadow-[6px_6px_0_0_#1e3a8a] hover:-translate-y-2 hover:shadow-[10px_10px_0_0_#1e3a8a] transition-all duration-300">
                    <div class="w-14 h-14 bg-blue-100 rounded-lg border-2 border-blue-900 flex items-center justify-center mb-6 shadow-[2px_2px_0_0_#1e3a8a]">
                        <svg class="w-8 h-8 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">PII</h3>
                    <p class="text-gray-600">Perekayasa Informatika Industri. Penilaian berfokus pada implementasi kode dan *problem solving* IT di industri.</p>
                </div>

                <!-- Card Supervisi -->
                <div class="bg-white p-8 rounded-xl border-2 border-blue-900 shadow-[6px_6px_0_0_#1e3a8a] hover:-translate-y-2 hover:shadow-[10px_10px_0_0_#1e3a8a] transition-all duration-300">
                    <div class="w-14 h-14 bg-blue-100 rounded-lg border-2 border-blue-900 flex items-center justify-center mb-6 shadow-[2px_2px_0_0_#1e3a8a]">
                        <svg class="w-8 h-8 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Supervisi</h3>
                    <p class="text-gray-600">Penilaian terhadap kedisiplinan, manajemen waktu, kerja sama tim, dan etika kerja di lingkungan perusahaan.</p>
                </div>

                <!-- Card K3 -->
                <div class="bg-white p-8 rounded-xl border-2 border-blue-900 shadow-[6px_6px_0_0_#1e3a8a] hover:-translate-y-2 hover:shadow-[10px_10px_0_0_#1e3a8a] transition-all duration-300">
                    <div class="w-14 h-14 bg-blue-100 rounded-lg border-2 border-blue-900 flex items-center justify-center mb-6 shadow-[2px_2px_0_0_#1e3a8a]">
                        <svg class="w-8 h-8 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">K3</h3>
                    <p class="text-gray-600">Keselamatan dan Kesehatan Kerja. Menilai kepatuhan mahasiswa terhadap standar *safety* di lapangan.</p>
                </div>

                <!-- Card Laporan Teknik -->
                <div class="bg-white p-8 rounded-xl border-2 border-blue-900 shadow-[6px_6px_0_0_#1e3a8a] hover:-translate-y-2 hover:shadow-[10px_10px_0_0_#1e3a8a] transition-all duration-300">
                    <div class="w-14 h-14 bg-blue-100 rounded-lg border-2 border-blue-900 flex items-center justify-center mb-6 shadow-[2px_2px_0_0_#1e3a8a]">
                        <svg class="w-8 h-8 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Laporan Teknik</h3>
                    <p class="text-gray-600">Kualitas penulisan buku log harian, PPT presentasi monitoring, dan kelengkapan Laporan Akhir magang.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Tujuan -->
    <section id="tujuan" class="py-24 bg-white border-t-4 border-blue-900">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl font-extrabold text-blue-900 mb-12">Tujuan Sistem PPI</h2>
            <div class="flex flex-col md:flex-row justify-center items-center gap-8">
                
                <div class="flex-1 text-left bg-blue-50 p-6 rounded-xl border-2 border-blue-200">
                    <ul class="space-y-4">
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-gray-700">Digitalisasi pengumpulan laporan harian mahasiswa.</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-gray-700">Mempermudah Dosen Polman memantau progres magang secara *remote*.</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-gray-700">Akurasi penilaian langsung dari Mentor Industri ke dalam sistem Akademik.</span>
                        </li>
                    </ul>
                </div>
                
                <!-- Ilustrasi -->
                <div class="flex-1">
                     <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Data Monitoring" class="rounded-xl border-2 border-blue-900 shadow-[8px_8px_0_0_#1e3a8a]">
                </div>

            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-blue-900 text-white border-t-8 border-blue-950 pt-16 pb-8 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 mb-12">
                
                <!-- Kolom 1: Profil / Kontak -->
                <div>
                    <h3 class="text-3xl font-extrabold tracking-wider mb-6">PPI<span class="text-blue-400">POLMAN</span></h3>
                    <p class="text-blue-200 mb-6 leading-relaxed">
                        Sistem Informasi Program Praktik Industri Eksternal Terpadu - Teknologi Rekayasa Informatika Industri.
                    </p>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-blue-400 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span class="text-blue-100">Jl. Kanayakan No.21, Dago, Kec. Coblong, Kota Bandung, Jawa Barat 40135</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-blue-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            <span class="text-blue-100">Support: polmanbandung@gmail.com</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-blue-400 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            <span class="text-blue-100">+62 22 2500241</span>
                        </div>
                    </div>
                </div>

                <!-- Kolom 2: Link Cepat -->
                <div>
                    <h4 class="text-xl font-bold mb-6 text-white">Tautan Cepat</h4>
                    <ul class="space-y-3">
                        <li><a href="#about" class="text-blue-200 hover:text-white hover:underline transition">Tentang Program</a></li>
                        <li><a href="#matkul" class="text-blue-200 hover:text-white hover:underline transition">Mata Kuliah Penilaian</a></li>
                        <li><a href="#tujuan" class="text-blue-200 hover:text-white hover:underline transition">Tujuan PPI</a></li>
                        <li><a href="#" @click.prevent="modalLogin = true" class="text-blue-200 hover:text-white hover:underline transition">Login Sistem</a></li>
                    </ul>
                </div>

                <!-- Kolom 3: Maps -->
                <div class="h-64 rounded-xl border-4 border-blue-950 overflow-hidden shadow-[6px_6px_0_0_#0f172a]">
                    <iframe 
                        src="https://maps.google.com/maps?q=Politeknik%20Manufaktur%20Bandung,%20Jl.%20Kanayakan,%20Dago&t=&z=16&ie=UTF8&iwloc=&output=embed" 
                        width="100%" 
                        height="100%" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
                
            </div>
            
            <hr class="border-blue-800 mb-6">
            <div class="flex flex-col md:flex-row justify-between items-center text-sm text-blue-300">
                <p>&copy; 2026 Politeknik Manufaktur Bandung. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- MODAL POP-UP PILIH ROLE -->
    <div x-show="modalLogin" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <!-- Background Overlay -->
            <div x-show="modalLogin" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-60 transition-opacity" @click="modalLogin = false"></div>

            <!-- Modal Panel -->
            <div x-show="modalLogin" x-transition.scale class="relative inline-block bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl w-full border-t-8 border-blue-600 p-8">
                
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-gray-900" id="modal-title">Pilih Role Login</h3>
                    <button @click="modalLogin = false" class="text-gray-400 hover:text-red-500 transition">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Grid Pilihan Role dengan parameter URL role -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    
                    <a href="/login?role=mahasiswa" class="group flex flex-col items-center justify-center p-8 bg-white border-2 border-blue-200 rounded-xl shadow-[6px_6px_0_0_#3b82f6] hover:translate-y-1 hover:translate-x-1 hover:shadow-[2px_2px_0_0_#3b82f6] hover:border-blue-500 transition-all duration-200">
                        <div class="p-4 bg-blue-50 rounded-full group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                        </div>
                        <h4 class="mt-4 text-xl font-bold text-gray-800">Mahasiswa</h4>
                        <p class="text-sm text-gray-500 mt-2 text-center">Pengisian Laporan Harian & Mingguan</p>
                    </a>

                    <a href="/login?role=dosen" class="group flex flex-col items-center justify-center p-8 bg-white border-2 border-blue-200 rounded-xl shadow-[6px_6px_0_0_#3b82f6] hover:translate-y-1 hover:translate-x-1 hover:shadow-[2px_2px_0_0_#3b82f6] hover:border-blue-500 transition-all duration-200">
                        <div class="p-4 bg-blue-50 rounded-full group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <h4 class="mt-4 text-xl font-bold text-gray-800">Dosen</h4>
                        <p class="text-sm text-gray-500 mt-2 text-center">Monitoring & Penilaian Akhir</p>
                    </a>

                    <a href="/login?role=mentor" class="group flex flex-col items-center justify-center p-8 bg-white border-2 border-blue-200 rounded-xl shadow-[6px_6px_0_0_#3b82f6] hover:translate-y-1 hover:translate-x-1 hover:shadow-[2px_2px_0_0_#3b82f6] hover:border-blue-500 transition-all duration-200">
                        <div class="p-4 bg-blue-50 rounded-full group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <h4 class="mt-4 text-xl font-bold text-gray-800">Mentor PT</h4>
                        <p class="text-sm text-gray-500 mt-2 text-center">Verifikasi & Penilaian Kinerja</p>
                    </a>

                </div>
                
                <div class="mt-8 text-center">
                    <a href="/login?role=admin" class="text-sm text-gray-400 hover:text-blue-600 underline">Login sebagai Administrator</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>