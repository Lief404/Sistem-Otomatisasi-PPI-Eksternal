<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'PPI Eksternal Polman') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <!-- 1. Ubah background menjadi bg-blue-50 agar senada dengan landing page -->
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-blue-50">
            <div>
                <a href="/">
                    <!-- 2. Mengganti logo komponen bawaan Breeze dengan teks khusus Polman -->
                    <h2 class="text-3xl font-extrabold text-blue-800 tracking-wider">PPI <span class="text-blue-500">POLMAN</span></h2>
                </a>
            </div>

            <!-- 3. Menambahkan border tebal, efek shadow 3D (neo-brutalism), dan animasi saat di-hover -->
            <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white border-2 border-blue-900 rounded-xl shadow-[8px_8px_0_0_#1e3a8a] transform transition-all hover:-translate-y-1 hover:shadow-[12px_12px_0_0_#1e3a8a] overflow-hidden">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>