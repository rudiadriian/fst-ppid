<?php

namespace App\Http\Controllers\Api\Cms;

use App\Http\Controllers\Api\CrudController;
use App\Models\LaporanTahunan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class LaporanTahunanController extends CrudController
{
    protected string $model = LaporanTahunan::class;

    protected string $modulSlug = 'laporan-tahunan';

    protected array $searchable = ['judul', 'tahun'];

    protected array $sortable = ['id', 'tahun', 'judul', 'urutan', 'status', 'created_at'];

    /*
     * Terbaru dulu: galeri beranda menampilkan tahun buku terakhir di kiri.
     * `urutan` dipakai bila petugas perlu menyusun ulang tanpa mengarang tahun.
     */
    protected string $defaultSort = '-tahun';

    protected array $filterable = [
        'status' => 'exact',
        'tahun' => 'exact',
    ];

    protected function rules(string $mode, ?Model $record): array
    {
        $wajib = $mode === 'create' ? 'required' : 'sometimes';

        return [
            'tahun' => [$wajib, 'integer', 'min:1900', 'max:2200'],
            'judul' => [$wajib, 'string', 'max:255'],
            'judul_en' => ['nullable', 'string', 'max:255'],
            // Path berkas berasal dari endpoint upload, bukan diketik operator.
            'sampul' => [$wajib, 'string', 'max:500'],
            // Halaman tempat laporannya dibaca. Hanya http/https supaya tidak
            // ada `javascript:` yang lolos ke tombol di beranda.
            'tautan' => ['nullable', 'url', 'starts_with:http://,https://', 'max:500'],
            'urutan' => ['nullable', 'integer', 'min:0'],
            'status' => ['sometimes', Rule::in(['draft', 'published', 'archived'])],
        ];
    }
}
