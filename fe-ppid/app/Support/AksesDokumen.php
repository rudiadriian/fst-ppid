<?php

namespace App\Support;

use App\Models\InformasiPublik;
use App\Models\PermohonanInformasi;
use App\Models\Pemohon;

/**
 * Siapa boleh melihat dan siapa boleh mengunduh satu dokumen informasi publik.
 *
 * Aturannya (langkah 83) dipisahkan ke sini, bukan disebar di controller dan
 * view, karena ia dipakai di tiga tempat yang harus selalu sepakat: tombol yang
 * ditampilkan di daftar, penyaji berkas pratinjau, dan penyaji berkas unduhan.
 * Kalau ketiganya menyimpan salinan aturannya sendiri, cepat atau lambat
 * tombolnya mengatakan satu hal dan penyajinya melakukan hal lain.
 *
 * **Melihat** terbuka untuk siapa saja — dokumen ini tetap informasi publik.
 * **Mengunduh** hanya untuk pemohon yang permohonannya atas dokumen itu sudah
 * disetujui petugas. Keputusan pelepasan salinan tetap di tangan PPID, sesuai
 * alur UU KIP; portal ini hanya menjalankan keputusan yang sudah dibuat.
 */
class AksesDokumen
{
    /**
     * Status permohonan yang berarti "petugas sudah memutuskan, dan hasilnya
     * dokumen boleh diberikan".
     *
     * `ditolak` jelas tidak masuk. `diajukan`, `diverifikasi`, `diproses`, dan
     * `menunggu_approval` juga tidak: semuanya berarti keputusannya belum ada.
     */
    public const STATUS_DISETUJUI = ['selesai'];

    /** Dokumen ini tunduk pada aturan unduhan terbatas? */
    public static function terbatas(InformasiPublik $dokumen): bool
    {
        return (bool) $dokumen->unduhan_terbatas;
    }

    /**
     * Pemohon ini boleh mengunduh dokumen ini?
     *
     * Dokumen yang tidak terbatas boleh diunduh siapa saja — termasuk yang
     * belum masuk. Yang terbatas menuntut permohonan yang sudah disetujui,
     * milik pemohon itu sendiri.
     */
    public static function bolehUnduh(InformasiPublik $dokumen, ?Pemohon $pemohon): bool
    {
        if (!self::terbatas($dokumen)) {
            return true;
        }

        if (!$pemohon) {
            return false;
        }

        return self::permohonanDisetujui($dokumen, $pemohon) !== null;
    }

    /**
     * Permohonan yang membuka kunci dokumen ini, bila ada.
     *
     * Dikembalikan barisnya, bukan sekadar benar/salah, supaya halaman bisa
     * menyebut nomor permohonannya — pemohon yang mengunduh berhak tahu
     * keputusan mana yang menjadi dasarnya.
     */
    public static function permohonanDisetujui(InformasiPublik $dokumen, Pemohon $pemohon): ?PermohonanInformasi
    {
        return PermohonanInformasi::query()
            ->where('pemohon_id', $pemohon->id)
            ->where('informasi_publik_id', $dokumen->id)
            ->whereIn('status', self::STATUS_DISETUJUI)
            ->orderByDesc('tanggal_permohonan')
            ->first();
    }

    /**
     * Permohonan atas dokumen ini yang masih berjalan, bila ada.
     *
     * Dipakai supaya tombolnya tidak mengajak mengirim permohonan kedua untuk
     * dokumen yang sama — yang hanya menambah antrean petugas tanpa mempercepat
     * apa pun bagi pemohon.
     */
    public static function permohonanBerjalan(InformasiPublik $dokumen, Pemohon $pemohon): ?PermohonanInformasi
    {
        return PermohonanInformasi::query()
            ->where('pemohon_id', $pemohon->id)
            ->where('informasi_publik_id', $dokumen->id)
            ->whereNotIn('status', array_merge(self::STATUS_DISETUJUI, ['ditolak']))
            ->orderByDesc('tanggal_permohonan')
            ->first();
    }

    /**
     * Keadaan tombol unduh untuk satu dokumen, siap dipakai view.
     *
     * @return array{keadaan: string, permohonan: ?PermohonanInformasi}
     *
     * Keadaan yang mungkin:
     *   - `bebas`    → bukan dokumen terbatas, unduh langsung
     *   - `masuk`    → belum masuk; harus login dulu
     *   - `ajukan`   → sudah masuk, belum pernah mengajukan
     *   - `menunggu` → permohonannya masih diproses petugas
     *   - `terbuka`  → permohonannya sudah disetujui
     */
    public static function keadaanUnduh(InformasiPublik $dokumen, ?Pemohon $pemohon): array
    {
        if (!self::terbatas($dokumen)) {
            return ['keadaan' => 'bebas', 'permohonan' => null];
        }

        if (!$pemohon) {
            return ['keadaan' => 'masuk', 'permohonan' => null];
        }

        if ($disetujui = self::permohonanDisetujui($dokumen, $pemohon)) {
            return ['keadaan' => 'terbuka', 'permohonan' => $disetujui];
        }

        if ($berjalan = self::permohonanBerjalan($dokumen, $pemohon)) {
            return ['keadaan' => 'menunggu', 'permohonan' => $berjalan];
        }

        return ['keadaan' => 'ajukan', 'permohonan' => null];
    }
}
