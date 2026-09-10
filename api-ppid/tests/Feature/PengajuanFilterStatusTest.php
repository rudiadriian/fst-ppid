<?php

namespace Tests\Feature;

use App\Models\KeberatanInformasi;
use App\Models\Pemohon;
use App\Models\PermohonanInformasi;
use App\Models\Role;
use App\Models\User;
use App\Support\SlaLayanan;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Filter Status pada daftar gabungan Permohonan (UAT poin 12).
 *
 * Dua hal yang dijaga:
 *
 *  - penyaringan status benar-benar bekerja untuk kedua kategori, termasuk saat
 *    dipadukan dengan filter Kategori;
 *  - setiap status yang dikenal alur (`PermohonanInformasi::TRANSISI` dan
 *    `KeberatanInformasi::TRANSISI`) memang diterima CHECK constraint tabelnya.
 *    Tanpa pemeriksaan itu, alur bisa menyebut status yang tidak pernah bisa
 *    tersimpan — dan filter di panel akan menawarkan pilihan yang selamanya
 *    kosong.
 *
 * `DatabaseTransactions` dengan alasan yang sama seperti tes lain di sini:
 * sebagian tabel inti `ppiddb` dibuat lewat DDL, bukan migration.
 */
class PengajuanFilterStatusTest extends TestCase
{
    use DatabaseTransactions;

    private string $password = 'RahasiaKuat#123';

    private string $tanda;

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

    private function token(): string
    {
        $user = User::create([
            'name' => 'Petugas Filter '.$this->tanda,
            'email' => 'filter-'.Str::lower($this->tanda).'@uji.test',
            'password' => Hash::make($this->password),
            'role_id' => Role::where('slug', 'super-admin')->value('id'),
            'is_active' => true,
        ]);

        return $this->postJson('/api/v1/auth/sign-in', [
            'email' => $user->email,
            'password' => $this->password,
        ])->assertOk()->json('access_token');
    }

    private function pemohon(): Pemohon
    {
        return Pemohon::create([
            'nama' => 'Pemohon Filter '.$this->tanda,
            'email' => 'pemohon-filter-'.Str::lower($this->tanda).'@uji.test',
            'password' => Hash::make($this->password),
            'jenis_pemohon' => 'perorangan',
        ]);
    }

    /**
     * Status dipasang lewat query, bukan `create()`.
     *
     * `status` sengaja tidak `fillable` pada kedua model: perpindahannya hanya
     * boleh lewat endpoint transisi supaya log statusnya selalu terisi. Tes ini
     * menyiapkan keadaan, bukan menjalankan perpindahan, jadi nilainya
     * dipasang langsung ke barisnya.
     */
    private function permohonan(Pemohon $pemohon, string $status): PermohonanInformasi
    {
        $baris = PermohonanInformasi::create([
            'pemohon_id' => $pemohon->id,
            'rincian_informasi' => 'Rincian filter '.$this->tanda,
            'jalur_pelayanan' => 'online',
            'batas_waktu_tanggapan' => SlaLayanan::batasPermohonan(),
        ]);

        DB::table('permohonan_informasi')->where('id', $baris->id)->update(['status' => $status]);

        return $baris->fresh();
    }

    private function keberatan(Pemohon $pemohon, PermohonanInformasi $permohonan, string $status): KeberatanInformasi
    {
        $baris = KeberatanInformasi::create([
            'permohonan_id' => $permohonan->id,
            'pemohon_id' => $pemohon->id,
            'jenis_keberatan' => 'permohonan_ditolak',
            'alasan_keberatan' => 'Alasan filter '.$this->tanda,
            'kasus_posisi' => 'Kasus filter '.$this->tanda,
            'jalur_pelayanan' => 'online',
            'tanggal_keberatan' => now(),
            'batas_waktu_tanggapan' => SlaLayanan::batasKeberatan(),
        ]);

        DB::table('keberatan_informasi')->where('id', $baris->id)->update(['status' => $status]);

        return $baris->fresh();
    }

    /** Baris uji saja; daftar bisa berisi data lain di basis data pengembangan. */
    private function barisUji(array $data): array
    {
        return array_values(array_filter(
            $data,
            fn (array $baris) => str_contains((string) ($baris['pokok'] ?? ''), $this->tanda)
        ));
    }

