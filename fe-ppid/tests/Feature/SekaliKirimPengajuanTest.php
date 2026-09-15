<?php

namespace Tests\Feature;

use App\Models\KeberatanInformasi;
use App\Models\Pemohon;
use App\Models\PermohonanInformasi;
use App\Support\SekaliKirim;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Satu klik, satu berkas — penjagaan kiriman ganda pada formulir Permohonan
 * Informasi dan Keberatan (UAT poin 15).
 *
 * Yang diuji adalah sisi server. Tombol yang terkunci di layar hanya membuat
 * penjagaannya terlihat; kiriman kedua tetap bisa lahir dari tombol Muat Ulang
 * setelah POST atau dari peramban yang mengulang permintaan, dan di situlah
 * token ini yang menahan.
 */
class SekaliKirimPengajuanTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        // Klaim token disimpan di cache; sisa klaim dari berkas uji lain tidak
        // boleh terbawa.
        Cache::flush();
    }

    private function pemohon(): Pemohon
    {
        return Pemohon::forceCreate([
            'nama' => 'Pemohon Uji Sekali Kirim',
            'email' => 'uji-sekali-'.Str::lower(Str::random(8)).'@contoh.test',
            'no_hp' => '08000000098',
            'nik' => '3175010101900098',
            'pekerjaan' => 'Karyawan',
            'alamat' => 'Jalan Uji Nomor 98',
            'jenis_pemohon' => 'perorangan',
            'status_verifikasi' => 'terverifikasi',
            'tanggal_verifikasi' => now(),
            'email_verified_at' => now(),
            'password' => 'RahasiaUji12345',
            'file_ktp' => 'uploads/ktp/uji-98.png',
        ]);
    }

    private function isian(array $tambahan = []): array
    {
        return array_merge([
            'rincian_informasi' => 'Salinan laporan tahunan tahun buku terakhir',
            'tujuan_penggunaan' => 'Bahan penelitian akademik',
            'cara_memperoleh' => 'membaca',
            'format_informasi' => 'softcopy',
            'cara_pengiriman' => 'email',
            'pernyataan_benar' => '1',
        ], $tambahan);
    }

    /** Permohonan yang sudah bisa dikeberatani. */
    private function permohonanDitolak(Pemohon $pemohon): PermohonanInformasi
    {
        $this->actingAs($pemohon, 'pemohon')
            ->post(route('akun.permohonan.store'), $this->isian())
            ->assertSessionHasNoErrors();

        $permohonan = PermohonanInformasi::where('pemohon_id', $pemohon->id)->firstOrFail();
        $permohonan->forceFill(['status' => 'ditolak', 'tanggal_tanggapan' => now()])->save();

        return $permohonan;
    }

    public function test_formulir_permohonan_membawa_token_dan_tombol_terkunci(): void
    {
        $pemohon = $this->pemohon();

        $halaman = $this->actingAs($pemohon, 'pemohon')
            ->get(route('akun.permohonan.create'))
            ->assertOk();

        $halaman->assertSee('data-sekali-kirim', false);
        $halaman->assertSee('name="'.SekaliKirim::FIELD.'"', false);
        $halaman->assertSee('data-label-sibuk', false);
    }

    public function test_formulir_keberatan_membawa_token_dan_tombol_terkunci(): void
    {
        $pemohon = $this->pemohon();
        $this->permohonanDitolak($pemohon);

        $halaman = $this->actingAs($pemohon, 'pemohon')
            ->get(route('akun.keberatan.create'))
            ->assertOk();

        $halaman->assertSee('data-sekali-kirim', false);
        $halaman->assertSee('name="'.SekaliKirim::FIELD.'"', false);
        $halaman->assertSee('data-label-sibuk', false);
    }

    /** Tiap pembukaan formulir membawa token sendiri; kalau kembar, dua pengajuan yang sah bisa saling menahan. */
    public function test_token_berganti_tiap_formulir_dibuka(): void
    {
        $pemohon = $this->pemohon();

        $ambil = fn () => $this->actingAs($pemohon, 'pemohon')
            ->get(route('akun.permohonan.create'))
            ->getContent();

        preg_match('/name="'.SekaliKirim::FIELD.'" value="([^"]+)"/', $ambil(), $satu);
        preg_match('/name="'.SekaliKirim::FIELD.'" value="([^"]+)"/', $ambil(), $dua);

        $this->assertNotEmpty($satu[1] ?? '');
        $this->assertNotSame($satu[1], $dua[1] ?? '');
    }

    public function test_permohonan_dengan_token_sama_hanya_tersimpan_sekali(): void
    {
        $pemohon = $this->pemohon();
        $isian = $this->isian([SekaliKirim::FIELD => Str::random(40)]);

        $this->actingAs($pemohon, 'pemohon')
            ->post(route('akun.permohonan.store'), $isian)
            ->assertRedirect(route('akun.permohonan.index'));

        $kode = PermohonanInformasi::where('pemohon_id', $pemohon->id)->value('kode_permohonan');

        // Klik kedua: berkasnya tidak lahir lagi, dan pemohon diberi tahu nomor
        // yang sudah terbit — bukan pesan galat.
        $kedua = $this->actingAs($pemohon, 'pemohon')
            ->post(route('akun.permohonan.store'), $isian)
            ->assertRedirect(route('akun.permohonan.index'));

        $this->assertSame(1, PermohonanInformasi::where('pemohon_id', $pemohon->id)->count());
        $kedua->assertSessionHas('status', fn ($pesan) => str_contains($pesan, $kode));
    }

    public function test_keberatan_dengan_token_sama_hanya_tersimpan_sekali(): void
    {
        $pemohon = $this->pemohon();
        $permohonan = $this->permohonanDitolak($pemohon);

        $isian = [
            'permohonan_id' => $permohonan->id,
            'jenis_keberatan' => 'permohonan_ditolak',
            'kasus_posisi' => 'Permohonan ditolak tanpa alasan yang jelas.',
            SekaliKirim::FIELD => Str::random(40),
        ];

        foreach ([1, 2] as $ke) {
            $this->actingAs($pemohon, 'pemohon')
                ->post(route('akun.keberatan.store'), $isian)
                ->assertRedirect(route('akun.keberatan.index'));
        }

        $this->assertSame(1, KeberatanInformasi::where('pemohon_id', $pemohon->id)->count());
    }

    /** Token berbeda = pengajuan berbeda: pemohon tetap boleh mengirim lebih dari satu permohonan. */
    public function test_token_berbeda_tetap_tersimpan_sendiri_sendiri(): void
    {
        $pemohon = $this->pemohon();

        foreach ([1, 2] as $ke) {
            $this->actingAs($pemohon, 'pemohon')
                ->post(route('akun.permohonan.store'), $this->isian([SekaliKirim::FIELD => Str::random(40)]))
                ->assertSessionHasNoErrors();
        }

        $this->assertSame(2, PermohonanInformasi::where('pemohon_id', $pemohon->id)->count());
    }

    /** Isian yang ditolak validasi tidak membakar tokennya — formulir yang dibetulkan tetap bisa dikirim. */
    public function test_kiriman_gagal_validasi_tidak_menghanguskan_token(): void
    {
        $pemohon = $this->pemohon();
        $token = Str::random(40);

        $this->actingAs($pemohon, 'pemohon')
            ->post(route('akun.permohonan.store'), $this->isian([
                'rincian_informasi' => '',
                SekaliKirim::FIELD => $token,
            ]))
            ->assertSessionHasErrors('rincian_informasi');

        $this->actingAs($pemohon, 'pemohon')
            ->post(route('akun.permohonan.store'), $this->isian([SekaliKirim::FIELD => $token]))
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('akun.permohonan.index'));

        $this->assertSame(1, PermohonanInformasi::where('pemohon_id', $pemohon->id)->count());
    }

    /** Kiriman tanpa token tetap dilayani: penjagaan ini menahan klik ganda, bukan menggantikan pembatas laju. */
    public function test_kiriman_tanpa_token_tetap_dilayani(): void
    {
        $pemohon = $this->pemohon();

        $this->actingAs($pemohon, 'pemohon')
            ->post(route('akun.permohonan.store'), $this->isian())
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('akun.permohonan.index'));

        $this->assertSame(1, PermohonanInformasi::where('pemohon_id', $pemohon->id)->count());
    }
}
