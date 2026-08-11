<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Formulir hanya boleh diisi akun yang emailnya sudah terverifikasi.
 *
 * Bisa dimatikan lewat `config('ppid.akun.wajib_verifikasi_email')` (env
 * `PPID_WAJIB_VERIFIKASI_EMAIL`) untuk lingkungan yang belum punya SMTP —
 * kalau dimatikan, middleware ini membiarkan permintaan lewat.
 */
class EnsurePemohonEmailIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!config('ppid.akun.wajib_verifikasi_email')) {
            return $next($request);
        }

        $pemohon = Auth::guard('pemohon')->user();

        if ($pemohon && $pemohon->hasVerifiedEmail()) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => __('Verifikasi email Anda terlebih dahulu.'),
                'login_url' => route('akun.verifikasi.notice'),
            ], 403);
        }

        return redirect()->route('akun.verifikasi.notice');
    }
}
