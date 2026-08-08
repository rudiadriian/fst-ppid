<?php

namespace App\Providers;

use App\View\Composers\CmsLayoutComposer;
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

        // Menu dan blok kontak di header/footer diisi dari CMS.
        View::composer(['layouts.header', 'layouts.footer'], CmsLayoutComposer::class);

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
