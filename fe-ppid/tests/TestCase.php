<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Tolak berjalan bila tesnya akan membangun ulang basis data sungguhan.
     *
     * Ini bukan kehati-hatian teoretis. Basis data `ppiddb` sudah dua kali
     * habis karena hal yang persis sama: berkas tes peninggalan Breeze memakai
     * `RefreshDatabase`, sementara `phpunit.xml` proyek ini **tidak** menunjuk
     * basis data terpisah — baris `DB_CONNECTION=sqlite` di sana dikomentari.
     * Akibatnya `RefreshDatabase` menjalankan `migrate:fresh` pada basis data
     * yang sedang dipakai, dan seluruh tabel PPID — yang sebagian besar dibuat
     * lewat DDL manual, bukan migrasi — lenyap beserta isinya.
     *
     * Kerugiannya tidak seimbang: satu perintah `php artisan test` yang tampak
     * tidak berbahaya menghapus permohonan, akun pemohon, dan berita yang tidak
     * ada backup-nya. Karena itu penjagaannya dipasang di sini, di kelas induk
     * yang dilewati setiap tes, bukan diserahkan pada ingatan orang yang
     * menulis tes berikutnya.
     *
     * Tes yang butuh menulis ke basis data memakai `DatabaseTransactions`,
     * yang menggulung balik perubahannya dan tidak pernah menyentuh skema.
     */
    protected function setUp(): void
    {
        parent::setUp();

        if (!in_array(RefreshDatabase::class, class_uses_recursive(static::class), true)) {
            return;
        }

        $koneksi = config('database.default');
        $nama = (string) config("database.connections.{$koneksi}.database");

        // Basis data dalam memori atau berkas sementara memang untuk dibuang.
        if ($nama === ':memory:' || str_contains($nama, 'test')) {
            return;
        }

        $this->fail(
            static::class." memakai RefreshDatabase pada basis data '{$nama}'.\n\n"
            ."RefreshDatabase menjalankan migrate:fresh — seluruh tabel dan isinya akan hilang, "
            ."dan sebagian tabel PPID tidak dapat dibangun ulang dari migrasi.\n\n"
            ."Pakai DatabaseTransactions, atau arahkan phpunit.xml ke basis data uji tersendiri."
        );
    }
}
