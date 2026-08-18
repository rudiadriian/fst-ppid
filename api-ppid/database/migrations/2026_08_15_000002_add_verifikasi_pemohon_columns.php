<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Kolom pendukung Verifikasi Data Diri Pemohon oleh petugas.
 *
 * `status_verifikasi` sudah ada sejak awal (belum | menunggu | terverifikasi |
 * ditolak). Yang belum ada adalah jejak keputusannya:
 *
 *   - `jumlah_ditolak`      : berapa kali berkasnya sudah ditolak. Pada
 *                             hitungan ketiga, pemohon tidak boleh mengirim
 *                             ulang lagi. Statusnya tetap `ditolak` — keadaan
 *                             "diblokir" diturunkan dari angka ini, bukan dari
 *                             status baru, supaya CHECK constraint yang sudah
 *                             ada tidak perlu diubah dan data lama tetap sah.
 *   - `catatan_verifikasi`  : alasan petugas, ditampilkan ke pemohon supaya ia
 *                             tahu apa yang harus diperbaiki.
 *   - `diverifikasi_oleh`   : petugas yang memutuskan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pemohon', function (Blueprint $table) {
            if (!Schema::hasColumn('pemohon', 'jumlah_ditolak')) {
                $table->unsignedTinyInteger('jumlah_ditolak')->default(0);
            }

            if (!Schema::hasColumn('pemohon', 'catatan_verifikasi')) {
                $table->text('catatan_verifikasi')->nullable();
            }

            if (!Schema::hasColumn('pemohon', 'diverifikasi_oleh')) {
                $table->foreignId('diverifikasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('pemohon', function (Blueprint $table) {
            if (Schema::hasColumn('pemohon', 'diverifikasi_oleh')) {
                $table->dropConstrainedForeignId('diverifikasi_oleh');
            }

            $lepas = array_values(array_filter(
                ['jumlah_ditolak', 'catatan_verifikasi'],
                fn (string $kolom) => Schema::hasColumn('pemohon', $kolom)
            ));

            if ($lepas !== []) {
                $table->dropColumn($lepas);
            }
        });
    }
};
