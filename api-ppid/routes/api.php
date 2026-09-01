<?php

use App\Http\Controllers\Api\AnalitikController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CaptchaController;
use App\Http\Controllers\Api\Cms\AlurApprovalController;
use App\Http\Controllers\Api\Cms\AlurProsedurController;
use App\Http\Controllers\Api\Cms\ArsipDokumenController;
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
use App\Http\Controllers\Api\Cms\MaklumatController;
use App\Http\Controllers\Api\Cms\MenuNavigasiController;
use App\Http\Controllers\Api\Cms\ModulSistemController;
use App\Http\Controllers\Api\Cms\PemohonController;
use App\Http\Controllers\Api\Cms\PengajuanLayananController;
use App\Http\Controllers\Api\Cms\PengaturanSitusController;
use App\Http\Controllers\Api\Cms\PenggunaController;
use App\Http\Controllers\Api\Cms\PermohonanController;
use App\Http\Controllers\Api\Cms\RegulasiController;
use App\Http\Controllers\Api\Cms\RoleController;
use App\Http\Controllers\Api\Cms\StrukturOrganisasiController;
use App\Http\Controllers\Api\Cms\SurveyKepuasanController;
use App\Http\Controllers\Api\Cms\TautanTerkaitController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\NavigationController;
use App\Http\Controllers\Api\NotifikasiController;
use App\Http\Controllers\Api\PasswordResetController;
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

        // Gambar captcha untuk formulir masuk, lupa password, dan password
        // baru. Terbuka tanpa token — belum ada yang bisa masuk tanpanya —
        // tetapi tetap direm supaya tidak dipakai memaksa server menggambar
        // ribuan PNG.
        Route::get('captcha', CaptchaController::class)->middleware('throttle:captcha');

        Route::post('lupa-password', [PasswordResetController::class, 'minta'])
            ->middleware('throttle:tautan-akun');
        Route::post('reset-password', [PasswordResetController::class, 'pasang'])
            ->middleware('throttle:tautan-akun');

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
        // Ringkasan, analisa, kepatuhan SLA, dan capaian KPI.
        Route::get('dashboard/analitik', [AnalitikController::class, 'index'])
            ->middleware('akses:dashboard,view');

        // Notifikasi pribadi pengguna; tidak terikat modul mana pun.
        Route::get('notifikasi', [NotifikasiController::class, 'index']);
        // Didaftarkan sebelum pola `/{id}` supaya tidak tertangkap olehnya.
        Route::post('notifikasi/baca-semua', [NotifikasiController::class, 'bacaSemua']);
        Route::post('notifikasi/{id}/baca', [NotifikasiController::class, 'baca'])->whereNumber('id');
        Route::get('notifikasi/{id}', [NotifikasiController::class, 'show'])->whereNumber('id');
        Route::delete('notifikasi/{id}', [NotifikasiController::class, 'destroy'])->whereNumber('id');
        Route::delete('notifikasi', [NotifikasiController::class, 'destroyMany']);

        Route::post('uploads', [UploadController::class, 'store'])->middleware('throttle:uploads');
        Route::delete('uploads', [UploadController::class, 'destroy'])->middleware('throttle:uploads');

        // --- Informasi publik ---
        CrudRoute::register('kategori-informasi', KategoriInformasiController::class, 'kategori-informasi');
        CrudRoute::register('informasi-publik', InformasiPublikController::class, 'informasi-publik');
        CrudRoute::register('informasi-dikecualikan', InformasiDikecualikanController::class, 'informasi-dikecualikan');

        // Daftar gabungan permohonan + keberatan (langkah 89). Hanya baca;
        // hak aksesnya menumpang modul Permohonan karena isinya berkas yang
        // sama, hanya ditampilkan dalam satu daftar.
        Route::get('pengajuan', [PengajuanLayananController::class, 'index'])
            ->middleware('akses:permohonan,view');

        // --- Layanan permohonan ---
        // Route khusus didaftarkan sebelum CrudRoute agar tidak tertangkap pola /{id}.
        Route::post('permohonan/{id}/status', [PermohonanController::class, 'ubahStatus'])
            ->middleware('akses:permohonan,edit')->whereNumber('id');
        // Jenjang persetujuan satu permohonan: `view` cukup untuk melihat
        // susunannya, memutuskan menuntut `approve`.
        Route::get('permohonan/{id}/approval', [PermohonanController::class, 'daftarPersetujuan'])
            ->middleware('akses:permohonan,view')->whereNumber('id');
        Route::post('permohonan/{id}/approval', [PermohonanController::class, 'putuskanPersetujuan'])
            ->middleware('akses:permohonan,approve')->whereNumber('id');
        // Perpanjangan tenggat menggeser janji resmi kepada pemohon, jadi
        // dijaga hak `approve` — sama dengan putusan persetujuan, bukan `edit`
        // yang dipegang setiap petugas pelaksana.
        Route::post('permohonan/{id}/perpanjang', [PermohonanController::class, 'perpanjangTenggat'])
            ->middleware('akses:permohonan,approve')->whereNumber('id');
        Route::post('permohonan/{id}/tanggapan-files', [PermohonanController::class, 'tambahTanggapanFile'])
            ->middleware('akses:permohonan,edit')->whereNumber('id');
        Route::delete('permohonan/{id}/tanggapan-files/{fileId}', [PermohonanController::class, 'hapusTanggapanFile'])
            ->middleware('akses:permohonan,edit')->whereNumber('id')->whereNumber('fileId');
        /*
         * Tanpa `store`, `update`, dan `destroy`: isi permohonan ditulis
         * pemohon sendiri lewat portal, jadi petugas tidak boleh membuatnya,
         * menyuntingnya, maupun menghapusnya — termasuk mencatatkan permohonan
         * baru atas nama orang lain (langkah 78). Yang tersisa untuk petugas
         * adalah perpindahan status, putusan persetujuan berjenjang, dan berkas
         * tanggapan — semuanya lewat endpoint khusus di atas, yang tercatat di
         * `permohonan_log_status` / `approval_pengajuan` / `audit_log`.
         */
        CrudRoute::register('permohonan', PermohonanController::class, 'permohonan', ['store', 'update', 'destroy']);
        // Pemohon hanya bisa dibaca dari panel. Akunnya dibuat dan disunting
        // sendiri oleh pengunjung lewat portal pemohon, jadi tidak ada jalur
        // tambah/ubah/hapus dari sisi petugas.
        Route::get('pemohon', [PemohonController::class, 'index'])->middleware('akses:permohonan,view');
        Route::get('pemohon/{id}', [PemohonController::class, 'show'])
            ->middleware('akses:permohonan,view')->whereNumber('id');
        // Berkas KTP disajikan di belakang token panel, bukan lewat URL media
        // publik situs — dokumen identitas tidak boleh terbuka tanpa masuk.
        Route::get('pemohon/{id}/berkas-ktp', [PemohonController::class, 'berkasKtp'])
            ->middleware('akses:permohonan,view')->whereNumber('id');
        // Satu-satunya jalur tulis pada modul Pemohon: keputusan Verifikasi Data
        // Diri. Memakai hak `approve`, bukan `edit`, karena yang dilakukan
        // petugas memang menyetujui/menolak berkas, bukan menyunting datanya.
        Route::post('pemohon/{id}/verifikasi', [PemohonController::class, 'verifikasi'])
            ->middleware('akses:permohonan,approve')->whereNumber('id');
        /*
         * Sama seperti permohonan: isinya milik pemohon. Bedanya keberatan
         * tidak punya endpoint status tersendiri, jadi satu-satunya jalur
         * tulis petugas dipisah ke `tanggapan` — hanya menerima `status` dan
         * `tanggapan_atasan_ppid`, tidak bisa menyentuh alasan keberatan.
         */
        Route::post('keberatan/{id}/tanggapan', [KeberatanController::class, 'ubahTanggapan'])
            ->middleware('akses:keberatan,edit')->whereNumber('id');
        Route::get('keberatan/{id}/approval', [KeberatanController::class, 'daftarPersetujuan'])
            ->middleware('akses:keberatan,view')->whereNumber('id');
        Route::post('keberatan/{id}/approval', [KeberatanController::class, 'putuskanPersetujuan'])
            ->middleware('akses:keberatan,approve')->whereNumber('id');
        CrudRoute::register('keberatan', KeberatanController::class, 'keberatan', ['store', 'update', 'destroy']);
        // Penilaian pemohon atas layanan; dibaca panel lewat modul Survei.
        CrudRoute::register('survey-kepuasan', SurveyKepuasanController::class, 'permohonan');

        // --- Laporan ---
        // Endpoint `laporan-layanan/rekap` dihapus pada langkah 68 bersama
        // Laporan Statistik Informasi Publik — angka rekap tahunan tidak punya
        // pemakai lagi, baik di panel maupun di situs publik.
        CrudRoute::register('laporan-layanan', LaporanLayananController::class, 'laporan-layanan');

        // Arsip dokumen petugas: berkas yang dipakai berulang untuk menjawab
        // permohonan, dilampirkan tanpa unggahan ulang (langkah 95).
        CrudRoute::register('arsip-dokumen', ArsipDokumenController::class, 'arsip-dokumen');

        // --- Konten situs ---
        CrudRoute::register('berita', BeritaController::class, 'berita');
        CrudRoute::register('kategori-berita', KategoriBeritaController::class, 'berita');
        CrudRoute::register('galeri', GaleriController::class, 'galeri');
        CrudRoute::register('faq', FaqController::class, 'faq');
        CrudRoute::register('banner-slider', BannerSliderController::class, 'banner-slider');
        CrudRoute::register('struktur-organisasi', StrukturOrganisasiController::class, 'struktur-organisasi');
        CrudRoute::register('halaman-statis', HalamanStatisController::class, 'halaman-statis');
        // Maklumat = halaman Standar Layanan berbentuk unggahan dokumen;
        // hak aksesnya menumpang modul Halaman Statis.
        CrudRoute::register('maklumat', MaklumatController::class, 'halaman-statis');
        // Alur bergambar halaman Standar Layanan; hak aksesnya menumpang modul
        // Halaman Statis dengan alasan yang sama seperti Maklumat.
        CrudRoute::register('alur-prosedur', AlurProsedurController::class, 'halaman-statis');
        CrudRoute::register('regulasi', RegulasiController::class, 'regulasi');
        CrudRoute::register('tautan-terkait', TautanTerkaitController::class, 'tautan-terkait');
        CrudRoute::register('menu-navigasi', MenuNavigasiController::class, 'menu-navigasi');

        // --- Administrasi sistem ---
        /*
         * Alur persetujuan berjenjang. Modulnya sendiri yang menjaga siapa
         * boleh menyusunnya: seeder hanya memberi hak tulis kepada super admin,
         * role lain sebatas melihat susunannya.
         */
        Route::get('alur-approval/{id}/tahap', [AlurApprovalController::class, 'tahap'])
            ->middleware('akses:alur-approval,view')->whereNumber('id');
        Route::put('alur-approval/{id}/tahap', [AlurApprovalController::class, 'simpanTahap'])
            ->middleware('akses:alur-approval,edit')->whereNumber('id');
        CrudRoute::register('alur-approval', AlurApprovalController::class, 'alur-approval');

        Route::get('role/{id}/akses', [RoleController::class, 'akses'])
            ->middleware('akses:pengguna,view')->whereNumber('id');
        Route::put('role/{id}/akses', [RoleController::class, 'simpanAkses'])
            ->middleware('akses:pengguna,edit')->whereNumber('id');
        CrudRoute::register('pengguna', PenggunaController::class, 'pengguna');
        CrudRoute::register('role', RoleController::class, 'pengguna');
        // Modul sistem = dasar matrix hak akses; hak aksesnya ikut modul Pengguna.
        CrudRoute::register('modul-sistem', ModulSistemController::class, 'pengguna');
        Route::post('pengaturan-situs/massal', [PengaturanSitusController::class, 'simpanMassal'])
            ->middleware('akses:pengaturan-situs,edit');
        CrudRoute::register('pengaturan-situs', PengaturanSitusController::class, 'pengaturan-situs');
        CrudRoute::register('audit-log', AuditLogController::class, 'audit-log');
    });
});
