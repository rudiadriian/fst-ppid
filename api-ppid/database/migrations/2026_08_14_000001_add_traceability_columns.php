<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Jejak dokumen (traceability) untuk seluruh modul CMS.
 *
 * Setiap tabel modul mendapat enam kolom yang menjawab "siapa dan kapan":
 *   - `created_at`  / `created_by`  — dibuat
 *   - `updated_at`  / `updated_by`  — diubah terakhir
 *   - `deleted_at`  / `deleted_by`  — dihapus
 *
 * `deleted_at` sekaligus mengubah penghapusan menjadi soft delete. Tanpa itu
 * `deleted_by` tidak ada gunanya: barisnya sudah lenyap sebelum sempat dicatat.
 * Situs publik tidak ikut menampilkan baris terhapus karena model di fe-ppid
 * juga memakai SoftDeletes.
 *
 * Kolom pelaku memakai `nullOnDelete`: pengguna yang dihapus tidak boleh
 * menyeret dokumen yang pernah disentuhnya ikut hilang.
 *
 * Baris lama tidak diisi mundur — nilainya dibiarkan NULL dan ditampilkan
 * sebagai "—" di panel. Menebak pelaku/waktu untuk data lama justru merusak
 * nilai jejaknya.
 */
return new class extends Migration
{
    /**
     * Tabel modul CMS yang mendapat jejak penuh.
     *
     * `modul_sistem` sengaja tidak ikut: isinya daftar modul yang di-seed, bukan
     * dokumen yang disunting operator, dan soft delete akan membuat seeder
     * membuat ulang baris yang slug-nya sudah dipakai.
     */
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

    private const PELAKU = ['created_by', 'updated_by', 'deleted_by'];

    public function up(): void
    {
        foreach (self::TABEL as $tabel) {
            if (!Schema::hasTable($tabel)) {
                continue;
            }

            Schema::table($tabel, function (Blueprint $table) use ($tabel) {
                if (!Schema::hasColumn($tabel, 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }

                if (!Schema::hasColumn($tabel, 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }

                if (!Schema::hasColumn($tabel, 'deleted_at')) {
                    $table->softDeletes();
                }

                foreach (self::PELAKU as $kolom) {
                    if (!Schema::hasColumn($tabel, $kolom)) {
                        $table->foreignId($kolom)->nullable()->constrained('users')->nullOnDelete();
                    }
                }
            });
        }
    }

    /**
     * Hanya kolom pelaku yang dilepas.
     *
     * `created_at`/`updated_at`/`deleted_at` dibiarkan: sebagian tabel sudah
     * memilikinya sejak awal dan migrasi ini tidak menyimpan catatan mana yang
     * baru, jadi menghapusnya berisiko membuang kolom milik skema lama.
     */
    public function down(): void
    {
        foreach (self::TABEL as $tabel) {
            if (!Schema::hasTable($tabel)) {
                continue;
            }

            Schema::table($tabel, function (Blueprint $table) use ($tabel) {
                foreach (self::PELAKU as $kolom) {
                    // `updated_by` pada halaman_statis sudah ada sebelum migrasi
                    // ini, jadi tidak boleh ikut dilepas.
                    if ($tabel === 'halaman_statis' && $kolom === 'updated_by') {
                        continue;
                    }

                    if (Schema::hasColumn($tabel, $kolom)) {
                        $table->dropConstrainedForeignId($kolom);
                    }
                }
            });
        }
    }
};
