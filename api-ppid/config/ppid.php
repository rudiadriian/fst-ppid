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

    /*
     * Alamat panel admin (be-ppid). Dipakai menyusun tautan reset password
     * pada email ke petugas — email itu harus membuka halaman panel, bukan
     * endpoint API ini.
     */
    'panel_url' => env('PPID_PANEL_URL', 'http://localhost:3000'),

    /*
    |--------------------------------------------------------------------------
    | Pengaman akun panel admin
    |--------------------------------------------------------------------------
    |
    | Angka-angka ini sengaja berbeda dari milik akun pengunjung
    | (fe-ppid/config/ppid.php → 'akun'): akun panel memegang hak tulis atas
    | seluruh data layanan, jadi tahap terakhirnya berujung suspend, bukan
    | sekadar tunggu lebih lama.
    */
    'akun' => [
        /* Jumlah kegagalan yang memicu satu tahap kunci. */
        'gagal_per_tahap' => (int) env('PPID_GAGAL_PER_TAHAP', 3),

        /*
         * Lama kunci tiap tahap, dalam menit: 1 jam, 1 hari, 14 hari.
         * Tahap keempat tidak ada di daftar ini — akunnya disuspend.
         */
        'tahap_kunci_menit' => [60, 1440, 20160],

        /*
         * Hitungan disetel ulang bila sekian jam berlalu tanpa kegagalan baru.
         * Tanpa ini, orang yang salah ketik tiga kali bulan lalu langsung
         * mendarat di tahap berikutnya hari ini.
         */
        'reset_hitungan_gagal_jam' => (int) env('PPID_RESET_HITUNGAN_GAGAL_JAM', 72),

        /*
         * Captcha gambar pada formulir masuk, permintaan lupa password, dan
         * konfirmasi password baru.
         *
         * Dimatikan hanya untuk pengujian otomatis; pada panel yang dapat
         * dijangkau dari jaringan ini harus tetap menyala.
         */
        'captcha_aktif' => (bool) env('PPID_CAPTCHA_AKTIF', true),

        /* Umur satu kode captcha, dalam detik. */
        'captcha_umur_detik' => (int) env('PPID_CAPTCHA_UMUR_DETIK', 300),

        /* Jeda minimum antar permintaan tautan lupa password, dalam menit. */
        'jeda_kirim_tautan_menit' => (int) env('PPID_JEDA_KIRIM_TAUTAN_MENIT', 5),

        /* Umur tautan reset password, dalam menit. */
        'umur_tautan_menit' => (int) env('PPID_UMUR_TAUTAN_MENIT', 60),

        /*
         * Katakan terus terang bila email pada formulir Lupa password bukan
         * akun panel.
         *
         * **Ini pertukaran, bukan perbaikan tanpa biaya.** Jawaban yang
         * membedakan "terdaftar" dan "tidak" membuat endpoint ini bisa dipakai
         * memastikan alamat mana yang punya akun panel — persis yang dicegah
         * oleh jawaban seragam.
         *
         * Dinyalakan karena panel ini bukan layanan terbuka: origin-nya dibatasi
         * `ADMIN_ORIGINS`, akunnya segelintir, dan alamatnya institusional
         * sehingga bisa ditebak tanpa bantuan endpoint mana pun. Yang hilang
         * kecil; yang didapat besar — petugas yang salah ketik alamatnya
         * langsung tahu, alih-alih disuruh memeriksa folder Spam lalu menunggu
         * email yang tidak akan pernah datang.
         *
         * Remnya tetap terpasang: permintaan untuk email asing pun ikut
         * dihitung tangga bertingkat, jadi penyisiran alamat berhenti sendiri
         * pada percobaan ketiga.
         *
         * Matikan (`false`) bila panel ini suatu saat dapat dijangkau dari
         * internet terbuka.
         */
        'beritahu_email_asing' => (bool) env('PPID_BERITAHU_EMAIL_ASING', true),
    ],

];
