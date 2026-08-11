<?php

namespace App\Http\Controllers\Api\Cms;

use App\Http\Controllers\Api\CrudController;
use App\Models\Regulasi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class RegulasiController extends CrudController
{
    protected string $model = Regulasi::class;

    protected string $modulSlug = 'regulasi';

    protected array $searchable = ['judul', 'ringkasan', 'nomor_peraturan', 'jenis_peraturan'];

    protected array $withList = ['pengunggah'];

    protected array $sortable = ['id', 'judul', 'tahun', 'tanggal_berlaku', 'created_at'];

    // Kolom Nomor dan Tahun dilepas dari modul CMS-nya, jadi daftarnya diurut
    // berdasarkan judul. Kolomnya sendiri tetap ada di tabel untuk data lama.
    protected string $defaultSort = 'judul';

    protected array $filterable = [
        'kategori' => 'exact',
        'tahun' => 'exact',
    ];

    protected function rules(string $mode, ?Model $record): array
    {
        $wajib = $mode === 'create' ? 'required' : 'sometimes';

        return [
            'kategori' => ['sometimes', Rule::in(['dasar_hukum_ppid', 'regulasi', 'pedoman'])],
            'judul' => [$wajib, 'string', 'max:255'],
            'ringkasan' => ['nullable', 'string', 'max:2000'],
            'nomor_peraturan' => ['nullable', 'string', 'max:100'],
            'jenis_peraturan' => ['nullable', 'string', 'max:100'],
            'tahun' => ['nullable', 'integer', 'min:1945', 'max:2100'],
            'file_path' => ['nullable', 'string', 'max:500'],
            'tanggal_berlaku' => ['nullable', 'date'],
        ];
    }

    /**
     * Pengunggah diisi server dari token, bukan dari input klien — nama yang
     * tampil di situs publik harus benar-benar petugas yang menyimpannya.
     */
    protected function beforeSave(array $data, Request $request, ?Model $record): array
    {
        $data['uploaded_by'] = Auth::guard('api')->id();

        return $data;
    }
}
