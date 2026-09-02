<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * Di produksi situs ini berada di belakang reverse proxy yang menangani
     * TLS. Tanpa daftar ini Laravel mengabaikan `X-Forwarded-Proto`, sehingga
     * `SESSION_SECURE_COOKIE=true` membuat cookie sesi tidak pernah terkirim
     * dan pemohon tidak bisa login.
     *
     * Sengaja bukan '*': hanya alamat proxy yang memang dipercaya yang boleh
     * memalsukan skema dan IP asal.
     *
     * @var array<int, string>|string|null
     */
    protected $proxies = [
        '192.168.1.17',
        '127.0.0.1',
    ];

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;
}
