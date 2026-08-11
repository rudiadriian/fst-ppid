<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/** Halaman masuk/daftar tidak perlu dibuka lagi oleh pengunjung yang sudah masuk. */
class RedirectIfPemohonAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('pemohon')->check()) {
            return redirect()->route('akun.dashboard');
        }

        return $next($request);
    }
}
