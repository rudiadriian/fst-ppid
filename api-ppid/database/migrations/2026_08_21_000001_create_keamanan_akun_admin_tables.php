<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pengaman akun petugas/admin panel (`users`).
 *
 * Sengaja dibuat terpisah dari tabel serupa milik akun pengunjung
 * (`percobaan_login_pemohon`, `pengiriman_tautan_akun` — dipakai fe-ppid).
 * Aturannya berbeda: akun panel naik ke suspend permanen, akun pengunjung
 * tidak; dan hitungan satu jenis akun tidak boleh mengunci jenis akun lain.
 *
 * **Catatan MAC address.** Sama seperti pada tabel akun pengunjung: server HTTP
 * tidak pernah menerima MAC address milik klien — yang sampai hanya alamat IP.
 * Yang dicatat di sini IP dan sidik `user_agent`.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('percobaan_login_admin')) {
            Schema::create('percobaan_login_admin', function (Blueprint $table) {
                $table->id();
                // Email yang diketik, sudah dibakukan huruf kecil.
                $table->string('identitas');
                $table->string('ip_address', 45);
                $table->string('user_agent', 255)->nullable();
                $table->unsignedInteger('jumlah_gagal')->default(0);
                // 1 = 1 jam, 2 = 1 hari, 3 = 14 hari, 4 = akun disuspend.
                $table->unsignedTinyInteger('tahap_kunci')->default(0);
                $table->timestamp('terkunci_sampai')->nullable();
                $table->timestamp('terakhir_gagal_pada')->nullable();
                $table->timestamps();

                $table->unique(['identitas', 'ip_address']);
                $table->index('terkunci_sampai');
            });
        }

        if (!Schema::hasTable('pengiriman_tautan_admin')) {
            Schema::create('pengiriman_tautan_admin', function (Blueprint $table) {
                $table->id();
                // Saat ini hanya 'lupa_password'; kolomnya tetap disediakan agar
                // jenis tautan lain bisa menumpang tabel yang sama.
                $table->string('jenis', 30);
                $table->string('email');
                $table->string('ip_address', 45)->nullable();
                $table->string('user_agent', 255)->nullable();
                // Hitungan bertingkat untuk permintaan tautan, terpisah dari
                // hitungan gagal masuk: menebak password dan membanjiri kotak
                // masuk orang adalah dua penyalahgunaan yang berbeda.
                $table->timestamp('created_at')->nullable();

                $table->index(['jenis', 'email', 'created_at']);
                $table->index(['jenis', 'ip_address', 'created_at']);
            });
        }

        if (!Schema::hasTable('percobaan_tautan_admin')) {
            Schema::create('percobaan_tautan_admin', function (Blueprint $table) {
                $table->id();
                $table->string('identitas');
                $table->string('ip_address', 45);
                $table->unsignedInteger('jumlah_minta')->default(0);
                $table->unsignedTinyInteger('tahap_kunci')->default(0);
                $table->timestamp('terkunci_sampai')->nullable();
                $table->timestamp('terakhir_minta_pada')->nullable();
                $table->timestamps();

                $table->unique(['identitas', 'ip_address']);
                $table->index('terkunci_sampai');
            });
        }

        Schema::table('users', function (Blueprint $table) {
            /*
             * Dibedakan dari `is_active`.
             *
             * `is_active` adalah keputusan administratif — pegawai pindah, akun
             * dinonaktifkan. `disuspend_pada` adalah hasil pengamanan otomatis
             * setelah percobaan masuk yang berulang. Keduanya sama-sama
             * menutup akses, tetapi hanya yang kedua yang boleh dibuka kembali
             * dengan alasan "ini pemiliknya sendiri yang lupa password", dan
             * hanya yang kedua yang perlu dijelaskan ke pengguna sebagai
             * pemblokiran keamanan.
             */
            if (!Schema::hasColumn('users', 'disuspend_pada')) {
                $table->timestampTz('disuspend_pada')->nullable();
            }

            if (!Schema::hasColumn('users', 'alasan_suspend')) {
                $table->string('alasan_suspend', 255)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'alasan_suspend')) {
                $table->dropColumn('alasan_suspend');
            }

            if (Schema::hasColumn('users', 'disuspend_pada')) {
                $table->dropColumn('disuspend_pada');
            }
        });

        Schema::dropIfExists('percobaan_tautan_admin');
        Schema::dropIfExists('pengiriman_tautan_admin');
        Schema::dropIfExists('percobaan_login_admin');
    }
};
