<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
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
                <!-- Judul -->
                <span x-show="sidebarOpen" class="font-bold text-lg whitespace-nowrap overflow-hidden ml-3 tracking-wide">
                    Panel Admin
                </span>
            </div>
            
            <!-- Sidebar Navigation -->
            <nav class="flex-1 overflow-y-auto py-4 overflow-hidden">
                <ul class="space-y-2 px-2">
                    <!-- Menu Dashboard Utama -->
                    <li>
                        <a href="{{ route('kaprodi.dashboard') }}" class="flex items-center py-3 rounded-md transition group {{ Request::is('kaprodi/dashboard') ? 'bg-blue-900' : 'hover:bg-blue-700' }}" :class="sidebarOpen ? 'px-4' : 'justify-center px-0'">
                            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            <span x-show="sidebarOpen" class="ml-3 whitespace-nowrap font-semibold">Pusat Kendali</span>
                        </a>
                    </li>
                    <!-- Menu Parameter -->
                    <li>
                        <a href="{{ route('kaprodi.parameter') }}" class="flex items-center py-3 rounded-md transition group {{ Request::is('kaprodi/parameter') ? 'bg-blue-900' : 'hover:bg-blue-700' }}" :class="sidebarOpen ? 'px-4' : 'justify-center px-0'">
                            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                            <span x-show="sidebarOpen" class="ml-3 whitespace-nowrap font-semibold">Manajemen Formulir</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden relative z-10">
            <!-- Top Navbar -->
            <header class="h-16 bg-blue-800 shadow-sm flex items-center justify-between px-6">
                <div class="flex items-center">
                    <div class="shrink-0 flex items-center">
                        <a href="{{ route('kaprodi.dashboard') }}">
                            <x-application-logo class="block h-9 w-auto fill-current text-white" />
                        </a>
                    </div>
                    <!-- Navigation Links -->
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <a href="{{ route('kaprodi.dashboard') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-yellow-400 text-sm font-black leading-5 text-white transition duration-150 ease-in-out">
                            Dashboard Kaprodi
                        </a>
                    </div>
                </div>
                <div class="flex items-center">
                    <span class="text-sm font-bold text-white mr-4">Hi, {{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="m-0 p-0 inline-block">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white text-sm px-4 py-2 rounded-lg transition cursor-pointer font-bold shadow-[2px_2px_0_0_#7f1d1d] hover:translate-y-px hover:translate-x-px hover:shadow-none">
                            Logout
                        </button>
                    </form>
                </div>
            </header>

            <!-- Main Dynamic Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 md:p-6">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>