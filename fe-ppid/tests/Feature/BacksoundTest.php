<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Backsound jingle pada situs publik (langkah 84).
 *
 * Yang diuji bukan bunyinya — itu urusan peramban — melainkan tiga aturan yang
 * menentukan di mana ia dipasang dan bagaimana ia bisa dihentikan.
 */
class BacksoundTest extends TestCase
{
    use DatabaseTransactions;

    public function test_backsound_dipasang_di_halaman_publik(): void
    {
        foreach (['/', '/informasi', '/regulasi', '/faq'] as $uri) {
            $html = $this->get($uri)->assertOk()->getContent();

            $this->assertStringContainsString('backsoundPpid', $html, "Backsound hilang di {$uri}");
            $this->assertStringContainsString('jingle-food-station.mp4', $html, "Berkas jingle hilang di {$uri}");
        }
    }

    /**
     * Portal pengguna tidak diberi musik latar.
     *
     * Di sanalah orang mengetik permohonan dan data diri; suara yang menyala
     * sendiri mengganggu pekerjaan, bukan menyambut tamu.
     */
    public function test_portal_pengguna_tanpa_backsound(): void
    {
        foreach (['/akun/masuk', '/akun/daftar'] as $uri) {
            $html = $this->get($uri)->assertOk()->getContent();

            $this->assertStringNotContainsString('backsoundPpid', $html, "Backsound seharusnya tidak ada di {$uri}");
        }
    }

    /**
     * Harus ada cara mematikannya, dan pilihannya harus diingat.
     *
     * WCAG 2.1 kriteria 1.4.2: suara yang berbunyi otomatis lebih dari tiga
     * detik wajib punya mekanisme penghenti. Tanpa tombol ini, satu-satunya
     * cara berhenti adalah menutup situsnya.
     */
    public function test_ada_tombol_mematikan_yang_diingat(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('Matikan musik latar', $html);
        // Pilihannya disimpan, jadi tidak perlu dimatikan ulang tiap halaman.
        $this->assertStringContainsString('localStorage', $html);
        $this->assertStringContainsString('ppid.backsound', $html);
    }

    public function test_berkas_jingle_tersedia(): void
    {
        $this->assertFileExists(public_path('assets/audio/jingle-food-station.mp4'));
    }
}
