<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sebagian entri Informasi Publik tidak berupa berkas unggahan, melainkan
 * menunjuk ke halaman lain (mis. profil perusahaan di situs korporat).
 * Kolom ini menampung tautan tersebut; tombol aksi di situs memakai `tautan`
 * bila berkas tidak ada.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('informasi_publik', function (Blueprint $table) {
            $table->string('tautan', 500)->nullable()->after('konten');
        });
    }

    public function down(): void
    {
        Schema::table('informasi_publik', function (Blueprint $table) {
            $table->dropColumn('tautan');
        });
    }
};
