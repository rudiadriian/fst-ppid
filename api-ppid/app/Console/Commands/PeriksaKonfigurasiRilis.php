<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Migrations\Migrator;

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

        foreach ($this->migrasiTertunda() as $satu) {
            $masalah[] = $satu;
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

    /**
     * Migrasi yang berkasnya sudah ikut terkirim tetapi belum dijalankan.
     *
     * Keadaan itu tidak meninggalkan jejak sampai ada yang menyimpan sesuatu:
     * kode sudah menyebut kolom baru, basis datanya belum punya, dan yang
     * sampai ke petugas hanya "500 Server Error" tanpa sebab yang terbaca.
     * Karena itu diperiksa di sini — lebih baik deploy berhenti daripada panel
     * hidup dengan skema yang tertinggal.
     *
     * Kegagalan menghubungi basis data tidak diperlakukan sebagai masalah
     * rilis: itu urusan lain, dan sudah terlihat dari mana-mana.
     *
     * @return array<int, string>
     */
    private function migrasiTertunda(): array
    {
        try {
            /** @var Migrator $migrator */
            $migrator = app('migrator');

            $migrator->setConnection(config('database.default'));

            if (!$migrator->repositoryExists()) {
                return ['Tabel migrasi belum ada — jalankan `php artisan migrate --force`.'];
            }

            $sudah = $migrator->getRepository()->getRan();
            $semua = $migrator->getMigrationFiles($migrator->paths() ?: [database_path('migrations')]);

            $tertunda = array_values(array_diff(
                array_map(fn (string $berkas) => $migrator->getMigrationName($berkas), $semua),
                $sudah
            ));

            if ($tertunda === []) {
                return [];
            }

            return [
                'Ada '.count($tertunda).' migrasi yang belum dijalankan: '.implode(', ', $tertunda).'. '
                    .'Jalankan `php artisan migrate --force` — tanpa itu kode yang menyebut kolom baru '
                    .'akan menjawab 500 pada setiap penyimpanan.',
            ];
        } catch (\Throwable $e) {
            $this->warn('Status migrasi tidak dapat diperiksa: '.$e->getMessage());

            return [];
        }
    }
}
