<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Teks yang menyertai tiap gambar banner beranda.
 *
 * Slider beranda kini menampilkan judul dan ringkasan milik masing-masing
 * gambar, bukan satu teks tetap dari template. Kolom Inggrisnya boleh kosong —
 * situs publik memakai teks Indonesia bila terjemahannya belum diisi.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('banner_slider', function (Blueprint $table) {
            if (!Schema::hasColumn('banner_slider', 'judul_en')) {
                $table->string('judul_en', 255)->nullable()->after('judul');
            }

            if (!Schema::hasColumn('banner_slider', 'ringkasan')) {
                $table->text('ringkasan')->nullable()->after('judul_en');
            }

            if (!Schema::hasColumn('banner_slider', 'ringkasan_en')) {
                $table->text('ringkasan_en')->nullable()->after('ringkasan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('banner_slider', function (Blueprint $table) {
            $hapus = array_values(array_filter(
                ['judul_en', 'ringkasan', 'ringkasan_en'],
                fn ($kolom) => Schema::hasColumn('banner_slider', $kolom)
            ));

            if ($hapus) {
                $table->dropColumn($hapus);
            }
        });
    }
};
