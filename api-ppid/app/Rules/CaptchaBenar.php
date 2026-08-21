<?php

namespace App\Rules;

use App\Support\Captcha;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Jawaban captcha harus cocok dengan kode yang id-nya disertakan.
 *
 * Aturan ini sengaja tidak membedakan "kode salah", "kode kedaluwarsa", dan
 * "id tidak dikenal": ketiganya berujung pada tindakan yang sama — minta gambar
 * baru lalu isi ulang.
 */
class CaptchaBenar implements ValidationRule
{
    public function __construct(private readonly ?string $captchaId)
    {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!Captcha::aktif()) {
            return;
        }

        if (!Captcha::cocok($this->captchaId, is_string($value) ? $value : null)) {
            $fail('Kode captcha salah atau sudah kedaluwarsa. Muat gambar baru lalu coba lagi.');
        }
    }
}
