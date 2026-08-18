<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tandai permintaan ini agar tidak dicatat sebagai "halaman sebelumnya".
 *
 * Middleware `StartSession` menyimpan URL setiap permintaan GET biasa sebagai
 * halaman sebelumnya. Gambar captcha diambil peramban lewat `<img>`, yang juga
 * GET biasa — akibatnya URL gambar itulah yang tercatat, dan `back()` setelah
 * validasi gagal akan melempar pengguna ke berkas gambar, bukan kembali ke
 * formulirnya.
 *
 * `StartSession` melewatkan pencatatan bila permintaannya AJAX, jadi penanda
 * itu yang dipasang di sini. Endpoint captcha memang hanya mengembalikan
 * gambar, sehingga penandaan ini tidak mengubah apa pun selain riwayat URL.
 */
class BukanHalamanSebelumnya
{
    public function handle(Request $request, Closure $next): Response
    {
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');

        return $next($request);
    }
}
