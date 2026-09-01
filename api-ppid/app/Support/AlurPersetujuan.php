<?php

namespace App\Support;

use App\Models\AlurApproval;
use App\Models\AlurApprovalTahap;
use App\Models\ApprovalPengajuan;
use App\Models\KeberatanInformasi;
use App\Models\Notifikasi;
use App\Models\PermohonanInformasi;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * Mesin persetujuan berjenjang.
 *
 * Menjalankan definisi di `alur_approval` / `alur_approval_tahap` atas satu
 * pengajuan, dan hanya itu: perpindahan status pengajuannya sendiri tetap
 * milik controller masing-masing, supaya `permohonan_log_status`, email, dan
 * notifikasi portal tetap ditulis di satu tempat. Yang dikembalikan dari
 * {@see putuskan()} adalah **hasil alur**, bukan status pengajuan — penerjemah
 * hasil ke status ada di controller karena permohonan dan keberatan memakai
 * kosakata status yang berbeda.
 *
 * Aturan yang dijaga di sini:
 *
 *  - Satu pengajuan hanya punya **satu** langkah `menunggu` pada satu waktu.
 *    Jenjang berarti berurutan; dua langkah terbuka sekaligus membuat urutan
 *    kehilangan artinya.
 *  - Yang boleh memutus hanya pengguna ber-role sesuai tahapnya (super admin
 *    selalu boleh, karena ia pula yang menyusun alurnya dan harus bisa
 *    membebaskan berkas yang macet).
 *  - Satu penolakan menutup seluruh sisa jenjang: langkah berikutnya ditandai
 *    `dilewati`, bukan dibiarkan menggantung sebagai `menunggu`.
 */
class AlurPersetujuan
{
    public const JENIS_PERMOHONAN = 'permohonan';

    public const JENIS_KEBERATAN = 'keberatan';

    /** Hasil satu putusan; diterjemahkan controller menjadi status pengajuan. */
    public const HASIL_LANJUT = 'lanjut';

    public const HASIL_DISETUJUI = 'disetujui';

    public const HASIL_DITOLAK = 'ditolak';

    public const HASIL_REVISI = 'revisi';

    /**
     * Status yang berarti berkasnya masih berjalan, per jenis pengajuan.
     *
     * Dipakai {@see pastikanBerjalan()} untuk memutuskan apakah satu pengajuan
     * masih pantas punya jenjang. Yang didaftar sengaja status **akhir**, bukan
     * status awal: status akhir berjumlah tetap dan jarang bertambah,
     * sedangkan status di tengah alur berubah tiap kali alurnya disempurnakan.
     */
    private const STATUS_AKHIR = [
        self::JENIS_PERMOHONAN => ['disetujui', 'ditolak', 'ditolak_sebagian', 'selesai', 'kedaluwarsa'],
        self::JENIS_KEBERATAN => ['selesai', 'ditolak'],
    ];

    public static function jenisDari(Model $pengajuan): string
    {
        return $pengajuan instanceof KeberatanInformasi ? self::JENIS_KEBERATAN : self::JENIS_PERMOHONAN;
    }

