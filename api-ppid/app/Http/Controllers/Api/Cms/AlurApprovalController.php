<?php

namespace App\Http\Controllers\Api\Cms;

use App\Http\Controllers\Api\CrudController;
use App\Models\AlurApproval;
use App\Models\AlurApprovalTahap;
use App\Models\ApprovalPengajuan;
use App\Support\AuditLogger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Alur persetujuan berjenjang — modul super admin.
 *
 * Alurnya data, bukan kode: susunan jenjang, role yang memutuskan, dan kotak
 * struktur organisasi yang diwakilinya semuanya disimpan di basis data dan
 * bisa disusun ulang dari panel. Perubahan struktur organisasi karena itu
 * tidak lagi menuntut penulisan ulang aplikasi.
 *
 * Jenjangnya sendiri tidak dikelola sebagai modul CRUD terpisah: satu alur
 * hanya berarti utuh bersama urutan tahapnya, jadi keduanya disimpan sekaligus
 * lewat `simpanTahap()` — persis pola matrix hak akses role.
 */
class AlurApprovalController extends CrudController
{
    protected string $model = AlurApproval::class;

    protected string $modulSlug = 'alur-approval';

    protected array $searchable = ['nama', 'keterangan'];

    protected array $sortable = ['id', 'nama', 'jenis', 'is_active'];

    protected string $defaultSort = 'jenis';

    protected array $withList = ['tahap'];

    protected array $withDetail = ['tahap.role:id,name', 'tahap.struktur:id,jabatan,nama'];

    protected array $filterable = [
        'jenis' => 'exact',
        'is_active' => 'boolean',
    ];

