<?php

namespace App\Support;

use App\Models\KeberatanInformasi;
use App\Models\Notifikasi;
use App\Models\Pemohon;
use App\Models\PermohonanInformasi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Pemberitahuan ke panel admin saat pemohon mengirim formulir.
 *
 * Barisnya ditulis ke tabel `notifikasi` di `ppiddb`, yaitu sumber yang dibaca
 * lonceng notifikasi be-ppid lewat `GET /v1/notifikasi`. Penerimanya bukan
 * "semua pengguna": hanya akun aktif yang rolenya punya hak lihat pada modul
 * terkait, sehingga admin yang tidak menangani permohonan tidak ikut kebanjiran.
 *
 * Gagal menulis notifikasi tidak boleh menggagalkan pengiriman formulir —
 * setiap galat dicatat di log lalu diabaikan.
 */
class NotifikasiAdmin
{
    /** Slug modul be-ppid yang menangani masing-masing formulir. */
    private const MODUL_PERMOHONAN = 'permohonan';

    private const MODUL_KEBERATAN = 'keberatan';

    public static function permohonanBaru(PermohonanInformasi $permohonan, Pemohon $pemohon): void
    {
        self::kirim(
            self::MODUL_PERMOHONAN,
            'permohonan_baru',
            __('Permohonan :kode dari :nama menunggu ditangani.', [
                'kode' => $permohonan->kode_permohonan,
                'nama' => self::namaPemohon($pemohon),
            ]),
            [
                'title' => __('Permohonan informasi baru'),
                'icon' => 'lucide:inbox',
                'link' => '/ppid/permohonan',
                'useRouter' => true,
                'variant' => 'primary',
                'permohonan_id' => $permohonan->id,
                'kode_permohonan' => $permohonan->kode_permohonan,
            ]
        );
    }

    public static function keberatanBaru(KeberatanInformasi $keberatan, PermohonanInformasi $permohonan, Pemohon $pemohon): void
    {
        self::kirim(
            self::MODUL_KEBERATAN,
            'keberatan_baru',
            __('Keberatan atas permohonan :kode dari :nama menunggu ditangani.', [
                'kode' => $permohonan->kode_permohonan,
                'nama' => self::namaPemohon($pemohon),
            ]),
            [
                'title' => __('Keberatan informasi baru'),
                'icon' => 'lucide:file-warning',
                'link' => '/ppid/keberatan',
                'useRouter' => true,
                'variant' => 'warning',
                'keberatan_id' => $keberatan->id,
                'permohonan_id' => $permohonan->id,
                'kode_permohonan' => $permohonan->kode_permohonan,
            ]
        );
    }

    /**
     * Tulis satu baris notifikasi untuk tiap admin penerima.
     *
     * @param  array<string, mixed>  $data
     */
    private static function kirim(string $modulSlug, string $tipe, string $pesan, array $data): void
    {
        try {
            foreach (self::penerima($modulSlug) as $userId) {
                Notifikasi::create([
                    'user_id' => $userId,
                    'type' => $tipe,
                    'message' => $pesan,
                    'is_read' => false,
                    'data' => $data,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('[PPID] Gagal mengirim notifikasi admin ('.$tipe.'): '.$e->getMessage());
        }
    }

    /**
     * Id pengguna panel yang berhak melihat modul tersebut.
     *
     * @return array<int, int>
     */
    private static function penerima(string $modulSlug): array
    {
        return DB::table('users')
            ->join('role_modul_akses as akses', 'akses.role_id', '=', 'users.role_id')
            ->join('modul_sistem as modul', 'modul.id', '=', 'akses.modul_id')
            ->where('modul.slug', $modulSlug)
            ->where('akses.can_view', true)
            ->where('users.is_active', true)
            ->whereNull('users.deleted_at')
            ->distinct()
            ->pluck('users.id')
            ->map(fn ($id) => (int) $id)
            ->all();
    }

    private static function namaPemohon(Pemohon $pemohon): string
    {
        $nama = trim((string) $pemohon->nama);

        return $nama !== '' ? $nama : __('pemohon');
    }
}
