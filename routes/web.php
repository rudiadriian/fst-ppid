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

Route::get('/search', function (Request $request) {
    return view('ppid.search_results', ['query' => $request->input('query')]);
})->name('search');


Route::get('/', function () {
    return view('ppid.home');
});

Route::get('/profile/{slug}', [PpidController::class, 'showProfilePage'])->name('ppid.profile');
Route::get('/informasi/{slug}', [PpidController::class, 'showPublicInformation'])->name('ppid.information');
Route::get('/regulasi', [PpidController::class, 'showRegulationPage'])->name('ppid.regulation');
Route::get('/standar-layanan/{slug}', [PpidController::class, 'showServiceStandardPage'])->name('ppid.service');

Route::get('/permohonan', [PpidController::class, 'showRequestForm'])->name('ppid.request');
Route::post('/submit-request', [PpidController::class, 'submitRequest'])->name('ppid.request.submit');

Route::get('/keberatan', [PpidController::class, 'showObjectionForm'])->name('ppid.objection');
Route::post('/submit-objection', [PpidController::class, 'submitObjection'])->name('ppid.objection.submit');

Route::get('/cek-status', [PpidController::class, 'showStatusCheck'])->name('ppid.status');
Route::post('/check-status', [PpidController::class, 'checkRequestStatus'])->name('ppid.status.check');


Route::get('/search-suggest', [SearchController::class, 'suggestions']);
Route::post('/download-report', [PpidController::class, 'sendDownloadLink'])->name('report.download');
