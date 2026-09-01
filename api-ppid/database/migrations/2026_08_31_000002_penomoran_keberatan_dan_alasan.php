<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Nomor registrasi keberatan, dan alasan keberatan yang lengkap (langkah 89).
 *
 * Dua kekurangan yang keduanya menyangkut pengarsipan keberatan:
 *
 * 1. **Keberatan tidak punya nomor.** Permohonan sudah lahir dengan
 *    `kode_permohonan` (`PPID-FSTJ/<tanggal>/<urutan>`), tetapi keberatan hanya
 *    dikenali lewat id tabelnya. Akibatnya berkas keberatan tidak bisa dirujuk
 *    dalam surat maupun arsip fisik, dan tidak ada cara membedakannya dari
 *    nomor permohonan yang menjadi dasarnya. Awalannya karena itu sengaja
 *    berbeda — `KBT-FSTJ/` — bukan sekadar melanjutkan deret yang sama:
 *    permohonan dan keberatan adalah dua berkas dengan tenggat berbeda (10
 *    hari kerja vs 30 hari kalender), dan satu deret bersama akan
 *    menyamarkannya.
 *
 * 2. **Satu alasan keberatan hilang.** Pasal 35 UU KIP menyebut tujuh dasar
 *    keberatan; tabel ini baru menerima enam. Yang tidak ada, "tidak
 *    dipenuhinya permintaan informasi", justru dasar yang paling sering
 *    dipakai ketika informasi diberikan sebagian. Tanpa nilainya, keberatan
 *    semacam itu terpaksa dititipkan ke alasan lain dan analisa sebarannya
 *    ikut salah.
 */
return new class extends Migration
{
    /** Tujuh dasar keberatan menurut Pasal 35 UU No. 14 Tahun 2008. */
    private const JENIS = [
        'permohonan_ditolak',
        'permintaan_tidak_ditanggapi',
        'melebihi_jangka_waktu',
        'informasi_tidak_sesuai',
        'permintaan_tidak_dipenuhi',
        'biaya_tidak_wajar',
        'informasi_tidak_disediakan',
    ];

    public function up(): void
    {
        if (!Schema::hasTable('keberatan_informasi')) {
            return;
        }

        if (!Schema::hasColumn('keberatan_informasi', 'kode_keberatan')) {
            Schema::table('keberatan_informasi', function (Blueprint $table) {
                $table->string('kode_keberatan', 50)->nullable()->unique()->after('id');
            });

            /*
             * Keberatan yang sudah terlanjur ada ikut diberi nomor, memakai
             * tanggal pengajuannya sendiri — bukan tanggal migrasi dijalankan.
             * Nomor arsip yang semuanya tertanggal hari pemasangan sistem sama
             * tidak bergunanya dengan tidak bernomor sama sekali.
             */
            DB::statement(<<<'SQL'
                UPDATE keberatan_informasi k
                   SET kode_keberatan = urut.kode
                  FROM (
                        SELECT id,
                               'KBT-FSTJ/' || to_char(tanggal_keberatan, 'YYYYMMDD') || '/'
                                  || lpad(row_number() OVER (
                                       PARTITION BY to_char(tanggal_keberatan, 'YYYYMMDD')
                                       ORDER BY id
                                     )::text, 4, '0') AS kode
                          FROM keberatan_informasi
                         WHERE kode_keberatan IS NULL
                       ) urut
                 WHERE k.id = urut.id
            SQL);
        }

        /*
         * Nomor dilahirkan basis data, bukan aplikasi: keberatan masuk dari
         * portal pemohon (fe-ppid) dan bisa juga lahir dari seeder, dan
         * keduanya tidak boleh memegang aturan penomoran sendiri-sendiri.
         * `pg_advisory_xact_lock` menahan dua keberatan bersamaan agar tidak
         * memperebutkan urutan yang sama, persis seperti pada permohonan.
         */
        DB::statement(<<<'SQL'
            CREATE OR REPLACE FUNCTION fn_keberatan_kode() RETURNS trigger AS $$
            DECLARE
                awalan text;
                urutan integer;
            BEGIN
                IF NEW.kode_keberatan IS NOT NULL THEN
                    RETURN NEW;
                END IF;

                awalan := 'KBT-FSTJ/' || to_char(now(), 'YYYYMMDD') || '/';

                PERFORM pg_advisory_xact_lock(hashtext(awalan));

                SELECT coalesce(max(substring(kode_keberatan from length(awalan) + 1)::integer), 0) + 1
                  INTO urutan
                  FROM keberatan_informasi
                 WHERE kode_keberatan LIKE awalan || '%';

                NEW.kode_keberatan := awalan || lpad(urutan::text, 4, '0');

                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        SQL);

        DB::statement('DROP TRIGGER IF EXISTS trg_keberatan_kode ON keberatan_informasi');
        DB::statement(<<<'SQL'
            CREATE TRIGGER trg_keberatan_kode
            BEFORE INSERT ON keberatan_informasi
            FOR EACH ROW EXECUTE FUNCTION fn_keberatan_kode();
        SQL);

        $nilai = "'".implode("', '", self::JENIS)."'";

        DB::statement('ALTER TABLE keberatan_informasi DROP CONSTRAINT IF EXISTS keberatan_informasi_jenis_keberatan_check');
        DB::statement("ALTER TABLE keberatan_informasi ADD CONSTRAINT keberatan_informasi_jenis_keberatan_check CHECK (jenis_keberatan::text = ANY (ARRAY[{$nilai}]::text[]))");
    }

    public function down(): void
    {
        if (!Schema::hasTable('keberatan_informasi')) {
            return;
        }

        DB::statement('DROP TRIGGER IF EXISTS trg_keberatan_kode ON keberatan_informasi');
        DB::statement('DROP FUNCTION IF EXISTS fn_keberatan_kode()');

        // Kembali ke enam nilai lama. Baris yang memakai alasan ketujuh
        // dipindahkan lebih dulu, kalau tidak constraint-nya menolak dipasang.
        DB::statement("UPDATE keberatan_informasi SET jenis_keberatan = 'informasi_tidak_sesuai' WHERE jenis_keberatan = 'permintaan_tidak_dipenuhi'");
        DB::statement('ALTER TABLE keberatan_informasi DROP CONSTRAINT IF EXISTS keberatan_informasi_jenis_keberatan_check');
        DB::statement("ALTER TABLE keberatan_informasi ADD CONSTRAINT keberatan_informasi_jenis_keberatan_check CHECK (jenis_keberatan::text = ANY (ARRAY['permohonan_ditolak', 'informasi_tidak_disediakan', 'permintaan_tidak_ditanggapi', 'informasi_tidak_sesuai', 'biaya_tidak_wajar', 'melebihi_jangka_waktu']::text[]))");

        if (Schema::hasColumn('keberatan_informasi', 'kode_keberatan')) {
            Schema::table('keberatan_informasi', function (Blueprint $table) {
                $table->dropColumn('kode_keberatan');
            });
        }
    }
};
