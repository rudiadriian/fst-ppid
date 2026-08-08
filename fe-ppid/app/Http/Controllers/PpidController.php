<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\DownloadReportMail; // Asumsikan Anda membuat Mailable ini
use Illuminate\Support\Facades\Log;
use App\Models\InformasiDikecualikan;
use App\Models\LaporanLayanan;
use App\Models\PermohonanInformasi;
use App\Models\Regulasi;

class PpidController extends Controller
{
    /** true bila query terakhir ke DB gagal, dipakai view untuk memberi tahu pengunjung. */
    private bool $dbOffline = false;

    /**
     * Bungkus query ke database ppiddb. Kalau DB/backend sedang bermasalah,
     * halaman tetap terbuka dengan data fallback — frontend tidak ikut mati.
     */
    private function fromDatabase(callable $query, $fallback, string $context)
    {
        try {
            return $query();
        } catch (\Throwable $e) {
            $this->dbOffline = true;
            Log::warning("[PPID] Query {$context} gagal: " . $e->getMessage());

            return $fallback;
        }
    }

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
                    ['days' => 'Senin s.d. Kamis', 'time' => '08.00 – 15.00 WIB', 'break' => '12.00 – 13.00 WIB'],
                    ['days' => 'Jum’at', 'time' => '08.00 – 15.00 WIB', 'break' => '11.30 – 13.30 WIB'],
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

        // Halaman yang sudah dibuat di modul Halaman Statis boleh tidak ada di
        // daftar bawaan — CMS bisa menambah halaman profil baru tanpa deploy.
        $halaman = $this->fromDatabase(
            fn () => \App\Models\HalamanStatis::where('is_active', true)
                ->whereIn('slug', [$key, 'profil-'.$key])
                ->first(),
            null,
            'halaman_statis'
        );

        if (!$halaman && !array_key_exists($key, $profileData)) {
            abort(404, 'Halaman profil tidak ditemukan.');
        }

        $data = $profileData[$key] ?? ['title' => $halaman->judul];

        if ($halaman) {
            $data['title'] = $halaman->judul;
            $data['html'] = $halaman->konten;
        }

        // Struktur PPID selalu memakai data pejabat dari CMS bila sudah diisi.
        if ($key === 'struktur') {
            $anggota = $this->fromDatabase(
                fn () => \App\Models\StrukturOrganisasi::aktif()->get(),
                collect(),
                'struktur_organisasi'
            );

            $data['anggota'] = $anggota->map(fn ($a) => [
                'nama' => $a->nama,
                'jabatan' => $a->jabatan,
                'foto' => $this->fileUrl($a->foto),
            ])->all();
        }

        $data['db_offline'] = $this->dbOffline;

