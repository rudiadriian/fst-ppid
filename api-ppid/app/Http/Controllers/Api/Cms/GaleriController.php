<?php

namespace App\Http\Controllers\Api\Cms;

use App\Http\Controllers\Api\CrudController;
use App\Models\Galeri;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class GaleriController extends CrudController
{
    protected string $model = Galeri::class;

    protected string $modulSlug = 'galeri';

    protected array $searchable = ['judul', 'deskripsi'];

    protected array $sortable = ['id', 'judul', 'tanggal', 'created_at'];

    protected array $filterable = [
        'tipe' => 'exact',
    ];

    protected function rules(string $mode, ?Model $record): array
    {
        $wajib = $mode === 'create' ? 'required' : 'sometimes';

        return [
            'judul' => ['nullable', 'string', 'max:255'],
            'tipe' => [$wajib, Rule::in(['foto', 'video'])],
            'path_file' => [$wajib, 'string', 'max:500'],
            'deskripsi' => ['nullable', 'string'],
            'tanggal' => ['nullable', 'date'],
        ];
    }
}
