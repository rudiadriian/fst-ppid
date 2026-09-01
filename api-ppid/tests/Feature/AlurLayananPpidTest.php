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
 * Alur layanan PPID: daftar gabungan, jalur pelayanan, dan tenggat (langkah 89).
 *
 * Memakai `DatabaseTransactions` dengan alasan yang sama seperti
 * {@see AuthKeamananTest}: skema `ppiddb` sebagian dibuat lewat DDL di luar
 * migration, jadi membangun ulang basis data akan menghapus yang tidak bisa
 * dikembalikan.
 */
class AlurLayananPpidTest extends TestCase
{
    use DatabaseTransactions;

    private string $password = 'RahasiaKuat123';

    private string $tanda;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tanda = Str::random(8);

        // Captcha dimatikan seperti pada AuthKeamananTest: yang diuji di sini
        // alur layanannya, bukan pengaman formulir masuknya.
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
            'email' => 'pemohon.'.Str::lower($this->tanda).'@uji.test',
            'password' => Hash::make($this->password),
            'jenis_pemohon' => 'pribadi',
        ]);
    }

    private function permohonan(Pemohon $pemohon, array $isi = []): PermohonanInformasi
    {
        return PermohonanInformasi::create(array_merge([
            'kode_permohonan' => 'UJI-'.$this->tanda,
            'pemohon_id' => $pemohon->id,
            'rincian_informasi' => "Rincian uji $this->tanda",
            'status' => 'diajukan',
            'jalur_pelayanan' => 'online',
            'tanggal_permohonan' => now(),
            'batas_waktu_tanggapan' => SlaLayanan::batasPermohonan(),
            'batas_waktu_awal' => SlaLayanan::batasPermohonan(),
        ], $isi));
    }

    public function test_daftar_gabungan_memuat_dua_kategori(): void
    {
        $pemohon = $this->pemohon();
        $permohonan = $this->permohonan($pemohon);

        KeberatanInformasi::create([
            'permohonan_id' => $permohonan->id,
            'pemohon_id' => $pemohon->id,
            'jenis_keberatan' => 'permohonan_ditolak',
            'alasan_keberatan' => "Alasan uji $this->tanda",
            'kasus_posisi' => "Kasus uji $this->tanda",
            'status' => 'diajukan',
            'jalur_pelayanan' => 'langsung',
            'tanggal_keberatan' => now(),
            'batas_waktu_tanggapan' => SlaLayanan::batasKeberatan(),
        ]);

        $token = $this->token($this->akun('ppid-pelaksana'));

        $isi = $this->withToken($token)
            ->getJson('/api/v1/pengajuan?search='.$this->tanda)
            ->assertOk()
            ->json('data');

        $jenis = array_column($isi, 'jenis');

        $this->assertContains('permohonan', $jenis, 'Permohonan tidak muncul di daftar gabungan.');
        $this->assertContains('keberatan', $jenis, 'Keberatan tidak muncul di daftar gabungan.');

        // Kunci barisnya harus unik lintas tabel; dua baris berid sama dari
        // tabel berbeda akan saling menimpa di daftar kalau tidak.
        $id = array_column($isi, 'id');
        $this->assertSame(count($id), count(array_unique($id)), 'Ada id baris yang bertabrakan.');
    }

    public function test_filter_kategori_memisahkan_daftarnya(): void
    {
        $pemohon = $this->pemohon();
        $permohonan = $this->permohonan($pemohon);

        KeberatanInformasi::create([
            'permohonan_id' => $permohonan->id,
            'pemohon_id' => $pemohon->id,
            'jenis_keberatan' => 'permohonan_ditolak',
            'alasan_keberatan' => "Alasan uji $this->tanda",
            'kasus_posisi' => "Kasus uji $this->tanda",
            'status' => 'diajukan',
            'tanggal_keberatan' => now(),
        ]);

        $token = $this->token($this->akun('ppid-pelaksana'));

        $hanyaKeberatan = $this->withToken($token)
            ->getJson('/api/v1/pengajuan?jenis=keberatan&search='.$this->tanda)
            ->assertOk()
            ->json('data');

        $this->assertNotEmpty($hanyaKeberatan);
        $this->assertSame(['keberatan'], array_values(array_unique(array_column($hanyaKeberatan, 'jenis'))));
    }

    public function test_perpanjangan_hanya_sekali(): void
    {
        $pemohon = $this->pemohon();
        $permohonan = $this->permohonan($pemohon, ['status' => 'diproses']);

        $token = $this->token($this->akun('ppid-utama'));
        $batasLama = $permohonan->batas_waktu_tanggapan;

        $this->withToken($token)
            ->postJson("/api/v1/permohonan/{$permohonan->id}/perpanjang", ['alasan' => 'Berkas perlu ditelusuri ke unit kerja.'])
            ->assertOk();

        $permohonan->refresh();

        $this->assertTrue($permohonan->batas_waktu_tanggapan->gt($batasLama), 'Tenggat tidak bergeser.');
        // Tenggat awal harus tersimpan, kalau tidak keterlambatan bisa dihapus
        // dengan cara menggeser tenggatnya.
        $this->assertNotNull($permohonan->batas_waktu_awal);
        $this->assertTrue($permohonan->batas_waktu_awal->eq($batasLama));

        $this->withToken($token)
            ->postJson("/api/v1/permohonan/{$permohonan->id}/perpanjang", ['alasan' => 'Sekali lagi.'])
            ->assertStatus(422);
    }

    public function test_perpanjangan_wajib_beralasan(): void
    {
        $permohonan = $this->permohonan($this->pemohon(), ['status' => 'diproses']);
        $token = $this->token($this->akun('ppid-utama'));

        $this->withToken($token)
            ->postJson("/api/v1/permohonan/{$permohonan->id}/perpanjang", [])
            ->assertStatus(422);
    }

    public function test_sla_permohonan_sepuluh_hari_kerja(): void
    {
        $mulai = now();
        $batas = SlaLayanan::batasPermohonan($mulai);

        // Sepuluh hari kerja selalu lebih jauh dari sepuluh hari kalender,
        // karena akhir pekannya dilewati.
        $this->assertTrue($batas->gt($mulai->copy()->addDays(10)));
        $this->assertSame(30, SlaLayanan::KEBERATAN_HARI);
        $this->assertSame(7, SlaLayanan::PERPANJANGAN_HARI_KERJA);
        $this->assertSame(14, SlaLayanan::SENGKETA_HARI_KERJA);
    }

    /**
     * Berkas tanggapan dilampirkan dari dialog rincian permohonan (langkah 94).
     *
     * Yang melampirkannya PPID Pelaksana: dialah jenjang penerima yang
     * menyetujui permohonan jalur Online, dan unggahannya bagian dari putusan
     * itu — bukan langkah terpisah milik atasannya.
     */
    public function test_ppid_pelaksana_bisa_melampirkan_berkas_tanggapan(): void
    {
        $pemohon = $this->pemohon();
        $permohonan = $this->permohonan($pemohon, ['status' => 'diproses']);
        $token = $this->token($this->akun('ppid-pelaksana'));

        $this->withToken($token)
            ->postJson("/api/v1/permohonan/{$permohonan->id}/tanggapan-files", [
                'files' => [
                    ['nama_file' => 'Tanggapan.pdf', 'path_file' => 'uploads/permohonan/2026/08/tanggapan.pdf'],
                ],
            ])
            ->assertCreated();

        $this->assertSame(
            1,
            \App\Models\PermohonanTanggapanFile::where('permohonan_id', $permohonan->id)->count()
        );

        /*
         * Pemohon belum diberi tahu: permohonannya masih `diproses`, jadi
         * berkasnya belum diserahkan dan belum tampil di portal. Waktu
         * pemberitahuannya diuji terpisah di {@see BerkasTanggapanTest}
         * (langkah 97).
         */
        $this->assertSame(
            0,
            \App\Models\NotifikasiPemohon::where('pemohon_id', $pemohon->id)
                ->where('type', 'permohonan_tanggapan_file')
                ->count()
        );
    }

    public function test_keadaan_tenggat_menandai_yang_lewat(): void
    {
        $permohonan = $this->permohonan($this->pemohon(), [
            'status' => 'diproses',
            'batas_waktu_tanggapan' => now()->subDays(2),
        ]);

        $keadaan = SlaLayanan::keadaan($permohonan);

        $this->assertSame('lewat_tenggat', $keadaan['keadaan']);
        $this->assertGreaterThan(0, $keadaan['terlambat_hari']);
    }
}
