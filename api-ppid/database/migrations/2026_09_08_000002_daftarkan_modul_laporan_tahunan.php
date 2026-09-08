<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Daftarkan modul `laporan-tahunan` beserta hak akses awalnya.
 *
 * `ModulSistemSeeder` sudah memuat modul ini, tetapi pipeline deploy hanya
 * menjalankan `artisan migrate` — seeder tidak pernah ikut jalan di produksi.
 * Tanpa barisnya, middleware `akses:laporan-tahunan,view` menolak seluruh
 * permintaan dan modulnya hilang dari menu meski halamannya sudah ada.
 *
 * Sengaja hanya menyentuh modul ini. Menjalankan seluruh seeder di setiap
 * deploy akan mengembalikan matrix hak akses seluruh modul ke nilai bawaan —
 * menghapus penyesuaian yang dilakukan petugas lewat panel.
 *
 * Hak akses yang sudah ada tidak ditimpa: baris hanya dibuat bila role itu
 * belum punya baris untuk modul ini, sehingga migrasi aman diulang.
 */
return new class extends Migration
{
    private const SLUG = 'laporan-tahunan';

    /**
     * Hak awal per role, mengikuti pola modul konten situs lain pada
     * `ModulSistemSeeder`: super admin penuh, pelaksana menyiapkan, PPID Utama
     * menyetujui, Atasan PPID tidak berurusan dengan konten situs.
     */
    private const AKSES = [
        'super-admin' => [true, true, true, true, true, true],
        'ppid-pelaksana' => [true, true, true, false, false, true],
        'ppid-utama' => [true, false, true, false, true, true],
        'atasan-ppid' => [false, false, false, false, false, false],
    ];

    public function up(): void
    {
        $modulId = DB::table('modul_sistem')->where('slug', self::SLUG)->value('id');

        if ($modulId === null) {
            $modulId = DB::table('modul_sistem')->insertGetId([
                'slug' => self::SLUG,
                'nama' => 'Laporan Tahunan',
                'icon' => 'heroicons-outline:book-open',
                'route' => '/ppid/laporan-tahunan',
                'urutan' => 11,
                'is_active' => true,
            ]);
        }

        foreach (self::AKSES as $roleSlug => [$view, $create, $edit, $delete, $approve, $export]) {
            $roleId = DB::table('roles')->where('slug', $roleSlug)->value('id');

            if ($roleId === null) {
                continue;
            }

            $sudahAda = DB::table('role_modul_akses')
                ->where('role_id', $roleId)
                ->where('modul_id', $modulId)
                ->exists();

            if ($sudahAda) {
                continue;
            }

            DB::table('role_modul_akses')->insert([
                'role_id' => $roleId,
                'modul_id' => $modulId,
                'can_view' => $view,
                'can_create' => $create,
                'can_edit' => $edit,
                'can_delete' => $delete,
                'can_approve' => $approve,
                'can_export' => $export,
            ]);
        }
    }

    /**
     * Barisnya dinonaktifkan, bukan dihapus.
     *
     * Menghapus modul akan menyeret seluruh hak akses yang menempel padanya
     * (`cascadeOnDelete`) — termasuk penyesuaian yang dibuat petugas.
     */
    public function down(): void
    {
        DB::table('modul_sistem')->where('slug', self::SLUG)->update(['is_active' => false]);
    }
};
