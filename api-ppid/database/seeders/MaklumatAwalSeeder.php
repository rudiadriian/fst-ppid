<?php

namespace Database\Seeders;

use App\Models\Maklumat;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Maklumat Pelayanan Informasi Publik awal.
 *
 * Berkas acuannya (MAKLUMAT PPID.png) disalin ke disk `media` seperti hasil
 * unggahan biasa, lalu dicatat sebagai satu baris berstatus `published`,
 * sehingga halaman Standar Layanan langsung menayangkan dokumen aslinya.
 * Setelahnya berkas bisa diganti kapan saja lewat modul Maklumat di be-ppid.
 *
 * Idempoten, tetapi tidak buta: maklumat terbit yang **sudah punya berkas**
 * dilewati, sedangkan maklumat terbit yang berkasnya kosong tetap dilengkapi
 * (langkah 88). Versi sebelumnya berhenti begitu ada baris berstatus published
 * tanpa memeriksa isinya, sehingga baris yang terlanjur dibuat saat berkas
 * acuannya belum tersedia tidak pernah terisi — halamannya diam-diam memakai
 * teks bawaan, dan tidak ada yang gagal untuk memberi tahu.
 *
 *   php artisan db:seed --class=MaklumatAwalSeeder
 */
class MaklumatAwalSeeder extends Seeder
{
    public function run(): void
    {
        $terbit = Maklumat::where('status', 'published')
            ->orderByDesc('tanggal_terbit')
            ->orderByDesc('id')
            ->first();

        if ($terbit && filled($terbit->file_dokumen)) {
            $this->command?->info('Maklumat terbit sudah punya dokumen — dilewati.');

            return;
        }

        $sumber = base_path('../MAKLUMAT PPID.png');

        if (!is_file($sumber)) {
            $this->command?->warn('Berkas "MAKLUMAT PPID.png" tidak ditemukan di folder proyek — baris dibuat tanpa dokumen.');
        }

        $path = null;

        if (is_file($sumber)) {
            $path = 'uploads/maklumat/'.now()->format('Y/m').'/'.Str::random(32).'.png';

            Storage::disk('media')->put($path, file_get_contents($sumber));
        }

        // Baris yang sudah ada dilengkapi, bukan digandakan: menambah maklumat
        // terbit kedua membuat situs memilih salah satunya berdasar tanggal,
        // dan arsipnya jadi berisi dua baris untuk satu dokumen yang sama.
        if ($terbit) {
            $terbit->update(['file_dokumen' => $path]);

            $this->command?->info('Dokumen maklumat terbit dilengkapi'.($path ? ": $path" : ' (berkas acuan tidak ada).'));

            return;
        }

        Maklumat::create([
            'judul' => 'Maklumat Pelayanan Informasi Publik',
            'judul_en' => 'Public Information Service Charter',
            'ringkasan' => 'Pernyataan komitmen PPID PT Food Station Tjipinang Jaya (Perseroda) dalam menyelenggarakan pelayanan informasi publik sesuai Undang-Undang Nomor 14 Tahun 2008 tentang Keterbukaan Informasi Publik.',
            'ringkasan_en' => 'The service commitment of PPID PT Food Station Tjipinang Jaya (Perseroda) in providing public information services under Law Number 14 of 2008 on Public Information Disclosure.',
            'file_dokumen' => $path,
            'tanggal_terbit' => now()->toDateString(),
            'status' => 'published',
        ]);

        $this->command?->info('Maklumat awal dibuat'.($path ? ": $path" : ' (tanpa dokumen).'));
    }
}
