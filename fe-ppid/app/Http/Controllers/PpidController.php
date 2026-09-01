<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use App\Mail\DownloadReportMail; // Asumsikan Anda membuat Mailable ini
use Illuminate\Support\Facades\Log;
use App\Models\AlurProsedur;
use App\Models\InformasiDikecualikan;
use App\Models\LaporanLayanan;
use App\Models\Maklumat;
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

    /**
     * Permintaan tautan unduh laporan. Wajib masuk sebagai pengunjung —
     * identitas diambil dari akun, tidak diketik ulang di formulir.
     *
     * Tautan yang dikirim adalah URL bertanda tangan yang kedaluwarsa dalam
     * 72 jam, menunjuk berkas laporan asli di penyimpanan CMS.
     */
    public function sendDownloadLink(Request $request)
    {
        $pemohon = Auth::guard('pemohon')->user();

        $data = $request->validate([
            'laporan_id' => 'required|integer',
        ]);

        $laporan = LaporanLayanan::published()->find($data['laporan_id']);

        if (!$laporan || blank($laporan->file_laporan)) {
            return response()->json([
                'success' => false,
                'message' => __('Berkas laporan tidak ditemukan.'),
            ], 404);
        }

        $tautan = URL::temporarySignedRoute('report.download.file', now()->addHours(72), [
            'laporan' => $laporan->id,
        ]);

        $judul = trim($laporan->judul.' '.$laporan->tahun);

        try {
            Mail::to($pemohon->email)->send(new DownloadReportMail($pemohon->nama, $tautan, $judul));
        } catch (\Throwable $e) {
            Log::error('[PPID] Gagal mengirim email tautan unduh: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => __('Tautan gagal dikirim. Coba lagi beberapa saat lagi.'),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => __('Tautan unduh berhasil dikirim.'),
            'email' => $pemohon->email,
            'title' => $judul,
        ]);
    }

    /**
     * Penyaji berkas laporan dari tautan email.
     *
     * Route-nya memakai middleware `signed`, jadi tautan tidak bisa ditebak
     * maupun dipakai lagi setelah 72 jam. Sengaja tidak butuh sesi login —
     * tanda tangan URL-nya sendiri yang jadi kunci.
     */
    public function downloadReportFile(int $laporan)
    {
        $baris = LaporanLayanan::published()->find($laporan);

        abort_if(!$baris || blank($baris->file_laporan), 404);

        // Sebagian entri CMS menyimpan URL penuh, sebagian lagi path relatif
        // di dalam disk `public` (mis. `uploads/laporan/…`).
        if (Str::startsWith($baris->file_laporan, ['http://', 'https://'])) {
            return redirect()->away($baris->file_laporan);
        }

        $disk = Storage::disk('public');
        $path = ltrim(Str::after($baris->file_laporan, 'storage/'), '/');

        abort_unless($disk->exists($path), 404);

        $nama = Str::slug($baris->judul.' '.$baris->tahun).'.'.pathinfo($path, PATHINFO_EXTENSION);

        return $disk->download($path, $nama);
    }


    public function showProfilePage($slug)
    {
        $profileData = [
            // Isi ketiga halaman di bawah mengikuti dokumen resmi
            // "Profil VISI MISI FUNGSI". Ini data cadangan: bila halaman dengan
            // slug yang sama sudah dibuat di modul Halaman Statis (CMS), isi
            // dari CMS yang dipakai.
            'singkat' => [
                'title' => 'Profil Singkat',
                'intro' => [
                    'Pejabat Pengelola Informasi dan Dokumentasi (PPID) PT Food Station Tjipinang Jaya (Perseroda) adalah pejabat yang bertanggung jawab dalam pengelolaan, pendokumentasian, penyimpanan, penyediaan, serta pelayanan informasi publik di lingkungan perusahaan.',
                    'PPID berperan penting dalam mewujudkan transparansi, akuntabilitas, dan pelayanan informasi yang cepat, tepat, dan mudah diakses oleh masyarakat.',
                ],
                // Jam layanan tidak lagi disertakan di sini — satu-satunya tempat
                // tayangnya sekarang halaman Standar Layanan → Jalur dan Waktu
                // Layanan (`showServiceStandardPage`), supaya tidak ada dua
                // sumber jam layanan yang bisa berbeda isinya.
            ],
            'struktur' => [
                'title' => 'Struktur Organisasi',
                'content' => 'Struktur PPID FSTJ dipimpin oleh seorang Atasan PPID (Direktur Utama) yang mendelegasikan tugas kepada Pejabat Pengelola Informasi dan Dokumentasi (PPID) Utama. PPID Utama dibantu oleh PPID Pelaksana yang bertugas melayani permohonan informasi. Staf Teknis yang terdiri dari berbagai unit kerja memastikan dokumen tersedia dan terbaharui.'
            ],
            'visi-misi' => [
                'title' => 'Visi dan Misi',
                'content' => [
                    'Visi' => 'Terwujudnya pelayanan informasi publik yang transparan, akuntabel, dan responsif untuk memenuhi hak pemohon informasi sesuai dengan ketentuan peraturan perundang-undangan yang berlaku di PT Food Station Tjipinang Jaya (Perseroda).',
                    'Misi' => [
                        'Meningkatkan pengelolaan dan pelayanan informasi publik yang berkualitas, akurat, benar, dan bertanggung jawab.',
                        'Membangun dan mengembangkan sistem penyediaan serta layanan informasi publik yang efektif, efisien, dan mudah diakses.',
                        'Meningkatkan kompetensi dan kualitas sumber daya manusia dalam pengelolaan dan pelayanan informasi publik.',
                        'Mewujudkan keterbukaan informasi publik di lingkungan PT Food Station Tjipinang Jaya (Perseroda) melalui proses pelayanan yang cepat, tepat, mudah, sederhana, dan transparan.',
                    ]
                ]
            ],
            // Menggantikan sub modul Dasar Hukum pada menu Profil.
            'tugas-fungsi-wewenang' => [
                'title' => 'Tugas, Fungsi dan Wewenang',
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

        $data = $profileData[$key] ?? ['title' => $halaman->teks('judul')];

        if ($halaman) {
            $data['title'] = $halaman->teks('judul');
            $data['html'] = $halaman->teks('konten');
        }

        // Struktur Organisasi hanya menampilkan bagannya; daftar "Susunan
        // Pejabat" dihapus atas permintaan, jadi datanya tidak diambil lagi.
        if ($key === 'struktur') {
            $data['bagan'] = \App\Models\StrukturOrganisasi::pohon($this->fromDatabase(
                fn () => \App\Models\StrukturOrganisasi::aktif()->get(),
                collect(),
                'struktur_organisasi'
            ));
        }

        $data['db_offline'] = $this->dbOffline;

        return view('ppid.profile', compact('data', 'slug'));
    }

    /**
     * Daftar Informasi Publik — seluruh dokumen terbit dari semua kategori
     * dalam satu tabel. Kategorinya ikut ditampilkan supaya pengunjung tetap
     * tahu dokumen itu masuk klasifikasi mana.
     */
    public function showPublicInformationIndex()
    {
        // Urutannya mengikuti Daftar Informasi Publik resmi: dikelompokkan per
        // klasifikasi, lalu nomor urut pada dokumen (disimpan di
        // `nomor_klasifikasi`, jadi dibandingkan sebagai angka).
        $rows = $this->fromDatabase(
            fn () => \App\Models\InformasiPublik::published()
                ->with(['files', 'kategori'])
                ->leftJoin('kategori_informasi', 'kategori_informasi.id', '=', 'informasi_publik.kategori_id')
                ->orderBy('kategori_informasi.urutan')
                ->orderBy('kategori_informasi.id')
                ->orderByRaw("NULLIF(regexp_replace(informasi_publik.nomor_klasifikasi, '[^0-9]', '', 'g'), '')::int NULLS LAST")
                ->orderBy('informasi_publik.judul')
                ->select('informasi_publik.*')
                ->get(),
            collect(),
            'informasi_publik'
        );

        $items = $rows->values()->map(function ($row, $i) {
            $berkas = $row->files->first();

            $aksi = $this->aksiDokumen($row, $berkas);

            return [
                'no' => $i + 1,
                'name' => $row->teks('judul'),
                'ringkasan' => $row->teks('ringkasan'),
                'kategori' => $row->kategori?->teks('nama') ?? __('Lainnya'),
                'kategori_slug' => $row->kategori->slug ?? null,
                'tahun' => optional($row->tanggal_publikasi)->format('Y'),
            ] + $aksi;
        })->all();

        $data = [
            'title' => 'Daftar Informasi Publik',
            'description' => 'Daftar Informasi Publik dilingkungan PT Food Station Tjipinang Jaya (Perseroda).',
            'items' => $items,
            // Ringkasan jumlah per klasifikasi untuk kartu di atas daftar.
            'kelompok' => collect($items)->groupBy('kategori')->map->count()->all(),
            'db_offline' => $this->dbOffline,
        ];

        return view('ppid.information_index', compact('data'));
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
        // [nama halaman, judul tabel dokumen menurut UU No. 14 Tahun 2008]
        $bawaan = [
            'berkala' => ['Informasi Berkala', 'Informasi Wajib Disediakan dan Diumumkan Secara Berkala'],
            'serta-merta' => ['Informasi Serta Merta', 'Informasi Wajib Diumumkan Secara Serta Merta'],
            'setiap-saat' => ['Informasi Tersedia Setiap Saat', 'Informasi Wajib Tersedia Setiap Saat'],
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

            $aksi = $this->aksiDokumen($row, $berkas);

            return [
                'no' => $i + 1,
                'name' => $row->teks('judul'),
                'ringkasan' => $row->teks('ringkasan'),
            ] + $aksi;
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

        $nama = $kategori?->teks('nama') ?? $bawaan[$key][0];

        $data = [
            'title' => $nama,
            // Judul tabel: klasifikasi wajib memakai frasa UU; kategori lain
            // (termasuk sub-kategori dari CMS) memakai namanya sendiri.
            'heading_dokumen' => $bawaan[$key][1] ?? $nama,
            'items' => $items,
            'subkategori' => $subkategori->map(fn ($k) => [
                'nama' => $k->teks('nama'),
                'slug' => $k->slug,
                'deskripsi' => $k->teks('deskripsi'),
                'jumlah' => (int) ($k->jumlah_dokumen ?? 0),
            ])->all(),
            'db_offline' => $this->dbOffline,
        ];

        return view('ppid.information', compact('data', 'slug'));
    }


    public function showRegulationPage()
    {
        // Halaman ini memuat seluruh kategori. Dasar hukum PPID ikut di sini
        // sejak halaman Profil > Dasar Hukum dihapus, supaya dokumennya tidak
        // kehilangan tempat tayang di situs publik.
        $fromDb = $this->fromDatabase(
            fn () => $this->mapRegulasi(
                Regulasi::kategori(['regulasi', 'pedoman', 'dasar_hukum_ppid'])
                    ->with('pengunggah')
                    ->orderByDesc('created_at')
                    ->orderBy('judul')
                    ->get()
            ),
            [],
            'regulasi'
        );

        if (!empty($fromDb)) {
            return view('ppid.regulation', ['data' => [
                'title'       => 'REGULASI',
                'description' => 'Peraturan perundang-undangan yang menjadi landasan penyelenggaraan Keterbukaan Informasi Publik (KIP) PPID PT Food Station Tjipinang Jaya (Perseroda).',
                'regulations' => $fromDb,
                'db_offline'  => false,
            ]]);
        }

        // Fallback: DB kosong atau tidak dapat dihubungi. Tanpa baris di
        // database tidak ada halaman detail yang bisa dituju, jadi `url` dan
        // `link` dibiarkan kosong — kartunya tampil tetapi tidak bisa diklik.
        $regulations = [
            [
                'no' => 1,
                'id' => null,
                'title' => 'Undang-Undang Republik Indonesia Nomor 14 Tahun 2008 tentang Keterbukaan Informasi Publik',
                'ringkasan' => 'Undang-undang yang menjamin hak setiap orang memperoleh informasi publik beserta kewajiban badan publik menyediakannya.',
                'number' => 'Nomor 14 Tahun 2008',
                'date' => '2008-04-30',
                'published' => null,
                'year' => 2008,
                'jenis' => 'Undang-Undang',
                'ext' => '',
                'type' => 'Dasar Hukum PPID',
                'pengunggah' => null,
                'link' => null,
                'url' => null,
            ],
            [
                'no' => 2,
                'id' => null,
                'title' => 'Peraturan Komisi Informasi Republik Indonesia Nomor 1 Tahun 2021 tentang Standar Layanan Informasi Publik',
                'ringkasan' => 'Standar layanan informasi publik: tata cara pelayanan permohonan, jangka waktu tanggapan, sampai pelaporan layanan.',
                'number' => 'Nomor 1 Tahun 2021',
                'date' => '2021-09-01',
                'published' => null,
                'year' => 2021,
                'jenis' => 'Peraturan Komisi Informasi',
                'ext' => '',
                'type' => 'Dasar Hukum PPID',
                'pengunggah' => null,
                'link' => null,
                'url' => null,
            ],
        ];

        $data = [
            'title' => 'REGULASI',
            'description' => 'Peraturan perundang-undangan yang menjadi landasan penyelenggaraan Keterbukaan Informasi Publik (KIP) PPID PT Food Station Tjipinang Jaya (Perseroda).',
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
            // Alur enam tahap ini sebelumnya tayang di beranda; dipindah ke
            // sini supaya prosedurnya berada satu tempat dengan penjelasannya.
            'prosedur-permohonan' => [
                'title' => 'Prosedur Permohonan Informasi',
                // Bunyinya tidak lagi menyebut "enam tahapan": sejak langkah 86
                // halaman ini menayangkan alur bergambar, dan kartu tahapannya
                // hanya muncul bila gambarnya belum diunggah.
                'intro' => 'Alur lengkap dari membuat akun sampai permohonan Anda diproses.',
                'flow' => [
                    ['title' => 'Ajukan Permohonan', 'desc' => 'Isi formulir permohonan informasi secara daring.', 'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
                    ['title' => 'Verifikasi', 'desc' => 'Berkas & identitas pemohon diperiksa petugas PPID.', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['title' => 'Diproses', 'desc' => 'Permohonan ditelaah dan informasi dihimpun.', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.525.321 1.157.498 1.724 1.065z'],
                    ['title' => 'Persetujuan PPID', 'desc' => 'Keputusan pemberian informasi disetujui pejabat PPID.', 'icon' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'],
                    ['title' => 'Informasi Dikirim', 'desc' => 'Dokumen dikirim via email atau diambil di kantor.', 'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                    ['title' => 'Selesai', 'desc' => 'Permohonan tuntas & tercatat dalam arsip layanan.', 'icon' => 'M5 13l4 4L19 7'],
                ],
                'steps' => [
                    'Pemohon mengisi Formulir Permohonan Informasi secara lengkap (online/langsung).',
                    'Pemohon menyerahkan salinan identitas diri (KTP/SIM/Paspor) atau Akta Pendirian bagi Instansi.',
                    'Petugas mencatat permohonan dan memberikan Tanda Bukti Penerimaan.',
                    'PPID memproses permintaan dan memberikan pemberitahuan tertulis dalam jangka waktu 10 hari kerja (dapat diperpanjang 7 hari kerja).',
                    'Pemohon mengambil atau menerima informasi publik sesuai format yang diminta, setelah melunasi biaya (jika ada).'
                ]
            ],
            'prosedur-keberatan' => [
                'title' => 'Prosedur Permohonan Keberatan',
                'intro' => 'Keberatan diajukan bila permohonan ditolak, tidak ditanggapi, atau informasi yang diberikan tidak sesuai. Pengajuannya lewat akun yang sama dengan permohonan informasi.',
                'flow' => [
                    ['title' => 'Cek Permohonan', 'desc' => 'Keberatan selalu menunjuk satu permohonan informasi milik Anda sendiri.', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    ['title' => 'Isi Formulir Keberatan', 'desc' => 'Pilih alasan keberatan, tulis kasus posisi, lampirkan dokumen pendukung.', 'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
                    ['title' => 'Verifikasi Berkas', 'desc' => 'Petugas memeriksa kelengkapan berkas dan mencatat keberatan Anda.', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['title' => 'Telaah Atasan PPID', 'desc' => 'Atasan PPID menelaah keberatan bersama unit kerja terkait.', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                    ['title' => 'Tanggapan Tertulis', 'desc' => 'Tanggapan diberikan paling lama 30 hari kalender sejak keberatan diregistrasi.', 'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                    ['title' => 'Sengketa Informasi', 'desc' => 'Bila tanggapan belum memuaskan, sengketa dapat diajukan ke Komisi Informasi dalam 14 hari kerja.', 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.398 16c-.77 1.333.192 3 1.732 3z'],
                ],
                'steps' => [
                    'Keberatan diajukan paling lambat 30 hari kerja setelah pemohon menerima tanggapan atau setelah batas waktu tanggapan terlewati.',
                    'Pemohon masuk ke akun, membuka menu Permohonan Keberatan, lalu memilih permohonan informasi yang dikeberatankan.',
                    'Pemohon mengisi alasan keberatan, kasus posisi, dan melampirkan dokumen pendukung (PDF/gambar, maksimal 10 MB per berkas).',
                    'Petugas mencatat keberatan dan meneruskannya kepada Atasan PPID untuk ditelaah.',
                    'Atasan PPID memberikan tanggapan tertulis paling lama 30 hari kalender sejak keberatan diregistrasi.',
                    'Apabila pemohon belum puas atas tanggapan tersebut, sengketa informasi dapat diajukan ke Komisi Informasi paling lambat 14 hari kerja setelah tanggapan diterima.',
                ]
            ],
            'jalur-waktu-layanan' => [
                'title' => 'Jalur & Waktu Layanan Informasi',
                'intro' => 'Informasi mengenai jam operasional dan pilihan saluran yang dapat digunakan pemohon untuk mengakses pelayanan PPID.',
                /*
                 * Jalur Surat dihapus (langkah 70): permohonan lewat surat
                 * tidak lagi dilayani, semuanya masuk lewat portal atau meja
                 * layanan.
                 *
                 * `aksi` menentukan perilaku kartunya di halaman:
                 *   - `masuk`   → menuju halaman masuk Portal Pemohon
                 *   - `waktu`   → membuka panel Waktu Layanan + peta lokasi
                 */
                'channels' => [
                    ['label' => 'Online', 'desc' => 'Melalui Formulir Permohonan di website resmi PPID.', 'recommended' => true, 'aksi' => 'masuk'],
                    ['label' => 'Langsung', 'desc' => 'Datang ke meja layanan PPID pada jam operasional.', 'recommended' => false, 'aksi' => 'waktu'],
                ],
                /*
                 * Satu baris jam layanan untuk seluruh hari kerja (langkah 70),
                 * dengan jam tutupnya dimajukan ke 15:00 dan jam istirahat
                 * diumumkan kembali (langkah 82).
                 *
                 * Istirahat penting disebut justru karena jendelanya sekarang
                 * pendek: tanpa keterangan itu, pemohon yang datang pukul 12:30
                 * mengira layanannya masih buka sampai sore.
                 */
                'hours' => [
                    ['days' => 'Senin - Jum\'at', 'time' => '08:00 - 15:00 WIB', 'break' => '12:00 - 13:00 WIB'],
                ],
                'note' => 'Permohonan di luar jam kerja akan diproses pada hari kerja berikutnya.'
            ]
        ];

        $key = \Illuminate\Support\Str::slug($slug);

        if (!array_key_exists($key, $standardData)) {
            abort(404, 'Halaman Standar Pelayanan tidak ditemukan.');
        }

        $data = $standardData[$key];

        // Maklumat tayang sebagai dokumen: berkas yang diunggah petugas lewat
        // modul Maklumat dibaca langsung di halaman ini. Teks di `$standardData`
        // hanya cadangan bila belum ada maklumat terbit / DB sedang bermasalah.
        if ($key === 'maklumat-pelayanan') {
            $data = $this->lengkapiMaklumat($data);
        }

        // Prosedur tayang sebagai alur bergambar bila petugas sudah mengunggah
        // infografisnya lewat modul Alur Prosedur. Kartu tahapan dan rincian
        // langkah di `$standardData` tetap ada di bawahnya — gambar menunjukkan
        // tampilan layarnya, teks tetap bisa dibaca pembaca layar dan dicari
        // mesin pencari.
        if (in_array($key, ['prosedur-permohonan', 'prosedur-keberatan'], true)) {
            $data = $this->lengkapiAlurGambar($data, $key);
        }

        return view('ppid.service_standard', compact('data', 'slug'));
    }

    /**
     * Tambahkan gambar alur ke data halaman Standar Pelayanan.
     *
     * Kunci `gambar_alur` hanya diisi bila ada gambar aktif untuk halaman itu;
     * halaman yang belum punya gambar tampil persis seperti sebelumnya.
     */
    private function lengkapiAlurGambar(array $data, string $halaman): array
    {
        $baris = $this->fromDatabase(
            fn () => AlurProsedur::tayang($halaman)->get(),
            collect(),
            'alur prosedur'
        );

        $gambar = $baris
            ->map(function ($item) {
                $url = $this->fileUrl($item->gambar);

                if (blank($url)) {
                    return null;
                }

                return [
                    'url' => $url,
                    'judul' => $item->teks('judul'),
                    'keterangan' => $item->teks('keterangan'),
                ];
            })
            ->filter()
            ->values()
            ->all();

        if ($gambar) {
            $data['gambar_alur'] = $gambar;
        }

        return $data;
    }

    /**
     * Tambahkan dokumen maklumat terbit ke data halaman Standar Pelayanan.
     *
     * Yang dipakai adalah satu maklumat berstatus `published` dengan tanggal
     * terbit terbaru. Bila tidak ada — atau berkasnya belum diunggah — kunci
     * `dokumen` tidak diisi dan halaman kembali memakai teks bawaannya.
     */
    private function lengkapiMaklumat(array $data): array
    {
        $baris = $this->fromDatabase(
            fn () => Maklumat::published()
                ->with('penerbit')
                ->orderByDesc('tanggal_terbit')
                ->orderByDesc('id')
                ->first(),
            null,
            'maklumat'
        );

        $data['db_offline'] = $this->dbOffline;

        if (!$baris) {
            return $data;
        }

        $judul = $baris->teks('judul');

        if (filled($judul)) {
            $data['title'] = $judul;
        }

        $url = $this->fileUrl($baris->file_dokumen);

        if (blank($url)) {
            return $data;
        }

        $ekstensi = strtolower(pathinfo(parse_url($url, PHP_URL_PATH) ?: '', PATHINFO_EXTENSION));

        $data['dokumen'] = [
            'url' => $url,
            'ext' => $ekstensi,
            'judul' => filled($judul) ? $judul : $data['title'],
            // Kolom `ringkasan` tidak ikut dibawa sejak langkah 88: pengantarnya
            // dilepas dari halaman, dan meneruskan data yang tidak dipakai
            // membuat view berikutnya mengira ia masih tayang.
            'tanggal' => $baris->tanggal_terbit ? \App\Support\Cms::tanggal($baris->tanggal_terbit) : null,
            'pengunggah' => $baris->penerbit->name ?? null,
        ];

        return $data;
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

        $regNumber = strtoupper(trim($request->registration_number));
        $pemohon = Auth::guard('pemohon')->user();

        // Halaman ini wajib login, jadi pencarian dibatasi pada permohonan
        // milik akun yang sedang masuk. Nomor registrasi orang lain tetap
        // dijawab "tidak ditemukan".
        $permohonan = $this->fromDatabase(
            fn () => PermohonanInformasi::where('pemohon_id', $pemohon->id)
                ->where('kode_permohonan', $regNumber)
                ->first(),
            null,
            'cek status permohonan'
        );

        if (!$permohonan) {
            return response()->json([
                'success' => true,
                'number' => $regNumber,
                'status' => 'TIDAK DITEMUKAN',
                'response_date' => null,
                'info_requested' => null,
            ]);
        }

        $label = [
            'diajukan'          => 'DIAJUKAN',
            'diverifikasi'      => 'DALAM PENELITIAN',
            'diproses'          => 'DIPROSES',
            'menunggu_approval' => 'MENUNGGU PERSETUJUAN',
            'disetujui'         => 'DITERIMA',
            'ditolak'           => 'DITOLAK',
            'ditolak_sebagian'  => 'DITOLAK SEBAGIAN',
            'selesai'           => 'SELESAI',
            'kedaluwarsa'       => 'KEDALUWARSA',
        ];

        return response()->json([
            'success' => true,
            'number' => $permohonan->kode_permohonan,
            'status' => $label[$permohonan->status] ?? strtoupper((string) $permohonan->status),
            'response_date' => optional($permohonan->tanggal_tanggapan)->translatedFormat('d F Y'),
            'info_requested' => \Illuminate\Support\Str::limit($permohonan->rincian_informasi, 160),
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
                // Keterangan penetapan (alasan, dasar hukum, jangka waktu,
                // tanggal) tidak lagi ditampilkan publik, jadi tidak dikirim
                // ke view sama sekali.
                ->map(fn ($row, $i) => [
                    'no'        => $i + 1,
                    'judul'     => $row->teks('judul'),
                    'ringkasan' => $row->teks('ringkasan'),
                    'file'      => $this->fileUrl($row->file_surat_penetapan),
                ])
                ->all(),
            [],
            'informasi_dikecualikan'
        );

        // Penyajiannya disamakan dengan Daftar Informasi Publik: kartu ringkasan
        // di atas daftar yang sekaligus jadi penyaring. Daftar ini tidak punya
        // klasifikasi, jadi yang dikelompokkan adalah ketersediaan surat
        // penetapannya.
        $adaSurat = count(array_filter($items, fn ($item) => !empty($item['file'])));

        $data = [
            'title'       => 'Daftar Informasi Dikecualikan',
            'description' => __('Informasi yang dikecualikan dari keterbukaan informasi publik di lingkungan PT Food Station Tjipinang Jaya (Perseroda) berdasarkan hasil uji konsekuensi.'),
            'items'       => $items,
            'kelompok'    => [
                'ada'   => $adaSurat,
                'belum' => count($items) - $adaSurat,
            ],
            'db_offline'  => $this->dbOffline,
        ];

        return view('ppid.excluded_information', compact('data'));
    }

    /**
     * Laporan Pelayanan Informasi.
     *
     * Sejak langkah 68 menu Laporan hanya berisi satu halaman: Laporan
     * Statistik Informasi Publik dihapus beserta rekap angkanya, jadi tidak
     * ada lagi percabangan slug di sini.
     */
    public function showReportPage()
    {
        return $this->halamanLaporanPelayanan([
            'tipe'        => 'pelayanan_informasi',
            'title'       => 'Laporan Pelayanan Informasi',
            'description' => 'Laporan periodik penyelenggaraan layanan informasi publik PPID PT Food Station Tjipinang Jaya (Perseroda).',
        ]);
    }

    /**
     * Daftar Laporan Pelayanan Informasi — berkas per tahun yang diunggah dari
     * be-ppid. Bentuk kartunya sama dengan modul Regulasi.
     *
     * @param  array{tipe: string, title: string, description: string}  $meta
     */
    private function halamanLaporanPelayanan(array $meta)
    {
        $laporan = $this->fromDatabase(
            fn () => $this->mapLaporanPelayanan(
                LaporanLayanan::published()
                    ->tipe($meta['tipe'])
                    ->with('penerbit')
                    ->orderByDesc('tahun')
                    ->orderByDesc('id')
                    ->get()
            ),
            [],
            'laporan_pelayanan'
        );

        return view('ppid.service_report', ['data' => [
            'title'       => $meta['title'],
            'description' => $meta['description'],
            'reports'     => $laporan,
            'db_offline'  => $this->dbOffline,
        ]]);
    }

    /**
     * Halaman detail satu Laporan Pelayanan Informasi. Dokumennya digambar di
     * halaman ini juga (pdf.js), tidak membuka tab baru.
     */
    public function showServiceReportDetail(int $laporan)
    {
        $baris = $this->fromDatabase(
            fn () => LaporanLayanan::published()
                ->tipe('pelayanan_informasi')
                ->with('penerbit')
                ->find($laporan),
            null,
            'laporan_pelayanan_detail'
        );

        abort_if(!$baris, 404, 'Laporan Pelayanan Informasi tidak ditemukan.');

        $lainnya = $this->fromDatabase(
            fn () => $this->mapLaporanPelayanan(
                LaporanLayanan::published()
                    ->tipe('pelayanan_informasi')
                    ->with('penerbit')
                    ->where('id', '!=', $baris->id)
                    ->orderByDesc('tahun')
                    ->orderByDesc('id')
                    ->limit(6)
                    ->get()
            ),
            [],
            'laporan_pelayanan_lain'
        );

        return view('ppid.service_report_show', [
            'data' => $this->satuLaporanPelayanan($baris) + ['db_offline' => $this->dbOffline],
            'lainnya' => $lainnya,
        ]);
    }

    private function mapLaporanPelayanan($rows): array
    {
        return $rows->values()->map(fn ($row, $i) => $this->satuLaporanPelayanan($row, $i + 1))->all();
    }

    /** Satu laporan pelayanan dalam bentuk yang dipakai daftar maupun detail. */
    private function satuLaporanPelayanan($row, int $nomorUrut = 1): array
    {
        return [
            'no'        => $nomorUrut,
            'id'        => $row->id,
            'title'     => $row->judul,
            'ringkasan' => $row->ringkasan,
            'year'      => $row->tahun,
            'periode'   => $row->periode ?: __('Tahunan'),
            // Tanggal terbit di situs publik memakai waktu baris dibuat di CMS,
            // sama seperti modul Regulasi.
            'published' => $row->created_at,
            // Dipakai view untuk memilih cara menampilkan berkas: PDF digambar
            // lewat pdf.js, gambar ditampilkan apa adanya.
            'ext'        => strtolower(pathinfo((string) $row->file_laporan, PATHINFO_EXTENSION)),
            'pengunggah' => $row->penerbit->name ?? null,
            'link'       => $this->fileUrl($row->file_laporan),
            'url'        => route('ppid.report.show', $row->id),
        ];
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

    /** Ubah koleksi model Regulasi menjadi bentuk array yang dipakai view regulasi. */
    private function mapRegulasi($rows): array
    {
        return $rows->values()->map(fn ($row, $i) => $this->satuRegulasi($row, $i + 1))->all();
    }

    /** Label kategori regulasi untuk badge di situs publik. */
    private function labelKategoriRegulasi(?string $kategori): string
    {
        return [
            'dasar_hukum_ppid' => 'Dasar Hukum PPID',
            'regulasi'         => 'Regulasi',
            'pedoman'          => 'Pedoman',
        ][$kategori] ?? 'Regulasi';
    }

    /** Satu baris regulasi dalam bentuk yang dipakai daftar maupun halaman detail. */
    private function satuRegulasi($row, int $nomorUrut = 1): array
    {
        return [
            'no'        => $nomorUrut,
            'id'        => $row->id,
            'title'     => $row->teks('judul'),
            'ringkasan' => $row->teks('ringkasan'),
            'number'    => $row->nomor_peraturan ?: ($row->tahun ? 'Tahun '.$row->tahun : null),
            'date'      => optional($row->tanggal_berlaku)->format('Y-m-d'),
            // Tanggal terbit di situs publik memakai waktu baris dibuat di CMS.
            'published' => $row->created_at,
            'year'      => $row->tahun,
            'jenis'     => $row->jenis_peraturan,
            // Dipakai view untuk memilih cara menampilkan berkas: PDF digambar
            // lewat pdf.js, gambar ditampilkan apa adanya.
            'ext'       => strtolower(pathinfo((string) $row->file_path, PATHINFO_EXTENSION)),
            'type'      => $this->labelKategoriRegulasi($row->kategori),
            'pengunggah' => $row->pengunggah->name ?? null,
            // Berkas kosong sengaja dibiarkan null: view menampilkan "Belum
            // tersedia", bukan tautan yang tidak menuju ke mana-mana.
            'link'      => $this->fileUrl($row->file_path),
            'url'       => route('ppid.regulation.show', $row->id),
        ];
    }

    /**
     * Halaman detail satu regulasi: dokumennya dibaca langsung di halaman,
     * ditutup dengan daftar regulasi lain supaya pengunjung tidak perlu balik
     * ke daftar untuk membuka dokumen berikutnya.
     */
    public function showRegulationDetail(int $regulasi)
    {
        $baris = $this->fromDatabase(
            fn () => Regulasi::with('pengunggah')->find($regulasi),
            null,
            'regulasi_detail'
        );

        abort_if(!$baris, 404, 'Regulasi tidak ditemukan.');

        $lainnya = $this->fromDatabase(
            fn () => $this->mapRegulasi(
                Regulasi::kategori(['regulasi', 'pedoman', 'dasar_hukum_ppid'])
                    ->with('pengunggah')
                    ->where('id', '!=', $baris->id)
                    // Yang sekategori didahulukan, sisanya menyusul yang terbaru.
                    ->orderByRaw('CASE WHEN kategori = ? THEN 0 ELSE 1 END', [$baris->kategori])
                    ->orderByDesc('created_at')
                    ->limit(6)
                    ->get()
            ),
            [],
            'regulasi_relevan'
        );

        return view('ppid.regulation_show', [
            'data' => $this->satuRegulasi($baris) + ['db_offline' => $this->dbOffline],
            'relevan' => $lainnya,
        ]);
    }

    /** Path file di DB -> URL yang bisa dibuka publik. Null bila belum ada file. */
    /**
     * Tautan dan jenis aksi untuk satu baris Daftar Informasi Publik.
     *
     * Diputuskan di satu tempat supaya keempat daftar — indeks seluruh
     * dokumen, Informasi Berkala, Serta Merta, dan Setiap Saat — tidak pernah
     * berbeda perlakuannya atas baris yang sama.
     *
     * Dua kemungkinan:
     *
     *   - `dialog` → entri yang punya isi (tautan bacanya, berkas salinannya,
     *     atau keduanya). Tombolnya membuka dialog dua pilihan: membaca lewat
     *     tautan yang diisikan petugas, atau mengunduh salinannya. Alamat
     *     berkas unduhannya tidak ikut dicetak — yang dicetak hanya alamat
     *     rutenya, yang memeriksa dulu apakah permohonan orangnya sudah
     *     disetujui petugas.
     *   - `null`   → entri yang belum punya isi sama sekali; daftarnya
     *     menawarkan Mohon Dokumen seperti sebelumnya.
     *
     * Sejak langkah 83 direvisi, membuka langsung tanpa dialog tidak ada lagi:
     * seluruh entri di keempat daftar itu melewati pintu yang sama, sehingga
     * tidak ada baris yang diam-diam berperilaku lain.
     *
     * @return array{file: ?string, jenis: ?string, pratinjau: ?string, unduh: ?string, id: ?int}
     */
    private function aksiDokumen($row, $berkas): array
    {
        if (!$row->tautan && !$berkas) {
            return ['file' => null, 'jenis' => null, 'pratinjau' => null, 'unduh' => null, 'id' => null];
        }

        return [
            // `file` diisi penanda supaya baris ini tetap dihitung "punya
            // aksi" oleh view; yang dipakai membuka dialog kunci di bawahnya.
            'file' => '#',
            'jenis' => 'dialog',
            'pratinjau' => $row->tautan ?: null,
            /*
             * Tombol unduh hanya dipasang bila berkas salinannya memang ada.
             * Memasangnya tanpa berkas berarti mengantar orang melewati login
             * dan permohonan untuk berakhir di 404.
             */
            'unduh' => $berkas ? route('ppid.dokumen.unduh', $row->id) : null,
            'id' => $row->id,
        ];
    }

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
