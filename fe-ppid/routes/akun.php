<?php

use App\Http\Controllers\Akun\DashboardController;
use App\Http\Controllers\Akun\EmailVerificationController;
use App\Http\Controllers\Akun\HistoriController;
use App\Http\Controllers\Akun\KeberatanController;
use App\Http\Controllers\Akun\NotifikasiController;
use App\Http\Controllers\Akun\PasswordResetController;
use App\Http\Controllers\Akun\PengaturanController;
use App\Http\Controllers\Akun\PermohonanController;
use App\Http\Controllers\Akun\RegisterController;
use App\Http\Controllers\Akun\SessionController;
use App\Http\Controllers\Akun\SurveiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Portal Pengguna (guard `pemohon`)
|--------------------------------------------------------------------------
|
| Terpisah penuh dari akun petugas: guard, tabel akun, tabel token reset, dan
| halamannya berbeda. Login petugas tidak ada di aplikasi ini sama sekali.
|
| Email wajib terverifikasi sebelum akun bisa dipakai masuk, jadi halaman
| verifikasi berada di luar grup `auth.pemohon` — pengguna yang belum masuk
| tetap harus bisa meminta tautan baru.
|
*/

Route::prefix('akun')->name('akun.')->group(function () {

    Route::middleware('guest.pemohon')->group(function () {
        Route::get('/masuk', [SessionController::class, 'create'])->name('login');
        Route::post('/masuk', [SessionController::class, 'store'])
            ->middleware('throttle:20,1')
            ->name('login.store');

        Route::get('/daftar', [RegisterController::class, 'create'])->name('register');
        Route::post('/daftar', [RegisterController::class, 'store'])
            ->middleware('throttle:10,1')
            ->name('register.store');

        Route::get('/lupa-password', [PasswordResetController::class, 'request'])->name('password.request');
        Route::post('/lupa-password', [PasswordResetController::class, 'email'])
            ->middleware('throttle:6,1')
            ->name('password.email');

        Route::get('/reset-password/{token}', [PasswordResetController::class, 'reset'])->name('password.reset');
        Route::post('/reset-password', [PasswordResetController::class, 'update'])
            ->middleware('throttle:6,1')
            ->name('password.update');
    });

    // Verifikasi email — dapat diakses sebelum maupun sesudah masuk.
    Route::get('/verifikasi', [EmailVerificationController::class, 'notice'])->name('verifikasi.notice');
    Route::get('/verifikasi/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->whereNumber('id')
        ->name('verifikasi.verify');
    Route::post('/verifikasi/kirim-ulang', [EmailVerificationController::class, 'send'])
        ->middleware('throttle:6,1')
        ->name('verifikasi.send');

    Route::middleware('auth.pemohon')->group(function () {
        Route::post('/keluar', [SessionController::class, 'destroy'])->name('logout');

        Route::get('/', DashboardController::class)->name('dashboard');
        Route::get('/histori', HistoriController::class)->name('histori');

        // --- Lonceng notifikasi ---
        // `daftar` dipanggil berkala oleh lonceng di header; sisanya menandai
        // sudah dibaca. Halaman penuhnya tetap ada untuk riwayat lama.
        Route::get('/notifikasi', [NotifikasiController::class, 'halaman'])->name('notifikasi');
        Route::post('/notifikasi/baca-semua', [NotifikasiController::class, 'bacaSemuaHalaman'])->name('notifikasi.baca-semua');
        Route::get('/notifikasi/daftar', [NotifikasiController::class, 'index'])->name('notifikasi.daftar');
        Route::get('/notifikasi/{notifikasi}/buka', [NotifikasiController::class, 'buka'])
            ->whereNumber('notifikasi')
            ->name('notifikasi.buka');
        Route::post('/notifikasi/{notifikasi}/baca', [NotifikasiController::class, 'baca'])
            ->whereNumber('notifikasi')
            ->name('notifikasi.baca');
        Route::post('/notifikasi/tandai-semua', [NotifikasiController::class, 'bacaSemua'])->name('notifikasi.tandai-semua');

        // --- Permohonan Informasi ---
        Route::get('/permohonan', [PermohonanController::class, 'index'])->name('permohonan.index');
        Route::get('/permohonan/baru', [PermohonanController::class, 'create'])->name('permohonan.create');
        Route::post('/permohonan', [PermohonanController::class, 'store'])->name('permohonan.store');
        Route::get('/permohonan/{permohonan}', [PermohonanController::class, 'show'])
            ->whereNumber('permohonan')
            ->name('permohonan.show');

        // --- Permohonan Keberatan ---
        Route::get('/keberatan', [KeberatanController::class, 'index'])->name('keberatan.index');
        Route::get('/keberatan/baru', [KeberatanController::class, 'create'])->name('keberatan.create');
        Route::post('/keberatan', [KeberatanController::class, 'store'])->name('keberatan.store');
        Route::get('/keberatan/berkas/{berkas}', [KeberatanController::class, 'berkas'])
            ->whereNumber('berkas')
            ->name('keberatan.berkas');

        // --- Pengaturan ---
        Route::get('/pengaturan/profil', [PengaturanController::class, 'profil'])->name('profil');
        Route::put('/pengaturan/profil', [PengaturanController::class, 'perbaruiProfil'])->name('profil.update');
        Route::get('/pengaturan/data-pemohon', [PengaturanController::class, 'dataPemohon'])->name('data-pemohon');
        Route::put('/pengaturan/data-pemohon', [PengaturanController::class, 'simpanDataPemohon'])->name('data-pemohon.update');
        Route::get('/pengaturan/data-pemohon/ktp', [PengaturanController::class, 'berkasKtp'])->name('data-pemohon.ktp');
        Route::get('/pengaturan/password', [PengaturanController::class, 'password'])->name('password.form');
        Route::put('/pengaturan/password', [PengaturanController::class, 'perbaruiPassword'])->name('password.change');

        // Survei kepuasan: hanya untuk permohonan milik akun ini yang sudah tuntas.
        Route::get('/survei/{permohonan}', [SurveiController::class, 'create'])
            ->whereNumber('permohonan')
            ->name('survei.create');
        Route::post('/survei/{permohonan}', [SurveiController::class, 'store'])
            ->whereNumber('permohonan')
            ->name('survei.store');
    });
});
