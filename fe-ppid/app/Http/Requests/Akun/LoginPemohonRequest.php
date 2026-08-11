<?php

namespace App\Http\Requests\Akun;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Permintaan login akun pengunjung.
 *
 * Percobaan dibatasi per kombinasi email + IP supaya penebakan password
 * tidak bisa dilakukan berulang-ulang.
 */
class LoginPemohonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ];
    }

    /** @throws \Illuminate\Validation\ValidationException */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $kredensial = [
            'email' => Str::lower($this->string('email')),
            'password' => $this->string('password'),
        ];

        if (!Auth::guard('pemohon')->attempt($kredensial)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('Email atau password tidak cocok.'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /** @throws \Illuminate\Validation\ValidationException */
    public function ensureIsNotRateLimited(): void
    {
        $batas = (int) config('ppid.akun.batas_percobaan_login', 5);

        if (!RateLimiter::tooManyAttempts($this->throttleKey(), $batas)) {
            return;
        }

        event(new Lockout($this));

        $detik = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('Terlalu banyak percobaan masuk. Coba lagi dalam :detik detik.', [
                'detik' => $detik,
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return 'akun-pemohon|'.Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
