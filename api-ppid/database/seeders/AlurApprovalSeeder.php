<?php

namespace Database\Seeders;

use App\Models\AlurApproval;
use App\Models\AlurApprovalTahap;
use App\Models\Role;
use App\Models\StrukturOrganisasi;
use Illuminate\Database\Seeder;

/**
 * Susunan awal alur persetujuan berjenjang.
 *
 * Isinya mengikuti bagan struktur organisasi PPID
 * ({@see BaganStrukturPpidSeeder}): PPID Pelaksana menyiapkan, PPID menyetujui,
 * Atasan PPID mengesahkan. Untuk keberatan jenjangnya lebih pendek dan
 * berhenti di Atasan PPID, sesuai UU Keterbukaan Informasi Publik yang
 * menempatkan putusan keberatan di tangan atasan PPID.
 *
 * Ini **contoh awal**, bukan aturan yang tertanam: setelah diseed seluruhnya
 * bisa disusun ulang super admin lewat modul Alur Persetujuan.
 *
 * Idempoten — dicocokkan lewat `jenis`, aman dijalankan ulang:
 *
 *   php artisan db:seed --class=AlurApprovalSeeder
 */
class AlurApprovalSeeder extends Seeder
{
    public function run(): void
    {
        $this->susun('permohonan', 'Alur Persetujuan Permohonan Informasi', [
            [
                'nama' => 'Persetujuan PPID',
                'role' => 'ppid-utama',
                'jabatan' => 'PPID',
                'sla_hari' => 3,
                'boleh_tolak' => true,
                'keterangan' => 'PPID memeriksa tanggapan yang disiapkan PPID Pelaksana sebelum diteruskan ke Atasan PPID.',
            ],
            [
                'nama' => 'Pengesahan Atasan PPID',
                'role' => 'atasan-ppid',
                'jabatan' => 'Atasan PPID',
                'sla_hari' => 3,
                'boleh_tolak' => true,
                'keterangan' => 'Pengesahan akhir. Setelah disetujui, permohonan berpindah ke status Disetujui.',
            ],
        ]);

        $this->susun('keberatan', 'Alur Persetujuan Keberatan Informasi', [
            [
                'nama' => 'Telaah PPID',
                'role' => 'ppid-utama',
                'jabatan' => 'PPID',
                'sla_hari' => 5,
                'boleh_tolak' => false,
                'keterangan' => 'PPID menelaah keberatan dan menyiapkan bahan putusan; penolakan bukan wewenangnya.',
            ],
            [
                'nama' => 'Putusan Atasan PPID',
                'role' => 'atasan-ppid',
                'jabatan' => 'Atasan PPID',
                'sla_hari' => 5,
                'boleh_tolak' => true,
                'keterangan' => 'Putusan atas keberatan. Setelah disetujui, keberatan berpindah ke status Selesai.',
            ],
        ]);

        $this->command?->info('Alur persetujuan: 2 alur, 4 tahap tersimpan.');
    }

    /** @param  array<int, array<string, mixed>>  $tahap */
    private function susun(string $jenis, string $nama, array $tahap): void
    {
        $alur = AlurApproval::firstOrNew(['jenis' => $jenis]);
        $alur->nama = $nama;
        $alur->is_active = true;
        $alur->save();

        foreach ($tahap as $index => $satu) {
            AlurApprovalTahap::updateOrCreate(
                ['alur_id' => $alur->id, 'urutan' => $index + 1],
                [
                    'nama' => $satu['nama'],
                    'role_id' => Role::where('slug', $satu['role'])->value('id'),
                    'struktur_id' => StrukturOrganisasi::where('jabatan', $satu['jabatan'])->value('id'),
                    'sla_hari' => $satu['sla_hari'],
                    'boleh_tolak' => $satu['boleh_tolak'],
                    'keterangan' => $satu['keterangan'],
                    'is_active' => true,
                ]
            );
        }
    }
}