    /**
     * Buat langkah persetujuan untuk satu pengajuan.
     *
     * Dipanggil saat pengajuan masuk status "menunggu persetujuan". Seluruh
     * jenjang dibuat sekaligus supaya petugas melihat sisa perjalanannya, bukan
     * satu kotak yang muncul-hilang. Hanya langkah pertama yang `menunggu`.
     *
     * Aman dipanggil ulang: bila masih ada langkah berjalan, tidak ada yang
     * dibuat. Bila putaran sebelumnya sudah tuntas (mis. dikembalikan ke
     * petugas lalu diajukan lagi), langkah lama ditinggalkan sebagai riwayat
     * dan putaran baru dibuat di atasnya.
     *
     * @return Collection<int, ApprovalPengajuan> langkah putaran ini; kosong
     *                                            bila alurnya belum disusun.
     */
    public static function mulai(Model $pengajuan): Collection
    {
        $jenis = self::jenisDari($pengajuan);
        $id = (int) $pengajuan->getKey();

        if (self::berjalan($jenis, $id) !== null) {
            return self::langkahPutaranTerakhir($jenis, $id);
        }

        $alur = AlurApproval::aktifUntuk($jenis);

        if ($alur === null) {
            return new Collection();
        }

        $tahap = $alur->tahapAktif()->with('struktur:id,jabatan,nama')->get();

        if ($tahap->isEmpty()) {
            return new Collection();
        }

        $dibuat = new Collection();

        foreach ($tahap->values() as $index => $satu) {
            /** @var AlurApprovalTahap $satu */
            $pertama = $index === 0;

            $dibuat->push(ApprovalPengajuan::create([
                'jenis' => $jenis,
                'pengajuan_id' => $id,
                'alur_id' => $alur->id,
                'tahap_id' => $satu->id,
                'urutan' => $index + 1,
                'nama_tahap' => $satu->nama,
                'role_id' => $satu->role_id,
                'nama_jabatan' => $satu->struktur?->jabatan,
                'status' => ApprovalPengajuan::MENUNGGU,
                // Hanya langkah berjalan yang punya jam masuk: langkah
                // berikutnya belum dimulai, dan SLA-nya baru berlaku saat
                // gilirannya tiba.
                'tanggal_masuk' => $pertama ? now() : null,
                'batas_waktu' => $pertama && $satu->sla_hari ? now()->addDays($satu->sla_hari) : null,
            ]));
        }

        self::beriTahuPenyetuju($dibuat->first(), $pengajuan);

        return $dibuat;
    }

    /**
     * Pastikan pengajuan yang masih berjalan punya jenjang (langkah 100).
     *
     * Sebelum ini jenjang baru lahir ketika petugas memindahkan status ke
     * "Menunggu Persetujuan" sendiri — dan karena itu tidak pernah lahir:
     * PPID Pelaksana berhenti di "Diproses", berkasnya diam di sana, dan PPID
     * tidak pernah diberi tahu ada yang menunggu putusannya. Pemohon menunggu
     * berkas yang, dari sudut pandang alur, belum pernah masuk.
     *
     * Yang benar: berkas yang masuk **selalu** punya jenjang. Tahap pertamanya
     * milik PPID Pelaksana, dan sejak itu perpindahan berkas ditentukan
     * putusan, bukan dropdown status.
     *
     * Idempoten dan aman dipanggil dari mana saja — termasuk dari endpoint
     * baca. Pengajuan yang sudah tuntas tidak dibuatkan jenjang baru: berkas
     * yang ditutup sebelum alur berjenjang dipakai tidak boleh dibuka lagi
     * hanya karena rinciannya dibuka orang.
     *
     * @return Collection<int, ApprovalPengajuan>
     */
    public static function pastikanBerjalan(Model $pengajuan): Collection
    {
        $jenis = self::jenisDari($pengajuan);
        $id = (int) $pengajuan->getKey();

        if (self::berjalan($jenis, $id) !== null) {
            return self::langkahPutaranTerakhir($jenis, $id);
        }

        if (in_array((string) $pengajuan->status, self::STATUS_AKHIR[$jenis] ?? [], true)) {
            return self::langkahPutaranTerakhir($jenis, $id);
        }

        return self::mulai($pengajuan);
    }

    /** Seluruh langkah satu pengajuan, terurut. */
    public static function langkah(string $jenis, int $pengajuanId): Collection
    {
        return ApprovalPengajuan::where('jenis', $jenis)
            ->where('pengajuan_id', $pengajuanId)
            // `tahap` ikut dimuat agar panel tahu jenjang mana yang tidak diberi
            // hak menolak — jenjang itulah tempat jalur pelayanan ditetapkan,
            // dan formulir putusannya berbeda isi karena itu (langkah 89).
            ->with(['role:id,name,slug', 'pemutus:id,name', 'tahap:id,boleh_tolak'])
            ->orderBy('id')
            ->get();
    }

