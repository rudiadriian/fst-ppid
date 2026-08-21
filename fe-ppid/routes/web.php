<?php

use App\Http\Controllers\CaptchaController;
use App\Http\Controllers\DokumenInformasiController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\KontenController;
use App\Http\Controllers\PpidController;
use App\Http\Controllers\SearchController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/search', [SearchController::class, 'index'])->name('search');

// Gambar captcha untuk formulir daftar/masuk akun. Dibatasi lajunya supaya
// tidak bisa dipakai memaksa server menggambar terus-menerus.
Route::get('/captcha', CaptchaController::class)
    ->middleware(['throttle:30,1', \App\Http\Middleware\BukanHalamanSebelumnya::class])
    ->name('captcha');


Route::get('/', [HomeController::class, 'index'])->name('ppid.home');

// Kanal konten yang seluruhnya dikelola dari CMS `be-ppid`.
Route::get('/berita', [KontenController::class, 'beritaIndex'])->name('ppid.news.index');
Route::get('/berita/{slug}', [KontenController::class, 'beritaShow'])->name('ppid.news.show');
Route::get('/faq', [KontenController::class, 'faq'])->name('ppid.faq');
Route::get('/struktur-ppid', [KontenController::class, 'struktur'])->name('ppid.structure');

// Ganti bahasa (relatif, origin-agnostic) — simpan ke session lalu kembali
Route::get('/set-locale/{locale}', function (Request $request, string $locale) {
    if (in_array($locale, ['id', 'en'], true)) {
        session(['locale' => $locale]);
    }

    // Kembali ke halaman asal, tapi hanya kalau asalnya memang situs ini —
    // header Referer datang dari peramban dan bisa menunjuk ke domain lain.
    $asal = $request->headers->get('referer');
    if ($asal && parse_url($asal, PHP_URL_HOST) === $request->getHost()) {
        return redirect()->to($asal);
    }

    return redirect()->to('/');
})->name('locale.set');

// Kanal Profil. Sub halamannya (termasuk Tugas, Fungsi dan Wewenang) ditangani
// satu rute slug; isinya boleh ditimpa lewat modul Halaman Statis di be-ppid.
Route::get('/profile/{slug}', [PpidController::class, 'showProfilePage'])->name('ppid.profile');

// Kanal Informasi Publik: Daftar Informasi Dikecualikan sebelum /informasi/{slug}.
Route::get('/informasi', [PpidController::class, 'showPublicInformationIndex'])->name('ppid.information.index');
Route::get('/informasi/dikecualikan', [PpidController::class, 'showExcludedInformation'])->name('ppid.excluded');

/*
 * Dokumen berunduhan terbatas (langkah 83).
 *
 * Didaftarkan sebelum `/informasi/{slug}` supaya segmen `dokumen` tidak
 * tertelan pola slug kategori.
 *
 * Melihat terbuka untuk siapa saja; mengunduh diperiksa
 * `App\Support\AksesDokumen` di dalam controllernya, bukan oleh middleware —
 * yang menentukan bukan sekadar sudah masuk atau belum, melainkan apakah
 * permohonan orang ini atas dokumen ini sudah disetujui petugas.
 */
Route::prefix('/informasi/dokumen/{dokumen}')->whereNumber('dokumen')->group(function () {
    Route::get('/', [DokumenInformasiController::class, 'pratinjau'])->name('ppid.dokumen.pratinjau');
    Route::get('/unduh', [DokumenInformasiController::class, 'unduh'])->name('ppid.dokumen.unduh');
    Route::get('/ajukan', [DokumenInformasiController::class, 'ajukan'])->name('ppid.dokumen.ajukan');
});

Route::get('/informasi/{slug}', [PpidController::class, 'showPublicInformation'])->name('ppid.information');

Route::get('/regulasi', [PpidController::class, 'showRegulationPage'])->name('ppid.regulation');
Route::get('/regulasi/{regulasi}', [PpidController::class, 'showRegulationDetail'])
    ->whereNumber('regulasi')
    ->name('ppid.regulation.show');
