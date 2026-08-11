<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Token reset password akun pengunjung (tabel `pemohon`).
 *
 * Sengaja terpisah dari `password_reset_tokens` milik akun petugas/admin
 * (tabel `users`) supaya token satu jenis akun tidak pernah bisa dipakai
 * untuk mengambil alih akun jenis lain.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('password_reset_tokens_pemohon')) {
            Schema::create('password_reset_tokens_pemohon', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        // Kolom akun pada tabel `pemohon`. Sudah ada pada skema saat ini,
        // dicek supaya migrasi tetap aman dijalankan di database kosong.
        Schema::table('pemohon', function (Blueprint $table) {
            if (!Schema::hasColumn('pemohon', 'password')) {
                $table->string('password')->nullable();
            }

            if (!Schema::hasColumn('pemohon', 'email_verified_at')) {
                $table->timestampTz('email_verified_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('password_reset_tokens_pemohon');
    }
};
