<?php

namespace Tests\Feature;

use App\Models\Pemohon;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Pagar kelengkapan data pada Verifikasi Data Diri Pemohon (langkah 90).
 *
 * Dua sisi dari satu perkara: berkas yang datanya belum lengkap tidak boleh
 * disetujui, dan persetujuan yang telanjur diberikan atas data seperti itu
 * harus masih bisa dicabut. Tanpa yang kedua, satu salah klik mengunci berkas
 * itu selamanya — pemohon tidak bisa memperbaiki (isiannya terkunci selama
 * berstatus terverifikasi) dan petugas tidak bisa membatalkan.
 *
 * `DatabaseTransactions` dengan alasan yang sama seperti
 * {@see PenomoranKeberatanTest}: sebagian skema `ppiddb` dibuat lewat DDL di
 * luar migration.
 */
class VerifikasiDataPemohonTest extends TestCase
{
    use DatabaseTransactions;

    private string $password = 'RahasiaKuat123';

    private string $tanda;

    private int $urut = 0;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tanda = Str::random(8);

        config([
            'ppid.akun.recaptcha_aktif' => false,
            'ppid.akun.gagal_per_tahap' => 99,
        ]);

        Mail::fake();
    }

    private function token(string $roleSlug = 'ppid-utama'): string
    {
        $user = User::create([
            'name' => "Uji $roleSlug $this->tanda",
            'email' => Str::lower($roleSlug).'.'.Str::lower($this->tanda).'@uji.test',
            'password' => Hash::make($this->password),
            'role_id' => Role::where('slug', $roleSlug)->value('id'),
            'is_active' => true,
        ]);

        return $this->postJson('/api/v1/auth/sign-in', [
            'email' => $user->email,
            'password' => $this->password,
        ])->assertOk()->json('access_token');
    }

    /** @param array<string, mixed> $isi */
    private function pemohon(array $isi = []): Pemohon
    {
        $pemohon = new Pemohon;

        $pemohon->forceFill(array_merge([
            'nama' => "Pemohon Uji $this->tanda",
            'email' => 'pemohon'.(++$this->urut).'.'.Str::lower($this->tanda).'@uji.test',
            'password' => Hash::make($this->password),
            'nik' => '3171000000000001',
            'jenis_pemohon' => 'perorangan',
            'pekerjaan' => 'Karyawan',
            'alamat' => "Alamat uji $this->tanda",
            'file_ktp' => 'uploads/ktp/uji-'.$this->tanda.'.jpg',
            'status_verifikasi' => 'menunggu',
            'jumlah_ditolak' => 0,
        ], $isi))->save();

        return $pemohon->refresh();
    }

    public function test_data_lengkap_bisa_disetujui(): void
    {
        $pemohon = $this->pemohon();

        $this->withToken($this->token())
            ->postJson("/api/v1/pemohon/{$pemohon->id}/verifikasi", ['status' => 'terverifikasi'])
            ->assertOk();

        $this->assertSame('terverifikasi', $pemohon->refresh()->status_verifikasi);
    }

    public function test_tanpa_berkas_ktp_tidak_bisa_disetujui(): void
    {
        $pemohon = $this->pemohon(['file_ktp' => null]);

        $this->withToken($this->token())
            ->postJson("/api/v1/pemohon/{$pemohon->id}/verifikasi", ['status' => 'terverifikasi'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('status');

        $this->assertSame('menunggu', $pemohon->refresh()->status_verifikasi);
    }

    public function test_data_diri_kosong_tidak_bisa_disetujui(): void
    {
        $pemohon = $this->pemohon(['nik' => null, 'alamat' => null, 'pekerjaan' => null]);

        $pesan = $this->withToken($this->token())
            ->postJson("/api/v1/pemohon/{$pemohon->id}/verifikasi", ['status' => 'terverifikasi'])
            ->assertStatus(422)
            ->json('errors.status.0');

        // Petugas harus tahu apa yang kurang; pesan "belum lengkap" saja
        // memaksanya menebak isian mana yang dimaksud.
        $this->assertStringContainsString('NIK', $pesan);
        $this->assertStringContainsString('Alamat', $pesan);
        $this->assertStringContainsString('Pekerjaan', $pesan);
    }

    /** NIK 12 digit bukan "sudah diisi" — tidak ada KTP yang cocok dengannya. */
    public function test_nik_kurang_dari_enam_belas_digit_ditolak(): void
    {
        $pemohon = $this->pemohon(['nik' => '123123123123']);

        $this->withToken($this->token())
            ->postJson("/api/v1/pemohon/{$pemohon->id}/verifikasi", ['status' => 'terverifikasi'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('status');
    }

    /** Perorangan tidak mewakili siapa pun, jadi nama lembaga tidak dituntut. */
    public function test_perorangan_tidak_dituntut_nama_lembaga(): void
    {
        $pemohon = $this->pemohon(['jenis_pemohon' => 'perorangan', 'nama_lembaga' => null]);

        $this->withToken($this->token())
            ->postJson("/api/v1/pemohon/{$pemohon->id}/verifikasi", ['status' => 'terverifikasi'])
            ->assertOk();
    }

    public function test_lembaga_tanpa_nama_lembaga_tidak_bisa_disetujui(): void
    {
        $pemohon = $this->pemohon(['jenis_pemohon' => 'lembaga', 'nama_lembaga' => null]);

        $this->withToken($this->token())
            ->postJson("/api/v1/pemohon/{$pemohon->id}/verifikasi", ['status' => 'terverifikasi'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('status');
    }

    public function test_persetujuan_atas_data_tak_lengkap_masih_bisa_dicabut(): void
    {
        // Baris seperti ini lahir dari persetujuan sebelum pagar di atas ada.
        $pemohon = $this->pemohon(['file_ktp' => null, 'status_verifikasi' => 'terverifikasi']);

        $this->withToken($this->token())
            ->postJson("/api/v1/pemohon/{$pemohon->id}/verifikasi", [
                'status' => 'ditolak',
                'catatan' => 'Berkas KTP belum diunggah.',
            ])
            ->assertOk();

        $pemohon->refresh();

        $this->assertSame('ditolak', $pemohon->status_verifikasi);
        // Pencabutan bukan penolakan atas berkas yang diperiksa, jadi jatah
        // kirim ulang pemohon tidak boleh ikut termakan.
        $this->assertSame(0, (int) $pemohon->jumlah_ditolak);
    }

    public function test_persetujuan_atas_data_lengkap_tetap_terkunci(): void
    {
        $pemohon = $this->pemohon(['status_verifikasi' => 'terverifikasi']);

        $this->withToken($this->token())
            ->postJson("/api/v1/pemohon/{$pemohon->id}/verifikasi", [
                'status' => 'ditolak',
                'catatan' => 'Berubah pikiran.',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('status');

        $this->assertSame('terverifikasi', $pemohon->refresh()->status_verifikasi);
    }

    /**
     * Pemohon yang sudah habis jatah penolakannya tetap boleh dicabut
     * persetujuannya: pagar itu menghitung berkas yang ditolak setelah
     * diperiksa, dan pencabutan tidak menambah hitungan itu.
     */
    public function test_pencabutan_tetap_bisa_walau_jatah_penolakan_habis(): void
    {
        $pemohon = $this->pemohon([
            'file_ktp' => null,
            'status_verifikasi' => 'terverifikasi',
            'jumlah_ditolak' => Pemohon::BATAS_DITOLAK,
        ]);

        $this->withToken($this->token())
            ->postJson("/api/v1/pemohon/{$pemohon->id}/verifikasi", [
                'status' => 'ditolak',
                'catatan' => 'Berkas KTP belum diunggah.',
            ])
            ->assertOk();

        $this->assertSame('ditolak', $pemohon->refresh()->status_verifikasi);
    }

    /** Panel membaca kekurangannya dari sini, bukan menghitung ulang sendiri. */
    public function test_detail_menyebut_kekurangan_datanya(): void
    {
        $pemohon = $this->pemohon(['file_ktp' => null]);

        $data = $this->withToken($this->token())
            ->getJson("/api/v1/pemohon/{$pemohon->id}")
            ->assertOk()
            ->json('data');

        $this->assertFalse($data['data_lengkap']);
        $this->assertContains('Berkas KTP', $data['kekurangan_data']);
    }
}
