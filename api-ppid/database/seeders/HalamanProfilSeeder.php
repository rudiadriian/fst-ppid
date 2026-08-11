<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Halaman sub modul Profil di situs publik (acuan: "Profil VISI MISI FUNGSI.docx").
 *
 * Barisnya dibuat di modul Halaman Statis supaya isinya bisa diubah dari
 * be-ppid tanpa deploy ulang. Slug-nya harus sama dengan kunci sub modul di
 * `PpidController@showProfilePage`; halaman yang sudah ada di sini selalu
 * menang atas isi bawaan yang ditulis di controller.
 *
 * Idempoten — dicocokkan lewat `slug`, aman dijalankan ulang. Isi yang sudah
 * disunting operator TIDAK ditimpa; hanya baris yang belum ada yang dibuat.
 *
 *   php artisan db:seed --class=HalamanProfilSeeder
 */
class HalamanProfilSeeder extends Seeder
{
    public function run(): void
    {
        $this->buatBilaBelumAda(
            'tugas-fungsi-wewenang',
            'Tugas, Fungsi dan Wewenang',
            $this->kontenTugasFungsiWewenang()
        );
    }

    private function buatBilaBelumAda(string $slug, string $judul, string $konten): void
    {
        if (DB::table('halaman_statis')->where('slug', $slug)->exists()) {
            $this->command?->info("Halaman '{$slug}' sudah ada — dilewati.");

            return;
        }

        DB::table('halaman_statis')->insert([
            'judul' => $judul,
            'slug' => $slug,
            'konten' => $konten,
            'is_active' => true,
            'updated_at' => now(),
        ]);

        $this->command?->info("Halaman '{$slug}' dibuat.");
    }

    private function kontenTugasFungsiWewenang(): string
    {
        $fungsi = [
            'Melaksanakan pembinaan, pengelolaan, dan pelayanan informasi serta dokumentasi di seluruh unit kerja PT Food Station Tjipinang Jaya (Perseroda).',
        ];

        $wewenang = [
            'Menetapkan apakah suatu informasi publik dapat diakses atau dikecualikan melalui Uji Konsekuensi bersama Atasan PPID.',
            'Menolak permohonan Informasi Publik secara tertulis apabila termasuk kategori informasi yang dikecualikan, dengan memberikan alasan penolakan serta penjelasan hak dan tata cara pengajuan keberatan bagi pemohon.',
            'Menghadiri rapat koordinasi atau pembahasan terkait PPID di tingkat Provinsi DKI Jakarta.',
            'Melakukan koordinasi dengan perangkat PPID dan/atau unit terkait dalam penanganan permohonan informasi maupun penyelesaian keberatan.',
            'Melakukan pembaruan dan penyediaan informasi publik terkini melalui portal resmi PT Food Station Tjipinang Jaya (Perseroda) dan/atau Sistem Informasi PPID.',
            'Melaporkan setiap ketidaksesuaian dalam proses sengketa informasi publik kepada Sekretariat Komisi Informasi dengan persetujuan Atasan PPID.',
            'Melaksanakan sosialisasi dan edukasi internal guna meningkatkan pemahaman terhadap keterbukaan informasi publik di lingkungan perusahaan.',
        ];

        $butir = fn (array $daftar) => implode('', array_map(fn ($isi) => '<li>'.e($isi).'</li>', $daftar));

        return '<h3>Fungsi PPID PT Food Station Tjipinang Jaya (Perseroda)</h3>'
            .'<ul>'.$butir($fungsi).'</ul>'
            .'<h3>Wewenang PPID PT Food Station Tjipinang Jaya (Perseroda)</h3>'
            .'<ul>'.$butir($wewenang).'</ul>';
    }
}
