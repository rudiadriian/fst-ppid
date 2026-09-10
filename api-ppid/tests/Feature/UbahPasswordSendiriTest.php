<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Ubah password mandiri dari panel (UAT poin 9).
 *
 * Yang dijaga: password lama tetap dituntut walau tokennya sah, syarat password
 * barunya tidak lebih longgar daripada saat akun dibuat administrator, dan
 * setelah berganti hanya password baru yang bisa dipakai masuk.
 *
 * Memakai `DatabaseTransactions`, bukan `RefreshDatabase`: sebagian tabel inti
 * `ppiddb` dibuat lewat DDL dan tidak punya migration sendiri, jadi membangun
 * ulang basis data untuk tiap tes akan menghapus yang tidak bisa dikembalikan.
 */
class UbahPasswordSendiriTest extends TestCase
{
    use DatabaseTransactions;

    private string $passwordLama = 'RahasiaKuat#123';

    private string $passwordBaru = 'GantiSandi#2026';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'ppid.akun.recaptcha_aktif' => false,
            'ppid.akun.gagal_per_tahap' => 99,
        ]);

        Mail::fake();
    }

    private function petugas(): User
    {
        return User::create([
            'name' => 'Petugas Ubah Sandi',
            'email' => 'ubah-sandi-'.Str::lower(Str::random(8)).'@uji.test',
            'password' => Hash::make($this->passwordLama),
            'role_id' => Role::query()->value('id'),
            'is_active' => true,
        ]);
    }

    private function token(User $user, ?string $password = null): string
    {
        return $this->postJson('/api/v1/auth/sign-in', [
            'email' => $user->email,
            'password' => $password ?? $this->passwordLama,
        ])->assertOk()->json('access_token');
    }

    public function test_password_berganti_dan_hanya_yang_baru_bisa_dipakai_masuk(): void
    {
        $user = $this->petugas();

        $this->withToken($this->token($user))
            ->postJson('/api/v1/auth/ubah-password', [
                'password_lama' => $this->passwordLama,
                'password' => $this->passwordBaru,
                'password_confirmation' => $this->passwordBaru,
            ])
            ->assertOk()
            ->assertJsonPath('message', 'Password berhasil diubah.');

        $this->assertTrue(Hash::check($this->passwordBaru, $user->fresh()->password));

        $this->postJson('/api/v1/auth/sign-in', [
            'email' => $user->email,
            'password' => $this->passwordLama,
        ])->assertStatus(401);

        $this->postJson('/api/v1/auth/sign-in', [
            'email' => $user->email,
            'password' => $this->passwordBaru,
        ])->assertOk();
    }

    /** Token yang sah saja tidak cukup: password lama tetap harus dibuktikan. */
    public function test_password_lama_salah_ditolak(): void
    {
        $user = $this->petugas();

        $this->withToken($this->token($user))
            ->postJson('/api/v1/auth/ubah-password', [
                'password_lama' => 'jelas-salah',
                'password' => $this->passwordBaru,
                'password_confirmation' => $this->passwordBaru,
            ])
            ->assertStatus(422)
            ->assertJsonPath('errors.password_lama.0', 'Password lama tidak cocok.');

        $this->assertTrue(Hash::check($this->passwordLama, $user->fresh()->password));
    }

    /** Syaratnya sama dengan modul Pengguna: 12 karakter, campuran, bersimbol. */
    public function test_password_baru_lemah_ditolak(): void
    {
        $user = $this->petugas();

        $this->withToken($this->token($user))
            ->postJson('/api/v1/auth/ubah-password', [
                'password_lama' => $this->passwordLama,
                'password' => 'rahasia123',
                'password_confirmation' => 'rahasia123',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('password');

        $this->assertTrue(Hash::check($this->passwordLama, $user->fresh()->password));
    }

    public function test_ulangan_password_harus_sama(): void
    {
        $user = $this->petugas();

        $this->withToken($this->token($user))
            ->postJson('/api/v1/auth/ubah-password', [
                'password_lama' => $this->passwordLama,
                'password' => $this->passwordBaru,
                'password_confirmation' => $this->passwordBaru.'x',
            ])
            ->assertStatus(422)
            ->assertJsonPath('errors.password.0', 'Ulangan password baru tidak sama.');
    }

    /** Mengganti dengan password yang sama tidak mengubah apa pun. */
    public function test_password_baru_tidak_boleh_sama_dengan_yang_lama(): void
    {
        $user = $this->petugas();

        $this->withToken($this->token($user))
            ->postJson('/api/v1/auth/ubah-password', [
                'password_lama' => $this->passwordLama,
                'password' => $this->passwordLama,
                'password_confirmation' => $this->passwordLama,
            ])
            ->assertStatus(422)
            ->assertJsonPath('errors.password.0', 'Password baru harus berbeda dari password lama.');
    }

    public function test_tanpa_token_ditolak(): void
    {
        $user = $this->petugas();

        $this->postJson('/api/v1/auth/ubah-password', [
            'password_lama' => $this->passwordLama,
            'password' => $this->passwordBaru,
            'password_confirmation' => $this->passwordBaru,
        ])->assertStatus(401);

        $this->assertTrue(Hash::check($this->passwordLama, $user->fresh()->password));
    }

    /**
     * Endpoint ini tidak digantung hak modul Pengguna: petugas tanpa hak apa
     * pun di sana tetap boleh mengurus akunnya sendiri.
     */
    public function test_petugas_tanpa_hak_modul_pengguna_tetap_bisa_mengganti(): void
    {
        $role = Role::where('slug', 'ppid-pelaksana')->first() ?? Role::query()->firstOrFail();

        $user = User::create([
            'name' => 'Pelaksana Ubah Sandi',
            'email' => 'pelaksana-sandi-'.Str::lower(Str::random(8)).'@uji.test',
            'password' => Hash::make($this->passwordLama),
            'role_id' => $role->id,
            'is_active' => true,
        ]);

        // Bukti bahwa rolenya memang tidak boleh menyentuh modul Pengguna.
        $this->withToken($this->token($user))
            ->getJson('/api/v1/pengguna')
            ->assertStatus(403);

        $this->withToken($this->token($user))
            ->postJson('/api/v1/auth/ubah-password', [
                'password_lama' => $this->passwordLama,
                'password' => $this->passwordBaru,
                'password_confirmation' => $this->passwordBaru,
            ])
            ->assertOk();
    }
}
