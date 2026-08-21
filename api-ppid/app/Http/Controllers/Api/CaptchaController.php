<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\Captcha;
use Illuminate\Http\JsonResponse;

/**
 * Gambar captcha untuk formulir masuk dan lupa password panel admin.
 *
 * Dikirim sebagai JSON berisi `id` dan gambarnya dalam bentuk data URI, bukan
 * sebagai PNG biasa. Alasannya: panel adalah SPA tanpa sesi, jadi id kodenya
 * harus sampai ke JavaScript untuk dikirim balik bersama jawaban — kalau
 * gambarnya dikirim sebagai `image/png`, id-nya terpaksa dititipkan di header
 * dan tidak terbaca oleh `<img src>` biasa.
 *
 * Tiap permintaan menghasilkan kode baru, jadi tombol "ganti gambar" cukup
 * memanggil ulang endpoint ini.
 */
class CaptchaController extends Controller
{
    public function __invoke(): JsonResponse
    {
        if (!Captcha::aktif()) {
            return response()->json(['data' => ['aktif' => false, 'id' => null, 'gambar' => null]]);
        }

        ['id' => $id, 'kode' => $kode] = Captcha::buat();

        return response()->json([
            'data' => [
                'aktif' => true,
                'id' => $id,
                'gambar' => 'data:image/png;base64,'.base64_encode(Captcha::gambar($kode)),
            ],
        ], 200, [
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        ]);
    }
}
