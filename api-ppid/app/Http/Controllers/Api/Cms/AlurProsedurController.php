<?php

namespace App\Http\Controllers\Api\Cms;

use App\Http\Controllers\Api\CrudController;
use App\Models\AlurProsedur;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

/**
 * Alur bergambar halaman Standar Layanan.
 *
 * Modul unggah gambar: petugas mengunggah infografis alur (satu gambar per
 * tahap besar) dan situs publik menayangkannya berurutan — bukan mengetik
 * ulang isi gambarnya di CMS.
 *
 * Hak aksesnya menumpang modul Halaman Statis, sama seperti Maklumat: keduanya
 * adalah isi halaman Standar Layanan, bukan modul layanan tersendiri.
 */
class AlurProsedurController extends CrudController
{
    protected string $model = AlurProsedur::class;

    protected string $modulSlug = 'halaman-statis';

    protected array $searchable = ['judul', 'keterangan'];

    protected array $sortable = ['id', 'judul', 'urutan', 'halaman', 'created_at'];

    protected string $defaultSort = 'urutan';

    protected array $filterable = [
        'halaman' => 'exact',
        'is_active' => 'boolean',
    ];

    protected function rules(string $mode, ?Model $record): array
    {
        $wajib = $mode === 'create' ? 'required' : 'sometimes';

        return [
            'halaman' => ['sometimes', Rule::in(['prosedur-permohonan', 'prosedur-keberatan'])],
            'judul' => [$wajib, 'string', 'max:255'],
            'judul_en' => ['nullable', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string', 'max:2000'],
            'keterangan_en' => ['nullable', 'string', 'max:2000'],
            // Gambarnya inti modul ini: baris tanpa gambar tidak punya apa pun
            // untuk ditayangkan, jadi situs publik pun melewatkannya.
            'gambar' => [$wajib, 'string', 'max:500'],
            'urutan' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_active' => ['boolean'],
        ];
    }
}
