<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Identitas pengirim & kontak layanan
    |--------------------------------------------------------------------------
    |
    | Dipakai pada kop dan kaki semua email ke pemohon. Alamat `email` di sini
    | adalah kotak masuk yang dibaca petugas — berbeda dari MAIL_FROM_ADDRESS
    | (`noreply-...`) yang tidak menerima balasan.
    */

    'kontak' => [
        'instansi' => env('PPID_INSTANSI', 'PT Food Station Tjipinang Jaya (Perseroda)'),
        'email' => env('PPID_KONTAK_EMAIL', 'ppid@foodstation.co.id'),
    ],

    /*
     * Alamat portal pemohon, dipakai kop dan tautan di email. Di aplikasi
     * situs nilainya sama dengan APP_URL; disendirikan supaya api-ppid bisa
     * memakai berkas templat email yang sama persis.
     */
    'situs_url' => env('PPID_SITUS_URL', env('APP_URL')),

    /*
     * Bahasa semua email ke pemohon.
     *
     * Dikunci, tidak mengikuti pilihan bahasa di situs: sebagian pemberitahuan
     * dikirim panel admin (api-ppid) yang tidak tahu bahasa yang dipilih
     * pengunjung, dan surat layanan informasi publik memang berbahasa
     * Indonesia. Tanpa ini, satu pengajuan bisa menghasilkan rangkaian email
     * yang berpindah-pindah bahasa.
     */
    'bahasa_email' => env('PPID_BAHASA_EMAIL', 'id'),

    /*
     * Zona waktu yang dipakai mencetak tanggal & jam pada email.
     *
     * Aplikasi menyimpan waktu dalam UTC (config app.timezone), jadi nilainya
     * harus digeser dulu sebelum diberi label "WIB" — tanpa ini pemohon
     * membaca jam yang meleset tujuh jam.
     */
    'zona_waktu' => env('PPID_ZONA_WAKTU', 'Asia/Jakarta'),

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

        /* Batas percobaan login per kombinasi identitas + IP, per menit. */
        'batas_percobaan_login' => (int) env('PPID_BATAS_PERCOBAAN_LOGIN', 5),

        /*
         * Batas percobaan login per IP saja, per menit.
         *
         * Menahan penyerang yang mencoba banyak akun sekaligus dari satu tempat
         * — pola itu lolos dari batas per-identitas karena tiap percobaan
         * memakai identitas berbeda.
         */
        'batas_percobaan_login_ip' => (int) env('PPID_BATAS_PERCOBAAN_LOGIN_IP', 20),

        /*
         * Captcha gambar pada formulir daftar dan masuk.
         *
         * Dimatikan hanya untuk pengujian otomatis; pada layanan yang terbuka
         * untuk umum ini harus tetap menyala.
         */
        'captcha_aktif' => (bool) env('PPID_CAPTCHA_AKTIF', true),

        /* Honeypot + jeda pengisian minimum pada formulir akun. */
        'perisai_formulir' => (bool) env('PPID_PERISAI_FORMULIR', true),

        /*
         * Jeda antar pengiriman tautan (verifikasi pendaftaran & lupa
         * password), dalam menit.
         *
         * Dihitung terpisah untuk email tujuan, alamat IP, dan penanda
         * perangkat — mengganti salah satunya tidak mengosongkan hitungan yang
         * lain. Ini rem utama terhadap penyalahgunaan kuota kirim email.
         */
        'jeda_kirim_tautan_menit' => (int) env('PPID_JEDA_KIRIM_TAUTAN_MENIT', 30),

        /* Jumlah kegagalan masuk yang memicu satu tahap kunci. */
        'gagal_per_tahap' => (int) env('PPID_GAGAL_PER_TAHAP', 3),

        /*
         * Lama kunci tiap tahap, dalam menit: 1 jam, 24 jam, 72 jam.
         * Tahap di atas daftar ini tetap memakai angka terakhir.
         */
        'tahap_kunci_menit' => [60, 1440, 4320],

        /*
         * Hitungan kegagalan disetel ulang bila sekian jam berlalu tanpa
         * kegagalan baru. Tanpa ini, orang yang lupa password tiga kali tahun
         * lalu akan langsung mendarat di tahap berikutnya.
         */
        'reset_hitungan_gagal_jam' => (int) env('PPID_RESET_HITUNGAN_GAGAL_JAM', 72),

        /* Detik tercepat yang masih dianggap wajar untuk mengisi formulir. */
        'jeda_isi_minimum' => (int) env('PPID_JEDA_ISI_MINIMUM', 3),

        /* Umur maksimum satu formulir yang dibuka, dalam detik (2 jam). */
        'umur_formulir_maksimum' => (int) env('PPID_UMUR_FORMULIR_MAKSIMUM', 7200),

        /*
         * Lama pemeriksaan berkas Verifikasi Data Diri Pemohon, dalam hari
         * kerja. Ditampilkan sebagai janji layanan pada portal pengguna.
         */
        'sla_verifikasi_hari_kerja' => (int) env('PPID_SLA_VERIFIKASI_HARI_KERJA', 14),

        /*
         * Portal ikut terkunci selagi berkas masih diperiksa petugas.
         *
         * Menyala berarti pemohon yang sudah mengirim berkas tetap dihalangi
         * sampai statusnya disetujui — sesuai permintaan "selama belum
         * terverifikasi tidak bisa melakukan apa pun". Matikan bila pemohon
         * sebaiknya tetap boleh melihat riwayatnya selama menunggu, karena pada
         * tahap itu tidak ada lagi yang bisa ia kerjakan.
         */
        'blokir_saat_menunggu' => (bool) env('PPID_BLOKIR_SAAT_MENUNGGU', true),
    ],

];
