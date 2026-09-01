<?php

namespace Database\Seeders;

use App\Models\AlurProsedur;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Gambar alur awal halaman Standar Layanan.
 *
 * Infografis di folder `standar layanan/<slug halaman>/` disalin ke disk
 * `media` seperti hasil unggahan biasa, lalu dicatat berurutan, sehingga
 * halamannya langsung menayangkan gambar aslinya. Setelahnya gambar bisa
 * diganti, ditambah, atau diurutkan ulang lewat modul Alur Prosedur di
 * be-ppid.
 *
 * Berkas sumbernya disimpan per halaman, bukan menumpuk di satu folder
 * (langkah 87). Sebelumnya kelima gambar Prosedur Permohonan berada langsung
 * di `standar layanan/` sebagai `1.png`–`5.png`; begitu gambar Keberatan
 * datang dengan penamaan yang sama, folder itu tertimpa dan seeder ini
 * diam-diam berubah arti — nama berkas yang sama menunjuk gambar yang lain.
 * Subfolder per halaman menutup jalan itu.
 *
 * Idempoten per halaman: halaman yang barisnya sudah ada dilewati, halaman
 * yang belum tetap diisi.
 *
 *   php artisan db:seed --class=AlurProsedurAwalSeeder
 */
class AlurProsedurAwalSeeder extends Seeder
{
    /**
     * Isi tiap halaman, berurutan.
     *
     * Judul dan keterangan ditulis pendek karena hanya pengiring: isi
     * lengkapnya sudah ada di dalam gambar dan tidak diketik ulang di sini.
     *
     * Urutan Prosedur Permohonan mengikuti perjalanan pemohon, bukan nomor
     * berkasnya: daftar akun dulu (1–2), baru mengajukan permohonan (3), lalu
     * mekanisme dan syaratnya sebagai rujukan (4–5).
     */
    private const HALAMAN = [
        'prosedur-permohonan' => [
            [
                'berkas' => '1.png',
                'judul' => 'Tata Cara Pembuatan Akun Permohonan Informasi',
                'judul_en' => 'How to Create an Information Request Account',
                'keterangan' => 'Langkah 1–4: mengunjungi situs PPID, membuka menu Permohonan Informasi, mendaftar akun, dan mengisi data pendaftaran.',
                'keterangan_en' => 'Steps 1–4: visiting the PPID site, opening the Information Request menu, registering an account, and filling in the registration form.',
            ],
            [
                'berkas' => '2.png',
                'judul' => 'Proses Verifikasi & Lengkapi Data',
                'judul_en' => 'Verification and Data Completion Process',
                'keterangan' => 'Langkah 5–13: konfirmasi email, masuk ke akun, melengkapi data identitas pemohon dan mengunggah dokumen, sampai berkas diverifikasi Tim PPID.',
                'keterangan_en' => 'Steps 5–13: confirming the email, signing in, completing the applicant identity data and uploading documents, until the files are verified by the PPID team.',
            ],
            [
                'berkas' => '3.png',
                'judul' => 'Tata Cara Permohonan Informasi Publik',
                'judul_en' => 'How to Submit a Public Information Request',
                'keterangan' => 'Langkah 1–5 setelah akun terverifikasi: masuk, membuka Permohonan Informasi, menambah pengajuan, mengisi rincian informasi yang diminta, lalu mengirimnya.',
                'keterangan_en' => 'Steps 1–5 once the account is verified: signing in, opening Information Request, adding a submission, describing the information requested, and sending it.',
            ],
            [
                'berkas' => '4.png',
                'judul' => 'Mekanisme Permohonan Informasi Publik',
                'judul_en' => 'Public Information Request Mechanism',
                'keterangan' => 'Lima tahap penanganan permohonan di sisi Badan Publik, termasuk batas waktu 10 hari kerja yang dapat diperpanjang 7 hari kerja.',
                'keterangan_en' => 'The five stages of handling a request at the public body, including the 10 working-day limit extendable by 7 working days.',
            ],
            [
                'berkas' => '5.png',
                'judul' => 'Syarat Pengajuan Permohonan Informasi Publik',
                'judul_en' => 'Requirements for Submitting a Public Information Request',
                'keterangan' => 'Dokumen identitas yang perlu dilampirkan untuk perorangan, kelompok orang, mahasiswa, dan badan hukum.',
                'keterangan_en' => 'Identity documents to attach for individuals, groups, students, and legal entities.',
            ],
        ],

        /*
         * Prosedur Keberatan dimulai dari cara mengajukannya, baru mekanisme
         * penanganannya, dan ditutup alasan-alasan yang sah. Alasan ditaruh
         * paling akhir karena ia rujukan sebelum memutuskan — pemohon yang
         * sudah membuka halaman ini umumnya sudah tahu ia keberatan, yang ia
         * cari adalah caranya.
         */
        'prosedur-keberatan' => [
            [
                'berkas' => '1.png',
                'judul' => 'Tata Cara Permohonan Keberatan',
                'judul_en' => 'How to Submit an Objection',
                'keterangan' => 'Masuk dengan akun yang sudah terverifikasi, buka menu Permohonan Keberatan, tambah pengajuan, isi kolomnya, lalu ajukan.',
                'keterangan_en' => 'Sign in with a verified account, open the Objection Request menu, add a submission, fill in the form, and send it.',
            ],
            [
                'berkas' => '2.png',
                'judul' => 'Mekanisme Permohonan Keberatan',
                'judul_en' => 'Objection Handling Mechanism',
                'keterangan' => 'Enam tahap penanganan keberatan, dari pencatatan berkas sampai tanggapan Atasan PPID paling lambat 30 hari, serta jalur sengketa ke Komisi Informasi dalam 14 hari kerja.',
                'keterangan_en' => 'The six stages of handling an objection, from registering the files to the PPID superior\'s response within 30 days, and the route to the Information Commission within 14 working days.',
            ],
            [
                'berkas' => '3.png',
                'judul' => 'Alasan Pengajuan Keberatan',
                'judul_en' => 'Grounds for Filing an Objection',
                'keterangan' => 'Tujuh alasan yang sah, antara lain penolakan permintaan informasi, permintaan yang tidak ditanggapi, tanggapan yang melebihi batas waktu, dan pengenaan biaya yang tidak wajar.',
                'keterangan_en' => 'The seven valid grounds, among them a rejected request, an unanswered request, a response beyond the time limit, and unreasonable fees.',
            ],
        ],
    ];

