<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Halaman hanya boleh dibuka pengunjung yang sudah masuk (guard `pemohon`).
 *
 * Dipisah dari middleware `auth` bawaan supaya pengunjung yang belum masuk
 * diarahkan ke halaman login publik (`/akun/masuk`), bukan ke login petugas.
 */
class AuthenticatePemohon
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('pemohon')->check()) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => __('Silakan masuk ke akun pengunjung terlebih dahulu.'),
                'login_url' => route('akun.login'),
            ], 401);
        }

        // Simpan tujuan supaya setelah masuk pengunjung kembali ke formulirnya.
        $request->session()->put('url.intended', $request->fullUrl());

        return redirect()->route('akun.login')
            ->with('status', __('Formulir ini hanya bisa diisi setelah Anda masuk ke akun pengunjung.'));
    }
}
