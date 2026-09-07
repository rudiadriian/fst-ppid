<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Google reCAPTCHA v3 untuk formulir masuk dan pemulihan password panel admin.
 *
 * Menggantikan captcha gambar buatan sendiri yang dipakai sebelumnya. v3 tidak
 * menampilkan teka-teki apa pun: peramban menjalankan skrip Google, mendapat
 * token sekali pakai, lalu server menukarnya ke `siteverify` dan menerima skor
 * 0.0–1.0 — makin tinggi makin besar keyakinan Google bahwa yang mengisi adalah
 * manusia. Tidak ada "benar/salah" seperti pada kode gambar; yang ada ambang.
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
     * `$aksi` harus sama dengan nama aksi yang dipakai peramban saat meminta
     * token. Tanpa pemeriksaan ini, token yang dipanen dari satu formulir bisa
     * dipakai ulang di formulir lain yang ambangnya lebih longgar.
     *
     * @return array{lolos: bool, alasan: string|null}
     */
    public static function periksa(?string $token, string $aksi, ?string $ip = null): array
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
            return ['lolos' => false, 'alasan' => self::pesanUmum()];
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
                'aksi' => $aksi,
                'galat' => $e->getMessage(),
            ]);

            return ['lolos' => false, 'alasan' => 'Verifikasi keamanan tidak dapat dihubungi. Coba lagi sebentar lagi.'];
        }

        if (!is_array($jawaban) || ($jawaban['success'] ?? false) !== true) {
            Log::info('Token reCAPTCHA ditolak Google.', [
                'aksi' => $aksi,
                'kode' => $jawaban['error-codes'] ?? null,
            ]);

            return ['lolos' => false, 'alasan' => self::pesanUmum()];
        }

        // Aksi dikirim balik oleh Google persis seperti yang diminta peramban.
        if (($jawaban['action'] ?? null) !== $aksi) {
            Log::info('Aksi reCAPTCHA tidak cocok.', [
                'diminta' => $aksi,
                'diterima' => $jawaban['action'] ?? null,
            ]);

            return ['lolos' => false, 'alasan' => self::pesanUmum()];
        }

        $skor = (float) ($jawaban['score'] ?? 0);

        if ($skor < self::skorMinimum()) {
            Log::info('Skor reCAPTCHA di bawah ambang.', [
                'aksi' => $aksi,
                'skor' => $skor,
                'ambang' => self::skorMinimum(),
            ]);

            return [
                'lolos' => false,
                'alasan' => 'Permintaan Anda terdeteksi sebagai aktivitas tidak wajar. Muat ulang halaman lalu coba lagi.',
            ];
        }

        return ['lolos' => true, 'alasan' => null];
    }

    /**
     * Satu pesan untuk token hilang, kedaluwarsa, sudah dipakai, dan aksi tidak
     * cocok: keempatnya berujung pada tindakan yang sama — muat ulang halaman.
     * Membedakannya hanya memberi penyerang umpan balik untuk menyetel serangan.
     */
    private static function pesanUmum(): string
    {
        return 'Verifikasi keamanan kedaluwarsa. Muat ulang halaman lalu coba lagi.';
    }

    private static function rahasia(): ?string
    {
        $nilai = config('ppid.akun.recaptcha_secret_key');

        return is_string($nilai) && $nilai !== '' ? $nilai : null;
    }

    private static function skorMinimum(): float
    {
        return (float) config('ppid.akun.recaptcha_skor_min', 0.7);
    }

    private static function timeout(): int
    {
        return max(2, (int) config('ppid.akun.recaptcha_timeout_detik', 5));
    }
}
