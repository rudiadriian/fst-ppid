<?php

namespace App\Http\Controllers;

use App\Models\BannerSlider;
use App\Models\Berita;
use App\Models\Faq;
use App\Models\KategoriInformasi;
use App\Support\Cms;

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
            'news' => $this->beritaTerbaru(),
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

        // Judul dan ringkasan ikut tiap gambar: slider beranda menampilkan teks
        // milik slide yang sedang tayang. Slide tanpa judul memakai teks bawaan
        // beranda (ditangani di view), jadi banner lama tetap tampil wajar.
        //
        // Modul Banner tidak lagi punya kolom isian versi Inggris. Urutan
        // pencariannya: kolom `*_en` bila kebetulan terisi (lewat `teks()`),
        // lalu kamus situs `lang/en.json` lewat `__()`. Kalimat yang belum ada
        // di kamus tetap tampil dalam Bahasa Indonesia — tidak ada penerjemah
        // otomatis di sini.
        $terjemah = fn (?string $teks) => filled($teks) ? __($teks) : '';

        return $baris->map(fn ($b) => [
            'image' => Cms::url($b->gambar),
            'caption' => $terjemah($b->teks('judul')),
            'ringkasan' => $terjemah($b->teks('ringkasan')),
            'link' => $b->link,
        ])->all();
    }

    /** Tiga kartu klasifikasi informasi publik beserta jumlah dokumennya. */
    private function kategoriInformasi(): array
    {
        $cadangan = [
            ['title' => 'Informasi Berkala', 'count' => 0, 'slug' => 'berkala', 'desc' => 'Informasi yang wajib disediakan dan diumumkan secara berkala.'],
            ['title' => 'Informasi Tersedia Setiap Saat', 'count' => 0, 'slug' => 'setiap-saat', 'desc' => 'Informasi yang wajib tersedia setiap saat bagi publik.'],
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
            'title' => $k->teks('nama'),
            'count' => (int) $k->jumlah,
            'slug' => $k->slug,
            'desc' => $k->teks('deskripsi') ?: '',
        ])->all();
    }

    // Statistik ringkas (Pemohon/Dokumen/Regulasi/Kepuasan) sempat pindah ke
    // halaman Laporan Statistik Informasi Publik, lalu ikut hilang bersama
    // halaman itu pada langkah 68. Tidak ada penggantinya di situs publik.

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
            'title' => $b->teks('judul'),
            'date' => Cms::tanggal($b->tanggal_publikasi),
            'category' => $b->kategori?->teks('nama') ?? __('Publikasi'),
            'excerpt' => $b->teks('ringkasan') ?: str($b->teks('konten') ?? '')->stripTags()->limit(160)->toString(),
            'image' => Cms::url($b->thumbnail) ?: asset('assets/images/logo/logo_fs.png'),
            'url' => route('ppid.news.show', $b->slug),
        ])->all();
    }

    /** Pertanyaan umum aktif, urut sesuai pengaturan CMS. */
    private function faq(): array
    {
        $baris = Cms::ambil(fn () => Faq::aktif()->limit(8)->get(), collect(), 'faq');

        return $baris->map(fn ($f) => [
            'q' => $f->teks('pertanyaan'),
            'a' => strip_tags($f->teks('jawaban')),
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
                /*
                 * Cadangan bila baris `kontak.jam_layanan` belum ada di CMS.
                 * Nilainya harus sama dengan tabel Waktu Layanan di
                 * `PpidController::showServiceStandardPage()` — dua tempat yang
                 * mengumumkan jam berbeda lebih buruk daripada satu tempat yang
                 * salah, karena tidak ada yang tahu mana yang benar.
                 */
                'value' => Cms::pengaturan(
                    'kontak.jam_layanan',
                    'Senin–Jumat, 08.00–15.00 WIB (istirahat 12.00–13.00 WIB)'
                ),
                /*
                 * Hari, jam buka, dan jam istirahat dipecah jadi tiga baris
                 * (langkah 82).
                 *
                 * Satu kalimat panjang berkurung — "Senin–Jumat, 08.00–15.00
                 * WIB (istirahat 12.00–13.00 WIB)" — memaksa orang membaca
                 * seluruhnya untuk menemukan satu angka. Dipisah per baris,
                 * jam buka dan jam istirahat langsung terbaca sekilas.
                 *
                 * `value` di atas tetap dipertahankan: ia yang dipakai bila
                 * petugas menyunting jam layanan lewat CMS menjadi bentuk yang
                 * tidak dikenali pemecah di bawah.
                 */
                'baris' => self::jamLayananPerBaris(),
                'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
            ],
        ];
    }

    /**
     * Jam layanan sebagai baris-baris terpisah.
     *
     * Dibaca dari `kontak.jam_layanan` bila bentuknya masih seperti yang
     * ditulis seeder; kalau petugas menggantinya dengan kalimat lain, hasilnya
     * `null` dan view kembali menampilkan `value` apa adanya. Menebak-nebak
     * struktur dari teks bebas hanya akan memotongnya di tempat yang salah.
     *
     * @return array<int, string>|null
     */
    private static function jamLayananPerBaris(): ?array
    {
        $nilai = (string) Cms::pengaturan(
            'kontak.jam_layanan',
            'Senin–Jumat, 08.00–15.00 WIB (istirahat 12.00–13.00 WIB)'
        );

        // "Senin–Jumat, 08.00–15.00 WIB (istirahat 12.00–13.00 WIB)"
        $pola = '/^(.+?),\s*(.+?)\s*\(\s*istirahat\s*(.+?)\s*\)$/iu';

        if (!preg_match($pola, trim($nilai), $cocok)) {
            return null;
        }

        return [
            trim($cocok[1]),
            'Pukul '.trim($cocok[2]),
            'Istirahat Pukul '.trim($cocok[3]),
        ];
    }
}
