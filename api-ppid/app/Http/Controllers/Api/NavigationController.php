<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ModulSistem;
use App\Models\RoleModulAkses;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Menu dan hak akses panel admin, dihitung dari `role_modul_akses`.
 *
 * Frontend memakai ini untuk menyusun menu samping dan menyembunyikan tombol
 * aksi. Penyembunyian tombol semata-mata soal tampilan — penegakan hak akses
 * tetap dilakukan middleware `akses:` di setiap endpoint.
 */
class NavigationController extends Controller
{
    public function index(): JsonResponse
    {
        /** @var User|null $user */
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json(['error' => 'Tidak terautentikasi'], 401);
        }

        $user->loadMissing('role');

        $modul = ModulSistem::where('is_active', true)->orderBy('urutan')->get();
        $superAdmin = $user->role?->slug === 'super-admin';

        $akses = $superAdmin
            ? collect()
            : RoleModulAkses::where('role_id', $user->role_id)->get()->keyBy('modul_id');

        $items = $modul
            ->map(function (ModulSistem $m) use ($akses, $superAdmin) {
                $hak = $akses[$m->id] ?? null;

                return [
                    'id' => $m->id,
                    'slug' => $m->slug,
                    'nama' => $m->nama,
                    'icon' => $m->icon,
                    'route' => $m->route,
                    'urutan' => $m->urutan,
                    'parent_id' => $m->parent_id,
                    'akses' => [
                        'view' => $superAdmin || (bool) ($hak->can_view ?? false),
                        'create' => $superAdmin || (bool) ($hak->can_create ?? false),
                        'edit' => $superAdmin || (bool) ($hak->can_edit ?? false),
                        'delete' => $superAdmin || (bool) ($hak->can_delete ?? false),
                        'approve' => $superAdmin || (bool) ($hak->can_approve ?? false),
                        'export' => $superAdmin || (bool) ($hak->can_export ?? false),
                    ],
                ];
            })
            ->filter(fn (array $item) => $item['akses']['view'])
            ->values();

        return response()->json([
            'data' => [
                'role' => $user->role?->slug,
                'modul' => $items,
            ],
        ]);
    }
}
