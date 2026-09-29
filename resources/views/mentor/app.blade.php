<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- WAJIB ADA UNTUK AJAX REQUEST -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Mentor Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 text-gray-800 font-sans antialiased" x-data="{ sidebarOpen: true }">
    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'w-64' : 'w-20'" class="bg-blue-800 text-white flex flex-col transition-all duration-300 shadow-xl relative z-20">
            
            <!-- Sidebar Header -->
            <div class="h-16 flex items-center border-b border-blue-700 transition-all duration-300" :class="sidebarOpen ? 'px-4' : 'justify-center'">
                
                <!-- Tombol Hamburger -->
                <button @click="sidebarOpen = !sidebarOpen" class="text-white hover:text-blue-300 focus:outline-none p-1 rounded-md flex-shrink-0 transition-transform duration-200">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>

                <!-- Judul disembunyikan saat collapse -->
                <span x-show="sidebarOpen" class="font-bold text-lg whitespace-nowrap overflow-hidden ml-3 tracking-wide">
                    Mentor PPI
                </span>
                
            </div>
            
            <!-- Sidebar Navigation -->
            <nav class="flex-1 overflow-y-auto py-4 overflow-hidden">
                <ul class="space-y-2 px-2">
                    <li>
                        <a href="{{ route('mentor.dashboard') }}" class="flex items-center py-3 rounded-md transition group {{ request()->routeIs('mentor.dashboard') ? 'bg-blue-900 text-white' : 'hover:bg-blue-700' }}" :class="sidebarOpen ? 'px-4' : 'justify-center px-0'">
                            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            <span x-show="sidebarOpen" class="ml-3 whitespace-nowrap">Dashboard</span>
                        </a>
                    </li>
                    <!-- MENU BARU: RIWAYAT TTD -->
                    <li>
                        <a href="{{ route('mentor.riwayat_ttd') }}" class="flex items-center py-3 rounded-md transition group {{ request()->routeIs('mentor.riwayat_ttd') ? 'bg-blue-900 text-white' : 'hover:bg-blue-700' }}" :class="sidebarOpen ? 'px-4' : 'justify-center px-0'">
                            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <span x-show="sidebarOpen" class="ml-3 whitespace-nowrap font-semibold">Riwayat TTD</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navbar -->
            <header class="h-16 bg-white shadow-sm flex items-center justify-end px-6 z-10 relative">
                
                <div class="flex items-center gap-4">
                    
                    <!-- ========================================== -->
                    <!-- TOMBOL NOTIFIKASI (LONCENG) MENTOR -->
                    <!-- ========================================== -->
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="relative p-2 mt-1 text-gray-500 hover:text-gray-700 transition duration-150 ease-in-out focus:outline-none flex items-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                                
                                <!-- Indikator Unread (Merah berkedip jika ada notif baru) -->
                                @if(auth()->user()->unreadNotifications->count() > 0)
                                <span class="absolute top-1 right-1 flex h-3 w-3">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                                </span>
                                @endif
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <!-- Wrapper Notifikasi -->
                            <div class="w-[320px] sm:w-[380px] bg-white flex flex-col shadow-lg overflow-hidden">
                                
                                <!-- Header Notifikasi -->
                                <div class="bg-blue-600 px-4 py-3 flex justify-between items-center">
                                    <h3 class="font-bold text-sm text-white m-0">Notifikasi TTD</h3>
                                    @if(auth()->user()->unreadNotifications->count() > 0)
                                        <span class="bg-white text-blue-600 text-[10px] font-extrabold px-2 py-1 rounded-full uppercase tracking-wider shadow-sm">
                                            {{ auth()->user()->unreadNotifications->count() }} Baru
                                        </span>
                                    @endif
                                </div>

                                <!-- Body Notifikasi (Scrollable) -->
                                <div class="max-h-[350px] overflow-y-auto w-full">
                                    @forelse(auth()->user()->notifications()->take(15)->get() as $notification)
                                        @php 
                                            $isUnread = is_null($notification->read_at); 
                                            // AMBIL STATUS DARI PAYLOAD DATA NOTIF, DEFAULT: 'pending'
                                            $statusPengajuan = $notification->data['status'] ?? 'pending'; 
                                        @endphp
                                        
                                        <!-- Container Item Notifikasi -->
                                        <div id="notif-item-{{ $notification->id }}" class="relative group border-b border-gray-100 transition-colors duration-200 {{ $isUnread ? 'bg-blue-50/50' : 'bg-gray-50' }}">
                                            
                                            <!-- Klik untuk Read dan Redirect -->
                                            <div onclick="handleNotifClick('{{ $notification->id }}', '{{ $notification->data['url'] ?? '#' }}')" class="flex items-start px-4 py-4 w-full pr-10 cursor-pointer">
                                                
                                                <div class="notif-icon flex-shrink-0 rounded-full w-10 h-10 flex items-center justify-center mr-3 mt-1 {{ $isUnread ? 'bg-blue-100 text-blue-600' : 'bg-gray-200 text-gray-400' }}">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                </div>

                                                <div class="flex-1 min-w-0 pointer-events-none">
                                                    <p class="notif-title text-sm font-bold truncate {{ $isUnread ? 'text-gray-900' : 'text-gray-500' }}">
                                                        {{ $notification->data['title'] ?? 'Pemberitahuan' }}
                                                    </p>
                                                    <p class="notif-message text-xs mt-1 whitespace-normal leading-relaxed {{ $isUnread ? 'text-gray-600' : 'text-gray-400' }}">
                                                        {{ $notification->data['message'] ?? 'Ada pengajuan baru.' }}
                                                    </p>
                                                    <p class="text-[10px] mt-2 font-medium flex items-center {{ $isUnread ? 'text-blue-500' : 'text-gray-400' }}">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        {{ $notification->created_at->diffForHumans() }}
                                                    </p>
                                                </div>
                                            </div>
                                            
                                            <!-- Tombol Hapus: HANYA MUNCUL JIKA STATUS BUKAN PENDING -->
                                            @if($statusPengajuan !== 'pending')
                                                <button type="button" onclick="deleteNotification(event, '{{ $notification->id }}')" class="absolute top-4 right-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200 text-gray-400 hover:text-red-500 bg-white border border-gray-200 hover:bg-red-50 rounded-full p-1.5 shadow-sm" title="Hapus Notifikasi Ini">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </button>
                                            @endif

                                        </div>
                                    @empty
                                        <div class="px-4 py-8 text-center flex flex-col items-center justify-center">
                                            <div class="bg-gray-100 text-gray-400 rounded-full w-14 h-14 flex items-center justify-center mb-3">
                                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                            </div>
                                            <p class="text-sm text-gray-500 font-medium">Tidak ada notifikasi baru.</p>
                                        </div>
                                    @endforelse
                                </div>
                                
                                <!-- Footer Notifikasi (Aksi Global) -->
                                @if(auth()->user()->notifications->count() > 0)
                                <div class="bg-gray-50 border-t border-gray-100 p-2 flex justify-between items-center w-full">
                                    <form method="POST" action="{{ route('notifikasi.read_all') }}" class="m-0">
                                        @csrf
                                        <button type="submit" class="text-[11px] font-bold text-blue-600 hover:text-blue-800 py-1.5 px-3 rounded hover:bg-blue-100 transition-colors duration-200">
                                            Tandai semua dibaca
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('notifikasi.clear_all') }}" class="m-0" onsubmit="return confirm('Bersihkan riwayat notifikasi? (Yang masih pending tidak akan dihapus)')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-[11px] font-bold text-red-500 hover:text-red-700 py-1.5 px-3 rounded hover:bg-red-100 transition-colors duration-200">
                                            Bersihkan Riwayat
                                        </button>
                                    </form>
                                </div>
                                @endif
                                
                            </div>
                        </x-slot>
                    </x-dropdown>
                    <!-- ========================================== -->

                    <!-- Menampilkan Nama User Dinamis -->
                    <span class="text-sm font-semibold text-gray-700">Hi, {{ Auth::user()->name }}</span>
                    
                    <!-- Form Logout -->
                    <form method="POST" action="{{ route('logout') }}" class="m-0 p-0 inline-block ml-2">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white text-sm px-4 py-2 rounded-lg transition cursor-pointer font-bold shadow-sm">
                            Logout
                        </button>
                    </form>
                </div>
            </header>

            <!-- Main Dynamic Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6 relative z-0">
                @yield('content')
            </main>

            <!-- Footer Dashboard -->
            <footer class="bg-white border-t border-gray-200 text-center py-4 text-sm text-gray-500">
                Sistem Penilaian PPI Eksternal - v1.0
            </footer>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- SCRIPT LOGIKA NOTIFIKASI AJAX -->
    <!-- ========================================== -->
    <script>
        // 1. Fungsi Klik Notifikasi (Tandai dibaca UI instan & Redirect)
        function handleNotifClick(notifId, targetUrl) {
            let el = document.getElementById('notif-item-' + notifId);
            
            if (el) {
                // Ubah UI seketika jadi abu-abu agar terasa cepat dan responsif
                el.classList.remove('bg-blue-50/50');
                el.classList.add('bg-gray-50');
                
                // Ubah Icon jadi abu-abu
                let icon = el.querySelector('.notif-icon');
                if (icon) {
                    icon.classList.remove('bg-blue-100', 'text-blue-600');
                    icon.classList.add('bg-gray-200', 'text-gray-400');
                }
                
                // Ubah Title jadi abu-abu gelap
                let title = el.querySelector('.notif-title');
                if (title) {
                    title.classList.remove('text-gray-900');
                    title.classList.add('text-gray-500');
                }
            }

            // Panggil API mark as read di background (tanpa await)
            fetch(`/notifikasi/${notifId}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            }).catch(err => console.error("Gagal tandai dibaca", err));

            // Redirect ke halaman detail (approval/riwayat)
            if(targetUrl && targetUrl !== '#') {
                window.location.href = targetUrl;
            }
        }

        // 2. Fungsi Hapus Notifikasi (Hanya dieksekusi jika button muncul = non-pending)
        async function deleteNotification(event, notifId) {
            // event.stopPropagation() mencegah efek klik tembus ke div parent (handleNotifClick)
            event.stopPropagation(); 
            
            if(!confirm('Hapus notifikasi ini? (Data approval / log riwayat akan tetap aman)')) return;

            let el = document.getElementById('notif-item-' + notifId);
            
            try {
                const response = await fetch(`/notifikasi/${notifId}/hapus`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    // Beri efek animasi memudar sebelum menghilang dari DOM
                    el.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                    el.style.opacity = '0';
                    el.style.transform = 'translateX(20px)';
                    setTimeout(() => el.remove(), 300);
                } else {
                    alert('Gagal menghapus notifikasi dari server.');
                }
            } catch (error) {
                console.error('Terjadi kesalahan koneksi:', error);
            }
        }
    </script>
</body>
</html>