<?php

namespace Database\Seeders;

use App\Models\LaporanTahunan;
use Illuminate\Database\Seeder;

/**
 * Isi awal modul Laporan Tahunan supaya section-nya terlihat di beranda.
 *
 * Sengaja **tidak** dipanggil `DatabaseSeeder`: isinya data contoh, bukan data
 * resmi. Jalankan sekali secara manual bila modulnya masih kosong —
 *
 *     php artisan db:seed --class=LaporanTahunanSeeder
 *
 * Sampulnya memakai logo perusahaan sebagai penanda sementara. Ganti dengan
 * gambar sampul laporan yang sebenarnya lewat panel (Konten Situs → Laporan
 * Tahunan); begitu diganti, seeder ini tidak perlu dijalankan lagi.
 *
 * Aman diulang: baris dicocokkan menurut tahun bukunya.
 */
class LaporanTahunanSeeder extends Seeder
{
    /** Halaman resmi tempat seluruh Laporan Tahunan dibaca. */
    private const TAUTAN = 'https://foodstation.id/laporan-tahunan-fstj/';

    /**
     * Penanda sementara, bukan berkas unggahan.
     *
     * Diawali garis miring supaya dipakai apa adanya oleh `Cms::url()` dan
     * tidak dicari di dalam `storage/`.
     */
    private const SAMPUL_SEMENTARA = '/assets/images/logo/logo_fs.png';

    public function run(): void
    {
        foreach ([2025, 2024, 2023] as $urutan => $tahun) {
            LaporanTahunan::updateOrCreate(
                ['tahun' => $tahun],
                [
                    'judul' => "Laporan Tahunan {$tahun}",
                    'judul_en' => "Annual Report {$tahun}",
                    'sampul' => self::SAMPUL_SEMENTARA,
                    'tautan' => self::TAUTAN,
                    'urutan' => $urutan,
                    'status' => 'published',
                ]
            );
        }

        $this->command?->info('Laporan Tahunan: 3 tahun buku disiapkan. Ganti sampulnya lewat panel.');
    }
}
