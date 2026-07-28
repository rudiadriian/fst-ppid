<?php

namespace App\Http\Controllers\Api\Cms;

use App\Http\Controllers\Api\CrudController;
use App\Models\BannerSlider;
use Illuminate\Database\Eloquent\Model;

class BannerSliderController extends CrudController
{
    protected string $model = BannerSlider::class;

    protected string $modulSlug = 'banner-slider';

    protected array $searchable = ['judul'];

    protected array $sortable = ['id', 'judul', 'urutan', 'tanggal_mulai'];

    protected string $defaultSort = 'urutan';

    protected array $filterable = [
        'is_active' => 'boolean',
    ];

    protected function rules(string $mode, ?Model $record): array
    {
        $wajib = $mode === 'create' ? 'required' : 'sometimes';

        return [
            'judul' => ['nullable', 'string', 'max:255'],
            'gambar' => [$wajib, 'string', 'max:500'],
            'link' => ['nullable', 'string', 'max:500'],
            'urutan' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
        ];
    }
}
