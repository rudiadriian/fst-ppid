<?php

namespace App\Http\Controllers\Api\Cms;

use App\Http\Controllers\Api\CrudController;
use App\Models\InformasiDikecualikan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class InformasiDikecualikanController extends CrudController
{
    protected string $model = InformasiDikecualikan::class;

    protected string $modulSlug = 'informasi-dikecualikan';

    protected array $searchable = ['judul', 'ringkasan', 'alasan_pengecualian', 'dasar_hukum_pengecualian'];

    protected array $sortable = ['id', 'judul', 'tanggal_penetapan', 'status', 'created_at'];

    protected array $withList = ['pejabat:id,name'];

    protected array $filterable = [
        'status' => 'exact',
        'tanggal_penetapan' => 'date',
    ];

    protected ?string $slugFrom = 'judul';

    protected function rules(string $mode, ?Model $record): array
    {
        $wajib = $mode === 'create' ? 'required' : 'sometimes';

        return [
            'judul' => [$wajib, 'string', 'max:255'],
            'judul_en' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'ringkasan' => ['nullable', 'string'],
            'ringkasan_en' => ['nullable', 'string'],
            // Situs publik hanya menampilkan judulnya, jadi keterangan penetapan
            // di bawah ini semuanya opsional — diisi hanya bila diperlukan.
            'alasan_pengecualian' => ['nullable', 'string'],
            'dasar_hukum_pengecualian' => ['nullable', 'string', 'max:255'],
            'jangka_waktu_pengecualian' => ['nullable', 'string', 'max:100'],
            'tanggal_penetapan' => ['nullable', 'date'],
            'file_surat_penetapan' => ['nullable', 'string', 'max:500'],
            'status' => ['sometimes', Rule::in(['draft', 'published', 'archived'])],
        ];
    }

    protected function beforeSave(array $data, Request $request, ?Model $record): array
    {
        // Pejabat penetap = user yang menerbitkan, diambil dari sesi bukan input.
        if (($data['status'] ?? null) === 'published' && $record?->pejabat_penetap === null) {
            $data['pejabat_penetap'] = Auth::guard('api')->id();
        }

        return $data;
    }
}
