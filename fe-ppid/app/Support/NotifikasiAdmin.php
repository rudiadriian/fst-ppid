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
    /**
     * Slug modul be-ppid yang menangani masing-masing formulir.
     *
     * Keberatan tidak punya slug sendiri di sini meskipun modulnya masih ada:
     * sejak langkah 89 isinya tampil sebagai salah satu kategori di modul
     * Permohonan dan entri menunya dilepas. Penerima notifikasi harus orang
     * yang menunya memang memuat halaman tujuan tautannya — kalau daftarnya
     * diambil dari modul `keberatan` sementara tautannya membuka halaman
     * Permohonan, ada role yang diberi tahu tapi ditolak saat mengklik, dan
     * ada role yang berhak menangani tapi tidak pernah diberi tahu.
     */
    private const MODUL_PERMOHONAN = 'permohonan';

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
            ],
            'pemohon_id'
        );
    }

    /**
     * Berkas Verifikasi Data Diri Pemohon menunggu diperiksa.
     *
     * Ini notifikasi yang menuntut tindakan, jadi dibedakan dari pendaftaran:
     * teksnya menyebut pengiriman ke berapa dan sisa kesempatannya, supaya
     * petugas tahu bobot keputusannya sebelum membuka berkasnya.
     *
     * Pemohon boleh memperbaiki dan mengirim ulang berkasnya berkali-kali
     * sebelum diperiksa. Tiap kiriman bukan pekerjaan baru, melainkan
     * pekerjaan yang sama dengan isi yang lebih baru — karena itu barisnya
     * diperbarui, bukan ditambah (lihat `kirim()`).
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
            ],
            'pemohon_id'
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
                'kode_keberatan' => $keberatan->kode_keberatan,
                'kode_permohonan' => $permohonan->kode_permohonan,
            ],
            'permohonan_id'
        );
    }

    public static function keberatanBaru(KeberatanInformasi $keberatan, PermohonanInformasi $permohonan, Pemohon $pemohon): void
    {
        self::kirim(
            self::MODUL_PERMOHONAN,
            'keberatan_baru',
            // Dua nomor sekaligus: nomor keberatannya sendiri untuk diarsipkan,
            // nomor permohonan induknya supaya petugas tahu perkara mana yang
            // dipersoalkan tanpa membuka rinciannya lebih dulu.
            __('Keberatan :kode atas permohonan :induk dari :nama menunggu ditangani.', [
                'kode' => $keberatan->kode_keberatan ?? '-',
                'induk' => $permohonan->kode_permohonan,
                'nama' => self::namaPemohon($pemohon),
            ], self::BAHASA_PANEL),
            [
                'title' => __('Keberatan informasi baru', [], self::BAHASA_PANEL),
                'icon' => 'lucide:file-warning',
                // Keberatan dibuka dari daftar gabungan di modul Permohonan;
                // `jenis` yang menentukan rincian mana yang dibuka, karena
                // nomor barisnya bisa sama dengan nomor permohonan.
                'link' => '/ppid/permohonan?detail='.$keberatan->id.'&jenis=keberatan',
                'useRouter' => true,
                'variant' => 'warning',
                'keberatan_id' => $keberatan->id,
                'permohonan_id' => $permohonan->id,
                'kode_keberatan' => $keberatan->kode_keberatan,
                'kode_permohonan' => $permohonan->kode_permohonan,
            ],
            'keberatan_id'
        );
    }

    /**
     * Tulis satu baris notifikasi untuk tiap admin penerima.
     *
     * Notifikasi yang **belum dibaca** untuk pokok yang sama tidak digandakan,
     * melainkan ditimpa isinya lalu dinaikkan waktunya ke atas lonceng. Satu
     * berkas yang dikirim ulang tiga kali adalah satu pekerjaan yang menunggu,
     * bukan tiga — dan pesan yang lama justru menyesatkan karena masih menyebut
     * pengiriman ke-1 saat yang menunggu diperiksa sudah kiriman ke-3.
     *
     * Yang sudah dibaca sengaja dibiarkan: petugas menandainya karena sudah
     * menanganinya, jadi kiriman berikutnya memang pemberitahuan baru.
     *
     * @param  array<string, mixed>  $data
     * @param  string  $kunciPokok  nama kolom di `$data` yang menandai pokoknya
     *                              (`pemohon_id`, `permohonan_id`, …)
     */
    private static function kirim(string $modulSlug, string $tipe, string $pesan, array $data, string $kunciPokok): void
    {
        try {
            // Modulnya ikut dicatat supaya api-ppid bisa menyembunyikan baris
            // ini kalau hak lihat rolenya dicabut setelah notifikasinya ditulis
            // (lihat `NotifikasiController::batasiKeModulBolehLihat`).
            $data['modul'] = $modulSlug;

            $idPokok = $data[$kunciPokok] ?? null;

            // Nama kolomnya ditempel ke dalam SQL karena operator `->>` di
            // PostgreSQL tidak bisa menerima parameter bertipe tak dikenal.
            // Isinya selalu tetapan di berkas ini, tetapi tetap disaring supaya
            // tidak ada jalan masuk dari luar seandainya pemanggilnya berubah.
            $kunciAman = preg_match('/^[a-z_]+$/', $kunciPokok) === 1;

            foreach (self::penerima($modulSlug) as $userId) {
                $lama = ($idPokok === null || !$kunciAman) ? null : Notifikasi::query()
                    ->where('user_id', $userId)
                    ->where('type', $tipe)
                    ->where('is_read', false)
                    ->whereRaw("data->>'{$kunciPokok}' = ?", [(string) $idPokok])
                    ->orderByDesc('id')
                    ->first();

                if ($lama) {
                    // `notifikasi` tidak punya `updated_at`; waktu tayang di
                    // lonceng dibaca dari `created_at`, jadi itu yang digeser.
                    $lama->forceFill([
                        'message' => $pesan,
                        'data' => $data,
                        'created_at' => now(),
                    ])->save();

                    continue;
                }

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
        $lewatMatrix = DB::table('users')
            ->join('role_modul_akses as akses', 'akses.role_id', '=', 'users.role_id')
            ->join('modul_sistem as modul', 'modul.id', '=', 'akses.modul_id')
            ->where('modul.slug', $modulSlug)
            ->where('akses.can_view', true)
            ->where('users.is_active', true)
            ->whereNull('users.deleted_at')
            ->distinct()
            ->pluck('users.id');

        /*
         * Super-admin ikut tanpa melihat matrix, sama seperti middleware
         * `akses:` di api-ppid yang meloloskannya lebih dulu. Barisnya di
         * `role_modul_akses` memang lengkap hari ini, tetapi menggantungkan
         * pemberitahuan pada baris itu berarti satu modul baru yang belum
         * dicentang membuat super-admin diam-diam berhenti diberi tahu.
         */
        $superAdmin = DB::table('users')
            ->join('roles', 'roles.id', '=', 'users.role_id')
            ->where('roles.slug', 'super-admin')
            ->where('users.is_active', true)
            ->whereNull('users.deleted_at')
            ->pluck('users.id');

        return $lewatMatrix->concat($superAdmin)
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    private static function namaPemohon(Pemohon $pemohon): string
    {
        $nama = trim((string) $pemohon->nama);

        return $nama !== '' ? $nama : __('pemohon', [], self::BAHASA_PANEL);
    }
}
