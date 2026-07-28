<?php

namespace App\Http\Controllers\Api\Cms;

use App\Http\Controllers\Api\CrudController;
use App\Models\KategoriInformasi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class KategoriInformasiController extends CrudController
{
    protected string $model = KategoriInformasi::class;

    protected string $modulSlug = 'kategori-informasi';

    protected array $searchable = ['nama', 'slug', 'deskripsi'];

    protected array $sortable = ['id', 'nama', 'urutan', 'created_at'];

    protected string $defaultSort = 'urutan';

    protected array $withList = ['parent'];

    protected array $filterable = [
        'parent_id' => 'exact',
        'is_active' => 'boolean',
    ];

    protected ?string $slugFrom = 'nama';

    protected function rules(string $mode, ?Model $record): array
    {
        $wajib = $mode === 'create' ? 'required' : 'sometimes';

        return [
            'parent_id' => ['nullable', Rule::exists('kategori_informasi', 'id')],
            'nama' => [$wajib, 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:150'],
            'deskripsi' => ['nullable', 'string'],
            'urutan' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ];
    }

    protected function beforeSave(array $data, \Illuminate\Http\Request $request, ?Model $record): array
    {
        // Kategori tidak boleh menjadi induk dirinya sendiri (siklus pohon).
        if ($record !== null && ($data['parent_id'] ?? null) == $record->getKey()) {
            throw ValidationException::withMessages([
                'parent_id' => 'Kategori tidak boleh menjadi induk dirinya sendiri.',
            ]);
        }

        return $data;
    }

    protected function beforeDelete(Model $record): void
    {
        // Termasuk baris yang sudah di-soft delete: foreign key di PostgreSQL
        // memakai RESTRICT dan tetap menahan penghapusan walau baris perujuknya
        // sudah "terhapus" di level aplikasi.
        if ($record->informasiPublik()->withTrashed()->exists()) {
            throw ValidationException::withMessages([
                'id' => 'Kategori masih dipakai informasi publik (termasuk yang diarsipkan). Pindahkan datanya lebih dulu.',
            ]);
        }

        if ($record->children()->exists()) {
            throw ValidationException::withMessages([
                'id' => 'Kategori masih punya sub-kategori.',
            ]);
        }
    }
}
