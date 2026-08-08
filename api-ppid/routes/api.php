<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Cms\AuditLogController;
use App\Http\Controllers\Api\Cms\BannerSliderController;
use App\Http\Controllers\Api\Cms\BeritaController;
use App\Http\Controllers\Api\Cms\FaqController;
use App\Http\Controllers\Api\Cms\GaleriController;
use App\Http\Controllers\Api\Cms\HalamanStatisController;
use App\Http\Controllers\Api\Cms\InformasiDikecualikanController;
use App\Http\Controllers\Api\Cms\InformasiPublikController;
use App\Http\Controllers\Api\Cms\KategoriBeritaController;
use App\Http\Controllers\Api\Cms\KategoriInformasiController;
use App\Http\Controllers\Api\Cms\KeberatanController;
use App\Http\Controllers\Api\Cms\LaporanLayananController;
use App\Http\Controllers\Api\Cms\MenuNavigasiController;
use App\Http\Controllers\Api\Cms\PemohonController;
use App\Http\Controllers\Api\Cms\PengaturanSitusController;
use App\Http\Controllers\Api\Cms\PenggunaController;
use App\Http\Controllers\Api\Cms\PermohonanController;
use App\Http\Controllers\Api\Cms\RegulasiController;
use App\Http\Controllers\Api\Cms\RoleController;
use App\Http\Controllers\Api\Cms\StrukturOrganisasiController;
use App\Http\Controllers\Api\Cms\TautanTerkaitController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\NavigationController;
use App\Http\Controllers\Api\NotifikasiController;
use App\Http\Controllers\Api\UploadController;
use App\Support\CrudRoute;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API PPID
|--------------------------------------------------------------------------
|
| Semua endpoint diberi prefix versi (/api/v1) supaya perubahan kontrak di
| masa depan bisa berjalan berdampingan tanpa memutus klien lama.
|
| Endpoint CMS didaftarkan lewat CrudRoute::register() yang otomatis memasang
| middleware hak akses per modul, jadi tidak ada endpoint yang lolos tanpa
| pemeriksaan role.
|
*/

Route::prefix('v1')->group(function () {
    Route::get('health', fn () => response()->json([
        'status' => 'ok',
        'time' => now()->toIso8601String(),
    ]));

    Route::prefix('auth')->group(function () {
        Route::post('sign-in', [AuthController::class, 'signIn'])->middleware('throttle:login');

        Route::middleware('auth:api')->group(function () {
            Route::get('sign-in-with-token', [AuthController::class, 'signInWithToken']);
            Route::post('refresh', [AuthController::class, 'refresh']);
            Route::post('sign-out', [AuthController::class, 'signOut']);
            Route::put('user/{id}', [AuthController::class, 'updateUser']);
        });
    });

    Route::middleware('auth:api')->group(function () {
        // --- Umum ---
        Route::get('me/navigation', [NavigationController::class, 'index']);
        Route::get('dashboard/ringkasan', [DashboardController::class, 'ringkasan'])
            ->middleware('akses:dashboard,view');

        // Notifikasi pribadi pengguna; tidak terikat modul mana pun.
        Route::get('notifikasi', [NotifikasiController::class, 'index']);
        Route::get('notifikasi/{id}', [NotifikasiController::class, 'show'])->whereNumber('id');
        Route::delete('notifikasi/{id}', [NotifikasiController::class, 'destroy'])->whereNumber('id');
        Route::delete('notifikasi', [NotifikasiController::class, 'destroyMany']);

        Route::post('uploads', [UploadController::class, 'store'])->middleware('throttle:uploads');
        Route::delete('uploads', [UploadController::class, 'destroy'])->middleware('throttle:uploads');

        // --- Informasi publik ---
        CrudRoute::register('kategori-informasi', KategoriInformasiController::class, 'kategori-informasi');
        CrudRoute::register('informasi-publik', InformasiPublikController::class, 'informasi-publik');
        CrudRoute::register('informasi-dikecualikan', InformasiDikecualikanController::class, 'informasi-dikecualikan');

        // --- Layanan permohonan ---
        // Route khusus didaftarkan sebelum CrudRoute agar tidak tertangkap pola /{id}.
        Route::post('permohonan/{id}/status', [PermohonanController::class, 'ubahStatus'])
            ->middleware('akses:permohonan,edit')->whereNumber('id');
        Route::post('permohonan/{id}/approval', [PermohonanController::class, 'putusanApproval'])
            ->middleware('akses:permohonan,approve')->whereNumber('id');
        Route::post('permohonan/{id}/tanggapan-files', [PermohonanController::class, 'tambahTanggapanFile'])
            ->middleware('akses:permohonan,edit')->whereNumber('id');
        Route::delete('permohonan/{id}/tanggapan-files/{fileId}', [PermohonanController::class, 'hapusTanggapanFile'])
            ->middleware('akses:permohonan,edit')->whereNumber('id')->whereNumber('fileId');
        CrudRoute::register('permohonan', PermohonanController::class, 'permohonan');
        CrudRoute::register('pemohon', PemohonController::class, 'permohonan');
        CrudRoute::register('keberatan', KeberatanController::class, 'keberatan');

        // --- Laporan ---
        Route::get('laporan-layanan/rekap', [LaporanLayananController::class, 'rekap'])
            ->middleware('akses:laporan-layanan,view');
        CrudRoute::register('laporan-layanan', LaporanLayananController::class, 'laporan-layanan');

        // --- Konten situs ---
        CrudRoute::register('berita', BeritaController::class, 'berita');
        CrudRoute::register('kategori-berita', KategoriBeritaController::class, 'berita');
        CrudRoute::register('galeri', GaleriController::class, 'galeri');
        CrudRoute::register('faq', FaqController::class, 'faq');
        CrudRoute::register('banner-slider', BannerSliderController::class, 'banner-slider');
        CrudRoute::register('struktur-organisasi', StrukturOrganisasiController::class, 'struktur-organisasi');
        CrudRoute::register('halaman-statis', HalamanStatisController::class, 'halaman-statis');
        CrudRoute::register('regulasi', RegulasiController::class, 'regulasi');
        CrudRoute::register('tautan-terkait', TautanTerkaitController::class, 'tautan-terkait');
        CrudRoute::register('menu-navigasi', MenuNavigasiController::class, 'menu-navigasi');

        // --- Administrasi sistem ---
        Route::get('role/{id}/akses', [RoleController::class, 'akses'])
            ->middleware('akses:pengguna,view')->whereNumber('id');
        Route::put('role/{id}/akses', [RoleController::class, 'simpanAkses'])
            ->middleware('akses:pengguna,edit')->whereNumber('id');
        CrudRoute::register('pengguna', PenggunaController::class, 'pengguna');
        CrudRoute::register('role', RoleController::class, 'pengguna');
        Route::post('pengaturan-situs/massal', [PengaturanSitusController::class, 'simpanMassal'])
            ->middleware('akses:pengaturan-situs,edit');
        CrudRoute::register('pengaturan-situs', PengaturanSitusController::class, 'pengaturan-situs');
        CrudRoute::register('audit-log', AuditLogController::class, 'audit-log');
    });
});
