<?php

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


Route::get('/', function () {
    return view('ppid.home');
});

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

// --- Backend / Web Admin (auth) ---
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

// Route autentikasi Breeze (login/register/logout/reset password)
require __DIR__ . '/auth.php';
