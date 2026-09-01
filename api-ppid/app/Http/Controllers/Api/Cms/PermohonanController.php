<?php

namespace App\Http\Controllers\Api\Cms;

use App\Http\Controllers\Api\Concerns\MenanganiPersetujuan;
use App\Http\Controllers\Api\CrudController;
use App\Models\ArsipDokumen;
use App\Models\PermohonanInformasi;
use App\Models\PermohonanLogStatus;
use App\Models\PermohonanTanggapanFile;
use App\Support\AlurPersetujuan;
use App\Support\AuditLogger;
use App\Support\EmailPemohon;
use App\Support\NotifikasiPortal;
use App\Support\SlaLayanan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Modul permohonan informasi.
 *
 * Modul ini tidak punya jalur tulis biasa sama sekali: `store`, `update`, dan
 * `destroy` tidak didaftarkan di `routes/api.php`. Isi permohonan ditulis
 * pemohon sendiri lewat portal, jadi petugas tidak boleh membuat, menyunting,
 * maupun menghapusnya dari panel — termasuk mencatatkan permohonan baru atas
 * nama orang lain. Yang tersisa untuk petugas ada empat, semuanya lewat
 * endpoint khusus dan semuanya meninggalkan jejak:
 *
 *   - `ubahStatus()`            → `permohonan_log_status` + `audit_log`
 *   - `putuskanPersetujuan()`   → `approval_pengajuan` + `audit_log`
 *   - `tambahTanggapanFile()`   → `permohonan_tanggapan_files`
 *   - `hapusTanggapanFile()`
 */
class PermohonanController extends CrudController
{
    use MenanganiPersetujuan;

    protected string $model = PermohonanInformasi::class;

    protected string $modulSlug = 'permohonan';

    protected array $searchable = ['kode_permohonan', 'rincian_informasi', 'tujuan_penggunaan'];

    protected array $sortable = ['id', 'kode_permohonan', 'status', 'tanggal_permohonan', 'batas_waktu_tanggapan'];

    protected string $defaultSort = '-tanggal_permohonan';

    protected array $withList = ['pemohon:id,nama,email,jenis_pemohon', 'kategori:id,nama', 'petugas:id,name'];

    protected array $withDetail = [
        'pemohon',
        'kategori:id,nama',
        'petugas:id,name',
        'files',
        'tanggapanFiles',
        'logStatus.petugas:id,name',
        // Jenjang persetujuan berjalan; `approval` yang lama tetap ikut sebagai
        // arsip putusan sebelum alur berjenjang dipakai.
        'approvalLangkah.role:id,name',
        'approvalLangkah.pemutus:id,name',
        'approval.penyiap:id,name',
        'approval.penyetuju:id,name',
        'keberatan',
    ];

    protected array $filterable = [
        'status' => 'exact',
        'kategori_id' => 'exact',
        'ditangani_oleh' => 'exact',
        'format_informasi' => 'exact',
        'tampil_di_register_publik' => 'boolean',
    ];

    /**
     * Tidak ada endpoint tulis yang memakainya.
     *
     * `CrudController` menuntut metode ini ada; membiarkannya kosong lebih
     * jujur daripada menyimpan aturan validasi untuk formulir yang sudah tidak
     * punya route — aturan yang tidak pernah dijalankan cepat menyimpang dari
     * kenyataan skema.
     */
    protected function rules(string $mode, ?Model $record): array
    {
        return [];
    }

