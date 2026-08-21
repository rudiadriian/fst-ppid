<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\KategoriBerita;
use App\Models\KategoriInformasi;
use App\Models\PengaturanSitus;
use Illuminate\Database\Seeder;

/**
 * Konten awal agar situs publik punya isi begitu CMS dipasang.
 *
 * Isinya hanya data yang memang baku (klasifikasi informasi menurut UU No. 14
 * Tahun 2008, pertanyaan umum, dan kontak resmi). Data yang harus diisi
 * perusahaan — berita, galeri, laporan, struktur pejabat — sengaja tidak
 * dibuat di sini supaya tidak ada informasi karangan yang tayang.
 *
 * Aman dijalankan berulang.
 */
class KontenAwalSeeder extends Seeder
{
    public function run(): void
    {
        $this->kategoriInformasi();
        $this->kategoriBerita();
        $this->faq();
        $this->pengaturan();
    }

    private function kategoriInformasi(): void
    {
        $daftar = [
            ['berkala', 'Informasi Berkala', 'Informasi yang wajib disediakan dan diumumkan secara berkala, sekurang-kurangnya setiap 6 (enam) bulan sekali.', 1],
            ['serta-merta', 'Informasi Serta Merta', 'Informasi yang wajib diumumkan tanpa penundaan karena menyangkut hajat hidup orang banyak dan/atau ketertiban umum.', 2],
            ['setiap-saat', 'Informasi Setiap Saat', 'Informasi yang dapat diakses oleh pemohon informasi publik setiap saat, baik secara langsung maupun tidak langsung.', 3],
        ];

        foreach ($daftar as [$slug, $nama, $deskripsi, $urutan]) {
            KategoriInformasi::updateOrCreate(
                ['slug' => $slug],
                ['nama' => $nama, 'deskripsi' => $deskripsi, 'urutan' => $urutan, 'is_active' => true]
            );
        }
    }

    private function kategoriBerita(): void
    {
        foreach (['Operasional', 'Prestasi', 'CSR', 'Siaran Pers'] as $nama) {
            KategoriBerita::updateOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($nama)],
                ['nama' => $nama]
            );
        }
    }

    private function faq(): void
    {
        $daftar = [
            ['Bagaimana cara mengajukan permohonan informasi publik?', 'Permohonan dapat diajukan secara daring melalui menu Permohonan Informasi pada portal ini dengan mengisi formulir yang tersedia, atau secara langsung ke kantor PPID pada jam layanan.'],
            ['Berapa lama waktu tanggapan atas permohonan?', 'Sesuai UU No. 14 Tahun 2008, PPID wajib menanggapi permohonan paling lambat 10 hari kerja sejak permohonan diterima, dan dapat diperpanjang 7 hari kerja apabila diperlukan.'],
            ['Apakah layanan informasi publik dikenakan biaya?', 'Layanan informasi publik tidak dipungut biaya, kecuali biaya penggandaan atau pengiriman dokumen yang besarannya wajar dan diinformasikan di awal.'],
            ['Apa yang bisa dilakukan jika permohonan ditolak?', 'Pemohon dapat mengajukan keberatan secara tertulis kepada atasan PPID melalui menu Pengajuan Keberatan pada portal ini, paling lambat 30 hari kerja sejak diterimanya penolakan.'],
            ['Bagaimana cara melacak status permohonan yang sudah diajukan?', 'Gunakan menu Cek Status Layanan dan masukkan nomor tiket yang diterima saat pengajuan untuk melihat perkembangan proses.'],
        ];

        foreach ($daftar as $i => [$tanya, $jawab]) {
            Faq::updateOrCreate(
                ['pertanyaan' => $tanya],
                ['jawaban' => $jawab, 'kategori' => 'Layanan Informasi', 'urutan' => $i + 1, 'is_active' => true]
            );
        }
    }

    private function pengaturan(): void
    {
        $daftar = [
            ['situs.nama', 'PPID PT Food Station Tjipinang Jaya (Perseroda)', 'umum'],
            ['kontak.alamat', 'Komplek Pasar Induk Beras Cipinang, Jl. Pisangan Lama Selatan No. 1, Jakarta Timur 13230', 'kontak'],
            ['kontak.telepon', '(021) 4718011 (Ext. PPID)', 'kontak'],
            ['kontak.email', 'ppid@foodstation.co.id', 'kontak'],
            ['kontak.jam_layanan', 'Senin–Jumat, 08.00–15.00 WIB (istirahat 12.00–13.00 WIB)', 'kontak'],
        ];

        foreach ($daftar as [$key, $value, $grup]) {
            PengaturanSitus::updateOrCreate(['key' => $key], ['value' => $value, 'group_name' => $grup]);
        }
    }
}
