<?php

namespace App\Rules;

use App\Support\Recaptcha;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\Request;

/**
 * Token reCAPTCHA v3 harus lolos pemeriksaan Google untuk aksi yang dimaksud.
 *
 * Menggantikan `CaptchaBenar`. Bedanya bukan hanya layanan yang dipakai: aturan
 * ini memanggil jaringan luar, jadi ia sengaja dipasang pada isian yang
 * divalidasi paling akhir — tidak ada gunanya menukar token ke Google kalau
 * emailnya saja belum berbentuk email.
 *
 * Alasan penolakan diteruskan apa adanya dari `Recaptcha::periksa()`; di sana
 * pesan untuk kasus yang tindakannya sama sudah disatukan.
 */
class RecaptchaBenar implements ValidationRule
{
    public function __construct(
        private readonly string $aksi,
        private readonly ?Request $request = null,
    ) {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $hasil = Recaptcha::periksa(
            is_string($value) ? $value : null,
            $this->aksi,
            $this->request?->ip(),
        );

        if (!$hasil['lolos']) {
            $fail($hasil['alasan'] ?? 'Verifikasi keamanan gagal.');
        }
    }
}
