<?php

namespace App\Support;

use App\Mail\StatusLayananMail;
use App\Models\KeberatanInformasi;
use App\Models\Pemohon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Email pemberitahuan ke pemohon dari sisi panel admin.
 *
 * Hanya dua peristiwa yang dikirim dari sini — pengajuan DITERIMA petugas dan
 * SELESAI ditangani. Tanda terima "berhasil dikirim" sudah dikirim aplikasi
 * situs saat formulirnya tersimpan, dan pergeseran status internal lain
 * sengaja tidak mengirim email supaya kuota SMTP tidak habis.
 *
 * Kegagalan kirim tidak boleh menggagalkan perubahan status yang sudah
 * tersimpan: setiap galat dicatat di log lalu diabaikan.
 */
class EmailPemohon
{
    public const TAHAP_DITERIMA = 'diterima';

    public const TAHAP_SELESAI = 'selesai';

    /**
     * Status yang memicu email, per jenis pengajuan.
     *
     * Permohonan memakai `diverifikasi` sebagai penanda "berkas diperiksa dan
     * diterima"; keberatan tidak punya status itu sehingga `diproses` yang
     * dipakai.
     */
    public const PEMICU_PERMOHONAN = [
        'diverifikasi' => self::TAHAP_DITERIMA,
        'selesai' => self::TAHAP_SELESAI,
    ];

    public const PEMICU_KEBERATAN = [
        'diproses' => self::TAHAP_DITERIMA,
        'selesai' => self::TAHAP_SELESAI,
    ];

    private const LABEL_JENIS_KEBERATAN = [
        'permohonan_ditolak' => 'Permohonan Informasi Ditolak',
        'informasi_tidak_disediakan' => 'Informasi Tidak Disediakan',
        'permintaan_tidak_ditanggapi' => 'Permintaan Tidak Ditanggapi',
        'informasi_tidak_sesuai' => 'Informasi yang Diberikan Tidak Sesuai',
        'biaya_tidak_wajar' => 'Pengenaan Biaya yang Tidak Wajar',
        'melebihi_jangka_waktu' => 'Permintaan Melebihi Jangka Waktu Tanggapan',
    ];

    /**
     * Kirim bila perpindahan statusnya memang salah satu pemicu.
     *
     * Perbandingan dengan status lama menjaga email tidak terkirim dua kali
     * saat petugas menyimpan ulang baris yang statusnya tidak berubah.
     */
    public static function statusBerubah(Model $pengajuan, ?string $statusLama, string $statusBaru): void
    {
        if ($statusLama === $statusBaru) {
            return;
        }

        $pemicu = $pengajuan instanceof KeberatanInformasi
            ? self::PEMICU_KEBERATAN
            : self::PEMICU_PERMOHONAN;

        $tahap = $pemicu[$statusBaru] ?? null;

        if ($tahap === null) {
            return;
        }

        self::kirim($pengajuan, $tahap);
    }

    /**
     * Hasil pemeriksaan berkas Verifikasi Data Diri Pemohon.
     *
     * Tanpa email ini pemohon hanya tahu keputusannya kalau kebetulan membuka
     * portal — padahal selama belum terverifikasi ia tidak bisa mengajukan apa
     * pun, dan kesempatan mengirim ulang terbatas.
     */
    public static function hasilVerifikasiData(Pemohon $pemohon): void
    {
        try {
            if (blank($pemohon->email)) {
                return;
            }

            $disetujui = $pemohon->status_verifikasi === 'terverifikasi';
            $nama = filled($pemohon->nama) ? $pemohon->nama : 'Pemohon';
            $situs = rtrim((string) config('ppid.situs_url'), '/');
            $sisa = max(0, Pemohon::BATAS_DITOLAK - (int) $pemohon->jumlah_ditolak);

            // Kesempatan kirim ulang habis: portal tidak lagi menerima
            // perbaikan, jadi tombol dan petunjuk perbaikannya ikut ditiadakan
            // supaya tidak bertabrakan dengan isi suratnya.
            $bolehKirimUlang = $disetujui || $sisa > 0;

            $subjek = $disetujui
                ? 'Data Pemohon Anda telah terverifikasi'
                : 'Verifikasi Data Pemohon belum dapat disetujui';

            Mail::to($pemohon->email)->send(new StatusLayananMail($subjek, [
                'judul' => $disetujui ? 'Data Pemohon Terverifikasi' : 'Verifikasi Data Pemohon Ditolak',
                'nama' => $nama,
                'paragraf' => self::paragrafVerifikasi($disetujui, $sisa),
                'baris' => [
                    'Nama' => $nama,
                    'Email' => (string) $pemohon->email,
                    'Status' => $disetujui ? 'Terverifikasi' : 'Ditolak',
                    'Tanggal diperiksa' => self::waktu($pemohon->tanggal_verifikasi),
                    'Catatan petugas' => (string) $pemohon->catatan_verifikasi,
                ],
                'catatan' => $disetujui || !$bolehKirimUlang ? [] : [
                    'Perbaikan dikirim melalui menu Pengaturan → Data Pemohon pada Portal Pemohon.',
                ],
                'url' => match (true) {
                    $disetujui => $situs.'/akun',
                    $bolehKirimUlang => $situs.'/akun/pengaturan/data-pemohon',
                    default => null,
                },
                'labelTombol' => match (true) {
                    $disetujui => 'Buka Portal Pemohon',
                    $bolehKirimUlang => 'Perbaiki Data Pemohon',
                    default => null,
                },
            ]));
        } catch (\Throwable $e) {
            Log::warning('[PPID] Gagal mengirim email hasil verifikasi pemohon: '.$e->getMessage());
        }
    }