    /** Langkah yang sedang menunggu putusan; null bila tidak ada. */
    public static function berjalan(string $jenis, int $pengajuanId): ?ApprovalPengajuan
    {
        return ApprovalPengajuan::where('jenis', $jenis)
            ->where('pengajuan_id', $pengajuanId)
            ->where('status', ApprovalPengajuan::MENUNGGU)
            ->whereNotNull('tanggal_masuk')
            ->orderBy('urutan')
            ->first();
    }

    /**
     * Pengguna ini boleh memutus langkah tersebut?
     *
     * Super admin selalu boleh: ia yang menyusun alurnya, dan berkas yang macet
     * karena rolenya kosong atau pemegangnya nonaktif harus tetap bisa
     * dibebaskan tanpa menyunting basis data.
     */
    public static function bolehMemutus(User $user, ApprovalPengajuan $langkah): bool
    {
        $user->loadMissing('role');

        if ($user->role?->slug === 'super-admin') {
            return true;
        }

        return $langkah->role_id !== null && (int) $user->role_id === (int) $langkah->role_id;
    }

    /**
     * Catat putusan atas langkah berjalan lalu majukan alurnya.
     *
     * @param  string  $keputusan  'disetujui' | 'ditolak' | 'revisi'
     * @return string salah satu HASIL_*
     */
    public static function putuskan(
        Model $pengajuan,
        ApprovalPengajuan $langkah,
        User $user,
        string $keputusan,
        ?string $catatan = null
    ): string {
        $langkah->status = $keputusan;
        $langkah->catatan = $catatan;
        $langkah->diputus_oleh = $user->getKey();
        $langkah->tanggal_putusan = now();
        $langkah->save();

        if ($keputusan !== self::HASIL_DISETUJUI) {
            // Ditolak maupun dikembalikan untuk revisi sama-sama menutup
            // putaran ini: jenjang di atasnya tidak pernah kebagian giliran.
            self::tutupSisa($langkah);

            return $keputusan === self::HASIL_REVISI ? self::HASIL_REVISI : self::HASIL_DITOLAK;
        }

        $berikutnya = self::langkahBerikutnya($langkah);

        if ($berikutnya === null) {
            return self::HASIL_DISETUJUI;
        }

        $sla = $berikutnya->tahap?->sla_hari;

        $berikutnya->tanggal_masuk = now();
        $berikutnya->batas_waktu = $sla ? now()->addDays($sla) : null;
        $berikutnya->save();

        self::beriTahuPenyetuju($berikutnya, $pengajuan);

        return self::HASIL_LANJUT;
    }

    /**
     * Buang langkah milik pengajuan yang dihapus.
     *
     * `approval_pengajuan` menunjuk dua tabel sekaligus sehingga tidak bisa
     * dipasangi foreign key; pembersihannya dikerjakan di sini.
     */
    public static function bersihkan(string $jenis, int $pengajuanId): void
    {
        ApprovalPengajuan::where('jenis', $jenis)->where('pengajuan_id', $pengajuanId)->delete();
    }

    // -----------------------------------------------------------------
    // Internal
    // -----------------------------------------------------------------

    /** Langkah putaran yang sama dengan langkah berjalan (satu `alur_id`). */
    private static function langkahPutaranTerakhir(string $jenis, int $pengajuanId): Collection
    {
        return ApprovalPengajuan::where('jenis', $jenis)
            ->where('pengajuan_id', $pengajuanId)
            ->orderBy('id')
            ->get();
    }

    private static function langkahBerikutnya(ApprovalPengajuan $langkah): ?ApprovalPengajuan
    {
        return ApprovalPengajuan::where('jenis', $langkah->jenis)
            ->where('pengajuan_id', $langkah->pengajuan_id)
            ->where('status', ApprovalPengajuan::MENUNGGU)
            ->where('id', '>', $langkah->id)
            ->with('tahap:id,sla_hari')
            ->orderBy('urutan')
            ->first();
    }

    private static function tutupSisa(ApprovalPengajuan $langkah): void
    {
        ApprovalPengajuan::where('jenis', $langkah->jenis)
            ->where('pengajuan_id', $langkah->pengajuan_id)
            ->where('status', ApprovalPengajuan::MENUNGGU)
            ->where('id', '>', $langkah->id)
            ->update(['status' => 'dilewati']);
    }

