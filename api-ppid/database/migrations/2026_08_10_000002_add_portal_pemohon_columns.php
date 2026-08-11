<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Kolom pendukung Portal Pengguna di situs publik (fe-ppid).
 *
 * Tiga hal yang ditambahkan:
 *  1. Verifikasi Data Pemohon — berkas KTP, status verifikasi, foto profil.
 *  2. Cara memperoleh informasi pada permohonan (melihat/membaca/…).
 *  3. Kasus posisi & penguasaan pada keberatan.
 *
 * Nilai CHECK constraint ikut diperluas: jenis pemohon bertambah `perorangan`,
 * `mahasiswa`, `lembaga`; status permohonan dan keberatan bertambah `revisi`
 * (dipakai petugas untuk meminta perbaikan berkas).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pemohon', function (Blueprint $table) {
            if (!Schema::hasColumn('pemohon', 'foto')) {
                $table->string('foto', 500)->nullable();
            }

            if (!Schema::hasColumn('pemohon', 'file_ktp')) {
                $table->string('file_ktp', 500)->nullable();
            }

            if (!Schema::hasColumn('pemohon', 'status_verifikasi')) {
                $table->string('status_verifikasi', 20)->default('belum');
            }

            if (!Schema::hasColumn('pemohon', 'tanggal_verifikasi')) {
                $table->timestampTz('tanggal_verifikasi')->nullable();
            }
        });

        Schema::table('permohonan_informasi', function (Blueprint $table) {
            if (!Schema::hasColumn('permohonan_informasi', 'cara_memperoleh')) {
                $table->string('cara_memperoleh', 20)->nullable();
            }
        });

        Schema::table('keberatan_informasi', function (Blueprint $table) {
            if (!Schema::hasColumn('keberatan_informasi', 'kasus_posisi')) {
                $table->text('kasus_posisi')->nullable();
            }

            if (!Schema::hasColumn('keberatan_informasi', 'dikuasakan')) {
                $table->boolean('dikuasakan')->default(false);
            }
        });

        $this->gantiCheck('pemohon', 'pemohon_jenis_pemohon_check', 'jenis_pemohon', [
            'pribadi', 'instansi', 'kelompok',      // nilai lama, dipertahankan
            'perorangan', 'mahasiswa', 'lembaga',   // nilai baru portal pengguna
        ]);

        $this->gantiCheck('pemohon', 'pemohon_status_verifikasi_check', 'status_verifikasi', [
            'belum', 'menunggu', 'terverifikasi', 'ditolak',
        ]);

        $this->gantiCheck('permohonan_informasi', 'permohonan_informasi_status_check', 'status', [
            'diajukan', 'diverifikasi', 'diproses', 'revisi', 'menunggu_approval',
            'disetujui', 'ditolak', 'ditolak_sebagian', 'selesai', 'kedaluwarsa',
        ]);

        $this->gantiCheck('permohonan_informasi', 'permohonan_informasi_cara_memperoleh_check', 'cara_memperoleh', [
            'melihat', 'membaca', 'mencatat', 'mendengar',
        ]);

        $this->gantiCheck('keberatan_informasi', 'keberatan_informasi_status_check', 'status', [
            'diajukan', 'diproses', 'revisi', 'menunggu_approval', 'ditolak', 'selesai',
        ]);
    }

    public function down(): void
    {
        Schema::table('pemohon', function (Blueprint $table) {
            $table->dropColumn(['foto', 'file_ktp', 'status_verifikasi', 'tanggal_verifikasi']);
        });

        Schema::table('permohonan_informasi', function (Blueprint $table) {
            $table->dropColumn('cara_memperoleh');
        });

        Schema::table('keberatan_informasi', function (Blueprint $table) {
            $table->dropColumn(['kasus_posisi', 'dikuasakan']);
        });
    }

    /** Ganti CHECK constraint daftar nilai; kolom NULL tetap diizinkan. */
    private function gantiCheck(string $tabel, string $nama, string $kolom, array $nilai): void
    {
        $daftar = collect($nilai)->map(fn ($v) => "'".$v."'")->implode(', ');

        DB::statement("ALTER TABLE {$tabel} DROP CONSTRAINT IF EXISTS {$nama}");
        DB::statement("ALTER TABLE {$tabel} ADD CONSTRAINT {$nama} CHECK ({$kolom} IS NULL OR {$kolom}::text = ANY (ARRAY[{$daftar}]::text[]))");
    }
};
