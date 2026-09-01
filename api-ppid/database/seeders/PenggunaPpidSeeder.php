<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\StrukturOrganisasi;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Akun panel untuk jabatan-jabatan pada bagan struktur organisasi PPID.
 *
 * Alur permohonan berjenjang tidak bisa berjalan tanpa pemegang jabatannya:
 * tahap yang menunggu role `ppid-pelaksana` akan menggantung selamanya kalau
 * tidak ada satu pun akun berrole itu. Seeder ini mengisi empat jabatan yang
 * disebut pada langkah 89 — tiga anggota PPID Pelaksana dan satu PPID.
 *
 * Ketiga anggota PPID Pelaksana berbagi satu role. Yang membedakan mereka
 * jabatannya di bagan (`struktur_id`), bukan hak aksesnya: ketiganya memang
 * boleh melakukan hal yang sama terhadap permohonan yang masuk, dan memecahnya
 * menjadi tiga role hanya menyalin matrix hak akses yang sama tiga kali.
 *
 * Kata sandinya sengaja seragam dan dapat ditebak — akun ini untuk pengujian
 * alur, bukan untuk dipakai di lingkungan sungguhan. Ganti sebelum dipakai
 * melayani permohonan nyata, atau nonaktifkan akunnya.
 *
 * Idempoten: dicocokkan lewat email, akun yang sudah ada hanya diperbarui
 * role dan jabatannya — kata sandinya tidak ditimpa.
 *
 *   php artisan db:seed --class=PenggunaPpidSeeder
 */
class PenggunaPpidSeeder extends Seeder
{
    private const SANDI_AWAL = 'PpidFood#2026';

    /**
     * email => [nama, role slug, jabatan pada bagan, telepon]
     *
     * "Jabatan pada bagan" dicocokkan ke kolom `jabatan` di
     * `struktur_organisasi`, bukan ke `nama`: kolom itulah yang dipakai alur
     * persetujuan untuk menunjuk tahap.
     */
    private const AKUN = [
        'ppid@foodstation.co.id' => [
            'PPID — Sekretaris Perusahaan & Kepatuhan',
            'ppid-utama',
            'PPID',
            '(021) 4718011',
        ],
        'dokumentasi.ppid@foodstation.co.id' => [
            'Pengelolaan & Dokumentasi Informasi — Kepala Seksi Humas',
            'ppid-pelaksana',
            'Pengelolaan & Dokumentasi Informasi',
            null,
        ],
        'pengumuman.ppid@foodstation.co.id' => [
            'Pengumuman Informasi — Staf Sekretaris Perusahaan & Kepatuhan',
            'ppid-pelaksana',
            'Pengumuman Informasi',
            null,
        ],
        'penyediaan.ppid@foodstation.co.id' => [
            'Penyediaan Informasi — Staf Seksi Humas',
            'ppid-pelaksana',
            'Penyediaan Informasi',
            null,
        ],
    ];

    public function run(): void
    {
        $dibuat = 0;
        $diperbarui = 0;

        foreach (self::AKUN as $email => [$nama, $roleSlug, $jabatan, $telepon]) {
            $roleId = Role::where('slug', $roleSlug)->value('id');

            if (!$roleId) {
                $this->command?->warn("Role \"$roleSlug\" belum ada — jalankan ModulSistemSeeder lebih dulu. Akun $email dilewati.");

                continue;
            }

            $strukturId = StrukturOrganisasi::where('jabatan', $jabatan)->value('id');

            if (!$strukturId) {
                $this->command?->warn("Jabatan \"$jabatan\" tidak ada di bagan — akun $email tetap dibuat tanpa jabatan.");
            }

            $akun = User::withTrashed()->where('email', $email)->first();

            if ($akun) {
                // Kata sandi tidak ditimpa: akun yang sudah dipakai orang tidak
                // boleh terlempar keluar hanya karena seeder dijalankan lagi.
                $akun->fill([
                    'name' => $nama,
                    'role_id' => $roleId,
                    'struktur_id' => $strukturId,
                    'is_active' => true,
                ]);
                $akun->deleted_at = null;
                $akun->save();

                $diperbarui++;

                continue;
            }

            User::create([
                'name' => $nama,
                'email' => $email,
                'password' => Hash::make(self::SANDI_AWAL),
                'role_id' => $roleId,
                'struktur_id' => $strukturId,
                'phone' => $telepon,
                'is_active' => true,
            ]);

            $dibuat++;
        }

        $this->command?->info("Akun PPID: $dibuat dibuat, $diperbarui diperbarui.");

        if ($dibuat > 0) {
            $this->command?->warn('Kata sandi awal seluruh akun baru: '.self::SANDI_AWAL.' — ganti sebelum dipakai melayani permohonan nyata.');
        }
    }
}
