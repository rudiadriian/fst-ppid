<?php

namespace App\Support;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

/**
 * Captcha gambar buatan sendiri (GD), tanpa layanan pihak ketiga.
 *
 * Alasannya: portal ini tidak boleh bergantung pada jaringan luar saat orang
 * mendaftar atau masuk, dan tidak ada kunci layanan captcha yang tersedia.
 * Konsekuensinya perlindungannya lebih lemah daripada reCAPTCHA/Turnstile,
 * jadi captcha di sini bukan satu-satunya rem: honeypot, jeda pengisian
 * minimum, dan pembatasan laju tetap berjalan berdampingan (lihat
 * `App\Rules\CaptchaBenar` dan `config/ppid.php`).
 *
 * Kodenya tidak pernah disimpan apa adanya — yang dititipkan di session hanya
 * hash-nya, sehingga isi session yang bocor tidak langsung memberi jawabannya.
 * Sekali diperiksa, kodenya langsung dibuang: satu gambar hanya berlaku untuk
 * satu kali kirim formulir.
 */
class Captcha
{
    private const KUNCI_HASH = 'captcha.hash';

    private const KUNCI_KEDALUWARSA = 'captcha.kedaluwarsa';

    /** Huruf/angka yang mudah dibedakan: tanpa 0/O, 1/I/L, 2/Z, 5/S. */
    private const HURUF = 'ABCDEFGHJKMNPQRTUVWXY346789';

    private const PANJANG = 5;

    /** Umur satu kode, dalam detik. */
    private const UMUR = 300;

    /** Buat kode baru, simpan hash-nya, kembalikan kodenya untuk digambar. */
    public static function buat(): string
    {
        $kode = '';

        for ($i = 0; $i < self::PANJANG; $i++) {
            $kode .= self::HURUF[random_int(0, strlen(self::HURUF) - 1)];
        }

        Session::put(self::KUNCI_HASH, self::hash($kode));
        Session::put(self::KUNCI_KEDALUWARSA, time() + self::UMUR);

        return $kode;
    }

    /**
     * Periksa jawaban pengguna, lalu buang kodenya apa pun hasilnya.
     *
     * Dibuang walau salah supaya satu kode tidak bisa ditebak berkali-kali;
     * formulir yang tampil ulang selalu memuat gambar baru.
     */
    public static function cocok(?string $jawaban): bool
    {
        $hash = Session::get(self::KUNCI_HASH);
        $kedaluwarsa = (int) Session::get(self::KUNCI_KEDALUWARSA, 0);

        self::bersihkan();

        if (!is_string($hash) || $jawaban === null || $jawaban === '') {
            return false;
        }

        if (time() > $kedaluwarsa) {
            return false;
        }

        return hash_equals($hash, self::hash($jawaban));
    }

    public static function bersihkan(): void
    {
        Session::forget([self::KUNCI_HASH, self::KUNCI_KEDALUWARSA]);
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

        // Derau: garis dan titik acak dengan warna muda supaya tetap terbaca
        // mata tetapi mengganggu pemisahan karakter otomatis.
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

    private static function hash(string $kode): string
    {
        // Huruf besar/kecil tidak dibedakan supaya pengguna tidak gagal hanya
        // karena huruf kapital; kunci aplikasi ikut dicampur agar hash tidak
        // bisa dicocokkan dengan tabel siap pakai.
        return hash_hmac('sha256', Str::upper(trim($kode)), (string) config('app.key'));
    }
}
