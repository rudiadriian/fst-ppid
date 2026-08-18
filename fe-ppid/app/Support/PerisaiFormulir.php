<?php

namespace App\Support;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\ValidationException;

/**
 * Rem tambahan untuk formulir akun, di luar captcha dan pembatasan laju.
 *
 * Dua hal yang diperiksa:
 *
 *   1. **Honeypot** — isian tersembunyi yang tidak pernah dilihat manusia.
 *      Bot pengisi-otomatis cenderung mengisi semua isian yang ditemukan, jadi
 *      begitu isian ini terisi, permintaannya pasti bukan dari orang.
 *   2. **Jeda pengisian** — waktu formulir dibuka dititipkan dalam bentuk
 *      terenkripsi, jadi tidak bisa dipalsukan dari sisi klien. Kiriman yang
 *      datang lebih cepat daripada kemampuan mengetik manusia ditolak.
 *
 * Pesan penolakannya sengaja netral dan tidak menyebut honeypot maupun jeda,
 * supaya penyerang tidak tahu rem mana yang menahannya.
 */
class PerisaiFormulir
{
    /** Nama isian umpan; harus terlihat wajar bila terbaca bot. */
    public const HONEYPOT = 'alamat_surat';

    /** Nama isian penanda waktu formulir dibuka. */
    public const WAKTU = 'dibuka_pada';

    /** Token waktu untuk ditempel di formulir sebagai isian tersembunyi. */
    public static function token(): string
    {
        return Crypt::encryptString((string) time());
    }

    /**
     * @throws ValidationException bila kiriman berperilaku seperti bot
     */
    public static function periksa(Request $request): void
    {
        if (!config('ppid.akun.perisai_formulir')) {
            return;
        }

        if (filled($request->input(self::HONEYPOT))) {
            self::tolak();
        }

        $jedaMinimum = (int) config('ppid.akun.jeda_isi_minimum', 3);
        $umurMaksimum = (int) config('ppid.akun.umur_formulir_maksimum', 7200);

        try {
            $dibuka = (int) Crypt::decryptString((string) $request->input(self::WAKTU));
        } catch (DecryptException) {
            self::tolak();

            return;
        }

        $berlalu = time() - $dibuka;

        // Formulir yang sudah terlalu lama dibuka juga ditolak: penanda waktu
        // yang berumur panjang bisa dipakai ulang berkali-kali.
        if ($berlalu < $jedaMinimum || $berlalu > $umurMaksimum) {
            self::tolak();
        }
    }

    /**
     * Kunci error `perisai` punya tempat tayang sendiri di
     * `akun/partials/status.blade.php`, bukan menumpang isian captcha —
     * captcha bisa dimatikan lewat konfigurasi, dan kalau pesannya menumpang di
     * sana penolakan ini jadi tidak terlihat sama sekali.
     *
     * @throws ValidationException
     */
    private static function tolak(): void
    {
        throw ValidationException::withMessages([
            'perisai' => __('Formulir tidak dapat diproses. Muat ulang halaman lalu isi kembali.'),
        ]);
    }
}
