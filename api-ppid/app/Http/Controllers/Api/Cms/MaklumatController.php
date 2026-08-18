<?php

namespace App\Http\Controllers\Api\Cms;

use App\Http\Controllers\Api\CrudController;
use App\Models\Maklumat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Maklumat Pelayanan Informasi Publik.
 *
 * Modul unggah dokumen: petugas mengunggah berkas maklumat (PDF/gambar) dan
 * situs publik menampilkan isi berkas itu langsung di halaman Standar
 * Pelayanan — bukan mengetik ulang butir-butirnya di CMS.
 *
 * Hak aksesnya menumpang modul Halaman Statis karena maklumat adalah salah
 * satu halaman Standar Layanan.
 */
class MaklumatController extends CrudController
{
    protected string $model = Maklumat::class;

    protected string $modulSlug = 'halaman-statis';

    protected array $searchable = ['judul', 'ringkasan'];

    protected array $sortable = ['id', 'judul', 'tanggal_terbit', 'status', 'created_at'];

    protected string $defaultSort = '-tanggal_terbit';

    protected array $withList = ['publisher:id,name'];

    protected array $filterable = [
        'status' => 'exact',
    ];

    protected function rules(string $mode, ?Model $record): array
    {
        $wajib = $mode === 'create' ? 'required' : 'sometimes';

        return [
            'judul' => [$wajib, 'string', 'max:255'],
            'judul_en' => ['nullable', 'string', 'max:255'],
            'ringkasan' => ['nullable', 'string', 'max:2000'],
            'ringkasan_en' => ['nullable', 'string', 'max:2000'],
            // Berkasnya inti modul ini: tanpa berkas, situs publik jatuh ke
            // teks bawaan dan maklumat resminya tidak pernah tayang.
            'file_dokumen' => [$wajib, 'string', 'max:500'],
            'tanggal_terbit' => ['nullable', 'date'],
            'status' => ['sometimes', Rule::in(['draft', 'published', 'archived'])],
        ];
    }

    protected function beforeSave(array $data, Request $request, ?Model $record): array
    {
        if (($data['status'] ?? null) === 'published') {
            $data['published_by'] = Auth::guard('api')->id();

            // Tanggal terbit dipakai situs publik untuk memilih maklumat yang
            // berlaku; kalau petugas tidak mengisinya, pakai hari penerbitan.
            if (blank($data['tanggal_terbit'] ?? $record?->tanggal_terbit)) {
                $data['tanggal_terbit'] = now()->toDateString();
            }
        }

        return $data;
    }
}
