<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | Situs publik ini hanya punya satu jenis akun: pengunjung (pemohon
    | informasi). Login petugas/admin ada di aplikasi `be-ppid` dengan tabel
    | `users`, jadi guard `web` bawaan Breeze sudah dihapus dari sini beserta
    | seluruh halaman login/register petugas.
    |
    */

    'defaults' => [
        'guard' => 'pemohon',
        'passwords' => 'pemohon',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    */

    'guards' => [
        /*
         * Akun pengunjung situs publik (pemohon informasi).
         * Tabel `pemohon`, terpisah dari tabel `users` milik panel admin.
         */
        'pemohon' => [
            'driver' => 'session',
            'provider' => 'pemohon',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    */

    'providers' => [
        'pemohon' => [
            'driver' => 'eloquent',
            'model' => App\Models\Pemohon::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | Token reset pengunjung disimpan di tabelnya sendiri, terpisah dari
    | `password_reset_tokens` milik akun petugas.
    |
    */

    'passwords' => [
        'pemohon' => [
            'provider' => 'pemohon',
            'table' => 'password_reset_tokens_pemohon',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    */

    'password_timeout' => 10800,

    /*
    |--------------------------------------------------------------------------
    | Masa Berlaku Tautan Verifikasi Email
    |--------------------------------------------------------------------------
    |
    | Dalam menit. Bawaan Laravel 60 menit terlalu pendek untuk layanan publik:
    | banyak orang membuka emailnya beberapa jam kemudian, lalu meminta tautan
    | baru — dan setiap permintaan ulang memakan kuota kirim email yang
    | jumlahnya terbatas. 24 jam memberi kelonggaran itu tanpa membuat tautannya
    | berlaku selamanya.
    |
    | Tautannya tetap bertanda tangan dan terikat pada id + email pemiliknya,
    | jadi memperpanjang masa berlaku tidak membuatnya bisa ditebak.
    |
    */

    'verification' => [
        'expire' => (int) env('PPID_VERIFIKASI_EMAIL_MENIT', 1440),
    ],

];