// Halaman detail Laporan Pelayanan Informasi — dokumennya dibaca di halaman
// itu juga, sama seperti detail Regulasi. Didaftarkan sebelum rute daftarnya
// supaya dua segmennya tidak tertelan.
Route::get('/laporan/pelayanan-informasi/{laporan}', [PpidController::class, 'showServiceReportDetail'])
    ->whereNumber('laporan')
    ->name('ppid.report.show');
/*
 * Menu Laporan tinggal satu: Laporan Pelayanan Informasi. Halaman Laporan
 * Statistik Informasi Publik dihapus pada langkah 68, jadi rutenya tidak lagi
 * menerima slug — alamat lama `/laporan/statistik-informasi` kini 404.
 */
Route::get('/laporan/pelayanan-informasi', [PpidController::class, 'showReportPage'])->name('ppid.report');
Route::get('/register-permohonan', [PpidController::class, 'showRequestRegister'])->name('ppid.register');
Route::get('/standar-layanan/{slug}', [PpidController::class, 'showServiceStandardPage'])->name('ppid.service');

/*
 * Formulir layanan pindah ke Portal Pengguna (`/akun/...`).
 *
 * Data pemohon tidak lagi diketik ulang di formulir — semuanya mengikuti akun
 * yang sedang masuk. Tautan lama tetap hidup supaya menu, bookmark, dan
 * dokumen yang menyebut /permohonan atau /keberatan tidak putus.
 */
Route::redirect('/permohonan', '/akun/permohonan/baru')->name('ppid.request');
Route::redirect('/keberatan', '/akun/keberatan/baru')->name('ppid.objection');

Route::middleware('auth.pemohon')->group(function () {
    // Cek status permohonan: hasilnya hanya permohonan milik akun yang masuk.
    Route::get('/cek-status', [PpidController::class, 'showStatusCheck'])->name('ppid.status');
    Route::post('/check-status', [PpidController::class, 'checkRequestStatus'])->name('ppid.status.check');

    // Permintaan tautan unduh laporan — formulir, jadi ikut wajib masuk.
    Route::post('/download-report', [PpidController::class, 'sendDownloadLink'])->name('report.download');
});

/*
 * Berkas laporan dari tautan email. Tanda tangan URL-nya yang jadi kunci
 * (berlaku 72 jam), jadi penerima email tidak perlu sesi login lagi.
 */
Route::get('/unduh-laporan/{laporan}', [PpidController::class, 'downloadReportFile'])
    ->middleware('signed')
    ->whereNumber('laporan')
    ->name('report.download.file');

Route::get('/search-suggest', [SearchController::class, 'suggestions']);

/*
 * Penyaji berkas media CMS.
 *
 * Berkas yang diunggah lewat `be-ppid` ditulis ke `storage/app/public` milik
 * project ini, bukan ke document root, jadi tidak pernah dieksekusi web server.
 * Route ini yang membacanya. Kalau `php artisan storage:link` dijalankan,
 * symlink di `public/storage` akan dilayani lebih dulu dan route ini menganggur.
 */
Route::get('/storage/{path}', function (string $path) {
    // Hanya folder unggahan CMS yang boleh dibaca, dan tidak boleh keluar darinya.
    if (!\Illuminate\Support\Str::startsWith($path, 'uploads/') || str_contains($path, '..')) {
        abort(404);
    }

    $disk = \Illuminate\Support\Facades\Storage::disk('public');

    if (!$disk->exists($path)) {
        abort(404);
    }

    return response()->file($disk->path($path), [
        'Cache-Control' => 'public, max-age=86400',
        // Berkas tidak boleh ditafsirkan ulang tipenya oleh browser.
        'X-Content-Type-Options' => 'nosniff',
    ]);
})->where('path', '.*')->name('media.show');

/*
 * Tidak ada login petugas/admin di situs publik ini.
 * Halaman login petugas ada di aplikasi `be-ppid`; route Breeze (`/login`,
 * `/register`, `/dashboard`, `/profile`) beserta controller dan view-nya
 * sudah dihapus supaya panel admin tidak punya pintu masuk dari sini.
 */

// Route akun pengunjung situs publik (guard `pemohon`)
require __DIR__ . '/akun.php';
