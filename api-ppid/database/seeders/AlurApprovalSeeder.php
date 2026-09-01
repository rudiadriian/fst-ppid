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
        /*
         * Dua jenjang, bukan tiga (langkah 89). Tahap "Pengesahan Atasan PPID"
         * dilepas dari kedua alur: alur layanan yang diminta berhenti di PPID
         * selaku Sekretaris Perusahaan & Kepatuhan, dan menyisakan satu jenjang
         * lagi di atasnya hanya menahan berkas di kotak yang tidak punya
         * tugas memutus dalam alur ini. Rolenya sendiri tidak dihapus —
         * susunannya masih bisa dikembalikan super admin lewat modul Alur
         * Persetujuan tanpa menyentuh kode.
         *
         * Pembagian SLA-nya mengikuti tenggat undang-undang, dibagi dua:
         * permohonan 3 + 7 = 10 hari kerja; keberatan 10 + 20 = 30 hari.
         * Jenjang pertama sengaja lebih pendek — ia hanya memeriksa
         * kelengkapan, sedangkan jenjang kedua yang menimbang isinya.
         */
        $this->susun('permohonan', 'Alur Persetujuan Permohonan Informasi', [
            [
                'nama' => 'Penerimaan PPID Pelaksana',
                'role' => 'ppid-pelaksana',
                'jabatan' => 'PPID Pelaksana',
                'sla_hari' => 3,
                // Sengaja tanpa hak menolak: tugas jenjang ini meneruskan, dan
                // menolak permohonan adalah keputusan yang menurut UU KIP harus
                // disertai alasan tertulis dari pejabat yang berwenang.
                'boleh_tolak' => false,
                'keterangan' => 'PPID Pelaksana memeriksa kelengkapan permohonan dan menyiapkan tanggapannya, lalu meneruskan ke PPID. Penolakan bukan wewenang jenjang ini.',
            ],
            [
                'nama' => 'Persetujuan PPID',
                'role' => 'ppid-utama',
                'jabatan' => 'PPID',
                'sla_hari' => 7,
                'boleh_tolak' => true,
                'keterangan' => 'Putusan akhir permohonan. PPID dapat menyetujui atau menolak dengan alasan; setelah disetujui, permohonan berpindah ke status Disetujui.',
            ],
        ]);

        $this->susun('keberatan', 'Alur Persetujuan Keberatan Informasi', [
            [
                'nama' => 'Penerimaan PPID Pelaksana',
                'role' => 'ppid-pelaksana',
                'jabatan' => 'PPID Pelaksana',
                'sla_hari' => 10,
                'boleh_tolak' => false,
                'keterangan' => 'PPID Pelaksana meregistrasi keberatan, memeriksa berkasnya, lalu meneruskan ke PPID. Penolakan bukan wewenang jenjang ini.',
            ],
            [
                'nama' => 'Putusan PPID',
                'role' => 'ppid-utama',
                'jabatan' => 'PPID',
                'sla_hari' => 20,
                'boleh_tolak' => true,
                'keterangan' => 'Putusan atas keberatan, paling lambat 30 hari sejak keberatan diregistrasi. Setelah disetujui, keberatan berpindah ke status Selesai.',
            ],
        ]);

        // Tahap lama di luar dua jenjang ini dilepas, bukan dibiarkan tertinggal:
        // tahap ketiga yang masih aktif akan tetap menahan berkas meski sudah
        // tidak disebut di seeder.
        $dilepas = AlurApprovalTahap::whereIn('alur_id', AlurApproval::whereIn('jenis', ['permohonan', 'keberatan'])->pluck('id'))
            ->where('urutan', '>', 2)
            ->delete();

        $this->command?->info('Alur persetujuan: 2 alur, 4 tahap tersimpan'.($dilepas ? ", $dilepas tahap lama dilepas." : '.'));
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