    public function test_status_menyaring_kedua_kategori(): void
    {
        $token = $this->token();
        $pemohon = $this->pemohon();

        $revisi = $this->permohonan($pemohon, 'revisi');
        $this->permohonan($pemohon, 'selesai');
        $this->keberatan($pemohon, $revisi, 'diproses');

        $hasil = $this->withToken($token)
            ->getJson('/api/v1/pengajuan?status=revisi&per_page=100')
            ->assertOk();

        $baris = $this->barisUji($hasil->json('data'));

        $this->assertCount(1, $baris);
        $this->assertSame('permohonan', $baris[0]['jenis']);
        $this->assertSame('revisi', $baris[0]['status']);
    }

    public function test_status_dipadukan_dengan_kategori(): void
    {
        $token = $this->token();
        $pemohon = $this->pemohon();

        $permohonan = $this->permohonan($pemohon, 'diproses');
        $this->keberatan($pemohon, $permohonan, 'diproses');

        $hanyaKeberatan = $this->withToken($token)
            ->getJson('/api/v1/pengajuan?jenis=keberatan&status=diproses&per_page=100')
            ->assertOk();

        $baris = $this->barisUji($hanyaKeberatan->json('data'));

        $this->assertCount(1, $baris);
        $this->assertSame('keberatan', $baris[0]['jenis']);
    }

    /**
     * Status yang tidak dipakai kategori itu menghasilkan daftar kosong, bukan
     * galat. Inilah yang membuat panel kini menyempitkan pilihannya begitu
     * kategori dipilih — jawabannya sah, tetapi tidak ada gunanya ditawarkan.
     */
    public function test_status_milik_permohonan_kosong_pada_kategori_keberatan(): void
    {
        $token = $this->token();
        $pemohon = $this->pemohon();

        $permohonan = $this->permohonan($pemohon, 'kedaluwarsa');
        $this->keberatan($pemohon, $permohonan, 'diajukan');

        $hasil = $this->withToken($token)
            ->getJson('/api/v1/pengajuan?jenis=keberatan&status=kedaluwarsa&per_page=100')
            ->assertOk();

        $this->assertSame([], $this->barisUji($hasil->json('data')));
    }

    /**
     * Setiap status pada alur permohonan bisa benar-benar tersimpan, dan ikut
     * terbaca lewat filternya. Ini yang menahan alur dan CHECK constraint
     * berjalan sendiri-sendiri.
     */
    public function test_setiap_status_alur_permohonan_tersimpan_dan_tersaring(): void
    {
        $token = $this->token();
        $pemohon = $this->pemohon();

        foreach (array_keys(PermohonanInformasi::TRANSISI) as $status) {
            $baris = $this->permohonan($pemohon, $status);

            $this->assertSame(
                $status,
                DB::table('permohonan_informasi')->where('id', $baris->id)->value('status'),
                "Status {$status} tidak tersimpan apa adanya."
            );

            $hasil = $this->withToken($token)
                ->getJson("/api/v1/pengajuan?jenis=permohonan&status={$status}&per_page=100")
                ->assertOk();

            $this->assertContains(
                $baris->kode_permohonan,
                array_column($this->barisUji($hasil->json('data')), 'kode'),
                "Filter status {$status} tidak menemukan barisnya."
            );
        }
    }

    public function test_setiap_status_alur_keberatan_tersimpan_dan_tersaring(): void
    {
        $token = $this->token();
        $pemohon = $this->pemohon();
        $permohonan = $this->permohonan($pemohon, 'diajukan');

        foreach (array_keys(KeberatanInformasi::TRANSISI) as $status) {
            $baris = $this->keberatan($pemohon, $permohonan, $status);

            $this->assertSame(
                $status,
                DB::table('keberatan_informasi')->where('id', $baris->id)->value('status'),
                "Status {$status} tidak tersimpan apa adanya."
            );

            $hasil = $this->withToken($token)
                ->getJson("/api/v1/pengajuan?jenis=keberatan&status={$status}&per_page=100")
                ->assertOk();

            $this->assertNotEmpty(
                $this->barisUji($hasil->json('data')),
                "Filter status {$status} tidak menemukan barisnya."
            );
        }
    }
}
