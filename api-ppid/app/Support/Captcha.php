<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Captcha gambar buatan sendiri (GD), tanpa layanan pihak ketiga.
 *
 * Alasannya sama dengan yang dipakai portal pemohon: panel ini tidak boleh
 * bergantung pada jaringan luar saat orang mencoba masuk, dan tidak ada kunci
 * layanan captcha yang tersedia. Perlindungannya memang lebih lemah daripada
 * reCAPTCHA/Turnstile, jadi captcha di sini bukan satu-satunya rem — kunci
 * masuk bertingkat (`KunciLoginAdmin`) dan pembatas laju rute tetap berjalan
 * berdampingan.
 *
 * **Bedanya dari versi fe-ppid: tidak memakai session.** Panel admin adalah
 * aplikasi SPA yang bicara ke API tanpa cookie sesi, jadi jawabannya tidak bisa
 * dititipkan di session. Sebagai gantinya tiap kode punya **id** sendiri; yang
 * disimpan server hanyalah hash kodenya, di cache, dengan masa hidup pendek.
 * Klien menyimpan id-nya dan mengirimkannya kembali bersama jawaban.
 *
 * Kodenya tidak pernah disimpan apa adanya, dan sekali diperiksa langsung
 * dibuang: satu gambar hanya berlaku untuk satu kali kirim formulir.
 */
class Captcha
{
    /** Huruf/angka yang mudah dibedakan: tanpa 0/O, 1/I/L, 2/Z, 5/S. */
    private const HURUF = 'ABCDEFGHJKMNPQRTUVWXY346789';

    private const PANJANG = 5;

    /**
     * Buat kode baru dan titipkan hash-nya.
     *
     * @return array{id: string, kode: string}
     */
    public static function buat(): array
    {
        $kode = '';

        for ($i = 0; $i < self::PANJANG; $i++) {
            $kode .= self::HURUF[random_int(0, strlen(self::HURUF) - 1)];
        }

        $id = (string) Str::uuid();

        Cache::put(self::kunci($id), self::hash($kode), self::umur());

        return ['id' => $id, 'kode' => $kode];
    }

    /**
     * Periksa jawaban, lalu buang kodenya apa pun hasilnya.
     *
     * Dibuang walau salah supaya satu kode tidak bisa ditebak berkali-kali;
     * formulir yang tampil ulang selalu meminta gambar baru.
     */
    public static function cocok(?string $id, ?string $jawaban): bool
    {
        if (blank($id) || blank($jawaban)) {
            return false;
        }

        $hash = Cache::pull(self::kunci($id));

        if (!is_string($hash)) {
            return false;
        }

        return hash_equals($hash, self::hash($jawaban));
    }

    /** PNG berisi kodenya, dengan derau supaya tidak mudah dibaca mesin. */
    public static function gambar(string $kode): string
    {
        $lebar = 190;
        $tinggi = 60;

        $kanvas = imagecreatetruecolor($lebar, $tinggi);
        imagealphablending($kanvas, true);

        $latar = imagecolorallocate($kanvas, 246, 244, 238);
        imagefilledrectangle($kanvas, 0, 0, $lebar, $tinggi, $latar);

        // Derau: garis dan titik acak berwarna muda — mengganggu pemisahan
        // karakter otomatis tanpa membuatnya sulit dibaca mata.
        for ($i = 0; $i < 6; $i++) {
            $warna = imagecolorallocate($kanvas, random_int(150, 210), random_int(150, 210), random_int(150, 210));
            imageline(
                $kanvas,
                random_int(0, $lebar),
                random_int(0, $tinggi),
                random_int(0, $lebar),
                random_int(0, $tinggi),
                $warna
            );
        }

        for ($i = 0; $i < 320; $i++) {
            $warna = imagecolorallocate($kanvas, random_int(140, 205), random_int(140, 205), random_int(140, 205));
            imagesetpixel($kanvas, random_int(0, $lebar), random_int(0, $tinggi), $warna);
        }

        // Tiap huruf digambar di kanvas kecil lalu diputar. Cara ini tidak
        // memerlukan berkas font TTF, jadi tetap jalan di server mana pun yang
        // punya GD.
        $langkah = (int) (($lebar - 30) / self::PANJANG);

        for ($i = 0; $i < strlen($kode); $i++) {
            $huruf = imagecreatetruecolor(20, 30);
            $bening = imagecolorallocatealpha($huruf, 0, 0, 0, 127);
            imagefill($huruf, 0, 0, $bening);
            imagesavealpha($huruf, true);

            $tinta = imagecolorallocate($huruf, random_int(10, 70), random_int(50, 110), random_int(20, 80));
            imagestring($huruf, 5, 4, 6, $kode[$i], $tinta);

            $diputar = imagerotate($huruf, random_int(-28, 28), $bening);
            imagesavealpha($diputar, true);

            imagecopy(
                $kanvas,
                $diputar,
                18 + ($i * $langkah),
                random_int(6, 18),
                0,
                0,
                imagesx($diputar),
                imagesy($diputar)
            );

            imagedestroy($huruf);
            imagedestroy($diputar);
        }

        ob_start();
        imagepng($kanvas);
        $png = (string) ob_get_clean();

        imagedestroy($kanvas);

        return $png;
    }

    public static function aktif(): bool
    {
        return (bool) config('ppid.akun.captcha_aktif', true);
    }

    private static function umur(): int
    {
        return max(60, (int) config('ppid.akun.captcha_umur_detik', 300));
    }

    private static function kunci(string $id): string
    {
        return 'captcha-admin|'.$id;
    }

    private static function hash(string $kode): string
    {
        // Huruf besar/kecil tidak dibedakan supaya orang tidak gagal hanya
        // karena kapital; kunci aplikasi ikut dicampur agar hash-nya tidak bisa
        // dicocokkan dengan tabel siap pakai.
        return hash_hmac('sha256', Str::upper(trim($kode)), (string) config('app.key'));
    }
}
