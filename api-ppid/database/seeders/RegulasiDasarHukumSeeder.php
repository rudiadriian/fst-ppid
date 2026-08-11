<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Delapan dasar hukum PPID beserta berkas PDF-nya (acuan: folder REGULASI dan
 * dokumen PPID_Dasar Hukum.docx).
 *
 * Berkasnya sudah disalin ke disk `media` (fe-ppid/storage/app/public) pada
 * folder `uploads/regulasi`, jadi seeder ini hanya menautkan barisnya.
 *
 * Idempoten: dicocokkan lewat `file_path`, dan baris lama tanpa berkas yang
 * membahas peraturan yang sama ikut dirapikan supaya tidak tampil dobel.
 *
 *   php artisan db:seed --class=RegulasiDasarHukumSeeder
 */
class RegulasiDasarHukumSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->daftar() as $baris) {
            $isi = $baris;
            unset($isi['kunci_lama']);

            $lama = DB::table('regulasi')->where('file_path', $baris['file_path'])->first();

            if ($lama) {
                DB::table('regulasi')->where('id', $lama->id)->update($isi);
                $this->command?->info("Diperbarui: {$baris['nomor_peraturan']}");

                continue;
            }

            // Baris lawas hasil data contoh: judulnya menyebut peraturan yang
            // sama tetapi belum punya berkas. Dipakai ulang, bukan digandakan.
            $kandidat = DB::table('regulasi')
                ->whereNull('file_path')
                ->where('tahun', $baris['tahun'])
                ->where('judul', 'ilike', '%'.$baris['kunci_lama'].'%')
                ->first();

            if ($kandidat) {
                DB::table('regulasi')->where('id', $kandidat->id)->update($isi);
                $this->command?->info("Baris lama dipakai ulang: {$baris['nomor_peraturan']}");

                continue;
            }

