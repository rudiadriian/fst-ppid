<?php

namespace App\Support;

use App\Models\KeberatanInformasi;
use App\Models\NotifikasiPemohon;
use App\Models\Pemohon;
use App\Models\PermohonanInformasi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * Umpan balik petugas ke lonceng Portal Pemohon.
 *
 * Kembaran `NotifikasiAdmin` di fe-ppid, arah sebaliknya: yang ditulis di sini
 * dibaca pemohon di `/akun`, bukan petugas di panel.
 *
 * Cakupannya lebih luas daripada email pemberitahuan ({@see EmailPemohon}).
 * Email hanya dikirim pada dua tahap besar karena kuota SMTP terbatas;
 * lonceng tidak punya batasan itu, jadi setiap perpindahan status, setiap
 * berkas tanggapan, dan setiap keputusan verifikasi ikut diberitahukan —
 * termasuk keterangan petugas yang memang ditujukan ke pemohon (alasan
 * penolakan, tanggapan atasan, catatan verifikasi). Catatan internal pada
 * perpindahan status tidak ikut: labelnya di panel memang menyebut itu
 * keterangan internal.
 *
 * Gagal menulis notifikasi tidak boleh membatalkan keputusan yang sudah
 * tersimpan: setiap galat dicatat di log lalu diabaikan.
 */
class NotifikasiPortal
{
    /** Isi notifikasi mengikuti bahasa portal: pembacanya pemohon. */
    private const LABEL_STATUS_PERMOHONAN = [
        'diajukan' => 'Dalam Proses',
        'diverifikasi' => 'Dalam Proses',
        'diproses' => 'Dalam Proses',
        'revisi' => 'Revisi',
        'menunggu_approval' => 'Menunggu Persetujuan',
        'disetujui' => 'Selesai',
        'selesai' => 'Selesai',
        'ditolak' => 'Tolak',
        'ditolak_sebagian' => 'Tolak',
        'kedaluwarsa' => 'Tolak',
    ];

    /**
     * Kalimat inti per status, tanpa nomor registrasi — nomornya ditempel di
     * depan supaya susunannya sama untuk permohonan maupun keberatan.
     */
    private const KALIMAT_PERMOHONAN = [
        'diverifikasi' => 'telah diperiksa dan diterima petugas PPID.',
        'diproses' => 'sedang ditelusuri dan disiapkan tanggapannya.',
        'revisi' => 'perlu diperbaiki sesuai catatan petugas.',
        'menunggu_approval' => 'menunggu persetujuan atasan PPID.',
        'disetujui' => 'telah disetujui atasan PPID.',
        'selesai' => 'telah selesai ditangani. Tanggapan dapat dilihat di portal.',
        'ditolak' => 'ditolak. Alasannya dapat dilihat pada rincian permohonan.',
        'ditolak_sebagian' => 'ditolak sebagian. Rinciannya dapat dilihat di portal.',
        'kedaluwarsa' => 'ditutup karena melewati batas waktu tanggapan.',
    ];

    private const KALIMAT_KEBERATAN = [
        'diproses' => 'sedang ditangani atasan PPID.',
        'revisi' => 'perlu diperbaiki sesuai catatan petugas.',
        'menunggu_approval' => 'menunggu persetujuan atasan PPID.',
        'ditolak' => 'ditolak. Alasannya dapat dilihat pada rincian keberatan.',
        'selesai' => 'telah selesai ditangani. Tanggapan dapat dilihat di portal.',
    ];

    /** Status yang tampil merah/oranye di lonceng, sisanya hijau. */
    private const STATUS_PERINGATAN = ['revisi', 'ditolak', 'ditolak_sebagian', 'kedaluwarsa'];

    /**
     * Status pengajuan berpindah karena tindakan petugas.
     *
     * Status yang tidak berubah tidak menghasilkan notifikasi: petugas kerap
     * menyimpan ulang baris yang sama hanya untuk membetulkan kolom lain.
     */
    public static function statusPengajuan(Model $pengajuan, ?string $statusLama, string $statusBaru, ?string $keterangan = null): void
    {
        if ($statusLama === $statusBaru) {
            return;
        }

        $keberatan = $pengajuan instanceof KeberatanInformasi;
        $kalimat = $keberatan
            ? (self::KALIMAT_KEBERATAN[$statusBaru] ?? null)
            : (self::KALIMAT_PERMOHONAN[$statusBaru] ?? null);

        // Status pembuka (`diajukan`) bukan umpan balik — itu pengajuan
        // pemohon sendiri, dan tanda terimanya sudah dikirim portal.
        if ($kalimat === null) {
            return;
        }

        $pemohonId = (int) ($pengajuan->pemohon_id ?? 0);

        if ($pemohonId < 1) {
            return;
        }

        $nomor = self::nomor($pengajuan, $keberatan);
        $jenis = $keberatan ? 'Keberatan atas permohonan' : 'Permohonan';
        $pesan = trim("{$jenis} {$nomor} {$kalimat}");

        // Hanya keterangan yang memang ditujukan ke pemohon yang boleh masuk —
        // alasan penolakan permohonan, tanggapan atasan pada keberatan.
        // Catatan internal petugas tidak pernah dioper ke sini.
        if (filled($keterangan)) {
            $pesan .= ' Keterangan petugas: '.str((string) $keterangan)->limit(200)->toString();
        }

        self::kirim(
            $pemohonId,
            $keberatan ? 'keberatan_status' : 'permohonan_status',
            $pesan,
            [
                'title' => ($keberatan ? 'Keberatan Informasi' : 'Permohonan Informasi').' — '
                    .(self::LABEL_STATUS_PERMOHONAN[$statusBaru] ?? $statusBaru),
                'icon' => $keberatan ? 'file-warning' : 'file-text',
                'link' => self::tautan($pengajuan, $keberatan),
                'variant' => in_array($statusBaru, self::STATUS_PERINGATAN, true) ? 'warning' : 'primary',
                'status' => $statusBaru,
                'kode_permohonan' => $nomor,
                'permohonan_id' => $keberatan ? $pengajuan->permohonan_id : $pengajuan->getKey(),
                'keberatan_id' => $keberatan ? $pengajuan->getKey() : null,
            ]
        );
    }

