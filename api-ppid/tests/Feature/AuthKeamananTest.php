<?php

namespace Tests\Feature;

use App\Models\PercobaanLoginAdmin;
use App\Models\PercobaanTautanAdmin;
use App\Models\Role;
use App\Models\User;
use App\Support\Captcha;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password as PasswordBroker;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Pengaman fitur auth panel: captcha, kunci bertingkat, suspend, dan jalur
 * lupa password.
 *
 * Memakai `DatabaseTransactions`, bukan `RefreshDatabase`: skema `ppiddb`
 * dibuat lewat DDL dan sebagian tabel intinya tidak punya migration, jadi
 * membangun ulang basis data untuk tiap tes akan menghapus yang tidak bisa
 * dikembalikan. Semua yang ditulis tes ini digulung balik saat tes selesai.
 */
class AuthKeamananTest extends TestCase
{
    use DatabaseTransactions;

    private string $password = 'RahasiaKuat123';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'ppid.akun.gagal_per_tahap' => 3,
            'ppid.akun.tahap_kunci_menit' => [60, 1440, 20160],
            'ppid.akun.captcha_aktif' => false,
            'ppid.akun.jeda_kirim_tautan_menit' => 0,
        ]);

        Mail::fake();
    }

    private function petugas(): User
    {
        $role = Role::query()->first();

        return User::query()->create([
            'role_id' => $role?->id,
            'name' => 'Petugas Uji',
            'email' => 'uji-keamanan-'.Str::random(8).'@contoh.test',
            'password' => Hash::make($this->password),
            'is_active' => true,
        ]);
    }

    /** Satu percobaan masuk dengan password salah. */
    private function gagalMasuk(string $email): \Illuminate\Testing\TestResponse
    {
        return $this->postJson('/api/v1/auth/sign-in', [
            'email' => $email,
            'password' => 'jelas-salah',
        ]);
    }

    public function test_captcha_wajib_saat_dinyalakan(): void
    {
        config(['ppid.akun.captcha_aktif' => true]);

        $this->postJson('/api/v1/auth/sign-in', [
            'email' => 'siapa@contoh.test',
            'password' => 'apa saja',
        ])->assertStatus(422)->assertJsonFragment(['type' => 'captcha']);
    }

    public function test_captcha_yang_benar_diterima_dan_hanya_sekali_pakai(): void
    {
        config(['ppid.akun.captcha_aktif' => true]);

        ['id' => $id, 'kode' => $kode] = Captcha::buat();

        $this->assertTrue(Captcha::cocok($id, $kode));
        // Sudah dibuang setelah diperiksa: kode yang sama tidak berlaku lagi.
        $this->assertFalse(Captcha::cocok($id, $kode));
    }

    public function test_captcha_tidak_peduli_huruf_besar_kecil(): void
    {
        ['id' => $id, 'kode' => $kode] = Captcha::buat();

        $this->assertTrue(Captcha::cocok($id, Str::lower($kode)));
    }

    public function test_endpoint_captcha_memberi_id_dan_gambar(): void
    {
        config(['ppid.akun.captcha_aktif' => true]);

        $this->getJson('/api/v1/auth/captcha')
            ->assertOk()
            ->assertJsonPath('data.aktif', true)
            ->assertJsonStructure(['data' => ['aktif', 'id', 'gambar']]);
    }

    public function test_tiga_kegagalan_mengunci_satu_jam(): void
    {
        $user = $this->petugas();

        $this->gagalMasuk($user->email)->assertStatus(401);
        $this->gagalMasuk($user->email)->assertStatus(401);
        // Kegagalan ketiga sekaligus memasang kuncinya, jadi sudah 429.
        $this->gagalMasuk($user->email)->assertStatus(429);

        $baris = PercobaanLoginAdmin::query()->where('identitas', $user->email)->firstOrFail();

        $this->assertSame(3, $baris->jumlah_gagal);
        $this->assertSame(1, $baris->tahap_kunci);
        $this->assertTrue($baris->sedangTerkunci());
        // 1 jam, dengan toleransi beberapa detik untuk waktu jalannya tes.
        $this->assertEqualsWithDelta(60, now()->diffInMinutes($baris->terkunci_sampai), 1);
    }

    public function test_percobaan_saat_terkunci_ditolak_tanpa_menambah_hitungan(): void
    {
        $user = $this->petugas();

        for ($i = 0; $i < 3; $i++) {
            $this->gagalMasuk($user->email);
        }

        $this->gagalMasuk($user->email)->assertStatus(429);

        $baris = PercobaanLoginAdmin::query()->where('identitas', $user->email)->firstOrFail();

        // Tetap 3: percobaan yang ditolak di gerbang tidak boleh mempercepat
        // perjalanan menuju suspend.
        $this->assertSame(3, $baris->jumlah_gagal);
    }

    public function test_tangga_naik_sampai_suspend_pada_kegagalan_kedua_belas(): void
    {
        /*
         * Rem per menit (`throttle:login`, 5 percobaan) dimatikan khusus di tes
         * ini. Yang sedang diuji tangga bertingkat di basis data, dan tangganya
         * baru mencapai suspend pada percobaan ke-12 — mustahil dijalani dalam
         * satu menit lewat gerbang itu. Perilaku rem per menitnya sendiri
         * diuji terpisah oleh `test_percobaan_saat_terkunci_ditolak...`.
         */
        $this->withoutMiddleware(\Illuminate\Routing\Middleware\ThrottleRequests::class);

        $user = $this->petugas();

        $tangga = [
            3 => ['tahap' => 1, 'menit' => 60],
            6 => ['tahap' => 2, 'menit' => 1440],
            9 => ['tahap' => 3, 'menit' => 20160],
        ];

        foreach ($tangga as $sampai => $harapan) {
            // Kuncinya dilewati dengan memundurkan waktu berakhirnya, bukan
            // dengan menghapus barisnya — hitungannya harus tetap berjalan.
            PercobaanLoginAdmin::query()
                ->where('identitas', $user->email)
                ->update(['terkunci_sampai' => now()->subMinute()]);

            // Batas putaran supaya kesalahan di kemudian hari gagal sebagai tes,
            // bukan menggantung tanpa akhir.
            $putaran = 0;

            while (($baris = PercobaanLoginAdmin::query()->where('identitas', $user->email)->first())?->jumlah_gagal < $sampai) {
                $this->gagalMasuk($user->email);

                $this->assertLessThan(20, ++$putaran, "Hitungan tidak naik menuju {$sampai}");
            }

            $baris = PercobaanLoginAdmin::query()->where('identitas', $user->email)->firstOrFail();

            $this->assertSame($sampai, $baris->jumlah_gagal);
            $this->assertSame($harapan['tahap'], $baris->tahap_kunci);
            $this->assertEqualsWithDelta($harapan['menit'], now()->diffInMinutes($baris->terkunci_sampai), 2);
        }

        PercobaanLoginAdmin::query()
            ->where('identitas', $user->email)
            ->update(['terkunci_sampai' => now()->subMinute()]);

        $this->gagalMasuk($user->email);
        $this->gagalMasuk($user->email);
        $terakhir = $this->gagalMasuk($user->email);

        $terakhir->assertStatus(403);

        $baris = PercobaanLoginAdmin::query()->where('identitas', $user->email)->firstOrFail();
        $this->assertSame(12, $baris->jumlah_gagal);
        $this->assertSame(4, $baris->tahap_kunci);

        $user->refresh();
        $this->assertNotNull($user->disuspend_pada, 'Akun seharusnya disuspend pada tahap keempat');
    }

    public function test_akun_disuspend_ditolak_walau_passwordnya_benar(): void
    {
        $user = $this->petugas();
        $user->forceFill(['disuspend_pada' => now(), 'alasan_suspend' => 'uji'])->save();

        $this->postJson('/api/v1/auth/sign-in', [
            'email' => $user->email,
            'password' => $this->password,
        ])->assertStatus(403)->assertJsonFragment(['type' => 'email']);
    }

    public function test_masuk_berhasil_menghapus_hitungan_kegagalan(): void
    {
        $user = $this->petugas();

        $this->gagalMasuk($user->email)->assertStatus(401);
        $this->assertSame(1, PercobaanLoginAdmin::query()->where('identitas', $user->email)->value('jumlah_gagal'));

        $this->postJson('/api/v1/auth/sign-in', [
            'email' => $user->email,
            'password' => $this->password,
        ])->assertOk()->assertJsonStructure(['user', 'access_token']);

        $this->assertSame(0, PercobaanLoginAdmin::query()->where('identitas', $user->email)->count());
    }

    public function test_email_tak_terdaftar_dijawab_sama_dengan_password_salah(): void
    {
        $asing = $this->postJson('/api/v1/auth/sign-in', [
            'email' => 'bukan-siapa-siapa-'.Str::random(6).'@contoh.test',
            'password' => 'apa saja',
        ]);

        $user = $this->petugas();
        $salah = $this->gagalMasuk($user->email);

        // Status maupun bentuk pesannya tidak boleh membocorkan mana yang ada.
        $asing->assertStatus(401);
        $salah->assertStatus(401);
        $this->assertSame(
            $asing->json('0.type'),
            $salah->json('0.type')
        );
    }

    public function test_lupa_password_menolak_email_yang_bukan_akun_panel(): void
    {
        config(['ppid.akun.beritahu_email_asing' => true]);

        $this->postJson('/api/v1/auth/lupa-password', [
            'email' => 'entah-'.Str::random(6).'@contoh.test',
        ])
            ->assertStatus(422)
            ->assertJsonFragment(['type' => 'email'])
            ->assertJsonPath('0.message', fn (string $pesan) => str_contains($pesan, 'tidak terdaftar'));
    }

    public function test_lupa_password_menolak_akun_nonaktif_dan_disuspend(): void
    {
        config(['ppid.akun.beritahu_email_asing' => true]);

        $nonaktif = $this->petugas();
        $nonaktif->forceFill(['is_active' => false])->save();

        $this->postJson('/api/v1/auth/lupa-password', ['email' => $nonaktif->email])
            ->assertStatus(422)
            ->assertJsonPath('0.message', fn (string $pesan) => str_contains($pesan, 'nonaktif'));

        $disuspend = $this->petugas();
        $disuspend->forceFill(['disuspend_pada' => now()])->save();

        $this->postJson('/api/v1/auth/lupa-password', ['email' => $disuspend->email])
            ->assertStatus(422)
            ->assertJsonPath('0.message', fn (string $pesan) => str_contains($pesan, 'disuspend'));
    }

    public function test_lupa_password_email_terdaftar_tetap_diterima(): void
    {
        config(['ppid.akun.beritahu_email_asing' => true]);

        $user = $this->petugas();

        $this->postJson('/api/v1/auth/lupa-password', ['email' => $user->email])->assertOk();
    }

    /**
     * Sakelar `beritahu_email_asing` dimatikan mengembalikan jawaban seragam.
     *
     * Diuji supaya jalur itu tidak mati diam-diam: ia yang harus dipakai bila
     * panel suatu saat dapat dijangkau dari internet terbuka.
     */
    public function test_jawaban_kembali_seragam_saat_sakelar_dimatikan(): void
    {
        config(['ppid.akun.beritahu_email_asing' => false]);

        $user = $this->petugas();

        $ada = $this->postJson('/api/v1/auth/lupa-password', ['email' => $user->email]);
        $tidak = $this->postJson('/api/v1/auth/lupa-password', [
            'email' => 'entah-'.Str::random(6).'@contoh.test',
        ]);

        $ada->assertOk();
        $tidak->assertOk();
        $this->assertSame($ada->json('message'), $tidak->json('message'));
    }

    /**
     * Email asing tetap ikut dihitung tangga bertingkat.
     *
     * Inilah rem yang menggantikan jawaban seragam: penyisiran alamat berhenti
     * sendiri pada percobaan ketiga, jadi menyebut "tidak terdaftar" tidak
     * berubah menjadi alat mendaftar akun panel satu per satu.
     */
    public function test_penolakan_email_asing_tetap_menaikkan_hitungan(): void
    {
        config(['ppid.akun.beritahu_email_asing' => true]);

        // Huruf kecil: kunci hitungannya dibakukan ke huruf kecil, jadi alamat
        // bercampur besar-kecil tidak akan cocok saat barisnya dicari kembali.
        $asing = 'entah-'.Str::lower(Str::random(6)).'@contoh.test';

        $this->postJson('/api/v1/auth/lupa-password', ['email' => $asing])->assertStatus(422);
        $this->postJson('/api/v1/auth/lupa-password', ['email' => $asing])->assertStatus(422);

        $baris = PercobaanTautanAdmin::query()->where('identitas', $asing)->firstOrFail();
        $this->assertSame(2, $baris->jumlah_minta);

        // Yang ketiga memasang kunci satu jam, bukan lagi menjawab "tidak terdaftar".
        $this->postJson('/api/v1/auth/lupa-password', ['email' => $asing])->assertStatus(429);

        $baris->refresh();
        $this->assertSame(3, $baris->jumlah_minta);
        $this->assertTrue($baris->sedangTerkunci());
    }

    public function test_reset_password_dengan_token_sah_mengganti_password(): void
    {
        $user = $this->petugas();
        $token = PasswordBroker::broker('users')->createToken($user);

        $baru = 'PasswordBaru456';

        $this->postJson('/api/v1/auth/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => $baru,
            'password_confirmation' => $baru,
        ])->assertOk();

        $user->refresh();
        $this->assertTrue(Hash::check($baru, $user->password));

        $this->postJson('/api/v1/auth/sign-in', [
            'email' => $user->email,
            'password' => $baru,
        ])->assertOk();
    }

    public function test_reset_password_menolak_token_palsu(): void
    {
        $user = $this->petugas();

        $this->postJson('/api/v1/auth/reset-password', [
            'token' => 'token-karangan',
            'email' => $user->email,
            'password' => 'PasswordBaru456',
            'password_confirmation' => 'PasswordBaru456',
        ])->assertStatus(422);

        $user->refresh();
        $this->assertTrue(Hash::check($this->password, $user->password));
    }

    public function test_reset_password_menolak_password_lemah(): void
    {
        $user = $this->petugas();
        $token = PasswordBroker::broker('users')->createToken($user);

        // Terlalu pendek, tanpa angka, tanpa huruf besar.
        $this->postJson('/api/v1/auth/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'rahasia',
            'password_confirmation' => 'rahasia',
        ])->assertStatus(422)->assertJsonFragment(['type' => 'password']);
    }

    public function test_reset_password_membuka_kunci_yang_sedang_berlaku(): void
    {
        $user = $this->petugas();

        for ($i = 0; $i < 3; $i++) {
            $this->gagalMasuk($user->email);
        }

        $this->assertTrue(
            PercobaanLoginAdmin::query()->where('identitas', $user->email)->firstOrFail()->sedangTerkunci()
        );

        $token = PasswordBroker::broker('users')->createToken($user);
        $baru = 'PasswordBaru456';

        $this->postJson('/api/v1/auth/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => $baru,
            'password_confirmation' => $baru,
        ])->assertOk();

        // Orang yang baru saja membuktikan dirinya pemilik email itu harus
        // bisa langsung masuk, bukan menunggu satu jam.
        $this->assertSame(0, PercobaanLoginAdmin::query()->where('identitas', $user->email)->count());

        $this->postJson('/api/v1/auth/sign-in', [
            'email' => $user->email,
            'password' => $baru,
        ])->assertOk();
    }
}
