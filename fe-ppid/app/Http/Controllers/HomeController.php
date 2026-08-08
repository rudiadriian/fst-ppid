<?php

namespace App\Http\Controllers;

use App\Models\BannerSlider;
use App\Models\Berita;
use App\Models\Faq;
use App\Models\Galeri;
use App\Models\InformasiPublik;
use App\Models\KategoriInformasi;
use App\Models\LaporanLayanan;
use App\Models\PermohonanInformasi;
use App\Models\Regulasi;
use App\Support\Cms;
use Illuminate\Support\Facades\DB;

/**
 * Beranda situs publik.
 *
 * Seluruh isi beranda datang dari CMS (`be-ppid` → `ppiddb`). Setiap bagian
 * punya data cadangan bawaan yang dipakai saat tabelnya masih kosong atau
 * database sedang bermasalah — beranda tidak pernah tampil kosong.
 */
class HomeController extends Controller
{
    public function index()
    {
        return view('ppid.home', [
            'heroSlides' => $this->heroSlides(),
            'infoPublik' => $this->kategoriInformasi(),
            'stats' => $this->statistik(),
            'news' => $this->beritaTerbaru(),
            'arsipSlides' => $this->arsipSlides(),
            'reports' => $this->laporanTerbaru(),
            'faqs' => $this->faq(),
            'contacts' => $this->kontak(),
            'db_offline' => Cms::offline(),
        ]);
    }

    /** Slider utama — modul Banner Slider di CMS. */
    private function heroSlides(): array
    {
        // Hero memakai slider full-bleed: gambar dipasang sebagai latar penuh.
        // Kalau CMS belum punya banner, kembalikan array kosong supaya hero
        // jatuh ke gradasi hijau — logo tidak layak di-stretch jadi background.
        $baris = Cms::ambil(fn () => BannerSlider::tayang()->get(), collect(), 'banner_slider');

        if ($baris->isEmpty()) {
            return [];
        }

        return $baris->map(fn ($b) => [
            'image' => Cms::url($b->gambar),
            'caption' => $b->judul ?: '',
            'link' => $b->link,
        ])->all();
    }

    /** Tiga kartu klasifikasi informasi publik beserta jumlah dokumennya. */
    private function kategoriInformasi(): array
    {
        $cadangan = [
            ['title' => 'Informasi Berkala', 'count' => 0, 'slug' => 'berkala', 'desc' => 'Informasi yang wajib disediakan dan diumumkan secara berkala.'],
            ['title' => 'Informasi Setiap Saat', 'count' => 0, 'slug' => 'setiap-saat', 'desc' => 'Informasi yang wajib tersedia setiap saat bagi publik.'],
            ['title' => 'Informasi Serta Merta', 'count' => 0, 'slug' => 'serta-merta', 'desc' => 'Informasi yang wajib diumumkan tanpa penundaan.'],
        ];

        $baris = Cms::ambil(
            fn () => KategoriInformasi::aktif()
                ->whereNull('parent_id')
                ->withCount(['informasiPublik as jumlah' => fn ($q) => $q->where('status', 'published')->whereNull('deleted_at')])
                ->limit(3)
                ->get(),
            collect(),
            'kategori_informasi'
        );

        if ($baris->isEmpty()) {
            return $cadangan;
        }

        return $baris->map(fn ($k) => [
            'title' => $k->nama,
            'count' => (int) $k->jumlah,
            'slug' => $k->slug,
            'desc' => $k->deskripsi ?: '',
        ])->all();
    }

    /** Empat angka ringkas: permohonan, dokumen, regulasi, kepuasan. */
    private function statistik(): array
    {
        $angka = Cms::ambil(function () {
            $rating = DB::table('survey_kepuasan')->avg('rating');

            return [
                'permohonan' => PermohonanInformasi::count(),
                'dokumen' => InformasiPublik::published()->count(),
                'regulasi' => Regulasi::count(),
                'kepuasan' => $rating ? round(((float) $rating / 5) * 100).'%' : '—',
            ];
        }, null, 'statistik_beranda');

        if ($angka === null) {
            return [
                ['value' => '—', 'label' => 'Permohonan'],
                ['value' => '—', 'label' => 'Dokumen'],
                ['value' => '—', 'label' => 'Regulasi'],
                ['value' => '—', 'label' => 'Kepuasan'],
            ];
        }

        return [
            ['value' => number_format($angka['permohonan'], 0, ',', '.'), 'label' => 'Permohonan'],
            ['value' => number_format($angka['dokumen'], 0, ',', '.'), 'label' => 'Dokumen'],
            ['value' => number_format($angka['regulasi'], 0, ',', '.'), 'label' => 'Regulasi'],
            ['value' => $angka['kepuasan'], 'label' => 'Kepuasan'],
        ];
    }

