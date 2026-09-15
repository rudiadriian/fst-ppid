<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Daftar Regulasi tanpa tombol "Lihat" (UAT poin 16).
 *
 * Tombolnya dilepas, tetapi jalan ke dokumennya tidak boleh ikut hilang:
 * seluruh kartu memang sudah menjadi tautan ke halaman rinciannya. Uji ini
 * menjaga keduanya sekaligus — tombolnya tidak kembali, dan tautannya tetap
 * ada.
 */
class RegulasiTombolLihatTest extends TestCase
{
    use DatabaseTransactions;

    private function regulasi(): int
    {
        return (int) DB::table('regulasi')->insertGetId([
            'judul' => 'Regulasi Uji Tombol '.Str::random(6),
            'kategori' => 'regulasi',
            'jenis_peraturan' => 'Peraturan Direksi',
            'tahun' => 2026,
            'created_at' => now(),
        ]);
    }

    public function test_daftar_regulasi_tidak_memasang_tombol_lihat(): void
    {
        $this->regulasi();

        $isi = $this->get('/regulasi')->assertOk()->getContent();

        $this->assertSame(
            0,
            preg_match('/>\s*Lihat\s*</', $isi),
            'Tombol "Lihat" masih terpasang di daftar Regulasi.'
        );
    }

    /** Kartu tetap mengantar ke rincian dokumennya. */
    public function test_kartu_regulasi_tetap_tertaut_ke_rinciannya(): void
    {
        $id = $this->regulasi();

        $this->get('/regulasi')
            ->assertOk()
            ->assertSee(route('ppid.regulation.show', $id), false);
    }
}
