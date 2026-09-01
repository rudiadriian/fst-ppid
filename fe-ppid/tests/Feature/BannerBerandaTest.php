<?php

namespace Tests\Feature;

use App\Models\BannerSlider;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Banner hero beranda (langkah 90).
 *
 * Yang diuji tiga hal yang menentukan tampilan berandanya: gambar banner
 * dipasang, judul dan ringkasan milik banner yang dipakai — bukan teks bawaan
 * template — dan judulnya ikut memakai konsep judul dua warna.
 */
class BannerBerandaTest extends TestCase
{
    use DatabaseTransactions;

    public function test_hero_memakai_gambar_judul_dan_ringkasan_banner(): void
    {
        $tanda = Str::random(8);

        // Urutan 0 supaya banner uji ini tayang lebih dulu daripada banner yang
        // sudah ada di basis data.
        $banner = BannerSlider::forceCreate([
            'judul' => "Selamat Datang Uji $tanda",
            'ringkasan' => "Ringkasan uji $tanda",
            'gambar' => 'uploads/banner/uji/'.Str::random(10).'.png',
            'urutan' => 0,
            'is_active' => true,
        ]);

        $isi = $this->get('/')
            ->assertOk()
            ->assertSee($banner->gambar)
            ->assertSee("Ringkasan uji $tanda")
            ->getContent();

        // Judulnya dipecah dua warna, jadi teksnya tidak utuh di HTML —
        // yang diperiksa potongan pertamanya beserta penanda aksennya.
        $this->assertStringContainsString('Selamat Datang Uji', $isi);
        $this->assertStringContainsString('fs-title-accent-soft', $isi);
    }

    public function test_banner_nonaktif_tidak_tayang(): void
    {
        $tanda = Str::random(8);

        BannerSlider::forceCreate([
            'judul' => "Banner Nonaktif $tanda",
            'gambar' => 'uploads/banner/uji/'.Str::random(10).'.png',
            'urutan' => 0,
            'is_active' => false,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertDontSee("Banner Nonaktif $tanda");
    }
}
