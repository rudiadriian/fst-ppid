<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Alur layanan PPID: jabatan pengguna, jalur pelayanan, dan perpanjangan SLA.
 *
 * Tiga penambahan yang saling terkait (langkah 89):
 *
 * 1. `users.struktur_id` — mengikat akun panel ke kotak pada bagan struktur
 *    organisasi. Role sudah menentukan *boleh apa*; kolom ini menentukan
 *    *siapa dalam bagan*, yang dibutuhkan begitu satu role dipegang beberapa
 *    orang dengan jabatan berbeda: tiga anggota PPID Pelaksana sama-sama
 *    berrole `ppid-pelaksana`, tetapi hanya bagan yang membedakan Pengelolaan
 *    & Dokumentasi Informasi dari Penyediaan Informasi.
 *
 * 2. `jalur_pelayanan` pada permohonan dan keberatan — Online (dokumen dikirim
 *    lewat surel) atau Langsung (pemohon diundang hadir). Keduanya menghasilkan
 *    tindak lanjut yang berbeda, jadi jalurnya harus tercatat, bukan
 *    disimpulkan dari `cara_pengiriman` yang isinya soal bentuk kiriman.
 *
 * 3. Kolom perpanjangan SLA — batas waktu awal sudah ada di
 *    `batas_waktu_tanggapan`; yang belum ada adalah jejak perpanjangannya.
 *    UU KIP mengizinkan tambahan 7 hari kerja untuk permohonan, dan keberatan
 *    punya batas 30 hari sejak diregistrasi. Tanpa kolom ini perpanjangan
 *    hanya tampak sebagai batas waktu yang berubah tanpa sebab.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'struktur_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('struktur_id')->nullable()->after('role_id')
                    ->constrained('struktur_organisasi')->nullOnDelete();
            });
        }

        if (Schema::hasTable('permohonan_informasi')) {
            Schema::table('permohonan_informasi', function (Blueprint $table) {
                if (!Schema::hasColumn('permohonan_informasi', 'jalur_pelayanan')) {
                    $table->string('jalur_pelayanan', 20)->nullable()->after('cara_pengiriman');
                }

                if (!Schema::hasColumn('permohonan_informasi', 'batas_waktu_awal')) {
                    // Batas pertama disimpan terpisah supaya perpanjangan tidak
                    // menghapus jejak tenggat aslinya.
                    $table->timestampTz('batas_waktu_awal')->nullable()->after('batas_waktu_tanggapan');
                }

                if (!Schema::hasColumn('permohonan_informasi', 'diperpanjang_pada')) {
                    $table->timestampTz('diperpanjang_pada')->nullable()->after('batas_waktu_awal');
                }

                if (!Schema::hasColumn('permohonan_informasi', 'alasan_perpanjangan')) {
                    $table->text('alasan_perpanjangan')->nullable()->after('diperpanjang_pada');
                }

                if (!Schema::hasColumn('permohonan_informasi', 'jadwal_layanan')) {
                    // Tanggal dan jam undangan bagi pemohon jalur Langsung.
                    $table->timestampTz('jadwal_layanan')->nullable()->after('alasan_perpanjangan');
                }

                if (!Schema::hasColumn('permohonan_informasi', 'keterangan_petugas')) {
                    $table->text('keterangan_petugas')->nullable()->after('jadwal_layanan');
                }
            });

            DB::statement('ALTER TABLE permohonan_informasi DROP CONSTRAINT IF EXISTS permohonan_informasi_jalur_pelayanan_check');
            DB::statement("ALTER TABLE permohonan_informasi ADD CONSTRAINT permohonan_informasi_jalur_pelayanan_check CHECK (jalur_pelayanan IS NULL OR jalur_pelayanan::text = ANY (ARRAY['online', 'langsung']::text[]))");
        }

        if (Schema::hasTable('keberatan_informasi')) {
            Schema::table('keberatan_informasi', function (Blueprint $table) {
                if (!Schema::hasColumn('keberatan_informasi', 'jalur_pelayanan')) {
                    $table->string('jalur_pelayanan', 20)->nullable()->after('status');
                }

                if (!Schema::hasColumn('keberatan_informasi', 'batas_waktu_tanggapan')) {
                    // Keberatan belum punya tenggat sama sekali; 30 hari sejak
                    // registrasi baru bisa ditagih kalau tanggalnya tercatat.
                    $table->timestampTz('batas_waktu_tanggapan')->nullable()->after('tanggal_keberatan');
                }

                if (!Schema::hasColumn('keberatan_informasi', 'batas_waktu_sengketa')) {
                    // 14 hari kerja setelah tanggapan: batas pemohon membawa
                    // perkaranya ke Komisi Informasi.
                    $table->timestampTz('batas_waktu_sengketa')->nullable()->after('batas_waktu_tanggapan');
                }

                if (!Schema::hasColumn('keberatan_informasi', 'jadwal_layanan')) {
                    $table->timestampTz('jadwal_layanan')->nullable()->after('batas_waktu_sengketa');
                }

                if (!Schema::hasColumn('keberatan_informasi', 'keterangan_petugas')) {
                    $table->text('keterangan_petugas')->nullable()->after('jadwal_layanan');
                }
            });

            DB::statement('ALTER TABLE keberatan_informasi DROP CONSTRAINT IF EXISTS keberatan_informasi_jalur_pelayanan_check');
            DB::statement("ALTER TABLE keberatan_informasi ADD CONSTRAINT keberatan_informasi_jalur_pelayanan_check CHECK (jalur_pelayanan IS NULL OR jalur_pelayanan::text = ANY (ARRAY['online', 'langsung']::text[]))");
        }
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE permohonan_informasi DROP CONSTRAINT IF EXISTS permohonan_informasi_jalur_pelayanan_check');
        DB::statement('ALTER TABLE keberatan_informasi DROP CONSTRAINT IF EXISTS keberatan_informasi_jalur_pelayanan_check');

        if (Schema::hasTable('permohonan_informasi')) {
            Schema::table('permohonan_informasi', function (Blueprint $table) {
                $table->dropColumn(array_values(array_filter(
                    ['jalur_pelayanan', 'batas_waktu_awal', 'diperpanjang_pada', 'alasan_perpanjangan', 'jadwal_layanan', 'keterangan_petugas'],
                    fn ($kolom) => Schema::hasColumn('permohonan_informasi', $kolom)
                )));
            });
        }

        if (Schema::hasTable('keberatan_informasi')) {
            Schema::table('keberatan_informasi', function (Blueprint $table) {
                $table->dropColumn(array_values(array_filter(
                    ['jalur_pelayanan', 'batas_waktu_tanggapan', 'batas_waktu_sengketa', 'jadwal_layanan', 'keterangan_petugas'],
                    fn ($kolom) => Schema::hasColumn('keberatan_informasi', $kolom)
                )));
            });
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'struktur_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropConstrainedForeignId('struktur_id');
            });
        }
    }
};
