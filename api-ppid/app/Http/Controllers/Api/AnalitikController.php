<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use App\Models\InformasiPublik;
use App\Models\KeberatanInformasi;
use App\Models\Pemohon;
use App\Models\PermohonanInformasi;
use App\Models\SurveyKepuasan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Ringkasan, analisa, kepatuhan SLA, dan capaian KPI layanan informasi.
 *
 * Berbeda dari `DashboardController` yang hanya menghitung angka sesaat,
 * controller ini menjawab tiga pertanyaan berbeda:
 *
 *   1. Ringkasan  — apa keadaan layanan sekarang;
 *   2. Analisa    — ke mana arahnya (tren, sebaran, kategori terbanyak);
 *   3. SLA & KPI  — apakah kewajiban hukum dan target kinerja terpenuhi,
 *                   serta permohonan mana yang perlu segera ditangani.
 *
 * Seluruh angka dihitung dari data permohonan, keberatan, dan survei — tidak
 * ada nilai yang ditulis tangan.
 */
class AnalitikController extends Controller
{
    /**
     * Batas waktu layanan menurut UU No. 14 Tahun 2008 dan Perki 1/2021.
     *
     * `batas_waktu_tanggapan` pada tabel permohonan adalah sumber kebenaran
     * per baris; angka di bawah dipakai sebagai acuan tampilan dan sebagai
     * cadangan saat baris lama belum punya batas waktu.
     */
    private const SLA_TANGGAPAN_HARI = 10;

    private const SLA_PERPANJANGAN_HARI = 7;

    private const SLA_KEBERATAN_HARI = 30;

    /** Ambang "mendekati batas" — dipakai untuk daftar tindakan. */
    private const AMBANG_SIAGA_HARI = 3;

    /**
     * Target KPI. Diletakkan di satu tempat supaya mudah disesuaikan bila
     * manajemen menetapkan angka lain.
     */
    private const TARGET_KPI = [
        'kepatuhan_sla' => 90.0,      // persen permohonan dijawab tepat waktu
        'rata_rata_hari' => 10.0,     // hari kerja rata-rata sampai tanggapan
        'penyelesaian' => 85.0,       // persen permohonan berstatus tuntas
        'kepuasan' => 80.0,           // persen kepuasan dari survei
        'rasio_keberatan' => 5.0,     // persen permohonan yang berujung keberatan
    ];

    /** Status yang dianggap sudah tuntas — tidak lagi menunggu tindakan. */
    private const STATUS_TUNTAS = ['selesai', 'ditolak', 'ditolak_sebagian', 'kedaluwarsa'];

