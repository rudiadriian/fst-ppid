<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Dua tabel pengaman akun pengunjung.
 *
 * 1. `pengiriman_tautan_akun` — catatan setiap pengiriman tautan verifikasi
 *    pendaftaran dan tautan lupa password, beserta asal permintaannya. Selain
 *    menjadi dasar pembatasan "satu kali per 30 menit", isinya juga jejak bila
 *    suatu saat perlu menelusuri siapa yang menghabiskan kuota email.
 *
 * 2. `percobaan_login_pemohon` — hitungan kegagalan masuk beserta sampai kapan
 *    kombinasi itu dikunci. Disimpan di basis data, bukan cache, karena
 *    kuncinya bisa berlaku sampai 72 jam: cache berkas bisa dibersihkan tanpa
 *    sengaja dan kuncinya ikut hilang.
 *
 * **Catatan MAC address.** Permintaan awal menyebut pencatatan MAC address.
 * Server HTTP tidak pernah menerima MAC address milik pengunjung — yang sampai
 * hanya alamat IP; MAC hanya terlihat oleh perangkat di segmen jaringan yang
 * sama. Karena itu yang dicatat di sini adalah IP ditambah **penanda perangkat**
 * (cookie acak berumur panjang) dan sidik `user_agent`. Penanda perangkat bisa
 * dihapus pengunjung dengan membersihkan cookie, jadi pembatasannya tidak
 * bergantung pada itu sendirian — IP dan email tetap dihitung terpisah.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('pengiriman_tautan_akun')) {
            Schema::create('pengiriman_tautan_akun', function (Blueprint $table) {
                $table->id();
                // 'registrasi' | 'lupa_password'
                $table->string('jenis', 30);
                $table->string('email');
                $table->string('ip_address', 45)->nullable();
                $table->string('penanda_perangkat', 64)->nullable();
                $table->string('user_agent', 255)->nullable();
                $table->timestamp('created_at')->nullable();

                $table->index(['jenis', 'email', 'created_at']);
                $table->index(['jenis', 'ip_address', 'created_at']);
                $table->index(['jenis', 'penanda_perangkat', 'created_at']);
            });
        }

        if (!Schema::hasTable('percobaan_login_pemohon')) {
            Schema::create('percobaan_login_pemohon', function (Blueprint $table) {
                $table->id();
                // Email atau nomor telepon yang diketik, sudah dibakukan.
                $table->string('identitas');
                $table->string('ip_address', 45);
                $table->string('penanda_perangkat', 64)->nullable();
                $table->unsignedInteger('jumlah_gagal')->default(0);
                // 0 = belum pernah dikunci, 1 = 1 jam, 2 = 24 jam, 3 = 72 jam.
                $table->unsignedTinyInteger('tahap_kunci')->default(0);
                $table->timestamp('terkunci_sampai')->nullable();
                $table->timestamp('terakhir_gagal_pada')->nullable();
                $table->timestamps();

                $table->unique(['identitas', 'ip_address']);
                $table->index('terkunci_sampai');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('percobaan_login_pemohon');
        Schema::dropIfExists('pengiriman_tautan_akun');
    }
};
