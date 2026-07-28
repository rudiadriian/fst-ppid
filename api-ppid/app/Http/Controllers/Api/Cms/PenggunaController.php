<?php

namespace App\Http\Controllers\Api\Cms;

use App\Http\Controllers\Api\CrudController;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class PenggunaController extends CrudController
{
    protected string $model = User::class;

    protected string $modulSlug = 'pengguna';

    protected array $searchable = ['name', 'email', 'phone'];

    protected array $sortable = ['id', 'name', 'email', 'last_login_at', 'created_at'];

    protected array $withList = ['role:id,name,slug'];

    protected array $filterable = [
        'role_id' => 'exact',
        'is_active' => 'boolean',
    ];

    protected function rules(string $mode, ?Model $record): array
    {
        $wajib = $mode === 'create' ? 'required' : 'sometimes';

        return [
            'role_id' => [$wajib, Rule::exists('roles', 'id')],
            'name' => [$wajib, 'string', 'max:150'],
            'email' => [
                $wajib,
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($record?->getKey())->whereNull('deleted_at'),
            ],
            // Kata sandi wajib saat pembuatan akun, opsional saat penyuntingan.
            'password' => [
                $mode === 'create' ? 'required' : 'nullable',
                'string',
                Password::min(12)->letters()->mixedCase()->numbers()->symbols(),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'is_active' => ['boolean'],
        ];
    }

    protected function beforeSave(array $data, Request $request, ?Model $record): array
    {
        if (blank($data['password'] ?? null)) {
            unset($data['password']); // cast 'hashed' di model yang mengenkripsi
        }

        // Admin tidak boleh menonaktifkan atau menurunkan akunnya sendiri;
        // mencegah panel terkunci tanpa super admin aktif.
        if ($record !== null && (int) $record->getKey() === (int) Auth::guard('api')->id()) {
            if (array_key_exists('is_active', $data) && !$data['is_active']) {
                throw ValidationException::withMessages([
                    'is_active' => 'Anda tidak dapat menonaktifkan akun sendiri.',
                ]);
            }

            if (array_key_exists('role_id', $data) && (int) $data['role_id'] !== (int) $record->role_id) {
                throw ValidationException::withMessages([
                    'role_id' => 'Anda tidak dapat mengubah role akun sendiri.',
                ]);
            }
        }

        return $data;
    }

    protected function beforeDelete(Model $record): void
    {
        if ((int) $record->getKey() === (int) Auth::guard('api')->id()) {
            throw ValidationException::withMessages([
                'id' => 'Anda tidak dapat menghapus akun sendiri.',
            ]);
        }
    }
}
