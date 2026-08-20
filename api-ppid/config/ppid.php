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
     * Zona waktu untuk tanggal & jam pada email.
     *
     * Sejak `app.timezone` ikut Asia/Jakarta, pergeseran di kelas email tidak
     * lagi mengubah apa pun — tetapi tetap dipasang: label "WIB" yang dicetak
     * di sebelahnya baru dijamin benar kalau zonanya disebut, bukan diwarisi
     * dari setelan aplikasi yang bisa berubah.
     */
    'zona_waktu' => env('PPID_ZONA_WAKTU', 'Asia/Jakarta'),

    /*
     * Bahasa email ke pemohon. Dikunci Bahasa Indonesia supaya rangkaian
     * pemberitahuan atas satu pengajuan tidak berpindah bahasa antara yang
     * dikirim situs dan yang dikirim panel.
     */
    'bahasa_email' => env('PPID_BAHASA_EMAIL', 'id'),

];
