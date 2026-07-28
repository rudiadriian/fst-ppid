<?php

namespace App\Http\Controllers\Api\Cms;

use App\Http\Controllers\Api\CrudController;
use App\Models\MenuNavigasi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class MenuNavigasiController extends CrudController
{
    protected string $model = MenuNavigasi::class;

    protected string $modulSlug = 'menu-navigasi';

    protected array $searchable = ['label', 'url'];

    protected array $sortable = ['id', 'label', 'urutan'];

    protected string $defaultSort = 'urutan';

    protected array $withList = ['parent:id,label'];

    protected array $filterable = [
        'parent_id' => 'exact',
        'is_active' => 'boolean',
    ];

    protected function rules(string $mode, ?Model $record): array
    {
        $wajib = $mode === 'create' ? 'required' : 'sometimes';

        return [
            'parent_id' => ['nullable', Rule::exists('menu_navigasi', 'id')],
            'label' => [$wajib, 'string', 'max:100'],
            // Menu boleh menunjuk path internal ("/informasi/xxx") atau URL penuh,
            // tapi bukan skema lain seperti javascript: atau data:.
            'url' => ['nullable', 'string', 'max:255', 'regex:/^(\/|https?:\/\/)/'],
            'urutan' => ['nullable', 'integer', 'min:0'],
            'target' => ['sometimes', Rule::in(['_self', '_blank'])],
            'is_active' => ['boolean'],
        ];
    }

    protected function beforeSave(array $data, Request $request, ?Model $record): array
    {
        if ($record !== null && ($data['parent_id'] ?? null) == $record->getKey()) {
            throw ValidationException::withMessages([
                'parent_id' => 'Menu tidak boleh menjadi induk dirinya sendiri.',
            ]);
        }

        return $data;
    }

    protected function beforeDelete(Model $record): void
    {
        if ($record->children()->exists()) {
            throw ValidationException::withMessages([
                'id' => 'Hapus sub-menu lebih dulu.',
            ]);
        }
    }
}
