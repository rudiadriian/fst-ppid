<?php

namespace Tests\Feature;

use App\Models\Pemohon;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Hapus permanen akun panel (UAT poin 10) dan keunikan email lintas tabel
 * (UAT poin 11).
 *
 * Keduanya diuji berdampingan karena memang satu rangkaian: penghapusan lunak
 * meninggalkan barisnya di `users`, dan selama baris itu ada, emailnya tetap
 * menempati indeks unik sehingga tidak bisa dipakai akun baru. Pelepasan
 * permanen inilah yang membebaskannya.
 *
 * Memakai `DatabaseTransactions` dengan alasan yang sama seperti tes lain di
 * sini: skema `ppiddb` sebagian dibuat lewat DDL, bukan migration.
 */
class PenggunaHapusPermanenTest extends TestCase
{
    use DatabaseTransactions;

    private string $password = 'RahasiaKuat#123';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'ppid.akun.recaptcha_aktif' => false,
            'ppid.akun.gagal_per_tahap' => 99,
        ]);

        Mail::fake();
    }

    private function akun(string $roleSlug): User
    {
        return User::create([
            'name' => 'Uji '.$roleSlug,
            'email' => Str::slug($roleSlug).'-'.Str::lower(Str::random(8)).'@uji.test',
            'password' => Hash::make($this->password),
            'role_id' => Role::where('slug', $roleSlug)->value('id'),
            'is_active' => true,
        ]);
    }

    private function token(User $user): string
    {
        return $this->postJson('/api/v1/auth/sign-in', [
            'email' => $user->email,
            'password' => $this->password,
        ])->assertOk()->json('access_token');
    }

    private function tokenAdmin(): string
    {
        return $this->token($this->akun('super-admin'));
    }

    /** Akun sasaran yang sudah masuk arsip penghapusan. */
    private function akunTerhapus(): User
    {
        $target = $this->akun('ppid-pelaksana');
        $target->delete();

        return $target;
    }

    public function test_akun_terhapus_bisa_dilepas_permanen(): void
    {
        $token = $this->tokenAdmin();
        $target = $this->akunTerhapus();

        $this->withToken($token)
            ->deleteJson("/api/v1/pengguna/{$target->id}/permanen")
            ->assertOk()
            ->assertJsonPath('message', 'Akun dihapus permanen');

        $this->assertFalse(
            DB::table('users')->where('id', $target->id)->exists(),
            'Barisnya harus benar-benar lepas dari basis data, bukan sekadar ditandai.'
        );

        // Penghapusannya ikut tercatat, lengkap dengan nomor barisnya.
        $this->assertTrue(
            DB::table('audit_log')
                ->where('action', 'force_delete')
                ->where('model_id', $target->id)
                ->exists()
        );
    }

    /**
     * Arsip penghapusan adalah ruang jeda yang disengaja: tidak ada akun aktif
     * yang bisa lenyap dalam satu langkah.
     */
    public function test_akun_yang_masih_aktif_tidak_bisa_dihapus_permanen(): void
    {
        $token = $this->tokenAdmin();
        $target = $this->akun('ppid-pelaksana');

        $this->withToken($token)
            ->deleteJson("/api/v1/pengguna/{$target->id}/permanen")
            ->assertStatus(422)
            ->assertJsonPath('errors.id.0', 'Akun ini masih aktif. Hapus dulu akunnya, baru bisa dihapus permanen.');

        $this->assertTrue(DB::table('users')->where('id', $target->id)->exists());
    }

    public function test_role_tanpa_hak_hapus_ditolak(): void
    {
        $token = $this->token($this->akun('ppid-pelaksana'));
        $target = $this->akunTerhapus();

        $this->withToken($token)
            ->deleteJson("/api/v1/pengguna/{$target->id}/permanen")
            ->assertStatus(403);

        $this->assertTrue(DB::table('users')->where('id', $target->id)->exists());
    }

    /**
     * Inti poin 10 bagi operator: email akun yang sudah dihapus baru bisa
     * dipakai lagi setelah barisnya dilepas permanen.
     */
    public function test_email_baru_bisa_dipakai_lagi_setelah_dilepas_permanen(): void
    {
        $token = $this->tokenAdmin();
        $target = $this->akunTerhapus();
        $email = $target->email;

        $baru = [
            'role_id' => Role::where('slug', 'ppid-pelaksana')->value('id'),
            'name' => 'Pengganti',
            'email' => $email,
            'password' => 'SandiPengganti#2026',
            'is_active' => true,
        ];

        $this->withToken($token)
            ->postJson('/api/v1/pengguna', $baru)
            ->assertStatus(422)
            ->assertJsonValidationErrors('email');

        $this->withToken($token)->deleteJson("/api/v1/pengguna/{$target->id}/permanen")->assertOk();

        $this->withToken($token)
            ->postJson('/api/v1/pengguna', $baru)
            ->assertStatus(201)
            ->assertJsonPath('data.email', $email);
    }

    // ------------------------------------------------------------------
    // Poin 11 — satu email, satu akun, lintas tabel
    // ------------------------------------------------------------------

    private function pemohon(string $email): Pemohon
    {
        return Pemohon::create([
            'nama' => 'Pemohon Uji Email',
            'email' => $email,
            'no_hp' => '08000000011',
            'jenis_pemohon' => 'perorangan',
        ]);
    }

    public function test_email_milik_pemohon_tidak_bisa_dipakai_akun_panel(): void
    {
        $token = $this->tokenAdmin();
        $email = 'pemohon-lintas-'.Str::lower(Str::random(8)).'@uji.test';
        $this->pemohon($email);

        $this->withToken($token)
            ->postJson('/api/v1/pengguna', [
                'role_id' => Role::where('slug', 'ppid-pelaksana')->value('id'),
                'name' => 'Petugas Bentrok',
                'email' => $email,
                'password' => 'SandiPetugas#2026',
                'is_active' => true,
            ])
            ->assertStatus(422)
            ->assertJsonPath(
                'errors.email.0',
                'Email ini sudah dipakai akun pemohon di situs PPID. Gunakan alamat lain.'
            );
    }

    /** Perbandingannya tidak boleh peka huruf besar-kecil. */
    public function test_email_pemohon_dengan_huruf_berbeda_tetap_ditolak(): void
    {
        $token = $this->tokenAdmin();
        $email = 'pemohon-huruf-'.Str::lower(Str::random(8)).'@uji.test';
        $this->pemohon($email);

        $this->withToken($token)
            ->postJson('/api/v1/pengguna', [
                'role_id' => Role::where('slug', 'ppid-pelaksana')->value('id'),
                'name' => 'Petugas Bentrok',
                'email' => Str::upper($email),
                'password' => 'SandiPetugas#2026',
                'is_active' => true,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    public function test_email_petugas_lain_tetap_ditolak(): void
    {
        $token = $this->tokenAdmin();
        $lain = $this->akun('ppid-pelaksana');

        $this->withToken($token)
            ->postJson('/api/v1/pengguna', [
                'role_id' => Role::where('slug', 'ppid-pelaksana')->value('id'),
                'name' => 'Kembaran',
                'email' => $lain->email,
                'password' => 'SandiPetugas#2026',
                'is_active' => true,
            ])
            ->assertStatus(422)
            ->assertJsonPath(
                'errors.email.0',
                'Email ini sudah dipakai akun petugas panel. Gunakan alamat lain.'
            );
    }

    /** Menyunting akun tanpa mengganti emailnya tidak boleh menabrak dirinya sendiri. */
    public function test_menyimpan_akun_dengan_email_sendiri_tetap_boleh(): void
    {
        $token = $this->tokenAdmin();
        $target = $this->akun('ppid-pelaksana');

        $this->withToken($token)
            ->putJson("/api/v1/pengguna/{$target->id}", [
                'name' => 'Nama Baru',
                'email' => $target->email,
            ])
            ->assertOk()
            ->assertJsonPath('data.name', 'Nama Baru');
    }
}