        return view('ppid.profile', compact('data', 'slug'));
    }

    public function showPublicInformation($slug)
    {
        $key = \Illuminate\Support\Str::slug($slug);

        // Kategori dan daftar dokumennya dikelola lewat CMS (`be-ppid`).
        $kategori = $this->fromDatabase(
            fn () => \App\Models\KategoriInformasi::where('slug', $key)->where('is_active', true)->first(),
            null,
            'kategori_informasi'
        );

        // Tiga klasifikasi wajib menurut UU No. 14 Tahun 2008 selalu punya
        // halaman, walau kategorinya belum dibuat di CMS — tabelnya tampil
        // kosong, bukan 404.
        $bawaan = [
            'berkala' => ['Informasi Berkala', 'Informasi yang wajib diumumkan secara rutin dan teratur, sekurang-kurangnya setiap 6 (enam) bulan sekali.'],
            'serta-merta' => ['Informasi Serta Merta', 'Informasi yang wajib diumumkan tanpa penundaan karena menyangkut hajat hidup orang banyak dan/atau ketertiban umum.'],
            'setiap-saat' => ['Informasi Setiap Saat', 'Informasi yang dapat diakses oleh pemohon informasi publik setiap saat.'],
        ];

        if (!$kategori && !array_key_exists($key, $bawaan)) {
            abort(404, 'Halaman Informasi Publik tidak ditemukan.');
        }

        $rows = $kategori
            ? $this->fromDatabase(
                fn () => \App\Models\InformasiPublik::published()
                    ->with('files')
                    ->where('kategori_id', $kategori->id)
                    ->orderByDesc('tanggal_publikasi')
                    ->orderBy('judul')
                    ->get(),
                collect(),
                'informasi_publik'
            )
            : collect();

        $items = $rows->values()->map(function ($row, $i) {
            $berkas = $row->files->first();

            // Entri boleh berupa berkas unggahan atau tautan ke halaman lain.
            // Berkas didahulukan; kalau tidak ada, dipakai kolom `tautan`.
            $url = $berkas ? $this->fileUrl($berkas->path_file) : ($row->tautan ?: null);

            return [
                'no' => $i + 1,
                'name' => $row->judul,
                'ringkasan' => $row->ringkasan,
                'file' => $url,
                'jenis' => $berkas ? 'berkas' : ($row->tautan ? 'tautan' : null),
            ];
        })->all();

        // Sub-kategori (anak dari kategori ini) ditampilkan sebagai kartu
        // dengan tombol "Selengkapnya" yang menuju halamannya sendiri.
        // Kedalamannya bebas: sub-kategori boleh punya sub-kategori lagi.
        $subkategori = $kategori
            ? $this->fromDatabase(
                fn () => \App\Models\KategoriInformasi::where('parent_id', $kategori->id)
                    ->where('is_active', true)
                    ->withCount([
                        'informasiPublik as jumlah_dokumen' => fn ($q) => $q->where('status', 'published')->whereNull('deleted_at'),
                    ])
                    ->orderBy('urutan')
                    ->orderBy('nama')
                    ->get(),
                collect(),
                'subkategori_informasi'
            )
            : collect();

        // Kategori induk dipakai untuk breadcrumb dan tombol kembali.
        $induk = $kategori?->parent_id
            ? $this->fromDatabase(
                fn () => \App\Models\KategoriInformasi::where('id', $kategori->parent_id)->first(),
                null,
                'induk_kategori_informasi'
            )
            : null;

        $data = [
            'title' => $kategori->nama ?? $bawaan[$key][0],
            'description' => $kategori?->deskripsi ?: ($bawaan[$key][1] ?? 'Daftar informasi publik pada kategori ini.'),
            'items' => $items,
            'subkategori' => $subkategori->map(fn ($k) => [
                'nama' => $k->nama,
                'slug' => $k->slug,
                'deskripsi' => $k->deskripsi,
                'jumlah' => (int) ($k->jumlah_dokumen ?? 0),
            ])->all(),
            'induk' => $induk ? ['nama' => $induk->nama, 'slug' => $induk->slug] : null,
            'db_offline' => $this->dbOffline,
        ];

        return view('ppid.information', compact('data', 'slug'));
    }


    public function showRegulationPage()
    {
        // Kanal Layanan -> hanya kategori 'regulasi' & 'pedoman'.
        // Kanal Profil (Dasar Hukum) memakai kategori 'dasar_hukum_ppid', lihat showLegalBasisPage().
        $fromDb = $this->fromDatabase(
            fn () => $this->mapRegulasi(
                Regulasi::kategori(['regulasi', 'pedoman'])
                    ->orderByDesc('tahun')
                    ->orderBy('judul')
                    ->get()
            ),
            [],
            'regulasi'
        );

        if (!empty($fromDb)) {
            return view('ppid.regulation', ['data' => [
                'title'       => 'Regulasi dan Pedoman',
                'description' => 'Daftar peraturan internal perusahaan dan hukum terkait yang menjadi pedoman dalam penyelenggaraan Keterbukaan Informasi Publik (KIP) PPID PT Food Station Tjipinang Jaya.',
                'regulations' => $fromDb,
                'db_offline'  => false,
            ]]);
        }

        // Fallback: DB kosong atau tidak dapat dihubungi.
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
            'regulations' => $regulations,
            'db_offline' => $this->dbOffline,
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
                    ['label' => 'Online', 'desc' => 'Melalui Formulir Permohonan di website resmi PPID.', 'recommended' => true],
                    ['label' => 'Langsung', 'desc' => 'Datang ke meja layanan PPID pada jam operasional.', 'recommended' => false],
                    ['label' => 'Surat', 'desc' => 'Mengirimkan surat permohonan ke alamat kantor PPID.', 'recommended' => false],
                ],
                'hours' => [
                    ['days' => 'Senin s.d. Kamis', 'time' => '08.00 – 15.00 WIB', 'break' => '12.00 – 13.00 WIB'],
                    ['days' => 'Jum’at', 'time' => '08.00 – 15.00 WIB', 'break' => '11.30 – 13.30 WIB'],
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

    // =====================================================================
    // Kanal tambahan (sumber data: PostgreSQL ppiddb)
    // =====================================================================

    /**
     * Daftar Informasi Dikecualikan — kanal Informasi Publik.
     * Struktur berbeda dari informasi publik biasa: wajib memuat alasan
     * pengecualian, dasar hukum, jangka waktu, dan pejabat penetap.
     */
    public function showExcludedInformation()
    {
        $items = $this->fromDatabase(
            fn () => InformasiDikecualikan::published()
                ->orderByDesc('tanggal_penetapan')
                ->orderBy('judul')
                ->get()
                ->values()
                ->map(fn ($row, $i) => [
                    'no'           => $i + 1,
                    'judul'        => $row->judul,
                    'ringkasan'    => $row->ringkasan,
                    'alasan'       => $row->alasan_pengecualian,
                    'dasar_hukum'  => $row->dasar_hukum_pengecualian,
                    'jangka_waktu' => $row->jangka_waktu_pengecualian,
                    'tanggal'      => optional($row->tanggal_penetapan)->translatedFormat('d F Y'),
                    'file'         => $this->fileUrl($row->file_surat_penetapan),
                ])
                ->all(),
            [],
            'informasi_dikecualikan'
        );

        $data = [
            'title'       => 'Daftar Informasi Dikecualikan',
            'description' => 'Informasi yang dikecualikan dari keterbukaan informasi publik berdasarkan hasil uji konsekuensi, lengkap dengan alasan pengecualian, dasar hukum, dan jangka waktu pengecualiannya.',
            'items'       => $items,
            'db_offline'  => $this->dbOffline,
        ];

        return view('ppid.excluded_information', compact('data'));
    }

    /**
     * Laporan Statistik Informasi Publik & Laporan Pelayanan Informasi.
     */
    public function showReportPage($slug)
    {
        $tipeMap = [
            'statistik-informasi' => [
                'tipe'        => 'statistik_informasi',
                'title'       => 'Laporan Statistik Informasi Publik',
                'description' => 'Rekapitulasi angka permohonan informasi publik per periode: jumlah permohonan masuk, dikabulkan, ditolak, keberatan, dan rata-rata waktu respon.',
            ],
            'pelayanan-informasi' => [
                'tipe'        => 'pelayanan_informasi',
                'title'       => 'Laporan Pelayanan Informasi',
                'description' => 'Laporan periodik penyelenggaraan layanan informasi publik PPID PT Food Station Tjipinang Jaya (Perseroda).',
            ],
        ];

        if (!array_key_exists($slug, $tipeMap)) {
            abort(404, 'Halaman Laporan tidak ditemukan.');
        }

        $meta = $tipeMap[$slug];

        $reports = $this->fromDatabase(
            fn () => LaporanLayanan::published()
                ->tipe($meta['tipe'])
                ->orderByDesc('tahun')
                ->orderByDesc('id')
                ->get()
                ->map(fn ($row) => [
                    'judul'            => $row->judul,
                    'tahun'            => $row->tahun,
                    'periode'          => $row->periode ?: 'Tahunan',
                    'masuk'            => $row->jumlah_permohonan_masuk,
                    'dikabulkan'       => $row->jumlah_dikabulkan,
                    'ditolak'          => $row->jumlah_ditolak,
                    'ditolak_sebagian' => $row->jumlah_ditolak_sebagian,
                    'keberatan'        => $row->jumlah_keberatan,
                    'rata_rata_hari'   => $row->rata_rata_hari_respon,
                    'ringkasan'        => $row->ringkasan,
                    'file'             => $this->fileUrl($row->file_laporan),
                ])
                ->all(),
            [],
            'laporan_layanan'
        );

        // Kartu ringkasan memakai tahun terbaru yang tersedia.
        $latestYear = collect($reports)->max('tahun');
        $summary    = collect($reports)->where('tahun', $latestYear);

        $data = [
            'title'       => $meta['title'],
            'description' => $meta['description'],
            'slug'        => $slug,
            'reports'     => $reports,
            'db_offline'  => $this->dbOffline,
            'summary'     => $summary->isEmpty() ? null : [
                'tahun'      => $latestYear,
                'masuk'      => $summary->sum('masuk'),
                'dikabulkan' => $summary->sum('dikabulkan'),
                'ditolak'    => $summary->sum('ditolak') + $summary->sum('ditolak_sebagian'),
                'keberatan'  => $summary->sum('keberatan'),
                'rata_rata'  => round((float) $summary->avg('rata_rata_hari'), 1),
            ],
        ];

        return view('ppid.report', compact('data'));
    }

    /**
     * Register Permohonan Informasi — rekap publik.
     * Hanya menampilkan permohonan yang pemohonnya memberi persetujuan
     * (tampil_di_register_publik); identitas pemohon tidak pernah ditampilkan.
     */
    public function showRequestRegister(Request $request)
    {
        $tahun = $request->integer('tahun') ?: null;

        $rows = $this->fromDatabase(
            fn () => PermohonanInformasi::registerPublik()
                ->when($tahun, fn ($q) => $q->whereYear('tanggal_permohonan', $tahun))
                ->orderByDesc('tanggal_permohonan')
                ->limit(200)
                ->get([
                    'kode_permohonan',
                    'rincian_informasi',
                    'status',
                    'tanggal_permohonan',
                    'tanggal_tanggapan',
                ])
                ->values()
                ->map(fn ($row, $i) => [
                    'no'                => $i + 1,
                    'kode'              => $row->kode_permohonan,
                    'rincian'           => \Illuminate\Support\Str::limit($row->rincian_informasi, 140),
                    'status'            => $row->status,
                    'status_label'      => $this->statusLabel($row->status),
                    'tanggal'           => optional($row->tanggal_permohonan)->translatedFormat('d F Y'),
                    'tanggal_tanggapan' => optional($row->tanggal_tanggapan)->translatedFormat('d F Y'),
                ])
                ->all(),
            [],
            'register_permohonan'
        );

        $years = $this->fromDatabase(
            fn () => PermohonanInformasi::registerPublik()
                ->selectRaw('EXTRACT(YEAR FROM tanggal_permohonan)::int AS tahun')
                ->distinct()
                ->orderByDesc('tahun')
                ->pluck('tahun')
                ->all(),
            [],
            'register_permohonan_tahun'
        );

        $data = [
            'title'         => 'Register Permohonan Informasi',
            'description'   => 'Rekapitulasi publik permohonan informasi yang masuk ke PPID PT Food Station Tjipinang Jaya. Hanya permohonan yang disetujui pemohon untuk dipublikasikan yang ditampilkan, tanpa identitas pemohon.',
            'items'         => $rows,
            'years'         => $years,
            'selected_year' => $tahun,
            'db_offline'    => $this->dbOffline,
        ];

        return view('ppid.request_register', compact('data'));
    }

    /**
     * Dasar Hukum (kanal Profil) — memakai tabel regulasi kategori 'dasar_hukum_ppid',
     * dibedakan dari halaman Regulasi (kanal Layanan).
     */
    public function showLegalBasisPage()
    {
        $fromDb = $this->fromDatabase(
            fn () => $this->mapRegulasi(
                Regulasi::kategori('dasar_hukum_ppid')
                    ->orderByDesc('tahun')
                    ->orderBy('judul')
                    ->get()
            ),
            [],
            'dasar_hukum'
        );

        $regulations = !empty($fromDb) ? $fromDb : [
            [
                'no' => 1,
                'title' => 'Undang-Undang Nomor 14 Tahun 2008 tentang Keterbukaan Informasi Publik',
                'number' => 'UU No. 14/2008',
                'date' => '2008-04-30',
                'type' => 'Hukum Negara',
                'link' => '#dasar-hukum-1',
            ],
            [
                'no' => 2,
                'title' => 'Peraturan Pemerintah Nomor 61 Tahun 2010 tentang Pelaksanaan UU Nomor 14 Tahun 2008',
                'number' => 'PP No. 61/2010',
                'date' => '2010-08-23',
                'type' => 'Hukum Negara',
                'link' => '#dasar-hukum-2',
            ],
            [
                'no' => 3,
                'title' => 'Peraturan Komisi Informasi Nomor 1 Tahun 2021 tentang Standar Layanan Informasi Publik',
                'number' => 'Perki No. 1/2021',
                'date' => '2021-09-01',
                'type' => 'Peraturan KI',
                'link' => '#dasar-hukum-3',
            ],
        ];

        $data = [
            'title'       => 'Dasar Hukum PPID',
            'description' => 'Landasan hukum pembentukan dan penyelenggaraan PPID PT Food Station Tjipinang Jaya (Perseroda).',
            'regulations' => $regulations,
            'db_offline'  => $this->dbOffline,
        ];

        return view('ppid.regulation', compact('data'));
    }

    /** Ubah koleksi model Regulasi menjadi bentuk array yang dipakai view regulasi. */
    private function mapRegulasi($rows): array
    {
        $labelKategori = [
            'dasar_hukum_ppid' => 'Hukum Negara',
            'regulasi'         => 'Internal PPID',
            'pedoman'          => 'Peraturan KI',
        ];

        return $rows->values()->map(fn ($row, $i) => [
            'no'     => $i + 1,
            'title'  => $row->judul,
            'number' => $row->nomor_peraturan ?: ($row->tahun ? 'Tahun ' . $row->tahun : '-'),
            'date'   => optional($row->tanggal_berlaku)->format('Y-m-d'),
            'type'   => $labelKategori[$row->kategori] ?? ($row->jenis_peraturan ?: 'Internal PPID'),
            'link'   => $this->fileUrl($row->file_path) ?? '#',
        ])->all();
    }

    /** Path file di DB -> URL yang bisa dibuka publik. Null bila belum ada file. */
    private function fileUrl(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://', '/'])) {
            return $path;
        }

        return asset('storage/' . ltrim($path, '/'));
    }

    /** Label status permohonan untuk tampilan publik. */
    private function statusLabel(?string $status): string
    {
        return [
            'diajukan'          => 'Diajukan',
            'diverifikasi'      => 'Diverifikasi',
            'diproses'          => 'Diproses',
            'menunggu_approval' => 'Menunggu Persetujuan',
            'disetujui'         => 'Disetujui',
            'ditolak'           => 'Ditolak',
            'ditolak_sebagian'  => 'Ditolak Sebagian',
            'selesai'           => 'Selesai',
            'kedaluwarsa'       => 'Kedaluwarsa',
        ][$status] ?? ucfirst((string) $status);
    }
}
