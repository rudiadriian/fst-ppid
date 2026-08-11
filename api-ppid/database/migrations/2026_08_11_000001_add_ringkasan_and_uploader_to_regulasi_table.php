<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Halaman Regulasi di situs publik menampilkan ringkasan dokumen dan nama
 * pengunggahnya, jadi keduanya perlu tempat sendiri di tabel.
 *
 * `uploaded_by` sengaja `nullOnDelete`: pengguna yang dihapus tidak boleh
 * menyeret baris regulasinya ikut hilang.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('regulasi', function (Blueprint $table) {
            if (!Schema::hasColumn('regulasi', 'ringkasan')) {
                $table->text('ringkasan')->nullable()->after('judul');
            }

            if (!Schema::hasColumn('regulasi', 'uploaded_by')) {
                $table->foreignId('uploaded_by')->nullable()->after('file_path')
                    ->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('regulasi', function (Blueprint $table) {
            if (Schema::hasColumn('regulasi', 'uploaded_by')) {
                $table->dropConstrainedForeignId('uploaded_by');
            }

            if (Schema::hasColumn('regulasi', 'ringkasan')) {
                $table->dropColumn('ringkasan');
            }
        });
    }
};
