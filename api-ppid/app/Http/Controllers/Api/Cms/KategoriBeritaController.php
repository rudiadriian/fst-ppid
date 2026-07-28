<?php

namespace App\Http\Controllers\Api\Cms;

use App\Http\Controllers\Api\CrudController;
use App\Models\KategoriBerita;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class KategoriBeritaController extends CrudController
{
    protected string $model = KategoriBerita::class;

    protected string $modulSlug = 'berita';

    protected array $searchable = ['nama', 'slug'];

    protected array $sortable = ['id', 'nama'];

    protected string $defaultSort = 'nama';

    protected ?string $slugFrom = 'nama';

    protected function rules(string $mode, ?Model $record): array
    {
        $wajib = $mode === 'create' ? 'required' : 'sometimes';

        return [
            'nama' => [$wajib, 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:100'],
        ];
    }

    protected function beforeDelete(Model $record): void
    {
        if ($record->berita()->exists()) {
            throw ValidationException::withMessages([
                'id' => 'Kategori masih dipakai berita.',
            ]);
        }
    }
}
