<?php

namespace Tests\Feature;

use App\Models\ModulSistem;
use App\Models\Role;
use App\Models\User;
use App\Support\AuditLogger;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Halaman Akun Saya di panel (UAT poin 9): profil, role & hak akses, dan
 * riwayat aktivitas milik akun sendiri.
 *
 * Ubah passwordnya diuji terpisah di `UbahPasswordSendiriTest`.
 *
 * `DatabaseTransactions` dengan alasan yang sama seperti tes lain di sini:
 * sebagian tabel inti `ppiddb` dibuat lewat DDL, bukan migration.
 */
class AkunSayaTest extends TestCase
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
            'name' => 'Uji Akun '.$roleSlug,
            'email' => 'akun-'.Str::slug($roleSlug).'-'.Str::lower(Str::random(8)).'@uji.test',
            'password' => Hash::make($this->password),
            'role_id' => Role::where('slug', $roleSlug)->value('id'),
            'phone' => '0800000000',
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

    public function test_profil_membawa_keterangan_akun_role_dan_hak_akses(): void
    {
        $user = $this->akun('ppid-pelaksana');

        $respons = $this->withToken($this->token($user))
            ->getJson('/api/v1/auth/akun')
            ->assertOk();

        $respons->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.email', $user->email)
            ->assertJsonPath('data.phone', $user->phone)
            ->assertJsonPath('data.role.slug', 'ppid-pelaksana')
            ->assertJsonPath('data.super_admin', false);

        // Seluruh modul aktif ikut, termasuk yang tertutup untuk role ini —
        // halaman ini menjawab "saya boleh apa saja".
        $this->assertSame(
            ModulSistem::where('is_active', true)->count(),
            count($respons->json('data.akses'))
        );

        $this->assertNotEmpty(array_filter(
            $respons->json('data.akses'),
            fn (array $baris) => $baris['view'] === false
        ), 'Modul yang tertutup untuk role ini harus tetap tampil, bukan disaring.');
    }

    public function test_super_admin_ditandai_dan_memegang_semua_hak(): void
    {
        $user = $this->akun('super-admin');

        $respons = $this->withToken($this->token($user))
            ->getJson('/api/v1/auth/akun')
            ->assertOk()
            ->assertJsonPath('data.super_admin', true);

        foreach ($respons->json('data.akses') as $baris) {
            $this->assertTrue($baris['view'] && $baris['edit'] && $baris['delete'] && $baris['approve']);
        }
    }

    public function test_profil_bisa_disunting_sendiri(): void
    {
        $user = $this->akun('ppid-pelaksana');

        $this->withToken($this->token($user))
            ->putJson('/api/v1/auth/akun', [
                'name' => 'Nama Baru Sendiri',
                'phone' => '081234567890',
                'photo_url' => 'uploads/pengguna/foto.jpg',
            ])
            ->assertOk()
            ->assertJsonPath('data.name', 'Nama Baru Sendiri');

        $segar = $user->fresh();
        $this->assertSame('Nama Baru Sendiri', $segar->name);
        $this->assertSame('081234567890', $segar->phone);
        $this->assertSame('uploads/pengguna/foto.jpg', $segar->photo_url);

        $this->assertTrue(
            DB::table('audit_log')
                ->where('action', 'ubah_profil_sendiri')
                ->where('model_id', $user->id)
                ->exists()
        );
    }

    /**
     * Email dan role adalah identitas masuk dan penentu hak akses; keduanya
     * urusan administrator, jadi endpoint ini tidak boleh menerimanya.
     */
    public function test_email_dan_role_tidak_bisa_diubah_lewat_profil_sendiri(): void
    {
        $user = $this->akun('ppid-pelaksana');
        $roleLain = Role::where('slug', 'super-admin')->value('id');

        $this->withToken($this->token($user))
            ->putJson('/api/v1/auth/akun', [
                'name' => 'Masih Sama Saja',
                'email' => 'email-baru-'.Str::lower(Str::random(6)).'@uji.test',
                'role_id' => $roleLain,
            ])
            ->assertOk();

        $segar = $user->fresh();
        $this->assertSame($user->email, $segar->email);
        $this->assertSame($user->role_id, $segar->role_id);
    }

    public function test_nama_kosong_ditolak(): void
    {
        $user = $this->akun('ppid-pelaksana');

        $this->withToken($this->token($user))
            ->putJson('/api/v1/auth/akun', ['name' => ''])
            ->assertStatus(422)
            ->assertJsonPath('errors.name.0', 'Nama tidak boleh kosong.');
    }

    /** Riwayat hanya berisi jejak pemanggilnya sendiri. */
    public function test_aktivitas_hanya_milik_sendiri(): void
    {
        $user = $this->akun('ppid-pelaksana');
        $orangLain = $this->akun('ppid-pelaksana');

        $token = $this->token($user); // ikut menulis baris audit `login`

        AuditLogger::record($orangLain->id, 'update', User::class, $orangLain->id);

        $respons = $this->withToken($token)
            ->getJson('/api/v1/auth/akun/aktivitas')
            ->assertOk();

        $baris = $respons->json('data');

        $this->assertNotEmpty($baris);
        $this->assertContains('login', array_column($baris, 'action'));

        // Baris milik orang lain tidak boleh ikut. Dibuktikan lewat nomor
        // barisnya di `audit_log`, bukan sekadar jumlahnya.
        $idOrangLain = DB::table('audit_log')->where('user_id', $orangLain->id)->pluck('id')->all();
        $this->assertEmpty(array_intersect($idOrangLain, array_column($baris, 'id')));
    }

    public function test_akun_tanpa_token_ditolak(): void
    {
        $this->getJson('/api/v1/auth/akun')->assertStatus(401);
        $this->putJson('/api/v1/auth/akun', ['name' => 'X'])->assertStatus(401);
        $this->getJson('/api/v1/auth/akun/aktivitas')->assertStatus(401);
    }
}