    /**
     * Pindahkan status permohonan mengikuti daftar transisi yang sah.
     */
    public function ubahStatus(Request $request, int $id): JsonResponse
    {
        /** @var PermohonanInformasi $permohonan */
        $permohonan = PermohonanInformasi::findOrFail($id);

        $data = $request->validate([
            'status_baru' => ['required', Rule::in(array_keys(PermohonanInformasi::TRANSISI))],
            'catatan' => ['nullable', 'string', 'max:2000'],
            'alasan_penolakan' => ['nullable', 'string'],
        ]);

        $statusLama = $permohonan->status;
        $statusBaru = $data['status_baru'];
        $diizinkan = PermohonanInformasi::TRANSISI[$statusLama] ?? [];

        if (!in_array($statusBaru, $diizinkan, true)) {
            throw ValidationException::withMessages([
                'status_baru' => "Status tidak bisa berpindah dari '{$statusLama}' ke '{$statusBaru}'.",
            ]);
        }

        if (in_array($statusBaru, ['ditolak', 'ditolak_sebagian'], true) && blank($data['alasan_penolakan'] ?? null)) {
            throw ValidationException::withMessages([
                'alasan_penolakan' => 'Alasan penolakan wajib diisi.',
            ]);
        }

        /*
         * Selama satu tahap persetujuan masih menunggu, perpindahan status
         * bukan lagi milik dropdown ini — milik putusan penyetujunya
         * (langkah 100). Sebelumnya yang dijaga hanya putusan akhir dari
         * "Menunggu Persetujuan", sehingga PPID Pelaksana yang sudah
         * meneruskan berkasnya masih bisa menariknya kembali, menolaknya
         * sendiri, atau menyatakannya kedaluwarsa — tiga hal yang seluruhnya
         * melangkahi jenjang di atasnya.
         *
         * Super admin dikecualikan dengan alasan yang sama seperti pada
         * {@see AlurPersetujuan::bolehMemutus()}: berkas yang macet karena
         * rolenya kosong atau pemegangnya nonaktif harus tetap bisa
         * dibebaskan tanpa menyunting basis data.
         */
        if (!$this->penggunaSuperAdmin()
            && AlurPersetujuan::berjalan(AlurPersetujuan::JENIS_PERMOHONAN, $id) !== null) {
            throw ValidationException::withMessages([
                'status_baru' => 'Permohonan ini sedang berada di jenjang persetujuan; '
                    .'perpindahannya ditentukan putusan pada panel Persetujuan Berjenjang.',
            ]);
        }

        DB::transaction(function () use ($permohonan, $data, $statusLama, $statusBaru) {
            $this->terapkanStatus(
                $permohonan,
                $statusLama,
                $statusBaru,
                $data['catatan'] ?? null,
                $data['alasan_penolakan'] ?? null
            );
        });

        AuditLogger::record(
            Auth::guard('api')->id(),
            'update_status',
            PermohonanInformasi::class,
            $permohonan->id,
            ['status' => $statusLama],
            ['status' => $statusBaru]
        );

        return response()->json([
            'data' => $permohonan->fresh($this->withDetail),
            'message' => "Status menjadi '{$statusBaru}'.",
        ]);
    }

