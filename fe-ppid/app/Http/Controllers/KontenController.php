<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use App\Models\Faq;
use App\Models\StrukturOrganisasi;
use App\Support\Cms;
use Illuminate\Http\Request;

/**
 * Kanal konten yang isinya sepenuhnya dikelola lewat CMS:
 * berita, pertanyaan umum, dan struktur organisasi.
 */
class KontenController extends Controller
{
    /** Daftar berita, 9 per halaman. */
    public function beritaIndex(Request $request)
    {
        $berita = Cms::ambil(
            fn () => Berita::published()
                ->with('kategori:id,nama')
                ->orderByDesc('tanggal_publikasi')
                ->orderByDesc('id')
                ->paginate(9)
                ->withQueryString(),
            null,
            'berita_index'
        );

        $items = $berita
            ? collect($berita->items())->map(fn ($b) => $this->kartuBerita($b))->all()
            : [];

        return view('ppid.news_index', [
            'items' => $items,
            'paginator' => $berita,
            'data' => ['db_offline' => Cms::offline()],
        ]);
    }

    /** Satu berita. Penghitung dibaca-tambah tanpa mengubah `updated_at`. */
    public function beritaShow(string $slug)
    {
        $berita = Cms::ambil(
            fn () => Berita::published()->with('kategori:id,nama')->where('slug', $slug)->first(),
            null,
            'berita_show'
        );

        if (!$berita) {
            abort(404, 'Berita tidak ditemukan.');
        }

        // Penghitung tampilan tidak boleh mengubah `updated_at` — kolom itu
        // dipakai CMS untuk menandai kapan konten terakhir disunting.
        Cms::ambil(function () use ($berita) {
            $berita->timestamps = false;
            $berita->increment('views_count');

            return true;
        }, null, 'berita_views');

        $lainnya = Cms::ambil(
            fn () => Berita::published()
                ->whereKeyNot($berita->id)
                ->orderByDesc('tanggal_publikasi')
                ->limit(3)
                ->get()
                ->map(fn ($b) => $this->kartuBerita($b))
                ->all(),
            [],
            'berita_lainnya'
        );

        return view('ppid.news_show', [
            'berita' => $berita,
            'tanggal' => Cms::tanggal($berita->tanggal_publikasi),
            'kategori' => $berita->kategori->nama ?? 'Publikasi',
            'gambar' => Cms::url($berita->thumbnail),
            'lainnya' => $lainnya,
            'data' => ['db_offline' => Cms::offline()],
        ]);
    }

    /** Halaman pertanyaan umum. */
    public function faq()
    {
        $items = Cms::ambil(fn () => Faq::aktif()->get(), collect(), 'faq_index');

        $grup = $items
            ->groupBy(fn ($f) => $f->kategori ?: 'Umum')
            ->map(fn ($rows) => $rows->map(fn ($f) => [
                'pertanyaan' => $f->pertanyaan,
                'jawaban' => $f->jawaban,
            ])->all())
            ->all();

        return view('ppid.faq', [
            'grup' => $grup,
            'data' => ['db_offline' => Cms::offline()],
        ]);
    }

    /** Bagan dan susunan pejabat PPID. */
    public function struktur()
    {
        $anggota = Cms::ambil(fn () => StrukturOrganisasi::aktif()->get(), collect(), 'struktur_organisasi');

        return view('ppid.structure', [
            // Bagan disusun dari kolom parent_id/tipe_node yang diisi lewat CMS.
            'bagan' => StrukturOrganisasi::pohon($anggota),
            'anggota' => $anggota->map(fn ($a) => [
                'nama' => $a->nama,
                'jabatan' => $a->jabatan,
                'foto' => Cms::url($a->foto),
                'deskripsi' => $a->deskripsi,
            ])->all(),
            'data' => ['db_offline' => Cms::offline()],
        ]);
    }

    private function kartuBerita(Berita $b): array
    {
        return [
            'title' => $b->judul,
            'date' => Cms::tanggal($b->tanggal_publikasi),
            'category' => $b->kategori->nama ?? 'Publikasi',
            'excerpt' => $b->ringkasan ?: str($b->konten ?? '')->stripTags()->limit(160)->toString(),
            'image' => Cms::url($b->thumbnail) ?: asset('assets/images/logo/logo_fs.png'),
            'url' => route('ppid.news.show', $b->slug),
        ];
    }
}
