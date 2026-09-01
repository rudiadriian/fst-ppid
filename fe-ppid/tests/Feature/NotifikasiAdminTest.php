<?php

namespace Tests\Feature;

use App\Models\Notifikasi;
use App\Models\Pemohon;
use App\Support\NotifikasiAdmin;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Notifikasi panel admin (langkah 91).
 *
 * Dua hal yang diuji: kiriman berulang untuk pokok yang sama tidak menggandakan
 * barisnya, dan penerimanya mengikuti hak lihat modul yang menaungi halaman
 * tujuan tautannya — bukan seluruh pengguna panel.
 */
class NotifikasiAdminTest extends TestCase
{
    use DatabaseTransactions;

    private int $roleLayanan;

    private int $roleKonten;

    private int $userLayanan;

    private int $userKonten;

    protected function setUp(): void
    {
        parent::setUp();

        // Role uji dibuat sendiri supaya hasilnya tidak bergantung pada isi
        // matrix hak akses yang sedang berlaku di basis data.
        $this->roleLayanan = $this->buatRole('uji-layanan-91', ['permohonan']);
        $this->roleKonten = $this->buatRole('uji-konten-91', ['berita']);

        $this->userLayanan = $this->buatUser('uji-layanan-91@example.test', $this->roleLayanan);
        $this->userKonten = $this->buatUser('uji-konten-91@example.test', $this->roleKonten);
    }

