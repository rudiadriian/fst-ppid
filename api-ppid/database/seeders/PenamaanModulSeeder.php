<?php

namespace Database\Seeders;

use App\Models\ModulSistem;
use Illuminate\Database\Seeder;

/**
 * Samakan nama modul di tabel `modul_sistem` dengan label di panel.
 *
 * Nama inilah yang tampil pada matrix hak akses role, jadi kalau tidak ikut
 * dipendekkan, satu modul bisa terbaca dua nama berbeda: satu di menu, satu di
 * halaman Role.
 *
 * **Slug tidak disentuh** — slug adalah kunci pemeriksaan hak akses
 * (`akses:{slug},{aksi}`); mengubahnya akan memutus akses yang sudah tersimpan.
 */
class PenamaanModulSeeder extends Seeder
{
    /** slug => nama baru */
    private const NAMA = [
        'permohonan' => 'Permohonan',
        'keberatan' => 'Keberatan',
        'laporan-layanan' => 'Laporan',
        'banner-slider' => 'Banner',
        'struktur-organisasi' => 'Struktur',
        'halaman-statis' => 'Halaman',
        'regulasi' => 'Regulasi',
        'tautan-terkait' => 'Tautan',
        'menu-navigasi' => 'Navigasi',
        'pengguna' => 'Pengguna, Role & Modul',
        'pengaturan-situs' => 'Pengaturan',
        'audit-log' => 'Audit',
    ];

    public function run(): void
    {
        $ubah = 0;

        foreach (self::NAMA as $slug => $nama) {
            $modul = ModulSistem::where('slug', $slug)->first();

            if (!$modul) {
                $this->command?->warn("Lewati (modul tidak ada): $slug");
                continue;
            }

            if ($modul->nama === $nama) {
                continue;
            }

            $modul->forceFill(['nama' => $nama])->save();
            $ubah++;
        }

        $this->command?->info("Nama modul disesuaikan: $ubah baris.");
    }
}
