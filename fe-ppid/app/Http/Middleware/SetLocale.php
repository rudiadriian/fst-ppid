<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Set application locale from ?lang=, then session. Default: id.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->has('lang') && in_array($request->query('lang'), ['id', 'en'])) {
            Session::put('locale', $request->query('lang'));
        }

        $locale = Session::get('locale', 'id');
        App::setLocale($locale);

        return $next($request);
    }
}
