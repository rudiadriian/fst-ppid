<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Tautan atur ulang password akun pemohon.
 *
 * Menggantikan `Illuminate\Auth\Notifications\ResetPassword` bawaan Laravel.
 * Tautannya menunjuk route akun pengunjung (`akun.password.reset`), bukan route
 * `password.reset` milik panel — dua broker token yang berbeda.
 */
class ResetPasswordPemohon extends Notification
{
    use Queueable;

    public function __construct(public string $token)
    {
    }

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject(__('Permintaan Reset Password — PPID Food Station'))
            ->view('emails.akun.reset-password', [
                'nama' => filled($notifiable->nama) ? $notifiable->nama : __('Pemohon'),
                // Email ikut dibawa di query string supaya formulir reset tidak
                // menuntut pemohon mengetik ulang alamatnya; token tetap yang
                // menentukan sah atau tidaknya.
                'url' => route('akun.password.reset', [
                    'token' => $this->token,
                    'email' => $notifiable->getEmailForPasswordReset(),
                ]),
                'berlakuMenit' => (int) config('auth.passwords.pemohon.expire', 60),
            ]);
    }
}
