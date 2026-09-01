<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Alur bergambar pada halaman Standar Layanan.
 *
 * Prosedur permohonan informasi sudah punya versi teksnya (kartu tahapan dan
 * rincian langkah yang ditulis di template fe-ppid). Yang belum ada adalah
 * gambar alur resmi buatan tim humas — infografis yang memperlihatkan tampilan
 * layarnya, bukan sekadar kalimat. Tabel ini menyimpan gambar-gambar itu
 * beserta urutannya.
 *
 * Barisnya sengaja tidak diikat ke satu halaman saja: kolom `halaman`
 * menentukan halaman Standar Layanan mana yang menayangkannya, sehingga
 * Prosedur Keberatan bisa memakai modul yang sama begitu gambarnya tersedia
 * tanpa perlu tabel kedua.
 *
 * Gambarnya wajib; tanpa gambar barisnya tidak punya isi untuk ditayangkan.
 * Judul dan keterangan hanya pengiring — situs publik menampilkan gambarnya
 * utuh, tidak mengetik ulang isinya.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('alur_prosedur')) {
            return;
        }

        Schema::create('alur_prosedur', function (Blueprint $table) {
            $table->id();
            // Halaman Standar Layanan tujuan, mengikuti slug rutenya di fe-ppid
            // (`/standar-layanan/{slug}`).
            $table->string('halaman', 40)->default('prosedur-permohonan');
            $table->string('judul', 255);
            $table->string('judul_en', 255)->nullable();
            $table->text('keterangan')->nullable();
            $table->text('keterangan_en')->nullable();
            // Path relatif di disk `media` (mis. uploads/alur-prosedur/2026/08/xxx.png).
            $table->string('gambar', 500)->nullable();
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->boolean('is_active')->default(true);

            // Jejak dokumen ditulis di sini karena migrasi traceability
            // (2026_08_14) sudah berjalan sebelum tabel ini ada.
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            // Urutan tayang per halaman adalah satu-satunya cara data ini
            // dibaca situs publik.
            $table->index(['halaman', 'is_active', 'urutan']);

            if (Schema::hasTable('users')) {
                $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
                $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
                $table->foreign('deleted_by')->references('id')->on('users')->nullOnDelete();
            }
        });

        DB::statement('ALTER TABLE alur_prosedur DROP CONSTRAINT IF EXISTS alur_prosedur_halaman_check');
        DB::statement("ALTER TABLE alur_prosedur ADD CONSTRAINT alur_prosedur_halaman_check CHECK (halaman::text = ANY (ARRAY['prosedur-permohonan', 'prosedur-keberatan']::text[]))");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE alur_prosedur DROP CONSTRAINT IF EXISTS alur_prosedur_halaman_check');

        Schema::dropIfExists('alur_prosedur');
    }
};
