<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Isi contoh Bagan Struktur Organisasi PPID (acuan: BAGAN STRUKTUR ORGANISASI PPID.png).
 *
 * Idempoten: dicocokkan lewat `jabatan`, jadi aman dijalankan ulang. Setelah
 * diseed, seluruh isinya bisa diubah dari modul Struktur Organisasi di be-ppid.
 *
 *   php artisan db:seed --class=BaganStrukturPpidSeeder
 */
class BaganStrukturPpidSeeder extends Seeder
{
    public function run(): void
    {
        $atasan = $this->simpan([
            'jabatan' => 'Atasan PPID',
            'nama' => 'Atasan PPID',
            'tipe_node' => 'utama',
            'urutan' => 1,
        ]);

        $ppid = $this->simpan([
            'jabatan' => 'PPID',
            'nama' => 'Sekretaris Perusahaan & Kepatuhan',
            'tipe_node' => 'utama',
            'parent_id' => $atasan,
            'urutan' => 2,
        ]);

        $this->simpan([
            'jabatan' => 'Tim Pertimbangan PPID',
            'nama' => 'Tim Pertimbangan PPID',
            'tipe_node' => 'samping',
            'parent_id' => $atasan,
            'urutan' => 3,
            'poin' => "Risk Management\nLegal & Kepatuhan\nSeluruh Kepala Divisi & Departemen Perusahaan",
        ]);

        $pelaksana = $this->simpan([
            'jabatan' => 'PPID Pelaksana',
            'nama' => 'PPID Pelaksana',
            'tipe_node' => 'grup',
            'parent_id' => $ppid,
            'urutan' => 4,
        ]);

        $anak = [
            ['jabatan' => 'Pengelolaan & Dokumentasi Informasi', 'nama' => 'Seksi Humas', 'urutan' => 5],
            ['jabatan' => 'Pengumuman Informasi', 'nama' => 'Staf Sekretaris Perusahaan & Kepatuhan', 'urutan' => 6],
            ['jabatan' => 'Penyediaan Informasi', 'nama' => 'Staf Seksi Humas', 'urutan' => 7],
        ];

        foreach ($anak as $baris) {
            $this->simpan($baris + ['tipe_node' => 'utama', 'parent_id' => $pelaksana]);
        }

        $this->command?->info('Bagan struktur PPID: 7 kotak tersimpan.');
    }

    /** Simpan satu kotak; dicocokkan lewat jabatan lalu kembalikan id-nya. */
    private function simpan(array $data): int
    {
        $jabatan = $data['jabatan'];
        unset($data['jabatan']);

        DB::table('struktur_organisasi')->updateOrInsert(
            ['jabatan' => $jabatan],
            $data + ['is_active' => true]
        );

        return (int) DB::table('struktur_organisasi')->where('jabatan', $jabatan)->value('id');
    }
}