            DB::table('regulasi')->insert($isi + ['created_at' => now()]);
            $this->command?->info("Ditambahkan: {$baris['nomor_peraturan']}");
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function daftar(): array
    {
        $data = [
            [
                'judul' => 'Undang-Undang Republik Indonesia Nomor 14 Tahun 2008 tentang Keterbukaan Informasi Publik',
                'nomor_peraturan' => 'Nomor 14 Tahun 2008',
                'jenis_peraturan' => 'Undang-Undang',
                'tahun' => 2008,
                'file_path' => 'uploads/regulasi/uu-14-2008.pdf',
                'ringkasan' => 'Undang-undang yang menjamin hak setiap orang memperoleh informasi publik, mengatur kewajiban badan publik menyediakan informasi, jenis informasi yang dikecualikan, serta mekanisme keberatan dan sengketa informasi.',
                'kunci_lama' => 'Nomor 14 Tahun 2008',
            ],
            [
                'judul' => 'Undang-Undang Republik Indonesia Nomor 25 Tahun 2009 tentang Pelayanan Publik',
                'nomor_peraturan' => 'Nomor 25 Tahun 2009',
                'jenis_peraturan' => 'Undang-Undang',
                'tahun' => 2009,
                'file_path' => 'uploads/regulasi/uu-25-2009.pdf',
                'ringkasan' => 'Undang-undang tentang pelayanan publik: asas dan tujuan, standar pelayanan, hak dan kewajiban penyelenggara maupun masyarakat, serta penanganan pengaduan.',
                'kunci_lama' => 'Nomor 25 Tahun 2009',
            ],
            [
                'judul' => 'Peraturan Komisi Informasi Republik Indonesia Nomor 1 Tahun 2021 tentang Standar Layanan Informasi Publik',
                'nomor_peraturan' => 'Nomor 1 Tahun 2021',
                'jenis_peraturan' => 'Peraturan Komisi Informasi',
                'tahun' => 2021,
                'file_path' => 'uploads/regulasi/perki-1-2021.pdf',
                'ringkasan' => 'Peraturan Komisi Informasi tentang Standar Layanan Informasi Publik: kewajiban badan publik, tata cara pelayanan permohonan, jangka waktu tanggapan, hingga pelaporan layanan.',
                'kunci_lama' => 'Nomor 1 Tahun 2021',
            ],
            [
                'judul' => 'Peraturan Komisi Informasi Pusat Nomor 1 Tahun 2003 tentang Prosedur Penyelesaian Sengketa Informasi Publik',
                'nomor_peraturan' => 'Nomor 1 Tahun 2003',
                'jenis_peraturan' => 'Peraturan Komisi Informasi',
                'tahun' => 2003,
                'file_path' => 'uploads/regulasi/perki-1-2003.pdf',
                'ringkasan' => 'Peraturan Komisi Informasi tentang prosedur penyelesaian sengketa informasi publik, mulai dari pengajuan permohonan, mediasi, hingga ajudikasi nonlitigasi.',
                'kunci_lama' => 'Nomor 1 Tahun 2003',
            ],
            [
                'judul' => 'Peraturan Gubernur Nomor 175 Tahun 2016 tentang Layanan Informasi Publik',
                'nomor_peraturan' => 'Nomor 175 Tahun 2016',
                'jenis_peraturan' => 'Peraturan Gubernur',
                'tahun' => 2016,
                'file_path' => 'uploads/regulasi/pergub-175-2016.pdf',
                'ringkasan' => 'Peraturan Gubernur DKI Jakarta tentang penyelenggaraan layanan informasi publik di lingkungan Pemerintah Provinsi DKI Jakarta beserta perangkat PPID-nya.',
                'kunci_lama' => 'Nomor 175 Tahun 2016',
            ],
            [
                'judul' => 'Peraturan Pemerintah Nomor 61 Tahun 2010 tentang Pelaksanaan Undang-Undang Nomor 14 Tahun 2008 tentang Keterbukaan Informasi Publik',
                'nomor_peraturan' => 'Nomor 61 Tahun 2010',
                'jenis_peraturan' => 'Peraturan Pemerintah',
                'tahun' => 2010,
                'file_path' => 'uploads/regulasi/pp-61-2010.pdf',
                'ringkasan' => 'Peraturan pelaksanaan UU Keterbukaan Informasi Publik: penetapan PPID, tata cara pengumuman dan pelayanan informasi, uji konsekuensi, dan jangka waktu pengecualian.',
                'kunci_lama' => 'Nomor 61 Tahun 2010',
            ],
            [
                'judul' => 'Peraturan Pemerintah Nomor 54 Tahun 2017 tentang Badan Usaha Milik Daerah',
                'nomor_peraturan' => 'Nomor 54 Tahun 2017',
                'jenis_peraturan' => 'Peraturan Pemerintah',
                'tahun' => 2017,
                'file_path' => 'uploads/regulasi/pp-54-2017.pdf',
                'ringkasan' => 'Peraturan Pemerintah tentang Badan Usaha Milik Daerah: pendirian, kepengurusan, tata kelola, pengawasan, serta kewajiban keterbukaan BUMD.',
                'kunci_lama' => 'Nomor 54 Tahun 2017',
            ],
            [
                'judul' => 'Peraturan Daerah Nomor 4 Tahun 2023 tentang Perubahan Bentuk Hukum Perseroan Terbatas Food Station Tjipinang Jaya menjadi Perseroan Terbatas Food Station Tjipinang Jaya (Perseroan Daerah)',
                'nomor_peraturan' => 'Nomor 4 Tahun 2023',
                'jenis_peraturan' => 'Peraturan Daerah',
                'tahun' => 2023,
                'file_path' => 'uploads/regulasi/perda-4-2023.pdf',
                'ringkasan' => 'Peraturan Daerah tentang perubahan bentuk hukum PT Food Station Tjipinang Jaya menjadi Perseroan Terbatas Perseroan Daerah, termasuk modal dan tata kelolanya.',
                'kunci_lama' => 'Nomor 4 Tahun 2023',
            ],
        ];

        // Seluruhnya masuk kategori dasar hukum PPID, sesuai dokumen acuan.
        return array_map(fn (array $b) => $b + ['kategori' => 'dasar_hukum_ppid'], $data);
    }
}
