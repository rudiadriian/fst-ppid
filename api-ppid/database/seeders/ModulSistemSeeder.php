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
        'keberatan' => ['Keberatan Informasi', 'heroicons-outline:exclamation-triangle', '/ppid/keberatan', 6],
        'laporan-layanan' => ['Laporan Layanan', 'heroicons-outline:chart-bar', '/ppid/laporan-layanan', 7],
        // Berkas yang dipakai berulang untuk menjawab permohonan (langkah 95).
        'arsip-dokumen' => ['Arsip Dokumen', 'heroicons-outline:folder-open', '/ppid/arsip-dokumen', 8],
        'berita' => ['Berita', 'heroicons-outline:newspaper', '/ppid/berita', 8],
        'galeri' => ['Galeri', 'heroicons-outline:photo', '/ppid/galeri', 9],
        'faq' => ['FAQ', 'heroicons-outline:question-mark-circle', '/ppid/faq', 10],
        'banner-slider' => ['Banner Slider', 'heroicons-outline:presentation-chart-line', '/ppid/banner-slider', 11],
        'struktur-organisasi' => ['Struktur Organisasi', 'heroicons-outline:users', '/ppid/struktur-organisasi', 12],
        'halaman-statis' => ['Halaman Statis', 'heroicons-outline:template', '/ppid/halaman-statis', 13],
        'regulasi' => ['Regulasi & Dasar Hukum', 'heroicons-outline:scale', '/ppid/regulasi', 14],
        'menu-navigasi' => ['Menu Navigasi', 'heroicons-outline:menu', '/ppid/menu-navigasi', 16],
        'alur-approval' => ['Alur Persetujuan', 'heroicons-outline:adjustments-horizontal', '/ppid/alur-approval', 16],
        'pengguna' => ['Pengguna & Role', 'heroicons-outline:user-group', '/ppid/pengguna', 17],
        'pengaturan-situs' => ['Pengaturan Situs', 'heroicons-outline:cog', '/ppid/pengaturan-situs', 18],
        'audit-log' => ['Audit Log', 'heroicons-outline:clipboard-list', '/ppid/audit-log', 19],
    ];

    /**
     * Modul yang sudah dilepas dari panel. Barisnya tidak dihapus supaya data
     * dan hak akses lamanya tetap tersimpan, tetapi dinonaktifkan sehingga
     * hilang dari menu, dari matrix hak akses role, dan ditolak middleware.
     */
    private const NONAKTIF = [
        'tautan-terkait',
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

        ModulSistem::whereIn('slug', self::NONAKTIF)->update(['is_active' => false]);

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

        // Jenjang teratas pada bagan struktur organisasi PPID. Perlu punya role
        // sendiri sejak alur persetujuan berjenjang dipakai: tanpa itu tahap
        // "Atasan PPID" tidak punya pemegang dan berkasnya berhenti di sana.
        $atasanPpid = Role::firstOrCreate(
            ['slug' => 'atasan-ppid'],
            ['name' => 'Atasan PPID', 'description' => 'Pengesahan akhir permohonan dan putusan keberatan']
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

            // Susunan alur persetujuan hanya boleh diubah super admin: ia yang
            // menentukan siapa menyetujui siapa, jadi role yang berada di dalam
            // alur itu tidak boleh bisa menyusun ulang jenjangnya sendiri.
            // Keduanya tetap boleh melihat susunannya — jenjang yang berjalan
            // harus bisa dibaca oleh yang menjalaninya.
            $hanyaLihat = ['can_view' => true, 'can_create' => false, 'can_edit' => false, 'can_delete' => false, 'can_approve' => false, 'can_export' => false];
            $tanpaAkses = ['can_view' => false, 'can_create' => false, 'can_edit' => false, 'can_delete' => false, 'can_approve' => false, 'can_export' => false];

            /*
             * Layanan menuntut hak `approve` meski jenjang ini tidak boleh
             * menolak.
             *
             * PPID Pelaksana memegang tahap pertama alur — menerima permohonan
             * dan keberatan, lalu meneruskannya ke PPID — dan memutuskan hasil
             * Verifikasi Data Diri Pemohon. Ketiga jalurnya dijaga
             * `akses:permohonan,approve` / `akses:keberatan,approve`, jadi tanpa
             * hak ini tahap pertamanya menggantung: notifikasinya sampai, tetapi
             * tombol putusannya ditolak 403.
             *
             * Hak menolak tidak ikut terbuka karenanya — itu ditentukan
             * `boleh_tolak` pada tahap alurnya, yang ditegakkan terpisah di
             * `MenanganiPersetujuan`.
             */
            $aksesLayanan = ['can_view' => true, 'can_create' => false, 'can_edit' => true, 'can_delete' => false, 'can_approve' => true, 'can_export' => true];

            $aksesPelaksana = match (true) {
                in_array($modul->slug, ['pengguna', 'audit-log', 'pengaturan-situs'], true) => $tanpaAkses,
                $modul->slug === 'alur-approval' => $hanyaLihat,
                in_array($modul->slug, ['permohonan', 'keberatan'], true) => $aksesLayanan,
                default => $operasional,
            };

            $this->setAkses($ppidPelaksana->id, $modul->id, $aksesPelaksana);

            /*
             * Arsip Dokumen tidak mengenal "menyetujui": isinya berkas milik
             * lembaga, bukan pengajuan yang menunggu putusan. Yang diperlukan
             * di sana justru menambah dan membuang — dan membuang hanya boleh
             * dilakukan PPID, karena baris yang hilang membuat dokumen itu tidak
             * lagi bisa dipilih petugas lain.
             */
            $arsipPenuh = ['can_view' => true, 'can_create' => true, 'can_edit' => true, 'can_delete' => true, 'can_approve' => false, 'can_export' => true];

            $aksesUtama = match ($modul->slug) {
                'pengguna', 'alur-approval' => $hanyaLihat,
                'arsip-dokumen' => $arsipPenuh,
                default => $persetujuan,
            };

            $this->setAkses($ppidUtama->id, $modul->id, $aksesUtama);

            // Atasan PPID hanya berurusan dengan layanan: ia mengesahkan
            // permohonan dan memutus keberatan, bukan menyunting konten situs.
            $aksesAtasan = match ($modul->slug) {
                'dashboard', 'permohonan', 'keberatan', 'laporan-layanan' => $persetujuan,
                'alur-approval' => $hanyaLihat,
                default => $tanpaAkses,
            };

            $this->setAkses($atasanPpid->id, $modul->id, $aksesAtasan);
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
