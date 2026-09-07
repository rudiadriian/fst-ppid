<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Periksa konfigurasi yang tidak boleh setengah jadi saat rilis.
 *
 *   php artisan ppid:periksa-konfigurasi
 *
 * Dijalankan pipeline setelah `config:cache`, dan sengaja mengembalikan
 * status gagal supaya deploy berhenti alih-alih menyerahkan panel yang
 * kelihatan hidup tetapi menolak setiap orang yang mencoba masuk.
 *
 * Yang diperiksa hanya hal yang pernah benar-benar terjadi. Nilai yang lupa
 * diisi tidak meninggalkan jejak di log mana pun: `.env` tidak disentuh
 * pipeline, jadi variabel baru yang ditambahkan lewat rilis harus dipasang
 * tangan di server, dan langkah itu mudah terlewat. Akibatnya baru terlihat
 * dari sisi pemakai — pada reCAPTCHA, sebagai penolakan masuk yang tidak bisa
 * dijelaskan tanpa membaca kode.
 */
class PeriksaKonfigurasiRilis extends Command
{
    protected $signature = 'ppid:periksa-konfigurasi';

    protected $description = 'Pastikan konfigurasi wajib terisi sebelum rilis dianggap selesai';

    public function handle(): int
    {
        $masalah = [];

        if (config('ppid.akun.recaptcha_aktif')) {
            if (blank(config('ppid.akun.recaptcha_secret_key'))) {
                $masalah[] = 'PPID_RECAPTCHA_SECRET_KEY kosong padahal PPID_RECAPTCHA_AKTIF menyala. '
                    .'Semua percobaan masuk, lupa password, dan reset password akan ditolak dengan '
                    .'"Verifikasi keamanan belum dikonfigurasi di server."';
            }
        } else {
            // Bukan galat: ada keadaan darurat yang membenarkannya. Tetapi
            // keadaan itu harus terlihat, bukan diam-diam menjadi normal baru.
            $this->warn('PPID_RECAPTCHA_AKTIF mati — formulir auth berjalan tanpa perlindungan bot.');
        }

        if (blank(config('app.key'))) {
            $masalah[] = 'APP_KEY kosong.';
        }

        if ($masalah === []) {
            $this->info('Konfigurasi rilis lengkap.');

            return self::SUCCESS;
        }

        $this->error('Konfigurasi rilis belum lengkap:');

        foreach ($masalah as $satu) {
            $this->line('  - '.$satu);
        }

        $this->newLine();
        $this->line('Perbaiki di '.base_path('.env').' lalu jalankan ulang: php artisan config:cache');

        return self::FAILURE;
    }
}
