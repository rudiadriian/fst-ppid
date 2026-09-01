<?php

namespace Tests\Feature;

use App\Models\Maklumat;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Halaman Standar Layanan → Maklumat Pelayanan (langkah 88).
 *
 * Yang diuji dua keadaan halamannya: saat dokumen maklumat sudah diunggah
 * (tayang sebagai gambar, tanpa judul "Detail …"), dan saat belum (kembali ke
 * teks bawaan beserta judulnya).
 */
class MaklumatHalamanTest extends TestCase
{
    use DatabaseTransactions;

    private string $tanda;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tanda = Str::random(8);
    }

    /**
     * Maklumat terbit dengan tanggal paling baru; situs publik memilih baris
     * inilah, bukan yang sudah ada di basis data.
     */
    private function buatTerbit(?string $berkas): Maklumat
    {
        return Maklumat::forceCreate([
            'judul' => 'Maklumat Uji '.$this->tanda,
            'ringkasan' => 'Pengantar uji '.$this->tanda,
            'file_dokumen' => $berkas,
            'tanggal_terbit' => now()->addYear()->toDateString(),
            'status' => 'published',
        ]);
    }

    public function test_dokumen_gambar_tayang_tanpa_judul_detail(): void
    {
        $maklumat = $this->buatTerbit('uploads/maklumat/uji/'.Str::random(10).'.png');

        $isi = $this->get('/standar-layanan/maklumat-pelayanan')
            ->assertOk()
            ->assertSee($maklumat->judul)
            ->assertSee($maklumat->file_dokumen)
            // Judul "Detail Maklumat Pelayanan Informasi Publik" dihapus dari
            // halaman yang sudah menayangkan dokumennya.
            ->assertDontSee('Detail ')
            // Ketiganya dilepas pada langkah 88; yang tersisa dokumennya saja.
            ->assertDontSee($maklumat->ringkasan)
            ->assertDontSee('Buka di tab baru')
            ->assertDontSee('Unduh Maklumat')
            // Keterangan penerbitan tetap, sebagai keterangan gambar.
            ->assertSee('Terbit')
            ->getContent();

        // Gambarnya sendiri yang jadi tautannya — itu satu-satunya jalan
        // membuka ukuran penuh setelah tombolnya dilepas.
        $this->assertStringContainsString('Buka gambar ukuran penuh', $isi, 'Gambar maklumat tidak bisa dibuka ukuran penuh.');
    }

    public function test_tanpa_dokumen_halaman_kembali_ke_teks_bawaan(): void
    {
        // Semua maklumat terbit dikosongkan berkasnya, termasuk baris yang
        // sudah ada, supaya keadaan "belum diunggah" benar-benar tercapai.
        Maklumat::where('status', 'published')->update(['file_dokumen' => null]);

        $this->get('/standar-layanan/maklumat-pelayanan')
            ->assertOk()
            ->assertSee('Kami berkomitmen memberikan pelayanan informasi publik yang cepat, mudah, dan transparan.', false)
            ->assertSee('Detail');
    }
}
