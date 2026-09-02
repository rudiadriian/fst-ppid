<?php

namespace Tests\Feature;

use App\Mail\StatusLayananMail;
use App\Models\ApprovalPengajuan;
use App\Models\Notifikasi;
use App\Models\NotifikasiPemohon;
use App\Models\Pemohon;
use App\Models\PermohonanInformasi;
use App\Models\PermohonanTanggapanFile;
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

        // 1. Statusnya berpindah ke Diproses: PPID Pelaksana sudah
        //    menyetujui berkasnya, dan yang tersisa tinggal putusan PPID.
        $permohonan->refresh();
        $this->assertSame('diproses', $permohonan->status);

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
        foreach (['ditolak', 'revisi', 'kedaluwarsa'] as $tujuan) {
            $this->sebagai($tokenPelaksana)
                ->postJson("/api/v1/permohonan/{$permohonan->id}/status", [
                    'status_baru' => $tujuan,
                    'alasan_penolakan' => 'Alasan uji.',
                ])
                ->assertStatus(422);
        }

        $this->assertSame('diproses', $permohonan->refresh()->status);
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
        $this->assertSame('revisi', $permohonan->status);

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

        $this->assertSame('diproses', $permohonan->refresh()->status);

        $this->sebagai($tokenPpid)
            ->postJson("/api/v1/permohonan/{$permohonan->id}/approval", [
                'keputusan' => 'disetujui',
            ])
            ->assertOk();

        $this->assertSame('selesai', $permohonan->refresh()->status);
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

    public function test_berkas_berstatus_diproses_langsung_jadi_giliran_ppid(): void
    {
        // Diproses berarti PPID Pelaksana sudah menyetujui berkasnya; yang
        // tersisa tinggal putusan PPID. Jenjangnya karena itu tidak boleh
        // dibuka di tahap penerima — tahap itu sudah dilalui.
        $permohonan = $this->permohonan();
        $permohonan->forceFill(['status' => 'diproses'])->save();

        $ppid = $this->akun('ppid-utama');

        $isi = $this->sebagai($this->token($ppid))
            ->getJson("/api/v1/permohonan/{$permohonan->id}/approval")
            ->assertOk()
            ->json('data');

        $this->assertCount(2, $isi['langkah']);
        $this->assertSame('disetujui', $isi['langkah'][0]['status'], 'Tahap penerima harus sudah terlewat.');
        $this->assertSame('menunggu', $isi['langkah'][1]['status']);
        $this->assertSame($isi['langkah'][1]['id'], $isi['berjalan_id']);
        $this->assertTrue($isi['boleh_memutus'], 'PPID tidak kebagian giliran atas berkas yang sudah Diproses.');

        // Loncengnya jatuh ke PPID, bukan ke PPID Pelaksana.
        $this->assertTrue(
            Notifikasi::where('user_id', $ppid->id)
                ->where('type', 'approval_menunggu')
                ->whereRaw("data->>'approval_id' = ?", [(string) $isi['berjalan_id']])
                ->exists(),
            'PPID tidak diberi tahu berkasnya menunggu putusannya.'
        );

        // Ketiga pilihan PPID benar-benar terbuka, termasuk Tolak yang tidak
        // diberikan kepada jenjang penerima.
        $this->assertTrue($isi['langkah'][1]['tahap']['boleh_tolak']);

        $this->sebagai($this->token($ppid))
            ->postJson("/api/v1/permohonan/{$permohonan->id}/approval", ['keputusan' => 'disetujui'])
            ->assertOk();

        $this->assertSame('selesai', $permohonan->refresh()->status);
    }

    public function test_jenjang_lama_yang_tertahan_di_penerima_ikut_dimajukan(): void
    {
        // Berkas yang jenjangnya dibuat sebelum aturan ini berlaku menahan
        // giliran di tahap penerima walaupun statusnya sudah Diproses. Dibuka
        // sekali, jenjangnya menyusul sendiri — tidak ada baris yang perlu
        // disunting tangan.
        $permohonan = $this->permohonan();

        $this->sebagai($this->token($this->akun('ppid-pelaksana')))
            ->getJson("/api/v1/permohonan/{$permohonan->id}/approval")
            ->assertOk();

        $langkah = $this->langkah($permohonan);
        $this->assertSame('menunggu', $langkah[0]->status, 'Prasyarat: tahap penerima yang berjalan.');

        // Status dipasang langsung, meniru berkas yang ditangani sebelum jenjang
        // berjalan dipakai.
        $permohonan->forceFill(['status' => 'diproses'])->save();

        $ppid = $this->akun('ppid-utama');

        $isi = $this->sebagai($this->token($ppid))
            ->getJson("/api/v1/permohonan/{$permohonan->id}/approval")
            ->assertOk()
            ->json('data');

        $this->assertSame('disetujui', $isi['langkah'][0]['status'], 'Tahap penerima tidak ikut dimajukan.');
        $this->assertSame($isi['langkah'][1]['id'], $isi['berjalan_id']);
        $this->assertTrue($isi['boleh_memutus'], 'PPID masih tidak kebagian giliran.');

        // Tidak berlipat: dibuka lagi, jenjangnya tetap dua langkah.
        $this->sebagai($this->token($ppid))
            ->getJson("/api/v1/permohonan/{$permohonan->id}/approval")
            ->assertOk();

        $this->assertCount(2, $this->langkah($permohonan));

        // Lonceng tahap penerima ikut hilang: gilirannya sudah lewat.
        $this->assertFalse(
            Notifikasi::where('type', 'approval_menunggu')
                ->whereRaw("data->>'approval_id' = ?", [(string) $langkah[0]->id])
                ->exists(),
            'Lonceng tahap yang sudah lewat masih menggantung.'
        );
    }

    public function test_pelaksana_tidak_bisa_mengubah_berkas_setelah_meneruskan(): void
    {
        $permohonan = $this->permohonan();
        $tokenPelaksana = $this->token($this->akun('ppid-pelaksana'));

        $this->sebagai($tokenPelaksana)->getJson("/api/v1/permohonan/{$permohonan->id}/approval")->assertOk();

        // Selama masih gilirannya, lampiran memang boleh disusun.
        $this->sebagai($tokenPelaksana)
            ->postJson("/api/v1/permohonan/{$permohonan->id}/tanggapan-files", [
                'files' => [['nama_file' => "Jawaban $this->tanda.pdf", 'path_file' => "uji/{$this->tanda}.pdf"]],
            ])
            ->assertStatus(201);

        $berkas = PermohonanTanggapanFile::where('permohonan_id', $permohonan->id)->firstOrFail();

        $this->sebagai($tokenPelaksana)
            ->postJson("/api/v1/permohonan/{$permohonan->id}/approval", [
                'keputusan' => 'disetujui',
                'jalur_pelayanan' => 'online',
            ])
            ->assertOk();

        // Diteruskan: berkas yang sedang dipertimbangkan PPID tidak boleh
        // berubah isi di tengah pertimbangannya.
        $this->sebagai($tokenPelaksana)
            ->postJson("/api/v1/permohonan/{$permohonan->id}/tanggapan-files", [
                'files' => [['nama_file' => 'Susulan.pdf', 'path_file' => "uji/{$this->tanda}-2.pdf"]],
            ])
            ->assertStatus(422);

        $this->sebagai($tokenPelaksana)
            ->deleteJson("/api/v1/permohonan/{$permohonan->id}/tanggapan-files/{$berkas->id}")
            ->assertStatus(422);

        $this->assertSame(1, PermohonanTanggapanFile::where('permohonan_id', $permohonan->id)->count());

        // Dikembalikan untuk diperbaiki: gilirannya balik, lampirannya terbuka.
        $this->sebagai($this->token($this->akun('ppid-utama')))
            ->postJson("/api/v1/permohonan/{$permohonan->id}/approval", [
                'keputusan' => 'revisi',
                'catatan' => 'Ganti lampirannya.',
            ])
            ->assertOk();

        $this->sebagai($tokenPelaksana)
            ->deleteJson("/api/v1/permohonan/{$permohonan->id}/tanggapan-files/{$berkas->id}")
            ->assertOk();
    }

    public function test_rincian_membawa_berkas_tanggapan_dan_riwayat_status(): void
    {
        // Kunci JSON-nya `tanggapan_files` dan `log_status` — Eloquent
        // meng-snake_case-kan nama relasi saat menyusun jawabannya. Panel
        // sempat membaca `tanggapanFiles` dan `logStatus`, sehingga berkas yang
        // sudah terunggah tidak pernah tampil di rincian.
        $permohonan = $this->permohonan();
        $tokenPelaksana = $this->token($this->akun('ppid-pelaksana'));

        $this->sebagai($tokenPelaksana)
            ->postJson("/api/v1/permohonan/{$permohonan->id}/tanggapan-files", [
                'files' => [['nama_file' => "Jawaban $this->tanda.pdf", 'path_file' => "uji/{$this->tanda}.pdf"]],
            ])
            ->assertStatus(201);

        $rincian = $this->sebagai($tokenPelaksana)
            ->getJson("/api/v1/permohonan/{$permohonan->id}")
            ->assertOk()
            ->json('data');

        $this->assertArrayHasKey('tanggapan_files', $rincian);
        $this->assertArrayNotHasKey('tanggapanFiles', $rincian, 'Kunci camelCase tidak pernah dikirim API.');
        $this->assertCount(1, $rincian['tanggapan_files']);
        $this->assertStringContainsString($this->tanda, $rincian['tanggapan_files'][0]['nama_file']);

        $this->assertArrayHasKey('log_status', $rincian);
        $this->assertArrayNotHasKey('logStatus', $rincian);
    }

    public function test_persetujuan_ppid_menutup_dan_mengundang_jalur_langsung(): void
    {
        $permohonan = $this->permohonan();
        $tokenPelaksana = $this->token($this->akun('ppid-pelaksana'));

        $this->sebagai($tokenPelaksana)->getJson("/api/v1/permohonan/{$permohonan->id}/approval")->assertOk();

        $jadwal = now()->addWeekdays(3)->setTime(9, 0);

        $this->sebagai($tokenPelaksana)
            ->postJson("/api/v1/permohonan/{$permohonan->id}/approval", [
                'keputusan' => 'disetujui',
                'jalur_pelayanan' => 'langsung',
                'jadwal_layanan' => $jadwal->toIso8601String(),
                'keterangan_petugas' => "Bawa identitas $this->tanda.",
            ])
            ->assertOk();

        // Undangan belum berangkat: berkasnya baru diteruskan, belum diputus.
        // Menjanjikan pertemuan atas permohonan yang masih bisa ditolak PPID
        // adalah janji yang belum tentu bisa ditepati.
        Mail::assertNothingSent();

        $this->sebagai($this->token($this->akun('ppid-utama')))
            ->postJson("/api/v1/permohonan/{$permohonan->id}/approval", ['keputusan' => 'disetujui'])
            ->assertOk();

        // Putusan PPID menutup perkaranya sekaligus mendistribusikan hasilnya.
        $permohonan->refresh();
        $this->assertSame('selesai', $permohonan->status);

        Mail::assertSent(
            StatusLayananMail::class,
            fn (StatusLayananMail $surat) => str_contains($surat->envelope()->subject, 'Undangan')
        );

        $notifikasi = NotifikasiPemohon::where('pemohon_id', $permohonan->pemohon_id)
            ->where('type', 'permohonan_status')
            ->latest('id')
            ->first();

        $this->assertNotNull($notifikasi, 'Pemohon tidak diberi tahu permohonannya selesai.');
        $this->assertSame('selesai', $notifikasi->data['status'] ?? null);
    }

    public function test_revisi_tidak_pernah_terlihat_pemohon(): void
    {
        $permohonan = $this->permohonan();
        $tokenPelaksana = $this->token($this->akun('ppid-pelaksana'));

        $this->sebagai($tokenPelaksana)->getJson("/api/v1/permohonan/{$permohonan->id}/approval")->assertOk();

        $this->sebagai($tokenPelaksana)
            ->postJson("/api/v1/permohonan/{$permohonan->id}/approval", [
                'keputusan' => 'disetujui',
                'jalur_pelayanan' => 'online',
            ])
            ->assertOk();

        $sebelum = NotifikasiPemohon::where('pemohon_id', $permohonan->pemohon_id)->count();

        $this->sebagai($this->token($this->akun('ppid-utama')))
            ->postJson("/api/v1/permohonan/{$permohonan->id}/approval", [
                'keputusan' => 'revisi',
                'catatan' => 'Lampirannya kurang jelas.',
            ])
            ->assertOk();

        // Statusnya berpindah di panel, tetapi pemohon tidak dikabari: yang
        // diperbaiki pekerjaan petugas, bukan berkas miliknya.
        $this->assertSame('revisi', $permohonan->refresh()->status);
        $this->assertSame(
            $sebelum,
            NotifikasiPemohon::where('pemohon_id', $permohonan->pemohon_id)->count(),
            'Putaran perbaikan internal bocor ke lonceng pemohon.'
        );
    }

    public function test_pemegang_giliran_masih_bisa_menandai_diverifikasi(): void
    {
        // Berkas baru masuk dan tahap pertamanya milik PPID Pelaksana. Sejak
        // seluruh perpindahan dikunci, ia tidak bisa menandainya Diverifikasi —
        // padahal itu tahap kerjanya sendiri, sebelum berkasnya diteruskan.
        $permohonan = $this->permohonan();
        $tokenPelaksana = $this->token($this->akun('ppid-pelaksana'));

        $this->sebagai($tokenPelaksana)->getJson("/api/v1/permohonan/{$permohonan->id}/approval")->assertOk();

        $this->sebagai($tokenPelaksana)
            ->postJson("/api/v1/permohonan/{$permohonan->id}/status", ['status_baru' => 'diverifikasi'])
            ->assertOk();

        $this->assertSame('diverifikasi', $permohonan->refresh()->status);

        // Yang memindahkan berkasnya ke meja lain atau menutup perkaranya tetap
        // tertutup, sekalipun bagi pemegang gilirannya.
        foreach (['diproses', 'ditolak', 'kedaluwarsa'] as $tujuan) {
            $this->sebagai($tokenPelaksana)
                ->postJson("/api/v1/permohonan/{$permohonan->id}/status", [
                    'status_baru' => $tujuan,
                    'alasan_penolakan' => 'Alasan uji.',
                ])
                ->assertStatus(422);
        }

        // Bukan gilirannya: seluruh perpindahan tertutup, termasuk Diverifikasi.
        $this->sebagai($this->token($this->akun('ppid-utama')))
            ->postJson("/api/v1/permohonan/{$permohonan->id}/status", ['status_baru' => 'diverifikasi'])
            ->assertStatus(422);

        $this->assertSame('diverifikasi', $permohonan->refresh()->status);

        // Alurnya tetap berjalan sesudahnya: berkasnya diteruskan lewat putusan.
        $this->sebagai($tokenPelaksana)
            ->postJson("/api/v1/permohonan/{$permohonan->id}/approval", [
                'keputusan' => 'disetujui',
                'jalur_pelayanan' => 'online',
            ])
            ->assertOk();

        $this->assertSame('diproses', $permohonan->refresh()->status);
    }

    public function test_putaran_lama_dipisahkan_dari_putaran_berjalan(): void
    {
        $permohonan = $this->permohonan();
        $tokenPelaksana = $this->token($this->akun('ppid-pelaksana'));

        $this->sebagai($tokenPelaksana)->getJson("/api/v1/permohonan/{$permohonan->id}/approval")->assertOk();

        $this->sebagai($tokenPelaksana)
            ->postJson("/api/v1/permohonan/{$permohonan->id}/approval", [
                'keputusan' => 'disetujui',
                'catatan' => 'Putaran pertama.',
                'jalur_pelayanan' => 'online',
            ])
            ->assertOk();

        $this->sebagai($this->token($this->akun('ppid-utama')))
            ->postJson("/api/v1/permohonan/{$permohonan->id}/approval", [
                'keputusan' => 'revisi',
                'catatan' => 'Lampiran kurang jelas.',
            ])
            ->assertOk();

        $isi = $this->sebagai($tokenPelaksana)
            ->getJson("/api/v1/permohonan/{$permohonan->id}/approval")
            ->assertOk()
            ->json('data');

        // Empat langkah tersimpan, tetapi yang dikirim sebagai jenjang berjalan
        // hanya dua. Daftar rata berbunyi "1, 2, 1, 2": urutan yang mundur di
        // tengah dan terbaca sebagai data rusak, bukan sebagai berkas yang
        // sudah berputar dua kali.
        $this->assertCount(4, $this->langkah($permohonan));
        $this->assertCount(2, $isi['langkah'], 'Putaran lama ikut masuk ke jenjang berjalan.');
        $this->assertSame(2, $isi['putaran']);
        $this->assertCount(1, $isi['riwayat_putaran'], 'Putaran pertama hilang dari riwayat.');
        $this->assertCount(2, $isi['riwayat_putaran'][0]);

        // Urutan di dalam tiap putaran tetap menaik, dan langkah yang berjalan
        // memang ada di putaran yang dikirim.
        $this->assertSame([1, 2], array_column($isi['langkah'], 'urutan'));
        $this->assertSame([1, 2], array_column($isi['riwayat_putaran'][0], 'urutan'));
        $this->assertContains($isi['berjalan_id'], array_column($isi['langkah'], 'id'));

        // Yang dilipat memang putusan lama, bukan salinan yang berjalan.
        $this->assertSame('revisi', $isi['riwayat_putaran'][0][1]['status']);
    }

    public function test_lonceng_giliran_ikut_hilang_saat_berkasnya_dihapus(): void
    {
        $permohonan = $this->permohonan();

        $this->sebagai($this->token($this->akun('ppid-pelaksana')))
            ->getJson("/api/v1/permohonan/{$permohonan->id}/approval")
            ->assertOk();

        $langkahId = $this->langkah($permohonan)->first()->id;

        $lonceng = fn () => Notifikasi::where('type', 'approval_menunggu')
            ->whereRaw("data->>'approval_id' = ?", [(string) $langkahId])
            ->count();

        $this->assertGreaterThan(0, $lonceng(), 'Lonceng giliran tidak terkirim.');

        // Hapus lunak: berkasnya lenyap dari daftar, jadi loncengnya menunjuk
        // rincian yang tidak bisa dibuka lagi. Langkahnya sendiri ditinggalkan
        // sebagai jejak.
        $permohonan->delete();

        $this->assertSame(0, $lonceng(), 'Lonceng menggantung setelah berkasnya dihapus.');
        $this->assertCount(2, $this->langkah($permohonan));

        // Hapus permanen membuang langkahnya juga: tidak ada lagi berkas yang
        // dirujuknya, dan tabelnya tidak bisa dipasangi foreign key.
        $permohonan->forceDelete();

        $this->assertCount(0, $this->langkah($permohonan));
    }
}
