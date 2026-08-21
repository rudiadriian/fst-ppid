<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Dokumen yang boleh dilihat siapa saja, tetapi hanya boleh diunduh setelah
 * permohonannya disetujui petugas (langkah 83).
 *
 * Dua kolom, dua sisi dari satu aturan:
 *
 * 1. `informasi_publik.unduhan_terbatas` — menandai dokumen mana yang tunduk
 *    pada aturan itu. Dibuat sebagai penanda per dokumen, bukan daftar nama
 *    yang dipaku di kode, supaya petugas bisa menerapkannya pada dokumen lain
 *    tanpa menunggu perubahan program.
 *
 * 2. `permohonan_informasi.informasi_publik_id` — permohonan ini meminta
 *    dokumen yang mana. Tanpa kolom ini tidak ada cara memeriksa "sudah pernah
 *    disetujui untuk dokumen ini": `rincian_informasi` hanya teks bebas, dan
 *    mencocokkan judul di dalamnya adalah tebakan, bukan pemeriksaan.
 *
 * Nullable, karena sebagian besar permohonan memang tidak menunjuk dokumen
 * tertentu — pemohon menuliskan sendiri informasi apa yang dicarinya.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('informasi_publik', function (Blueprint $table) {
            if (!Schema::hasColumn('informasi_publik', 'unduhan_terbatas')) {
                $table->boolean('unduhan_terbatas')->default(false);
            }
        });

        Schema::table('permohonan_informasi', function (Blueprint $table) {
            if (!Schema::hasColumn('permohonan_informasi', 'informasi_publik_id')) {
                $table->unsignedBigInteger('informasi_publik_id')->nullable();

                /*
                 * `nullOnDelete`, bukan `cascadeOnDelete`: permohonan adalah
                 * catatan layanan yang sudah terjadi. Menghapus dokumennya dari
                 * daftar tidak boleh menghapus riwayat bahwa seseorang pernah
                 * memintanya dan petugas pernah memutuskannya.
                 */
                $table->foreign('informasi_publik_id')
                    ->references('id')
                    ->on('informasi_publik')
                    ->nullOnDelete();

                $table->index(['informasi_publik_id', 'pemohon_id', 'status']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('permohonan_informasi', function (Blueprint $table) {
            if (Schema::hasColumn('permohonan_informasi', 'informasi_publik_id')) {
                $table->dropForeign(['informasi_publik_id']);
                $table->dropIndex(['informasi_publik_id', 'pemohon_id', 'status']);
                $table->dropColumn('informasi_publik_id');
            }
        });

        Schema::table('informasi_publik', function (Blueprint $table) {
            if (Schema::hasColumn('informasi_publik', 'unduhan_terbatas')) {
                $table->dropColumn('unduhan_terbatas');
            }
        });
    }
};
