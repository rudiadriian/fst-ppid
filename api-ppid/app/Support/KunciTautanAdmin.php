<?php

namespace App\Support;

use App\Models\PengirimanTautanAdmin;
use App\Models\PercobaanTautanAdmin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Pembatas permintaan tautan lupa password untuk akun panel.
 *
 * Dua rem yang berbeda tugasnya:
 *
 *   1. **Jeda pendek** — satu tautan per beberapa menit, dihitung dari catatan
 *      pengiriman yang benar-benar terjadi. Ini yang menahan orang menekan
 *      tombol berkali-kali karena emailnya belum sampai.
 *   2. **Tangga bertingkat** — sama seperti kunci masuk: 3 permintaan = 1 jam,
 *      3 berikutnya = 1 hari, 3 berikutnya = 14 hari, lalu akun disuspend. Ini
 *      yang menahan pemakaian jalur kirim email sebagai alat membanjiri kotak
 *      masuk orang.
 *
 * Hitungannya terpisah dari hitungan gagal masuk: seseorang yang benar-benar
 * lupa password tidak boleh ikut terkunci dari mencoba masuk hanya karena
 * meminta tautan beberapa kali.
 */
class KunciTautanAdmin
{
    /**
     * @return array{terkunci: bool, pesan: ?string}
     */
    public static function periksa(Request $request, string $email): array
    {
        $baris = self::baris($request, $email);

        if ($baris && $baris->sedangTerkunci()) {
            return [
                'terkunci' => true,
                'pesan' => 'Terlalu banyak permintaan tautan untuk email ini. Coba lagi '
                    .KunciBertingkat::sisaTerbaca($baris->sisaKunci()).'.',
            ];
        }

        $jeda = (int) config('ppid.akun.jeda_kirim_tautan_menit', 5);

        if ($jeda <= 0) {
            return ['terkunci' => false, 'pesan' => null];
        }

        $terakhir = PengirimanTautanAdmin::query()
            ->where('jenis', PengirimanTautanAdmin::JENIS_LUPA_PASSWORD)
            ->where('created_at', '>=', now()->subMinutes($jeda))
            ->where(function ($q) use ($request, $email) {
                $q->where('email', self::bakukan($email))
                    ->orWhere('ip_address', $request->ip());
            })
            ->orderByDesc('created_at')
            ->first();

        if (!$terakhir) {
            return ['terkunci' => false, 'pesan' => null];
        }

        $menit = max(1, (int) ceil(now()->diffInSeconds($terakhir->created_at->addMinutes($jeda), false) / 60));

        return [
            'terkunci' => true,
            'pesan' => 'Tautan sudah pernah dikirim. Coba lagi dalam '.$menit.' menit — '
                .'periksa juga folder Spam pada email Anda.',
        ];
    }

    /**
     * Catat satu permintaan dan kembalikan akibatnya.
     *
     * Dipanggil untuk setiap permintaan yang lolos pemeriksaan, termasuk yang
     * emailnya tidak terdaftar — kalau hanya yang terdaftar yang dihitung,
     * penghitungnya sendiri berubah menjadi alat menebak email mana yang ada.
     *
     * @return array{pesan: ?string, suspend: bool}
     */
    public static function catat(Request $request, string $email): array
    {
        $baris = self::baris($request, $email) ?? new PercobaanTautanAdmin([
            'identitas' => self::bakukan($email),
            'ip_address' => (string) $request->ip(),
        ]);

        KunciBertingkat::luruhkanBilaUsang($baris, 'jumlah_minta', 'terakhir_minta_pada');

        $baris->jumlah_minta = $baris->jumlah_minta + 1;
        $baris->terakhir_minta_pada = now();

        $akibat = KunciBertingkat::akibat($baris->jumlah_minta);

        if ($akibat['suspend']) {
            $baris->tahap_kunci = KunciBertingkat::TAHAP_SUSPEND;
            $baris->terkunci_sampai = null;
            $baris->save();

            self::suspend($email);

            return [
                'suspend' => true,
                'pesan' => 'Akun ini disuspend karena permintaan tautan yang berulang kali. '
                    .'Hubungi administrator untuk membukanya kembali.',
            ];
        }

        if ($akibat['menit'] !== null) {
            $baris->tahap_kunci = $akibat['tahap'];
            $baris->terkunci_sampai = now()->addMinutes($akibat['menit']);
            $baris->save();

            return [
                'suspend' => false,
                'pesan' => 'Sudah '.$baris->jumlah_minta.' kali meminta tautan. Coba lagi setelah '
                    .KunciBertingkat::durasiTerbaca($akibat['menit']).'.',
            ];
        }

        $baris->save();

        return ['suspend' => false, 'pesan' => null];
    }

    /** Catat pengiriman yang benar-benar terjadi. */
    public static function catatPengiriman(Request $request, string $email): void
    {
        PengirimanTautanAdmin::create([
            'jenis' => PengirimanTautanAdmin::JENIS_LUPA_PASSWORD,
            'email' => self::bakukan($email),
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 250, ''),
            'created_at' => now(),
        ]);
    }

    /** Hapus hitungan setelah password benar-benar diganti. */
    public static function bersihkan(string $email): void
    {
        PercobaanTautanAdmin::query()->where('identitas', self::bakukan($email))->delete();
    }

    private static function suspend(string $email): void
    {
        $user = User::denganEmail($email);

        if (!$user || $user->disuspend_pada !== null) {
            return;
        }

        $user->timestamps = false;
        $user->forceFill([
            'disuspend_pada' => now(),
            'alasan_suspend' => 'Permintaan tautan lupa password berulang kali.',
        ])->saveQuietly();
        $user->timestamps = true;

        AuditLogger::record(null, 'akun_disuspend', User::class, $user->id, null, [
            'email' => $user->email,
            'alasan' => 'Permintaan tautan lupa password berulang kali.',
        ]);
    }

    private static function baris(Request $request, string $email): ?PercobaanTautanAdmin
    {
        return PercobaanTautanAdmin::query()
            ->where('identitas', self::bakukan($email))
            ->where('ip_address', (string) $request->ip())
            ->first();
    }

    private static function bakukan(string $email): string
    {
        return Str::limit(Str::lower(trim($email)), 190, '');
    }
}
