<?php

namespace App\Support;

use App\Models\PercobaanLoginPemohon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Kunci masuk bertingkat untuk akun pengunjung.
 *
 * Setiap tiga kegagalan berturut-turut menaikkan masa tunggu: 3 kali pertama
 * satu jam, 3 kali berikutnya 24 jam, 3 kali berikutnya lagi 72 jam, dan
 * seterusnya tetap 72 jam. Hitungannya disimpan di basis data karena masa
 * kuncinya bisa berhari-hari — cache berkas bisa terhapus tanpa sengaja dan
 * kuncinya ikut hilang.
 *
 * **Kunci dipasang per kombinasi identitas + alamat IP, bukan per identitas
 * saja.** Mengunci per identitas berarti siapa pun yang tahu email seorang
 * pemohon bisa mengunci akun itu selama 72 jam hanya dengan sengaja salah
 * password sembilan kali — pemblokiran layanan yang lebih merugikan daripada
 * serangan yang hendak dicegah. Penyerang sungguhan tetap tertahan karena
 * kegagalannya menumpuk pada IP-nya sendiri, dan penyebaran lewat banyak IP
 * masih dibatasi rem per-IP per menit di `LoginPemohonRequest`.
 */
class KunciLoginPemohon
{
    /**
     * Tolak bila kombinasi ini sedang dalam masa tunggu.
     *
     * @throws ValidationException
     */
    public static function pastikanTidakTerkunci(Request $request, string $identitas, string $kunciError = 'identitas'): void
    {
        $baris = self::baris($request, $identitas);

        if (!$baris || !$baris->sedangTerkunci()) {
            return;
        }

        throw ValidationException::withMessages([
            $kunciError => __('Akun ini sementara dikunci karena terlalu banyak percobaan masuk yang gagal. Coba lagi :waktu, atau pakai tautan Lupa password.', [
                'waktu' => $baris->terkunci_sampai->diffForHumans(),
            ]),
        ]);
    }

    /**
     * Catat satu kegagalan; pasang kunci bila kelipatan tiga tercapai.
     *
     * @throws ValidationException saat kunci baru mulai berlaku
     */
    public static function catatGagal(Request $request, string $identitas, string $kunciError = 'identitas'): void
    {
        $tahapan = (array) config('ppid.akun.tahap_kunci_menit', [60, 1440, 4320]);
        $setiap = max(1, (int) config('ppid.akun.gagal_per_tahap', 3));
        $resetJam = (int) config('ppid.akun.reset_hitungan_gagal_jam', 72);

        $baris = self::baris($request, $identitas) ?? new PercobaanLoginPemohon([
            'identitas' => self::bakukan($identitas),
            'ip_address' => (string) $request->ip(),
        ]);

        // Kegagalan lama tidak boleh menyeret orang yang sekadar lupa password
        // beberapa bulan lalu langsung ke tahap tertinggi.
        if (
            $baris->exists
            && !$baris->sedangTerkunci()
            && $baris->terakhir_gagal_pada !== null
            && $baris->terakhir_gagal_pada->lt(now()->subHours($resetJam))
        ) {
            $baris->jumlah_gagal = 0;
            $baris->tahap_kunci = 0;
        }

        $baris->penanda_perangkat = PenandaPerangkat::ambil($request);
        $baris->jumlah_gagal = $baris->jumlah_gagal + 1;
        $baris->terakhir_gagal_pada = now();

        $kunciBaru = null;

        if ($baris->jumlah_gagal % $setiap === 0) {
            $tahap = intdiv($baris->jumlah_gagal, $setiap);
            $menit = (int) ($tahapan[min($tahap, count($tahapan)) - 1] ?? end($tahapan));

            $baris->tahap_kunci = $tahap;
            $baris->terkunci_sampai = now()->addMinutes($menit);
            $kunciBaru = $menit;
        }

        $baris->save();

        if ($kunciBaru !== null) {
            throw ValidationException::withMessages([
                $kunciError => __('Sudah :jumlah kali gagal masuk. Demi keamanan akun, coba lagi setelah :durasi.', [
                    'jumlah' => $baris->jumlah_gagal,
                    'durasi' => self::durasiTerbaca($kunciBaru),
                ]),
            ]);
        }
    }

    /** Hapus hitungan setelah berhasil masuk. */
    public static function bersihkan(Request $request, string $identitas): void
    {
        PercobaanLoginPemohon::query()
            ->where('identitas', self::bakukan($identitas))
            ->where('ip_address', (string) $request->ip())
            ->delete();
    }

    private static function baris(Request $request, string $identitas): ?PercobaanLoginPemohon
    {
        return PercobaanLoginPemohon::query()
            ->where('identitas', self::bakukan($identitas))
            ->where('ip_address', (string) $request->ip())
            ->first();
    }

    /**
     * Bentuk baku identitas: huruf kecil dan tanpa spasi berlebih, supaya
     * "Budi@Mail.com" dan "budi@mail.com " tidak dihitung sebagai dua orang.
     */
    private static function bakukan(string $identitas): string
    {
        return Str::limit(Str::lower(trim($identitas)), 200, '');
    }

    private static function durasiTerbaca(int $menit): string
    {
        if ($menit % 1440 === 0) {
            return __(':jumlah jam', ['jumlah' => intdiv($menit, 60)]);
        }

        return $menit >= 60
            ? __(':jumlah jam', ['jumlah' => intdiv($menit, 60)])
            : __(':jumlah menit', ['jumlah' => $menit]);
    }
}
