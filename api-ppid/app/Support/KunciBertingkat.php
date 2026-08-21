<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Model;

/**
 * Aturan kunci bertingkat yang dipakai bersama oleh percobaan masuk dan
 * permintaan tautan lupa password.
 *
 * Tangganya sama untuk keduanya, sesuai permintaan: tiap 3 percobaan menaikkan
 * satu tahap — 1 jam, lalu 1 hari, lalu 14 hari, lalu **akun disuspend** dan
 * tidak terbuka sendiri oleh waktu. Yang berbeda hanya tabel penyimpannya dan
 * nama kolom hitungannya, jadi logikanya ditaruh di satu tempat: kalau
 * tangganya diubah, tidak ada versi kedua yang tertinggal.
 *
 * **Kunci dipasang per kombinasi identitas + alamat IP, bukan per identitas
 * saja.** Mengunci per identitas berarti siapa pun yang tahu email seorang
 * petugas bisa menutup akunnya hanya dengan sengaja salah password — pemblokiran
 * layanan yang lebih merugikan daripada serangan yang hendak dicegah. Suspend
 * pada tahap keempat adalah pengecualiannya, dan memang harus: kalau satu IP
 * sudah gagal 12 kali pada satu akun, itu bukan lagi salah ketik.
 */
class KunciBertingkat
{
    /** Tahap ke berapa yang berarti "suspend", bukan sekadar tunggu. */
    public const TAHAP_SUSPEND = 4;

    /**
     * Hitung akibat dari satu percobaan yang gagal.
     *
     * Tidak menyentuh basis data — pemanggilnya yang menyimpan. Dipisah supaya
     * aturannya bisa diuji tanpa tabel.
     *
     * @param  int  $jumlah  hitungan setelah percobaan ini
     * @return array{tahap: int, menit: ?int, suspend: bool}
     */
    public static function akibat(int $jumlah): array
    {
        $setiap = max(1, (int) config('ppid.akun.gagal_per_tahap', 3));

        if ($jumlah % $setiap !== 0) {
            return ['tahap' => 0, 'menit' => null, 'suspend' => false];
        }

        $tahap = intdiv($jumlah, $setiap);

        if ($tahap >= self::TAHAP_SUSPEND) {
            return ['tahap' => self::TAHAP_SUSPEND, 'menit' => null, 'suspend' => true];
        }

        $tangga = (array) config('ppid.akun.tahap_kunci_menit', [60, 1440, 20160]);
        $menit = (int) ($tangga[$tahap - 1] ?? end($tangga));

        return ['tahap' => $tahap, 'menit' => $menit, 'suspend' => false];
    }

    /**
     * Nolkan hitungan lama yang sudah jauh tertinggal.
     *
     * Tanpa ini, orang yang salah ketik tiga kali beberapa bulan lalu akan
     * langsung mendarat di tahap berikutnya begitu salah ketik lagi hari ini.
     * Baris yang sedang terkunci tidak pernah dinolkan — masa tunggunya harus
     * dijalani sampai habis.
     */
    public static function luruhkanBilaUsang(Model $baris, string $kolomJumlah, string $kolomWaktu): void
    {
        $jam = (int) config('ppid.akun.reset_hitungan_gagal_jam', 72);

        if (!$baris->exists || $baris->terkunci_sampai?->isFuture()) {
            return;
        }

        $terakhir = $baris->{$kolomWaktu};

        if ($terakhir !== null && $terakhir->lt(now()->subHours($jam))) {
            $baris->{$kolomJumlah} = 0;
            $baris->tahap_kunci = 0;
        }
    }

    /** "1 jam", "1 hari", "14 hari" — bukan "20160 menit". */
    public static function durasiTerbaca(int $menit): string
    {
        if ($menit % 1440 === 0) {
            return intdiv($menit, 1440).' hari';
        }

        if ($menit % 60 === 0) {
            return intdiv($menit, 60).' jam';
        }

        return $menit.' menit';
    }

    /** Sisa kunci dalam kalimat, mis. "3 jam 12 menit lagi". */
    public static function sisaTerbaca(int $detik): string
    {
        if ($detik <= 60) {
            return 'kurang dari satu menit lagi';
        }

        $hari = intdiv($detik, 86400);
        $jam = intdiv($detik % 86400, 3600);
        $menit = intdiv($detik % 3600, 60);

        $bagian = [];

        if ($hari > 0) {
            $bagian[] = $hari.' hari';
        }

        if ($jam > 0) {
            $bagian[] = $jam.' jam';
        }

        // Menit hanya disebut kalau harinya belum ada — "2 hari 3 jam 5 menit"
        // lebih sulit dibaca daripada "2 hari 3 jam", dan ketelitiannya tidak
        // menolong siapa pun yang harus menunggu selama itu.
        if ($menit > 0 && $hari === 0) {
            $bagian[] = $menit.' menit';
        }

        return implode(' ', $bagian).' lagi';
    }
}
