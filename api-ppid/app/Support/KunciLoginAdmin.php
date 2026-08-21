<?php

namespace App\Support;

use App\Models\PercobaanLoginAdmin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Kunci masuk bertingkat untuk akun panel admin.
 *
 * Tangganya di `KunciBertingkat`: tiap 3 kegagalan menaikkan satu tahap —
 * 1 jam, 1 hari, 14 hari, lalu akun disuspend.
 *
 * Hitungannya disimpan di basis data, bukan cache, karena masa kuncinya bisa
 * berminggu-minggu: cache berkas bisa dibersihkan tanpa sengaja dan kuncinya
 * ikut hilang bersamanya.
 */
class KunciLoginAdmin
{
    /**
     * Keadaan kunci saat ini.
     *
     * @return array{terkunci: bool, pesan: ?string, detik: int}
     */
    public static function periksa(Request $request, string $email): array
    {
        $baris = self::baris($request, $email);

        if (!$baris || !$baris->sedangTerkunci()) {
            return ['terkunci' => false, 'pesan' => null, 'detik' => 0];
        }

        $detik = $baris->sisaKunci();

        return [
            'terkunci' => true,
            'detik' => $detik,
            'pesan' => 'Akun ini sementara dikunci karena terlalu banyak percobaan masuk yang gagal. '
                .'Coba lagi '.KunciBertingkat::sisaTerbaca($detik).', atau pakai tautan Lupa password.',
        ];
    }

    /**
     * Catat satu kegagalan dan kembalikan akibatnya.
     *
     * Menyuspend akun pada tahap keempat. Yang disuspend hanya akun yang
     * benar-benar ada — email asing tidak boleh membuat baris `users` baru
     * ataupun membocorkan bahwa ia tidak terdaftar.
     *
     * @return array{pesan: ?string, suspend: bool, terkunci: bool}
     */
    public static function catatGagal(Request $request, string $email): array
    {
        $baris = self::baris($request, $email) ?? new PercobaanLoginAdmin([
            'identitas' => self::bakukan($email),
            'ip_address' => (string) $request->ip(),
        ]);

        KunciBertingkat::luruhkanBilaUsang($baris, 'jumlah_gagal', 'terakhir_gagal_pada');

        $baris->user_agent = Str::limit((string) $request->userAgent(), 250, '');
        $baris->jumlah_gagal = $baris->jumlah_gagal + 1;
        $baris->terakhir_gagal_pada = now();

        $akibat = KunciBertingkat::akibat($baris->jumlah_gagal);

        if ($akibat['suspend']) {
            $baris->tahap_kunci = KunciBertingkat::TAHAP_SUSPEND;
            // Tidak diberi `terkunci_sampai`: yang menutup akses sekarang
            // adalah suspend pada akunnya, dan itu hanya dibuka administrator.
            $baris->terkunci_sampai = null;
            $baris->save();

            self::suspend($email);

            return [
                'suspend' => true,
                'terkunci' => true,
                'pesan' => 'Akun ini disuspend karena percobaan masuk yang gagal berulang kali. '
                    .'Hubungi administrator untuk membukanya kembali.',
            ];
        }

        if ($akibat['menit'] !== null) {
            $baris->tahap_kunci = $akibat['tahap'];
            $baris->terkunci_sampai = now()->addMinutes($akibat['menit']);
            $baris->save();

            return [
                'suspend' => false,
                'terkunci' => true,
                'pesan' => 'Sudah '.$baris->jumlah_gagal.' kali gagal masuk. Demi keamanan akun, '
                    .'coba lagi setelah '.KunciBertingkat::durasiTerbaca($akibat['menit']).'.',
            ];
        }

        $baris->save();

        $setiap = max(1, (int) config('ppid.akun.gagal_per_tahap', 3));
        $sisa = $setiap - ($baris->jumlah_gagal % $setiap);

        return [
            'suspend' => false,
            'terkunci' => false,
            // Sisa kesempatan disebut supaya orang yang benar-benar lupa
            // passwordnya berhenti menebak dan beralih ke Lupa password,
            // bukan tiba-tiba mendapati akunnya terkunci sejam.
            'pesan' => 'Email atau kata sandi salah. Sisa '.$sisa.' percobaan sebelum akun dikunci sementara.',
        ];
    }

    /** Hapus hitungan setelah berhasil masuk. */
    public static function bersihkan(Request $request, string $email): void
    {
        PercobaanLoginAdmin::query()
            ->where('identitas', self::bakukan($email))
            ->where('ip_address', (string) $request->ip())
            ->delete();
    }

    private static function suspend(string $email): void
    {
        $user = User::denganEmail($email);

        if (!$user || $user->disuspend_pada !== null) {
            return;
        }

        // `timestamps` dimatikan sebentar: ini tindakan sistem, bukan
        // penyuntingan data pengguna oleh siapa pun, jadi kolom "Diubah" pada
        // modul Pengguna tidak boleh ikut terisi.
        $user->timestamps = false;
        $user->forceFill([
            'disuspend_pada' => now(),
            'alasan_suspend' => 'Percobaan masuk gagal berulang kali.',
        ])->saveQuietly();
        $user->timestamps = true;

        AuditLogger::record(null, 'akun_disuspend', User::class, $user->id, null, [
            'email' => $user->email,
            'alasan' => 'Percobaan masuk gagal berulang kali.',
        ]);
    }

    private static function baris(Request $request, string $email): ?PercobaanLoginAdmin
    {
        return PercobaanLoginAdmin::query()
            ->where('identitas', self::bakukan($email))
            ->where('ip_address', (string) $request->ip())
            ->first();
    }

    /** Bentuk baku: huruf kecil tanpa spasi berlebih. */
    private static function bakukan(string $email): string
    {
        return Str::limit(Str::lower(trim($email)), 190, '');
    }
}