    /**
     * Simpan status baru beserta seluruh jejak dan pemberitahuannya.
     *
     * Dipakai dua pemanggil: dialog status petugas dan hasil akhir alur
     * persetujuan. Keduanya harus meninggalkan jejak yang sama persis, jadi
     * jalurnya satu — bukan disalin di dua tempat yang lambat laun berbeda.
     *
     * Harus dijalankan di dalam transaksi.
     */
    private function terapkanStatus(
        PermohonanInformasi $permohonan,
        string $statusLama,
        string $statusBaru,
        ?string $catatan,
        ?string $alasanPenolakan
    ): void {
        $permohonan->status = $statusBaru;

        if ($alasanPenolakan !== null) {
            $permohonan->alasan_penolakan = $alasanPenolakan;
        }

        // Status yang menutup layanan sekaligus menandai waktu tanggapan.
        if (in_array($statusBaru, ['disetujui', 'ditolak', 'ditolak_sebagian', 'selesai'], true)) {
            $permohonan->tanggal_tanggapan = $permohonan->tanggal_tanggapan ?? now();
        }

        if ($permohonan->ditangani_oleh === null) {
            $permohonan->ditangani_oleh = Auth::guard('api')->id();
        }

        $permohonan->save();

        PermohonanLogStatus::create([
            'permohonan_id' => $permohonan->id,
            'status_sebelumnya' => $statusLama,
            'status_baru' => $statusBaru,
            'catatan' => $catatan,
            'changed_by' => Auth::guard('api')->id(),
        ]);

        // Berkasnya baru masuk antrean persetujuan: jenjangnya dibuat di sini
        // supaya penyetuju tahap pertama langsung mendapat pemberitahuan.
        if ($statusBaru === 'menunggu_approval') {
            AlurPersetujuan::mulai($permohonan);
        }

        // Email dan lonceng portal menunggu commit: transaksi yang batal tidak
        // boleh menyisakan pemberitahuan atas status yang tidak jadi.
        //
        // Yang ikut ke pemohon adalah `alasan_penolakan`, bukan `catatan`:
        // catatan itu keterangan internal petugas dan memang dilabeli begitu
        // di panel.
        DB::afterCommit(function () use ($permohonan, $statusLama, $statusBaru, $alasanPenolakan) {
            EmailPemohon::statusBerubah($permohonan, $statusLama, $statusBaru);
            NotifikasiPortal::statusPengajuan($permohonan, $statusLama, $statusBaru, $alasanPenolakan);

            /*
             * Berkas tanggapan baru diberitahukan di sini — saat permohonannya
             * benar-benar diserahkan (langkah 97).
             *
             * Berkasnya dilampirkan petugas jauh sebelum itu, sewaktu menyiapkan
             * jawaban dan sebelum PPID memutuskan. Memberi tahu pemohon pada
             * saat pelampiran berarti menjanjikan dokumen yang belum tentu jadi
             * diberikan, dan portal memang belum menampilkannya sebelum
             * permohonannya diserahkan.
             */
            if (self::statusTerbukaUntukPemohon($statusBaru) && !self::statusTerbukaUntukPemohon($statusLama)) {
                $jumlah = $permohonan->tanggapanFiles()->count();

                if ($jumlah > 0) {
                    NotifikasiPortal::berkasTanggapan($permohonan, $jumlah);
                }
            }
        });
    }

    // -----------------------------------------------------------------
    // Persetujuan berjenjang
    // -----------------------------------------------------------------

    protected function pengajuanPersetujuan(int $id): Model
    {
        return PermohonanInformasi::findOrFail($id);
    }

    /** Pengguna yang sedang masuk adalah super admin? */
    private function penggunaSuperAdmin(): bool
    {
        $user = Auth::guard('api')->user();
        $user?->loadMissing('role');

        return $user?->role?->slug === 'super-admin';
    }

    /**
     * Berkas naik ke jenjang berikutnya.
     *
     * Statusnya dipasang lewat {@see terapkanStatus()} yang sama dengan jalur
     * lain, bukan disimpan langsung: perpindahan yang tidak menulis
     * `permohonan_log_status` adalah perpindahan yang hilang dari riwayat, dan
     * riwayat itu yang dibaca petugas berikutnya untuk tahu berkas ini sudah
     * lewat tangan siapa.
     */
    protected function terapkanLanjutPersetujuan(Model $pengajuan): void
    {
        /** @var PermohonanInformasi $pengajuan */
        $statusLama = (string) $pengajuan->status;

        if ($statusLama === 'menunggu_approval') {
            return;
        }

        $this->terapkanStatus($pengajuan, $statusLama, 'menunggu_approval', null, null);

        AuditLogger::record(
            Auth::guard('api')->id(),
            'update_status',
            PermohonanInformasi::class,
            $pengajuan->id,
            ['status' => $statusLama],
            ['status' => 'menunggu_approval']
        );
    }

