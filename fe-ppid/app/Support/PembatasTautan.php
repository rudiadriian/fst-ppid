<?php

namespace App\Support;

use App\Models\PengirimanTautanAkun;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Pembatas pengiriman tautan verifikasi pendaftaran dan lupa password.
 *
 * Aturannya: satu tautan per 30 menit. Yang dihitung ada tiga hal terpisah —
 * **email tujuan**, **alamat IP**, dan **penanda perangkat** — sehingga
 * mengganti salah satunya tidak mengosongkan hitungan yang lain. Tanpa hitungan
 * per-IP, satu orang bisa menghabiskan kuota email hanya dengan mengetik alamat
 * yang berbeda-beda.
 *
 * Setiap pengiriman dicatat ke `pengiriman_tautan_akun` lengkap dengan asal
 * permintaannya, jadi jejaknya tetap ada walau kuncinya sudah lewat.
 *
 * Soal MAC address: lihat catatan di `App\Support\PenandaPerangkat` — alamat itu
 * tidak pernah sampai ke server, penanda perangkat adalah penggantinya.
 */
class PembatasTautan
{
    /**
     * Tolak bila salah satu penghitung masih dalam masa tunggu.
     *
     * @param  string  $jenis  PengirimanTautanAkun::JENIS_*
     * @param  string  $kunciError  nama isian tempat pesannya ditempel
     *
     * @throws ValidationException
     */
    public static function pastikanBoleh(Request $request, string $jenis, string $email, string $kunciError): void
    {
        $jeda = (int) config('ppid.akun.jeda_kirim_tautan_menit', 30);

        if ($jeda <= 0) {
            return;
        }

        $sejak = now()->subMinutes($jeda);
        $email = Str::lower(trim($email));

        $terakhir = PengirimanTautanAkun::query()
            ->where('jenis', $jenis)
            ->where('created_at', '>=', $sejak)
            ->where(function ($q) use ($request, $email) {
                $q->where('email', $email)
                    ->orWhere('ip_address', $request->ip())
                    ->orWhere('penanda_perangkat', PenandaPerangkat::ambil($request));
            })
            ->orderByDesc('created_at')
            ->first();

        if (!$terakhir) {
            return;
        }

        $menit = max(1, (int) ceil(now()->diffInSeconds($terakhir->created_at->addMinutes($jeda), false) / 60));

        throw ValidationException::withMessages([
            $kunciError => __('Tautan sudah pernah dikirim. Silakan coba lagi dalam :menit menit — periksa juga folder Spam pada email Anda.', [
                'menit' => $menit,
            ]),
        ]);
    }

    /** Catat satu pengiriman yang benar-benar terjadi. */
    public static function catat(Request $request, string $jenis, string $email): void
    {
        PengirimanTautanAkun::create([
            'jenis' => $jenis,
            'email' => Str::lower(trim($email)),
            'ip_address' => $request->ip(),
            'penanda_perangkat' => PenandaPerangkat::ambil($request),
            'user_agent' => PenandaPerangkat::sidikUserAgent($request),
        ]);
    }
}
