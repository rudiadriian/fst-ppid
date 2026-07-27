<?php

namespace App\Http\Middleware;

use App\Models\ModulSistem;
use App\Models\RoleModulAkses;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Cek hak akses role terhadap satu modul backend.
 *
 * Pemakaian di route: ->middleware('akses:informasi-publik,edit')
 * Aksi yang valid: view, create, edit, delete, approve, export.
 *
 * Hak akses dibaca dari DB tiap request (bukan dari klaim token) supaya
 * pencabutan akses langsung berlaku tanpa menunggu token lama kedaluwarsa.
 */
class CheckModulAkses
{
    private const AKSI_VALID = ['view', 'create', 'edit', 'delete', 'approve', 'export'];

    public function handle(Request $request, Closure $next, string $modulSlug, string $aksi = 'view'): Response
    {
        if (!in_array($aksi, self::AKSI_VALID, true)) {
            abort(500, "Aksi hak akses tidak dikenal: {$aksi}");
        }

        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json(['error' => 'Tidak terautentikasi'], 401);
        }

        $user->loadMissing('role');

        if ($user->role?->slug === 'super-admin') {
            return $next($request);
        }

        $modul = ModulSistem::where('slug', $modulSlug)->where('is_active', true)->first();

        if (!$modul) {
            return response()->json(['error' => 'Modul tidak ditemukan atau nonaktif'], 403);
        }

        $akses = RoleModulAkses::where('role_id', $user->role_id)
            ->where('modul_id', $modul->id)
            ->first();

        if (!$akses || !$akses->{'can_'.$aksi}) {
            return response()->json([
                'error' => "Role Anda tidak punya hak '{$aksi}' pada modul '{$modulSlug}'",
            ], 403);
        }

        return $next($request);
    }
}
