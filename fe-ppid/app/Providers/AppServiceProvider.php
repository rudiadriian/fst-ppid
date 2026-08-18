<?php

namespace App\Providers;

use App\Models\Pemohon;
use App\View\Composers\CmsLayoutComposer;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\HtmlString;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (env('APP_ENV') !== 'local') {
            URL::forceScheme('https');
        }

        // Satu-satunya akun di situs publik adalah pengunjung (`Pemohon`), jadi
        // notifikasi selalu menunjuk ke halaman `/akun/…`.
        // Masa berlakunya dibaca dari `config('auth.verification.expire')` supaya
        // angka yang dipakai membuat tautan sama dengan angka yang ditulis di
        // email dan di halaman peringatan.
        VerifyEmail::createUrlUsing(fn (Pemohon $pemohon) => URL::temporarySignedRoute(
            'akun.verifikasi.verify',
            now()->addMinutes((int) config('auth.verification.expire', 1440)),
            ['id' => $pemohon->getKey(), 'hash' => sha1($pemohon->getEmailForVerification())]
        ));

        ResetPassword::createUrlUsing(fn (Pemohon $pemohon, string $token) => route('akun.password.reset', [
            'token' => $token,
            'email' => $pemohon->getEmailForPasswordReset(),
        ]));

        // Menu dan blok kontak di header/footer diisi dari CMS.
        View::composer(['layouts.header', 'layouts.footer'], CmsLayoutComposer::class);

        // Kelas isian formulir dipakai bersama halaman akun & portal pengguna,
        // supaya seluruh formulir tampil seragam tanpa menyalin kelas panjang.
        View::share('fsInput', 'mt-1.5 block w-full px-4 py-3 bg-gray-50 border border-gray-200 dark:border-white/10 rounded-xl focus:bg-white dark:bg-[#0B2A1D] focus:border-[#10462F] focus:ring-2 focus:ring-[#10462F]/15 outline-none transition-all text-base');
        View::share('fsLabel', 'block text-sm font-medium text-gray-700 dark:text-gray-300');
        View::share('fsBtn', 'inline-flex items-center justify-center py-3 px-6 fs-gradient-accent text-white text-base font-semibold rounded-xl shadow-lg shadow-emerald-900/20 hover:-translate-y-0.5 transition-all duration-300 disabled:opacity-60 disabled:cursor-not-allowed disabled:translate-y-0');

        // Latar kartu bergantian antara tiga nada oranye (acuan theme-color-card.png).
        // Kelas .fs-card-1/2/3 didefinisikan di layouts/app.blade.php.
        View::share('cardTier', fn (int $i) => 'fs-card-'.(($i % 3) + 1));

        // Judul section dua warna (acuan theme-color.jpeg): sebagian kata memakai
        // warna dasar, sisanya warna aksen oranye.
        //   $kataAksen — jumlah kata terakhir yang diberi warna aksen.
        //   $kelas     — 'fs-title-accent' (di atas latar terang) atau
        //                'fs-title-accent-soft' (di atas latar hijau/gelap).
        View::share('judulDua', function (string $teks, int $kataAksen = 1, string $kelas = 'fs-title-accent') {
            $kata = preg_split('/\s+/', trim($teks), -1, PREG_SPLIT_NO_EMPTY) ?: [];

            // Judul satu kata: seluruhnya memakai warna aksen.
            if (count($kata) < 2) {
                return new HtmlString('<span class="'.$kelas.'">'.e($teks).'</span>');
            }

            // Selalu sisakan minimal satu kata pada warna dasar.
            $jumlah = max(1, min($kataAksen, count($kata) - 1));
            $aksen = array_splice($kata, -$jumlah);

            return new HtmlString(
                e(implode(' ', $kata)).' <span class="'.$kelas.'">'.e(implode(' ', $aksen)).'</span>'
            );
        });
    }
}
