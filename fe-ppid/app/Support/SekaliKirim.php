<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Satu formulir, satu berkas: penjaga kiriman ganda pada formulir pengajuan.
 *
 * Mengunci tombol di peramban saja tidak cukup. Kiriman kedua tetap bisa lahir
 * dari tombol Muat Ulang setelah halaman POST, dari tombol Kembali lalu kirim
 * lagi, dari peramban yang mengulang permintaan saat jaringan putus, atau dari
 * perangkat yang JavaScript-nya mati. Karena itu penjagaannya ada di dua sisi:
 * di layar (`resources/js/app.js`) supaya pemohon melihat tombolnya terkunci,
 * dan di sini supaya berkas keduanya memang tidak pernah tersimpan.
 *
 * Cara kerjanya: tiap formulir dibuka membawa satu token acak. Token itu
 * diklaim di cache dengan `add()` — operasi tulis-bila-belum-ada yang atomik,
 * jadi dua permintaan yang tiba bersamaan tidak bisa sama-sama menang. Yang
 * kalah tidak menyimpan apa pun; ia diantar ke berkas yang sudah tersimpan
 * lengkap dengan nomor registrasinya, bukan diberi pesan galat — bagi pemohon,
 * klik gandanya memang berhasil.
 *
 * Token yang tidak dikenal (formulir lama, atau permintaan yang disusun sendiri
 * tanpa token) dibiarkan lewat: penjagaan ini menahan kiriman ganda yang tidak
 * disengaja, sedangkan pengiriman beruntun yang disengaja adalah urusan
 * pembatas laju {@see \App\Support\PembatasTautan} dan {@see PerisaiFormulir}.
 */
class SekaliKirim
{
    /** Nama isian tersembunyi yang membawa tokennya. */
    public const FIELD = 'sekali_kirim';

    /**
     * Umur klaim. Cukup panjang untuk menutup pengulangan permintaan dan tombol
     * Kembali, cukup pendek supaya cache tidak menumpuk token mati.
     */
    private const UMUR = 900;

    /** Nilai sementara selama berkasnya masih disimpan. */
    private const SEDANG_DIPROSES = '1';

    /** Token untuk ditempel di formulir sebagai isian tersembunyi. */
    public static function token(): string
    {
        return Str::random(40);
    }

    /**
     * Klaim token kiriman ini.
     *
     * @return bool `false` bila tokennya sudah pernah dipakai — artinya
     *              kiriman ini pengulangan dan tidak boleh disimpan lagi.
     */
    public static function klaim(Request $request, string $ruang): bool
    {
        $kunci = self::kunci($request, $ruang);

        if ($kunci === null) {
            return true;
        }

        return Cache::add($kunci, self::SEDANG_DIPROSES, self::UMUR);
    }

    /**
     * Catat hasil kiriman yang menang, supaya kiriman kembarnya bisa diberi
     * tahu nomor registrasi yang sudah terbit — bukan sekadar ditolak.
     */
    public static function catat(Request $request, string $ruang, string $hasil): void
    {
        $kunci = self::kunci($request, $ruang);

        if ($kunci !== null) {
            Cache::put($kunci, $hasil, self::UMUR);
        }
    }

    /**
     * Nomor registrasi dari kiriman pertama, bila sudah sempat tercatat.
     *
     * Bisa `null` walau tokennya terklaim: kiriman kembar yang datang satu
     * detik sesudahnya bisa tiba saat berkas pertama masih disimpan.
     */
    public static function hasil(Request $request, string $ruang): ?string
    {
        $kunci = self::kunci($request, $ruang);
        $nilai = $kunci !== null ? Cache::get($kunci) : null;

        return is_string($nilai) && $nilai !== self::SEDANG_DIPROSES ? $nilai : null;
    }

    /**
     * Lepas klaimnya kembali.
     *
     * Dipakai saat penyimpanan gagal: formulirnya ditampilkan ulang dengan
     * isian yang sama, dan pemohon harus bisa menekan Kirim sekali lagi.
     */
    public static function lepas(Request $request, string $ruang): void
    {
        $kunci = self::kunci($request, $ruang);

        if ($kunci !== null) {
            Cache::forget($kunci);
        }
    }

    /**
     * Kunci cache untuk kiriman ini, atau `null` bila tokennya tidak berbentuk
     * token yang pernah kita terbitkan.
     */
    private static function kunci(Request $request, string $ruang): ?string
    {
        $token = (string) $request->input(self::FIELD, '');

        if (!preg_match('/^[A-Za-z0-9]{40}$/', $token)) {
            return null;
        }

        return 'sekali-kirim:'.$ruang.':'.$token;
    }
}
