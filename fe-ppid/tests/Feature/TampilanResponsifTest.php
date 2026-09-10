<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Pagar tampilan pada layar sempit (UAT poin 13).
 *
 * Yang bisa diperiksa tanpa peramban adalah sebabnya, bukan rupanya: geseran
 * mendatar di ponsel hampir selalu lahir dari satu elemen yang lebih lebar
 * daripada layarnya — tabel, gambar, atau media sematan. Tes ini menjaga
 * ketiganya:
 *
 *  - setiap `<table>` pada halaman publik berada di dalam pembungkus yang boleh
 *    digeser sendiri (`overflow-x-auto`), jadi yang bergeser tabelnya, bukan
 *    seluruh halaman;
 *  - aturan dasar "media tidak melebihi wadahnya" tetap ada di stylesheet;
 *  - tabel dari isi CMS — yang jumlah kolomnya tidak bisa ditentukan template —
 *    tetap dibungkus penggeser oleh `resources/js/app.js`.
 *
 * Rupa halamannya sendiri (ukuran huruf, jarak, susunan kartu) tidak diuji di
 * sini; itu penilaian mata pada perangkat sungguhan.
 */
class TampilanResponsifTest extends TestCase
{
    use DatabaseTransactions;

    /** Halaman publik yang memuat tabel. */
    public static function halamanBertabel(): array
    {
        return [
            'daftar informasi publik' => ['/informasi'],
            'daftar informasi dikecualikan' => ['/informasi/dikecualikan'],
            'register permohonan' => ['/register-permohonan'],
        ];
    }

    /**
     * @dataProvider halamanBertabel
     */
    public function test_tabel_halaman_publik_bisa_digeser_sendiri(string $uri): void
    {
        $html = $this->get($uri)->assertOk()->getContent();

        $dom = new \DOMDocument();
        // Markupnya HTML5 dan memuat entitas; peringatan parser tidak relevan
        // untuk yang diperiksa di sini.
        @$dom->loadHTML('<?xml encoding="utf-8" ?>'.$html);

        $xpath = new \DOMXPath($dom);
        $tabel = $xpath->query('//table');

        $this->assertGreaterThan(0, $tabel->length, "Halaman {$uri} tidak lagi memuat tabel; tes ini perlu disesuaikan.");

        foreach ($tabel as $node) {
            $adaPenggeser = false;

            for ($induk = $node->parentNode; $induk instanceof \DOMElement; $induk = $induk->parentNode) {
                $kelas = (string) $induk->getAttribute('class');

                if (str_contains($kelas, 'overflow-x-auto')) {
                    $adaPenggeser = true;
                    break;
                }
            }

            $this->assertTrue(
                $adaPenggeser,
                "Ada tabel di {$uri} yang tidak berada dalam pembungkus `overflow-x-auto`; "
                    .'di layar sempit isinya akan terpotong atau menggeser seluruh halaman.'
            );
        }
    }

    public function test_stylesheet_menjaga_media_tidak_melebihi_wadahnya(): void
    {
        $css = file_get_contents(resource_path('css/app.css'));

        $this->assertMatchesRegularExpression(
            '/img,\s*\n?\s*video,\s*\n?\s*iframe,\s*\n?\s*canvas\s*\{\s*max-width:\s*100%;/',
            $css,
            'Aturan dasar "media tidak melebihi wadahnya" hilang dari app.css.'
        );

        $this->assertStringContainsString('.fs-tabel-scroll', $css, 'Kelas pembungkus tabel rich text hilang dari app.css.');
    }

    public function test_tabel_isi_cms_dibungkus_penggeser(): void
    {
        $js = file_get_contents(resource_path('js/app.js'));

        $this->assertStringContainsString('.fs-rte table', $js);
        $this->assertStringContainsString('fs-tabel-scroll', $js);
    }
}
