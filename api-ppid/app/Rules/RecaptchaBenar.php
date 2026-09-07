<?php

namespace App\Rules;

use App\Support\Recaptcha;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\Request;

/**
 * Token reCAPTCHA v2 harus lolos pemeriksaan Google.
 *
 * Aturan ini memanggil jaringan luar, jadi sengaja dipasang pada isian yang
 * divalidasi paling akhir — tidak ada gunanya menukar token ke Google kalau
 * emailnya saja belum berbentuk email.
 *
 * Alasan penolakan diteruskan apa adanya dari `Recaptcha::periksa()`; di sana
 * pesan untuk kasus yang tindakannya sama sudah disatukan.
 */
class RecaptchaBenar implements ValidationRule
{
    public function __construct(private readonly ?Request $request = null)
    {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $hasil = Recaptcha::periksa(
            is_string($value) ? $value : null,
            $this->request?->ip(),
        );

        if (!$hasil['lolos']) {
            $fail($hasil['alasan'] ?? 'Verifikasi keamanan gagal.');
        }
    }
}
