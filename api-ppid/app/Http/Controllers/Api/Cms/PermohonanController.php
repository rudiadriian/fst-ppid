<?php

namespace App\Http\Controllers\Api\Cms;

use App\Http\Controllers\Api\Concerns\MenanganiPersetujuan;
use App\Http\Controllers\Api\CrudController;
use App\Models\PermohonanInformasi;
use App\Models\PermohonanLogStatus;
use App\Models\PermohonanTanggapanFile;
use App\Support\AlurPersetujuan;
use App\Support\AuditLogger;
use App\Support\EmailPemohon;
use App\Support\NotifikasiPortal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
         * Persetujuan berjenjang hanya boleh dilewati kalau alurnya memang
         * belum disusun. Selama masih ada tahap yang menunggu, putusan
         * akhirnya milik penyetuju — bukan petugas yang mengubah status
         * sendiri lewat dialog status.
         */
        if ($statusLama === 'menunggu_approval'
            && in_array($statusBaru, ['disetujui', 'ditolak', 'ditolak_sebagian'], true)
            && AlurPersetujuan::berjalan(AlurPersetujuan::JENIS_PERMOHONAN, $id) !== null) {
            throw ValidationException::withMessages([
                'status_baru' => 'Permohonan ini sedang menunggu putusan tahap persetujuan; '
                    .'selesaikan tahapnya lewat menu Persetujuan Berjenjang.',
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
        });
    }

    // -----------------------------------------------------------------
    // Persetujuan berjenjang
    // -----------------------------------------------------------------

    protected function pengajuanPersetujuan(int $id): Model
    {
        return PermohonanInformasi::findOrFail($id);
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
            $dibuat[] = PermohonanTanggapanFile::create([
                'permohonan_id' => $permohonan->id,
                'nama_file' => $file['nama_file'] ?? basename($file['path_file']),
                'path_file' => $file['path_file'],
                'uploaded_by' => Auth::guard('api')->id(),
            ]);
        }

        // Berkasnya sudah bisa diunduh pemohon begitu barisnya tersimpan, jadi
        // loncengnya diberi tahu di sini — bukan menunggu status berpindah.
        NotifikasiPortal::berkasTanggapan($permohonan, count($dibuat));

        return response()->json(['data' => $dibuat], 201);
    }

    public function hapusTanggapanFile(int $id, int $fileId): JsonResponse
    {
        $file = PermohonanTanggapanFile::where('permohonan_id', $id)->findOrFail($fileId);
        $file->delete();

        return response()->json(['message' => 'Berkas tanggapan dihapus']);
    }
}