    public function index(Request $request): JsonResponse
    {
        $data = $request->validate([
            'tahun' => ['nullable', 'integer', 'min:2000', 'max:2100'],
        ]);

        $tahun = $data['tahun'] ?? null;

        $permohonan = fn () => PermohonanInformasi::query()
            ->when($tahun, fn ($q) => $q->whereYear('tanggal_permohonan', $tahun));

        $keberatan = fn () => KeberatanInformasi::query()
            ->when($tahun, fn ($q) => $q->whereYear('tanggal_keberatan', $tahun));

        // Pemohon disaring lewat tanggal pendaftaran akunnya — itu yang berarti
        // "mendaftar pada tahun ini".
        $pemohon = fn () => Pemohon::query()
            ->when($tahun, fn ($q) => $q->whereYear('created_at', $tahun));

        $total = (clone $permohonan())->count();
        $tuntas = (clone $permohonan())->whereIn('status', self::STATUS_TUNTAS)->count();
        $selesai = (clone $permohonan())->where('status', 'selesai')->count();

        $sla = $this->kepatuhanSla($permohonan, $keberatan);
        $rataHari = $this->rataHariTanggapan($permohonan);
        $kepuasan = $this->kepuasan($tahun);
        $jumlahKeberatan = (clone $keberatan())->count();
        $perStatus = $this->sebaranStatus($permohonan);
        $perStatusKeberatan = $this->sebaranStatus($keberatan);

        return response()->json([
            'data' => [
                'tahun' => $tahun,
                'tahun_tersedia' => $this->tahunTersedia(),

                'ringkasan' => [
                    'permohonan' => $total,
                    'selesai' => $selesai,
                    'sedang_berjalan' => $total - $tuntas,
                    'keberatan' => $jumlahKeberatan,
                    'keberatan_belum_selesai' => (clone $keberatan())->where('status', '!=', 'selesai')->count(),
                    'rata_rata_hari' => $rataHari,
                    'kepuasan_persen' => $kepuasan['persen'],
                    'responden_survei' => $kepuasan['responden'],
                    // Beban kerja yang menunggu petugas.
                    'perlu_tindakan' => (int) ($perStatus['diajukan'] ?? 0)
                        + (int) ($perStatus['diverifikasi'] ?? 0)
                        + (int) ($perStatus['diproses'] ?? 0),
                    'menunggu_approval' => (int) ($perStatus['menunggu_approval'] ?? 0),
                ],

                /*
                 * Pemohon — siapa yang memakai layanan, bukan berapa banyak
                 * pengajuannya. Dipisah dari `ringkasan` karena satuannya
                 * berbeda: satu pemohon bisa punya banyak permohonan, jadi
                 * angka di sini tidak boleh dijumlahkan dengan angka layanan.
                 */
                'pemohon' => [
                    'total' => (clone $pemohon())->count(),
                    'per_jenis' => $this->sebaran($pemohon, 'jenis_pemohon'),
                    'verifikasi' => $this->verifikasiPemohon($pemohon),
                ],

                // Jumlah konten tidak ikut disaring tahun: yang relevan adalah
                // keadaan pustaka informasi sekarang, bukan per periode.
                'konten' => [
                    'informasi_publik' => InformasiPublik::count(),
                    'informasi_publik_published' => InformasiPublik::where('status', 'published')->count(),
                    'berita' => Berita::count(),
                    'berita_draft' => Berita::where('status', 'draft')->count(),
                ],

                'sla' => $sla,

                /*
                 * Sebaran pengajuan, selalu berpasangan permohonan + keberatan.
                 * Keduanya dihitung dengan cara yang sama supaya bisa dibaca
                 * berdampingan tanpa catatan kaki.
                 */
                'analisa' => [
                    'tren' => $this->tren($tahun),
                    'per_status' => [
                        'permohonan' => $perStatus,
                        'keberatan' => $perStatusKeberatan,
                    ],
                    'per_jenis_pemohon' => [
                        'permohonan' => $this->perJenisPemohon($permohonan, 'permohonan_informasi'),
                        'keberatan' => $this->perJenisPemohon($keberatan, 'keberatan_informasi'),
                    ],
                    'cara_pengiriman' => $this->sebaran($permohonan, 'cara_pengiriman'),
                ],

                'kpi' => $this->kpi($sla, $rataHari, $total, $tuntas, $jumlahKeberatan, $kepuasan['persen']),

                'tindakan' => $this->perluTindakan(),
            ],
        ]);
    }

    /**
     * Kepatuhan batas waktu, dihitung terpisah untuk permohonan dan keberatan.
     *
     * Yang dinilai hanya baris yang batas waktunya diketahui; baris lama tanpa
     * `batas_waktu_tanggapan` tidak ikut dihitung agar persentasenya tidak
     * menyesatkan.
     */
    private function kepatuhanSla(callable $permohonan, callable $keberatan): array
    {
        $dinilai = (clone $permohonan())->whereNotNull('batas_waktu_tanggapan');

        $tepat = (clone $dinilai)
            ->whereNotNull('tanggal_tanggapan')
            ->whereColumn('tanggal_tanggapan', '<=', 'batas_waktu_tanggapan')
            ->count();

        $telatDijawab = (clone $dinilai)
            ->whereNotNull('tanggal_tanggapan')
            ->whereColumn('tanggal_tanggapan', '>', 'batas_waktu_tanggapan')
            ->count();

        // Belum dijawab dan batas waktunya sudah lewat: pelanggaran berjalan.
        $telatBerjalan = (clone $dinilai)
            ->whereNull('tanggal_tanggapan')
            ->where('batas_waktu_tanggapan', '<', now())
            ->whereNotIn('status', self::STATUS_TUNTAS)
            ->count();

        $siaga = (clone $dinilai)
            ->whereNull('tanggal_tanggapan')
            ->whereBetween('batas_waktu_tanggapan', [now(), now()->addDays(self::AMBANG_SIAGA_HARI)])
            ->whereNotIn('status', self::STATUS_TUNTAS)
            ->count();

        $totalDinilai = $tepat + $telatDijawab + $telatBerjalan;

        $keberatanSelesai = (clone $keberatan())->whereNotNull('tanggal_tanggapan')->count();
        $keberatanTepat = (clone $keberatan())
            ->whereNotNull('tanggal_tanggapan')
            ->whereRaw('tanggal_tanggapan <= tanggal_keberatan + interval \''.self::SLA_KEBERATAN_HARI.' days\'')
            ->count();

        return [
            'target_hari' => self::SLA_TANGGAPAN_HARI,
            'perpanjangan_hari' => self::SLA_PERPANJANGAN_HARI,
            'target_keberatan_hari' => self::SLA_KEBERATAN_HARI,
            'ambang_siaga_hari' => self::AMBANG_SIAGA_HARI,

            'dinilai' => $totalDinilai,
            'tepat_waktu' => $tepat,
            'telat_dijawab' => $telatDijawab,
            'lewat_batas' => $telatBerjalan,
            'mendekati_batas' => $siaga,
            'persen' => $totalDinilai > 0 ? round(($tepat / $totalDinilai) * 100, 1) : null,

            'keberatan' => [
                'ditanggapi' => $keberatanSelesai,
                'tepat_waktu' => $keberatanTepat,
                'persen' => $keberatanSelesai > 0 ? round(($keberatanTepat / $keberatanSelesai) * 100, 1) : null,
            ],
        ];
    }