    /** @return array<int, string> */
    protected static function paragrafVerifikasi(bool $disetujui, int $sisa): array
    {
        $instansi = config('ppid.kontak.instansi');

        if ($disetujui) {
            return [
                "Data diri Anda telah diperiksa dan DINYATAKAN TERVERIFIKASI oleh petugas PPID {$instansi}.",
                'Mulai sekarang Anda dapat mengajukan Permohonan Informasi dan Keberatan Informasi melalui Portal Pemohon.',
            ];
        }

        if ($sisa < 1) {
            return [
                "Berkas Verifikasi Data Diri Anda BELUM DAPAT DISETUJUI oleh petugas PPID {$instansi}.",
                'Batas pengiriman ulang sudah habis, sehingga perbaikan tidak dapat lagi dikirim melalui portal. Silakan hubungi petugas PPID melalui alamat email di bawah untuk penyelesaiannya.',
            ];
        }

        return [
            "Berkas Verifikasi Data Diri Anda BELUM DAPAT DISETUJUI oleh petugas PPID {$instansi}.",
            "Silakan perbaiki sesuai catatan petugas di bawah, lalu kirim ulang. Sisa kesempatan pengiriman: {$sisa} kali.",
        ];
    }

    protected static function kirim(Model $pengajuan, string $tahap): void
    {
        try {
            $pengajuan->loadMissing($pengajuan instanceof KeberatanInformasi ? ['pemohon', 'permohonan'] : ['pemohon']);

            $pemohon = $pengajuan->pemohon;

            // Pemohon lama hasil entri petugas bisa saja belum punya email.
            if (!$pemohon || blank($pemohon->email)) {
                return;
            }

            $keberatan = $pengajuan instanceof KeberatanInformasi;
            $jenis = $keberatan ? 'Keberatan Informasi' : 'Permohonan Informasi';
            $nomor = self::nomor($pengajuan, $keberatan);
            $subjek = self::subjek($tahap, $jenis, $nomor);

            Mail::to($pemohon->email)->send(new StatusLayananMail($subjek, [
                'judul' => ($tahap === self::TAHAP_SELESAI ? 'Pengajuan Selesai' : 'Pengajuan Diterima').' — '.$jenis,
                'nama' => filled($pemohon->nama) ? $pemohon->nama : 'Pemohon',
                'paragraf' => self::paragraf($tahap, $jenis, $nomor, $keberatan),
                'baris' => self::baris($pengajuan, $keberatan, $nomor),
                'catatan' => self::catatan($tahap, $keberatan),
                'url' => self::url($pengajuan, $keberatan),
                'labelTombol' => 'Lihat di Portal Pemohon',
            ]));
        } catch (\Throwable $e) {
            Log::warning('[PPID] Gagal mengirim email status layanan ('.$tahap.'): '.$e->getMessage());
        }
    }

    protected static function nomor(Model $pengajuan, bool $keberatan): string
    {
        if (!$keberatan) {
            return (string) $pengajuan->kode_permohonan;
        }

        return (string) ($pengajuan->permohonan?->kode_permohonan ?? '-');
    }

