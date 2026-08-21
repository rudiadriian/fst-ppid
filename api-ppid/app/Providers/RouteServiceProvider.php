<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        // Panel admin memuat beberapa daftar sekaligus per halaman, jadi kuota
        // untuk sesi yang sudah login lebih longgar daripada untuk anonim.
        RateLimiter::for('api', function (Request $request) {
            $userId = $this->idPengguna($request);

            return $userId
                ? Limit::perMinute(300)->by('user:'.$userId)
                : Limit::perMinute(60)->by('ip:'.$request->ip());
        });

        // Unggahan berkas jauh lebih mahal daripada request biasa.
        RateLimiter::for('uploads', function (Request $request) {
            return Limit::perMinute(30)->by('upload:'.($this->idPengguna($request) ?: $request->ip()));
        });

        // Rem brute force login: per kombinasi email + IP, dan per IP saja.
        RateLimiter::for('login', function (Request $request) {
            return [
                Limit::perMinute(5)->by(strtolower((string) $request->input('email')).'|'.$request->ip()),
                Limit::perMinute(20)->by($request->ip()),
            ];
        });

        /*
         * Menggambar captcha memakai GD — jauh lebih mahal daripada menjawab
         * JSON. Kuotanya per IP saja: endpoint ini terbuka, belum ada identitas
         * yang bisa dipakai sebagai kunci.
         */
        RateLimiter::for('captcha', function (Request $request) {
            return Limit::perMinute(30)->by('captcha:'.$request->ip());
        });

        /*
         * Rem lapis pertama untuk lupa/reset password. Tangga bertingkat yang
         * sebenarnya ada di `KunciTautanAdmin`; yang ini sekadar menahan
         * banjir dalam hitungan detik sebelum tangga itu sempat menghitung.
         */
        RateLimiter::for('tautan-akun', function (Request $request) {
            return [
                Limit::perMinute(5)->by(strtolower((string) $request->input('email')).'|'.$request->ip()),
                Limit::perMinute(15)->by('tautan:'.$request->ip()),
            ];
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * ID pengguna dari token JWT, atau null bila token tidak ada/tidak sah.
     *
     * Throttle berjalan sebelum middleware auth, jadi token harus dibaca
     * sendiri di sini. Token rusak tidak boleh melempar error — cukup
     * diperlakukan sebagai anonim dan biarkan auth yang menolak.
     */
    private function idPengguna(Request $request): ?int
    {
        try {
            return auth('api')->id();
        } catch (\Throwable) {
            return null;
        }
    }
}
