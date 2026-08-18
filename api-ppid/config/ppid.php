<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Identitas layanan pada email ke pemohon
    |--------------------------------------------------------------------------
    |
    | Panel admin ikut mengirim email ke pemohon saat status pengajuannya
    | berpindah, jadi nilai-nilai ini harus sama dengan milik aplikasi situs
    | (fe-ppid/config/ppid.php) — templat emailnya memang berkas yang sama.
    */

    'kontak' => [
        'instansi' => env('PPID_INSTANSI', 'PT Food Station Tjipinang Jaya (Perseroda)'),
        'email' => env('PPID_KONTAK_EMAIL', 'ppid@foodstation.co.id'),
    ],

    /*
     * Alamat portal pemohon. Bukan APP_URL: APP_URL di sini menunjuk API,
     * sedangkan tombol dan logo di email harus mengarah ke situsnya.
     */
    'situs_url' => env('PPID_SITUS_URL', 'http://localhost:8000'),

    /*
     * Zona waktu untuk tanggal & jam pada email. Waktu tersimpan dalam UTC,
     * jadi harus digeser dulu sebelum diberi label "WIB".
     */
    'zona_waktu' => env('PPID_ZONA_WAKTU', 'Asia/Jakarta'),

    /*
     * Bahasa email ke pemohon. Dikunci Bahasa Indonesia supaya rangkaian
     * pemberitahuan atas satu pengajuan tidak berpindah bahasa antara yang
     * dikirim situs dan yang dikirim panel.
     */
    'bahasa_email' => env('PPID_BAHASA_EMAIL', 'id'),

];
