<?php

namespace App\Http\Controllers\Api\Cms;

use App\Http\Controllers\Api\CrudController;
use App\Models\ApprovalPermohonan;
use App\Models\PermohonanInformasi;
use App\Models\PermohonanLogStatus;
use App\Models\PermohonanTanggapanFile;
use App\Support\AuditLogger;
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
 * Kolom `status` sengaja tidak ikut jalur update biasa: perpindahan status
 * hanya lewat endpoint `ubahStatus()` supaya setiap perubahan pasti tercatat
 * di `permohonan_log_status` dan mengikuti alur yang diizinkan.
 */
class PermohonanController extends CrudController
{
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

    /** Batas waktu tanggapan bawaan (hari kalender) sesuai SOP layanan. */
    private const SLA_HARI = 10;

    protected function rules(string $mode, ?Model $record): array
    {
        $wajib = $mode === 'create' ? 'required' : 'sometimes';

        return [
            'pemohon_id' => [$wajib, Rule::exists('pemohon', 'id')],
            'kategori_id' => ['nullable', Rule::exists('kategori_informasi', 'id')],
            'rincian_informasi' => [$wajib, 'string'],
            'tujuan_penggunaan' => ['nullable', 'string'],
            'format_informasi' => ['nullable', Rule::in(['softcopy', 'hardcopy'])],
            'cara_pengiriman' => ['nullable', Rule::in(['email', 'ambil_langsung', 'pos'])],
            'batas_waktu_tanggapan' => ['nullable', 'date'],
            'ditangani_oleh' => ['nullable', Rule::exists('users', 'id')],
            'tampil_di_register_publik' => ['boolean'],
        ];
    }

    protected function beforeSave(array $data, Request $request, ?Model $record): array
    {
        if ($record === null) {
            $data['batas_waktu_tanggapan'] = $data['batas_waktu_tanggapan'] ?? now()->addDays(self::SLA_HARI);
        }

        return $data;
    }

    protected function afterSave(Model $record, Request $request, string $mode): void
    {
        if ($mode === 'create') {
            PermohonanLogStatus::create([
                'permohonan_id' => $record->getKey(),
                'status_sebelumnya' => null,
                'status_baru' => $record->status,
                'catatan' => 'Permohonan dicatat lewat panel admin.',
                'changed_by' => Auth::guard('api')->id(),
            ]);
        }
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

        DB::transaction(function () use ($permohonan, $data, $statusLama, $statusBaru) {
            $permohonan->status = $statusBaru;

            if (array_key_exists('alasan_penolakan', $data) && $data['alasan_penolakan'] !== null) {
                $permohonan->alasan_penolakan = $data['alasan_penolakan'];
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
                'catatan' => $data['catatan'] ?? null,
                'changed_by' => Auth::guard('api')->id(),
            ]);
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
     * Ajukan / putuskan persetujuan berjenjang.
     */
    public function putusanApproval(Request $request, int $id): JsonResponse
    {
        $permohonan = PermohonanInformasi::findOrFail($id);

        $data = $request->validate([
            'status_approval' => ['required', Rule::in(['pending', 'disetujui', 'ditolak', 'revisi'])],
            'catatan_approval' => ['nullable', 'string', 'max:2000'],
        ]);

        $userId = Auth::guard('api')->id();

        $approval = ApprovalPermohonan::create([
            'permohonan_id' => $permohonan->id,
            'disiapkan_oleh' => $permohonan->ditangani_oleh ?? $userId,
            'tanggal_diajukan' => now(),
            'disetujui_oleh' => $data['status_approval'] === 'pending' ? null : $userId,
            'status_approval' => $data['status_approval'],
            'catatan_approval' => $data['catatan_approval'] ?? null,
            'tanggal_approval' => $data['status_approval'] === 'pending' ? null : now(),
        ]);

        AuditLogger::record(
            $userId,
            'approval',
            ApprovalPermohonan::class,
            $approval->id,
            null,
            ['permohonan_id' => $permohonan->id, 'status_approval' => $data['status_approval']]
        );

        return response()->json(['data' => $approval], 201);
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

        return response()->json(['data' => $dibuat], 201);
    }

    public function hapusTanggapanFile(int $id, int $fileId): JsonResponse
    {
        $file = PermohonanTanggapanFile::where('permohonan_id', $id)->findOrFail($fileId);
        $file->delete();

        return response()->json(['message' => 'Berkas tanggapan dihapus']);
    }
}
