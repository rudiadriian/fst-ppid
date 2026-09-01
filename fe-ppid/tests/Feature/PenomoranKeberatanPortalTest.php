<?php

namespace Tests\Feature;

use App\Models\KeberatanInformasi;
use App\Models\Pemohon;
use App\Models\PermohonanInformasi;
use App\Support\SlaLayanan;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Nomor registrasi keberatan dan tujuh dasar Pasal 35 di portal (langkah 89).
 *
 * DatabaseTransactions, bukan RefreshDatabase, dengan alasan yang sama seperti
 * {@see PortalDaftarTest}: skema PPID lahir dari migrasi baseline yang berat dan
 * basis data pengembangan ini berisi data nyata.
 */
class PenomoranKeberatanPortalTest extends TestCase
{
    use DatabaseTransactions;

    private Pemohon $pemohon;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pemohon = Pemohon::create([
            'nama' => 'Uji Penomoran',
            'email' => 'uji-penomoran-89@example.test',
            'no_hp' => '08000000089',
            'jenis_pemohon' => 'perorangan',
            'status_verifikasi' => 'terverifikasi',
            'email_verified_at' => now(),
            'password' => 'RahasiaUji12345',
        ]);
    }

    private function permohonanSelesai(): PermohonanInformasi
    {
        $baris = PermohonanInformasi::create([
            'pemohon_id' => $this->pemohon->id,
            'rincian_informasi' => 'Uji penomoran keberatan',
            'status' => 'diajukan',
        ]);

        DB::table('permohonan_informasi')->where('id', $baris->id)->update(['status' => 'selesai']);

        return $baris->fresh();
    }

    public function test_keberatan_portal_lahir_bernomor_sendiri(): void
    {
        $permohonan = $this->permohonanSelesai();

        $jawab = $this->actingAs($this->pemohon, 'pemohon')->post(route('akun.keberatan.store'), [
            'permohonan_id' => $permohonan->id,
            'jenis_keberatan' => 'permintaan_tidak_dipenuhi',
            'kasus_posisi' => 'Informasi diberikan sebagian saja.',
        ]);

        $jawab->assertRedirect(route('akun.keberatan.index'));

        $keberatan = KeberatanInformasi::where('permohonan_id', $permohonan->id)->firstOrFail();

        $this->assertNotNull($keberatan->kode_keberatan, 'Keberatan portal tersimpan tanpa nomor registrasi.');
        $this->assertStringStartsWith('KBT-FSTJ/', $keberatan->kode_keberatan);
        $this->assertNotSame($permohonan->kode_permohonan, $keberatan->kode_keberatan);

        // Nomornya ikut ke tanda terima: pemohon tidak punya cara lain
        // mengetahuinya sebelum membuka daftar keberatan.
        $jawab->assertSessionHas('status', fn ($pesan) => str_contains((string) $pesan, $keberatan->kode_keberatan));
    }

    public function test_alasan_ketujuh_diterima_formulir(): void
    {
        $this->assertArrayHasKey('permintaan_tidak_dipenuhi', KeberatanInformasi::JENIS);
        $this->assertCount(7, KeberatanInformasi::JENIS);

        $permohonan = $this->permohonanSelesai();

        $this->actingAs($this->pemohon, 'pemohon')
            ->get(route('akun.keberatan.create'))
            ->assertOk()
            ->assertSee('permintaan_tidak_dipenuhi', false);

        $this->actingAs($this->pemohon, 'pemohon')
            ->post(route('akun.keberatan.store'), [
                'permohonan_id' => $permohonan->id,
                'jenis_keberatan' => 'alasan_yang_tidak_ada',
                'kasus_posisi' => 'Alasan di luar daftar resmi.',
            ])
            ->assertSessionHasErrors('jenis_keberatan');
    }

    public function test_tenggat_keberatan_dihitung_hari_kalender(): void
    {
        $permohonan = $this->permohonanSelesai();

        $this->actingAs($this->pemohon, 'pemohon')->post(route('akun.keberatan.store'), [
            'permohonan_id' => $permohonan->id,
            'jenis_keberatan' => 'biaya_tidak_wajar',
            'kasus_posisi' => 'Biaya penggandaan tidak wajar.',
        ]);

        $keberatan = KeberatanInformasi::where('permohonan_id', $permohonan->id)->firstOrFail();

        // 30 hari kalender, bukan 30 hari kerja: yang kedua akan memberi
        // petugas sekitar dua minggu lebih longgar daripada yang dijanjikan.
        $this->assertSame(
            SlaLayanan::batasKeberatan($keberatan->tanggal_keberatan)->toDateString(),
            $keberatan->batas_waktu_tanggapan->toDateString()
        );
    }

    public function test_nomor_keberatan_tampil_di_daftar(): void
    {
        $permohonan = $this->permohonanSelesai();

        $keberatan = KeberatanInformasi::create([
            'permohonan_id' => $permohonan->id,
            'pemohon_id' => $this->pemohon->id,
            'jenis_keberatan' => 'permohonan_ditolak',
            'alasan_keberatan' => 'Uji tampil nomor',
            'kasus_posisi' => 'Uji tampil nomor',
            'status' => 'diajukan',
            'tanggal_keberatan' => now(),
        ])->fresh();

        $this->actingAs($this->pemohon, 'pemohon')
            ->get(route('akun.keberatan.index'))
            ->assertOk()
            ->assertSee($keberatan->kode_keberatan)
            ->assertSee($permohonan->kode_permohonan);
    }
}
