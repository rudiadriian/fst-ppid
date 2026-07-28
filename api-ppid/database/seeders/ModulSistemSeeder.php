<?php

namespace Database\Seeders;

use App\Models\ModulSistem;
use App\Models\Role;
use App\Models\RoleModulAkses;
use Illuminate\Database\Seeder;

/**
 * Daftar modul backend + hak akses default per role.
 * Aman dijalankan berulang (pakai updateOrCreate berdasarkan slug).
 */
class ModulSistemSeeder extends Seeder
{
    /**
     * slug => [nama, icon, route, urutan]
     */
    private const MODUL = [
        'dashboard' => ['Dashboard', 'heroicons-outline:home', '/ppid/dashboard', 1],
        'informasi-publik' => ['Informasi Publik', 'heroicons-outline:document-text', '/ppid/informasi-publik', 2],
        'kategori-informasi' => ['Kategori Informasi', 'heroicons-outline:tag', '/ppid/kategori-informasi', 3],
        'informasi-dikecualikan' => ['Informasi Dikecualikan', 'heroicons-outline:lock-closed', '/ppid/informasi-dikecualikan', 4],
        'permohonan' => ['Permohonan Informasi', 'heroicons-outline:inbox', '/ppid/permohonan', 5],
        'keberatan' => ['Keberatan Informasi', 'heroicons-outline:exclamation', '/ppid/keberatan', 6],
        'laporan-layanan' => ['Laporan Layanan', 'heroicons-outline:chart-bar', '/ppid/laporan-layanan', 7],
        'berita' => ['Berita', 'heroicons-outline:newspaper', '/ppid/berita', 8],
        'galeri' => ['Galeri', 'heroicons-outline:photograph', '/ppid/galeri', 9],
        'faq' => ['FAQ', 'heroicons-outline:question-mark-circle', '/ppid/faq', 10],
        'banner-slider' => ['Banner Slider', 'heroicons-outline:presentation-chart-line', '/ppid/banner-slider', 11],
        'struktur-organisasi' => ['Struktur Organisasi', 'heroicons-outline:users', '/ppid/struktur-organisasi', 12],
        'halaman-statis' => ['Halaman Statis', 'heroicons-outline:template', '/ppid/halaman-statis', 13],
        'regulasi' => ['Regulasi & Dasar Hukum', 'heroicons-outline:scale', '/ppid/regulasi', 14],
        'tautan-terkait' => ['Tautan Terkait', 'heroicons-outline:link', '/ppid/tautan-terkait', 15],
        'menu-navigasi' => ['Menu Navigasi', 'heroicons-outline:menu', '/ppid/menu-navigasi', 16],
        'pengguna' => ['Pengguna & Role', 'heroicons-outline:user-group', '/ppid/pengguna', 17],
        'pengaturan-situs' => ['Pengaturan Situs', 'heroicons-outline:cog', '/ppid/pengaturan-situs', 18],
        'audit-log' => ['Audit Log', 'heroicons-outline:clipboard-list', '/ppid/audit-log', 19],
    ];

    public function run(): void
    {
        foreach (self::MODUL as $slug => [$nama, $icon, $route, $urutan]) {
            ModulSistem::updateOrCreate(
                ['slug' => $slug],
                [
                    'nama' => $nama,
                    'icon' => $icon,
                    'route' => $route,
                    'urutan' => $urutan,
                    'is_active' => true,
                ]
            );
        }

        $superAdmin = Role::firstOrCreate(
            ['slug' => 'super-admin'],
            ['name' => 'Super Admin', 'description' => 'Akses penuh ke seluruh modul']
        );

        // Role standar PPID sesuai alur persetujuan berjenjang.
        $ppidPelaksana = Role::firstOrCreate(
            ['slug' => 'ppid-pelaksana'],
            ['name' => 'PPID Pelaksana', 'description' => 'Menyiapkan konten dan menangani permohonan']
        );

        $ppidUtama = Role::firstOrCreate(
            ['slug' => 'ppid-utama'],
            ['name' => 'PPID Utama', 'description' => 'Menyetujui konten dan tanggapan permohonan']
        );

        $penuh = [
            'can_view' => true, 'can_create' => true, 'can_edit' => true,
            'can_delete' => true, 'can_approve' => true, 'can_export' => true,
        ];

        $operasional = [
            'can_view' => true, 'can_create' => true, 'can_edit' => true,
            'can_delete' => false, 'can_approve' => false, 'can_export' => true,
        ];

        $persetujuan = [
            'can_view' => true, 'can_create' => false, 'can_edit' => true,
            'can_delete' => false, 'can_approve' => true, 'can_export' => true,
        ];

        foreach (ModulSistem::all() as $modul) {
            $this->setAkses($superAdmin->id, $modul->id, $penuh);

            $aksesPelaksana = in_array($modul->slug, ['pengguna', 'audit-log', 'pengaturan-situs'], true)
                ? ['can_view' => false, 'can_create' => false, 'can_edit' => false, 'can_delete' => false, 'can_approve' => false, 'can_export' => false]
                : $operasional;

            $this->setAkses($ppidPelaksana->id, $modul->id, $aksesPelaksana);

            $aksesUtama = $modul->slug === 'pengguna'
                ? ['can_view' => true, 'can_create' => false, 'can_edit' => false, 'can_delete' => false, 'can_approve' => false, 'can_export' => false]
                : $persetujuan;

            $this->setAkses($ppidUtama->id, $modul->id, $aksesUtama);
        }
    }

    private function setAkses(int $roleId, int $modulId, array $hak): void
    {
        RoleModulAkses::updateOrCreate(
            ['role_id' => $roleId, 'modul_id' => $modulId],
            $hak
        );
    }
}
