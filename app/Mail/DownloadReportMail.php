<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DownloadReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $downloadUrl;
    public $reportTitle;

    /**
     * Create a new message instance.
     */
    public function __construct($name, $downloadUrl, $reportTitle)
    {
        $this->name = $name;
        $this->downloadUrl = $downloadUrl;
        $this->reportTitle = $reportTitle;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tautan Unduh Laporan PPID - ' . $this->reportTitle,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.ppid.download-link', // <-- View email akan kita buat di sini
            with: [
                'name' => $this->name,
                'url' => $this->downloadUrl,
                'title' => $this->reportTitle,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
