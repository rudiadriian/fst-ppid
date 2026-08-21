<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Unduhan terbatas jadi perlakuan bawaan, bukan pengecualian (langkah 83
 * direvisi).
 *
 * Semula penanda ini dipasang per dokumen, dan hanya Annual Report yang
 * menyalakannya. Permintaan yang direvisi memberlakukannya pada seluruh entri
 * Daftar Informasi Publik, Informasi Berkala, Serta Merta, dan Setiap Saat:
 * membaca tetap terbuka lewat tautan yang diisikan petugas, sedangkan salinan
 * untuk diunduh menuntut permohonan yang disetujui.
 *
 * Penandanya **tidak dihapus**. Ia tetap berguna sebagai jalan keluar: dokumen
 * yang memang boleh diunduh siapa saja cukup dimatikan penandanya, tanpa
 * perubahan program. Yang berubah hanya arah bawaannya.
 *
 * Baris lama ikut dinyalakan. Membiarkannya `false` berarti dokumen yang sudah
 * ada diam-diam berperilaku lain dari dokumen yang dibuat setelah hari ini —
 * dua aturan pada satu daftar, tanpa apa pun di layar yang menjelaskan
 * bedanya.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('informasi_publik', function (Blueprint $table) {
            $table->boolean('unduhan_terbatas')->default(true)->change();
        });

        DB::table('informasi_publik')->where('unduhan_terbatas', false)->update([
            'unduhan_terbatas' => true,
        ]);
    }

    public function down(): void
    {
        Schema::table('informasi_publik', function (Blueprint $table) {
            $table->boolean('unduhan_terbatas')->default(false)->change();
        });
    }
};
