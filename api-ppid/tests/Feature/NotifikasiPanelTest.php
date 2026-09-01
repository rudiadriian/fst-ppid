<?php

namespace Tests\Feature;

use App\Models\Notifikasi;
use App\Models\NotifikasiPemohon;
use App\Models\Pemohon;
use App\Models\PermohonanInformasi;
use App\Models\Role;
use App\Models\User;
use App\Support\AlurPersetujuan;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Lonceng panel dan pemberitahuan hasil verifikasi pemohon (langkah 91).
 *
 * Dua sisi yang diuji: putusan verifikasi yang disimpan dua kali tidak
 * menggandakan pemberitahuan ke pemohon, dan lonceng petugas hanya memuat
 * notifikasi milik modul yang masih boleh dilihat rolenya.
 *
 * `DatabaseTransactions` dipakai dengan alasan yang sama seperti
 * {@see AlurLayananPpidTest}.
 */
class NotifikasiPanelTest extends TestCase
{
    use DatabaseTransactions;

    private string $password = 'RahasiaKuat123';

    private string $tanda;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tanda = Str::random(8);

        // Sama seperti AlurLayananPpidTest: yang diuji notifikasinya, bukan
        // pengaman formulir masuknya.
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
            'nik' => '3175010101900091',
            'status_verifikasi' => 'menunggu',
        ]);
    }

    public function test_putusan_verifikasi_yang_diulang_tidak_menggandakan_pemberitahuan(): void
    {
        $pemohon = $this->pemohon();
        $token = $this->token($this->akun('ppid-utama'));

        $kirim = fn () => $this->withToken($token)
            ->postJson("/api/v1/pemohon/{$pemohon->id}/verifikasi", [
                'status' => 'ditolak',
                'catatan' => 'Berkas KTP tidak terbaca.',
            ])->assertOk();

        $kirim();
        $kirim();

        $notifikasi = NotifikasiPemohon::where('pemohon_id', $pemohon->id)
            ->where('type', 'verifikasi_pemohon')
            ->count();

        $this->assertSame(1, $notifikasi, 'Putusan yang sama disimpan dua kali tidak boleh mengirim dua notifikasi.');

        // Jatah kirim ulang pemohon juga tidak boleh ikut termakan klik kedua.
        $this->assertSame(1, (int) $pemohon->fresh()->jumlah_ditolak);
    }

    public function test_putusan_yang_benar_benar_berubah_tetap_diberitahukan(): void
    {
        $pemohon = $this->pemohon();
        $token = $this->token($this->akun('ppid-utama'));

        $this->withToken($token)
            ->postJson("/api/v1/pemohon/{$pemohon->id}/verifikasi", [
                'status' => 'ditolak',
                'catatan' => 'Berkas KTP tidak terbaca.',
            ])->assertOk();

        $this->withToken($token)
            ->postJson("/api/v1/pemohon/{$pemohon->id}/verifikasi", [
                'status' => 'terverifikasi',
            ])->assertOk();

        $this->assertSame(2, NotifikasiPemohon::where('pemohon_id', $pemohon->id)
            ->where('type', 'verifikasi_pemohon')
            ->count());
    }

    /**
     * Notifikasi hasil verifikasi mengantar pemohon ke halaman Data Pemohon —
     * satu-satunya halaman portal yang memuat keputusannya beserta catatan
     * petugas, sisa kesempatan, dan formulir perbaikannya (langkah 93).
     */
    public function test_notifikasi_hasil_verifikasi_mengarah_ke_halaman_data_pemohon(): void
    {
        $token = $this->token($this->akun('ppid-utama'));

        $tautan = function (string $status, ?string $catatan) use ($token): string {
            $pemohon = Pemohon::create([
                'nama' => "Pemohon $status $this->tanda",
                'email' => Str::lower($status).'.'.Str::lower($this->tanda).'@uji.test',
                'password' => Hash::make($this->password),
                'jenis_pemohon' => 'pribadi',
                'nik' => '3175010101900093',
                'status_verifikasi' => 'menunggu',
            ]);

            $this->withToken($token)
                ->postJson("/api/v1/pemohon/{$pemohon->id}/verifikasi", array_filter([
                    'status' => $status,
                    'catatan' => $catatan,
                ]))
                ->assertOk();

            return NotifikasiPemohon::where('pemohon_id', $pemohon->id)
                ->where('type', 'verifikasi_pemohon')
                ->latest('id')
                ->first()
                ->data['link'];
        };

        $this->assertSame('/akun/pengaturan/data-pemohon', $tautan('terverifikasi', null));
        $this->assertSame('/akun/pengaturan/data-pemohon', $tautan('ditolak', 'Berkas KTP tidak terbaca.'));
    }

    public function test_lonceng_menyembunyikan_notifikasi_modul_tanpa_hak_lihat(): void
    {
        // Atasan PPID tidak punya hak lihat modul Berita; notifikasi bertanda
        // modul itu tidak boleh muncul di loncengnya.
        $user = $this->akun('atasan-ppid');

        Notifikasi::create([
            'user_id' => $user->id,
            'type' => 'uji_layanan',
            'message' => "Pengajuan layanan $this->tanda",
            'is_read' => false,
            'data' => ['modul' => 'permohonan', 'title' => 'Layanan'],
        ]);

        Notifikasi::create([
            'user_id' => $user->id,
            'type' => 'uji_konten',
            'message' => "Berita $this->tanda",
            'is_read' => false,
            'data' => ['modul' => 'berita', 'title' => 'Konten'],
        ]);

        // Baris lama sebelum penanda modul ada tetap ikut tampil.
        Notifikasi::create([
            'user_id' => $user->id,
            'type' => 'uji_lama',
            'message' => "Tanpa penanda $this->tanda",
            'is_read' => false,
            'data' => ['title' => 'Lama'],
        ]);

        $isi = $this->withToken($this->token($user))
            ->getJson('/api/v1/notifikasi')
            ->assertOk()
            ->json();

        $pesan = array_column($isi, 'description');

        $this->assertContains("Pengajuan layanan $this->tanda", $pesan);
        $this->assertContains("Tanpa penanda $this->tanda", $pesan);
        $this->assertNotContains("Berita $this->tanda", $pesan);
    }

    /**
     * Tahap pertama alur dipegang PPID Pelaksana, dan endpoint putusannya
     * dijaga `akses:permohonan,approve`. Notifikasi "giliran Anda" jadi tidak
     * ada gunanya kalau hak modulnya belum dibuka — berkasnya berhenti di
     * tahap pertama dengan pesan 403 yang tidak dijelaskan di mana pun.
     */
    public function test_ppid_pelaksana_bisa_memutus_tahap_pertamanya(): void
    {
        $pemohon = $this->pemohon();

        $permohonan = PermohonanInformasi::create([
            'kode_permohonan' => 'UJI-91-'.$this->tanda,
            'pemohon_id' => $pemohon->id,
            'rincian_informasi' => "Rincian uji $this->tanda",
            'status' => 'menunggu_approval',
            'jalur_pelayanan' => 'online',
            'tanggal_permohonan' => now(),
        ]);

        AlurPersetujuan::mulai($permohonan);

        $pelaksana = $this->akun('ppid-pelaksana');

        $this->withToken($this->token($pelaksana))
            ->postJson("/api/v1/permohonan/{$permohonan->id}/approval", [
                'keputusan' => 'disetujui',
                'catatan' => 'Berkas lengkap, diteruskan ke PPID.',
                'jalur_pelayanan' => 'online',
                'keterangan_petugas' => 'Dokumen dikirim lewat surel.',
            ])
            ->assertOk();

        // Giliran berpindah ke PPID, dan tautannya menunjuk modul Permohonan —
        // satu-satunya modul layanan yang masih punya entri menu.
        $ppid = User::where('role_id', Role::where('slug', 'ppid-utama')->value('id'))->first();

        $notifikasi = Notifikasi::where('user_id', $ppid?->id)
            ->where('type', 'approval_menunggu')
            ->orderByDesc('id')
            ->first();

        $this->assertNotNull($notifikasi, 'Penyetuju tahap berikutnya harus diberi tahu.');
        $this->assertSame('permohonan', $notifikasi->data['modul']);
        $this->assertSame('/ppid/permohonan?detail='.$permohonan->id, $notifikasi->data['link']);
    }

    public function test_super_admin_melihat_notifikasi_seluruh_modul(): void
    {
        $user = $this->akun('super-admin');

        Notifikasi::create([
            'user_id' => $user->id,
            'type' => 'uji_konten',
            'message' => "Berita $this->tanda",
            'is_read' => false,
            'data' => ['modul' => 'berita', 'title' => 'Konten'],
        ]);

        $pesan = array_column($this->withToken($this->token($user))
            ->getJson('/api/v1/notifikasi')
            ->assertOk()
            ->json(), 'description');

        $this->assertContains("Berita $this->tanda", $pesan);
    }
}
