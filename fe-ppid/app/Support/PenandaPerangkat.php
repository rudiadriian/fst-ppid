<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

/**
 * Penanda perangkat pengunjung.
 *
 * **Bukan MAC address.** Permintaan awal menyebut pencatatan MAC address, tetapi
 * server HTTP tidak pernah menerimanya: paket yang sampai sudah melewati banyak
 * perangkat jaringan, dan MAC hanya terlihat oleh perangkat di segmen yang sama
 * dengan pengunjung. Tidak ada cara apa pun — header, JavaScript, maupun
 * konfigurasi server — yang bisa mengambilnya dari peramban.
 *
 * Gantinya sebuah nilai acak berumur panjang yang dititipkan sebagai cookie.
 * Nilainya menandai *peramban*, bukan perangkat keras: membersihkan cookie atau
 * membuka mode penyamaran menghasilkan penanda baru. Karena itu penanda ini
 * tidak pernah dipakai sendirian — pembatasan pengiriman tautan menghitung
 * email, alamat IP, dan penanda perangkat secara terpisah, sehingga menghapus
 * cookie tidak mengosongkan hitungan yang lain.
 */
class PenandaPerangkat
{
    public const NAMA_COOKIE = 'ppid_perangkat';

    /** Umur cookie dalam menit (2 tahun). */
    private const UMUR = 60 * 24 * 730;

    /** Cache per-request supaya satu permintaan tidak membuat dua penanda. */
    private static ?string $penanda = null;

    public static function ambil(Request $request): string
    {
        if (self::$penanda !== null) {
            return self::$penanda;
        }

        $nilai = $request->cookie(self::NAMA_COOKIE);

        if (is_string($nilai) && preg_match('/^[A-Za-z0-9]{48}$/', $nilai)) {
            return self::$penanda = $nilai;
        }

        $nilai = Str::random(48);

        // Cookie dititipkan ke respons apa pun hasil permintaannya, jadi
        // penandanya sudah ada pada percobaan berikutnya.
        Cookie::queue(cookie(
            name: self::NAMA_COOKIE,
            value: $nilai,
            minutes: self::UMUR,
            httpOnly: true,
            sameSite: 'lax',
        ));

        return self::$penanda = $nilai;
    }

    /** Sidik ringkas peramban, untuk catatan bila cookie dihapus. */
    public static function sidikUserAgent(Request $request): string
    {
        return substr((string) $request->userAgent(), 0, 255);
    }
}
