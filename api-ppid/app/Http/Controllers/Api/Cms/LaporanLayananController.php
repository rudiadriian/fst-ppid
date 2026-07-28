<?php

namespace App\Http\Controllers\Api\Cms;

use App\Http\Controllers\Api\CrudController;
use App\Models\KeberatanInformasi;
use App\Models\LaporanLayanan;
use App\Models\PermohonanInformasi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class LaporanLayananController extends CrudController
{
    protected string $model = LaporanLayanan::class;

    protected string $modulSlug = 'laporan-layanan';

    protected array $searchable = ['judul', 'periode', 'ringkasan'];

    protected array $sortable = ['id', 'judul', 'tahun', 'tipe_laporan', 'status', 'created_at'];

    protected string $defaultSort = '-tahun';

    protected array $withList = ['publisher:id,name'];

    protected array $filterable = [
        'tipe_laporan' => 'exact',
        'tahun' => 'exact',
        'status' => 'exact',
    ];

    protected function rules(string $mode, ?Model $record): array
    {
        $wajib = $mode === 'create' ? 'required' : 'sometimes';

        return [
            'tipe_laporan' => [$wajib, Rule::in(['statistik_informasi', 'pelayanan_informasi'])],
            'judul' => [$wajib, 'string', 'max:255'],
            'tahun' => [$wajib, 'integer', 'min:2000', 'max:2100'],
            'periode' => ['nullable', 'string', 'max:30'],
            'jumlah_permohonan_masuk' => ['nullable', 'integer', 'min:0'],
            'jumlah_dikabulkan' => ['nullable', 'integer', 'min:0'],
            'jumlah_ditolak' => ['nullable', 'integer', 'min:0'],
            'jumlah_ditolak_sebagian' => ['nullable', 'integer', 'min:0'],
            'jumlah_keberatan' => ['nullable', 'integer', 'min:0'],
            'rata_rata_hari_respon' => ['nullable', 'numeric', 'min:0'],
            'ringkasan' => ['nullable', 'string'],
            'file_laporan' => ['nullable', 'string', 'max:500'],
            'status' => ['sometimes', Rule::in(['draft', 'published', 'archived'])],
        ];
    }

    protected function beforeSave(array $data, Request $request, ?Model $record): array
    {
        if (($data['status'] ?? null) === 'published') {
            $data['published_by'] = Auth::guard('api')->id();
        }

        return $data;
    }

    /**
     * Hitung angka rekap satu tahun langsung dari data permohonan.
     * Dipakai tombol "Hitung otomatis" di form laporan agar angka laporan
     * tidak diketik manual.
     */
    public function rekap(Request $request): JsonResponse
    {
        $data = $request->validate([
            'tahun' => ['required', 'integer', 'min:2000', 'max:2100'],
        ]);

        $tahun = $data['tahun'];

        $permohonan = PermohonanInformasi::query()
            ->whereYear('tanggal_permohonan', $tahun);

        $perStatus = (clone $permohonan)
            ->select('status', DB::raw('count(*) as jumlah'))
            ->groupBy('status')
            ->pluck('jumlah', 'status');

        $rataHari = (clone $permohonan)
            ->whereNotNull('tanggal_tanggapan')
            ->select(DB::raw('avg(extract(epoch from (tanggal_tanggapan - tanggal_permohonan)) / 86400) as rata'))
            ->value('rata');

        return response()->json([
            'data' => [
                'tahun' => $tahun,
                'jumlah_permohonan_masuk' => (int) $perStatus->sum(),
                'jumlah_dikabulkan' => (int) ($perStatus['disetujui'] ?? 0) + (int) ($perStatus['selesai'] ?? 0),
                'jumlah_ditolak' => (int) ($perStatus['ditolak'] ?? 0),
                'jumlah_ditolak_sebagian' => (int) ($perStatus['ditolak_sebagian'] ?? 0),
                'jumlah_keberatan' => KeberatanInformasi::whereYear('tanggal_keberatan', $tahun)->count(),
                'rata_rata_hari_respon' => $rataHari !== null ? round((float) $rataHari, 2) : null,
            ],
        ]);
    }
}
