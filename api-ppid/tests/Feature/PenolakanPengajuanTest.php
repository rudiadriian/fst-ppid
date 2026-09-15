<?php

namespace Tests\Feature;

use App\Mail\StatusLayananMail;
use App\Models\KeberatanInformasi;
use App\Models\NotifikasiPemohon;
use App\Models\Pemohon;
use App\Models\PermohonanInformasi;
use App\Models\Role;
use App\Models\User;
use App\Support\EmailPemohon;
use App\Support\SlaLayanan;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Pemohon diberi tahu saat pengajuannya DITOLAK (UAT poin 14).
 *
 * `DatabaseTransactions` dengan alasan yang sama seperti
 * {@see AlurLayananPpidTest}.
 */
class PenolakanPengajuanTest extends TestCase
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

    private function akun(string $roleSlug): User
    {
        $this->urut++;

        return User::create([
            'name' => "Uji $roleSlug $this->tanda",
            'email' => Str::lower($roleSlug).$this->urut.'.'.Str::lower($this->tanda).'@uji.test',
            'password' => Hash::make($this->password),
            'role_id' => Role::where('slug', $roleSlug)->value('id'),
            'is_active' => true,
        ]);
    }

    /** Lihat catatan yang sama di {@see AlurPersetujuanPermohonanTest::token()}. */
    private function token(User $user): string
    {
        $this->flushHeaders();
        $this->lupakanIdentitas();

        return $this->postJson('/api/v1/auth/sign-in', [
            'email' => $user->email,
            'password' => $this->password,
        ])->assertOk()->json('access_token');
    }

    private function lupakanIdentitas(): void
    {
        $this->app['auth']->forgetGuards();
        $this->app->forgetInstance('tymon.jwt');
        $this->app->forgetInstance('tymon.jwt.auth');
        $this->app->forgetInstance('tymon.jwt.provider.jwt');
    }

    private function sebagai(string $token): self
    {
        $this->lupakanIdentitas();

        return $this->withToken($token);
    }

    private function pemohon(): Pemohon
    {
        $this->urut++;

        return Pemohon::create([
            'nama' => "Pemohon Uji $this->tanda",
            'email' => 'pemohon'.$this->urut.'.'.Str::lower($this->tanda).'@uji.test',
            'password' => Hash::make($this->password),
            'jenis_pemohon' => 'pribadi',
        ]);
    }

    private function permohonan(string $status = 'diajukan'): PermohonanInformasi
    {
        $pemohon = $this->pemohon();

        return PermohonanInformasi::create([
            'kode_permohonan' => 'UJI-TOLAK-'.$this->urut.'-'.$this->tanda,
            'pemohon_id' => $pemohon->id,
            'rincian_informasi' => "Rincian uji $this->tanda",
            'status' => $status,
            'jalur_pelayanan' => 'online',
            'tanggal_permohonan' => now(),
            'batas_waktu_tanggapan' => SlaLayanan::batasPermohonan(),
            'batas_waktu_awal' => SlaLayanan::batasPermohonan(),
        ]);
    }

    private function keberatan(): KeberatanInformasi
    {
        $induk = $this->permohonan('selesai');

        return KeberatanInformasi::create([
            'permohonan_id' => $induk->id,
            'pemohon_id' => $induk->pemohon_id,
            'jenis_keberatan' => 'permohonan_ditolak',
            'alasan_keberatan' => "Alasan uji $this->tanda",
            'kasus_posisi' => "Kasus uji $this->tanda",
            'status' => 'diajukan',
            'jalur_pelayanan' => 'online',
            'tanggal_keberatan' => now(),
            'batas_waktu_tanggapan' => SlaLayanan::batasKeberatan(),
        ])->refresh();
    }

    /** Pelaksana meneruskan berkasnya, lalu PPID menolaknya. */
    private function tolakLewatJenjang(string $modul, int $id, string $alasan): void
    {
        $tokenPelaksana = $this->token($this->akun('ppid-pelaksana'));

        $this->sebagai($tokenPelaksana)->getJson("/api/v1/{$modul}/{$id}/approval")->assertOk();

        $this->sebagai($tokenPelaksana)
            ->postJson("/api/v1/{$modul}/{$id}/approval", [
                'keputusan' => 'disetujui',
                'jalur_pelayanan' => 'online',
            ])
            ->assertOk();

        $this->sebagai($this->token($this->akun('ppid-utama')))
            ->postJson("/api/v1/{$modul}/{$id}/approval", [
                'keputusan' => 'ditolak',
                'catatan' => $alasan,
            ])
            ->assertOk();
    }

    private function loncengTolak(int $pemohonId, string $tipe): ?NotifikasiPemohon
    {
        return NotifikasiPemohon::where('pemohon_id', $pemohonId)
            ->where('type', $tipe)
            ->whereRaw("data->>'status' = 'ditolak'")
            ->latest('id')
            ->first();
    }

    public function test_permohonan_ditolak_ppid_sampai_ke_lonceng_dan_email(): void
    {
        $permohonan = $this->permohonan();
        $alasan = "Informasi dikecualikan Pasal 17 $this->tanda.";

        $this->tolakLewatJenjang('permohonan', $permohonan->id, $alasan);

        $permohonan->refresh()->load('pemohon');
        $this->assertSame('ditolak', $permohonan->status);

        $lonceng = $this->loncengTolak($permohonan->pemohon_id, 'permohonan_status');
        $this->assertNotNull($lonceng, 'Lonceng portal pemohon tidak menerima kabar penolakan.');
        $this->assertStringContainsString($alasan, $lonceng->message);

        Mail::assertSent(StatusLayananMail::class, function (StatusLayananMail $surat) use ($permohonan, $alasan) {
            return $surat->hasTo($permohonan->pemohon->email)
                && str_contains($surat->envelope()->subject, 'ditolak')
                && str_contains($surat->render(), $alasan);
        });
    }

    public function test_keberatan_ditolak_ppid_sampai_ke_lonceng_dan_email(): void
    {
        $keberatan = $this->keberatan();
        $alasan = "Tanggapan PPID sudah sesuai $this->tanda.";

        $this->tolakLewatJenjang('keberatan', $keberatan->id, $alasan);

        $keberatan->refresh()->load('pemohon');
        $this->assertSame('ditolak', $keberatan->status);

        $lonceng = $this->loncengTolak($keberatan->pemohon_id, 'keberatan_status');
        $this->assertNotNull($lonceng, 'Lonceng portal pemohon tidak menerima kabar penolakan keberatan.');
        $this->assertStringContainsString($alasan, $lonceng->message);

        Mail::assertSent(StatusLayananMail::class, function (StatusLayananMail $surat) use ($keberatan, $alasan) {
            return $surat->hasTo($keberatan->pemohon->email)
                && str_contains($surat->envelope()->subject, 'ditolak')
                && str_contains($surat->envelope()->subject, (string) $keberatan->kode_keberatan)
                && str_contains($surat->render(), $alasan);
        });
    }

    public function test_tolak_lewat_dropdown_status_membawa_alasan_bukan_catatan_internal(): void
    {
        $permohonan = $this->permohonan();
        $alasan = "Dokumen tidak dikuasai badan publik $this->tanda.";
        $internal = "Catatan rapat internal $this->tanda";

        $this->sebagai($this->token($this->akun('ppid-utama')))
            ->postJson("/api/v1/permohonan/{$permohonan->id}/status", [
                'status_baru' => 'ditolak',
                'catatan' => $internal,
                'alasan_penolakan' => $alasan,
            ])
            ->assertOk();

        $lonceng = $this->loncengTolak($permohonan->pemohon_id, 'permohonan_status');
        $this->assertNotNull($lonceng);
        $this->assertStringContainsString($alasan, $lonceng->message);
        $this->assertStringNotContainsString($internal, $lonceng->message);

        Mail::assertSent(StatusLayananMail::class, function (StatusLayananMail $surat) use ($alasan, $internal) {
            $html = $surat->render();

            return str_contains($html, $alasan)
                && !str_contains($html, $internal)
                // Hak keberatan ikut disebut: tanpanya surat penolakan hanya
                // menutup pintu.
                && str_contains($html, 'keberatan');
        });
    }

    public function test_ditolak_sebagian_disurati_sebagai_penolakan_sebagian(): void
    {
        // Status dipasang langsung: baris baru selalu lahir `diajukan`, dan
        // memindahkannya ke Diproses lewat endpoint ikut membuka jenjang.
        $permohonan = $this->permohonan();
        $permohonan->forceFill(['status' => 'diproses'])->save();

        $this->sebagai($this->token($this->akun('ppid-utama')))
            ->postJson("/api/v1/permohonan/{$permohonan->id}/status", [
                'status_baru' => 'ditolak_sebagian',
                'alasan_penolakan' => "Lampiran B dikecualikan $this->tanda.",
            ])
            ->assertOk();

        Mail::assertSent(
            StatusLayananMail::class,
            fn (StatusLayananMail $surat) => str_contains($surat->envelope()->subject, 'ditolak sebagian')
        );
    }

    public function test_keberatan_tidak_bisa_ditolak_tanpa_tanggapan(): void
    {
        $keberatan = $this->keberatan();
        $token = $this->token($this->akun('ppid-utama'));

        $this->sebagai($token)
            ->postJson("/api/v1/keberatan/{$keberatan->id}/tanggapan", ['status' => 'ditolak'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('tanggapan_atasan_ppid');

        $this->assertSame('diajukan', $keberatan->refresh()->status);
        Mail::assertNothingSent();

        $alasan = "Keberatan diajukan melewati tenggat $this->tanda.";

        $this->sebagai($token)
            ->postJson("/api/v1/keberatan/{$keberatan->id}/tanggapan", [
                'status' => 'ditolak',
                'tanggapan_atasan_ppid' => $alasan,
            ])
            ->assertOk();

        $lonceng = $this->loncengTolak($keberatan->pemohon_id, 'keberatan_status');
        $this->assertNotNull($lonceng);

        // Tautannya ke rincian keberatan itu sendiri — di sanalah tanggapannya
        // tertulis — bukan ke daftar yang harus dicari lagi barisnya.
        $this->assertSame("/akun/keberatan/{$keberatan->id}", $lonceng->data['link'] ?? null);

        Mail::assertSent(StatusLayananMail::class, function (StatusLayananMail $surat) use ($alasan, $keberatan) {
            $html = $surat->render();

            return str_contains($html, $alasan)
                && str_contains($html, "/akun/keberatan/{$keberatan->id}")
                && str_contains($html, 'Komisi Informasi');
        });
    }

    public function test_simpan_ulang_penolakan_tidak_memberi_tahu_dua_kali(): void
    {
        $keberatan = $this->keberatan();
        $token = $this->token($this->akun('ppid-utama'));

        foreach (["Tanggapan $this->tanda.", "Tanggapan dibetulkan $this->tanda."] as $tanggapan) {
            $this->sebagai($token)
                ->postJson("/api/v1/keberatan/{$keberatan->id}/tanggapan", [
                    'status' => 'ditolak',
                    'tanggapan_atasan_ppid' => $tanggapan,
                ])
                ->assertOk();
        }

        $this->assertSame(1, NotifikasiPemohon::where('pemohon_id', $keberatan->pemohon_id)->count());

        /*
         * Surelnya diperiksa langsung, bukan dihitung dari dua permintaan di
         * atas: `afterResponse()` menumpang callback `terminating` aplikasi,
         * dan di dalam tes aplikasinya dipakai ulang antarpermintaan — callback
         * permintaan pertama ikut berjalan lagi saat permintaan kedua selesai.
         * Server sungguhan membangun aplikasinya baru tiap permintaan.
         */
        Mail::fake();

        EmailPemohon::statusBerubah($keberatan->refresh(), 'ditolak', 'ditolak');

        Mail::assertNothingSent();
    }
}