    /** Tiga berita terbaru yang sudah diterbitkan. */
    private function beritaTerbaru(): array
    {
        $baris = Cms::ambil(
            fn () => Berita::published()
                ->with('kategori:id,nama')
                ->orderByDesc('tanggal_publikasi')
                ->orderByDesc('id')
                ->limit(3)
                ->get(),
            collect(),
            'berita'
        );

        return $baris->map(fn ($b) => [
            'title' => $b->judul,
            'date' => Cms::tanggal($b->tanggal_publikasi),
            'category' => $b->kategori->nama ?? 'Publikasi',
            'excerpt' => $b->ringkasan ?: str($b->konten ?? '')->stripTags()->limit(160)->toString(),
            'image' => Cms::url($b->thumbnail) ?: asset('assets/images/logo/logo_fs.png'),
            'url' => route('ppid.news.show', $b->slug),
        ])->all();
    }

    /** Slider arsip — memakai galeri foto terbaru. */
    private function arsipSlides(): array
    {
        $baris = Cms::ambil(
            fn () => Galeri::where('tipe', 'foto')->orderByDesc('tanggal')->orderByDesc('id')->limit(3)->get(),
            collect(),
            'galeri'
        );

        if ($baris->isEmpty()) {
            return [
                [
                    'image' => asset('assets/images/logo/logo_fs.png'),
                    'title' => 'Arsip Resmi',
                    'subtitle' => 'Dokumen dan laporan resmi perusahaan',
                ],
            ];
        }

        return $baris->map(fn ($g) => [
            'image' => Cms::url($g->path_file),
            'title' => $g->judul ?: 'Dokumentasi',
            'subtitle' => $g->deskripsi ?: '',
        ])->all();
    }

    /** Tiga laporan terbaru yang berkasnya sudah diterbitkan. */
    private function laporanTerbaru(): array
    {
        $baris = Cms::ambil(
            fn () => LaporanLayanan::published()->orderByDesc('tahun')->orderByDesc('id')->limit(3)->get(),
            collect(),
            'laporan_layanan'
        );

        return $baris->map(fn ($l) => [
            'title' => $l->judul,
            'year' => $l->tahun,
            'size' => $this->ukuranBerkas($l->file_laporan),
            'url' => Cms::url($l->file_laporan),
        ])->all();
    }

    /** Pertanyaan umum aktif, urut sesuai pengaturan CMS. */
    private function faq(): array
    {
        $baris = Cms::ambil(fn () => Faq::aktif()->limit(8)->get(), collect(), 'faq');

        return $baris->map(fn ($f) => [
            'q' => $f->pertanyaan,
            'a' => strip_tags($f->jawaban),
        ])->all();
    }

    /** Blok kontak; nilainya diambil dari modul Pengaturan Situs. */
    private function kontak(): array
    {
        return [
            [
                'label' => 'Alamat',
                'value' => Cms::pengaturan(
                    'kontak.alamat',
                    'Komplek Pasar Induk Beras Cipinang, Jl. Pisangan Lama Selatan No. 1, Jakarta Timur 13230'
                ),
                'icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z',
            ],
            [
                'label' => 'Email & Telepon',
                'value' => trim(
                    Cms::pengaturan('kontak.email', 'ppid@foodstation.co.id')
                    .' · '
                    .Cms::pengaturan('kontak.telepon', '021-471 8011')
                ),
                'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
            ],
            [
                'label' => 'Jam Layanan',
                'value' => Cms::pengaturan('kontak.jam_layanan', 'Senin–Jumat, 08.00–17.00 WIB'),
                'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
            ],
        ];
    }

    /**
     * Ukuran berkas dibaca dari disk publik; berkas yang belum ada tidak
     * boleh membuat beranda gagal dimuat.
     */
    private function ukuranBerkas(?string $path): string
    {
        if (blank($path)) {
            return '—';
        }

        try {
            $disk = \Illuminate\Support\Facades\Storage::disk('public');
            $relatif = ltrim($path, '/');

            return $disk->exists($relatif) ? Cms::ukuran($disk->size($relatif)) : '—';
        } catch (\Throwable $e) {
            return '—';
        }
    }
}
