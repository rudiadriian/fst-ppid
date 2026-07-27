<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Data contoh untuk kanal baru: Informasi Dikecualikan, Laporan Layanan,
 * Register Permohonan, dan Dasar Hukum/Regulasi.
 *
 * Jalankan manual: php artisan db:seed --class=PpidDemoSeeder
 */
class PpidDemoSeeder extends Seeder
{
    /** Insert baris yang belum ada saja, dicek lewat kolom $kunci. Aman dijalankan berulang. */
    private function insertBaru(string $tabel, string $kunci, array $rows): void
    {
        foreach ($rows as $row) {
            if (DB::table($tabel)->where($kunci, $row[$kunci])->exists()) {
                continue;
            }

            DB::table($tabel)->insert($row);
        }
    }

    public function run(): void
    {
        $this->insertBaru('informasi_dikecualikan', 'slug', [
            [
                'judul'                     => 'Dokumen Kontrak Pengadaan Beras dengan Pihak Ketiga',
                'slug'                      => 'kontrak-pengadaan-beras',
                'ringkasan'                 => 'Perjanjian pengadaan beras yang memuat harga satuan dan strategi negosiasi perusahaan.',
                'alasan_pengecualian'       => 'Pengungkapan dapat mengganggu kepentingan perlindungan usaha dari persaingan usaha tidak sehat.',
                'dasar_hukum_pengecualian'  => 'Pasal 17 huruf b UU No. 14 Tahun 2008',
                'jangka_waktu_pengecualian' => '5 tahun sejak tanggal penetapan',
                'tanggal_penetapan'         => '2025-03-12',
                'status'                    => 'published',
                'created_at'                => now(),
                'updated_at'                => now(),
            ],
            [
                'judul'                     => 'Data Pribadi Pemohon Informasi Publik',
                'slug'                      => 'data-pribadi-pemohon',
                'ringkasan'                 => 'Identitas, NIK, alamat, dan kontak pemohon informasi publik.',
                'alasan_pengecualian'       => 'Pengungkapan dapat mengungkap rahasia pribadi dan melanggar perlindungan data pribadi.',
                'dasar_hukum_pengecualian'  => 'Pasal 17 huruf h UU No. 14 Tahun 2008 jo. UU No. 27 Tahun 2022',
                'jangka_waktu_pengecualian' => 'Sepanjang tidak ada persetujuan dari pemilik data',
                'tanggal_penetapan'         => '2025-01-20',
                'status'                    => 'published',
                'created_at'                => now(),
                'updated_at'                => now(),
            ],
        ]);

        $this->insertBaru('laporan_layanan', 'judul', [
            [
                'tipe_laporan'             => 'statistik_informasi',
                'judul'                    => 'Statistik Permohonan Informasi Publik Semester I',
                'tahun'                    => 2026,
                'periode'                  => 'Semester I',
                'jumlah_permohonan_masuk'  => 48,
                'jumlah_dikabulkan'        => 41,
                'jumlah_ditolak'           => 4,
                'jumlah_ditolak_sebagian'  => 2,
                'jumlah_keberatan'         => 1,
                'rata_rata_hari_respon'    => 6.4,
                'ringkasan'                => 'Mayoritas permohonan berkaitan dengan laporan keuangan dan kegiatan CSR.',
                'status'                   => 'published',
                'created_at'               => now(),
                'updated_at'               => now(),
            ],
            [
                'tipe_laporan'             => 'statistik_informasi',
                'judul'                    => 'Statistik Permohonan Informasi Publik Tahunan',
                'tahun'                    => 2025,
                'periode'                  => 'Tahunan',
                'jumlah_permohonan_masuk'  => 87,
                'jumlah_dikabulkan'        => 76,
                'jumlah_ditolak'           => 7,
                'jumlah_ditolak_sebagian'  => 4,
                'jumlah_keberatan'         => 2,
                'rata_rata_hari_respon'    => 7.1,
                'ringkasan'                => null,
                'status'                   => 'published',
                'created_at'               => now(),
                'updated_at'               => now(),
            ],
            [
                'tipe_laporan'             => 'pelayanan_informasi',
                'judul'                    => 'Laporan Pelayanan Informasi Publik Tahun 2025',
                'tahun'                    => 2025,
                'periode'                  => 'Tahunan',
                'jumlah_permohonan_masuk'  => 87,
                'jumlah_dikabulkan'        => 76,
                'jumlah_ditolak'           => 7,
                'jumlah_ditolak_sebagian'  => 4,
                'jumlah_keberatan'         => 2,
                'rata_rata_hari_respon'    => 7.1,
                'ringkasan'                => 'Laporan penyelenggaraan layanan informasi publik beserta capaian standar layanan.',
                'status'                   => 'published',
                'created_at'               => now(),
                'updated_at'               => now(),
            ],
        ]);

        $this->insertBaru('regulasi', 'judul', [
            [
                'kategori'        => 'dasar_hukum_ppid',
                'judul'           => 'Undang-Undang Nomor 14 Tahun 2008 tentang Keterbukaan Informasi Publik',
                'nomor_peraturan' => 'UU No. 14/2008',
                'jenis_peraturan' => 'Undang-Undang',
                'tahun'           => 2008,
                'tanggal_berlaku' => '2010-04-30',
                'created_at'      => now(),
            ],
            [
                'kategori'        => 'dasar_hukum_ppid',
                'judul'           => 'Peraturan Pemerintah Nomor 61 Tahun 2010 tentang Pelaksanaan UU Nomor 14 Tahun 2008',
                'nomor_peraturan' => 'PP No. 61/2010',
                'jenis_peraturan' => 'Peraturan Pemerintah',
                'tahun'           => 2010,
                'tanggal_berlaku' => '2010-08-23',
                'created_at'      => now(),
            ],
            [
                'kategori'        => 'regulasi',
                'judul'           => 'Peraturan Direksi tentang Pedoman Keterbukaan Informasi Publik',
                'nomor_peraturan' => 'No. 12 Tahun 2023',
                'jenis_peraturan' => 'Peraturan Direksi',
                'tahun'           => 2023,
                'tanggal_berlaku' => '2023-11-15',
                'created_at'      => now(),
            ],
            [
                'kategori'        => 'pedoman',
                'judul'           => 'Peraturan Komisi Informasi Nomor 1 Tahun 2021 tentang Standar Layanan Informasi Publik',
                'nomor_peraturan' => 'Perki No. 1/2021',
                'jenis_peraturan' => 'Peraturan Komisi Informasi',
                'tahun'           => 2021,
                'tanggal_berlaku' => '2021-09-01',
                'created_at'      => now(),
            ],
        ]);

        // Register permohonan publik butuh baris pemohon sebagai induk.
        $email = 'pemohon.contoh@example.test';

        $pemohonId = DB::table('pemohon')->where('email', $email)->value('id')
            ?: DB::table('pemohon')->insertGetId([
                'nama'       => 'Pemohon Contoh',
                'email'      => $email,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        $permohonan = [
            [
                'rincian_informasi'  => 'Laporan keuangan tahunan yang telah diaudit tahun buku 2025.',
                'tujuan_penggunaan'  => 'Penelitian akademik',
                'status'             => 'selesai',
                'alasan_penolakan'   => null,
                'tanggal_permohonan' => now()->subDays(30),
                'tanggal_tanggapan'  => now()->subDays(24),
            ],
            [
                'rincian_informasi'  => 'Data realisasi program tanggung jawab sosial dan lingkungan tahun 2025.',
                'tujuan_penggunaan'  => 'Pemantauan publik',
                'status'             => 'diproses',
                'alasan_penolakan'   => null,
                'tanggal_permohonan' => now()->subDays(5),
                'tanggal_tanggapan'  => null,
            ],
            [
                'rincian_informasi'  => 'Salinan kontrak pengadaan beras dengan pihak ketiga.',
                'tujuan_penggunaan'  => 'Kajian industri',
                'status'             => 'ditolak',
                'alasan_penolakan'   => 'Termasuk informasi dikecualikan.',
                'tanggal_permohonan' => now()->subDays(60),
                'tanggal_tanggapan'  => now()->subDays(54),
            ],
        ];

        foreach ($permohonan as $row) {
            $sudahAda = DB::table('permohonan_informasi')
                ->where('rincian_informasi', $row['rincian_informasi'])
                ->exists();

            if ($sudahAda) {
                continue;
            }

            DB::table('permohonan_informasi')->insert($row + [
                'pemohon_id'                => $pemohonId,
                'format_informasi'          => 'softcopy',
                'cara_pengiriman'           => 'email',
                'tampil_di_register_publik' => true,
                'created_at'                => now(),
                'updated_at'                => now(),
            ]);
        }
    }
}
