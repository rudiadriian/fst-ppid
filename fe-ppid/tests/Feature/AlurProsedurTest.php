<?php

namespace Tests\Feature;

use App\Models\AlurProsedur;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Alur bergambar halaman Standar Layanan (langkah 86).
 *
 * Yang diuji tiga aturan yang menentukan apa yang dilihat pengunjung:
 * gambar tayang berurutan sesuai kolom `urutan`, baris nonaktif tidak ikut
 * tayang, dan teks prosedurnya tetap ada di bawah gambar.
 */
class AlurProsedurTest extends TestCase
{
    use DatabaseTransactions;

    private string $tanda;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tanda = Str::random(8);
    }

    private function buat(array $isi): AlurProsedur
    {
        return AlurProsedur::forceCreate(array_merge([
            'halaman' => 'prosedur-permohonan',
            'judul' => 'Gambar Uji '.$this->tanda,
            'gambar' => 'uploads/alur-prosedur/uji/'.Str::random(10).'.png',
            'urutan' => 0,
            'is_active' => true,
        ], $isi));
    }

    public function test_gambar_tayang_berurutan(): void
    {
        // Sengaja dibuat terbalik: kalau urutannya diabaikan, yang tampil
        // lebih dulu adalah baris yang dibuat lebih dulu.
        $kedua = $this->buat(['judul' => 'Uji Langkah Kedua '.$this->tanda, 'urutan' => 90]);
        $pertama = $this->buat(['judul' => 'Uji Langkah Pertama '.$this->tanda, 'urutan' => 80]);

        $isi = $this->get('/standar-layanan/prosedur-permohonan')
            ->assertOk()
            ->getContent();

        $posisiPertama = strpos($isi, $pertama->judul);
        $posisiKedua = strpos($isi, $kedua->judul);

        $this->assertNotFalse($posisiPertama, 'Gambar berurutan 80 tidak tayang.');
        $this->assertNotFalse($posisiKedua, 'Gambar berurutan 90 tidak tayang.');
        $this->assertLessThan($posisiKedua, $posisiPertama, 'Gambar tidak tayang sesuai kolom Urutan.');
    }

    public function test_gambar_nonaktif_dan_tanpa_berkas_tidak_tayang(): void
    {
        $nonaktif = $this->buat(['judul' => 'Uji Nonaktif '.$this->tanda, 'urutan' => 91, 'is_active' => false]);
        $tanpaBerkas = $this->buat(['judul' => 'Uji Tanpa Berkas '.$this->tanda, 'urutan' => 92, 'gambar' => null]);

        $this->get('/standar-layanan/prosedur-permohonan')
            ->assertOk()
            ->assertDontSee($nonaktif->judul)
            ->assertDontSee($tanpaBerkas->judul);
    }

    public function test_gambar_menggantikan_ringkasan_dan_rincian_tahapan(): void
    {
        $this->buat(['judul' => 'Uji Pengganti Teks '.$this->tanda, 'urutan' => 93]);

        // Judul bagian tidak dipakai sebagai penanda: `$judulDua()` memecahnya
        // menjadi beberapa elemen, jadi teksnya tidak utuh di HTML. Yang dicek
        // isi kedua bagian itu sendiri — satu kalimat kartu tahapan dan satu
        // kalimat rincian tahapan.
        $this->get('/standar-layanan/prosedur-permohonan')
            ->assertOk()
            ->assertSee('Uji Pengganti Teks '.$this->tanda)
            ->assertDontSee('Isi formulir permohonan informasi secara daring.', false)
            ->assertDontSee('Pemohon mengisi Formulir Permohonan Informasi secara lengkap', false)
            // Judul "Detail …" dan intronya juga dilewati; hero di atas halaman
            // sudah menyebut nama halamannya.
            ->assertDontSee('Alur lengkap dari membuat akun sampai permohonan Anda diproses.', false)
            // Tombol ajakannya bukan bagian tahapan, jadi tetap tayang.
            ->assertSee('Mulai Ajukan Permohonan');
    }

    public function test_prosedur_keberatan_juga_tayang_sebagai_alur_bergambar(): void
    {
        $gambar = $this->buat([
            'halaman' => 'prosedur-keberatan',
            'judul' => 'Uji Keberatan Bergambar '.$this->tanda,
            'urutan' => 95,
        ]);

        $this->get('/standar-layanan/prosedur-keberatan')
            ->assertOk()
            ->assertSee($gambar->judul)
            // Aturan yang sama seperti Prosedur Permohonan: begitu gambarnya
            // ada, kartu tahapan, rincian, judul "Detail …", dan intronya tidak
            // ikut tayang — hanya tombol ajakannya yang bertahan.
            ->assertDontSee('Pilih alasan keberatan, tulis kasus posisi, lampirkan dokumen pendukung.', false)
            ->assertDontSee('Keberatan diajukan paling lambat 30 hari kerja', false)
            ->assertSee('Ajukan Keberatan');
    }

    public function test_halaman_tanpa_gambar_tetap_memakai_teks_tahapannya(): void
    {
        // Menghapus teks tahapan dari kode akan mengosongkan halaman prosedur
        // yang gambarnya belum diunggah — atau yang gambarnya dinonaktifkan
        // petugas, seperti yang ditiru di sini.
        AlurProsedur::where('halaman', 'prosedur-keberatan')->update(['is_active' => false]);

        $this->get('/standar-layanan/prosedur-keberatan')
            ->assertOk()
            ->assertSee('Isi Formulir Keberatan')
            ->assertSee('Keberatan diajukan paling lambat 30 hari kerja', false);
    }

    public function test_halaman_lain_tidak_ikut_menayangkan_gambarnya(): void
    {
        $milikPermohonan = $this->buat(['judul' => 'Uji Milik Permohonan '.$this->tanda, 'urutan' => 94]);

        $this->get('/standar-layanan/prosedur-keberatan')
            ->assertOk()
            ->assertDontSee($milikPermohonan->judul);
    }
}
