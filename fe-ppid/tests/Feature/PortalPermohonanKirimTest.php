<?php

namespace Tests\Feature;

use App\Models\KeberatanInformasi;
use App\Models\Pemohon;
use App\Models\PermohonanInformasi;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Pengiriman formulir Permohonan Informasi dan Keberatan dari Portal Pengguna.
 *
 * Kedua formulir ini sempat menjawab 500 pada UAT. Yang diuji di sini bukan
 * isian per isian, melainkan bahwa jalur kirimnya benar-benar sampai ke akhir:
 * barisnya tersimpan, dan pemohon dialihkan ke daftarnya.
 */
class PortalPermohonanKirimTest extends TestCase
{
    use DatabaseTransactions;

    private function pemohon(string $statusVerifikasi = 'terverifikasi'): Pemohon
    {
        return Pemohon::forceCreate([
            'nama' => 'Pemohon Uji Kirim',
            'email' => 'uji-kirim-'.Str::lower(Str::random(8)).'@contoh.test',
            'no_hp' => '08000000099',
            'nik' => '3175010101900099',
            'pekerjaan' => 'Karyawan',
            'alamat' => 'Jalan Uji Nomor 99',
            'jenis_pemohon' => 'perorangan',
            'status_verifikasi' => $statusVerifikasi,
            'tanggal_verifikasi' => $statusVerifikasi === 'terverifikasi' ? now() : null,
            'email_verified_at' => now(),
            'password' => 'RahasiaUji12345',
            'file_ktp' => 'uploads/ktp/uji-99.png',
        ]);
    }

    /** Payload persis seperti yang dikirim formulir pada UAT. */
    private function isian(): array
    {
        return [
            'rincian_informasi' => 'Natus nostrum tempor ea velit doloribus commodi eos voluptas quos unde et',
            'tujuan_penggunaan' => 'Aperiam voluptas nisi impedit exercitationem ad dolores',
            'cara_memperoleh' => 'membaca',
            'format_informasi' => 'softcopy',
            'cara_pengiriman' => 'email',
            'pernyataan_benar' => '1',
        ];
    }

    public function test_permohonan_tersimpan_dan_mengalihkan_ke_daftar(): void
    {
        $pemohon = $this->pemohon();

        $this->actingAs($pemohon, 'pemohon')
            ->post(route('akun.permohonan.store'), $this->isian())
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('akun.permohonan.index'));

        $baris = PermohonanInformasi::where('pemohon_id', $pemohon->id)->first();

        $this->assertNotNull($baris, 'Permohonan tidak tersimpan.');
        $this->assertSame('diajukan', $baris->status);
        $this->assertSame('membaca', $baris->cara_memperoleh);
        // Jalur pelayanan diturunkan dari cara pengiriman, tidak ditanyakan.
        $this->assertSame('online', $baris->jalur_pelayanan);
        $this->assertNotEmpty($baris->kode_permohonan);
    }

    /** Dua permohonan berturut-turut tetap mendapat nomor sendiri-sendiri. */
    public function test_dua_permohonan_berturut_turut_dapat_nomor_berbeda(): void
    {
        $pemohon = $this->pemohon();

        foreach ([1, 2] as $ke) {
            $this->actingAs($pemohon, 'pemohon')
                ->post(route('akun.permohonan.store'), $this->isian())
                ->assertSessionHasNoErrors()
                ->assertRedirect(route('akun.permohonan.index'));
        }

        $kode = PermohonanInformasi::where('pemohon_id', $pemohon->id)
            ->pluck('kode_permohonan')
            ->all();

        $this->assertCount(2, $kode);
        $this->assertCount(2, array_unique($kode), 'Nomor permohonan tidak boleh kembar.');
    }

    /** Pemohon yang belum diverifikasi tidak bisa mengirim, tapi juga tidak 500. */
    public function test_pemohon_belum_terverifikasi_dialihkan_ke_data_pemohon(): void
    {
        $pemohon = $this->pemohon('belum');

        $this->actingAs($pemohon, 'pemohon')
            ->post(route('akun.permohonan.store'), $this->isian())
            ->assertRedirect(route('akun.data-pemohon'));

        $this->assertSame(0, PermohonanInformasi::where('pemohon_id', $pemohon->id)->count());
    }

    public function test_keberatan_tersimpan_dan_mengalihkan_ke_daftar(): void
    {
        $pemohon = $this->pemohon();

        // Keberatan berangkat dari permohonan yang sudah ditanggapi.
        $this->actingAs($pemohon, 'pemohon')
            ->post(route('akun.permohonan.store'), $this->isian())
            ->assertSessionHasNoErrors();

        $permohonan = PermohonanInformasi::where('pemohon_id', $pemohon->id)->firstOrFail();
        $permohonan->forceFill([
            'status' => 'ditolak',
            'tanggal_tanggapan' => now(),
        ])->save();

        $this->actingAs($pemohon, 'pemohon')
            ->post(route('akun.keberatan.store'), [
                'permohonan_id' => $permohonan->id,
                'jenis_keberatan' => 'permohonan_ditolak',
                'kasus_posisi' => 'Permohonan ditolak tanpa alasan yang jelas.',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('akun.keberatan.index'));

        $this->assertSame(1, KeberatanInformasi::where('pemohon_id', $pemohon->id)->count());
    }
}
