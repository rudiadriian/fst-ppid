<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Google reCAPTCHA v2 (kotak centang) untuk formulir masuk dan pemulihan
 * password panel admin.
 *
 * Sebelumnya panel ini memakai v3, yang menilai perilaku halaman diam-diam dan
 * mengembalikan skor. v2 bekerja lain: orangnya mencentang "Saya bukan robot",
 * kadang diminta memilih gambar, dan yang sampai ke server adalah token yang
 * membuktikan tantangan itu terlewati. Jawaban Google karena itu tidak lagi
 * memuat `score` maupun `action` — hanya berhasil atau tidak, jadi tidak ada
 * ambang yang perlu disetel dan tidak ada pemisahan token antar-formulir.
 *
 * **Sengaja gagal tertutup.** Bila Google tidak terjangkau, token ditolak dan
 * orangnya tidak bisa masuk. Alternatifnya — meloloskan permintaan saat
 * pemeriksaan gagal — berarti siapa pun yang bisa memutus lalu lintas ke
 * Google juga bisa mematikan captcha-nya, dan itu justru jadi jalan masuk.
 * Konsekuensinya harus disadari: gangguan di sisi Google ikut menutup pintu
 * panel. Kalau itu terjadi, `PPID_RECAPTCHA_AKTIF=false` adalah tuas darurat
 * yang harus diputar sadar oleh operator, bukan sesuatu yang terjadi diam-diam.
 *
 * Token hanya berlaku dua menit di sisi Google dan sekali tukar, jadi tidak
 * ada yang perlu disimpan server: tidak ada cache, tidak ada session.
 */
class Recaptcha
{
    private const URL = 'https://www.google.com/recaptcha/api/siteverify';

    /**
     * Sakelar utama. Dimatikan hanya untuk pengujian otomatis dan keadaan
     * darurat; pada panel yang dapat dijangkau dari jaringan harus menyala.
     */
    public static function aktif(): bool
    {
        return (bool) config('ppid.akun.recaptcha_aktif', true);
    }

    /**
     * Tukar token ke Google, lalu nilai jawabannya.
     *
     * @return array{lolos: bool, alasan: string|null}
     */
    public static function periksa(?string $token, ?string $ip = null): array
    {
        if (!self::aktif()) {
            return ['lolos' => true, 'alasan' => null];
        }

        $rahasia = self::rahasia();

        if (blank($rahasia)) {
            // Salah pasang, bukan salah pengguna. Ditolak — kalau kunci kosong
            // diperlakukan sebagai "captcha mati", satu variabel env yang lupa
            // diisi diam-diam melucuti seluruh perlindungan ini.
            Log::error('reCAPTCHA aktif tetapi PPID_RECAPTCHA_SECRET_KEY kosong.');

            return ['lolos' => false, 'alasan' => 'Verifikasi keamanan belum dikonfigurasi di server.'];
        }

        if (blank($token)) {
            return ['lolos' => false, 'alasan' => 'Centang dulu kotak "Saya bukan robot".'];
        }

        try {
            $jawaban = Http::asForm()
                ->timeout(self::timeout())
                ->post(self::URL, array_filter([
                    'secret' => $rahasia,
                    'response' => $token,
                    'remoteip' => $ip,
                ]))
                ->throw()
                ->json();
        } catch (Throwable $e) {
            Log::warning('Verifikasi reCAPTCHA gagal dihubungi.', [
                'galat' => $e->getMessage(),
            ]);

            return ['lolos' => false, 'alasan' => 'Verifikasi keamanan tidak dapat dihubungi. Coba lagi sebentar lagi.'];
        }

        if (!is_array($jawaban) || ($jawaban['success'] ?? false) !== true) {
            Log::info('Token reCAPTCHA ditolak Google.', [
                'kode' => $jawaban['error-codes'] ?? null,
            ]);

            /*
             * Satu pesan untuk token kedaluwarsa, sudah dipakai, dan dipalsukan:
             * ketiganya berujung pada tindakan yang sama — centang ulang.
             * Membedakannya hanya memberi penyerang umpan balik untuk menyetel
             * serangan.
             */
            return ['lolos' => false, 'alasan' => 'Verifikasi keamanan kedaluwarsa. Centang ulang kotaknya lalu coba lagi.'];
        }

        return ['lolos' => true, 'alasan' => null];
    }

    private static function rahasia(): ?string
    {
        $nilai = config('ppid.akun.recaptcha_secret_key');

        return is_string($nilai) && $nilai !== '' ? $nilai : null;
    }

    private static function timeout(): int
    {
        return max(2, (int) config('ppid.akun.recaptcha_timeout_detik', 5));
    }
}