    /** Rata-rata hari kalender dari permohonan masuk sampai ditanggapi. */
    private function rataHariTanggapan(callable $permohonan): ?float
    {
        $rata = (clone $permohonan())
            ->whereNotNull('tanggal_tanggapan')
            ->value(DB::raw('avg(extract(epoch from (tanggal_tanggapan - tanggal_permohonan)) / 86400)'));

        return $rata !== null ? round((float) $rata, 1) : null;
    }

    private function kepuasan(?int $tahun): array
    {
        $survei = SurveyKepuasan::query()
            ->when($tahun, fn ($q) => $q->whereYear('created_at', $tahun));

        $rata = (clone $survei)->avg('rating');

        return [
            'responden' => (clone $survei)->count(),
            'persen' => $rata !== null ? round(((float) $rata / 5) * 100, 1) : null,
        ];
    }

    /**
     * Berapa tahun yang ikut dibandingkan pada tren bulanan, termasuk tahun
     * utama: tahun berjalan + paling banyak tiga tahun sebelumnya.
     *
     * Samakan dengan grafik Portal Pemohon supaya satu peristiwa tidak terbaca
     * beda rentang di dua tempat.
     */
    private const TAHUN_PEMBANDING = 4;

    /**
     * Tren bulanan permohonan masuk vs ditanggapi, dengan pembanding tahun lalu.
     *
     * Bentuknya ringkasan per bulan (Januari–Desember), bukan 12 bulan terakhir,
     * supaya bulan yang sama antar tahun berdiri sejajar dan bisa dibandingkan.
     * Tahun utama adalah tahun yang dipilih penyaring; tanpa penyaring dipakai
     * tahun berjalan. Tiga tahun sebelumnya ikut ditarik sebagai pembanding —
     * hanya yang benar-benar punya data.
     */
    private function tren(?int $tahun): array
    {
        $tahunUtama = $tahun ?? (int) now()->format('Y');

        $tersedia = $this->tahunTersedia();

        // Tahun utama selalu ikut walau datanya kosong, supaya grafiknya tidak
        // menghilang begitu penyaring diarahkan ke tahun tanpa permohonan.
        $tahunDipakai = collect($tersedia)
            ->filter(fn ($t) => $t < $tahunUtama)
            ->take(self::TAHUN_PEMBANDING - 1)
            ->prepend($tahunUtama)
            ->unique()
            ->sortDesc()
            ->values()
            ->all();

        $mulai = now()->setDate(min($tahunDipakai), 1, 1)->startOfDay();
        $selesai = now()->setDate(max($tahunDipakai), 12, 31)->endOfDay();

        $hitung = fn (string $kolom) => PermohonanInformasi::query()
            ->whereNotNull($kolom)
            ->whereBetween($kolom, [$mulai, $selesai])
            ->select(
                DB::raw("to_char(date_trunc('month', $kolom), 'YYYY-MM') as periode"),
                DB::raw('count(*) as jumlah')
            )
            ->groupBy('periode')
            ->pluck('jumlah', 'periode');

        $masuk = $hitung('tanggal_permohonan');
        $ditanggapi = $hitung('tanggal_tanggapan');

        $bulanan = [];

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $perTahun = [];

            foreach ($tahunDipakai as $t) {
                $kunci = sprintf('%04d-%02d', $t, $bulan);

                $perTahun[(string) $t] = [
                    'masuk' => (int) ($masuk[$kunci] ?? 0),
                    'ditanggapi' => (int) ($ditanggapi[$kunci] ?? 0),
                ];
            }

            $bulanan[] = [
                'bulan' => $bulan,
                'label' => now()->setDate(2000, $bulan, 1)->translatedFormat('M'),
                'tahun' => $perTahun,
            ];
        }

