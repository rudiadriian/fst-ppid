<?php

namespace App\Rules;

use App\Support\Captcha;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Jawaban captcha harus cocok dengan kode yang sedang berlaku.
 *
 * Aturan ini sengaja tidak membedakan "kode salah" dan "kode kedaluwarsa":
 * keduanya berujung pada tindakan yang sama, yaitu mengisi ulang dengan gambar
 * baru.
 */
class CaptchaBenar implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!config('ppid.akun.captcha_aktif')) {
            return;
        }

        if (!Captcha::cocok(is_string($value) ? $value : null)) {
            $fail(__('Kode captcha salah atau sudah kedaluwarsa. Coba lagi dengan gambar baru.'));
        }
    }
}
