<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100 text-gray-800 font-sans antialiased" x-data="{ sidebarOpen: true }">
    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'w-64' : 'w-20'" class="bg-blue-800 text-white flex flex-col transition-all duration-300 shadow-xl">
            <!-- Sidebar Header -->
            <div class="h-16 flex items-center justify-center border-b border-blue-700">
                <span x-show="sidebarOpen" class="font-bold text-lg">Mahasiswa PPI</span>
                <span x-show="!sidebarOpen" class="font-bold text-lg">PPI</span>
            </div>
            
            <!-- Sidebar Navigation -->
            <nav class="flex-1 overflow-y-auto py-4">
                <ul class="space-y-2 px-2">
                    <li>
                        <a href="#" class="flex items-center px-4 py-3 bg-blue-900 rounded-md hover:bg-blue-700 transition">
                            <!-- Icon (menggunakan SVG sederhana) -->
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            <span x-show="sidebarOpen" class="ml-3">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center px-4 py-3 rounded-md hover:bg-blue-700 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            <span x-show="sidebarOpen" class="ml-3">Kelola Akun (CRUD)</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navbar -->
            <header class="h-16 bg-white shadow-sm flex items-center justify-between px-6">
                <!-- Tombol Toggle Sidebar -->
                <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-blue-600 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                
                <div class="flex items-center">
                    <!-- Menampilkan Nama User Dinamis -->
                    <span class="text-sm font-semibold text-gray-700 mr-4">Hi, {{ Auth::user()->name }}</span>
                    
                    <!-- Form Logout Bawaan Breeze -->
                    <form method="POST" action="{{ route('logout') }}" class="m-0 p-0 inline-block">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white text-sm px-4 py-2 rounded transition cursor-pointer">
                            Logout
                        </button>
                    </form>
                </div>
            </header>

            <!-- Main Dynamic Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
                @yield('content')
            </main>

            <!-- Footer Dashboard -->
            <footer class="bg-white border-t border-gray-200 text-center py-4 text-sm text-gray-500">
                Sistem Penilaian PPI Eksternal - v1.0
            </footer>
        </div>
    </div>
</body>
</html>