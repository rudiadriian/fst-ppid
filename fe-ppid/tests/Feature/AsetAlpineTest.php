<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Alpine dimuat dari bundel sendiri, lengkap dengan plugin yang dipakai situs.
 *
 * Situs ini memakai `x-collapse` di header, FAQ, dan Standar Layanan. Direktif
 * itu bukan bawaan Alpine — ia datang dari plugin Collapse. Selama halaman
 * memuat Alpine dari `cdn.min.js`, pluginnya tidak ikut: console penuh
 * "You can't use [x-collapse] without first installing the Collapse plugin"
 * dan panelnya membuka-tutup tanpa animasi. Kerusakan seperti itu tidak
 * menggagalkan permintaan HTTP mana pun, jadi hanya pemeriksaan seperti ini
 * yang menangkapnya.
 */
class AsetAlpineTest extends TestCase
{
    use DatabaseTransactions;

    public function test_halaman_tidak_memuat_alpine_dari_cdn(): void
    {
        $html = $this->get(route('ppid.home'))->assertOk()->getContent();

        $this->assertStringNotContainsString('cdn.jsdelivr.net/npm/alpinejs', $html);
    }

    public function test_halaman_memuat_bundel_javascript_sendiri(): void
    {
        $html = $this->get(route('ppid.home'))->assertOk()->getContent();

        $this->assertMatchesRegularExpression(
            '#<script[^>]+src="[^"]*/build/assets/app-[^"]+\.js"#',
            $html,
            'Bundel resources/js/app.js tidak ikut dimuat halaman.'
        );
    }

    /** Bundelnya memang yang memasang plugin Collapse. */
    public function test_bundel_memasang_plugin_collapse(): void
    {
        $sumber = file_get_contents(resource_path('js/app.js'));

        $this->assertStringContainsString("@alpinejs/collapse", $sumber);
        $this->assertStringContainsString('Alpine.plugin(collapse)', $sumber);
    }

    /**
     * Direktif `x-collapse` memang dipakai — inilah yang membuat pluginnya
     * wajib. Kalau suatu saat tidak dipakai lagi, tes di atas boleh ditinjau
     * ulang; selama masih dipakai, pluginnya tidak boleh hilang.
     */
    public function test_x_collapse_masih_dipakai_halaman_publik(): void
    {
        $html = $this->get(route('ppid.home'))->assertOk()->getContent();

        $this->assertStringContainsString('x-collapse', $html);
    }
}
