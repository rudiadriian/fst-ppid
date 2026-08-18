<?php

namespace Tests\Feature;

use App\Models\KeberatanInformasi;
use App\Models\Pemohon;
use App\Models\PermohonanInformasi;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Tab status daftar Portal Pemohon & pencarian pada Histori (langkah 71).
 *
 * Memakai DatabaseTransactions, bukan RefreshDatabase: skema PPID dibuat lewat
 * migrasi baseline yang berat, dan basis data pengembangan ini berisi data
 * nyata — semua baris uji cukup di-rollback pada akhir tiap tes.
 */
class PortalDaftarTest extends TestCase
{
    use DatabaseTransactions;

    private Pemohon $pemohon;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pemohon = Pemohon::create([
            'nama' => 'Uji Portal',
            'email' => 'uji-portal-71@example.test',
            'no_hp' => '08000000071',
            'jenis_pemohon' => 'perorangan',
            'status_verifikasi' => 'terverifikasi',
            'email_verified_at' => now(),
            'password' => 'RahasiaUji12345',
        ]);
    }

    /** Simpan permohonan lalu paksa statusnya (transisi status dijaga API). */
    private function permohonan(string $status): PermohonanInformasi
    {
        $baris = PermohonanInformasi::create([
            'pemohon_id' => $this->pemohon->id,
            'rincian_informasi' => 'Uji tab '.$status,
            'status' => 'diajukan',
        ]);

        DB::table('permohonan_informasi')->where('id', $baris->id)->update(['status' => $status]);

        return $baris->fresh();
    }

    private function keberatan(int $permohonanId, string $status): KeberatanInformasi
    {
        $baris = KeberatanInformasi::create([
            'permohonan_id' => $permohonanId,
            'pemohon_id' => $this->pemohon->id,
            'jenis_keberatan' => 'informasi_tidak_sesuai',
            'alasan_keberatan' => 'Uji tab '.$status,
            'kasus_posisi' => 'Uji tab '.$status,
            'status' => 'diajukan',
        ]);

        DB::table('keberatan_informasi')->where('id', $baris->id)->update(['status' => $status]);

        return $baris->fresh();
    }

    public function test_tab_permohonan_hanya_semua_dalam_proses_dan_selesai(): void
    {
        $this->permohonan('diajukan');
        $this->permohonan('menunggu_approval');
        $this->permohonan('selesai');
        $this->permohonan('ditolak');

        $html = $this->actingAs($this->pemohon, 'pemohon')
            ->get(route('akun.permohonan.index'))
            ->assertOk()
            ->getContent();

        /*
         * Diperiksa hanya di dalam blok tablist: "Menunggu Persetujuan" dan
         * "Tolak" tetap muncul di halaman sebagai label status tiap baris —
         * yang dihapus adalah tab-nya, bukan statusnya.
         */
        $tablist = $this->potongTablist($html);

        $this->assertStringContainsString('Semua', $tablist);
        $this->assertStringContainsString('Dalam Proses', $tablist);
        $this->assertStringContainsString('Selesai', $tablist);
        $this->assertStringNotContainsString('Revisi', $tablist);
        $this->assertStringNotContainsString('Menunggu Persetujuan', $tablist);
        $this->assertStringNotContainsString('Tolak', $tablist);
    }

    /** Isi elemen ber-`role="tablist"` sampai tab terakhir. */
    private function potongTablist(string $html): string
    {
        $mulai = strpos($html, 'role="tablist"');
        $this->assertNotFalse($mulai, 'Blok tab status tidak ditemukan.');

        $akhir = strpos($html, '</div>', $mulai);

        return substr($html, $mulai, $akhir - $mulai);
    }

    public function test_tab_permohonan_menyaring_status_yang_benar(): void
    {
        $this->permohonan('diajukan');
        $this->permohonan('menunggu_approval');
        $this->permohonan('selesai');
        $this->permohonan('ditolak');

        $jumlah = fn (string $tab) => substr_count(
            $this->actingAs($this->pemohon, 'pemohon')
                ->get(route('akun.permohonan.index', $tab === '' ? [] : ['status' => $tab]))
                ->getContent(),
            'PPID-FSTJ/'
        );

        $this->assertSame(4, $jumlah(''));
        // diajukan + menunggu_approval
        $this->assertSame(2, $jumlah('Dalam Proses'));
        // selesai + ditolak — status yang ditolak ikut dihitung tuntas
        $this->assertSame(2, $jumlah('Selesai'));
    }

    public function test_tab_keberatan_memakai_pengelompokan_yang_sama(): void
    {
        $induk = $this->permohonan('selesai');
        $this->keberatan($induk->id, 'diajukan');
        $this->keberatan($induk->id, 'selesai');
        $this->keberatan($induk->id, 'ditolak');

        $jumlah = fn (string $tab) => substr_count(
            $this->actingAs($this->pemohon, 'pemohon')
                ->get(route('akun.keberatan.index', $tab === '' ? [] : ['status' => $tab]))
                ->getContent(),
            'Uji tab '
        );

        $this->assertSame(3, $jumlah(''));
        $this->assertSame(1, $jumlah('Dalam Proses'));
        $this->assertSame(2, $jumlah('Selesai'));
    }

    public function test_histori_dapat_dicari_lewat_nomor_permohonan(): void
    {
        $satu = $this->permohonan('diajukan');
        $dua = $this->permohonan('selesai');
        $this->keberatan($satu->id, 'diajukan');

        $html = $this->actingAs($this->pemohon, 'pemohon')
            ->get(route('akun.histori', ['cari' => $satu->kode_permohonan]))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString($satu->kode_permohonan, $html);
        $this->assertStringNotContainsString($dua->kode_permohonan, $html);
    }

    public function test_histori_tanpa_hasil_menampilkan_pesan_pencarian(): void
    {
        $this->permohonan('diajukan');

        $html = $this->actingAs($this->pemohon, 'pemohon')
            ->get(route('akun.histori', ['cari' => 'PPID-FSTJ/19000101/9999']))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('Tidak ada permohonan yang cocok dengan pencarian.', $html);
        $this->assertStringContainsString('Tidak ada keberatan yang cocok dengan pencarian.', $html);
    }

    public function test_histori_tanpa_kata_kunci_menampilkan_semuanya(): void
    {
        $this->permohonan('diajukan');
        $this->permohonan('selesai');

        $html = $this->actingAs($this->pemohon, 'pemohon')
            ->get(route('akun.histori'))
            ->assertOk()
            ->getContent();

        $this->assertSame(2, substr_count($html, 'PPID-FSTJ/'));
    }
}
