<?php

namespace App\Http\Controllers\Api\Cms;

use App\Http\Controllers\Api\CrudController;
use App\Models\ModulSistem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

/**
 * Daftar modul sistem — dasar pemeriksaan hak akses tiap role.
 *
 * Slug modul dipakai middleware `akses:{slug},{aksi}` dan oleh registry modul
 * di panel, jadi mengubahnya memutus hak akses yang sudah tersimpan. Karena itu
 * slug divalidasi unik dan modul ini berada di bawah hak akses `pengguna`,
 * sama seperti Role.
 */
class ModulSistemController extends CrudController
{
    protected string $model = ModulSistem::class;

    protected string $modulSlug = 'pengguna';

    protected array $searchable = ['nama', 'slug'];

    protected array $sortable = ['id', 'nama', 'slug', 'urutan'];

    protected string $defaultSort = 'urutan';

    protected array $withList = ['parent:id,nama'];

    protected array $filterable = [
        'is_active' => 'boolean',
        'parent_id' => 'exact',
    ];

    protected function rules(string $mode, ?Model $record): array
    {
        $wajib = $mode === 'create' ? 'required' : 'sometimes';

        return [
            'nama' => [$wajib, 'string', 'max:100'],
            'slug' => [
                $wajib,
                'string',
                'max:100',
                'regex:/^[a-z0-9-]+$/',
                Rule::unique('modul_sistem', 'slug')->ignore($record?->id),
            ],
            'parent_id' => ['nullable', 'integer', Rule::exists('modul_sistem', 'id')],
            'icon' => ['nullable', 'string', 'max:100'],
            'route' => ['nullable', 'string', 'max:150'],
            'urutan' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ];
    }
}