    protected static function subjek(string $tahap, string $jenis, string $nomor): string
    {
        return $tahap === self::TAHAP_SELESAI
            ? "{$jenis} {$nomor} telah selesai"
            : "{$jenis} {$nomor} diterima PPID";
    }

    /** @return array<int, string> */
    protected static function paragraf(string $tahap, string $jenis, string $nomor, bool $keberatan): array
    {
        $instansi = config('ppid.kontak.instansi');
        $hariKerja = $keberatan ? 30 : 10;

        if ($tahap === self::TAHAP_SELESAI) {
            return [
                "Pengajuan {$jenis} Anda dengan nomor registrasi {$nomor} telah SELESAI ditangani PPID {$instansi}.",
                'Tanggapan beserta lampirannya (bila ada) dapat dilihat dan diunduh melalui Portal Pemohon.',
            ];
        }

        return [
            "Pengajuan {$jenis} Anda dengan nomor registrasi {$nomor} telah diperiksa dan DITERIMA oleh petugas PPID {$instansi}.",
            "Pengajuan Anda kini masuk tahap penelusuran dan penyiapan tanggapan. Tanggapan disampaikan paling lambat {$hariKerja} hari kerja sejak pengajuan diterima.",
        ];
    }

    /** @return array<string, string> */
    protected static function baris(Model $pengajuan, bool $keberatan, string $nomor): array
    {
        if ($keberatan) {
            return [
                'Jenis pengajuan' => 'Keberatan Informasi',
                'Nomor permohonan' => $nomor,
                'Alasan keberatan' => self::LABEL_JENIS_KEBERATAN[$pengajuan->jenis_keberatan] ?? '-',
                'Tanggal keberatan' => self::waktu($pengajuan->tanggal_keberatan),
                'Status' => self::labelStatus($pengajuan->status),
            ];
        }

        return [
            'Nomor registrasi' => $nomor,
            'Tanggal permohonan' => self::waktu($pengajuan->tanggal_permohonan),
            'Rincian informasi' => str((string) $pengajuan->rincian_informasi)->limit(160)->toString(),
            'Batas waktu tanggapan' => self::tanggal($pengajuan->batas_waktu_tanggapan),
            'Status' => self::labelStatus($pengajuan->status),
        ];
    }

    /** Waktu tersimpan dalam UTC; digeser dulu supaya label "WIB" benar. */
    protected static function waktu($nilai): string
    {
        return $nilai
            ? $nilai->copy()->timezone(config('ppid.zona_waktu'))->locale(config('ppid.bahasa_email'))->translatedFormat('d F Y H:i').' WIB'
            : '';
    }

    protected static function tanggal($nilai): string
    {
        return $nilai
            ? $nilai->copy()->timezone(config('ppid.zona_waktu'))->locale(config('ppid.bahasa_email'))->translatedFormat('d F Y')
            : '';
    }

    /** Label yang sama dengan yang dilihat pemohon di portal. */
    protected static function labelStatus(?string $status): string
    {
        return match ($status) {
            'diajukan', 'diverifikasi', 'diproses' => 'Dalam Proses',
            'revisi' => 'Revisi',
            'menunggu_approval' => 'Menunggu Persetujuan',
            'disetujui', 'selesai' => 'Selesai',
            'ditolak', 'ditolak_sebagian', 'kedaluwarsa' => 'Tolak',
            default => (string) $status,
        };
    }

    /** @return array<int, string> */
    protected static function catatan(string $tahap, bool $keberatan): array
    {
        if ($tahap !== self::TAHAP_SELESAI) {
            return [];
        }

        if ($keberatan) {
            return [
                'Bila tanggapan atas keberatan ini dinilai belum sesuai, Anda dapat mengajukan sengketa informasi ke Komisi Informasi paling lambat 14 (empat belas) hari kerja sejak tanggapan diterima.',
            ];
        }

        return [
            'Bila tanggapan dinilai belum sesuai, Anda dapat mengajukan keberatan melalui Portal Pemohon paling lambat 30 (tiga puluh) hari kerja sejak tanggapan diterima.',
            'Kami juga mengharapkan penilaian Anda atas layanan ini melalui Survei Kepuasan pada halaman rincian permohonan.',
        ];
    }

    protected static function url(Model $pengajuan, bool $keberatan): string
    {
        $situs = rtrim((string) config('ppid.situs_url'), '/');

        return $keberatan
            ? $situs.'/akun/keberatan'
            : $situs.'/akun/permohonan/'.$pengajuan->getKey();
    }
}
