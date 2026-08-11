<?php

namespace App\Support;

use App\Models\PengaturanSitus;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Jembatan baca konten CMS untuk situs publik.
 *
 * Situs publik membaca `ppiddb` langsung, tidak lewat API admin. Konsekuensinya
 * satu: kalau panel admin atau API mati, halaman publik tetap hidup. Kalau
 * database yang bermasalah, setiap pemanggilan di sini mengembalikan data
 * cadangan supaya halaman tetap terbuka — bukan menampilkan layar error.
 */
class Cms
{
    /** Ditandai true bila ada query yang gagal pada request ini. */
    private static bool $offline = false;

    /**
     * Jalankan query CMS. Bila gagal, catat di log dan pakai data cadangan.
     *
     * @template T
     *
     * @param  callable():T  $query
     * @param  T  $cadangan
     * @return T
     */
    public static function ambil(callable $query, $cadangan, string $konteks)
    {
        try {
            return $query();
        } catch (\Throwable $e) {
            self::$offline = true;
            Log::warning("[PPID] Query CMS {$konteks} gagal: ".$e->getMessage());

            return $cadangan;
        }
    }

    public static function offline(): bool
    {
        return self::$offline;
    }

    /**
     * URL publik satu berkas media.
     *
     * Kolom path di database menyimpan lokasi relatif (`uploads/...`). Nilai
     * yang sudah berupa URL penuh atau path absolut dibiarkan apa adanya agar
     * data lama tetap tampil.
     */
    public static function url(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', '/'])) {
            return $path;
        }

        return asset('storage/'.ltrim($path, '/'));
    }

    /**
     * Nilai pengaturan situs yang dikelola lewat CMS.
     * Dibaca sekali per request lalu ditahan di memori.
     */
    public static function pengaturan(string $key, ?string $default = null): ?string
    {
        static $cache = null;

        if ($cache === null) {
            $cache = self::ambil(
                fn () => PengaturanSitus::pluck('value', 'key')->all(),
                [],
                'pengaturan_situs'
            );
        }

        $nilai = $cache[$key] ?? null;

        return filled($nilai) ? $nilai : $default;
    }

    /** Ukuran berkas dalam satuan yang enak dibaca (mis. "2.1 MB"). */
    public static function ukuran(?int $bytes): string
    {
        if (!$bytes) {
            return '—';
        }

        if ($bytes >= 1024 * 1024) {
            return round($bytes / (1024 * 1024), 1).' MB';
        }

        return max(1, (int) round($bytes / 1024)).' KB';
    }

    /** Tanggal panjang berbahasa Indonesia, mis. "10 Desember 2025". */
    public static function tanggal($tanggal): string
    {
        if (blank($tanggal)) {
            return '';
        }

        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $carbon = $tanggal instanceof \DateTimeInterface ? $tanggal : \Illuminate\Support\Carbon::parse($tanggal);

        return $carbon->format('j').' '.$bulan[(int) $carbon->format('n')].' '.$carbon->format('Y');
    }

    /** Tanggal beserta jamnya, mis. "11 Agustus 2026, 14.05 WIB". */
    public static function tanggalWaktu($waktu): string
    {
        if (blank($waktu)) {
            return '';
        }

        $carbon = $waktu instanceof \DateTimeInterface ? $waktu : \Illuminate\Support\Carbon::parse($waktu);

        return self::tanggal($carbon).', '.$carbon->format('H.i').' WIB';
    }
}
