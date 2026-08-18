<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

/**
 * Tautan konfirmasi alamat email pendaftar baru.
 *
 * Menggantikan `Illuminate\Auth\Notifications\VerifyEmail` bawaan Laravel:
 * isinya berbahasa Indonesia dan memakai kop PPID Food Station, bukan templat
 * markdown bawaan yang menyebut nama aplikasi apa adanya.
 */
class VerifikasiEmailPemohon extends Notification
{
    use Queueable;

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = $this->tautanVerifikasi($notifiable);

        return (new MailMessage())
            ->subject(__('Konfirmasi Email — PPID Food Station'))
            ->view('emails.akun.verifikasi', [
                'nama' => $this->sapaan($notifiable),
                'url' => $url,
                'berlakuJam' => (int) ceil($this->umurMenit() / 60),
            ]);
    }

    /**
     * URL bertanda tangan yang sama bentuknya dengan bawaan Laravel: `id` +
     * sha1 email. Hash-nya membuat tautan mati begitu pemohon mengganti
     * alamat email, meski masa berlakunya belum habis.
     */
    protected function tautanVerifikasi(object $notifiable): string
    {
        return URL::temporarySignedRoute(
            'akun.verifikasi.verify',
            Carbon::now()->addMinutes($this->umurMenit()),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }

    protected function umurMenit(): int
    {
        return (int) config('auth.verification.expire', 60);
    }

    protected function sapaan(object $notifiable): string
    {
        return filled($notifiable->nama) ? $notifiable->nama : __('Pemohon');
    }
}