    /** @param  array<int, string>  $modulBolehLihat */
    private function buatRole(string $slug, array $modulBolehLihat): int
    {
        $roleId = (int) DB::table('roles')->insertGetId([
            'slug' => $slug,
            'name' => $slug,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach ($modulBolehLihat as $modulSlug) {
            $modulId = DB::table('modul_sistem')->where('slug', $modulSlug)->value('id');

            DB::table('role_modul_akses')->insert([
                'role_id' => $roleId,
                'modul_id' => $modulId,
                'can_view' => true,
                'can_create' => false,
                'can_edit' => false,
                'can_delete' => false,
                'can_approve' => false,
                'can_export' => false,
            ]);
        }

        return $roleId;
    }

    private function buatUser(string $email, int $roleId): int
    {
        return (int) DB::table('users')->insertGetId([
            'name' => $email,
            'email' => $email,
            'password' => bcrypt('RahasiaUji12345'),
            'role_id' => $roleId,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function pemohon(): Pemohon
    {
        return Pemohon::create([
            'nama' => 'Uji Notifikasi',
            'email' => 'uji-notifikasi-91@example.test',
            'no_hp' => '08000000091',
            'jenis_pemohon' => 'perorangan',
            'status_verifikasi' => 'belum',
            'email_verified_at' => now(),
            'password' => 'RahasiaUji12345',
        ]);
    }

    /** @return \Illuminate\Support\Collection<int, Notifikasi> */
    private function notifikasi(int $userId, string $tipe)
    {
        return Notifikasi::where('user_id', $userId)->where('type', $tipe)->orderBy('id')->get();
    }

    public function test_kiriman_ulang_data_pemohon_tidak_menggandakan_notifikasi(): void
    {
        Storage::fake('public');

        $pemohon = $this->pemohon();

        $kirim = fn () => $this->actingAs($pemohon, 'pemohon')->put(route('akun.data-pemohon.update'), [
            'jenis_pemohon' => 'perorangan',
            'nik' => '3175010101900091',
            'pekerjaan' => 'Karyawan',
            'alamat' => 'Jalan Uji Nomor 91',
            'file_ktp' => UploadedFile::fake()->image('ktp.png'),
        ]);

        $kirim()->assertSessionHasNoErrors();
        $kirim()->assertSessionHasNoErrors();

        $baris = $this->notifikasi($this->userLayanan, 'verifikasi_pemohon');

        $this->assertCount(1, $baris, 'Kiriman kedua harus memperbarui baris yang sama, bukan menambah baris baru.');
        $this->assertSame($pemohon->id, (int) $baris->first()->data['pemohon_id']);
    }

    public function test_notifikasi_yang_sudah_dibaca_tidak_ditimpa(): void
    {
        $pemohon = $this->pemohon();

        NotifikasiAdmin::verifikasiPemohonMenunggu($pemohon);

        Notifikasi::where('user_id', $this->userLayanan)->update(['is_read' => true]);

        // Petugas menandainya karena sudah menanganinya; kiriman berikutnya
        // memang pemberitahuan baru, bukan kelanjutan yang sama.
        NotifikasiAdmin::verifikasiPemohonMenunggu($pemohon);

        $this->assertCount(2, $this->notifikasi($this->userLayanan, 'verifikasi_pemohon'));
    }

    public function test_penerima_hanya_role_yang_boleh_melihat_modulnya(): void
    {
        $pemohon = $this->pemohon();

        NotifikasiAdmin::verifikasiPemohonMenunggu($pemohon);

        $this->assertCount(1, $this->notifikasi($this->userLayanan, 'verifikasi_pemohon'));
        $this->assertCount(0, $this->notifikasi($this->userKonten, 'verifikasi_pemohon'));
    }

    public function test_notifikasi_membawa_slug_modulnya(): void
    {
        $pemohon = $this->pemohon();

        NotifikasiAdmin::pendaftaranBaru($pemohon);

        $baris = $this->notifikasi($this->userLayanan, 'pemohon_baru')->first();

        $this->assertSame('permohonan', $baris->data['modul']);
        $this->assertSame('/ppid/pemohon?detail='.$pemohon->id, $baris->data['link']);
    }

    public function test_keberatan_menaut_ke_modul_permohonan(): void
    {
        $pemohon = $this->pemohon();

        $permohonan = \App\Models\PermohonanInformasi::create([
            'pemohon_id' => $pemohon->id,
            'rincian_informasi' => 'Uji keberatan langkah 91',
            'tujuan_penggunaan' => 'Pengujian',
            'cara_memperoleh' => 'membaca',
            'cara_pengiriman' => 'email',
            'status' => 'selesai',
            'tanggal_permohonan' => now(),
        ]);

        $keberatan = \App\Models\KeberatanInformasi::create([
            'permohonan_id' => $permohonan->id,
            'pemohon_id' => $pemohon->id,
            'jenis_keberatan' => 'informasi_tidak_disediakan',
            'alasan_keberatan' => 'Informasi yang diminta belum diberikan.',
            'kasus_posisi' => 'Uji kasus posisi langkah 91',
            'status' => 'diajukan',
            'tanggal_keberatan' => now(),
        ])->fresh();

        NotifikasiAdmin::keberatanBaru($keberatan, $permohonan, $pemohon);

        $baris = $this->notifikasi($this->userLayanan, 'keberatan_baru')->first();

        // Modul Keberatan sudah tidak ada di menu sejak langkah 89: isinya
        // dibuka dari daftar gabungan pada modul Permohonan.
        $this->assertNotNull($baris, 'Pemegang hak lihat modul Permohonan harus ikut diberi tahu.');
        $this->assertSame('permohonan', $baris->data['modul']);
        $this->assertSame('/ppid/permohonan?detail='.$keberatan->id.'&jenis=keberatan', $baris->data['link']);

        /*
         * Kedua nomor ikut ke lonceng (langkah 89). Nomor keberatannya sendiri
         * yang diarsipkan petugas; nomor permohonan induknya yang memberi tahu
         * perkara mana yang dipersoalkan. Tanpa yang pertama, dua keberatan
         * atas permohonan yang sama tampak sebagai baris kembar.
         */
        $this->assertNotNull($keberatan->kode_keberatan);
        $this->assertStringContainsString($keberatan->kode_keberatan, $baris->message);
        $this->assertStringContainsString((string) $permohonan->kode_permohonan, $baris->message);
        $this->assertSame($keberatan->kode_keberatan, $baris->data['kode_keberatan']);
    }
}
