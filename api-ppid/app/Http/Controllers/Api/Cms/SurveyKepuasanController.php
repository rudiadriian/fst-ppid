<?php

namespace App\Http\Controllers\Api\Cms;

use App\Http\Controllers\Api\CrudController;
use App\Models\SurveyKepuasan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

/**
 * Survei Kepuasan Pemohon.
 *
 * Angka "Kepuasan" pada halaman Laporan Statistik Informasi Publik di situs
 * publik dihitung dari rata-rata `rating` tabel ini, jadi modul ini adalah
 * satu-satunya sumbernya. Hak aksesnya menumpang modul `permohonan` karena
 * survei selalu melekat pada permohonan yang sudah dilayani.
 */
class SurveyKepuasanController extends CrudController
{
    protected string $model = SurveyKepuasan::class;

    protected string $modulSlug = 'permohonan';

    protected array $searchable = ['komentar'];

    protected array $sortable = ['id', 'rating', 'created_at'];

    protected string $defaultSort = '-id';

    protected array $withList = ['permohonan:id,kode_permohonan'];

    protected array $withDetail = ['permohonan:id,kode_permohonan'];

    protected array $filterable = [
        'rating' => 'exact',
        'permohonan_id' => 'exact',
    ];

    protected function rules(string $mode, ?Model $record): array
    {
        $wajib = $mode === 'create' ? 'required' : 'sometimes';

        return [
            'permohonan_id' => ['nullable', 'integer', Rule::exists('permohonan_informasi', 'id')],
            'rating' => [$wajib, 'integer', 'min:1', 'max:5'],
            'komentar' => ['nullable', 'string'],
        ];
    }
}
