<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\DownloadReportMail; // Asumsikan Anda membuat Mailable ini
use Illuminate\Support\Facades\Log;

class PpidController extends Controller
{
    public function sendDownloadLink(Request $request)
    {
        // 1. Validasi Data
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'email' => 'required|email|max:255',
            'purpose' => 'required|in:Pribadi,Instansi',
            'institution' => 'nullable|required_if:purpose,Instansi|string|max:255',
            'reportTitle' => 'required|string|max:255',
        ]);

        // 2. Kirim Email
        try {
            $downloadUrl = 'https://link-download-laporan-fstj.com/file/' . \Illuminate\Support\Str::slug($request->reportTitle);

            Mail::to($request->email)->send(new DownloadReportMail($request->name, $downloadUrl, $request->reportTitle));

            // 3. Respon Sukses
            return response()->json([
                'success' => true,
                'message' => 'Tautan unduh berhasil dikirim.',
                'email' => $request->email,
                'title' => $request->reportTitle,
            ]);

        } catch (\Exception $e) {
            // Log error
            Log::error('Gagal mengirim email download: ' . $e->getMessage());

            // 4. Respon Gagal
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengirim tautan unduh.',
            ], 500);
        }
    }


    public function showProfilePage($slug)
    {
        $profileData = [
            'singkat' => [
                'title' => 'Profil Singkat',
                'intro' => 'Pejabat Pengelola Informasi dan Dokumentasi (PPID) PT Food Station Tjipinang Jaya (Perseroda) adalah pejabat yang bertanggung jawab dalam pengelolaan, pendokumentasian, penyimpanan, penyediaan, serta pelayanan informasi publik di lingkungan perusahaan. PPID berperan penting dalam mewujudkan transparansi, akuntabilitas, dan pelayanan informasi yang cepat, tepat, dan mudah diakses oleh masyarakat.',
                'functions' => [
                    'Melaksanakan pembinaan, pengelolaan, dan pelayanan informasi serta dokumentasi di seluruh unit kerja PT Food Station Tjipinang Jaya (Perseroda).'
                ],
                'authorities' => [
                    'Menetapkan apakah suatu informasi publik dapat diakses atau dikecualikan melalui Uji Konsekuensi bersama Atasan PPID.',
                    'Menolak permohonan Informasi Publik secara tertulis apabila termasuk kategori informasi yang dikecualikan, dengan memberikan alasan penolakan serta penjelasan hak dan tata cara pengajuan keberatan bagi pemohon.',
                    'Menghadiri rapat koordinasi atau pembahasan terkait PPID di tingkat Provinsi DKI Jakarta.',
                    'Melakukan koordinasi dengan perangkat PPID dan/atau unit terkait dalam penanganan permohonan informasi maupun penyelesaian keberatan.',
                    'Melakukan pembaruan dan penyediaan informasi publik terkini melalui portal resmi PT Food Station Tjipinang Jaya (Perseroda) dan/atau Sistem Informasi PPID.',
                    'Melaporkan setiap ketidaksesuaian dalam proses sengketa informasi publik kepada Sekretariat Komisi Informasi dengan persetujuan Atasan PPID.',
                    'Melaksanakan sosialisasi dan edukasi internal guna meningkatkan pemahaman terhadap keterbukaan informasi publik di lingkungan perusahaan.',
                ],
                'service_hours' => [
                    'Senin s.d. Kamis: Pukul 08.00 – 15.00 WIB (Istirahat pukul 12.00 – 13.00 WIB)',
                    'Jum’at: Pukul 08.00 – 15.00 WIB (Istirahat pukul 11.30 – 13.30 WIB)'
                ]
            ],
            'struktur' => [
                'title' => 'Struktur PPID',
                'content' => 'Struktur PPID FSTJ dipimpin oleh seorang Atasan PPID (Direktur Utama) yang mendelegasikan tugas kepada Pejabat Pengelola Informasi dan Dokumentasi (PPID) Utama. PPID Utama dibantu oleh PPID Pelaksana yang bertugas melayani permohonan informasi. Staf Teknis yang terdiri dari berbagai unit kerja memastikan dokumen tersedia dan terbaharui.'
            ],
            'visi-misi' => [
                'title' => 'Visi dan Misi',
                'content' => [
                    'Visi' => 'Menjadi penyedia informasi publik terbaik di sektor pangan, menjamin transparansi dan akuntabilitas perusahaan untuk kepentingan publik.',
                    'Misi' => [
                        'Menyediakan layanan informasi yang cepat, mudah diakses, dan akurat.',
                        'Membangun sistem dokumentasi yang modern dan terintegrasi.',
                        'Meningkatkan kompetensi SDM PPID agar profesional dalam pelayanan.'
                    ]
                ]
            ],
        ];

        $key = strtolower($slug);

        if (!array_key_exists($key, $profileData)) {
            abort(404, 'Halaman profil tidak ditemukan.');
        }

        $data = $profileData[$key];

        return view('ppid.profile', compact('data', 'slug'));
    }

    public function showPublicInformation($slug)
    {
        $informationData = [
            'berkala' => [
                'title' => 'Informasi Berkala',
                'description' => 'Informasi yang wajib diumumkan secara rutin dan teratur, sekurang-kurangnya setiap 6 (enam) bulan sekali.',
                'items' => [
                    ['no' => 1, 'name' => 'INFORMASI UMUM FS'],
                    ['no' => 2, 'name' => 'PROFIL DEWAN KOMISARIS & DIREKSI'],
                    ['no' => 3, 'name' => 'STRUKTUR ORGANISASI'],
                    ['no' => 4, 'name' => 'SIARAN MEDIA'],
                    ['no' => 5, 'name' => 'COMPANY PROFILE VIDEO'],
                    ['no' => 6, 'name' => 'COMPANY PROFILE CETAK'],
                    ['no' => 7, 'name' => 'ANNUAL REPORT'],
                ]
            ],
            'serta-merta' => [
                'title' => 'Informasi Serta Merta',
                'description' => 'Informasi yang wajib diumumkan tanpa penundaan dan wajib tersedia segera setelah diketahui, karena menyangkut hajat hidup orang banyak dan/atau ketertiban umum.',
                'items' => [
                    ['no' => 1, 'name' => 'DATA KEGIATAN KORPORASI'],
                    ['no' => 2, 'name' => 'KEGIATAN TANGGUNG JAWAB PERUSAHAAN (CSR)'],
                    ['no' => 3, 'name' => 'TUGAS DAN WEWENANG PPID'],
                    ['no' => 4, 'name' => 'FOTO ASET (GEDUNG)'],
                    ['no' => 5, 'name' => 'RINGKASAN LAPORAN KSD PERUSAHAAN TRIWULANAN'],
                ]
            ],
            'setiap-saat' => [
                'title' => 'Informasi Setiap Saat',
                'description' => 'Informasi yang dapat diakses oleh pemohon informasi publik setiap saat, baik secara langsung maupun tidak langsung.',
                'items' => [
                    ['no' => 1, 'name' => 'MEMORANDUM OF UNDERSTANDING (MOU)'],
                    ['no' => 2, 'name' => 'PERJANJIAN KERJASAMA DAN ADDENDUM'],
                    ['no' => 3, 'name' => 'NON DISCLOSURE AGREEMENT (NDA)'],
                    ['no' => 4, 'name' => 'HARGA PROMO PENJUALAN'],
                    ['no' => 5, 'name' => 'AGENDA PASAR MURAH'],
                ]
            ],
        ];

        $key = \Illuminate\Support\Str::slug($slug);

        if (!array_key_exists($key, $informationData)) {
            abort(404, 'Halaman Informasi Publik tidak ditemukan.');
        }

        $data = $informationData[$key];

        return view('ppid.information', compact('data', 'slug'));
    }


    public function showRegulationPage()
    {
        $regulations = [
            [
                'no' => 1,
                'title' => 'Peraturan Direksi tentang Pedoman Keterbukaan Informasi Publik',
                'number' => 'No. 12 Tahun 2023',
                'date' => '2023-11-15',
                'type' => 'Internal PPID',
                'link' => '#regulasi-1'
            ],
            [
                'no' => 2,
                'title' => 'Keputusan Direksi tentang Standar Pelayanan Informasi Publik',
                'number' => 'No. 05 Tahun 2022',
                'date' => '2022-07-20',
                'type' => 'Internal PPID',
                'link' => '#regulasi-2'
            ],
            [
                'no' => 3,
                'title' => 'Undang-Undang Nomor 14 Tahun 2008 tentang Keterbukaan Informasi Publik (KIP)',
                'number' => 'UU No. 14/2008',
                'date' => '2008-04-30',
                'type' => 'Hukum Negara',
                'link' => '#regulasi-3'
            ],
            [
                'no' => 4,
                'title' => 'Peraturan Komisi Informasi Nomor 1 Tahun 2021 tentang Standar Layanan Informasi Publik',
                'number' => 'Perki No. 1/2021',
                'date' => '2021-09-01',
                'type' => 'Peraturan KI',
                'link' => '#regulasi-4'
            ],
        ];

        $data = [
            'title' => 'Regulasi dan Pedoman',
            'description' => 'Daftar peraturan internal perusahaan dan hukum terkait yang menjadi pedoman dalam penyelenggaraan Keterbukaan Informasi Publik (KIP) PPID PT Food Station Tjipinang Jaya.',
            'regulations' => $regulations
        ];

        return view('ppid.regulation', compact('data'));
    }

    public function showServiceStandardPage($slug)
    {
        // Data dummy Standar Pelayanan
        $standardData = [
            'maklumat-pelayanan' => [
                'title' => 'Maklumat Pelayanan',
                'intro' => 'Dengan ini, Pejabat Pengelola Informasi dan Dokumentasi (PPID) PT Food Station Tjipinang Jaya menyatakan kesanggupan kami untuk melaksanakan pelayanan informasi publik sesuai dengan standar yang telah ditetapkan.',
                'content_list' => [
                    'Kami berkomitmen memberikan pelayanan informasi publik yang cepat, mudah, dan transparan.',
                    'Kami akan menanggapi permohonan informasi publik dalam jangka waktu yang ditetapkan oleh Undang-Undang KIP.',
                    'Kami siap menerima dan memproses keberatan atas penolakan atau ketidakpuasan pelayanan informasi.',
                    'Kami menjamin kerahasiaan identitas Pemohon kecuali diwajibkan oleh undang-undang.'
                ],
                'footer' => 'Apabila kami tidak menepati janji ini, kami siap menerima sanksi sesuai ketentuan peraturan perundang-undangan.'
            ],
            'prosedur-permohonan' => [
                'title' => 'Prosedur Permohonan Informasi Publik',
                'intro' => 'Langkah-langkah yang harus dilalui pemohon untuk mendapatkan informasi publik dari PPID PT Food Station Tjipinang Jaya.',
                'steps' => [
                    'Pemohon mengisi Formulir Permohonan Informasi secara lengkap (online/langsung).',
                    'Pemohon menyerahkan salinan identitas diri (KTP/SIM/Paspor) atau Akta Pendirian bagi Instansi.',
                    'Petugas mencatat permohonan dan memberikan Tanda Bukti Penerimaan.',
                    'PPID memproses permintaan dan memberikan pemberitahuan tertulis dalam jangka waktu 10 hari kerja (dapat diperpanjang 7 hari kerja).',
                    'Pemohon mengambil atau menerima informasi publik sesuai format yang diminta, setelah melunasi biaya (jika ada).'
                ]
            ],
            'jalur-waktu-layanan' => [
                'title' => 'Jalur & Waktu Layanan Informasi',
                'intro' => 'Informasi mengenai jam operasional dan pilihan saluran yang dapat digunakan pemohon untuk mengakses pelayanan PPID.',
                'channels' => [
                    'Online: Melalui Formulir Permohonan di website resmi PPID (Direkomendasikan).',
                    'Langsung: Datang ke meja layanan PPID pada jam operasional.',
                    'Surat: Mengirimkan surat permohonan ke alamat kantor PPID.'
                ],
                'hours' => [
                    'Senin s.d. Kamis: Pukul 08.00 – 15.00 WIB (Istirahat 12.00 – 13.00 WIB)',
                    'Jum’at: Pukul 08.00 – 15.00 WIB (Istirahat 11.30 – 13.30 WIB)'
                ],
                'note' => 'Permohonan di luar jam kerja akan diproses pada hari kerja berikutnya.'
            ]
        ];

        $key = \Illuminate\Support\Str::slug($slug);

        if (!array_key_exists($key, $standardData)) {
            abort(404, 'Halaman Standar Pelayanan tidak ditemukan.');
        }

        $data = $standardData[$key];

        return view('ppid.service_standard', compact('data', 'slug'));
    }


    public function showRequestForm()
    {
        return view('ppid.request_form');
    }

    public function submitRequest(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'id_number' => 'required|string|max:30',
            'address' => 'required|string|max:500',
            'phone' => 'required|string|max:15',
            'email' => 'required|email|max:255',
            'applicant_type' => 'required|in:Pribadi,Instansi,Kelompok',
            'institution_name' => 'nullable|required_if:applicant_type,Instansi,Kelompok|string|max:255',
            'info_needed' => 'required|string|max:1000',
            'purpose' => 'required|string|max:1000',
            'format' => 'required|in:softcopy,hardcopy',
            'way_to_get' => 'required|in:ambil_langsung,email,pos',
        ]);

        $registrationNumber = 'PPID-FSTJ/' . date('Ymd') . '/' . rand(1000, 9999);

        return response()->json([
            'success' => true,
            'registration_number' => $registrationNumber,
            'applicant_name' => $request->full_name,
            'submission_date' => now()->translatedFormat('d F Y'),
        ]);
    }

    public function showObjectionForm()
    {
        return view('ppid.objection_form');
    }

    public function submitObjection(Request $request)
    {
        $request->validate([
            'registration_number' => 'required|string|max:255',
            'objection_reason' => 'required|string',
            'objection_purpose' => 'required|string|max:1000',
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'email' => 'required|email|max:255',
        ]);

        $objectionNumber = 'KBRT-FSTJ/' . date('Ymd') . '/' . rand(100, 999);

        return response()->json([
            'success' => true,
            'objection_number' => $objectionNumber,
            'applicant_name' => $request->full_name,
            'submission_date' => now()->translatedFormat('d F Y'),
        ]);
    }

    public function showStatusCheck()
    {
        return view('ppid.status_check');
    }

    public function checkRequestStatus(Request $request)
    {
        $request->validate([
            'registration_number' => 'required|string|max:255',
        ]);

        $regNumber = strtoupper($request->registration_number);

        $lastDigits = substr($regNumber, -4);
        $status = 'DALAM PENELITIAN';
        $responseDate = null;
        $infoRequested = 'Laporan yang Dimohonkan (Contoh Data)';

        if ($lastDigits === '1000') {
            $status = 'DIPROSES';
        } elseif ($lastDigits === '2000') {
            $status = 'DITERIMA';
            $responseDate = now()->subDays(2)->translatedFormat('d F Y');
        } elseif ($lastDigits === '3000') {
            $status = 'DITOLAK';
            $responseDate = now()->subDays(1)->translatedFormat('d F Y');
        } elseif ($lastDigits === '9999') {
            $status = 'TIDAK DITEMUKAN';
        } else {
            $status = 'DALAM PENELITIAN';
        }

        return response()->json([
            'success' => true,
            'number' => $regNumber,
            'status' => $status,
            'response_date' => $responseDate,
            'info_requested' => $infoRequested,
        ]);
    }


}
