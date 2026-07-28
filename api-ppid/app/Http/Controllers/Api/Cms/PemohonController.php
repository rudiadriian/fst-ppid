<?php

namespace App\Http\Controllers\Api\Cms;

use App\Http\Controllers\Api\CrudController;
use App\Models\Pemohon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PemohonController extends CrudController
{
    protected string $model = Pemohon::class;

    protected string $modulSlug = 'permohonan';

    protected array $searchable = ['nama', 'email', 'no_hp', 'nama_lembaga'];

    protected array $sortable = ['id', 'nama', 'email', 'created_at'];

    protected array $filterable = [
        'jenis_pemohon' => 'exact',
    ];

    protected function rules(string $mode, ?Model $record): array
    {
        $wajib = $mode === 'create' ? 'required' : 'sometimes';

        return [
            'nik' => ['nullable', 'string', 'max:20', 'regex:/^[0-9]+$/'],
            'nama' => [$wajib, 'string', 'max:150'],
            'email' => [$wajib, 'email', 'max:150'],
            'no_hp' => ['nullable', 'string', 'max:30'],
            'alamat' => ['nullable', 'string'],
            'pekerjaan' => ['nullable', 'string', 'max:100'],
            'jenis_pemohon' => ['sometimes', Rule::in(['pribadi', 'instansi', 'kelompok'])],
            'nama_lembaga' => ['nullable', 'string', 'max:200'],
        ];
    }

    protected function beforeDelete(Model $record): void
    {
        if ($record->permohonan()->exists()) {
            throw ValidationException::withMessages([
                'id' => 'Pemohon masih punya permohonan tercatat.',
            ]);
        }
    }
}
