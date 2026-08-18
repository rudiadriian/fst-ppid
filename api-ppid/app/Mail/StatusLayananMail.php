<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Pemberitahuan status pengajuan layanan ke pemohon.
 *
 * Isinya sudah disusun oleh App\Support\EmailPemohon; kelas ini hanya
 * membungkusnya dengan templat `emails.layanan.status` — templat yang sama
 * persis dengan yang dipakai aplikasi situs, supaya email dari panel dan dari
 * portal tidak terlihat berbeda oleh pemohon.
 */
class StatusLayananMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array<string, mixed>  $isi  Variabel untuk view: judul, nama,
     *                                     paragraf[], baris[], catatan[], url,
     *                                     labelTombol.
     */
    public function __construct(
        protected string $subjek,
        protected array $isi,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->subjek);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.layanan.status',
            with: $this->isi + ['preheader' => $this->subjek],
        );
    }

    /** @return array<int, \Illuminate\Mail\Mailables\Attachment> */
    public function attachments(): array
    {
        return [];
    }
}
