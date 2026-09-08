<?php

namespace Tests\Feature;

use App\Models\AlurProsedur;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Alur bergambar pada halaman Standar Layanan.
 *
 * Gambarnya diunggah petugas lewat modul Alur Prosedur di be-ppid. Halaman ini
 * yang menayangkannya; bila barisnya ada tetapi gambarnya tidak muncul,
 * kerusakannya tidak menggagalkan permintaan apa pun — halamannya tetap 200,
 * hanya isinya yang hilang.
 */
class AlurProsedurHalamanTest extends TestCase
{
    use DatabaseTransactions;

    private function alur(string $halaman, array $ubah = []): AlurProsedur
    {
        return AlurProsedur::forceCreate(array_merge([
            'halaman' => $halaman,
            'judul' => 'Tahap Uji '.Str::random(6),
            'keterangan' => 'Keterangan uji.',
            'gambar' => 'uploads/alur-prosedur/uji-'.Str::lower(Str::random(8)).'.png',
            'urutan' => 1,
            'is_active' => true,
        ], $ubah));
    }

    public function test_halaman_prosedur_menayangkan_gambar_yang_diunggah(): void
    {
        $baris = $this->alur('prosedur-permohonan');

        $html = $this->get(route('ppid.service', 'prosedur-permohonan'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString($baris->gambar, $html);
        $this->assertStringContainsString($baris->judul, $html);
    }

    public function test_halaman_keberatan_memakai_gambarnya_sendiri(): void
    {
        $permohonan = $this->alur('prosedur-permohonan');
        $keberatan = $this->alur('prosedur-keberatan');

        $html = $this->get(route('ppid.service', 'prosedur-keberatan'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString($keberatan->gambar, $html);
        $this->assertStringNotContainsString($permohonan->gambar, $html);
    }

    /** Baris nonaktif tidak ikut tayang. */
    public function test_baris_nonaktif_tidak_tayang(): void
    {
        $baris = $this->alur('prosedur-permohonan', ['is_active' => false]);

        $html = $this->get(route('ppid.service', 'prosedur-permohonan'))
            ->assertOk()
            ->getContent();

        $this->assertStringNotContainsString($baris->gambar, $html);
    }
}
