<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Daftar Informasi Publik PT Food Station Tjipinang Jaya (Perseroda) Tahun 2026.
 *
 * Acuan: "DAFTAR INFORMASI PUBLIK PT FOOD STATION TJIPINANG JAYA.docx.pdf" —
 * 17 entri dengan klasifikasi Berkala / Serta Merta / Setiap Saat.
 *
 * Idempoten: dicocokkan lewat `judul`. Baris contoh lama yang tidak ada di
 * dokumen resmi TIDAK dihapus, hanya diturunkan statusnya jadi `draft` supaya
 * tidak tampil di situs publik tetapi tetap bisa dikembalikan dari be-ppid.
 *
 *   php artisan db:seed --class=DaftarInformasiPublikSeeder
 */
class DaftarInformasiPublikSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = DB::table('kategori_informasi')->pluck('id', 'slug');

        $judulResmi = [];

        foreach ($this->daftar() as $urutan => $baris) {
            $judulResmi[] = $baris['judul'];

            $isi = [
                'kategori_id' => $kategori[$baris['kategori']] ?? null,
                'judul' => $baris['judul'],
                'ringkasan' => $baris['ringkasan'],
                'nomor_klasifikasi' => (string) ($urutan + 1),
                'status' => 'published',
                'updated_at' => now(),
            ];

            $lama = DB::table('informasi_publik')->where('judul', $baris['judul'])->first();

            if ($lama) {
                DB::table('informasi_publik')->where('id', $lama->id)->update($isi);

                continue;
            }

            DB::table('informasi_publik')->insert($isi + [
                'slug' => Str::slug($baris['judul']),
                'tanggal_publikasi' => now()->toDateString(),
                'views_count' => 0,
                'created_at' => now(),
            ]);
        }

        // Sisa data contoh lama disembunyikan, bukan dihapus.
        $disembunyikan = DB::table('informasi_publik')
            ->whereNotIn('judul', $judulResmi)
            ->where('status', 'published')
            ->update(['status' => 'draft', 'updated_at' => now()]);

        $this->command?->info(count($judulResmi).' entri resmi disiapkan; '.$disembunyikan.' entri contoh lama dijadikan draft.');
    }

    /**
     * @return array<int, array{judul: string, kategori: string, ringkasan: string}>
     */
    private function daftar(): array
    {
        $berkala = 'berkala';
        $sertaMerta = 'serta-merta';
        $setiapSaat = 'setiap-saat';

        return [
            ['judul' => 'Informasi Umum Food Station', 'kategori' => $berkala, 'ringkasan' => 'Profil ringkas perusahaan, bidang usaha, dan informasi umum PT Food Station Tjipinang Jaya (Perseroda).'],
            ['judul' => 'Profil Dewan Komisaris & Direksi', 'kategori' => $berkala, 'ringkasan' => 'Susunan serta riwayat singkat Dewan Komisaris dan Direksi perusahaan.'],
            ['judul' => 'Struktur Organisasi', 'kategori' => $berkala, 'ringkasan' => 'Bagan struktur organisasi perusahaan beserta pembagian unit kerjanya.'],
            ['judul' => 'Siaran Media', 'kategori' => $berkala, 'ringkasan' => 'Siaran pers dan pemberitaan resmi yang diterbitkan perusahaan.'],
            ['judul' => 'Company Profile Video', 'kategori' => $berkala, 'ringkasan' => 'Profil perusahaan dalam bentuk video.'],
            ['judul' => 'Company Profile Cetak', 'kategori' => $berkala, 'ringkasan' => 'Profil perusahaan dalam bentuk dokumen cetak.'],
            ['judul' => 'Annual Report', 'kategori' => $berkala, 'ringkasan' => 'Laporan tahunan perusahaan berisi kinerja dan capaian sepanjang tahun buku.'],

            ['judul' => 'Data Kegiatan Korporasi', 'kategori' => $sertaMerta, 'ringkasan' => 'Data kegiatan korporasi yang perlu diketahui publik segera setelah berlangsung.'],
            ['judul' => 'Kegiatan Tanggung Jawab Perusahaan', 'kategori' => $sertaMerta, 'ringkasan' => 'Program tanggung jawab sosial dan lingkungan perusahaan (CSR).'],
            ['judul' => 'Tugas dan Wewenang PPID', 'kategori' => $sertaMerta, 'ringkasan' => 'Tugas, fungsi, dan wewenang Pejabat Pengelola Informasi dan Dokumentasi perusahaan.'],
            ['judul' => 'Foto Ases (Gedung)', 'kategori' => $sertaMerta, 'ringkasan' => 'Dokumentasi foto aset gedung dan fasilitas perusahaan.'],
            ['judul' => 'Ringkasan Laporan KSD Perusahaan Triwulan', 'kategori' => $sertaMerta, 'ringkasan' => 'Ringkasan laporan Kegiatan Strategis Daerah perusahaan per triwulan.'],

            ['judul' => 'Memorandum of Understanding (MoU)', 'kategori' => $setiapSaat, 'ringkasan' => 'Nota kesepahaman perusahaan dengan mitra yang bersifat terbuka.'],
            ['judul' => 'Perjanjuan Kerja Sama dan Addendum', 'kategori' => $setiapSaat, 'ringkasan' => 'Perjanjian kerja sama beserta addendum yang dapat diakses publik.'],
            ['judul' => 'Non Disclosure Agreement (NDA)', 'kategori' => $setiapSaat, 'ringkasan' => 'Dokumen perjanjian kerahasiaan yang statusnya terbuka bagi publik.'],
            ['judul' => 'Harga Promo Penjualan', 'kategori' => $setiapSaat, 'ringkasan' => 'Informasi harga promo penjualan produk perusahaan.'],
            ['judul' => 'Agenda Pasar Murah', 'kategori' => $setiapSaat, 'ringkasan' => 'Jadwal dan lokasi kegiatan pasar murah yang diselenggarakan perusahaan.'],
        ];
    }
}
