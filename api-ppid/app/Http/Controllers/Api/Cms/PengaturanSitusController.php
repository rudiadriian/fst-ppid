<?php

namespace App\Http\Controllers\Api\Cms;

use App\Http\Controllers\Api\CrudController;
use App\Models\PengaturanSitus;
use App\Support\AuditLogger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PengaturanSitusController extends CrudController
{
    protected string $model = PengaturanSitus::class;

    protected string $modulSlug = 'pengaturan-situs';

    protected array $searchable = ['key', 'value', 'group_name'];

    protected array $sortable = ['id', 'key', 'group_name'];

    protected string $defaultSort = 'group_name';

    protected array $filterable = [
        'group_name' => 'exact',
    ];

    protected function rules(string $mode, ?Model $record): array
    {
        return [
            'key' => [
                $mode === 'create' ? 'required' : 'sometimes',
                'string',
                'max:100',
                'regex:/^[a-z0-9_.]+$/',
                Rule::unique('pengaturan_situs', 'key')->ignore($record?->getKey()),
            ],
            'value' => ['nullable', 'string'],
            'group_name' => ['nullable', 'string', 'max:50'],
        ];
    }

    /**
     * Simpan banyak pengaturan sekaligus dari satu form.
     * Kunci yang belum ada dibuat, yang sudah ada diperbarui.
     */
    public function simpanMassal(Request $request): JsonResponse
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'max:200'],
            'items.*.key' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9_.]+$/'],
            'items.*.value' => ['nullable', 'string'],
            'items.*.group_name' => ['nullable', 'string', 'max:50'],
        ]);

        DB::transaction(function () use ($data) {
            foreach ($data['items'] as $item) {
                $sebelum = PengaturanSitus::where('key', $item['key'])->value('value');

                PengaturanSitus::updateOrCreate(
                    ['key' => $item['key']],
                    [
                        'value' => $item['value'] ?? null,
                        'group_name' => $item['group_name'] ?? null,
                    ]
                );

                AuditLogger::record(
                    Auth::guard('api')->id(),
                    'update',
                    PengaturanSitus::class,
                    null,
                    ['key' => $item['key'], 'value' => $sebelum],
                    ['key' => $item['key'], 'value' => $item['value'] ?? null]
                );
            }
        });

        return response()->json(['message' => 'Pengaturan disimpan']);
    }
}
