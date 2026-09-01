<?php

namespace Tests\Feature;

use App\Models\ApprovalPengajuan;
use App\Models\Notifikasi;
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
 * Alur permohonan dari penerimaan sampai putusan PPID (langkah 100).
 *
 * Yang diuji adalah empat cacat yang dilaporkan sekaligus, karena keempatnya
 * berasal dari satu sebab: jenjang persetujuan tidak pernah dimulai, sehingga
 * berkas berhenti di tangan PPID Pelaksana tanpa ada yang tahu.
 *
 * `DatabaseTransactions` dengan alasan yang sama seperti
 * {@see AlurLayananPpidTest}.
 */
class AlurPersetujuanPermohonanTest extends TestCase
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
        // Masuk tanpa membawa token siapa pun. Guard yang masih memegang
        // pengguna dari permintaan sebelumnya membuat sign-in ini menjawab
        // dengan token milik pengguna itu, bukan milik $user — dan seluruh
        // permintaan berikutnya lalu dinilai memakai role yang keliru.
        $this->flushHeaders();
        $this->lupakanIdentitas();

        return $this->postJson('/api/v1/auth/sign-in', [
            'email' => $user->email,
            'password' => $this->password,
        ])->assertOk()->json('access_token');
    }

    /**
     * Lupakan pengguna yang sedang dikenali, beserta token yang sudah terurai.
     *
     * `forgetGuards()` saja tidak cukup: singleton `tymon.jwt` menyimpan token
     * yang terakhir diurai, dan guard yang dibangun ulang mengambil token itu
     * lagi alih-alih membaca header permintaan berikutnya. Akibatnya seluruh
     * permintaan sesudah pergantian pemegang token dinilai memakai role yang
     * keliru — kekeliruan yang hanya muncul di dalam tes, karena server
     * sungguhan membangun ulang seluruh wadahnya tiap permintaan.
     */
    private function lupakanIdentitas(): void
    {
        $this->app['auth']->forgetGuards();
        $this->app->forgetInstance('tymon.jwt');
        $this->app->forgetInstance('tymon.jwt.auth');
        $this->app->forgetInstance('tymon.jwt.provider.jwt');
    }

    /**
     * Kirim permintaan berikutnya sebagai pemegang token ini.
     *
     * Identitas lama dilupakan lebih dulu; lihat {@see lupakanIdentitas()}.
     */
    private function sebagai(string $token): self
    {
        $this->lupakanIdentitas();

        return $this->withToken($token);
    }

    /** Permohonan baru, persis seperti yang dikirim portal pemohon. */
    private function permohonan(): PermohonanInformasi
    {
        $this->urut++;

        $pemohon = Pemohon::create([
            'nama' => "Pemohon Uji $this->tanda",
            'email' => 'pemohon'.$this->urut.'.'.Str::lower($this->tanda).'@uji.test',
            'password' => Hash::make($this->password),
            'jenis_pemohon' => 'pribadi',
        ]);

        return PermohonanInformasi::create([
            'kode_permohonan' => 'UJI-100-'.$this->urut.'-'.$this->tanda,
            'pemohon_id' => $pemohon->id,
            'rincian_informasi' => "Rincian uji $this->tanda",
            'status' => 'diajukan',
            'jalur_pelayanan' => 'online',
            'tanggal_permohonan' => now(),
            'batas_waktu_tanggapan' => SlaLayanan::batasPermohonan(),
            'batas_waktu_awal' => SlaLayanan::batasPermohonan(),
        ]);
    }

    private function langkah(PermohonanInformasi $permohonan)
    {
        return ApprovalPengajuan::where('jenis', 'permohonan')
            ->where('pengajuan_id', $permohonan->id)
            ->orderBy('id')
            ->get();
    }

    public function test_jenjang_lahir_tanpa_langkah_manual(): void
    {
        $permohonan = $this->permohonan();

        // Sebelum ada yang membukanya, belum ada jenjang sama sekali.
        $this->assertCount(0, $this->langkah($permohonan));

        $token = $this->token($this->akun('ppid-pelaksana'));

        $isi = $this->sebagai($token)
            ->getJson("/api/v1/permohonan/{$permohonan->id}/approval")
            ->assertOk()
            ->json('data');

        // Membuka rinciannya sudah cukup: berkas yang masuk selalu punya
        // jenjang, tanpa petugas harus memindahkan status lebih dulu.
        $this->assertCount(2, $isi['langkah'], 'Jenjang tidak dibuat otomatis.');
        $this->assertNotNull($isi['berjalan_id']);
        $this->assertTrue($isi['boleh_memutus'], 'Tahap pertama seharusnya giliran PPID Pelaksana.');

        // Idempoten: dibuka dua kali tidak melahirkan putaran kedua.
        $this->sebagai($token)->getJson("/api/v1/permohonan/{$permohonan->id}/approval")->assertOk();
        $this->assertCount(2, $this->langkah($permohonan));
    }

    public function test_berkas_tuntas_tidak_dibuatkan_jenjang_baru(): void
    {
        $permohonan = $this->permohonan();
        $permohonan->forceFill(['status' => 'selesai'])->save();

        $this->sebagai($this->token($this->akun('ppid-pelaksana')))
            ->getJson("/api/v1/permohonan/{$permohonan->id}/approval")
            ->assertOk();

        $this->assertCount(0, $this->langkah($permohonan), 'Berkas yang sudah ditutup tidak boleh dibuka ulang.');
    }

    public function test_penerimaan_pelaksana_meneruskan_dan_memberi_tahu_ppid(): void
    {
        $permohonan = $this->permohonan();
        $pelaksana = $this->akun('ppid-pelaksana');
        $tokenPelaksana = $this->token($pelaksana);

        $this->sebagai($tokenPelaksana)->getJson("/api/v1/permohonan/{$permohonan->id}/approval")->assertOk();

        $this->sebagai($tokenPelaksana)
            ->postJson("/api/v1/permohonan/{$permohonan->id}/approval", [
                'keputusan' => 'disetujui',
                'catatan' => 'Berkas lengkap.',
                'jalur_pelayanan' => 'online',
                'keterangan_petugas' => "Dokumen dikirim lewat surel $this->tanda.",
            ])
            ->assertOk();

        // 1. Statusnya berpindah — sebelumnya berhenti di "Diproses" dan
        //    keadaan "sedang di jenjang" tidak tercermin di mana pun.
        $permohonan->refresh();
        $this->assertSame('menunggu_approval', $permohonan->status);

        // 2. Isian jenjang penerima tersimpan dan ikut terbaca pada rincian,
        //    supaya PPID tahu apa yang sudah dijanjikan ke pemohon.
        $this->assertSame('online', $permohonan->jalur_pelayanan);
        $this->assertStringContainsString($this->tanda, (string) $permohonan->keterangan_petugas);

        // 3. PPID diberi tahu bahwa gilirannya tiba.
        $ppid = User::where('role_id', Role::where('slug', 'ppid-utama')->value('id'))
            ->where('is_active', true)
            ->firstOrFail();

        $langkahKedua = $this->langkah($permohonan)->last();

        $this->assertTrue(
            Notifikasi::where('user_id', $ppid->id)
                ->where('type', 'approval_menunggu')
                ->whereRaw("data->>'approval_id' = ?", [(string) $langkahKedua->id])
                ->exists(),
            'PPID tidak menerima pemberitahuan giliran persetujuan.'
        );
    }

    public function test_pelaksana_tidak_bisa_menggeser_status_setelah_meneruskan(): void
    {
        $permohonan = $this->permohonan();
        $pelaksana = $this->akun('ppid-pelaksana');
        $tokenPelaksana = $this->token($pelaksana);

        $this->sebagai($tokenPelaksana)->getJson("/api/v1/permohonan/{$permohonan->id}/approval")->assertOk();

        $this->sebagai($tokenPelaksana)
            ->postJson("/api/v1/permohonan/{$permohonan->id}/approval", [
                'keputusan' => 'disetujui',
                'catatan' => 'Berkas lengkap.',
                'jalur_pelayanan' => 'online',
            ])
            ->assertOk();

        // Menolak sendiri, menarik kembali, maupun menyatakan kedaluwarsa:
        // ketiganya melangkahi jenjang PPID di atasnya.
        foreach (['ditolak', 'diproses', 'kedaluwarsa'] as $tujuan) {
            $this->sebagai($tokenPelaksana)
                ->postJson("/api/v1/permohonan/{$permohonan->id}/status", [
                    'status_baru' => $tujuan,
                    'alasan_penolakan' => 'Alasan uji.',
                ])
                ->assertStatus(422);
        }

        $this->assertSame('menunggu_approval', $permohonan->refresh()->status);
    }

    public function test_revisi_mengembalikan_berkas_ke_pelaksana_dan_alurnya_berulang(): void
    {
        $permohonan = $this->permohonan();
        $pelaksana = $this->akun('ppid-pelaksana');
        $tokenPelaksana = $this->token($pelaksana);

        $this->sebagai($tokenPelaksana)->getJson("/api/v1/permohonan/{$permohonan->id}/approval")->assertOk();

        $this->sebagai($tokenPelaksana)
            ->postJson("/api/v1/permohonan/{$permohonan->id}/approval", [
                'keputusan' => 'disetujui',
                'catatan' => 'Putaran pertama.',
                'jalur_pelayanan' => 'online',
            ])
            ->assertOk();

        $tokenPpid = $this->token($this->akun('ppid-utama'));

        $this->sebagai($tokenPpid)
            ->postJson("/api/v1/permohonan/{$permohonan->id}/approval", [
                'keputusan' => 'revisi',
                'catatan' => 'Lampiran kurang jelas, mohon diperbaiki.',
            ])
            ->assertOk();

        // Berkasnya kembali ke petugas, dan putaran baru langsung terbuka —
        // bukan berhenti menunggu seseorang ingat mengajukannya lagi.
        $permohonan->refresh();
        $this->assertSame('diproses', $permohonan->status);

        $berjalan = ApprovalPengajuan::where('jenis', 'permohonan')
            ->where('pengajuan_id', $permohonan->id)
            ->where('status', 'menunggu')
            ->whereNotNull('tanggal_masuk')
            ->orderBy('urutan')
            ->first();

        $this->assertNotNull($berjalan, 'Revisi tidak membuka putaran baru.');
        $this->assertSame(1, (int) $berjalan->urutan, 'Putaran baru harus dimulai dari jenjang penerima.');
        $this->assertSame((int) $pelaksana->role_id, (int) $berjalan->role_id);

        $this->assertTrue(
            Notifikasi::where('user_id', $pelaksana->id)
                ->where('type', 'approval_menunggu')
                ->whereRaw("data->>'approval_id' = ?", [(string) $berjalan->id])
                ->exists(),
            'PPID Pelaksana tidak diberi tahu berkasnya dikembalikan.'
        );

        // Putaran kedua berjalan seperti yang pertama, sampai putusan akhir.
        $this->sebagai($tokenPelaksana)
            ->postJson("/api/v1/permohonan/{$permohonan->id}/approval", [
                'keputusan' => 'disetujui',
                'catatan' => 'Sudah diperbaiki.',
                'jalur_pelayanan' => 'online',
            ])
            ->assertOk();

        $this->assertSame('menunggu_approval', $permohonan->refresh()->status);

        $this->sebagai($tokenPpid)
            ->postJson("/api/v1/permohonan/{$permohonan->id}/approval", [
                'keputusan' => 'disetujui',
            ])
            ->assertOk();

        $this->assertSame('disetujui', $permohonan->refresh()->status);
    }

    public function test_pelaksana_tidak_diberi_pilihan_menolak(): void
    {
        $permohonan = $this->permohonan();
        $tokenPelaksana = $this->token($this->akun('ppid-pelaksana'));

        $this->sebagai($tokenPelaksana)->getJson("/api/v1/permohonan/{$permohonan->id}/approval")->assertOk();

        // Penolakan menurut UU KIP harus datang dari pejabat yang berwenang;
        // jenjang penerima tidak diberi hak itu pada alurnya.
        $this->sebagai($tokenPelaksana)
            ->postJson("/api/v1/permohonan/{$permohonan->id}/approval", [
                'keputusan' => 'ditolak',
                'catatan' => 'Coba menolak dari jenjang penerima.',
            ])
            ->assertStatus(422);

        $this->assertSame('diajukan', $permohonan->refresh()->status);
    }

    public function test_ppid_membaca_berkas_dan_keterangan_petugas(): void
    {
        $permohonan = $this->permohonan();
        $tokenPelaksana = $this->token($this->akun('ppid-pelaksana'));

        $this->sebagai($tokenPelaksana)->getJson("/api/v1/permohonan/{$permohonan->id}/approval")->assertOk();

        $this->sebagai($tokenPelaksana)
            ->postJson("/api/v1/permohonan/{$permohonan->id}/approval", [
                'keputusan' => 'disetujui',
                'catatan' => 'Berkas lengkap.',
                'jalur_pelayanan' => 'langsung',
                'jadwal_layanan' => now()->addWeekdays(2)->setTime(9, 0)->toIso8601String(),
                'keterangan_petugas' => "Silakan hadir $this->tanda.",
            ])
            ->assertOk();

        $rincian = $this->sebagai($this->token($this->akun('ppid-utama')))
            ->getJson("/api/v1/permohonan/{$permohonan->id}")
            ->assertOk()
            ->json('data');

        // Tiga isian yang menjadi dasar putusan PPID, plus wadah berkasnya.
        $this->assertSame('langsung', $rincian['jalur_pelayanan']);
        $this->assertNotNull($rincian['jadwal_layanan']);
        $this->assertStringContainsString($this->tanda, (string) $rincian['keterangan_petugas']);
        $this->assertArrayHasKey('files', $rincian);
        $this->assertArrayHasKey('tanggapan_files', $rincian);
    }
}
