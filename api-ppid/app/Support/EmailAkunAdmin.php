<?php

namespace App\Support;

use App\Mail\StatusLayananMail;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Email seputar akun panel admin: tautan atur ulang password dan pemberitahuan
 * bahwa passwordnya baru saja diganti.
 *
 * Memakai templat yang sama dengan email ke pemohon (`emails.layanan.status`)
 * supaya seluruh surat yang keluar dari sistem PPID terlihat satu keluarga.
 *
 * Sama seperti `EmailPemohon`, percakapan SMTP-nya dijalankan setelah tanggapan
 * HTTP terkirim: petugas tidak boleh menunggu server surat hanya untuk melihat
 * pesan "tautan sudah dikirim".
 */
class EmailAkunAdmin
{
    /** Tautan atur ulang password, dikirim saat petugas menekan Lupa password. */
    public static function tautanReset(User $user, string $token): void
    {
        $panel = rtrim((string) config('ppid.panel_url'), '/');
        $umur = (int) config('ppid.akun.umur_tautan_menit', 60);

        $url = $panel.'/reset-password?token='.urlencode($token).'&email='.urlencode((string) $user->email);

        self::antre((string) $user->email, new StatusLayananMail('Atur ulang password panel PPID', [
            'judul' => 'Atur Ulang Password',
            'nama' => filled($user->name) ? $user->name : 'Petugas',
            'paragraf' => [
                'Kami menerima permintaan untuk mengatur ulang password akun panel PPID '
                    .config('ppid.kontak.instansi').' milik Anda.',
                'Tekan tombol di bawah untuk memasang password baru. Tautan ini berlaku '
                    .KunciBertingkat::durasiTerbaca($umur).' dan hanya bisa dipakai satu kali.',
            ],
            'baris' => [
                'Email' => (string) $user->email,
                'Diminta pada' => now()->timezone(config('ppid.zona_waktu'))->format('d/m/Y H:i').' WIB',
            ],
            'catatan' => [
                'Bila bukan Anda yang meminta, abaikan email ini — password lama tetap berlaku. '
                    .'Bila email seperti ini datang berulang kali, hubungi administrator.',
            ],
            'url' => $url,
            'labelTombol' => 'Pasang Password Baru',
        ]));
    }

    /**
     * Pemberitahuan bahwa password berhasil diganti.
     *
     * Ini bukan basa-basi: kalau bukan pemiliknya yang mengganti, email inilah
     * satu-satunya tanda bahwa akunnya sudah berpindah tangan.
     */
    public static function passwordDiubah(User $user): void
    {
        self::antre((string) $user->email, new StatusLayananMail('Password panel PPID Anda telah diubah', [
            'judul' => 'Password Berhasil Diubah',
            'nama' => filled($user->name) ? $user->name : 'Petugas',
            'paragraf' => [
                'Password akun panel PPID milik Anda baru saja diubah melalui tautan atur ulang password.',
                'Mulai sekarang, gunakan password baru tersebut untuk masuk.',
            ],
            'baris' => [
                'Email' => (string) $user->email,
                'Diubah pada' => now()->timezone(config('ppid.zona_waktu'))->format('d/m/Y H:i').' WIB',
            ],
            'catatan' => [
                'Bukan Anda yang melakukannya? Segera hubungi administrator — akun ini perlu diamankan.',
            ],
            'url' => rtrim((string) config('ppid.panel_url'), '/').'/sign-in',
            'labelTombol' => 'Masuk ke Panel',
        ]));
    }

    private static function antre(string $email, StatusLayananMail $surat): void
    {
        if (blank($email)) {
            return;
        }

        dispatch(function () use ($email, $surat) {
            try {
                Mail::to($email)->send($surat);
            } catch (\Throwable $e) {
                Log::warning('[PPID] Gagal mengirim email akun panel ke '.$email.': '.$e->getMessage());
            }
        })->afterResponse();
    }
}
