<x-guest-layout>
    <!-- Menangkap parameter role dari URL -->
    @php
        $roleTitle = request()->query('role');
        $displayRole = 'Sistem PPI';
        
        if($roleTitle == 'mahasiswa') $displayRole = 'Login Mahasiswa';
        elseif($roleTitle == 'dosen') $displayRole = 'Login Dosen';
        elseif($roleTitle == 'mentor') $displayRole = 'Login Mentor Industri';
        elseif($roleTitle == 'admin') $displayRole = 'Login Administrator';
    @endphp

    <!-- Judul Dinamis -->
    <div class="mb-6 text-center border-b-2 border-blue-100 pb-4">
        <h2 class="text-2xl font-black text-blue-900 uppercase tracking-widest">{{ $displayRole }}</h2>
        <p class="text-sm text-gray-500 mt-1">Silakan masukkan kredensial Anda.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf
        
        <!-- Input Tersembunyi: Mengirim parameter role ke Controller (LoginRequest) -->
        <input type="hidden" name="expected_role" value="{{ request('role') }}">

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full border-2 border-blue-200 focus:border-blue-600 focus:ring focus:ring-blue-200 focus:ring-opacity-50 rounded-md shadow-sm font-medium" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full border-2 border-blue-200 focus:border-blue-600 focus:ring focus:ring-blue-200 focus:ring-opacity-50 rounded-md shadow-sm font-medium"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-blue-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 font-medium" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3 bg-blue-600 hover:bg-blue-700 border-2 border-blue-900 shadow-[3px_3px_0_0_#1e3a8a] hover:translate-y-px hover:translate-x-px hover:shadow-[1px_1px_0_0_#1e3a8a] transition-all">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>