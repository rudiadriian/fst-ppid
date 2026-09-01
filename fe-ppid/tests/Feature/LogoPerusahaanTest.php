<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Logo perusahaan pada header dan footer situs publik (langkah 85).
 *
 * Berkasnya `logo_fs.png` — nama yang sama seperti sebelumnya, isinya artwork
 * baru. Menyimpannya di nama yang sama membuat kesebelas rujukan di seluruh
 * view ikut berganti tanpa satu pun disunting; yang diuji di sini bahwa
 * berkasnya benar-benar ada dan benar-benar dipasang di dua tempat yang
 * diminta.
 */
class LogoPerusahaanTest extends TestCase
{
    use DatabaseTransactions;

    private string $logo = 'assets/images/logo/logo_fs.png';

    public function test_berkas_logo_ada_dan_berupa_png(): void
    {
        $path = public_path($this->logo);

        $this->assertFileExists($path);

        $ukuran = getimagesize($path);

        $this->assertSame('image/png', $ukuran['mime']);
        // Logo baru berbanding 707x243. Nilainya diikat supaya penggantian
        // berkas yang tidak disengaja — mis. kembali ke logo lama 1382x629 —
        // gagal di sini, bukan baru ketahuan di layar orang.
        $this->assertSame(707, $ukuran[0]);
        $this->assertSame(243, $ukuran[1]);
    }

    public function test_logo_dipasang_di_header_dan_footer(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        /*
         * Tiga kemunculan: apple-touch-icon di <head>, header, dan footer.
         * Yang dijaga angkanya bukan tepatnya tiga, melainkan bahwa header dan
         * footer sama-sama kebagian — karena itu batas bawahnya dua.
         */
        $this->assertGreaterThanOrEqual(2, substr_count($html, $this->logo));
    }

    /**
     * Header dan footer memakai tinggi tetap dengan lebar mengikuti.
     *
     * Logo baru jauh lebih lebar daripada yang lama (2,91 : 1 lawan 2,20 : 1).
     * Begitu salah satunya dikunci lebarnya, logo itu memipih — dan pemipihan
     * logo resmi perusahaan bukan cacat kosmetik kecil.
     */
    public function test_header_dan_footer_tidak_mengunci_lebar_logo(): void
    {
        foreach (['layouts/header.blade.php', 'layouts/footer.blade.php'] as $berkas) {
            $isi = file_get_contents(resource_path('views/'.$berkas));

            $posisi = strpos($isi, $this->logo);
            $this->assertNotFalse($posisi, "Logo tidak ditemukan di {$berkas}");

            // Potongan markup tepat setelah src-nya memuat kelas ukurannya.
            $potongan = substr($isi, $posisi, 400);

            $this->assertStringContainsString('w-auto', $potongan, "Lebar logo dikunci di {$berkas}");
        }
    }
}
