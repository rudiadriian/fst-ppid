<?php

namespace App\Support;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Unduhan berkas milik pemohon di Portal (berkas tanggapan petugas, lampiran
 * keberatan).
 *
 * Berkas tanggapan ditulis api-ppid ke disk `media`-nya, bukan oleh situs ini,
 * jadi letaknya tidak selalu di disk `public` milik fe-ppid: bisa berada di
 * MEDIA_ROOT yang diarahkan ke folder lain, atau di `dokumen_terbatas` bila
 * dipilih dari arsip dan berkas asalnya kemudian ditandai Unduhan Terbatas.
 * Sebelumnya hanya `public` yang diperiksa, dan berkas yang tidak ada di sana
 * dijawab halaman 404 kosong — pemohon tidak tahu apa yang terjadi, petugas
 * tidak punya jejak apa pun di log.
 */
class BerkasPortal
{
    /** Urutan disk yang diperiksa; yang pertama memuat berkasnya dipakai. */
    private const DISK = ['public', 'media', 'dokumen_terbatas'];

    /**
     * Kirim berkas sebagai unduhan, atau kembalikan pemohon ke `$kembali`
     * dengan pesan bila berkasnya tidak ditemukan.
     */
    public static function unduh(?string $pathFile, ?string $namaFile, string $kembali): Response
    {
        $path = self::normalkan((string) $pathFile);

        if ($path === null) {
            return self::hilang($pathFile, $kembali, 'path tidak sah');
        }

        foreach (self::DISK as $nama) {
            $disk = Storage::disk($nama);

            if ($disk->exists($path)) {
                return $disk->download($path, self::namaUnduhan($namaFile, $path), [
                    'Cache-Control' => 'private, no-store, max-age=0',
                ]);
            }
        }

        return self::hilang($pathFile, $kembali, 'tidak ada di disk mana pun');
    }

    /**
     * Path relatif di dalam disk. Kolom path_file umumnya sudah berbentuk
     * `uploads/…`, tetapi sebagian isian lama membawa awalan `storage/` atau
     * URL penuh. Apa pun yang mencoba keluar dari folder disk ditolak.
     */
    private static function normalkan(string $path): ?string
    {
        $path = trim(str_replace('\\', '/', $path));

        if (Str::startsWith($path, ['http://', 'https://'])) {
            $path = (string) parse_url($path, PHP_URL_PATH);
        }

        $path = ltrim($path, '/');

        if (Str::startsWith($path, 'storage/')) {
            $path = Str::after($path, 'storage/');
        }

        $path = rawurldecode($path);

        if ($path === '' || str_contains($path, '..')) {
            return null;
        }

        return $path;
    }

    /**
     * Nama berkas yang diterima pemohon. Petugas kerap menamai lampiran tanpa
     * ekstensi (mis. "Laporan tahunan 2025"); tanpa ekstensi, berkas yang
     * terunduh tidak bisa dibuka dengan aplikasi yang tepat.
     */
    private static function namaUnduhan(?string $namaFile, string $path): string
    {
        $ekstensi = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $nama = trim((string) $namaFile);

        if ($nama === '') {
            return basename($path);
        }

        if ($ekstensi !== '' && strtolower(pathinfo($nama, PATHINFO_EXTENSION)) !== $ekstensi) {
            $nama .= '.'.$ekstensi;
        }

        return $nama;
    }

    private static function hilang(?string $pathFile, string $kembali, string $sebab): RedirectResponse
    {
        Log::warning('[PPID] Berkas unduhan portal tidak ditemukan: '.$sebab, [
            'path_file' => $pathFile,
            'root' => collect(self::DISK)->mapWithKeys(fn ($nama) => [$nama => config("filesystems.disks.$nama.root")])->all(),
        ]);

        return redirect()->to($kembali)->withErrors(['berkas' => __('Berkas tidak ditemukan di server. Silakan hubungi petugas PPID agar berkasnya diunggah ulang.')]);
    }
}
