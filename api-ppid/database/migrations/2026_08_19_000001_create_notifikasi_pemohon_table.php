<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Lonceng notifikasi Portal Pemohon.
 *
 * Kembaran `notifikasi` (lonceng panel admin), tetapi penerimanya baris di
 * tabel `pemohon`, bukan `users`. Dipisah — bukan ditumpuk di satu tabel
 * dengan kolom "jenis penerima" — karena kunci asingnya berbeda: satu ke
 * `users`, satu ke `pemohon`. Menyatukannya berarti melepas kedua constraint
 * itu dan menjaga keutuhannya lewat kode.
 *
 * Isinya ditulis api-ppid setiap petugas memberi umpan balik (status pengajuan
 * berpindah, berkas tanggapan dilampirkan, hasil verifikasi data diri
 * diputuskan) dan dibaca fe-ppid di `GET /akun/notifikasi`.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('notifikasi_pemohon')) {
            return;
        }

        Schema::create('notifikasi_pemohon', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemohon_id')->constrained('pemohon')->cascadeOnDelete();
            $table->string('type', 50)->nullable();
            $table->text('message');
            $table->boolean('is_read')->default(false);
            // Judul, ikon, tautan, dan id pengajuan terkait — sama seperti
            // `notifikasi.data` supaya kedua lonceng bisa dibaca dengan pola
            // yang sama.
            $table->jsonb('data')->nullable();
            $table->timestampTz('created_at')->nullable();

            $table->index(['pemohon_id', 'is_read']);
            $table->index(['pemohon_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi_pemohon');
    }
};
