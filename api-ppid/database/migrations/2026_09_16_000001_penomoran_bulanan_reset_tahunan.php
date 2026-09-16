<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Penomoran permohonan dan keberatan: deret tahunan, penanda bulan.
 *
 * Bentuk lamanya `PPID-FSTJ/<YYYYMMDD>/<urutan harian>` — urutannya dimulai
 * ulang dari 1 setiap pagi. Akibatnya nomor 0001 lahir ratusan kali setahun dan
 * hanya bisa dibedakan oleh tanggal yang menempel padanya; rekapitulasi
 * tahunan, yang justru bentuk laporan yang diminta UU KIP, tidak punya deret
 * yang bisa dihitung.
 *
 * Bentuk barunya memisahkan dua peran yang sebelumnya dirangkap satu angka:
 *
 *   PPID-FSTJ/202609/001   ← bulan sebagai penanda, urutan sebagai deret
 *   PPID-FSTJ/202609/002
 *   PPID-FSTJ/202610/003   ← bulan berganti, deret jalan terus
 *   KBT-FSTJ/202609/001    ← keberatan punya deretnya sendiri
 *
 * Urutan berlanjut lintas bulan dan baru dimulai ulang pada tahun berikutnya.
 * Segmen tengah tetap memuat bulan supaya berkas fisik bisa disusun per bulan
 * tanpa membuka daftarnya.
 *
 * Nomor yang sudah terbit tidak disentuh. Nomor registrasi sudah beredar di
 * tanda terima, surel, dan surat — menomori ulangnya berarti dokumen yang
 * dipegang pemohon menunjuk berkas yang tidak lagi ada dengan nomor itu. Dua
 * bentuk hidup berdampingan, dan itu aman: bentuk lama memakai delapan angka
 * di segmen tengah, yang baru enam, jadi keduanya tidak akan pernah
 * menghasilkan teks yang sama.
 *
 * Deret barunya karena itu mulai dari 001 — perhitungannya hanya membaca nomor
 * berbentuk baru pada tahun berjalan, bukan nomor harian yang lama.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('permohonan_informasi')) {
            DB::statement(<<<'SQL'
                CREATE OR REPLACE FUNCTION fn_permohonan_kode() RETURNS trigger AS $$
                DECLARE
                    tahun text;
                    awalan text;
                    pola text;
                    urutan integer;
                BEGIN
                    IF NEW.kode_permohonan IS NOT NULL THEN
                        RETURN NEW;
                    END IF;

                    tahun := to_char(now(), 'YYYY');
                    awalan := 'PPID-FSTJ/' || tahun || to_char(now(), 'MM') || '/';

                    -- Hanya nomor berbentuk baru (enam angka di segmen tengah)
                    -- yang ikut dihitung; nomor harian lama tidak.
                    pola := '^PPID-FSTJ/' || tahun || '[0-9]{2}/[0-9]+$';

                    -- Kuncinya sepanjang tahun, bukan sepanjang bulan: dua
                    -- permohonan yang masuk bersamaan di dua bulan berbeda
                    -- (tengah malam pergantian bulan) tetap memperebutkan
                    -- urutan yang sama.
                    PERFORM pg_advisory_xact_lock(hashtext('PPID-FSTJ/' || tahun));

                    SELECT coalesce(max(split_part(kode_permohonan, '/', 3)::integer), 0) + 1
                      INTO urutan
                      FROM permohonan_informasi
                     WHERE kode_permohonan ~ pola;

                    NEW.kode_permohonan := awalan || lpad(urutan::text, 3, '0');

                    RETURN NEW;
                END;
                $$ LANGUAGE plpgsql;
            SQL);

            DB::statement('DROP TRIGGER IF EXISTS trg_permohonan_kode ON permohonan_informasi');
            DB::statement(<<<'SQL'
                CREATE TRIGGER trg_permohonan_kode
                BEFORE INSERT ON permohonan_informasi
                FOR EACH ROW EXECUTE FUNCTION fn_permohonan_kode();
            SQL);
        }

        if (Schema::hasTable('keberatan_informasi')) {
            DB::statement(<<<'SQL'
                CREATE OR REPLACE FUNCTION fn_keberatan_kode() RETURNS trigger AS $$
                DECLARE
                    tahun text;
                    awalan text;
                    pola text;
                    urutan integer;
                BEGIN
                    IF NEW.kode_keberatan IS NOT NULL THEN
                        RETURN NEW;
                    END IF;

                    tahun := to_char(now(), 'YYYY');
                    awalan := 'KBT-FSTJ/' || tahun || to_char(now(), 'MM') || '/';
                    pola := '^KBT-FSTJ/' || tahun || '[0-9]{2}/[0-9]+$';

                    PERFORM pg_advisory_xact_lock(hashtext('KBT-FSTJ/' || tahun));

                    SELECT coalesce(max(split_part(kode_keberatan, '/', 3)::integer), 0) + 1
                      INTO urutan
                      FROM keberatan_informasi
                     WHERE kode_keberatan ~ pola;

                    NEW.kode_keberatan := awalan || lpad(urutan::text, 3, '0');

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
        }
    }

    /** Kembali ke deret harian. Nomor yang sudah terbit tetap dibiarkan. */
    public function down(): void
    {
        if (Schema::hasTable('permohonan_informasi')) {
            DB::statement(<<<'SQL'
                CREATE OR REPLACE FUNCTION fn_permohonan_kode() RETURNS trigger AS $$
                DECLARE
                    awalan text;
                    urutan integer;
                BEGIN
                    IF NEW.kode_permohonan IS NOT NULL THEN
                        RETURN NEW;
                    END IF;

                    awalan := 'PPID-FSTJ/' || to_char(now(), 'YYYYMMDD') || '/';

                    PERFORM pg_advisory_xact_lock(hashtext(awalan));

                    SELECT coalesce(max(substring(kode_permohonan from length(awalan) + 1)::integer), 0) + 1
                      INTO urutan
                      FROM permohonan_informasi
                     WHERE kode_permohonan LIKE awalan || '%';

                    NEW.kode_permohonan := awalan || lpad(urutan::text, 4, '0');

                    RETURN NEW;
                END;
                $$ LANGUAGE plpgsql;
            SQL);
        }

        if (Schema::hasTable('keberatan_informasi')) {
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
        }
    }
};
