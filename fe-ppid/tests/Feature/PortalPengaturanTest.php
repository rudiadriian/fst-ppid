<?php

namespace Tests\Feature;

use App\Models\Pemohon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Profil hanya-baca & penguncian Data Pemohon setelah terverifikasi
 * (langkah 71).
 */
class PortalPengaturanTest extends TestCase
{
    use DatabaseTransactions;

    private function pemohon(string $statusVerifikasi = 'belum'): Pemohon
    {
        return Pemohon::create([
            'nama' => 'Uji Pengaturan',
            'email' => 'uji-pengaturan-71@example.test',
            'no_hp' => '08000000072',
            'nik' => '3175010101900001',
            'pekerjaan' => 'Karyawan',
            'alamat' => 'Jalan Uji Nomor 1',
            'jenis_pemohon' => 'perorangan',
            'status_verifikasi' => $statusVerifikasi,
            'tanggal_verifikasi' => $statusVerifikasi === 'terverifikasi' ? now() : null,
            'email_verified_at' => now(),
            'password' => 'RahasiaUji12345',
            'file_ktp' => 'uploads/ktp/uji-71.png',
        ]);
    }

    public function test_profil_menampilkan_seluruh_data_dalam_tab(): void
    {
        $pemohon = $this->pemohon('terverifikasi');

        $html = $this->actingAs($pemohon, 'pemohon')
            ->get(route('akun.profil'))
            ->assertOk()
            ->getContent();

        foreach (['Akun', 'Data Diri', 'Verifikasi &amp; Berkas', 'Aktivitas'] as $tab) {
            $this->assertStringContainsString($tab, $html);
        }

        // Data dari seluruh tab ikut dirender, bukan hanya tab pertama.
        $this->assertStringContainsString('3175010101900001', $html);
        $this->assertStringContainsString('Karyawan', $html);
        $this->assertStringContainsString('Jalan Uji Nomor 1', $html);
        $this->assertStringContainsString('08000000072', $html);
    }

