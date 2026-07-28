<?php

namespace App\Http\Controllers\Api\Cms;

use App\Http\Controllers\Api\CrudController;
use App\Models\ModulSistem;
use App\Models\Role;
use App\Models\RoleModulAkses;
use App\Models\User;
use App\Support\AuditLogger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class RoleController extends CrudController
{
    protected string $model = Role::class;

    protected string $modulSlug = 'pengguna';

    protected array $searchable = ['name', 'slug', 'description'];

    protected array $sortable = ['id', 'name', 'slug'];

    protected string $defaultSort = 'name';

    protected ?string $slugFrom = 'name';

    protected function rules(string $mode, ?Model $record): array
    {
        $wajib = $mode === 'create' ? 'required' : 'sometimes';

        return [
            'name' => [$wajib, 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:100', 'regex:/^[a-z0-9-]+$/'],
            'description' => ['nullable', 'string'],
        ];
    }

    protected function beforeDelete(Model $record): void
    {
        if ($record->slug === 'super-admin') {
            throw ValidationException::withMessages([
                'id' => 'Role super-admin tidak dapat dihapus.',
            ]);
        }

        if (User::where('role_id', $record->getKey())->exists()) {
            throw ValidationException::withMessages([
                'id' => 'Role masih dipakai pengguna.',
            ]);
        }
    }

    /**
     * Matrix hak akses satu role terhadap seluruh modul.
     */
    public function akses(int $id): JsonResponse
    {
        $role = Role::findOrFail($id);

        $akses = RoleModulAkses::where('role_id', $role->id)->get()->keyBy('modul_id');

        $baris = ModulSistem::orderBy('urutan')->get()->map(fn (ModulSistem $modul) => [
            'modul_id' => $modul->id,
            'slug' => $modul->slug,
            'nama' => $modul->nama,
            'can_view' => (bool) ($akses[$modul->id]->can_view ?? false),
            'can_create' => (bool) ($akses[$modul->id]->can_create ?? false),
            'can_edit' => (bool) ($akses[$modul->id]->can_edit ?? false),
            'can_delete' => (bool) ($akses[$modul->id]->can_delete ?? false),
            'can_approve' => (bool) ($akses[$modul->id]->can_approve ?? false),
            'can_export' => (bool) ($akses[$modul->id]->can_export ?? false),
        ]);

        return response()->json(['data' => ['role' => $role, 'akses' => $baris]]);
    }

    public function simpanAkses(Request $request, int $id): JsonResponse
    {
        $role = Role::findOrFail($id);

        if ($role->slug === 'super-admin') {
            throw ValidationException::withMessages([
                'role_id' => 'Hak akses super-admin bersifat tetap dan tidak bisa dibatasi.',
            ]);
        }

        $data = $request->validate([
            'akses' => ['required', 'array'],
            'akses.*.modul_id' => ['required', 'integer', Rule::exists('modul_sistem', 'id')],
            'akses.*.can_view' => ['boolean'],
            'akses.*.can_create' => ['boolean'],
            'akses.*.can_edit' => ['boolean'],
            'akses.*.can_delete' => ['boolean'],
            'akses.*.can_approve' => ['boolean'],
            'akses.*.can_export' => ['boolean'],
        ]);

        DB::transaction(function () use ($data, $role) {
            foreach ($data['akses'] as $item) {
                RoleModulAkses::updateOrCreate(
                    ['role_id' => $role->id, 'modul_id' => $item['modul_id']],
                    [
                        'can_view' => (bool) ($item['can_view'] ?? false),
                        'can_create' => (bool) ($item['can_create'] ?? false),
                        'can_edit' => (bool) ($item['can_edit'] ?? false),
                        'can_delete' => (bool) ($item['can_delete'] ?? false),
                        'can_approve' => (bool) ($item['can_approve'] ?? false),
                        'can_export' => (bool) ($item['can_export'] ?? false),
                    ]
                );
            }
        });

        AuditLogger::record(
            Auth::guard('api')->id(),
            'update',
            RoleModulAkses::class,
            $role->id,
            null,
            ['role' => $role->slug, 'jumlah_modul' => count($data['akses'])]
        );

        return response()->json(['message' => 'Hak akses role diperbarui']);
    }
}
