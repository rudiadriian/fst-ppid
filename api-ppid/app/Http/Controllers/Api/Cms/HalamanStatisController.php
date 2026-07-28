<?php

namespace App\Http\Controllers\Api\Cms;

use App\Http\Controllers\Api\CrudController;
use App\Models\HalamanStatis;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HalamanStatisController extends CrudController
{
    protected string $model = HalamanStatis::class;

    protected string $modulSlug = 'halaman-statis';

    protected array $searchable = ['judul', 'slug'];

    protected array $sortable = ['id', 'judul', 'slug', 'updated_at'];

    protected string $defaultSort = 'judul';

    protected array $withList = ['editor:id,name'];

    protected array $filterable = [
        'is_active' => 'boolean',
    ];

    protected ?string $slugFrom = 'judul';

    protected function rules(string $mode, ?Model $record): array
    {
        $wajib = $mode === 'create' ? 'required' : 'sometimes';

        return [
            'judul' => [$wajib, 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'konten' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }

    protected function beforeSave(array $data, Request $request, ?Model $record): array
    {
        $data['updated_by'] = Auth::guard('api')->id();

        return $data;
    }
}
