<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // 1. Ambil input email dan expected_role dari form (URL / pop-up)
        $email = $this->input('email');
        $expectedRole = $this->input('expected_role');

        // 2. Ekstrak domain dari email (mengambil teks setelah '@')
        $domain = substr(strrchr($email, "@"), 1);

        // 3. Daftarkan domain yang valid beserta role pasangannya
        $validDomains = [
            'mhs.polman' => 'mahasiswa',
            'dosen.polman' => 'dosen',
            'industri.id' => 'mentor',
            'admin.polman' => 'admin'
        ];

        // LOGIKA FILTER A: Jika domain ngawur / tidak terdaftar
        if (!array_key_exists($domain, $validDomains)) {
            throw ValidationException::withMessages([
                // Admin tidak disebutkan demi keamanan / rahasia sistem
                'email' => 'Silahkan login menggunakan akun mahasiswa, dosen, atau mentor.',
            ]);
        }

        // LOGIKA FILTER B: Cek kecocokan pintu masuk (expected) dengan role asli
        $actualRole = $validDomains[$domain];
        
        // Jika user masuk lewat pop-up khusus (ada parameter expectedRole) dan role aslinya beda
        if ($expectedRole && $expectedRole !== $actualRole) {
            
            // Pengecualian: Biarkan admin bebas masuk dari pintu mana saja secara diam-diam.
            // Namun, jika bukan admin yang nyasar, tolak!
            if ($actualRole !== 'admin') {
                throw ValidationException::withMessages([
                    'email' => 'Silahkan login menggunakan akun ' . $expectedRole . '!',
                ]);
            }
        }

        // Lanjutkan ke proses Autentikasi bawaan Laravel
        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}