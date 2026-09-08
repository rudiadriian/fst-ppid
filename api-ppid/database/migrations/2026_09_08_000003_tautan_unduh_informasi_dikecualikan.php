<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Alamat salinan pada Daftar Informasi Dikecualikan.
 *
 * Migrasi 2026_09_08_000001 sengaja melewatkan kolom ini karena tidak ada cara
 * menautkan persetujuan permohonan ke baris informasi yang dikecualikan.
 * Sikapnya diubah setelah UAT: pada halaman itu penerbitan berkas memang
 * keputusan petugas yang langsung terbaca publik — Surat Penetapan sudah
 * dilayani apa adanya di sana, tanpa gerbang permohonan. Alamat salinan
 * mengikuti pola yang sama: tombolnya hanya muncul bila petugas mengisinya,
 * dan mengisinya berarti petugas memang menerbitkan salinan itu.
 *
 * Berbeda dengan `informasi_publik.tautan_unduh`, yang tetap dijaga aturan
 * unduhan terbatas — di sana dokumennya milik Daftar Informasi Publik dan
 * salinannya baru keluar setelah permohonan disetujui.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('informasi_dikecualikan', 'tautan_unduh')) {
            return;
        }

        Schema::table('informasi_dikecualikan', function (Blueprint $table) {
            $table->string('tautan_unduh', 500)->nullable()->after('tautan');
        });
    }

    public function down(): void
    {
        Schema::table('informasi_dikecualikan', function (Blueprint $table) {
            $table->dropColumn('tautan_unduh');
        });
    }
};
