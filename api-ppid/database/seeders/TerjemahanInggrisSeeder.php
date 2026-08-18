<?php

namespace Database\Seeders;

use App\Models\Berita;
use App\Models\Faq;
use App\Models\HalamanStatis;
use App\Models\InformasiDikecualikan;
use App\Models\InformasiPublik;
use App\Models\KategoriBerita;
use App\Models\KategoriInformasi;
use App\Models\Regulasi;
use App\Models\StrukturOrganisasi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

/**
 * Isi awal kolom `*_en` untuk data yang sudah ada di CMS.
 *
 * Seeder ini **tidak menimpa** terjemahan yang sudah diisi petugas: kolom
 * hanya diisi bila masih kosong. Barisnya dicocokkan lewat teks Indonesianya,
 * bukan id, supaya aman dijalankan di basis data mana pun.
 *
 * Isi baru yang ditambahkan lewat be-ppid setelah ini diterjemahkan langsung
 * dari formulirnya (field "… (English)"), bukan dari seeder.
 */
class TerjemahanInggrisSeeder extends Seeder
{
    public function run(): void
    {
        $jumlah = 0;

        $jumlah += $this->isi(KategoriInformasi::class, 'nama', [
            'Informasi Berkala' => [
                'nama_en' => 'Periodic Information',
                'deskripsi_en' => 'Information that must be provided and announced periodically, at least once every 6 (six) months.',
            ],
            'Informasi Serta Merta' => [
                'nama_en' => 'Immediate Information',
                'deskripsi_en' => 'Information that must be announced without delay because it concerns the livelihood of many people and/or public order.',
            ],
            'Informasi Tersedia Setiap Saat' => [
                'nama_en' => 'Information Available at All Times',
                'deskripsi_en' => 'Information that public information applicants can access at any time, either directly or indirectly.',
            ],
        ]);

        $jumlah += $this->isi(InformasiPublik::class, 'judul', [
            'Profil dan Sejarah Perusahaan' => ['judul_en' => 'Company Profile and History', 'ringkasan_en' => 'Company profile and history.'],
            'Profil Singkat Pimpinan Perusahaan' => ['judul_en' => 'Brief Profile of Company Leadership'],
            'Visi dan Misi Perusahaan' => ['judul_en' => 'Company Vision and Mission'],
            'Tugas Pokok dan Fungsi Perusahaan' => ['judul_en' => 'Company Duties and Functions'],
            'Core Value/Nilai Perusahaan' => ['judul_en' => 'Company Core Values'],
            'Struktur Organisasi Perusahaan' => ['judul_en' => 'Company Organisational Structure'],
            'Kontak Perusahaan dan Jam Layanan' => ['judul_en' => 'Company Contact and Service Hours'],
            'Siaran Media' => ['judul_en' => 'Media Releases', 'ringkasan_en' => 'Press releases and official news published by the company.'],
            'Company Profile Video' => ['judul_en' => 'Company Profile Video', 'ringkasan_en' => 'Company profile in video form.'],
            'Annual Report' => ['judul_en' => 'Annual Report', 'ringkasan_en' => "The company's annual report covering performance and achievements throughout the financial year."],
            'Informasi Kebencanaan' => ['judul_en' => 'Disaster Information', 'ringkasan_en' => 'Disaster information.'],
            'Karir (Lowongan Pekerjaan)' => ['judul_en' => 'Careers (Job Vacancies)'],
            'Informasi Terkait Fasilitas Lini Bisnis Perusahaan' => [
                'judul_en' => "Information on the Company's Business Line Facilities",
                'ringkasan_en' => "Information on the company's business line facilities.",
            ],
            'Informasi Perubahan Jam Operasional Perusahaan' => ['judul_en' => 'Information on Changes to Company Operating Hours'],
            'Kegiatan Korporasi' => ['judul_en' => 'Corporate Activities'],
            'Logo Perusahaan' => ['judul_en' => 'Company Logo'],
            'Jam Operasional Perusahaan' => ['judul_en' => 'Company Operating Hours'],
            'Daftar Informasi Publik' => ['judul_en' => 'Public Information List'],
            'Daftar Informasi Dikecualikan' => [
                'judul_en' => 'Excluded Information List',
                'ringkasan_en' => "Information on promotional prices for the company's products.",
            ],
            'Agenda Pasar Murah' => [
                'judul_en' => 'Affordable Market Schedule',
                'ringkasan_en' => 'Schedule and locations of affordable market events held by the company.',
            ],
        ]);

        $jumlah += $this->isi(InformasiDikecualikan::class, 'judul', [
            'Dokumen Kontrak Pengadaan Beras dengan Pihak Ketiga' => [
                'judul_en' => 'Rice Procurement Contracts with Third Parties',
                'ringkasan_en' => "Rice procurement agreements containing unit prices and the company's negotiation strategy.",
            ],
            'Data Pribadi Pemohon Informasi Publik' => [
                'judul_en' => 'Personal Data of Public Information Applicants',
                'ringkasan_en' => 'Identity, national ID number, address, and contact details of public information applicants.',
            ],
            'Data Pelanggan (Perjanjian Sewa Lahan dan Properti/Bisnis lain)' => ['judul_en' => 'Customer Data (Land, Property, and Other Business Lease Agreements)'],
            'Data Pribadi Pegawai' => ['judul_en' => 'Employee Personal Data'],
            'Disposisi Surat Pimpinan' => ['judul_en' => 'Management Letter Dispositions'],
            'Dokumen Hasil Pemeriksaan Perusahaan' => ['judul_en' => 'Company Audit Result Documents'],
            'Dokumen SPI (Pertanggungjawaban Keuangan)' => ['judul_en' => 'Internal Audit Documents (Financial Accountability)'],
            'Informasi Upah Pegawai Food Station' => ['judul_en' => 'Food Station Employee Pay Information'],
            'Kebutuhan Karyawan Setiap Bidang dan Tes Calon Karyawan' => ['judul_en' => 'Staffing Needs per Division and Candidate Assessments'],
            'Lokasi Server' => ['judul_en' => 'Server Locations'],
            'Materi Perselisihan Hubungan Industri' => ['judul_en' => 'Industrial Relations Dispute Materials'],
            'Korespondensi Internal dan Eksternal Perusahaan' => ['judul_en' => 'Internal and External Company Correspondence'],
            'Notulensi Perusahaan' => ['judul_en' => 'Company Meeting Minutes'],
            'Pendapatan dari Semua Sektor' => ['judul_en' => 'Revenue from All Sectors'],
            'Surat Keputusan Direksi' => ['judul_en' => 'Board of Directors Decrees'],
            'Perincian Laporan Keuangan Perusahaan dan Bukti-bukti terkait aktivitas perusahaan' => [
                'judul_en' => 'Detailed Company Financial Statements and Supporting Records of Company Activities',
            ],
            'Perjanjian Kerja Karyawan' => ['judul_en' => 'Employee Employment Agreements'],
            'Rencana Kerja Anggaran Perusahaan/RJPP/Master Plan (Tahun Berjalan)' => [
                'judul_en' => 'Company Work and Budget Plan / Long-Term Plan / Master Plan (Current Year)',
            ],
            'Somasi dan surat keberatan/penolakan dari individu/kelompok masyarakat untuk diterbitkan izin/non izin' => [
                'judul_en' => 'Legal Notices and Objection or Rejection Letters from Individuals and Community Groups Regarding Permit and Non-Permit Issuance',
            ],
            'Strategi Perseroan Kedepan' => ['judul_en' => 'Future Corporate Strategy'],
            'Data Kajian Bisnis Perusahaan' => ['judul_en' => 'Company Business Study Data'],
            'Uraian Lengkap Hasil Assessment Pegawai' => ['judul_en' => 'Full Details of Employee Assessment Results'],
            'Perjanjian Kerjasama dengan Pihak Ketiga' => ['judul_en' => 'Cooperation Agreements with Third Parties'],
            'Dokumen Pertanahan' => ['judul_en' => 'Land Documents'],
        ]);

        $jumlah += $this->isi(Regulasi::class, 'judul', [
            'Undang-Undang Republik Indonesia Nomor 14 Tahun 2008 tentang Keterbukaan Informasi Publik' => [
                'judul_en' => 'Law of the Republic of Indonesia Number 14 of 2008 on Public Information Disclosure',
                'ringkasan_en' => "A law guaranteeing everyone's right to obtain public information. It sets out public bodies' obligation to provide information, the categories of excluded information, and the objection and information dispute mechanisms.",
            ],
            'Peraturan Pemerintah Nomor 61 Tahun 2010 tentang Pelaksanaan Undang-Undang Nomor 14 Tahun 2008 tentang Keterbukaan Informasi Publik' => [
                'judul_en' => 'Government Regulation Number 61 of 2010 on the Implementation of Law Number 14 of 2008 on Public Information Disclosure',
                'ringkasan_en' => 'Implementing regulation of the Public Information Disclosure Law: appointment of the PPID, procedures for announcing and serving information, the consequence test, and exclusion periods.',
            ],
            'Peraturan Direksi tentang Pedoman Keterbukaan Informasi Publik' => [
                'judul_en' => 'Board of Directors Regulation on Public Information Disclosure Guidelines',
            ],
            'Peraturan Komisi Informasi Republik Indonesia Nomor 1 Tahun 2021 tentang Standar Layanan Informasi Publik' => [
                'judul_en' => 'Regulation of the Information Commission of the Republic of Indonesia Number 1 of 2021 on Public Information Service Standards',
                'ringkasan_en' => "Information Commission regulation on Public Information Service Standards: public bodies' obligations, request handling procedures, response deadlines, and service reporting.",
            ],
            'Undang-Undang Republik Indonesia Nomor 25 Tahun 2009 tentang Pelayanan Publik' => [
                'judul_en' => 'Law of the Republic of Indonesia Number 25 of 2009 on Public Services',
                'ringkasan_en' => 'A law on public services: principles and objectives, service standards, the rights and obligations of both providers and the public, and complaint handling.',
            ],
            'Peraturan Komisi Informasi Pusat Nomor 1 Tahun 2003 tentang Prosedur Penyelesaian Sengketa Informasi Publik' => [
                'judul_en' => 'Regulation of the Central Information Commission Number 1 of 2003 on Procedures for Settling Public Information Disputes',
                'ringkasan_en' => 'Information Commission regulation on procedures for settling public information disputes, from filing a request through mediation to non-litigation adjudication.',
            ],
            'Peraturan Gubernur Nomor 175 Tahun 2016 tentang Layanan Informasi Publik' => [
                'judul_en' => 'Governor Regulation Number 175 of 2016 on Public Information Services',
                'ringkasan_en' => 'DKI Jakarta Governor Regulation on the delivery of public information services across the DKI Jakarta Provincial Government and its PPID apparatus.',
            ],
            'Peraturan Pemerintah Nomor 54 Tahun 2017 tentang Badan Usaha Milik Daerah' => [
                'judul_en' => 'Government Regulation Number 54 of 2017 on Regionally Owned Enterprises',
                'ringkasan_en' => 'Government Regulation on Regionally Owned Enterprises: establishment, management, governance, supervision, and disclosure obligations.',
            ],
            'Peraturan Daerah Nomor 4 Tahun 2023 tentang Perubahan Bentuk Hukum Perseroan Terbatas Food Station Tjipinang Jaya menjadi Perseroan Terbatas Food Station Tjipinang Jaya (Perseroan Daerah)' => [
                'judul_en' => 'Regional Regulation Number 4 of 2023 on the Change of Legal Form of PT Food Station Tjipinang Jaya into PT Food Station Tjipinang Jaya (Regionally Owned Company)',
                'ringkasan_en' => 'Regional Regulation on the change of legal form of PT Food Station Tjipinang Jaya into a regionally owned limited company, including its capital and governance.',
            ],
        ]);

        $jumlah += $this->isi(Faq::class, 'pertanyaan', [
            'Bagaimana cara mengajukan permohonan informasi publik?' => [
                'pertanyaan_en' => 'How do I submit a public information request?',
                'jawaban_en' => 'Requests can be submitted online through the Information Request menu on this portal by completing the form provided, or in person at the PPID office during service hours.',
                'kategori_en' => 'Information Services',
            ],
            'Berapa lama waktu tanggapan atas permohonan?' => [
                'pertanyaan_en' => 'How long does a response to a request take?',
                'jawaban_en' => 'Under Law No. 14 of 2008, the PPID must respond to a request within 10 working days of receiving it, extendable by a further 7 working days where necessary.',
                'kategori_en' => 'Information Services',
            ],
            'Apakah layanan informasi publik dikenakan biaya?' => [
                'pertanyaan_en' => 'Is there a charge for public information services?',
                'jawaban_en' => 'Public information services are free of charge, except for reasonable copying or delivery costs, which are stated up front.',
                'kategori_en' => 'Information Services',
            ],
            'Apa yang bisa dilakukan jika permohonan ditolak?' => [
                'pertanyaan_en' => 'What can I do if my request is refused?',
                'jawaban_en' => "Applicants may file a written objection with the PPID's superior through the Objection Submission menu on this portal, within 30 working days of receiving the refusal.",
                'kategori_en' => 'Information Services',
            ],
            'Bagaimana cara melacak status permohonan yang sudah diajukan?' => [
                'pertanyaan_en' => 'How do I track the status of a request I have submitted?',
                'jawaban_en' => 'Use the Check Service Status menu and enter the ticket number you received when submitting the request to see its progress.',
                'kategori_en' => 'Information Services',
            ],
        ]);

        $jumlah += $this->isi(StrukturOrganisasi::class, 'jabatan', [
            'Atasan PPID' => ['jabatan_en' => 'PPID Supervisor'],
            'PPID' => ['jabatan_en' => 'PPID'],
            'Tim Pertimbangan PPID' => [
                'jabatan_en' => 'PPID Advisory Team',
                'poin_en' => "Risk Management\nLegal & Compliance\nAll Company Division and Department Heads",
            ],
            'PPID Pelaksana' => ['jabatan_en' => 'Implementing PPID'],
            'Pengelolaan & Dokumentasi Informasi' => ['jabatan_en' => 'Information Management & Documentation'],
            'Pengumuman Informasi' => ['jabatan_en' => 'Information Announcement'],
            'Penyediaan Informasi' => ['jabatan_en' => 'Information Provision'],
        ]);

        $jumlah += $this->isi(KategoriBerita::class, 'nama', [
            'Operasional' => ['nama_en' => 'Operations'],
            'Prestasi' => ['nama_en' => 'Achievements'],
            'CSR' => ['nama_en' => 'CSR'],
            'Siaran Pers' => ['nama_en' => 'Press Releases'],
        ]);

        $jumlah += $this->isi(Berita::class, 'judul', [
            'Tes CSR' => ['judul_en' => 'CSR Test'],
        ]);

        $jumlah += $this->isi(HalamanStatis::class, 'judul', [
            'Tugas, Fungsi dan Wewenang' => [
                'judul_en' => 'Duties, Functions and Authority',
                'konten_en' => '<h3>Functions of the PPID of PT Food Station Tjipinang Jaya (Perseroda)</h3>'
                    .'<ul><li>Carrying out the development, management, and delivery of information and documentation services across all work units of PT Food Station Tjipinang Jaya (Perseroda).</li></ul>'
                    .'<h3>Authority of the PPID of PT Food Station Tjipinang Jaya (Perseroda)</h3>'
                    .'<ul>'
                    .'<li>Determining, through a Consequence Test together with the PPID Supervisor, whether a piece of public information may be accessed or is excluded.</li>'
                    .'<li>Refusing a public information request in writing where it falls within the excluded categories, stating the grounds for refusal and explaining the applicant\'s right and the procedure for filing an objection.</li>'
                    .'<li>Attending coordination meetings and discussions relating to the PPID at DKI Jakarta Province level.</li>'
                    .'<li>Coordinating with PPID officers and/or related units in handling information requests and resolving objections.</li>'
                    .'<li>Updating and providing current public information through the official portal of PT Food Station Tjipinang Jaya (Perseroda) and/or the PPID Information System.</li>'
                    .'<li>Reporting any irregularity in the public information dispute process to the Secretariat of the Information Commission with the approval of the PPID Supervisor.</li>'
                    .'<li>Conducting internal outreach and education to improve understanding of public information disclosure within the company.</li>'
                    .'</ul>',
            ],
        ]);

        $this->command?->info("Terjemahan Inggris terisi: $jumlah kolom.");
    }

    /**
     * Isi kolom `*_en` pada baris yang cocok, tanpa menimpa isian yang sudah ada.
     *
     * @param  class-string<Model>  $model
     * @param  array<string, array<string, string>>  $peta  teks Indonesia => [kolom_en => terjemahan]
     */
    private function isi(string $model, string $kunci, array $peta): int
    {
        $terisi = 0;

        foreach ($peta as $teksAsli => $terjemahan) {
            /** @var Model|null $baris */
            $baris = $model::where($kunci, $teksAsli)->first();

            if (!$baris) {
                $this->command?->warn("Lewati (tidak ada barisnya): $teksAsli");
                continue;
            }

            $ubah = [];

            foreach ($terjemahan as $kolom => $nilai) {
                if (blank($baris->{$kolom})) {
                    $ubah[$kolom] = $nilai;
                }
            }

            if ($ubah) {
                $baris->forceFill($ubah)->save();
                $terisi += count($ubah);
            }
        }

        return $terisi;
    }
}
