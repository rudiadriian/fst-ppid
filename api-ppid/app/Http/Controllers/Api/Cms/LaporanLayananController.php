<?php

namespace App\Http\Controllers\Api\Cms;

use App\Http\Controllers\Api\CrudController;
use App\Models\LaporanLayanan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Modul Laporan Pelayanan Informasi.
 *
 * Tabel `laporan_layanan` dulu dipakai dua modul sekaligus, dibedakan lewat
 * `tipe_laporan`. Sejak Laporan Statistik Informasi Publik dihapus pada
 * langkah 68, tinggal `pelayanan_informasi` — berkas laporan per tahun.
 * Angka rekap tahunan beserta endpoint hitung otomatisnya ikut dilepas.
 */
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
            'tipe_laporan' => [$wajib, Rule::in(['pelayanan_informasi'])],
            'judul' => [$wajib, 'string', 'max:255'],
            'tahun' => [$wajib, 'integer', 'min:2000', 'max:2100'],
            'periode' => ['nullable', 'string', 'max:30'],
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

}
