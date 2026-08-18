<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Kosongkan kolom "Diubah" pada baris yang belum pernah disunting.
 *
 * Sebelum jejak dokumen ada, Laravel selalu menyamakan `updated_at` dengan
 * `created_at` saat baris dibuat, dan seeder ikut menstempelnya massal. Akibatnya
 * kolom "Diubah pada" di panel terisi pada semua baris walau belum ada satu pun
 * aktivitas Ubah — angka yang tidak berarti apa-apa.
 *
 * Syaratnya `updated_by IS NULL`: sejak trait MencatatPelaku aktif, setiap
 * perubahan isi yang sungguhan selalu meninggalkan pelaku di `updated_by`. Baris
 * tanpa pelaku berarti tidak pernah disunting lewat panel, jadi aman dikosongkan.
 * Baris yang sudah pernah diubah tidak tersentuh, termasuk bila migrasi ini
 * dijalankan lagi di lingkungan lain yang datanya sudah hidup.
 */
return new class extends Migration
{
    private const TABEL = [
        'kategori_informasi',
        'informasi_publik',
        'informasi_dikecualikan',
        'pemohon',
        'permohonan_informasi',
        'keberatan_informasi',
        'laporan_layanan',
        'survey_kepuasan',
        'kategori_berita',
        'berita',
        'galeri',
        'faq',
        'banner_slider',
        'struktur_organisasi',
        'halaman_statis',
        'maklumat',
        'regulasi',
        'menu_navigasi',
        'tautan_terkait',
        'pengaturan_situs',
        'roles',
        'users',
    ];

    public function up(): void
    {
        foreach (self::TABEL as $tabel) {
            if (!Schema::hasTable($tabel) || !Schema::hasColumn($tabel, 'updated_by')) {
                continue;
            }

            DB::table($tabel)
                ->whereNull('updated_by')
                ->whereNotNull('updated_at')
                ->update(['updated_at' => null]);
        }
    }

    /**
     * Tidak bisa dikembalikan: nilai `updated_at` lama tidak disimpan di mana
     * pun. Isinya memang cap waktu semu (sama dengan waktu pembuatan atau waktu
     * seeder berjalan), bukan riwayat penyuntingan yang hilang.
     */
    public function down(): void
    {
    }
};
