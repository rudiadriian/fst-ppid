<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * Project ini murni API — tidak punya halaman login sendiri. Mengembalikan
     * null membuat Laravel melempar AuthenticationException yang sudah diubah
     * jadi JSON 401 di Handler. Sebelumnya, request tanpa header
     * `Accept: application/json` mencoba redirect ke route bernama `login`
     * yang tidak ada, dan klien menerima 500 alih-alih 401.
     */
    protected function redirectTo(Request $request): ?string
    {
        return null;
    }
}
