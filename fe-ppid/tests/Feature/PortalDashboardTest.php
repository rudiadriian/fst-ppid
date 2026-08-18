<?php

namespace Tests\Feature;

use App\Models\Pemohon;
use App\Models\PermohonanInformasi;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Dashboard Portal Pemohon: ringkasan dua status & grafik perbandingan tahun
 * (langkah 71).
 */
class PortalDashboardTest extends TestCase
{
    use DatabaseTransactions;

    private Pemohon $pemohon;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pemohon = Pemohon::create([
            'nama' => 'Uji Dashboard',
            'email' => 'uji-dashboard-71@example.test',
            'no_hp' => '08000000073',
            'jenis_pemohon' => 'perorangan',
            'status_verifikasi' => 'terverifikasi',
            'email_verified_at' => now(),
            'password' => 'RahasiaUji12345',
        ]);
    }

    private function permohonan(string $status, Carbon $tanggal): PermohonanInformasi
    {
        $baris = PermohonanInformasi::create([
            'pemohon_id' => $this->pemohon->id,
            'rincian_informasi' => 'Uji dashboard',
            'status' => 'diajukan',
        ]);

        DB::table('permohonan_informasi')->where('id', $baris->id)->update([
            'status' => $status,
            'tanggal_permohonan' => $tanggal,
        ]);

        return $baris->fresh();
    }

    public function test_ringkasan_hanya_dua_kelompok_status(): void
    {
        $this->permohonan('menunggu_approval', Carbon::now());
        $this->permohonan('ditolak', Carbon::now());

        $html = $this->actingAs($this->pemohon, 'pemohon')
            ->get(route('akun.dashboard'))
            ->assertOk()
            ->getContent();

        $ringkasan = $this->potongRingkasan($html);

        $this->assertStringContainsString('Dalam Proses', $ringkasan);
        $this->assertStringContainsString('Selesai', $ringkasan);
        $this->assertStringNotContainsString('Revisi', $ringkasan);
        $this->assertStringNotContainsString('Menunggu Persetujuan', $ringkasan);
        $this->assertStringNotContainsString('Tolak<', $ringkasan);
    }

    public function test_grafik_menampilkan_dua_belas_bulan_dan_seri_per_tahun(): void
    {
        $tahunIni = (int) Carbon::now()->format('Y');

        $this->permohonan('diajukan', Carbon::create($tahunIni, 3, 10));
        $this->permohonan('selesai', Carbon::create($tahunIni - 1, 3, 10));
        $this->permohonan('selesai', Carbon::create($tahunIni - 2, 7, 1));

        $html = $this->actingAs($this->pemohon, 'pemohon')
            ->get(route('akun.dashboard'))
            ->assertOk()
            ->getContent();

        // Legend memuat tahun berjalan dan dua tahun yang ada datanya.
        foreach ([$tahunIni, $tahunIni - 1, $tahunIni - 2] as $th) {
            $this->assertStringContainsString((string) $th, $html);
        }

        // Tahun tanpa data di luar rentang tidak ikut.
        $this->assertStringNotContainsString((string) ($tahunIni - 4), $html);

        // Dua belas label bulan, satu kali di sumbu X.
        foreach ([3, 7, 12] as $bulan) {
            $label = Carbon::create(null, $bulan, 1)->translatedFormat('M');
            $this->assertStringContainsString($label, $html);
        }

        // Batang bulan Maret tahun ini dan tahun lalu masing-masing berisi 1.
        $this->assertStringContainsString(
            Carbon::create(null, 3, 1)->translatedFormat('M').' '.$tahunIni.': 1',
            $html
        );
        $this->assertStringContainsString(
            Carbon::create(null, 3, 1)->translatedFormat('M').' '.($tahunIni - 1).': 1',
            $html
        );
    }

    public function test_maksimal_empat_tahun_yang_dibandingkan(): void
    {
        $tahunIni = (int) Carbon::now()->format('Y');

        // Termasuk satu yang lebih tua dari batas tiga tahun ke belakang.
        foreach ([0, 1, 2, 3, 5] as $mundur) {
            $this->permohonan('selesai', Carbon::create($tahunIni - $mundur, 5, 5));
        }

        $html = $this->actingAs($this->pemohon, 'pemohon')
            ->get(route('akun.dashboard'))
            ->assertOk()
            ->getContent();

        $this->assertStringNotContainsString((string) ($tahunIni - 5), $html);
        $this->assertStringContainsString((string) ($tahunIni - 3), $html);
    }

    public function test_tahun_berjalan_tetap_tampil_walau_kosong(): void
    {
        $html = $this->actingAs($this->pemohon, 'pemohon')
            ->get(route('akun.dashboard'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString((string) Carbon::now()->format('Y'), $html);
        $this->assertStringContainsString('Belum ada pengajuan pada rentang tahun yang ditampilkan.', $html);
    }

    /**
     * Potongan HTML blok ringkasan status.
     *
     * Judulnya dirender lewat helper `judulDua` yang membungkus kata terakhir
     * dalam <span>, jadi kalimat utuhnya tidak pernah muncul di HTML — yang
     * dicari cukup kata pertamanya.
     */
    private function potongRingkasan(string $html): string
    {
        $mulai = strpos($html, 'Statistik');
        $this->assertNotFalse($mulai, 'Blok ringkasan status tidak ditemukan.');

        return substr($html, $mulai, 1600);
    }
}
