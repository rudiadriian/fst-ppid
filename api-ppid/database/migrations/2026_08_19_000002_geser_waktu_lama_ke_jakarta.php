<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Koreksi waktu yang tersimpan sebelum langkah 77.
 *
 * Sampai langkah 77, `app.timezone` masih `UTC` sementara TimeZone sesi
 * PostgreSQL `Asia/Bangkok` (+07). Laravel mengirim tanggal sebagai jam dinding
 * tanpa offset, jadi setiap penulisan meleset tujuh jam:
 *
 *   - Kolom `timestamptz` — Laravel bermaksud 09:42 UTC (= 16:42 WIB), yang
 *     terkirim string "09:42:02", dan PostgreSQL menstempelnya jadi 09:42+07.
 *     Instan yang tersimpan tujuh jam LEBIH AWAL daripada yang dimaksud.
 *   - Kolom `timestamp` polos — berisi jam dinding UTC. Setelah `app.timezone`
 *     jadi Asia/Jakarta, nilai itu dibaca sebagai jam Jakarta, jadi juga tujuh
 *     jam lebih awal.
 *
 * Keduanya meleset ke arah yang sama dan sebesar yang sama, jadi koreksinya
 * satu: tambah tujuh jam pada baris yang ditulis sebelum pembetulan setelan.
 *
 * === BATAS WAKTU ===
 *
 * Hanya baris yang lebih tua dari `BATAS` yang digeser. Baris yang ditulis
 * setelah setelan dibetulkan sudah benar, dan menggesernya justru merusak.
 * Sesuaikan `BATAS` bila migrasi ini dijalankan di lingkungan lain — isinya
 * harus saat setelan zona waktu di lingkungan itu diperbaiki.
 */
return new class extends Migration
{
    /**
     * Saat `app.timezone` dan `connections.pgsql.timezone` dibetulkan, dalam
     * WIB. Baris yang lebih baru dari ini tidak disentuh.
     */
    private const BATAS = '2026-08-19 16:45:00';

    private const JAM = 7;

    public function up(): void
    {
        $this->geser(self::JAM);
    }

    public function down(): void
    {
        $this->geser(-self::JAM);
    }

    private function geser(int $jam): void
    {
        $arah = $jam >= 0 ? '+' : '-';
        $selang = "interval '".abs($jam)." hours'";

        foreach ($this->kolomWaktu() as [$tabel, $kolom]) {
            /*
             * Pembanding `<` dipasang pada kolomnya sendiri, bukan pada
             * `created_at` tabelnya: sebuah baris lama bisa saja punya kolom
             * yang baru diisi hari ini (mis. `tanggal_tanggapan`), dan kolom
             * itu sudah benar.
             */
            DB::statement(
                'update "'.$tabel.'" set "'.$kolom.'" = "'.$kolom.'" '.$arah.' '.$selang.' '
                .'where "'.$kolom.'" is not null and "'.$kolom.'" < ?',
                [self::BATAS]
            );
        }
    }

    /**
     * Seluruh kolom bertipe waktu pada skema `public`.
     *
     * Dibaca dari katalog, bukan didaftar tangan: kolom waktu tersebar di
     * puluhan tabel dan satu pun yang terlewat berarti satu tempat yang
     * jamnya tetap meleset.
     *
     * @return array<int, array{0: string, 1: string}>
     */
    private function kolomWaktu(): array
    {
        $baris = DB::select(
            "select table_name, column_name
             from information_schema.columns
             where table_schema = 'public'
               and data_type in ('timestamp without time zone', 'timestamp with time zone')
             order by table_name, column_name"
        );

        return collect($baris)
            // Tabel bawaan kerangka kerja tidak menyimpan riwayat yang dibaca
            // siapa pun, dan `migrations` justru tidak boleh disentuh.
            ->reject(fn ($k) => in_array($k->table_name, ['migrations', 'failed_jobs', 'password_reset_tokens', 'password_reset_tokens_pemohon', 'personal_access_tokens'], true))
            ->filter(fn ($k) => Schema::hasTable($k->table_name))
            ->map(fn ($k) => [$k->table_name, $k->column_name])
            ->values()
            ->all();
    }
};
