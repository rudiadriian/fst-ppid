<?php

namespace Tests\Feature;

use App\Models\InformasiDikecualikan;
use App\Models\LaporanTahunan;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Galeri Laporan Tahunan di beranda dan dialog "melihat atau mengunduh".
 *
 * Aturannya satu, dipakai beranda maupun Daftar Informasi Dikecualikan:
 * membaca terbuka lewat tautan yang diisikan petugas; memperoleh salinannya
 * lewat Permohonan Informasi, yang menuntut masuk lebih dulu.
 */
class LaporanTahunanBerandaTest extends TestCase
{
    use DatabaseTransactions;

    private function laporan(array $ubah = []): LaporanTahunan
    {
        return LaporanTahunan::forceCreate(array_merge([
            'tahun' => 2025,
            'judul' => 'Laporan Tahunan Uji '.Str::random(6),
            'sampul' => 'uploads/laporan-tahunan/uji-sampul.jpg',
            'tautan' => 'https://contoh.test/laporan-tahunan-fstj/',
            'urutan' => 0,
            'status' => 'published',
        ], $ubah));
    }

    /**
     * Bentuk sebuah nilai sebagaimana `@js()` mencetaknya ke atribut Alpine:
     * literal JavaScript, jadi garis miringnya lolos menjadi `\/`.
     */
    private function sepertiDiJs(string $nilai): string
    {
        return trim(json_encode($nilai), '"');
    }

    public function test_beranda_menampilkan_sampul_yang_terbit(): void
    {
        $laporan = $this->laporan();

        $html = $this->get(route('ppid.home'))->assertOk()->getContent();

        $this->assertStringContainsString($laporan->judul, $html);
        $this->assertStringContainsString('uploads/laporan-tahunan/uji-sampul.jpg', $html);
        $this->assertStringContainsString('Laporan', $html);
    }

    public function test_entri_draft_tidak_tampil_di_beranda(): void
    {
        $laporan = $this->laporan(['status' => 'draft']);

        $html = $this->get(route('ppid.home'))->assertOk()->getContent();

        $this->assertStringNotContainsString($laporan->judul, $html);
    }

    /**
     * Dialognya membawa dua tujuan: tautan bacanya, dan rute Membuat
     * Permohonan untuk salinannya.
     */
    public function test_dialog_membawa_tautan_baca_dan_rute_permohonan(): void
    {
        $this->laporan();

        $html = $this->get(route('ppid.home'))->assertOk()->getContent();

        $this->assertStringContainsString('buka-dialog-lihat-unduh', $html);
        $this->assertStringContainsString($this->sepertiDiJs('https://contoh.test/laporan-tahunan-fstj/'), $html);
        $this->assertStringContainsString($this->sepertiDiJs(route('ppid.request')), $html);
        $this->assertStringContainsString('Mengunduh', $html);

        /*
         * Penerimanya ikut diperiksa, bukan hanya pengirimnya. Tombol yang
         * mengirim event dan dialog yang menunggunya pernah memakai nama event
         * berbeda — halamannya tampil wajar, tombolnya diam saja.
         */
        $this->assertStringContainsString('@buka-dialog-lihat-unduh.window', $html);
        $this->assertStringContainsString('Hanya Lihat', $html);
    }

    /**
     * Tombol sampulnya harus berada di dalam komponen Alpine.
     *
     * `$dispatch` hanya tersedia di dalam `x-data`. Tanpa itu tombolnya diam
     * saja saat diklik — markupnya tetap lengkap dan halamannya tidak
     * menunjukkan galat apa pun, jadi tidak ada yang menangkapnya selain
     * pemeriksaan ini.
     */
    public function test_tombol_sampul_berada_di_dalam_komponen_alpine(): void
    {
        $this->laporan();

        $html = $this->get(route('ppid.home'))->assertOk()->getContent();

        $section = strpos($html, '<section id="laporan-tahunan"');
        $tombol = strpos($html, "\$dispatch('buka-dialog-lihat-unduh'");

        $this->assertNotFalse($section, 'Section Laporan Tahunan tidak dirender.');
        $this->assertNotFalse($tombol, 'Tombol sampul tidak dirender.');

        // `x-data` harus berada pada pembuka section itu sendiri, sebelum
        // tombolnya — kalau tidak, tombolnya berada di luar komponen.
        $pembuka = substr($html, $section, strpos($html, '>', $section) - $section);

        $this->assertStringContainsString('x-data', $pembuka);
        $this->assertLessThan($tombol, $section);
    }

    /** Label section dua baris: nama laporan, lalu nama perusahaan. */
    public function test_label_section_dua_baris(): void
    {
        $this->laporan();

        $html = $this->get(route('ppid.home'))->assertOk()->getContent();

        // Kata terakhir baris pertama dibungkus span aksen, jadi yang
        // dicocokkan potongan sebelum dan sesudah pembungkusnya.
        $this->assertStringContainsString('Laporan <span class="fs-title-accent">Tahunan</span>', $html);
        $this->assertStringContainsString('PT Food Station Tjipinang Jaya (Perseroda)', $html);
    }

    /** Tanpa tautan, tombol "Melihat" tidak dipasang — tapi dialognya tetap ada. */
    public function test_tanpa_tautan_hanya_jalur_permohonan(): void
    {
        $this->laporan(['tautan' => null]);

        $html = $this->get(route('ppid.home'))->assertOk()->getContent();

        $this->assertStringContainsString('buka-dialog-lihat-unduh', $html);
        $this->assertStringContainsString('tautan: null', $html);
    }

    /**
     * Tamu yang memilih "Mengunduh" berakhir di halaman masuk, bukan di
     * formulir permohonan.
     */
    public function test_tamu_yang_mengunduh_diantar_ke_halaman_masuk(): void
    {
        $this->get(route('ppid.request'))
            ->assertRedirect(route('akun.permohonan.create'));

        $this->get(route('akun.permohonan.create'))
            ->assertRedirect(route('akun.login'));
    }

    /**
     * Daftar Informasi Dikecualikan memakai dialog yang sama.
     *
     * Tanpa ini, dua halaman yang menawarkan pilihan serupa gampang berpisah
     * jalan: satu memakai dialog, satu lagi tautan langsung.
     */
    public function test_daftar_dikecualikan_memakai_dialog_yang_sama(): void
    {
        $baris = InformasiDikecualikan::forceCreate([
            'judul' => 'Informasi Dikecualikan Uji '.Str::random(6),
            'slug' => 'informasi-dikecualikan-uji-'.Str::lower(Str::random(8)),
            'alasan_pengecualian' => 'uji',
            'tautan' => 'https://contoh.test/keterangan-pengecualian',
            'status' => 'published',
        ]);

        $html = $this->get(route('ppid.excluded'))->assertOk()->getContent();

        $this->assertStringContainsString($baris->judul, $html);
        $this->assertStringContainsString('buka-dialog-lihat-unduh', $html);
        $this->assertStringContainsString($this->sepertiDiJs('https://contoh.test/keterangan-pengecualian'), $html);
        $this->assertStringContainsString($this->sepertiDiJs(route('ppid.request')), $html);
        $this->assertStringContainsString('Lihat / Unduh', $html);
        $this->assertStringContainsString('@buka-dialog-lihat-unduh.window', $html);
    }
}