    protected function rules(string $mode, ?Model $record): array
    {
        $wajib = $mode === 'create' ? 'required' : 'sometimes';

        return [
            'jenis' => [$wajib, Rule::in(AlurApproval::JENIS)],
            'nama' => [$wajib, 'string', 'max:150'],
            'keterangan' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * Jaga satu alur aktif per jenis.
     *
     * Kalau dua alur aktif untuk jenis yang sama, pengajuan berikutnya akan
     * memakai salah satunya tanpa aturan yang bisa dijelaskan ke siapa pun.
     * Yang baru diaktifkan menang; yang lama dinonaktifkan, bukan dihapus —
     * langkah yang sudah dibuat darinya masih menunjuk ke sana.
     */
    protected function afterSave(Model $record, Request $request, string $mode): void
    {
        if (!$record->is_active) {
            return;
        }

        AlurApproval::where('jenis', $record->jenis)
            ->whereKeyNot($record->getKey())
            ->where('is_active', true)
            ->update(['is_active' => false]);
    }

    /**
     * Alur yang tahapnya pernah dipakai tidak boleh dihapus.
     *
     * Menghapusnya akan menyeret `alur_approval_tahap` lewat cascade, dan
     * langkah persetujuan yang sudah terjadi kehilangan tahap acuannya. Yang
     * benar adalah menonaktifkannya.
     */
    protected function beforeDelete(Model $record): void
    {
        if (ApprovalPengajuan::where('alur_id', $record->getKey())->exists()) {
            throw ValidationException::withMessages([
                'id' => 'Alur ini sudah pernah dipakai pada pengajuan. Nonaktifkan saja agar riwayat persetujuannya tetap utuh.',
            ]);
        }
    }

    /**
     * Jenjang satu alur, terurut, beserta jabatan yang diwakilinya.
     */
    public function tahap(int $id): JsonResponse
    {
        $alur = AlurApproval::with(['tahap.role:id,name,slug', 'tahap.struktur:id,jabatan,nama'])->findOrFail($id);

        return response()->json([
            'data' => [
                'alur' => $alur->only(['id', 'jenis', 'nama', 'keterangan', 'is_active']),
                'tahap' => $alur->tahap->map(fn (AlurApprovalTahap $t) => [
                    'id' => $t->id,
                    'urutan' => $t->urutan,
                    'nama' => $t->nama,
                    'role_id' => $t->role_id,
                    'role' => $t->role?->name,
                    'struktur_id' => $t->struktur_id,
                    'jabatan' => $t->struktur?->jabatan,
                    'sla_hari' => $t->sla_hari,
                    'boleh_tolak' => (bool) $t->boleh_tolak,
                    'keterangan' => $t->keterangan,
                    'is_active' => (bool) $t->is_active,
                ])->values(),
            ],
        ]);
    }

    /**
     * Simpan seluruh jenjang sekaligus.
     *
     * Urutannya diambil dari posisi pada array, bukan dari angka yang dikirim
     * klien: susunan yang terlihat di panel dan urutan yang dijalankan server
     * tidak boleh bisa berbeda.
     *
     * Tahap yang hilang dari kiriman dihapus (soft delete). Yang sudah pernah
     * dipakai pengajuan tidak ikut hilang — barisnya di `approval_pengajuan`
     * menyimpan salinan namanya sendiri, jadi riwayatnya tetap terbaca.
     */
    public function simpanTahap(Request $request, int $id): JsonResponse
    {
        $alur = AlurApproval::findOrFail($id);

        $data = $request->validate([
            'tahap' => ['present', 'array', 'max:20'],
            'tahap.*.id' => ['nullable', 'integer'],
            'tahap.*.nama' => ['required', 'string', 'max:150'],
            'tahap.*.role_id' => ['nullable', 'integer', Rule::exists('roles', 'id')],
            'tahap.*.struktur_id' => ['nullable', 'integer', Rule::exists('struktur_organisasi', 'id')],
            'tahap.*.sla_hari' => ['nullable', 'integer', 'min:1', 'max:365'],
            'tahap.*.boleh_tolak' => ['boolean'],
            'tahap.*.keterangan' => ['nullable', 'string'],
            'tahap.*.is_active' => ['boolean'],
        ]);

        // Tahap tanpa role tidak bisa diputus siapa pun kecuali super admin;
        // alur seperti itu macet begitu berkas sampai di sana.
        foreach ($data['tahap'] as $index => $tahap) {
            if (($tahap['is_active'] ?? true) && blank($tahap['role_id'] ?? null)) {
                throw ValidationException::withMessages([
                    "tahap.{$index}.role_id" => 'Tahap yang aktif harus punya role penyetuju.',
                ]);
            }
        }

        DB::transaction(function () use ($alur, $data) {
            $dipertahankan = [];

            foreach (array_values($data['tahap']) as $index => $tahap) {
                $atribut = [
                    'alur_id' => $alur->id,
                    'urutan' => $index + 1,
                    'nama' => $tahap['nama'],
                    'role_id' => $tahap['role_id'] ?? null,
                    'struktur_id' => $tahap['struktur_id'] ?? null,
                    'sla_hari' => $tahap['sla_hari'] ?? null,
                    'boleh_tolak' => (bool) ($tahap['boleh_tolak'] ?? true),
                    'keterangan' => $tahap['keterangan'] ?? null,
                    'is_active' => (bool) ($tahap['is_active'] ?? true),
                ];

                $baris = AlurApprovalTahap::where('alur_id', $alur->id)
                    ->whereKey($tahap['id'] ?? 0)
                    ->first();

                if ($baris === null) {
                    $baris = AlurApprovalTahap::create($atribut);
                } else {
                    $baris->fill($atribut)->save();
                }

                $dipertahankan[] = $baris->getKey();
            }

            AlurApprovalTahap::where('alur_id', $alur->id)
                ->whereNotIn('id', $dipertahankan ?: [0])
                ->get()
                ->each
                ->delete();
        });

        AuditLogger::record(
            Auth::guard('api')->id(),
            'update',
            AlurApprovalTahap::class,
            $alur->id,
            null,
            ['alur' => $alur->nama, 'jumlah_tahap' => count($data['tahap'])]
        );

        return $this->tahap($alur->id);
    }
}
