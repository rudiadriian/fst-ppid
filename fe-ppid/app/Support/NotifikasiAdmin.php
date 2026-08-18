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

    /**
     * Isi notifikasi selalu Bahasa Indonesia: pembacanya petugas di be-ppid,
     * bukan pengunjung. Tanpa ini, pengunjung yang memakai situs versi Inggris
     * membuat notifikasi berbahasa Inggris di panel admin.
     */
    private const BAHASA_PANEL = 'id';

    /**
     * Akun pengunjung baru mendaftar.
     *
     * Sekadar pemberitahuan: pada tahap ini belum ada yang perlu diperiksa —
     * berkas identitasnya baru dikirim pada langkah berikutnya. Tautannya
     * mengarah ke modul Pemohon supaya petugas bisa langsung melihat siapa yang
     * masuk hari itu.
     */
    public static function pendaftaranBaru(Pemohon $pemohon): void
    {
        self::kirim(
            self::MODUL_PERMOHONAN,
            'pemohon_baru',
            __('Akun pemohon baru: :nama (:email).', [
                'nama' => self::namaPemohon($pemohon),
                'email' => $pemohon->email,
            ], self::BAHASA_PANEL),
            [
                'title' => __('Pendaftaran akun pemohon', [], self::BAHASA_PANEL),
                'icon' => 'lucide:user-plus',
                'link' => '/ppid/pemohon?detail='.$pemohon->id,
                'useRouter' => true,
                'variant' => 'secondary',
                'pemohon_id' => $pemohon->id,
            ]
        );
    }

    /**
     * Berkas Verifikasi Data Diri Pemohon menunggu diperiksa.
     *
     * Ini notifikasi yang menuntut tindakan, jadi dibedakan dari pendaftaran:
     * teksnya menyebut pengiriman ke berapa dan sisa kesempatannya, supaya
     * petugas tahu bobot keputusannya sebelum membuka berkasnya.
     */
    public static function verifikasiPemohonMenunggu(Pemohon $pemohon): void
    {
        $pengirimanKe = (int) $pemohon->jumlah_ditolak + 1;

        self::kirim(
            self::MODUL_PERMOHONAN,
            'verifikasi_pemohon',
            __('Data diri :nama menunggu verifikasi (pengiriman ke-:ke dari :batas).', [
                'nama' => self::namaPemohon($pemohon),
                'ke' => $pengirimanKe,
                'batas' => Pemohon::BATAS_DITOLAK,
            ], self::BAHASA_PANEL),
            [
                'title' => __('Verifikasi data pemohon', [], self::BAHASA_PANEL),
                'icon' => 'lucide:user-check',
                'link' => '/ppid/pemohon?detail='.$pemohon->id,
                'useRouter' => true,
                'variant' => 'warning',
                'pemohon_id' => $pemohon->id,
            ]
        );
    }

    public static function permohonanBaru(PermohonanInformasi $permohonan, Pemohon $pemohon): void
    {
        self::kirim(
            self::MODUL_PERMOHONAN,
            'permohonan_baru',
            __('Permohonan :kode dari :nama menunggu ditangani.', [
                'kode' => $permohonan->kode_permohonan,
                'nama' => self::namaPemohon($pemohon),
            ], self::BAHASA_PANEL),
            [
                'title' => __('Permohonan informasi baru', [], self::BAHASA_PANEL),
                'icon' => 'lucide:inbox',
                'link' => '/ppid/permohonan?detail='.$permohonan->id,
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
            ], self::BAHASA_PANEL),
            [
                'title' => __('Keberatan informasi baru', [], self::BAHASA_PANEL),
                'icon' => 'lucide:file-warning',
                'link' => '/ppid/keberatan?detail='.$keberatan->id,
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

        return $nama !== '' ? $nama : __('pemohon', [], self::BAHASA_PANEL);
    }
}
