<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\KontenController;
use App\Http\Controllers\PpidController;
use App\Http\Controllers\ProfileController;
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


Route::get('/', [HomeController::class, 'index'])->name('ppid.home');

// Kanal konten yang seluruhnya dikelola dari CMS `be-ppid`.
Route::get('/berita', [KontenController::class, 'beritaIndex'])->name('ppid.news.index');
Route::get('/berita/{slug}', [KontenController::class, 'beritaShow'])->name('ppid.news.show');
Route::get('/faq', [KontenController::class, 'faq'])->name('ppid.faq');
Route::get('/struktur-ppid', [KontenController::class, 'struktur'])->name('ppid.structure');

// Ganti bahasa (relatif, origin-agnostic) — simpan ke session lalu kembali
Route::get('/set-locale/{locale}', function (string $locale) {
    if (in_array($locale, ['id', 'en'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('locale.set');

// Kanal Profil: Dasar Hukum didaftarkan sebelum /profile/{slug} agar tidak tertangkap wildcard.
Route::get('/profile/dasar-hukum', [PpidController::class, 'showLegalBasisPage'])->name('ppid.legal_basis');
Route::get('/profile/{slug}', [PpidController::class, 'showProfilePage'])->name('ppid.profile');

// Kanal Informasi Publik: Daftar Informasi Dikecualikan sebelum /informasi/{slug}.
Route::get('/informasi/dikecualikan', [PpidController::class, 'showExcludedInformation'])->name('ppid.excluded');
Route::get('/informasi/{slug}', [PpidController::class, 'showPublicInformation'])->name('ppid.information');

Route::get('/regulasi', [PpidController::class, 'showRegulationPage'])->name('ppid.regulation');
Route::get('/laporan/{slug}', [PpidController::class, 'showReportPage'])->name('ppid.report');
Route::get('/register-permohonan', [PpidController::class, 'showRequestRegister'])->name('ppid.register');
Route::get('/standar-layanan/{slug}', [PpidController::class, 'showServiceStandardPage'])->name('ppid.service');

Route::get('/permohonan', [PpidController::class, 'showRequestForm'])->name('ppid.request');
Route::post('/submit-request', [PpidController::class, 'submitRequest'])->name('ppid.request.submit');

Route::get('/keberatan', [PpidController::class, 'showObjectionForm'])->name('ppid.objection');
Route::post('/submit-objection', [PpidController::class, 'submitObjection'])->name('ppid.objection.submit');

Route::get('/cek-status', [PpidController::class, 'showStatusCheck'])->name('ppid.status');
Route::post('/check-status', [PpidController::class, 'checkRequestStatus'])->name('ppid.status.check');


Route::get('/search-suggest', [SearchController::class, 'suggestions']);
Route::post('/download-report', [PpidController::class, 'sendDownloadLink'])->name('report.download');

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

// --- Backend / Web Admin (auth) ---
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

// Route autentikasi Breeze (login/register/logout/reset password)
require __DIR__ . '/auth.php';
