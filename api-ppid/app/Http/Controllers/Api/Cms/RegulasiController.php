<?php

namespace App\Http\Controllers\Api\Cms;

use App\Http\Controllers\Api\CrudController;
use App\Models\Regulasi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class RegulasiController extends CrudController
{
    protected string $model = Regulasi::class;

    protected string $modulSlug = 'regulasi';

    protected array $searchable = ['judul', 'nomor_peraturan', 'jenis_peraturan'];

    protected array $sortable = ['id', 'judul', 'tahun', 'tanggal_berlaku', 'created_at'];

    protected string $defaultSort = '-tahun';

    protected array $filterable = [
        'kategori' => 'exact',
        'tahun' => 'exact',
    ];

    protected function rules(string $mode, ?Model $record): array
    {
        $wajib = $mode === 'create' ? 'required' : 'sometimes';

        return [
            'kategori' => ['sometimes', Rule::in(['dasar_hukum_ppid', 'regulasi', 'pedoman'])],
            'judul' => [$wajib, 'string', 'max:255'],
            'nomor_peraturan' => ['nullable', 'string', 'max:100'],
            'jenis_peraturan' => ['nullable', 'string', 'max:100'],
            'tahun' => ['nullable', 'integer', 'min:1945', 'max:2100'],
            'file_path' => ['nullable', 'string', 'max:500'],
            'tanggal_berlaku' => ['nullable', 'date'],
        ];
    }
}
