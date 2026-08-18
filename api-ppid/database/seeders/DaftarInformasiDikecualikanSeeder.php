<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Daftar Informasi Dikecualikan PT Food Station Tjipinang Jaya (Perseroda)
 * Tahun 2026.
 *
 * Acuan: "DAFTAR INFORMASI DIKECUALIKAN PT FOOD STATION TJIPINANG JAYA.docx.pdf"
 * — 22 entri. Dokumen acuannya hanya memuat judul, sedangkan tabelnya
 * mewajibkan alasan pengecualian; alasan dan dasar hukum di bawah diisi rumusan
 * umum Pasal 17 UU No. 14 Tahun 2008 sebagai titik awal, **untuk dilengkapi
 * petugas lewat be-ppid** sesuai hasil uji konsekuensi masing-masing dokumen.
 *
 * Idempoten: dicocokkan lewat `judul`; entri contoh lama yang tidak ada di
 * dokumen resmi diturunkan jadi `draft`, tidak dihapus.
 *
 *   php artisan db:seed --class=DaftarInformasiDikecualikanSeeder
 */
class DaftarInformasiDikecualikanSeeder extends Seeder
{
    public function run(): void
    {
        $judulResmi = [];

        foreach ($this->daftar() as $judul) {
            $judulResmi[] = $judul;

            // Keterangan penetapan (alasan, dasar hukum, jangka waktu, tanggal)
            // tidak ditampilkan di situs publik dan tidak wajib diisi, jadi
            // seeder ini tidak lagi mengisinya dengan teks pengganti.
            $isi = [
                'judul' => $judul,
                'status' => 'published',
                'updated_at' => now(),
            ];

            $lama = DB::table('informasi_dikecualikan')->where('judul', $judul)->first();

            if ($lama) {
                // Isian yang sudah dilengkapi petugas tidak ditimpa.
                DB::table('informasi_dikecualikan')->where('id', $lama->id)->update([
                    'status' => 'published',
                    'updated_at' => now(),
                ]);

                continue;
            }

            DB::table('informasi_dikecualikan')->insert($isi + [
                'slug' => Str::slug($judul),
                'created_at' => now(),
            ]);
        }

        $disembunyikan = DB::table('informasi_dikecualikan')
            ->whereNotIn('judul', $judulResmi)
            ->where('status', 'published')
            ->update(['status' => 'draft', 'updated_at' => now()]);

        $this->command?->info(count($judulResmi).' entri resmi disiapkan; '.$disembunyikan.' entri contoh lama dijadikan draft.');
    }

    /**
     * @return array<int, string>
     */
    private function daftar(): array
    {
        return [
            'Data Pelanggan (Perjanjian Sewa Lahan dan Properti/Bisnis lain)',
            'Data Pribadi Pegawai',
            'Disposisi Surat Pimpinan',
            'Dokumen Hasil Pemeriksaan Perusahaan',
            'Dokumen SPI (Pertanggungjawaban Keuangan)',
            'Informasi Upah Pegawai Food Station',
            'Kebutuhan Karyawan Setiap Bidang dan Tes Calon Karyawan',
            'Lokasi Server',
            'Materi Perselisihan Hubungan Industri',
            'Korespondensi Internal dan Eksternal Perusahaan',
            'Notulensi Perusahaan',
            'Pendapatan dari Semua Sektor',
            'Surat Keputusan Direksi',
            'Perincian Laporan Keuangan Perusahaan dan Bukti-bukti terkait aktivitas perusahaan',
            'Perjanjian Kerja Karyawan',
            'Rencana Kerja Anggaran Perusahaan/RJPP/Master Plan (Tahun Berjalan)',
            'Somasi dan surat keberatan/penolakan dari individu/kelompok masyarakat untuk diterbitkan izin/non izin',
            'Strategi Perseroan Kedepan',
            'Data Kajian Bisnis Perusahaan',
            'Uraian Lengkap Hasil Assessment Pegawai',
            'Perjanjian Kerjasama dengan Pihak Ketiga',
            'Dokumen Pertanahan',
        ];
    }
}
