<?php

namespace App\Http\Controllers\Api\Cms;

use App\Http\Controllers\Api\CrudController;
use App\Models\User;
use App\Rules\EmailBelumTerpakai;
use App\Support\AuditLogger;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
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
            /*
             * Keunikan email diperiksa lintas tabel, bukan hanya di `users`:
             * alamat yang sudah dipakai akun pemohon di situs publik ikut
             * ditolak. Baris terhapus pun ikut dihitung — alasan lengkapnya ada
             * di `EmailBelumTerpakai`.
             */
            'email' => [
                $wajib,
                'email',
                'max:150',
                new EmailBelumTerpakai(abaikanUserId: $record === null ? null : (int) $record->getKey()),
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

    /**
     * Hapus permanen satu akun yang sudah berada di arsip penghapusan.
     *
     * Penghapusan biasa hanya menandai `deleted_at`: barisnya tetap ada, dan
     * emailnya tetap menempati indeks unik `users.email` sehingga alamat itu
     * tidak bisa dipakai akun baru. Yang dilepas di sini adalah barisnya sendiri.
     *
     * Dua pagar sebelum barisnya lenyap:
     *
     *  - hanya baris yang **sudah dihapus** yang boleh dihapus permanen, jadi
     *    tidak ada akun aktif yang bisa hilang dalam satu langkah — arsipnya
     *    menjadi ruang jeda yang disengaja;
     *  - akun sendiri tetap tidak bisa disentuh, sama seperti pada `destroy()`.
     *
     * Jejak yang ditinggalkan akun ini di modul lain tidak ikut hilang: seluruh
     * kolom `created_by`/`updated_by`/`deleted_by` berelasi `ON DELETE SET NULL`,
     * jadi barisnya tetap ada dengan kolom pelaku kosong. Yang ikut terhapus
     * hanya notifikasi pribadinya (`ON DELETE CASCADE`) — isinya memang tidak
     * punya pembaca lagi. Riwayat aksinya di `audit_log` tetap tersimpan, dengan
     * `user_id` menjadi null, dan penghapusan ini sendiri ikut tercatat di sana
     * lengkap dengan nama serta email akun yang dilepas.
     */
    public function hapusPermanen(int $id): JsonResponse
    {
        /** @var User $record */
        $record = User::withTrashed()->findOrFail($id);

        if ($record->deleted_at === null) {
            throw ValidationException::withMessages([
                'id' => 'Akun ini masih aktif. Hapus dulu akunnya, baru bisa dihapus permanen.',
            ]);
        }

        if ((int) $record->getKey() === (int) Auth::guard('api')->id()) {
            throw ValidationException::withMessages([
                'id' => 'Anda tidak dapat menghapus akun sendiri.',
            ]);
        }

        $sebelum = $this->scrub($record->getAttributes());

        $record->forceDelete();

        AuditLogger::record(
            Auth::guard('api')->id(),
            'force_delete',
            User::class,
            $id,
            $sebelum,
            null
        );

        return response()->json(['message' => 'Akun dihapus permanen']);
    }
}
