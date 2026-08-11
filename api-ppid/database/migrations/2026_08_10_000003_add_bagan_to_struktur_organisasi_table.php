<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Kolom pembentuk Bagan Struktur Organisasi PPID.
 *
 * Tabel `struktur_organisasi` sebelumnya hanya daftar datar (nama, jabatan,
 * foto). Tiga kolom di bawah membuatnya jadi pohon yang bisa digambar:
 *
 *  - `parent_id`  : induk kotak; kosong berarti kotak paling atas.
 *  - `tipe_node`  : `utama`   → kotak pada alur vertikal (bergaris panah),
 *                   `samping` → kotak di samping induk, garis putus-putus,
 *                   `grup`    → bingkai berjudul yang membungkus anak-anaknya
 *                               secara berjajar (mis. "PPID Pelaksana").
 *  - `poin`       : daftar butir isi kotak, satu butir per baris. Dipakai bila
 *                   isinya bukan satu nama (mis. Tim Pertimbangan PPID).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('struktur_organisasi', function (Blueprint $table) {
            if (!Schema::hasColumn('struktur_organisasi', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->foreign('parent_id')
                    ->references('id')->on('struktur_organisasi')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('struktur_organisasi', 'tipe_node')) {
                $table->string('tipe_node', 20)->default('utama');
            }

            if (!Schema::hasColumn('struktur_organisasi', 'poin')) {
                $table->text('poin')->nullable();
            }
        });

        DB::statement('ALTER TABLE struktur_organisasi DROP CONSTRAINT IF EXISTS struktur_organisasi_tipe_node_check');
        DB::statement("ALTER TABLE struktur_organisasi ADD CONSTRAINT struktur_organisasi_tipe_node_check CHECK (tipe_node IS NULL OR tipe_node::text = ANY (ARRAY['utama', 'samping', 'grup']::text[]))");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE struktur_organisasi DROP CONSTRAINT IF EXISTS struktur_organisasi_tipe_node_check');

        Schema::table('struktur_organisasi', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn(['parent_id', 'tipe_node', 'poin']);
        });
    }
};