    public function run(): void
    {
        foreach (self::HALAMAN as $halaman => $daftar) {
            $this->isiHalaman($halaman, $daftar);
        }
    }

    private function isiHalaman(string $halaman, array $daftar): void
    {
        if (AlurProsedur::where('halaman', $halaman)->exists()) {
            $this->command?->info("Alur bergambar $halaman sudah ada — dilewati.");

            return;
        }

        $folder = base_path('../standar layanan/'.$halaman);
        $dibuat = 0;

        foreach ($daftar as $i => $baris) {
            $sumber = $folder.'/'.$baris['berkas'];

            if (!is_file($sumber)) {
                $this->command?->warn('Berkas "'.$halaman.'/'.$baris['berkas'].'" tidak ditemukan di folder "standar layanan" — dilewati.');

                continue;
            }

            $path = 'uploads/alur-prosedur/'.now()->format('Y/m').'/'.Str::random(32).'.png';

            Storage::disk('media')->put($path, file_get_contents($sumber));

            AlurProsedur::create([
                'halaman' => $halaman,
                'judul' => $baris['judul'],
                'judul_en' => $baris['judul_en'],
                'keterangan' => $baris['keterangan'],
                'keterangan_en' => $baris['keterangan_en'],
                'gambar' => $path,
                'urutan' => $i + 1,
                'is_active' => true,
            ]);

            $dibuat++;
        }

        $this->command?->info("Alur bergambar $halaman dibuat: $dibuat gambar.");
    }
}