        return [
            'tahun_utama' => $tahunUtama,
            'tahun_dibanding' => $tahunDipakai,
            'bulanan' => $bulanan,
            // Total setahun per tahun — dipakai panel untuk baris ringkasan.
            'total' => collect($tahunDipakai)
                ->mapWithKeys(fn ($t) => [(string) $t => [
                    'masuk' => collect($bulanan)->sum(fn ($b) => $b['tahun'][(string) $t]['masuk']),
                    'ditanggapi' => collect($bulanan)->sum(fn ($b) => $b['tahun'][(string) $t]['ditanggapi']),
                ]])
                ->all(),
        ];
    }

    /** Dipakai permohonan maupun keberatan — keduanya punya kolom `status`. */
    private function sebaranStatus(callable $kueri): array
    {
        return (clone $kueri())
            ->select('status', DB::raw('count(*) as jumlah'))
            ->groupBy('status')
            ->orderByDesc('jumlah')
            ->pluck('jumlah', 'status')
            ->map(fn ($n) => (int) $n)
            ->all();
    }

    private function sebaran(callable $kueri, string $kolom): array
    {
        return (clone $kueri())
            ->whereNotNull($kolom)
            ->select($kolom, DB::raw('count(*) as jumlah'))
            ->groupBy($kolom)
            ->orderByDesc('jumlah')
            ->pluck('jumlah', $kolom)
            ->map(fn ($n) => (int) $n)
            ->all();
    }

    /**
     * Hasil Verifikasi Data Diri para pemohon.
     *
     * Keempat keadaannya dikembalikan lengkap — termasuk `ditolak`, yang tidak
     * diminta secara khusus — supaya jumlahnya tetap sama dengan total pemohon.
     * Angka yang tidak menutup adalah angka yang tidak bisa dipercaya.
     */
    private function verifikasiPemohon(callable $pemohon): array
    {
        $sebaran = $this->sebaran($pemohon, 'status_verifikasi');

        return [
            // Sudah mengirim berkas, menunggu diperiksa petugas.
            'menunggu' => (int) ($sebaran['menunggu'] ?? 0),
            'terverifikasi' => (int) ($sebaran['terverifikasi'] ?? 0),
            // Belum mengirim berkas sama sekali.
            'belum' => (int) ($sebaran['belum'] ?? 0),
            'ditolak' => (int) ($sebaran['ditolak'] ?? 0),
        ];
    }

    /**
     * Pengajuan menurut jenis pemohonnya.
     *
     * Di-join, bukan lewat relasi Eloquent, karena yang dibutuhkan cuma satu
     * kolom pengelompokan — menarik seluruh baris pemohon untuk itu pemborosan.
     * Jenis yang kosong dikelompokkan sebagai `tidak_diisi` supaya barisnya
     * tidak lenyap diam-diam dari total.
     */
    private function perJenisPemohon(callable $kueri, string $tabel): array
    {
        return (clone $kueri())
            ->join('pemohon as p', 'p.id', '=', $tabel.'.pemohon_id')
            ->select(
                DB::raw("coalesce(p.jenis_pemohon, 'tidak_diisi') as jenis"),
                DB::raw('count(*) as jumlah')
            )
            ->groupBy('jenis')
            ->orderByDesc('jumlah')
            ->pluck('jumlah', 'jenis')
            ->map(fn ($n) => (int) $n)
            ->all();
    }

    /**
     * Capaian tiap indikator terhadap targetnya.
     *
     * `arah` menandai indikator yang justru lebih baik bila angkanya kecil
     * (rata-rata hari, rasio keberatan), supaya panel tidak salah memberi warna.
     */
    private function kpi(
        array $sla,
        ?float $rataHari,
        int $total,
        int $tuntas,
        int $jumlahKeberatan,
        ?float $kepuasan
    ): array {
        $penyelesaian = $total > 0 ? round(($tuntas / $total) * 100, 1) : null;
        $rasioKeberatan = $total > 0 ? round(($jumlahKeberatan / $total) * 100, 1) : null;

        $indikator = [
            [
                'kode' => 'kepatuhan_sla',
                'nama' => 'Kepatuhan batas waktu tanggapan',
                'satuan' => '%',
                'arah' => 'naik',
                'realisasi' => $sla['persen'],
                'target' => self::TARGET_KPI['kepatuhan_sla'],
                'dasar' => 'Pasal 22 UU No. 14 Tahun 2008 — tanggapan paling lambat 10 hari kerja.',
            ],
            [
                'kode' => 'rata_rata_hari',
                'nama' => 'Rata-rata waktu tanggapan',
                'satuan' => 'hari',
                'arah' => 'turun',
                'realisasi' => $rataHari,
                'target' => self::TARGET_KPI['rata_rata_hari'],
                'dasar' => 'Semakin singkat semakin baik; batas atasnya sama dengan SLA tanggapan.',
            ],
            [
                'kode' => 'penyelesaian',
                'nama' => 'Permohonan tuntas',
                'satuan' => '%',
                'arah' => 'naik',
                'realisasi' => $penyelesaian,
                'target' => self::TARGET_KPI['penyelesaian'],
                'dasar' => 'Berstatus selesai, ditolak, ditolak sebagian, atau kedaluwarsa.',
            ],
            [
                'kode' => 'kepuasan',
                'nama' => 'Kepuasan pemohon',
                'satuan' => '%',
                'arah' => 'naik',
                'realisasi' => $kepuasan,
                'target' => self::TARGET_KPI['kepuasan'],
                'dasar' => 'Rata-rata rating survei dibagi 5.',
            ],
            [
                'kode' => 'rasio_keberatan',
                'nama' => 'Rasio keberatan',
                'satuan' => '%',
                'arah' => 'turun',
                'realisasi' => $rasioKeberatan,
                'target' => self::TARGET_KPI['rasio_keberatan'],
                'dasar' => 'Jumlah keberatan dibagi jumlah permohonan pada periode yang sama.',
            ],
        ];

        return array_map(function (array $item) {
            $item['tercapai'] = $item['realisasi'] === null
                ? null
                : ($item['arah'] === 'naik'
                    ? $item['realisasi'] >= $item['target']
                    : $item['realisasi'] <= $item['target']);

            return $item;
        }, $indikator);
    }

    /**
     * Permohonan yang menuntut tindakan sekarang: sudah lewat batas waktu atau
     * tinggal beberapa hari lagi. Diurutkan dari yang paling mendesak.
     */
    private function perluTindakan(): array
    {
        return PermohonanInformasi::query()
            ->with('pemohon:id,nama')
            ->whereNotNull('batas_waktu_tanggapan')
            ->whereNull('tanggal_tanggapan')
            ->whereNotIn('status', self::STATUS_TUNTAS)
            ->where('batas_waktu_tanggapan', '<=', now()->addDays(self::AMBANG_SIAGA_HARI))
            ->orderBy('batas_waktu_tanggapan')
            ->limit(10)
            ->get()
            ->map(fn ($baris) => [
                'id' => $baris->id,
                'kode_permohonan' => $baris->kode_permohonan,
                'pemohon' => $baris->pemohon->nama ?? null,
                'status' => $baris->status,
                'batas_waktu' => optional($baris->batas_waktu_tanggapan)->toDateString(),
                // Negatif berarti masih ada sisa waktu.
                'telat_hari' => (int) now()->startOfDay()->diffInDays($baris->batas_waktu_tanggapan, false) * -1,
            ])
            ->all();
    }

    /** Tahun yang benar-benar punya data, untuk mengisi penyaring tahun. */
    private function tahunTersedia(): array
    {
        return PermohonanInformasi::query()
            ->whereNotNull('tanggal_permohonan')
            ->select(DB::raw('distinct extract(year from tanggal_permohonan)::int as tahun'))
            ->orderByDesc('tahun')
            ->pluck('tahun')
            ->map(fn ($t) => (int) $t)
            ->all();
    }
}
