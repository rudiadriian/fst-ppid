<?php

namespace App\Notifications;

use App\Models\KeberatanInformasi;
use App\Models\PermohonanInformasi;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Pemberitahuan status pengajuan layanan ke pemohon.
 *
 * Hanya tiga peristiwa yang dikirim — pengajuan berhasil DIKIRIM, DITERIMA
 * petugas, dan SELESAI. Pergeseran status internal (diproses, menunggu
 * approval, dan seterusnya) sengaja tidak mengirim email: kuota kirim SMTP
 * terbatas dan pemohon sudah bisa melihatnya di portal.
 *
 * Tahap `diterima` dan `selesai` dipicu dari panel admin (api-ppid); tahap
 * `dikirim` dipicu saat formulir portal tersimpan.
 */
class StatusLayanan extends Notification
{
    use Queueable;

    public const TAHAP_DIKIRIM = 'dikirim';

    public const TAHAP_DITERIMA = 'diterima';

    public const TAHAP_SELESAI = 'selesai';

    public function __construct(
        protected Model $pengajuan,
        protected string $tahap,
    ) {
    }

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $keberatan = $this->pengajuan instanceof KeberatanInformasi;
        $jenis = $keberatan ? __('Keberatan Informasi') : __('Permohonan Informasi');
        $nomor = $this->nomor();

        return (new MailMessage())
            ->subject($this->subjek($jenis, $nomor))
            ->view('emails.layanan.status', [
                'judul' => $this->judul($jenis),
                'preheader' => $this->subjek($jenis, $nomor),
                'nama' => filled($notifiable->nama) ? $notifiable->nama : __('Pemohon'),
                'paragraf' => $this->paragraf($jenis, $nomor),
                'baris' => $this->baris($keberatan),
                'catatan' => $this->catatan($keberatan),
                'url' => $this->url($keberatan),
                'labelTombol' => __('Lihat di Portal Pemohon'),
            ]);
    }

    /**
     * Nomor registrasi permohonan dipakai apa adanya; keberatan belum punya
     * kolom nomor sendiri, jadi dirujuk lewat nomor permohonan induknya.
     */
    protected function nomor(): string
    {
        if ($this->pengajuan instanceof PermohonanInformasi) {
            return (string) $this->pengajuan->kode_permohonan;
        }

        return (string) ($this->pengajuan->permohonan?->kode_permohonan ?? '-');
    }

    protected function subjek(string $jenis, string $nomor): string
    {
        return match ($this->tahap) {
            self::TAHAP_DITERIMA => __(':jenis :nomor diterima PPID', ['jenis' => $jenis, 'nomor' => $nomor]),
            self::TAHAP_SELESAI => __(':jenis :nomor telah selesai', ['jenis' => $jenis, 'nomor' => $nomor]),
            default => __(':jenis :nomor berhasil dikirim', ['jenis' => $jenis, 'nomor' => $nomor]),
        };
    }

    protected function judul(string $jenis): string
    {
        return match ($this->tahap) {
            self::TAHAP_DITERIMA => __('Pengajuan Diterima'),
            self::TAHAP_SELESAI => __('Pengajuan Selesai'),
            default => __('Pengajuan Berhasil Dikirim'),
        }.' — '.$jenis;
    }

    /** @return array<int, string> */
    protected function paragraf(string $jenis, string $nomor): array
    {
        $hariKerja = $this->pengajuan instanceof KeberatanInformasi ? 30 : 10;

        return match ($this->tahap) {
            self::TAHAP_DITERIMA => [
                __('Pengajuan :jenis Anda dengan nomor registrasi :nomor telah diperiksa dan DITERIMA oleh petugas PPID :instansi.', [
                    'jenis' => $jenis,
                    'nomor' => $nomor,
                    'instansi' => config('ppid.kontak.instansi'),
                ]),
                __('Pengajuan Anda kini masuk tahap penelusuran dan penyiapan tanggapan. Tanggapan disampaikan paling lambat :hari hari kerja sejak pengajuan diterima.', [
                    'hari' => $hariKerja,
                ]),
            ],
            self::TAHAP_SELESAI => [
                __('Pengajuan :jenis Anda dengan nomor registrasi :nomor telah SELESAI ditangani PPID :instansi.', [
                    'jenis' => $jenis,
                    'nomor' => $nomor,
                    'instansi' => config('ppid.kontak.instansi'),
                ]),
                __('Tanggapan beserta lampirannya (bila ada) dapat dilihat dan diunduh melalui Portal Pemohon.'),
            ],
            default => [
                __('Pengajuan :jenis Anda BERHASIL DIKIRIM dan tercatat di sistem PPID :instansi dengan nomor registrasi :nomor.', [
                    'jenis' => $jenis,
                    'nomor' => $nomor,
                    'instansi' => config('ppid.kontak.instansi'),
                ]),
                __('Petugas akan memeriksa kelengkapan pengajuan Anda terlebih dahulu. Pemberitahuan berikutnya dikirim setelah pengajuan diterima.'),
            ],
        };
    }

    /** @return array<string, string> */
    protected function baris(bool $keberatan): array
    {
        if ($keberatan) {
            return [
                __('Jenis pengajuan') => __('Keberatan Informasi'),
                __('Nomor permohonan') => $this->nomor(),
                __('Alasan keberatan') => __(KeberatanInformasi::JENIS[$this->pengajuan->jenis_keberatan] ?? '-'),
                __('Tanggal keberatan') => $this->waktu($this->pengajuan->tanggal_keberatan),
                __('Status') => $this->pengajuan->labelStatus(),
            ];
        }

        return [
            __('Nomor registrasi') => $this->nomor(),
            __('Tanggal permohonan') => $this->waktu($this->pengajuan->tanggal_permohonan),
            __('Rincian informasi') => str($this->pengajuan->rincian_informasi)->limit(160)->toString(),
            __('Batas waktu tanggapan') => $this->tanggal($this->pengajuan->batas_waktu_tanggapan),
            __('Status') => $this->pengajuan->labelStatus(),
        ];
    }

    /**
     * Waktu tersimpan dalam UTC; digeser dulu supaya label "WIB" benar.
     */
    protected function waktu($nilai): string
    {
        return $nilai
            ? $nilai->copy()->timezone(config('ppid.zona_waktu'))->translatedFormat('d F Y H:i').' WIB'
            : '';
    }

    protected function tanggal($nilai): string
    {
        return $nilai
            ? $nilai->copy()->timezone(config('ppid.zona_waktu'))->translatedFormat('d F Y')
            : '';
    }

    /** @return array<int, string> */
    protected function catatan(bool $keberatan): array
    {
        if ($this->tahap !== self::TAHAP_SELESAI) {
            return [];
        }

        if ($keberatan) {
            return [
                __('Bila tanggapan atas keberatan ini dinilai belum sesuai, Anda dapat mengajukan sengketa informasi ke Komisi Informasi paling lambat 14 (empat belas) hari kerja sejak tanggapan diterima.'),
            ];
        }

        return [
            __('Bila tanggapan dinilai belum sesuai, Anda dapat mengajukan keberatan melalui Portal Pemohon paling lambat 30 (tiga puluh) hari kerja sejak tanggapan diterima.'),
            __('Kami juga mengharapkan penilaian Anda atas layanan ini melalui Survei Kepuasan pada halaman rincian permohonan.'),
        ];
    }

    protected function url(bool $keberatan): string
    {
        return $keberatan
            ? route('akun.keberatan.index')
            : route('akun.permohonan.show', ['permohonan' => $this->pengajuan->getKey()]);
    }
}
