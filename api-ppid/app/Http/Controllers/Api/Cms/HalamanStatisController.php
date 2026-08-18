<?php

namespace App\Http\Controllers\Api\Cms;

use App\Http\Controllers\Api\CrudController;
use App\Models\HalamanStatis;
use Illuminate\Database\Eloquent\Model;

class HalamanStatisController extends CrudController
{
    protected string $model = HalamanStatis::class;

    protected string $modulSlug = 'halaman-statis';

    protected array $searchable = ['judul', 'slug'];

    protected array $sortable = ['id', 'judul', 'slug', 'updated_at'];

    protected string $defaultSort = 'judul';

    // Pemuatan relasi pelaku (`pembuat`/`pengubah`/`penghapus`) ditangani
    // CrudController untuk semua modul, jadi tidak perlu ditulis di sini.

    protected array $filterable = [
        'is_active' => 'boolean',
    ];

    protected ?string $slugFrom = 'judul';

    protected function rules(string $mode, ?Model $record): array
    {
        $wajib = $mode === 'create' ? 'required' : 'sometimes';

        return [
            'judul' => [$wajib, 'string', 'max:255'],
            'judul_en' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'konten' => ['nullable', 'string'],
            'konten_en' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }
}
