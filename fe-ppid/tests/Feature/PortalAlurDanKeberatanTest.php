<?php

namespace Tests\Feature;

use App\Models\KeberatanInformasi;
use App\Models\Pemohon;
use App\Models\PermohonanInformasi;
use App\Models\PermohonanLogStatus;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Penyempurnaan Portal Pemohon pada langkah 101.
 *
 * Tiga hal yang diuji: dasar keberatan mengikuti Pasal 35 UU KIP dan bukan
 * "sudah selesai", rincian keberatan punya halamannya sendiri, dan alur yang
 * ditampilkan pemohon selalu tiga langkah — berapa pun putaran internal yang
 * terjadi di panel admin.
 *
 * `DatabaseTransactions` dengan alasan yang sama seperti {@see PortalDaftarTest}.
 */
class PortalAlurDanKeberatanTest extends TestCase
{
    use DatabaseTransactions;

    private Pemohon $pemohon;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pemohon = Pemohon::create([
            'nama' => 'Uji Portal 101',
            'email' => 'uji-portal-101@example.test',
            'no_hp' => '08000000101',
            'jenis_pemohon' => 'perorangan',
            'status_verifikasi' => 'terverifikasi',
            'email_verified_at' => now(),
            'password' => 'RahasiaUji12345',
        ]);
    }

    /** Simpan permohonan lalu paksa statusnya; transisi status dijaga API. */
    private function permohonan(string $status, ?string $batasTanggapan = null): PermohonanInformasi
    {
        $baris = PermohonanInformasi::create([
            'pemohon_id' => $this->pemohon->id,
            'rincian_informasi' => 'Uji 101 '.$status,
            'status' => 'diajukan',
            'batas_waktu_tanggapan' => $batasTanggapan,
        ]);

        DB::table('permohonan_informasi')->where('id', $baris->id)->update(['status' => $status]);

        return $baris->fresh();
    }

    public function test_dasar_keberatan_mengikuti_tujuh_alasan_pasal_35(): void
    {
        // Berkas yang belum ditanggapi dan tenggatnya sudah lewat: dasarnya
        // "tidak ditanggapinya permintaan informasi" dan "penyampaian melebihi
        // waktu yang diatur". Aturan lama menutupnya karena statusnya belum
        // tuntas — padahal justru dua dasar itu yang paling sering dipakai.
        $lewatTenggat = $this->permohonan('diproses', now()->subDays(3)->toDateTimeString());
        $ditolak = $this->permohonan('ditolak');
        $masihBerjalan = $this->permohonan('diproses', now()->addDays(5)->toDateTimeString());

        $this->assertTrue($lewatTenggat->layakDikeberatankan());
        $this->assertTrue($ditolak->layakDikeberatankan());
        $this->assertFalse(
            $masihBerjalan->layakDikeberatankan(),
            'Berkas yang masih dalam tenggat belum punya dasar keberatan.'
        );

        $html = $this->actingAs($this->pemohon, 'pemohon')
            ->get(route('akun.keberatan.create'))
            ->assertOk()
            ->getContent();

        // Yang layak muncul di dropdown, yang belum tidak.
        $this->assertStringContainsString('value="'.$lewatTenggat->id.'"', $html);
        $this->assertStringContainsString('value="'.$ditolak->id.'"', $html);
        $this->assertStringNotContainsString('value="'.$masihBerjalan->id.'"', $html);

        // Ketujuh dasarnya ditulis di muka, bukan hanya tersembunyi di dropdown.
        foreach (KeberatanInformasi::JENIS as $label) {
            $this->assertStringContainsString($label, $html);
        }
    }

    public function test_keberatan_atas_permohonan_yang_belum_layak_ditolak_server(): void
    {
        $masihBerjalan = $this->permohonan('diproses', now()->addDays(5)->toDateTimeString());

        $this->actingAs($this->pemohon, 'pemohon')
            ->post(route('akun.keberatan.store'), [
                'permohonan_id' => $masihBerjalan->id,
                'jenis_keberatan' => 'permohonan_ditolak',
                'kasus_posisi' => 'Mencoba menembus daftar pilihan.',
            ])
            ->assertSessionHasErrors('permohonan_id');

        $this->assertSame(0, KeberatanInformasi::where('permohonan_id', $masihBerjalan->id)->count());
    }

    public function test_rincian_keberatan_punya_halamannya_sendiri(): void
    {
        $permohonan = $this->permohonan('ditolak');

        $keberatan = KeberatanInformasi::create([
            'permohonan_id' => $permohonan->id,
            'pemohon_id' => $this->pemohon->id,
            'jenis_keberatan' => 'permohonan_ditolak',
            'alasan_keberatan' => 'Uji rincian keberatan.',
            'kasus_posisi' => 'Uji rincian keberatan.',
            'status' => 'diajukan',
        ]);

        $keberatan->refresh();

        $html = $this->actingAs($this->pemohon, 'pemohon')
            ->get(route('akun.keberatan.show', $keberatan->id))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString((string) $keberatan->kode_keberatan, $html);
        $this->assertStringContainsString('Uji rincian keberatan.', $html);
        // Judul dipecah dua warna oleh helper `judulDua`, jadi yang diperiksa
        // isi partial alurnya, bukan judul yang tidak pernah utuh di HTML.
        $this->assertStringContainsString('Sedang ditelaah dan disiapkan tanggapannya oleh petugas PPID.', $html);

        // Barisnya di daftar menuju ke sana.
        $this->actingAs($this->pemohon, 'pemohon')
            ->get(route('akun.keberatan.index'))
            ->assertOk()
            ->assertSee(route('akun.keberatan.show', $keberatan->id), false);
    }

    public function test_keberatan_milik_orang_lain_tidak_bisa_dibuka(): void
    {
        $lain = Pemohon::create([
            'nama' => 'Pemohon Lain 101',
            'email' => 'uji-portal-101-lain@example.test',
            'no_hp' => '08000000102',
            'jenis_pemohon' => 'perorangan',
            'status_verifikasi' => 'terverifikasi',
            'email_verified_at' => now(),
            'password' => 'RahasiaUji12345',
        ]);

        $permohonan = PermohonanInformasi::create([
            'pemohon_id' => $lain->id,
            'rincian_informasi' => 'Milik orang lain',
            'status' => 'diajukan',
        ]);

        $keberatan = KeberatanInformasi::create([
            'permohonan_id' => $permohonan->id,
            'pemohon_id' => $lain->id,
            'jenis_keberatan' => 'permohonan_ditolak',
            'alasan_keberatan' => 'Bukan milik penguji.',
            'kasus_posisi' => 'Bukan milik penguji.',
            'status' => 'diajukan',
        ]);

        $this->actingAs($this->pemohon, 'pemohon')
            ->get(route('akun.keberatan.show', $keberatan->id))
            ->assertNotFound();
    }

    public function test_alur_pemohon_selalu_tiga_langkah(): void
    {
        $permohonan = $this->permohonan('diajukan');
        $this->assertSame('diajukan', $permohonan->tahapAlurPortal());

        // Seluruh perpindahan internal menyatu di satu langkah: Diproses.
        foreach (['diverifikasi', 'diproses', 'revisi', 'menunggu_approval'] as $status) {
            DB::table('permohonan_informasi')->where('id', $permohonan->id)->update(['status' => $status]);

            $this->assertSame(
                'diproses',
                $permohonan->fresh()->tahapAlurPortal(),
                "Status '$status' seharusnya terbaca sebagai Diproses oleh pemohon."
            );
        }

        foreach (['selesai', 'ditolak', 'kedaluwarsa'] as $status) {
            DB::table('permohonan_informasi')->where('id', $permohonan->id)->update(['status' => $status]);

            $this->assertSame('selesai', $permohonan->fresh()->tahapAlurPortal());
        }
    }

    public function test_histori_memakai_alur_yang_sama_untuk_kedua_daftar(): void
    {
        $permohonan = $this->permohonan('diproses');

        PermohonanLogStatus::create([
            'permohonan_id' => $permohonan->id,
            'status_sebelumnya' => 'diproses',
            'status_baru' => 'revisi',
        ]);

        KeberatanInformasi::create([
            'permohonan_id' => $permohonan->id,
            'pemohon_id' => $this->pemohon->id,
            'jenis_keberatan' => 'permintaan_tidak_ditanggapi',
            'alasan_keberatan' => 'Uji histori keberatan.',
            'kasus_posisi' => 'Uji histori keberatan.',
            'status' => 'diproses',
        ]);

        $html = $this->actingAs($this->pemohon, 'pemohon')
            ->get(route('akun.histori'))
            ->assertOk()
            ->getContent();

        // Dua daftar, dua alur — dan tidak satu pun putaran internal yang bocor.
        $this->assertSame(
            2,
            substr_count($html, 'Sedang ditelaah dan disiapkan tanggapannya oleh petugas PPID.'),
            'Alur tiga langkah belum dipasang di kedua daftar histori.'
        );
        $this->assertStringNotContainsString('Revisi', $html);

        // Tenggat kerja petugas tidak ditayangkan ke pemohon.
        $this->assertStringNotContainsString('Batas Waktu Tanggapan', $html);
    }

    public function test_tanggal_tanggapan_diambil_dari_perpindahan_ke_selesai(): void
    {
        $permohonan = $this->permohonan('selesai');

        // Kolom `tanggal_tanggapan` diisi status akhir mana pun dan tidak
        // pernah ditulis ulang, jadi ia bisa memuat tanggal yang bukan tanggal
        // selesainya. Yang dipakai portal adalah perpindahan ke Selesai.
        DB::table('permohonan_informasi')
            ->where('id', $permohonan->id)
            ->update(['tanggal_tanggapan' => now()->subDays(30)]);

        $selesaiPada = now()->subDays(2);

        $log = PermohonanLogStatus::create([
            'permohonan_id' => $permohonan->id,
            'status_sebelumnya' => 'diproses',
            'status_baru' => 'selesai',
        ]);

        // `created_at` diisi Eloquent sendiri; dimundurkan lewat query supaya
        // tanggalnya benar-benar berbeda dari `tanggal_tanggapan`.
        DB::table('permohonan_log_status')->where('id', $log->id)->update(['created_at' => $selesaiPada]);

        $this->assertSame(
            $selesaiPada->format('Y-m-d'),
            $permohonan->fresh()->tanggalSelesaiPortal()?->format('Y-m-d')
        );

        $html = $this->actingAs($this->pemohon, 'pemohon')
            ->get(route('akun.permohonan.show', $permohonan->id))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString($selesaiPada->translatedFormat('d F Y'), $html);
        $this->assertStringNotContainsString('Batas Waktu Tanggapan', $html);
    }

    public function test_rincian_menampilkan_alur_bukan_jejak_status(): void
    {
        $permohonan = $this->permohonan('diproses');

        // Putaran internal yang tercatat di panel; tidak satu pun boleh bocor
        // sebagai langkah tersendiri di sisi pemohon.
        foreach ([['diajukan', 'diverifikasi'], ['diverifikasi', 'diproses'], ['diproses', 'revisi']] as [$dari, $ke]) {
            PermohonanLogStatus::create([
                'permohonan_id' => $permohonan->id,
                'status_sebelumnya' => $dari,
                'status_baru' => $ke,
            ]);
        }

        $html = $this->actingAs($this->pemohon, 'pemohon')
            ->get(route('akun.permohonan.show', $permohonan->id))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('Sedang ditelaah dan disiapkan tanggapannya oleh petugas PPID.', $html);
        $this->assertStringNotContainsString('Jejak', $html);
        $this->assertStringNotContainsString('Revisi', $html);
        $this->assertStringNotContainsString('Diverifikasi', $html);
    }
}
