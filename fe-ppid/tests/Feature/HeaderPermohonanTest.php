<?php

namespace Tests\Feature;

use App\Models\Pemohon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Tombol CTA "Permohonan" pada header (langkah 74).
 *
 * Tautannya mengalihkan ke `/akun/permohonan/baru`, yang sudah jadi modul
 * tersendiri di Portal Pemohon — jadi tombolnya hanya berguna bagi pengunjung
 * yang belum masuk.
 */
class HeaderPermohonanTest extends TestCase
{
    use DatabaseTransactions;

    /** Penanda kelas yang hanya dipakai tombol CTA header, bukan tautan lain. */
    private const CTA_DESKTOP = 'hidden sm:inline-flex items-center gap-1.5 fs-gradient-accent';

    private const CTA_MOBILE = 'block w-full text-center py-3 fs-gradient-accent';

    private function pemohon(): Pemohon
    {
        return Pemohon::create([
            'nama' => 'Uji Header',
            'email' => 'uji-header-74@example.test',
            'no_hp' => '08000000074',
            'jenis_pemohon' => 'perorangan',
            'status_verifikasi' => 'terverifikasi',
            'email_verified_at' => now(),
            'password' => 'RahasiaUji12345',
        ]);
    }

    public function test_tamu_masih_melihat_tombol_permohonan(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee(self::CTA_DESKTOP, false)
            ->assertSee(self::CTA_MOBILE, false);
    }

    public function test_pemohon_yang_sudah_masuk_tidak_melihat_tombol_permohonan(): void
    {
        $this->actingAs($this->pemohon(), 'pemohon')
            ->get('/')
            ->assertOk()
            ->assertDontSee(self::CTA_DESKTOP, false)
            ->assertDontSee(self::CTA_MOBILE, false);
    }

    /**
     * Yang hilang hanya tombolnya. Menu Layanan tetap memuat tautan
     * Permohonan Informasi Publik — itu navigasi, bukan pintu kedua.
     */
    public function test_menu_layanan_tetap_memuat_tautan_permohonan(): void
    {
        $this->actingAs($this->pemohon(), 'pemohon')
            ->get('/')
            ->assertOk()
            ->assertSee(route('ppid.request'), false);
    }
}