    /**
     * Beri tahu pemegang role tahap ini bahwa gilirannya tiba.
     *
     * Tanpa ini jenjang berikutnya hanya terlihat oleh yang kebetulan membuka
     * modulnya, dan berkas menunggu tanpa ada yang tahu. Gagal menulis
     * notifikasi tidak boleh membatalkan putusan yang sudah tersimpan.
     *
     * Rolenya saja tidak cukup jadi dasar penerima. Tahap alur menyebut role,
     * sedangkan yang menentukan seseorang bisa membuka dan memutuskan berkasnya
     * adalah hak modul (`role_modul_akses`). Role yang dipasang di alur tetapi
     * hak modulnya belum dibuka hanya akan menerima tautan yang berujung
     * "Akses ditolak", jadi hak `view` + `approve` ikut disyaratkan di sini —
     * sama dengan yang dijaga middleware `akses:{modul},approve` di route.
     */
    private static function beriTahuPenyetuju(?ApprovalPengajuan $langkah, Model $pengajuan): void
    {
        if ($langkah === null || $langkah->role_id === null) {
            return;
        }

        try {
            $keberatan = $pengajuan instanceof KeberatanInformasi;

            /*
             * Keberatan tampil sebagai kategori di dalam modul Permohonan sejak
             * langkah 89 dan menunya sendiri sudah dilepas, jadi baik penerima
             * maupun tautannya mengikuti modul Permohonan. `jenis` yang memberi
             * tahu halaman itu rincian mana yang harus dibuka.
             */
            $modulPanel = 'permohonan';
            $tautan = "/ppid/{$modulPanel}?detail=".$langkah->pengajuan_id
                .($keberatan ? '&jenis=keberatan' : '');

            $penerima = User::where('users.role_id', $langkah->role_id)
                ->where('users.is_active', true)
                ->whereExists(fn ($q) => $q
                    ->from('role_modul_akses as akses')
                    ->join('modul_sistem as modul', 'modul.id', '=', 'akses.modul_id')
                    ->whereColumn('akses.role_id', 'users.role_id')
                    ->where('modul.slug', $modulPanel)
                    ->where('akses.can_view', true)
                    ->where('akses.can_approve', true))
                ->pluck('users.id');

            if ($penerima->isEmpty()) {
                return;
            }

            $nomor = self::nomor($pengajuan, $keberatan);
            $label = $keberatan ? 'Keberatan Informasi' : 'Permohonan Informasi';

            foreach ($penerima as $userId) {
                // Satu tahap hanya menunggu sekali. Alur yang dihitung ulang
                // (mis. putusan tahap sebelumnya disimpan dua kali) tidak boleh
                // menumpuk pemberitahuan untuk giliran yang sama.
                $sudahAda = Notifikasi::where('user_id', $userId)
                    ->where('type', 'approval_menunggu')
                    ->where('is_read', false)
                    ->whereRaw("data->>'approval_id' = ?", [(string) $langkah->getKey()])
                    ->exists();

                if ($sudahAda) {
                    continue;
                }

                Notifikasi::create([
                    'user_id' => $userId,
                    'type' => 'approval_menunggu',
                    'message' => "{$label} {$nomor} menunggu persetujuan Anda pada tahap "
                        ."{$langkah->urutan}. {$langkah->nama_tahap}.",
                    'is_read' => false,
                    'data' => [
                        'title' => 'Persetujuan Menunggu',
                        'icon' => 'lucide:stamp',
                        'link' => $tautan,
                        'useRouter' => true,
                        'variant' => 'warning',
                        'modul' => $modulPanel,
                        'approval_id' => $langkah->getKey(),
                    ],
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('[PPID] Gagal memberi tahu penyetuju: '.$e->getMessage());
        }
    }

    private static function nomor(Model $pengajuan, bool $keberatan): string
    {
        $nomor = $keberatan
            ? (string) ($pengajuan->kode_keberatan ?? '')
            : (string) ($pengajuan instanceof PermohonanInformasi ? $pengajuan->kode_permohonan : '');

        return $nomor !== '' ? $nomor : '(tanpa nomor)';
    }
}
