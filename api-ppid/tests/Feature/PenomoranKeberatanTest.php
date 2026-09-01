<?php

namespace Tests\Feature;

use App\Models\KeberatanInformasi;
use App\Models\Pemohon;
use App\Models\PermohonanInformasi;
use App\Models\Role;
use App\Models\User;
use App\Support\SlaLayanan;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Penomoran keberatan, alasan Pasal 35, dan tenggat sengketa (langkah 89).
 *
 * `DatabaseTransactions` dengan alasan yang sama seperti
 * {@see AlurLayananPpidTest}: sebagian skema `ppiddb` dibuat lewat DDL di luar
 * migration, jadi membangun ulang basis data akan menghapus yang tidak bisa
 * dikembalikan.
 */
class PenomoranKeberatanTest extends TestCase
{
    use DatabaseTransactions;

    private string $password = 'RahasiaKuat123';

    private string $tanda;

    /** Pembeda antar-baris dalam satu tes; email dan kode permohonan sama-sama unik. */
    private int $urut = 0;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tanda = Str::random(8);

        config([
            'ppid.akun.captcha_aktif' => false,
            'ppid.akun.gagal_per_tahap' => 99,
        ]);

        Mail::fake();
    }

    private function akun(string $roleSlug): User
    {
        return User::create([
            'name' => "Uji $roleSlug $this->tanda",
            'email' => Str::lower($roleSlug).'.'.Str::lower($this->tanda).'@uji.test',
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

    private function pemohon(): Pemohon
    {
        return Pemohon::create([
            'nama' => "Pemohon Uji $this->tanda",
            'email' => 'pemohon'.(++$this->urut).'.'.Str::lower($this->tanda).'@uji.test',
            'password' => Hash::make($this->password),
            'jenis_pemohon' => 'pribadi',
        ]);
    }

    private function permohonan(Pemohon $pemohon): PermohonanInformasi
    {
        return PermohonanInformasi::create([
            'kode_permohonan' => 'UJI-'.$this->urut.'-'.$this->tanda,
            'pemohon_id' => $pemohon->id,
            'rincian_informasi' => "Rincian uji $this->tanda",
            'status' => 'selesai',
            'jalur_pelayanan' => 'online',
            'tanggal_permohonan' => now(),
            'batas_waktu_tanggapan' => SlaLayanan::batasPermohonan(),
            'batas_waktu_awal' => SlaLayanan::batasPermohonan(),
        ]);
    }

    private function keberatan(array $isi = []): KeberatanInformasi
    {
        $pemohon = $this->pemohon();
        $permohonan = $this->permohonan($pemohon);

        return KeberatanInformasi::create(array_merge([
            'permohonan_id' => $permohonan->id,
            'pemohon_id' => $pemohon->id,
            'jenis_keberatan' => 'permohonan_ditolak',
            'alasan_keberatan' => "Alasan uji $this->tanda",
            'kasus_posisi' => "Kasus uji $this->tanda",
            'status' => 'diajukan',
            'jalur_pelayanan' => 'online',
            'tanggal_keberatan' => now(),
            'batas_waktu_tanggapan' => SlaLayanan::batasKeberatan(),
        ], $isi));
    }

    public function test_keberatan_lahir_dengan_nomor_sendiri(): void
    {
        $keberatan = $this->keberatan()->refresh();

        $this->assertNotNull($keberatan->kode_keberatan, 'Keberatan tersimpan tanpa nomor registrasi.');
        $this->assertStringStartsWith('KBT-FSTJ/', $keberatan->kode_keberatan);

        // Awalannya harus beda dari permohonan; itu seluruh alasan kolom ini
        // ada. Nomor yang sama untuk dua berkas berarti arsipnya tidak bisa
        // dipisahkan lagi.
        $this->assertNotSame($keberatan->permohonan->kode_permohonan, $keberatan->kode_keberatan);
        $this->assertStringStartsNotWith('KBT-FSTJ/', (string) $keberatan->permohonan->kode_permohonan);
    }

    public function test_nomor_keberatan_berderet_dan_unik(): void
    {
        $pertama = $this->keberatan()->refresh();
        $kedua = $this->keberatan()->refresh();

        $this->assertNotSame($pertama->kode_keberatan, $kedua->kode_keberatan);
    }

    public function test_daftar_gabungan_memakai_nomor_masing_masing(): void
    {
        $keberatan = $this->keberatan()->refresh();
        $token = $this->token($this->akun('ppid-pelaksana'));

        $baris = collect(
            $this->withToken($token)
                ->getJson('/api/v1/pengajuan?jenis=keberatan&search='.$this->tanda)
                ->assertOk()
                ->json('data')
        )->firstWhere('ref_id', $keberatan->id);

        $this->assertNotNull($baris, 'Keberatan tidak muncul di daftar gabungan.');
        $this->assertSame($keberatan->kode_keberatan, $baris['kode']);
    }

    public function test_nomor_keberatan_bisa_dicari(): void
    {
        $keberatan = $this->keberatan()->refresh();
        $token = $this->token($this->akun('ppid-pelaksana'));

        $baris = $this->withToken($token)
            ->getJson('/api/v1/pengajuan?search='.urlencode($keberatan->kode_keberatan))
            ->assertOk()
            ->json('data');

        $this->assertSame([$keberatan->id], array_column($baris, 'ref_id'));
    }

    public function test_alasan_ketujuh_diterima(): void
    {
        // Sebelum langkah ini, CHECK constraint tabelnya hanya mengenal enam
        // dasar; keberatan atas informasi yang diberikan sebagian terpaksa
        // dititipkan ke alasan lain.
        $keberatan = $this->keberatan(['jenis_keberatan' => 'permintaan_tidak_dipenuhi'])->refresh();

        $this->assertSame('permintaan_tidak_dipenuhi', $keberatan->jenis_keberatan);
        $this->assertArrayHasKey('permintaan_tidak_dipenuhi', KeberatanInformasi::JENIS);
        $this->assertCount(7, KeberatanInformasi::JENIS);
    }

    public function test_batas_sengketa_terisi_saat_keberatan_ditanggapi(): void
    {
        $keberatan = $this->keberatan(['status' => 'diproses']);
        $token = $this->token($this->akun('ppid-utama'));

        $this->withToken($token)
            ->postJson("/api/v1/keberatan/{$keberatan->id}/tanggapan", [
                'status' => 'ditolak',
                'tanggapan_atasan_ppid' => "Tanggapan uji $this->tanda",
            ])
            ->assertOk();

        $keberatan->refresh();

        $this->assertNotNull($keberatan->tanggal_tanggapan);
        $this->assertNotNull(
            $keberatan->batas_waktu_sengketa,
            'Batas pengajuan sengketa tidak terisi saat keberatan ditanggapi.'
        );
        $this->assertSame(
            SlaLayanan::batasSengketa($keberatan->tanggal_tanggapan)->toDateString(),
            $keberatan->batas_waktu_sengketa->toDateString()
        );
    }

    public function test_analitik_memuat_tujuh_alasan_keberatan(): void
    {
        $this->keberatan(['jenis_keberatan' => 'biaya_tidak_wajar']);
        $token = $this->token($this->akun('super-admin'));

        $alasan = $this->withToken($token)
            ->getJson('/api/v1/dashboard/analitik')
            ->assertOk()
            ->json('data.analisa.alasan_keberatan');

        $this->assertCount(7, $alasan, 'Sebaran alasan tidak memuat seluruh dasar Pasal 35.');
        $resmi = array_keys(KeberatanInformasi::JENIS);
        $dikirim = collect($alasan)->pluck('kode')->all();
        sort($resmi);
        sort($dikirim);

        $this->assertSame($resmi, $dikirim, 'Kode alasan pada analitik tidak sama dengan daftar resminya.');
    }
}
