<?php

namespace App\Support;

use App\Models\Pemohon;
use App\Notifications\StatusLayanan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * Pemberitahuan email ke pemohon atas pengajuan layanannya.
 *
 * Semua pengiriman lewat sini supaya satu aturan berlaku di seluruh portal:
 * SMTP yang mati atau alamat yang ditolak **tidak boleh** menggagalkan
 * transaksi yang sudah tersimpan — galatnya dicatat di log lalu diabaikan.
 */
class EmailPemohon
{
    /** Pengajuan tersimpan dan tercatat di sistem. */
    public static function pengajuanDikirim(Model $pengajuan, ?Pemohon $pemohon = null): void
    {
        self::kirim($pengajuan, StatusLayanan::TAHAP_DIKIRIM, $pemohon);
    }

    /** Berkas pengajuan sudah diperiksa dan diterima petugas. */
    public static function pengajuanDiterima(Model $pengajuan, ?Pemohon $pemohon = null): void
    {
        self::kirim($pengajuan, StatusLayanan::TAHAP_DITERIMA, $pemohon);
    }

    /** Tanggapan sudah terbit; berkasnya ditutup. */
    public static function pengajuanSelesai(Model $pengajuan, ?Pemohon $pemohon = null): void
    {
        self::kirim($pengajuan, StatusLayanan::TAHAP_SELESAI, $pemohon);
    }

    protected static function kirim(Model $pengajuan, string $tahap, ?Pemohon $pemohon): void
    {
        $tujuan = $pemohon ?: $pengajuan->pemohon;

        // Pemohon lama yang dientri petugas bisa saja belum punya alamat email.
        if (!$tujuan || blank($tujuan->email)) {
            return;
        }

        try {
            // Bahasanya dikunci — lihat config('ppid.bahasa_email').
            $tujuan->notify((new StatusLayanan($pengajuan, $tahap))->locale(config('ppid.bahasa_email')));
        } catch (\Throwable $e) {
            Log::warning('[PPID] Gagal mengirim email status layanan ('.$tahap.'): '.$e->getMessage());
        }
    }
}
