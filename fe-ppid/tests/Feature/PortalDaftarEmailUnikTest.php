<?php

namespace Tests\Feature;

use App\Models\Pemohon;
use App\Rules\EmailBelumTerpakai;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Satu email, satu akun, di seluruh sistem PPID (UAT poin 11) — sisi situs.
 *
 * Aturannya diuji langsung lewat `Validator`, bukan lewat kiriman formulir:
 * isian email pendaftaran juga memakai `email:rfc,dns` yang menembak DNS, dan
 * yang sedang diuji di sini bukan itu. Satu tes kiriman formulir tetap ada
 * untuk membuktikan aturannya benar-benar terpasang pada jalur pendaftaran.
 *
 * `DatabaseTransactions`, bukan `RefreshDatabase`: basis data pengembangan ini
 * berisi data nyata, jadi baris uji cukup digulung balik.
 */
class PortalDaftarEmailUnikTest extends TestCase
{
    use DatabaseTransactions;

    private function periksa(string $email): \Illuminate\Validation\Validator
    {
        return Validator::make(['email' => $email], ['email' => [new EmailBelumTerpakai()]]);
    }

    /** Baris `users` milik be-ppid, dibuat lewat query mentah: fe-ppid tidak punya modelnya. */
    private function petugas(string $email): int
    {
        return (int) DB::table('users')->insertGetId([
            'name' => 'Petugas Uji Email',
            'email' => $email,
            'password' => bcrypt('RahasiaKuat#123'),
            'is_active' => true,
            'created_at' => now(),
        ]);
    }

    public function test_email_petugas_panel_tidak_bisa_dipakai_mendaftar(): void
    {
        $email = 'petugas-lintas-'.Str::lower(Str::random(8)).'@uji.test';
        $this->petugas($email);

        $hasil = $this->periksa($email);

        $this->assertTrue($hasil->fails());
        $this->assertSame(
            'Email ini sudah terdaftar sebagai akun petugas PPID sehingga tidak bisa dipakai mendaftar. Gunakan alamat email lain.',
            $hasil->errors()->first('email')
        );
    }

    /** Perbandingannya tidak peka huruf besar-kecil. */
    public function test_email_petugas_dengan_huruf_berbeda_tetap_ditolak(): void
    {
        $email = 'petugas-huruf-'.Str::lower(Str::random(8)).'@uji.test';
        $this->petugas($email);

        $this->assertTrue($this->periksa(Str::upper($email))->fails());
    }

    /**
     * Baris pemohon yang sudah dihapus tetap menempati indeks unik `pemohon.email`.
     * Tanpa pemeriksaan ini, pendaftarannya lolos validasi lalu jatuh sebagai
     * galat SQL — 500 di layar pendaftar, bukan pesan pada isian emailnya.
     */
    public function test_email_pemohon_terhapus_ditolak_dengan_pesan_bukan_galat_sql(): void
    {
        $email = 'pemohon-terhapus-'.Str::lower(Str::random(8)).'@uji.test';

        $pemohon = Pemohon::create([
            'nama' => 'Pemohon Terhapus',
            'email' => $email,
            'no_hp' => '08000000011',
            'jenis_pemohon' => 'perorangan',
        ]);
        $pemohon->delete();

        $hasil = $this->periksa($email);

        $this->assertTrue($hasil->fails());
        $this->assertStringContainsString('sudah dihapus', $hasil->errors()->first('email'));
    }

    /**
     * Baris pemohon yang masih aktif sengaja dilewatkan aturan ini: jalurnya
     * sudah ditangani RegisterController — diklaim bila belum berpassword,
     * ditolak "silakan masuk" bila sudah.
     */
    public function test_baris_pemohon_aktif_bukan_urusan_aturan_ini(): void
    {
        $email = 'pemohon-aktif-'.Str::lower(Str::random(8)).'@uji.test';

        Pemohon::create([
            'nama' => 'Pemohon Aktif',
            'email' => $email,
            'no_hp' => '08000000011',
            'jenis_pemohon' => 'perorangan',
        ]);

        $this->assertFalse($this->periksa($email)->fails());
    }

    public function test_email_yang_belum_dipakai_lolos(): void
    {
        $this->assertFalse($this->periksa('belum-dipakai-'.Str::lower(Str::random(10)).'@uji.test')->fails());
    }

    /** Aturannya memang terpasang pada jalur pendaftaran, bukan hanya ada di berkasnya. */
    public function test_formulir_pendaftaran_menolak_email_petugas(): void
    {
        config([
            'ppid.akun.captcha_aktif' => false,
            'ppid.akun.perisai_formulir' => false,
        ]);

        // Domain sungguhan: isian email pendaftaran juga diperiksa `dns`.
        $email = 'petugas.formulir.'.Str::lower(Str::random(8)).'@gmail.com';
        $this->petugas($email);

        $this->from('/akun/daftar')
            ->post('/akun/daftar', [
                'nama' => 'Calon Pemohon',
                'email' => $email,
                'no_hp' => '08123456789',
                'password' => 'RahasiaUji12345',
                'password_confirmation' => 'RahasiaUji12345',
                'setuju' => '1',
            ])
            ->assertRedirect('/akun/daftar')
            ->assertSessionHasErrors('email');

        $this->assertFalse(
            Pemohon::withTrashed()->where('email', $email)->exists(),
            'Tidak boleh ada baris pemohon yang lahir dari kiriman yang ditolak.'
        );
    }
}
