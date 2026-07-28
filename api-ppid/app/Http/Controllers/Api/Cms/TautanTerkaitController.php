<?php

namespace App\Http\Controllers\Api\Cms;

use App\Http\Controllers\Api\CrudController;
use App\Models\TautanTerkait;
use Illuminate\Database\Eloquent\Model;

class TautanTerkaitController extends CrudController
{
    protected string $model = TautanTerkait::class;

    protected string $modulSlug = 'tautan-terkait';

    protected array $searchable = ['nama', 'url'];

    protected array $sortable = ['id', 'nama', 'urutan'];

    protected string $defaultSort = 'urutan';

    protected array $filterable = [
        'is_active' => 'boolean',
    ];

    protected function rules(string $mode, ?Model $record): array
    {
        $wajib = $mode === 'create' ? 'required' : 'sometimes';

        return [
            'nama' => [$wajib, 'string', 'max:150'],
            // Skema hanya izinkan tautan http/https supaya tidak ada javascript: di menu publik.
            'url' => [$wajib, 'url', 'starts_with:http://,https://', 'max:500'],
            'logo' => ['nullable', 'string', 'max:500'],
            'urutan' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ];
    }
}
