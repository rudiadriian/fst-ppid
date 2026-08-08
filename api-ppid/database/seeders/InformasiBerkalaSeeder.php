<?php

namespace Database\Seeders;

use App\Models\InformasiPublik;
use App\Models\KategoriInformasi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Daftar "Informasi Wajib Disediakan dan Diumumkan Secara Berkala"
 * sesuai berkas INFORMASI BERKALA.xlsx.
 *
 * Setiap entri menunjuk ke halaman di situs korporat, bukan berkas unggahan,
 * jadi yang diisi adalah kolom `tautan`.
 *
 * Idempoten: dicocokkan dengan `slug`, aman dijalankan berulang.
 *
 *   php artisan db:seed --class=InformasiBerkalaSeeder
 */
class InformasiBerkalaSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = KategoriInformasi::where('slug', 'berkala')->first();

        if (!$kategori) {
            $this->command?->error('Kategori dengan slug "berkala" tidak ditemukan. Buat dulu di modul Kategori Informasi.');

            return;
        }

        $daftar = [
            ['Profil dan Sejarah Perusahaan', 'https://foodstation.id/sejarah-perusahaan/'],
            ['Profil Singkat Pimpinan Perusahaan', 'https://foodstation.id/struktur-organisasi/'],
            ['Visi dan Misi Perusahaan', 'https://foodstation.id/visi-misi-dan-strategi/'],
            ['Tugas Pokok dan Fungsi Perusahaan', 'https://foodstation.id/visi-misi-dan-strategi/'],
            ['Core Value/Nilai Perusahaan', 'https://foodstation.id/visi-misi-dan-strategi/'],
            ['Struktur Organisasi Perusahaan', 'https://foodstation.id/struktur-organisasi/'],
            ['Kontak Perusahaan dan Jam Layanan', 'https://foodstation.id/hubungi-kami/'],
            ['Siaran Media', 'https://foodstation.id/berita-kegiatan/'],
            ['Company Profile Video', 'https://foodstation.id/berita-kegiatan/'],
            ['Annual Report', 'https://foodstation.id/laporan-tahunan-fstj/'],
        ];

        foreach ($daftar as $urutan => [$judul, $tautan]) {
            InformasiPublik::withTrashed()->updateOrCreate(
                ['slug' => Str::slug($judul)],
                [
                    'kategori_id' => $kategori->id,
                    'judul' => $judul,
                    'tautan' => $tautan,
                    'status' => 'published',
                    'tanggal_publikasi' => now()->toDateString(),
                    'deleted_at' => null,
                ]
            );
        }

        $this->command?->info(count($daftar).' entri Informasi Berkala tersimpan.');
    }
}
