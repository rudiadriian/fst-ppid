<?php

namespace Tests\Feature;

use App\Models\NotifikasiPemohon;
use App\Models\Pemohon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Lonceng notifikasi Portal Pemohon (langkah 72).
 *
 * Isinya ditulis api-ppid saat petugas memberi umpan balik; yang diuji di sini
 * sisi portal: jumlah belum dibaca, penandaan, dan pembatasan antarakun.
 */
class PortalNotifikasiTest extends TestCase
{
    use DatabaseTransactions;

    private Pemohon $pemohon;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pemohon = $this->buatPemohon('uji-lonceng-72@example.test');
    }

    private function buatPemohon(string $email): Pemohon
    {
        return Pemohon::create([
            'nama' => 'Uji Lonceng',
            'email' => $email,
            'no_hp' => '08000000072',
            'jenis_pemohon' => 'perorangan',
            'status_verifikasi' => 'terverifikasi',
            'email_verified_at' => now(),
            'password' => 'RahasiaUji12345',
        ]);
    }

    private function notifikasi(Pemohon $pemohon, bool $dibaca = false, array $data = []): NotifikasiPemohon
    {
        return NotifikasiPemohon::create([
            'pemohon_id' => $pemohon->id,
            'type' => 'permohonan_status',
            'message' => 'Permohonan PPID-001 telah selesai ditangani.',
            'is_read' => $dibaca,
            'data' => $data + [
                'title' => 'Permohonan Informasi — Selesai',
                'link' => '/akun/permohonan/1',
                'variant' => 'primary',
            ],
        ]);
    }

    /**
     * Lonceng hanya memuat yang belum dibaca (langkah 76). Yang sudah dibaca
     * tidak ikut, walaupun barisnya masih ada di basis data.
     */
    public function test_daftar_hanya_memuat_yang_belum_dibaca(): void
    {
        $this->notifikasi($this->pemohon);
        $this->notifikasi($this->pemohon);
        $this->notifikasi($this->pemohon, dibaca: true);

        $this->actingAs($this->pemohon, 'pemohon')
            ->getJson(route('akun.notifikasi.daftar'))
            ->assertOk()
            ->assertJsonPath('belum_dibaca', 2)
            ->assertJsonCount(2, 'daftar')
            ->assertJsonPath('daftar.0.tautan', url('/akun/permohonan/1'));
    }

    /** Sesudah ditandai dibaca, barisnya hilang dari lonceng. */
    public function test_notifikasi_yang_sudah_dibaca_hilang_dari_lonceng(): void
    {
        $baris = $this->notifikasi($this->pemohon);

        $this->actingAs($this->pemohon, 'pemohon')
            ->postJson(route('akun.notifikasi.baca', $baris->id))
            ->assertOk();

        $this->actingAs($this->pemohon, 'pemohon')
            ->getJson(route('akun.notifikasi.daftar'))
            ->assertOk()
            ->assertJsonPath('belum_dibaca', 0)
            ->assertJsonCount(0, 'daftar');
    }

    /** Riwayatnya tetap ada di halaman penuh, bukan terhapus. */
    public function test_halaman_penuh_tetap_memuat_yang_sudah_dibaca(): void
    {
        $this->notifikasi($this->pemohon, dibaca: true);

        $this->actingAs($this->pemohon, 'pemohon')
            ->get(route('akun.notifikasi'))
            ->assertOk()
            ->assertSee('Permohonan PPID-001 telah selesai ditangani.', false);
    }

    public function test_notifikasi_akun_lain_tidak_ikut_terbaca(): void
    {
        $lain = $this->buatPemohon('uji-lonceng-72-lain@example.test');
        $this->notifikasi($lain);

        $this->actingAs($this->pemohon, 'pemohon')
            ->getJson(route('akun.notifikasi.daftar'))
            ->assertOk()
            ->assertJsonPath('belum_dibaca', 0)
            ->assertJsonCount(0, 'daftar');
    }

    public function test_menandai_satu_notifikasi_dibaca(): void
    {
        $baris = $this->notifikasi($this->pemohon);

        $this->actingAs($this->pemohon, 'pemohon')
            ->postJson(route('akun.notifikasi.baca', $baris->id))
            ->assertOk();

        $this->assertTrue($baris->fresh()->is_read);
    }

    /** Id milik akun lain tidak boleh ikut tertandai lewat endpoint ini. */
    public function test_tidak_bisa_menandai_notifikasi_akun_lain(): void
    {
        $lain = $this->buatPemohon('uji-lonceng-72-lain2@example.test');
        $baris = $this->notifikasi($lain);

        $this->actingAs($this->pemohon, 'pemohon')
            ->postJson(route('akun.notifikasi.baca', $baris->id))
            ->assertOk();

        $this->assertFalse($baris->fresh()->is_read);
    }

    public function test_tandai_semua_mengosongkan_lencana(): void
    {
        $this->notifikasi($this->pemohon);
        $this->notifikasi($this->pemohon);

        $this->actingAs($this->pemohon, 'pemohon')
            ->postJson(route('akun.notifikasi.tandai-semua'))
            ->assertOk();

        $this->assertSame(0, NotifikasiPemohon::milik($this->pemohon->id)->where('is_read', false)->count());
    }

    /** Membuka dari halaman penuh menandai dibaca lalu mengantar ke tujuannya. */
    public function test_membuka_notifikasi_menandai_dan_mengalihkan(): void
    {
        $baris = $this->notifikasi($this->pemohon);

        $this->actingAs($this->pemohon, 'pemohon')
            ->get(route('akun.notifikasi.buka', $baris->id))
            ->assertRedirect(url('/akun/permohonan/1'));

        $this->assertTrue($baris->fresh()->is_read);
    }

    /**
     * Tautan notifikasi hanya boleh berupa path internal — kalau tidak, baris
     * notifikasi bisa dipakai melempar pemohon ke domain lain.
     */
    public function test_tautan_luar_diabaikan(): void
    {
        $baris = $this->notifikasi($this->pemohon, data: ['link' => 'https://situs-lain.test/phishing']);

        $this->actingAs($this->pemohon, 'pemohon')
            ->get(route('akun.notifikasi.buka', $baris->id))
            ->assertRedirect(route('akun.notifikasi'));
    }

    public function test_halaman_notifikasi_menampilkan_pesan(): void
    {
        $this->notifikasi($this->pemohon);

        $this->actingAs($this->pemohon, 'pemohon')
            ->get(route('akun.notifikasi'))
            ->assertOk()
            ->assertSee('Permohonan PPID-001 telah selesai ditangani.', false);
    }

    public function test_lonceng_tampil_di_header_saat_masuk(): void
    {
        $this->actingAs($this->pemohon, 'pemohon')
            ->get(route('akun.dashboard'))
            ->assertOk()
            ->assertSee(route('akun.notifikasi.daftar'), false);
    }

    public function test_tamu_tidak_bisa_membaca_notifikasi(): void
    {
        $this->getJson(route('akun.notifikasi.daftar'))->assertStatus(401);
    }
}