    /**
     * Status permohonan setelah alur persetujuan berakhir.
     *
     * `revisi` mengembalikan berkas ke `diproses`, bukan ke status `revisi`
     * milik skema: yang perlu diperbaiki adalah pekerjaan petugas, dan dari
     * `diproses` ia bisa mengajukan ulang ke persetujuan tanpa jalan memutar.
     */
    protected function terapkanHasilPersetujuan(Model $pengajuan, string $hasil, ?string $catatan): void
    {
        /** @var PermohonanInformasi $pengajuan */
        $statusLama = (string) $pengajuan->status;

        [$statusBaru, $alasan] = match ($hasil) {
            AlurPersetujuan::HASIL_DISETUJUI => ['disetujui', null],
            AlurPersetujuan::HASIL_DITOLAK => ['ditolak', $catatan],
            default => ['diproses', null],
        };

        $this->terapkanStatus($pengajuan, $statusLama, $statusBaru, $catatan, $alasan);

        /*
         * Dikembalikan untuk diperbaiki berarti berkasnya kembali ke tangan
         * jenjang pertama — bukan berhenti di "Diproses" menunggu seseorang
         * ingat untuk mengajukannya lagi (langkah 100). Putaran baru dibuat
         * saat itu juga supaya PPID Pelaksana langsung menerima lonceng
         * giliran, dan siklusnya bisa berulang sebanyak yang diperlukan.
         */
        if ($hasil === AlurPersetujuan::HASIL_REVISI) {
            AlurPersetujuan::mulai($pengajuan->refresh());
        }

        AuditLogger::record(
            Auth::guard('api')->id(),
            'update_status',
            PermohonanInformasi::class,
            $pengajuan->id,
            ['status' => $statusLama],
            ['status' => $statusBaru]
        );
    }

    /**
     * Lampirkan berkas tanggapan (dokumen yang diberikan ke pemohon).
     */
    public function tambahTanggapanFile(Request $request, int $id): JsonResponse
    {
        $permohonan = PermohonanInformasi::findOrFail($id);

        $data = $request->validate([
            'files' => ['required', 'array', 'max:20'],
            'files.*.nama_file' => ['nullable', 'string', 'max:255'],
            'files.*.path_file' => ['required', 'string', 'max:500'],
        ]);

        $dibuat = [];

        foreach ($data['files'] as $file) {
            $namaFile = $file['nama_file'] ?? basename($file['path_file']);

            $dibuat[] = PermohonanTanggapanFile::create([
                'permohonan_id' => $permohonan->id,
                'nama_file' => $namaFile,
                'path_file' => $file['path_file'],
                'uploaded_by' => Auth::guard('api')->id(),
            ]);

            /*
             * Apa pun yang dilampirkan ikut tercatat di Arsip Dokumen (langkah
             * 95), supaya permohonan berikutnya yang meminta dokumen sama bisa
             * memilihnya tanpa unggahan kedua. Berkas yang memang dipilih dari
             * arsip tidak menghasilkan baris kedua — pencatatannya dikunci pada
             * `path_file`.
             *
             * Gagal mencatat arsip tidak boleh membatalkan lampirannya: yang
             * pokok adalah berkasnya sampai ke pemohon.
             */
            try {
                ArsipDokumen::catatSekali((string) $file['path_file'], $namaFile);
            } catch (\Throwable $e) {
                Log::warning('[PPID] Gagal mencatat berkas ke arsip: '.$e->getMessage());
            }
        }

        /*
         * Pemohon hanya diberi tahu bila permohonannya memang sudah diserahkan
         * (langkah 97).
         *
         * Selama masih disiapkan — belum diputus PPID — berkasnya tidak tampil
         * di portal, jadi pemberitahuan pada tahap itu menunjuk sesuatu yang
         * tidak bisa dibuka pemohon dan menjanjikan jawaban yang belum tentu
         * jadi diberikan. Untuk berkas yang dilampirkan lebih awal,
         * pemberitahuannya menyusul saat statusnya berpindah ke Disetujui atau
         * Selesai (lihat `terapkanStatus()`).
         */
        if (self::statusTerbukaUntukPemohon((string) $permohonan->status)) {
            NotifikasiPortal::berkasTanggapan($permohonan, count($dibuat));
        }

        return response()->json(['data' => $dibuat], 201);
    }

    /**
     * Status yang berarti tanggapan sudah diserahkan kepada pemohon.
     *
     * Dipakai dua tempat: penentu kapan berkas tanggapan diberitahukan, dan —
     * di sisi portal — penentu kapan berkasnya boleh diunduh. Keduanya harus
     * memakai daftar yang sama; berkas yang terlihat tanpa pemberitahuan sama
     * membingungkannya dengan pemberitahuan atas berkas yang tak bisa dibuka.
     *
     * Penolakan tidak termasuk: yang disampaikan pada penolakan adalah
     * alasannya, dan itu sudah dibawa notifikasi status.
     */
    private static function statusTerbukaUntukPemohon(?string $status): bool
    {
        return in_array((string) $status, ['disetujui', 'selesai'], true);
    }