    /**
     * Berkas tanggapan dilampirkan petugas.
     *
     * Dipisahkan dari notifikasi status karena berkasnya sering menyusul
     * beberapa saat setelah status berpindah — tanpa notifikasi sendiri,
     * pemohon tidak punya penanda kapan dokumennya sudah bisa diunduh.
     */
    public static function berkasTanggapan(PermohonanInformasi $permohonan, int $jumlah): void
    {
        if ($jumlah < 1) {
            return;
        }

        $berkas = $jumlah === 1 ? '1 berkas tanggapan' : "{$jumlah} berkas tanggapan";

        self::kirim(
            (int) $permohonan->pemohon_id,
            'permohonan_tanggapan_file',
            "Petugas melampirkan {$berkas} pada permohonan {$permohonan->kode_permohonan}.",
            [
                'title' => 'Berkas Tanggapan',
                'icon' => 'paperclip',
                'link' => '/akun/permohonan/'.$permohonan->getKey(),
                'variant' => 'primary',
                'permohonan_id' => $permohonan->getKey(),
                'kode_permohonan' => (string) $permohonan->kode_permohonan,
            ]
        );
    }

    /**
     * Hasil pemeriksaan berkas Verifikasi Data Diri Pemohon.
     *
     * Yang ditolak dibawa langsung ke halaman perbaikannya, kecuali kalau
     * kesempatan kirim ulangnya sudah habis — tautan ke formulir yang tidak
     * lagi menerima kiriman cuma bikin pemohon berputar.
     */
    public static function hasilVerifikasiData(Pemohon $pemohon): void
    {
        $disetujui = $pemohon->status_verifikasi === 'terverifikasi';
        $sisa = max(0, Pemohon::BATAS_DITOLAK - (int) $pemohon->jumlah_ditolak);
        $bolehKirimUlang = !$disetujui && $sisa > 0;

        if ($disetujui) {
            $pesan = 'Data diri Anda telah diperiksa dan dinyatakan TERVERIFIKASI. '
                .'Anda kini dapat mengajukan Permohonan dan Keberatan Informasi.';
        } elseif ($bolehKirimUlang) {
            $pesan = "Berkas Verifikasi Data Diri Anda belum dapat disetujui. Sisa kesempatan pengiriman: {$sisa} kali.";
        } else {
            $pesan = 'Berkas Verifikasi Data Diri Anda belum dapat disetujui dan batas pengiriman ulang sudah habis. '
                .'Silakan hubungi petugas PPID.';
        }

        if (filled($pemohon->catatan_verifikasi)) {
            $pesan .= ' Catatan petugas: '.str((string) $pemohon->catatan_verifikasi)->limit(200)->toString();
        }

        self::kirim(
            (int) $pemohon->getKey(),
            'verifikasi_pemohon',
            $pesan,
            [
                'title' => $disetujui ? 'Data Pemohon Terverifikasi' : 'Verifikasi Data Pemohon Ditolak',
                'icon' => $disetujui ? 'user-check' : 'user-x',
                'link' => $bolehKirimUlang ? '/akun/pengaturan/data-pemohon' : '/akun',
                'variant' => $disetujui ? 'primary' : 'warning',
                'status_verifikasi' => (string) $pemohon->status_verifikasi,
            ]
        );
    }

    /** @param  array<string, mixed>  $data */
    private static function kirim(int $pemohonId, string $tipe, string $pesan, array $data): void
    {
        try {
            if ($pemohonId < 1) {
                return;
            }

            NotifikasiPemohon::create([
                'pemohon_id' => $pemohonId,
                'type' => $tipe,
                'message' => $pesan,
                'is_read' => false,
                'data' => array_filter($data, fn ($nilai) => $nilai !== null),
            ]);
        } catch (\Throwable $e) {
            Log::warning('[PPID] Gagal menulis notifikasi pemohon ('.$tipe.'): '.$e->getMessage());
        }
    }

    /**
     * Keberatan tidak punya nomor sendiri; dirujuk lewat permohonan induknya.
     *
     * Nomornya diisi DEFAULT di sisi PostgreSQL, jadi baris yang baru dibuat
     * pada permintaan yang sama bisa saja belum memuatnya — pesannya tidak
     * boleh menyisakan lubang kosong di tengah kalimat.
     */
    private static function nomor(Model $pengajuan, bool $keberatan): string
    {
        $nomor = $keberatan
            ? (string) ($pengajuan->loadMissing('permohonan')->permohonan?->kode_permohonan ?? '')
            : (string) $pengajuan->kode_permohonan;

        return $nomor !== '' ? $nomor : '(tanpa nomor)';
    }

    private static function tautan(Model $pengajuan, bool $keberatan): string
    {
        return $keberatan
            ? '/akun/keberatan'
            : '/akun/permohonan/'.$pengajuan->getKey();
    }
}
