<?php

namespace App\Http\Controllers\Api\Cms;

use App\Http\Controllers\Api\CrudController;
use App\Models\StrukturOrganisasi;
use Illuminate\Database\Eloquent\Model;

class StrukturOrganisasiController extends CrudController
{
    protected string $model = StrukturOrganisasi::class;

    protected string $modulSlug = 'struktur-organisasi';

    protected array $searchable = ['nama', 'jabatan', 'deskripsi'];

    protected array $sortable = ['id', 'nama', 'jabatan', 'urutan'];

    protected string $defaultSort = 'urutan';

    protected array $filterable = [
        'is_active' => 'boolean',
    ];

    protected function rules(string $mode, ?Model $record): array
    {
        $wajib = $mode === 'create' ? 'required' : 'sometimes';

        return [
            'nama' => [$wajib, 'string', 'max:150'],
            'jabatan' => [$wajib, 'string', 'max:150'],
            'foto' => ['nullable', 'string', 'max:500'],
            'urutan' => ['nullable', 'integer', 'min:0'],
            'deskripsi' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }
}
