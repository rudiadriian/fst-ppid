<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Daftar Informasi Dikecualikan di situs publik hanya menampilkan judulnya,
 * sehingga alasan pengecualian tidak lagi wajib diisi petugas.
 *
 * Kolomnya tetap ada — isian lama tidak hilang dan tetap bisa diisi lagi bila
 * suatu saat dibutuhkan untuk arsip internal.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE informasi_dikecualikan ALTER COLUMN alasan_pengecualian DROP NOT NULL');
    }

    public function down(): void
    {
        // Baris tanpa alasan diberi teks pengganti dulu supaya NOT NULL bisa dipasang lagi.
        DB::table('informasi_dikecualikan')
            ->whereNull('alasan_pengecualian')
            ->update(['alasan_pengecualian' => '-']);

        DB::statement('ALTER TABLE informasi_dikecualikan ALTER COLUMN alasan_pengecualian SET NOT NULL');
    }
};
