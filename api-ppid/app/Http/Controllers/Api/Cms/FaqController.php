<?php

namespace App\Http\Controllers\Api\Cms;

use App\Http\Controllers\Api\CrudController;
use App\Models\Faq;
use Illuminate\Database\Eloquent\Model;

class FaqController extends CrudController
{
    protected string $model = Faq::class;

    protected string $modulSlug = 'faq';

    protected array $searchable = ['pertanyaan', 'jawaban', 'kategori'];

    protected array $sortable = ['id', 'urutan', 'kategori'];

    protected string $defaultSort = 'urutan';

    protected array $filterable = [
        'kategori' => 'exact',
        'is_active' => 'boolean',
    ];

    protected function rules(string $mode, ?Model $record): array
    {
        $wajib = $mode === 'create' ? 'required' : 'sometimes';

        return [
            'pertanyaan' => [$wajib, 'string'],
            'pertanyaan_en' => ['nullable', 'string'],
            'jawaban' => [$wajib, 'string'],
            'jawaban_en' => ['nullable', 'string'],
            'kategori' => ['nullable', 'string', 'max:100'],
            'kategori_en' => ['nullable', 'string', 'max:100'],
            'urutan' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ];
    }
}
