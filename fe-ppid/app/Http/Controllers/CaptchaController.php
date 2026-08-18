<?php

namespace App\Http\Controllers;

use App\Support\Captcha;
use Illuminate\Http\Response;

/**
 * Gambar captcha untuk formulir daftar dan masuk akun pengunjung.
 *
 * Setiap permintaan menghasilkan kode baru, jadi tombol "ganti gambar" cukup
 * memuat ulang URL ini. Respons tidak boleh disimpan cache mana pun — gambar
 * lama yang tersimpan di peramban atau proksi akan selalu ditolak karena
 * kodenya sudah berganti.
 */
class CaptchaController extends Controller
{
    public function __invoke(): Response
    {
        $png = Captcha::gambar(Captcha::buat());

        return response($png, 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
        ]);
    }
}
