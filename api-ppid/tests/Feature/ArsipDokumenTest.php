<?php

namespace Tests\Feature;

use App\Models\ArsipDokumen;
use App\Models\Pemohon;
use App\Models\PermohonanInformasi;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Arsip Dokumen petugas (langkah 95).
 *
 * Dua hal yang dijaga: berkas apa pun yang dilampirkan ke permohonan ikut
 * tercatat di arsip, dan berkas arsip bisa dilampirkan ke permohonan lain
 * tanpa unggahan kedua — satu berkas fisik, banyak permohonan.
 */
class ArsipDokumenTest extends TestCase
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

    private function permohonan(): PermohonanInformasi
    {
        $pemohon = Pemohon::create([
            'nama' => "Pemohon $this->tanda",
            'email' => 'pemohon.'.Str::lower(Str::random(6)).'@uji.test',
            'password' => Hash::make($this->password),
            'jenis_pemohon' => 'pribadi',
        ]);

        return PermohonanInformasi::create([
            'kode_permohonan' => 'ARS-'.Str::random(6),
            'pemohon_id' => $pemohon->id,
            'rincian_informasi' => "Rincian uji $this->tanda",
            'status' => 'diproses',
            'tanggal_permohonan' => now(),
        ]);
    }

    /**
     * Yang dilaporkan pada langkah 95: unggahan berkas tanggapan oleh PPID
     * Pelaksana. Dokumen Office ikut diuji karena jenis `dokumen_gambar` hanya
     * menerima PDF dan gambar — panel kini memilih jenisnya per berkas.
     */
    public function test_pelaksana_bisa_mengunggah_pdf_dan_dokumen_office(): void
    {
        $token = $this->token($this->akun('ppid-pelaksana'));

        $pdf = $this->withToken($token)->post('/api/v1/uploads', [
            'folder' => 'permohonan',
            'jenis' => 'dokumen_gambar',
            'file' => UploadedFile::fake()->create('tanggapan.pdf', 64, 'application/pdf'),
        ], ['Accept' => 'application/json']);

        $pdf->assertCreated();

        $docx = $this->withToken($token)->post('/api/v1/uploads', [
            'folder' => 'permohonan',
            'jenis' => 'dokumen',
            'file' => UploadedFile::fake()->create(
                'tanggapan.docx',
                64,
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ),
        ], ['Accept' => 'application/json']);

        $docx->assertCreated();

        // Jenis yang salah memang ditolak — inilah sebab kegagalan yang
        // dilaporkan, dan panel sekarang tidak lagi mengirimkannya begitu.
        $this->withToken($token)->post('/api/v1/uploads', [
            'folder' => 'permohonan',
            'jenis' => 'dokumen_gambar',
            'file' => UploadedFile::fake()->create('tanggapan.docx', 64, 'application/msword'),
        ], ['Accept' => 'application/json'])->assertStatus(422);
    }

    public function test_berkas_tanggapan_ikut_tercatat_di_arsip(): void
    {
        $permohonan = $this->permohonan();
        $token = $this->token($this->akun('ppid-pelaksana'));
        $path = "uploads/permohonan/2026/08/{$this->tanda}.pdf";

        $this->withToken($token)
            ->postJson("/api/v1/permohonan/{$permohonan->id}/tanggapan-files", [
                'files' => [['nama_file' => 'Tanggapan.pdf', 'path_file' => $path]],
            ])
            ->assertCreated();

        $this->assertSame(1, ArsipDokumen::where('path_file', $path)->count());
    }

    /** Melampirkan berkas yang sama dua kali tidak menggandakan barisnya. */
    public function test_berkas_yang_sama_tidak_menggandakan_baris_arsip(): void
    {
        $token = $this->token($this->akun('ppid-pelaksana'));
        $path = "uploads/permohonan/2026/08/{$this->tanda}-dipakai-ulang.pdf";

        foreach ([$this->permohonan(), $this->permohonan()] as $permohonan) {
            $this->withToken($token)
                ->postJson("/api/v1/permohonan/{$permohonan->id}/tanggapan-files", [
                    'files' => [['nama_file' => 'SK Bersama.pdf', 'path_file' => $path]],
                ])
                ->assertCreated();
        }

        $this->assertSame(1, ArsipDokumen::where('path_file', $path)->count());
    }

    public function test_pelaksana_bisa_membaca_dan_menambah_arsip(): void
    {
        $token = $this->token($this->akun('ppid-pelaksana'));

        $this->withToken($token)
            ->postJson('/api/v1/arsip-dokumen', [
                'nama' => "Laporan Tahunan $this->tanda",
                'kategori' => 'Laporan',
                'path_file' => "uploads/permohonan/2026/08/{$this->tanda}-laporan.pdf",
            ])
            ->assertCreated();

        $isi = $this->withToken($token)
            ->getJson('/api/v1/arsip-dokumen?search='.$this->tanda)
            ->assertOk()
            ->json('data');

        $this->assertCount(1, $isi);
        $this->assertSame("Laporan Tahunan $this->tanda", $isi[0]['nama']);
    }

    /** Satu berkas fisik hanya boleh punya satu baris arsip. */
    public function test_path_berkas_tidak_boleh_kembar(): void
    {
        $token = $this->token($this->akun('ppid-pelaksana'));
        $path = "uploads/permohonan/2026/08/{$this->tanda}-kembar.pdf";

        $this->withToken($token)
            ->postJson('/api/v1/arsip-dokumen', ['nama' => 'Pertama', 'path_file' => $path])
            ->assertCreated();

        $this->withToken($token)
            ->postJson('/api/v1/arsip-dokumen', ['nama' => 'Kedua', 'path_file' => $path])
            ->assertStatus(422);
    }

    /** Atasan PPID tidak berurusan dengan arsip dokumen petugas. */
    public function test_role_tanpa_hak_ditolak(): void
    {
        $this->withToken($this->token($this->akun('atasan-ppid')))
            ->getJson('/api/v1/arsip-dokumen')
            ->assertStatus(403);
    }
}
