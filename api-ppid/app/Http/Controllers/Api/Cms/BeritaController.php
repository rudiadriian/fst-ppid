<?php

namespace App\Http\Controllers\Api\Cms;

use App\Http\Controllers\Api\CrudController;
use App\Models\Berita;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class BeritaController extends CrudController
{
    protected string $model = Berita::class;

    protected string $modulSlug = 'berita';

    protected array $searchable = ['judul', 'ringkasan'];

    protected array $sortable = ['id', 'judul', 'tanggal_publikasi', 'status', 'views_count', 'created_at'];

    protected array $withList = ['kategori:id,nama,slug', 'author:id,name'];

    protected array $filterable = [
        'status' => 'exact',
        'kategori_berita_id' => 'exact',
        'tanggal_publikasi' => 'date',
    ];

    protected ?string $slugFrom = 'judul';

    protected function rules(string $mode, ?Model $record): array
    {
        $wajib = $mode === 'create' ? 'required' : 'sometimes';

        return [
            'kategori_berita_id' => ['nullable', Rule::exists('kategori_berita', 'id')],
            'judul' => [$wajib, 'string', 'max:255'],
            'judul_en' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'thumbnail' => ['nullable', 'string', 'max:500'],
            'ringkasan' => ['nullable', 'string'],
            'ringkasan_en' => ['nullable', 'string'],
            'konten' => ['nullable', 'string'],
            'konten_en' => ['nullable', 'string'],
            'tanggal_publikasi' => ['nullable', 'date'],
            'status' => ['sometimes', Rule::in(['draft', 'published', 'archived'])],
        ];
    }

    protected function beforeSave(array $data, Request $request, ?Model $record): array
    {
        if ($record === null) {
            $data['penulis'] = Auth::guard('api')->id();
        }

        if (($data['status'] ?? null) === 'published') {
            $data['tanggal_publikasi'] = $data['tanggal_publikasi'] ?? $record?->tanggal_publikasi ?? now()->toDateString();
        }

        return $data;
    }
}
