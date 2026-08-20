<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;

/**
 * Pendaftar route CRUD standar.
 *
 * Setiap aksi otomatis dipasangi middleware `akses:{modul},{aksi}` sehingga
 * tidak mungkin ada endpoint CMS yang lolos tanpa pemeriksaan hak akses —
 * hak akses tidak perlu ditulis ulang satu per satu di tiap baris route.
 */
class CrudRoute
{
    /**
     * @param  array<int, string>  $kecuali  Aksi yang tidak didaftarkan:
     *                                       `store`, `update`, `destroy`.
     *                                       Modul berisi data kiriman pemohon
     *                                       memakai ini agar jalur ubah/hapus
     *                                       benar-benar tidak ada — bukan
     *                                       sekadar tombolnya disembunyikan di
     *                                       panel.
     */
    public static function register(string $prefix, string $controller, string $modul, array $kecuali = []): void
    {
        $ada = fn (string $aksi) => !in_array($aksi, $kecuali, true);

        Route::get($prefix, [$controller, 'index'])->middleware("akses:{$modul},view");
        Route::get($prefix.'/{id}', [$controller, 'show'])->middleware("akses:{$modul},view")->whereNumber('id');

        if ($ada('store')) {
            Route::post($prefix, [$controller, 'store'])->middleware("akses:{$modul},create");
        }

        if ($ada('update')) {
            Route::put($prefix.'/{id}', [$controller, 'update'])->middleware("akses:{$modul},edit")->whereNumber('id');
            Route::patch($prefix.'/{id}', [$controller, 'update'])->middleware("akses:{$modul},edit")->whereNumber('id');
        }

        if ($ada('destroy')) {
            Route::delete($prefix.'/{id}', [$controller, 'destroy'])->middleware("akses:{$modul},delete")->whereNumber('id');
            Route::post($prefix.'/hapus-massal', [$controller, 'destroyMany'])->middleware("akses:{$modul},delete");
        }
    }
}
