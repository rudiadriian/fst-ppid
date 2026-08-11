<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Akun pengunjung (guard `pemohon`)
    |--------------------------------------------------------------------------
    */

    'akun' => [
        /*
         * Email wajib terverifikasi sebelum akun bisa dipakai masuk.
         *
         * Pastikan MAIL_* benar sebelum menyalakan — kalau email verifikasi
         * tidak pernah sampai, pengunjung tidak akan bisa masuk sama sekali.
         * Untuk pengembangan lokal, `MAIL_MAILER=log` menaruh tautannya di
         * `storage/logs/laravel.log`.
         */
        'wajib_verifikasi_email' => (bool) env('PPID_WAJIB_VERIFIKASI_EMAIL', true),

        /* Batas percobaan login per kombinasi email + IP, per menit. */
        'batas_percobaan_login' => (int) env('PPID_BATAS_PERCOBAAN_LOGIN', 5),
    ],

];
