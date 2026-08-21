<?php

namespace Tests\Unit;

use App\Support\KunciBertingkat;
use Tests\TestCase;

/**
 * Tangga kunci bertingkat, diuji tanpa basis data.
 *
 * Aturannya dititipkan pada satu kelas justru supaya bisa diuji seperti ini:
 * kalau angkanya digeser suatu saat, berkas inilah yang lebih dulu berteriak.
 */
class KunciBertingkatTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'ppid.akun.gagal_per_tahap' => 3,
            'ppid.akun.tahap_kunci_menit' => [60, 1440, 20160],
        ]);
    }

    public function test_percobaan_di_bawah_kelipatan_tiga_tidak_mengunci(): void
    {
        foreach ([1, 2, 4, 5, 7, 8, 10, 11] as $jumlah) {
            $akibat = KunciBertingkat::akibat($jumlah);

            $this->assertNull($akibat['menit'], "Percobaan ke-{$jumlah} seharusnya belum mengunci");
            $this->assertFalse($akibat['suspend']);
        }
    }

    public function test_tiga_kegagalan_pertama_mengunci_satu_jam(): void
    {
        $akibat = KunciBertingkat::akibat(3);

        $this->assertSame(1, $akibat['tahap']);
        $this->assertSame(60, $akibat['menit']);
        $this->assertFalse($akibat['suspend']);
    }

    public function test_tiga_kegagalan_kedua_mengunci_satu_hari(): void
    {
        $akibat = KunciBertingkat::akibat(6);

        $this->assertSame(2, $akibat['tahap']);
        $this->assertSame(1440, $akibat['menit']);
        $this->assertFalse($akibat['suspend']);
    }

    public function test_tiga_kegagalan_ketiga_mengunci_empat_belas_hari(): void
    {
        $akibat = KunciBertingkat::akibat(9);

        $this->assertSame(3, $akibat['tahap']);
        $this->assertSame(20160, $akibat['menit']);
        $this->assertSame('14 hari', KunciBertingkat::durasiTerbaca($akibat['menit']));
        $this->assertFalse($akibat['suspend']);
    }

    public function test_tiga_kegagalan_keempat_menyuspend_akun(): void
    {
        $akibat = KunciBertingkat::akibat(12);

        $this->assertSame(KunciBertingkat::TAHAP_SUSPEND, $akibat['tahap']);
        $this->assertTrue($akibat['suspend']);
        // Suspend tidak punya masa tunggu — hanya administrator yang membukanya.
        $this->assertNull($akibat['menit']);
    }

    public function test_setelah_suspend_tetap_suspend(): void
    {
        foreach ([15, 18, 30, 300] as $jumlah) {
            $this->assertTrue(
                KunciBertingkat::akibat($jumlah)['suspend'],
                "Percobaan ke-{$jumlah} seharusnya tetap berujung suspend"
            );
        }
    }

    public function test_durasi_dicetak_dalam_satuan_yang_wajar(): void
    {
        $this->assertSame('1 jam', KunciBertingkat::durasiTerbaca(60));
        $this->assertSame('1 hari', KunciBertingkat::durasiTerbaca(1440));
        $this->assertSame('14 hari', KunciBertingkat::durasiTerbaca(20160));
        $this->assertSame('45 menit', KunciBertingkat::durasiTerbaca(45));
    }

    public function test_sisa_waktu_dibaca_manusia(): void
    {
        $this->assertSame('kurang dari satu menit lagi', KunciBertingkat::sisaTerbaca(30));
        $this->assertSame('5 menit lagi', KunciBertingkat::sisaTerbaca(5 * 60));
        $this->assertSame('2 jam 30 menit lagi', KunciBertingkat::sisaTerbaca(2 * 3600 + 30 * 60));
        // Lewat sehari, menitnya tidak lagi menolong siapa pun.
        $this->assertSame('13 hari 23 jam lagi', KunciBertingkat::sisaTerbaca(13 * 86400 + 23 * 3600 + 40 * 60));
    }
}
