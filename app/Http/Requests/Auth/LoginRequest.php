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
            'username' => ['required', 'string'], // Diubah dari email menjadi username
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

        $expectedRole = $this->input('expected_role');

        // Lakukan Autentikasi dengan USERNAME dan PASSWORD
        if (! Auth::attempt($this->only('username', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'username' => 'Username atau Password salah.',
            ]);
        }

        // Jika berhasil login, cek Role-nya
        $user = Auth::user();

        // LOGIKA FILTER: Cek kecocokan pintu masuk (expected) dengan role asli di DB
        if ($expectedRole && $expectedRole !== $user->role) {
            
            // Pengecualian: Admin bebas masuk dari pintu mana saja
            if ($user->role !== 'admin') {
                
                Auth::logout(); // Keluarkan kembali karena salah pintu
                
                throw ValidationException::withMessages([
                    'username' => 'Akses ditolak! Silahkan login melalui portal khusus ' . ucfirst($user->role) . '.',
                ]);
            }
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
            'username' => trans('auth.throttle', [ // Ubah ke username
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
        // Ubah dari email menjadi username
        return Str::transliterate(Str::lower($this->string('username')).'|'.$this->ip());
    }
}