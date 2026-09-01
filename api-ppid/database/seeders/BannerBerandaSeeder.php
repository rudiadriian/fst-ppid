<?php

namespace Database\Seeders;

use App\Models\BannerSlider;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Banner beranda awal.
 *
 * Berkas acuannya (`HOME 1920 x 1080.png`) disalin ke disk `media` seperti hasil
 * unggahan biasa, lalu dicatat sebagai satu banner aktif — sehingga hero beranda
 * langsung memakai gambar resminya, bukan jatuh ke gradasi hijau polos.
 *
 * Judul dan ringkasannya ikut disimpan di baris ini, bukan ditulis di template.
 * Hero beranda memang membaca teks per slide dari modul Banner; teks bawaan di
 * template hanya cadangan untuk banner yang sengaja dibiarkan tanpa judul.
 * Menaruhnya di sini membuat humas bisa menggantinya tanpa deploy.
 *
 * Idempoten: kalau sudah ada banner, seeder berhenti — banner yang sudah
 * disusun petugas tidak boleh tergeser oleh seeder yang kebetulan dijalankan
 * ulang.
 *
 *   php artisan db:seed --class=BannerBerandaSeeder
 */
class BannerBerandaSeeder extends Seeder
{
    private const JUDUL = 'Selamat Datang di Portal Resmi PPID Food Station.';

    private const RINGKASAN = 'Dikelola oleh Pejabat Pengelola Informasi dan Dokumentasi (PPID) PT Food Station Tjipinang Jaya (Perseroda). Informasi yang disediakan diharapkan dapat digunakan secara bijak dan dimanfaatkan untuk kepentingan masyarakat sesuai dengan ketentuan peraturan perundang-undangan yang berlaku.';

    public function run(): void
    {
        if (BannerSlider::exists()) {
            $this->command?->info('Banner beranda sudah ada — dilewati.');

            return;
        }

        $sumber = base_path('../HOME 1920 x 1080.png');

        if (!is_file($sumber)) {
            $this->command?->warn('Berkas "HOME 1920 x 1080.png" tidak ditemukan di folder proyek — banner tidak dibuat.');

            return;
        }

        $path = 'uploads/banner/'.now()->format('Y/m').'/'.Str::random(32).'.png';

        Storage::disk('media')->put($path, file_get_contents($sumber));

        BannerSlider::create([
            'judul' => self::JUDUL,
            'ringkasan' => self::RINGKASAN,
            'gambar' => $path,
            'urutan' => 1,
            'is_active' => true,
        ]);

        $this->command?->info("Banner beranda dibuat: $path");
    }
}