    /**
     * Perpanjang tenggat tanggapan sekali, paling lama 7 hari kerja.
     *
     * UU KIP memberi satu kali perpanjangan dan menuntut alasannya diberitahukan
     * kepada pemohon, jadi keduanya dijaga di sini: alasan wajib diisi, dan
     * perpanjangan kedua ditolak. Tenggat awalnya disimpan terpisah supaya
     * penilaian ketepatan waktu tetap punya pembanding aslinya — tanpa itu,
     * setiap keterlambatan bisa dihapus dengan cara menggeser tenggatnya.
     */
    public function perpanjangTenggat(Request $request, int $id): JsonResponse
    {
        /** @var PermohonanInformasi $permohonan */
        $permohonan = PermohonanInformasi::findOrFail($id);

        $data = $request->validate([
            'alasan' => ['required', 'string', 'max:2000'],
        ]);

        if (!SlaLayanan::bolehDiperpanjang($permohonan)) {
            throw ValidationException::withMessages([
                'alasan' => filled($permohonan->diperpanjang_pada)
                    ? 'Permohonan ini sudah pernah diperpanjang. Undang-undang hanya memberi satu kali perpanjangan.'
                    : 'Permohonan yang sudah ditanggapi atau ditutup tidak dapat diperpanjang.',
            ]);
        }

        if (blank($permohonan->batas_waktu_tanggapan)) {
            throw ValidationException::withMessages([
                'alasan' => 'Permohonan ini belum punya batas waktu tanggapan, jadi tidak ada yang bisa diperpanjang.',
            ]);
        }

        $batasLama = $permohonan->batas_waktu_tanggapan;
        $batasBaru = SlaLayanan::perpanjang($batasLama);

        DB::transaction(function () use ($permohonan, $batasLama, $batasBaru, $data) {
            $permohonan->fill([
                // Diisi hanya kalau masih kosong: perpanjangan boleh sekali,
                // tetapi baris lama yang tenggat awalnya belum tercatat pun
                // tidak boleh kehilangan tenggat aslinya.
                'batas_waktu_awal' => $permohonan->batas_waktu_awal ?? $batasLama,
                'batas_waktu_tanggapan' => $batasBaru,
                'diperpanjang_pada' => now(),
                'alasan_perpanjangan' => $data['alasan'],
            ])->save();

            PermohonanLogStatus::create([
                'permohonan_id' => $permohonan->id,
                'status_lama' => $permohonan->status,
                'status_baru' => $permohonan->status,
                'catatan' => 'Tenggat diperpanjang '.SlaLayanan::PERPANJANGAN_HARI_KERJA.' hari kerja. Alasan: '.$data['alasan'],
                'diubah_oleh' => Auth::guard('api')->id(),
            ]);
        });

        EmailPemohon::tenggatDiperpanjang($permohonan, $data['alasan']);
        NotifikasiPortal::tenggatDiperpanjang($permohonan);

        AuditLogger::record(
            Auth::guard('api')->id(),
            'perpanjang_tenggat',
            PermohonanInformasi::class,
            $permohonan->id,
            ['batas_waktu_tanggapan' => $batasLama],
            ['batas_waktu_tanggapan' => $batasBaru, 'alasan' => $data['alasan']]
        );

        return response()->json([
            'data' => $permohonan->fresh(),
            'message' => 'Tenggat diperpanjang sampai '.$batasBaru->translatedFormat('d F Y').'.',
        ]);
    }

    public function hapusTanggapanFile(int $id, int $fileId): JsonResponse
    {
        $file = PermohonanTanggapanFile::where('permohonan_id', $id)->findOrFail($fileId);
        $file->delete();

        return response()->json(['message' => 'Berkas tanggapan dihapus']);
    }
}
