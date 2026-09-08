<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Dua pintu untuk satu dokumen: dibaca lewat tautan, disalin lewat permohonan.
 *
 * Sampai sekarang entri Daftar Informasi Publik hanya punya satu kolom tautan
 * (`tautan`), yang dipakai tombol "Di Lihat Saja". Tombol unduhnya hanya muncul
 * bila ada berkas yang diunggah — padahal sebagian dokumen (mis. Laporan
 * Tahunan) salinannya sudah tersedia di situs korporat dan tidak perlu diunggah
 * ulang. `tautan_unduh` menampung alamat salinan itu; siapa boleh membukanya
 * tetap ditentukan aturan unduhan terbatas yang sudah ada, bukan oleh kolom ini.
 *
 * `informasi_dikecualikan` mendapat pasangan kolom yang sama supaya kedua
 * daftar di situs publik berperilaku serupa.
 *
 * `laporan_tahunan` adalah modul tersendiri: isinya sampul laporan per tahun
 * buku yang tampil sebagai galeri di beranda. Sengaja tidak menumpang
 * `informasi_publik` — daftar itu terikat klasifikasi UU No. 14 Tahun 2008 dan
 * tidak punya (serta tidak perlu) kolom gambar sampul.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('informasi_publik', 'tautan_unduh')) {
            Schema::table('informasi_publik', function (Blueprint $table) {
                $table->string('tautan_unduh', 500)->nullable()->after('tautan');
            });
        }

        /*
         * `informasi_dikecualikan` hanya mendapat `tautan`.
         *
         * Pasangan `tautan_unduh` sengaja tidak ikut: alamat salinan baru boleh
         * dibuka setelah permohonan atas dokumen itu disetujui, dan
         * `permohonan_informasi` hanya mengenal `informasi_publik_id` — tidak
         * ada kolom yang menautkan sebuah permohonan ke baris informasi yang
         * dikecualikan. Menambahkan kolomnya tanpa penautan itu berarti
         * memasang alamat yang tidak pernah punya penjaga.
         */
        if (!Schema::hasColumn('informasi_dikecualikan', 'tautan')) {
            Schema::table('informasi_dikecualikan', function (Blueprint $table) {
                $table->string('tautan', 500)->nullable()->after('ringkasan_en');
            });
        }

        if (!Schema::hasTable('laporan_tahunan')) {
            Schema::create('laporan_tahunan', function (Blueprint $table) {
                $table->id();
                $table->smallInteger('tahun');
                $table->string('judul', 255);
                $table->string('judul_en', 255)->nullable();
                $table->string('sampul', 500);
                $table->string('tautan', 500)->nullable();
                $table->integer('urutan')->default(0);
                $table->string('status', 20)->default('draft');

                // Jejak dokumen, sama seperti tabel modul CMS lainnya
                // (2026_08_14_000001). Ditulis di sini karena tabelnya baru.
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->softDeletes();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();

                $table->index(['status', 'urutan']);
            });

            DB::statement(
                "ALTER TABLE laporan_tahunan ADD CONSTRAINT laporan_tahunan_status_check ".
                "CHECK (status IN ('draft', 'published', 'archived'))"
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_tahunan');

        Schema::table('informasi_dikecualikan', function (Blueprint $table) {
            $table->dropColumn('tautan');
        });

        Schema::table('informasi_publik', function (Blueprint $table) {
            $table->dropColumn('tautan_unduh');
        });
    }
};
