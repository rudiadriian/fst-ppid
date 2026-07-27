<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API PPID
|--------------------------------------------------------------------------
|
| Semua endpoint diberi prefix versi (/api/v1) supaya perubahan kontrak di
| masa depan bisa berjalan berdampingan tanpa memutus klien lama.
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
});
