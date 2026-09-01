<?php

namespace Tests\Feature;

use App\Models\NotifikasiPemohon;
use App\Models\Pemohon;
use App\Models\PermohonanInformasi;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Waktu pemberitahuan berkas tanggapan (langkah 97).
 *
 * Petugas melampirkan dokumen jauh sebelum permohonannya diputus. Pemberitahuan
 * pada saat itu menjanjikan jawaban yang belum tentu jadi diberikan — dan
 * portal memang belum menampilkan berkasnya. Karena itu pemberitahuannya
 * menunggu sampai permohonan diserahkan.
 */
class BerkasTanggapanTest extends TestCase
{
    use DatabaseTransactions;

    private string $password = 'RahasiaKuat123';

    private string $tanda;

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

    private function permohonan(string $status): PermohonanInformasi
    {
        $pemohon = Pemohon::create([
            'nama' => "Pemohon $this->tanda",
            'email' => 'pemohon.'.Str::lower(Str::random(6)).'@uji.test',
            'password' => Hash::make($this->password),
            'jenis_pemohon' => 'pribadi',
        ]);

        $permohonan = PermohonanInformasi::create([
            'pemohon_id' => $pemohon->id,
            'rincian_informasi' => "Rincian uji $this->tanda",
        ]);

        // `status`, `kode_permohonan`, dan `tanggal_permohonan` sengaja tidak
        // `fillable` di model: statusnya hanya boleh berpindah lewat alurnya.
        // Untuk menyiapkan keadaan awal pengujian, ketiganya dipasang paksa.
        $permohonan->forceFill([
            'kode_permohonan' => 'TGP-'.Str::random(6),
            'status' => $status,
            'tanggal_permohonan' => now(),
        ])->save();

        return $permohonan;
    }

    private function jumlahNotifikasiBerkas(PermohonanInformasi $permohonan): int
    {
        return NotifikasiPemohon::where('pemohon_id', $permohonan->pemohon_id)
            ->where('type', 'permohonan_tanggapan_file')
            ->count();
    }

    private function lampirkan(string $token, PermohonanInformasi $permohonan, string $nama = 'Tanggapan.pdf'): void
    {
        $this->withToken($token)
            ->postJson("/api/v1/permohonan/{$permohonan->id}/tanggapan-files", [
                'files' => [[
                    'nama_file' => $nama,
                    'path_file' => 'uploads/permohonan/2026/08/'.Str::random(10).'.pdf',
                ]],
            ])
            ->assertCreated();
    }

    /** Inti laporannya: melampirkan berkas saat masih disiapkan tidak memberi tahu siapa pun. */
    public function test_melampirkan_saat_masih_diproses_tidak_memberi_tahu_pemohon(): void
    {
        $permohonan = $this->permohonan('diproses');
        $token = $this->token($this->akun('ppid-pelaksana'));

        $this->lampirkan($token, $permohonan);

        $this->assertSame(0, $this->jumlahNotifikasiBerkas($permohonan));
    }

    /**
     * Pemberitahuannya menyusul saat permohonannya benar-benar diserahkan,
     * dan hanya sekali — meski statusnya masih berpindah sesudahnya.
     *
     * Dijalankan lewat alur yang sebenarnya: berkas dilampirkan saat berkasnya
     * masih disiapkan, lalu kedua tahap persetujuan diputus sampai
     * permohonannya diserahkan. Itulah urutan yang membuat cacatnya terlihat —
     * pemohon dulu diberi tahu sejak lampiran pertama.
     *
     * Satu akun super admin memutus kedua tahap. Dua peran berbeda dalam satu
     * skenario tidak bisa dipakai di sini: guard `api` menahan pengguna yang
     * sudah diselesaikan pada permintaan pertama, sehingga token kedua tetap
     * dianggap milik pengguna sebelumnya. Yang diuji waktu pemberitahuannya,
     * bukan siapa yang berhak memutus — itu sudah dijaga
     * {@see NotifikasiPanelTest}.
     */
    public function test_pemberitahuan_menyusul_saat_permohonan_diserahkan(): void
    {
        $permohonan = $this->permohonan('diproses');
        $token = $this->token($this->akun('super-admin'));

        $this->lampirkan($token, $permohonan);
        $this->assertSame(0, $this->jumlahNotifikasiBerkas($permohonan));

        $this->withToken($token)
            ->postJson("/api/v1/permohonan/{$permohonan->id}/status", ['status_baru' => 'menunggu_approval'])
            ->assertOk();

        $this->assertSame(0, $this->jumlahNotifikasiBerkas($permohonan));

        // Tahap 1: jenjang penerima menetapkan jalur pelayanan.
        $this->withToken($token)
            ->postJson("/api/v1/permohonan/{$permohonan->id}/approval", [
                'keputusan' => 'disetujui',
                'jalur_pelayanan' => 'online',
                'keterangan_petugas' => 'Dokumen dikirim lewat surel.',
            ])
            ->assertOk();

        $this->assertSame(0, $this->jumlahNotifikasiBerkas($permohonan));

        // Tahap 2: putusan PPID — di sinilah permohonannya diserahkan.
        $this->withToken($token)
            ->postJson("/api/v1/permohonan/{$permohonan->id}/approval", ['keputusan' => 'disetujui'])
            ->assertOk();

        $this->assertSame('disetujui', $permohonan->fresh()->status);
        $this->assertSame(1, $this->jumlahNotifikasiBerkas($permohonan));

        // Disetujui → Selesai sama-sama terbuka bagi pemohon; berkas yang sama
        // tidak boleh diberitahukan dua kali.
        $this->withToken($token)
            ->postJson("/api/v1/permohonan/{$permohonan->id}/status", ['status_baru' => 'selesai'])
            ->assertOk();

        $this->assertSame(1, $this->jumlahNotifikasiBerkas($permohonan));
    }

    /** Yang dilampirkan setelah diserahkan tetap diberitahukan saat itu juga. */
    public function test_melampirkan_setelah_diserahkan_langsung_memberi_tahu(): void
    {
        $permohonan = $this->permohonan('selesai');
        $token = $this->token($this->akun('ppid-utama'));

        $this->lampirkan($token, $permohonan);

        $this->assertSame(1, $this->jumlahNotifikasiBerkas($permohonan));
    }

    /**
     * Permohonan yang ditolak tidak menyerahkan berkas.
     *
     * Yang disampaikan pada penolakan adalah alasannya, dan itu sudah dibawa
     * notifikasi status. Berkas yang telanjur disiapkan tidak ikut dibuka.
     */
    public function test_penolakan_tidak_memberitahukan_berkas_tanggapan(): void
    {
        $permohonan = $this->permohonan('diproses');
        $token = $this->token($this->akun('ppid-utama'));

        $this->lampirkan($token, $permohonan);

        $this->withToken($token)
            ->postJson("/api/v1/permohonan/{$permohonan->id}/status", [
                'status_baru' => 'ditolak',
                'alasan_penolakan' => 'Informasi termasuk yang dikecualikan.',
            ])
            ->assertOk();

        $this->assertSame(0, $this->jumlahNotifikasiBerkas($permohonan));
    }
}
