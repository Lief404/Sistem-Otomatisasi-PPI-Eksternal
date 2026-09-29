<x-guest-layout>
    <!-- Menangkap parameter role dari URL -->
    @php
        $roleTitle = request()->query('role');
        $displayRole = 'Sistem PPI';
        
        if($roleTitle == 'mahasiswa') $displayRole = 'Login Mahasiswa';
        elseif($roleTitle == 'dosen') $displayRole = 'Login Dosen';
        elseif($roleTitle == 'mentor') $displayRole = 'Login Mentor Industri';
        elseif($roleTitle == 'kaprodi') $displayRole = 'Login Kaprodi';
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

        <!-- Password dengan Fitur Toggle Reveal (Mata) -->
        <div class="mt-4" x-data="{ showPassword: false }">
            <x-input-label for="password" :value="__('Password')" />

            <div class="relative mt-1">
                <x-text-input id="password" class="block w-full border-2 border-blue-200 focus:border-blue-600 focus:ring focus:ring-blue-200 focus:ring-opacity-50 rounded-md shadow-sm font-medium pr-10"
                            ::type="showPassword ? 'text' : 'password'"
                            name="password"
                            required autocomplete="current-password" />
                
                <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-blue-900 transition-colors focus:outline-none">
                    <!-- Icon Mata Terbuka (Show) -->
                    <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    <!-- Icon Mata Tertutup / Dicoret (Hide) -->
                    <svg x-show="showPassword" style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                    </svg>
                </button>
            </div>

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