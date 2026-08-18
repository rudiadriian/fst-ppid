<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Modul Maklumat Pelayanan Informasi Publik.
 *
 * Isinya bukan teks yang diketik ulang di CMS, melainkan satu berkas maklumat
 * yang sudah ditandatangani (PDF atau gambar hasil pindai). Berkas itulah yang
 * dibaca langsung di situs publik — teks `judul`/`ringkasan` hanya pengantar
 * di atas dokumennya.
 *
 * Barisnya boleh lebih dari satu supaya maklumat lama tetap tersimpan sebagai
 * arsip; situs publik memakai satu baris berstatus `published` yang paling baru.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('maklumat')) {
            return;
        }

        Schema::create('maklumat', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 255);
            $table->string('judul_en', 255)->nullable();
            $table->text('ringkasan')->nullable();
            $table->text('ringkasan_en')->nullable();
            // Path relatif di disk `media` (mis. uploads/maklumat/2026/08/xxx.png).
            $table->string('file_dokumen', 500)->nullable();
            $table->date('tanggal_terbit')->nullable();
            $table->string('status', 20)->default('draft');
            $table->unsignedBigInteger('published_by')->nullable();
            $table->timestamps();

            $table->index(['status', 'tanggal_terbit']);

            if (Schema::hasTable('users')) {
                $table->foreign('published_by')->references('id')->on('users')->nullOnDelete();
            }
        });

        // Batasan nilai status disamakan dengan tabel konten lain.
        DB::statement('ALTER TABLE maklumat DROP CONSTRAINT IF EXISTS maklumat_status_check');
        DB::statement("ALTER TABLE maklumat ADD CONSTRAINT maklumat_status_check CHECK (status::text = ANY (ARRAY['draft', 'published', 'archived']::text[]))");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE maklumat DROP CONSTRAINT IF EXISTS maklumat_status_check');

        Schema::dropIfExists('maklumat');
    }
};