    public function test_profil_hanya_punya_isian_foto(): void
    {
        $pemohon = $this->pemohon();

        $html = $this->actingAs($pemohon, 'pemohon')
            ->get(route('akun.profil'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('name="foto"', $html);
        $this->assertStringNotContainsString('name="nama"', $html);
        $this->assertStringNotContainsString('name="no_hp"', $html);
    }

    public function test_profil_menolak_perubahan_nama_dan_telepon(): void
    {
        Storage::fake('public');

        $pemohon = $this->pemohon();

        $this->actingAs($pemohon, 'pemohon')
            ->put(route('akun.profil.update'), [
                'nama' => 'Nama Diretas',
                'no_hp' => '0899999999',
                'foto' => UploadedFile::fake()->image('avatar.jpg'),
            ])
            ->assertRedirect();

        $pemohon->refresh();

        $this->assertSame('Uji Pengaturan', $pemohon->nama);
        $this->assertSame('08000000072', $pemohon->no_hp);
        $this->assertStringStartsWith('uploads/avatar/', $pemohon->foto);
    }

    /**
     * Avatar di header situs dulu selalu digambar sebagai inisial, sehingga
     * foto yang baru diunggah seolah tidak tersimpan.
     */
    public function test_avatar_tampil_di_header_situs_dan_portal(): void
    {
        $pemohon = $this->pemohon();
        $pemohon->forceFill(['foto' => 'uploads/avatar/uji-71.png'])->save();

        $urlFoto = route('media.show', ['path' => 'uploads/avatar/uji-71.png']);

        // Header situs publik.
        $beranda = $this->actingAs($pemohon, 'pemohon')->get('/')->assertOk()->getContent();
        $this->assertStringContainsString($urlFoto, $beranda);

        // Sapaan Portal Pemohon.
        $portal = $this->actingAs($pemohon, 'pemohon')
            ->get(route('akun.profil'))
            ->assertOk()
            ->getContent();
        $this->assertStringContainsString($urlFoto, $portal);
    }

    public function test_avatar_kosong_jatuh_ke_inisial_nama(): void
    {
        $pemohon = $this->pemohon();
        $pemohon->forceFill(['foto' => null])->save();

        $html = $this->actingAs($pemohon, 'pemohon')->get('/')->assertOk()->getContent();

        $this->assertStringNotContainsString('uploads/avatar/', $html);
        // Huruf pertama "Uji Pengaturan".
        $this->assertStringContainsString('>U', $html);
    }

    public function test_data_pemohon_terkunci_setelah_terverifikasi(): void
    {
        $pemohon = $this->pemohon('terverifikasi');

        $html = $this->actingAs($pemohon, 'pemohon')
            ->get(route('akun.data-pemohon'))
            ->assertOk()
            ->getContent();

        $this->assertStringNotContainsString('name="nik"', $html);
        $this->assertStringNotContainsString('name="file_ktp"', $html);
        $this->assertStringNotContainsString('Kirim untuk Verifikasi', $html);
        $this->assertStringContainsString('Lihat berkas KTP', $html);
        $this->assertStringContainsString('tidak dapat diubah sendiri', $html);
    }

    public function test_data_pemohon_masih_bisa_diisi_sebelum_terverifikasi(): void
    {
        $pemohon = $this->pemohon('ditolak');

        $html = $this->actingAs($pemohon, 'pemohon')
            ->get(route('akun.data-pemohon'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('name="nik"', $html);
        $this->assertStringContainsString('name="file_ktp"', $html);
        $this->assertStringContainsString('Kirim untuk Verifikasi', $html);
    }

    public function test_server_menolak_simpan_data_pemohon_yang_sudah_terverifikasi(): void
    {
        $pemohon = $this->pemohon('terverifikasi');

        $this->actingAs($pemohon, 'pemohon')
            ->from(route('akun.data-pemohon'))
            ->put(route('akun.data-pemohon.update'), [
                'jenis_pemohon' => 'perorangan',
                'nik' => '9999999999999999',
                'pekerjaan' => 'Diubah',
                'alamat' => 'Alamat Diubah',
            ])
            ->assertSessionHasErrors('nik');

        $pemohon->refresh();

        $this->assertSame('3175010101900001', $pemohon->nik);
        $this->assertSame('Karyawan', $pemohon->pekerjaan);
        $this->assertSame('terverifikasi', $pemohon->status_verifikasi);
    }

    public function test_data_pemohon_terkunci_selama_menunggu_pemeriksaan(): void
    {
        $pemohon = $this->pemohon('menunggu');

        $html = $this->actingAs($pemohon, 'pemohon')
            ->get(route('akun.data-pemohon'))
            ->assertOk()
            ->getContent();

        $this->assertStringNotContainsString('name="nik"', $html);
        $this->assertStringNotContainsString('name="file_ktp"', $html);
        $this->assertStringNotContainsString('Kirim untuk Verifikasi', $html);
        $this->assertStringContainsString('sedang diperiksa petugas PPID', $html);
    }

    public function test_server_menolak_kiriman_ulang_selama_menunggu_pemeriksaan(): void
    {
        $pemohon = $this->pemohon('menunggu');

        $this->actingAs($pemohon, 'pemohon')
            ->from(route('akun.data-pemohon'))
            ->put(route('akun.data-pemohon.update'), [
                'jenis_pemohon' => 'perorangan',
                'nik' => '9999999999999999',
                'pekerjaan' => 'Diubah',
                'alamat' => 'Alamat Diubah',
            ])
            ->assertSessionHasErrors('nik');

        $pemohon->refresh();

        $this->assertSame('3175010101900001', $pemohon->nik);
        $this->assertSame('Karyawan', $pemohon->pekerjaan);
    }

    public function test_kiriman_data_pemohon_diarahkan_ke_dashboard(): void
    {
        Storage::fake('public');

        $pemohon = $this->pemohon();

        $this->actingAs($pemohon, 'pemohon')
            ->from(route('akun.data-pemohon'))
            ->put(route('akun.data-pemohon.update'), [
                'jenis_pemohon' => 'perorangan',
                'nik' => '3175010101900002',
                'pekerjaan' => 'Karyawan',
                'alamat' => 'Jalan Uji Nomor 2',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('akun.dashboard'));

        $this->assertSame('menunggu', $pemohon->refresh()->status_verifikasi);
    }

    public function test_nik_lebih_dari_16_digit_ditolak(): void
    {
        $pemohon = $this->pemohon();

        $this->actingAs($pemohon, 'pemohon')
            ->from(route('akun.data-pemohon'))
            ->put(route('akun.data-pemohon.update'), [
                'jenis_pemohon' => 'perorangan',
                'nik' => '31750101019000012345',
                'pekerjaan' => 'Karyawan',
                'alamat' => 'Jalan Uji Nomor 2',
            ])
            ->assertSessionHasErrors('nik');

        $this->assertSame('belum', $pemohon->refresh()->status_verifikasi);
    }

    public function test_nik_kurang_dari_16_digit_ditolak(): void
    {
        $pemohon = $this->pemohon();

        // Angka yang dipakai penguji UAT: lolos waktu aturannya masih
        // "paling banyak 16 digit".
        $this->actingAs($pemohon, 'pemohon')
            ->from(route('akun.data-pemohon'))
            ->put(route('akun.data-pemohon.update'), [
                'jenis_pemohon' => 'perorangan',
                'nik' => '123123123123',
                'pekerjaan' => 'Karyawan',
                'alamat' => 'Jalan Uji Nomor 2',
            ])
            ->assertSessionHasErrors('nik');

        $this->assertSame('belum', $pemohon->refresh()->status_verifikasi);
    }

    public function test_nik_berisi_huruf_ditolak(): void
    {
        $pemohon = $this->pemohon();

        $this->actingAs($pemohon, 'pemohon')
            ->from(route('akun.data-pemohon'))
            ->put(route('akun.data-pemohon.update'), [
                'jenis_pemohon' => 'perorangan',
                'nik' => '31750101019000AB',
                'pekerjaan' => 'Karyawan',
                'alamat' => 'Jalan Uji Nomor 2',
            ])
            ->assertSessionHasErrors('nik');
    }

    public function test_jenis_pemohon_tidak_punya_pilihan_bawaan(): void
    {
        $pemohon = $this->pemohon();
        $pemohon->forceFill(['jenis_pemohon' => null])->save();

        $html = $this->actingAs($pemohon, 'pemohon')
            ->get(route('akun.data-pemohon'))
            ->assertOk()
            ->getContent();

        // Bukaan dropdown-nya "Pilih Jenis Pemohon" dan itu yang terpilih,
        // bukan salah satu jenis asli — pemohon yang memilih sendiri.
        $this->assertStringContainsString('<option value="" disabled selected>Pilih Jenis Pemohon</option>', $html);
        $this->assertStringNotContainsString('<option value="perorangan" selected>', $html);
        $this->assertStringContainsString("jenis: ''", $html);
        $this->assertStringContainsString('pattern="[0-9]{16}"', $html);
        $this->assertStringContainsString('maxlength="16"', $html);
    }

    public function test_jenis_pemohon_yang_pernah_dipilih_tetap_terpilih(): void
    {
        // Berkas yang ditolak dibuka lagi untuk diperbaiki; pilihan lamanya
        // tidak boleh ikut terhapus jadi "Pilih Jenis Pemohon".
        $pemohon = $this->pemohon('ditolak');
        $pemohon->forceFill(['jenis_pemohon' => 'mahasiswa'])->save();

        $html = $this->actingAs($pemohon, 'pemohon')
            ->get(route('akun.data-pemohon'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('<option value="mahasiswa" selected>', $html);
        $this->assertStringContainsString("jenis: 'mahasiswa'", $html);
    }
}
