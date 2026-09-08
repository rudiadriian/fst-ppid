<?php

namespace Tests\Feature;

use App\Models\InformasiDikecualikan;
use App\Models\InformasiPublik;
use App\Models\InformasiPublikFile;
use App\Models\KategoriInformasi;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Isian "Tautan salinan (Mengunduh)" pada modul Informasi Publik dan Informasi
 * Dikecualikan.
 *
 * Kolomnya baru; yang diuji di sini bahwa panel benar-benar bisa menyimpannya
 * — bukan menjawab 500 — dan bahwa alamat yang tidak berupa http/https
 * ditolak sebagai galat isian, bukan diterima diam-diam.
 */
class TautanUnduhInformasiTest extends TestCase
{
    use DatabaseTransactions;

    private string $password = 'RahasiaKuat123';

    private string $tanda;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tanda = Str::random(8);

        config([
            'ppid.akun.recaptcha_aktif' => false,
            'ppid.akun.gagal_per_tahap' => 99,
        ]);
    }

    private function token(): string
    {
        $user = User::create([
            'name' => "Uji Tautan $this->tanda",
            'email' => 'uji.tautan.'.Str::lower($this->tanda).'@uji.test',
            'password' => Hash::make($this->password),
            'role_id' => Role::where('slug', 'super-admin')->value('id'),
            'is_active' => true,
        ]);

        return $this->postJson('/api/v1/auth/sign-in', [
            'email' => $user->email,
            'password' => $this->password,
        ])->assertOk()->json('access_token');
    }

    private function informasiPublik(): InformasiPublik
    {
        return InformasiPublik::forceCreate([
            'kategori_id' => KategoriInformasi::value('id'),
            'judul' => "Uji Tautan Unduh $this->tanda",
            'slug' => 'uji-tautan-unduh-'.Str::lower($this->tanda),
            'status' => 'published',
            'unduhan_terbatas' => true,
        ]);
    }

    private function informasiDikecualikan(): InformasiDikecualikan
    {
        return InformasiDikecualikan::forceCreate([
            'judul' => "Uji Dikecualikan $this->tanda",
            'slug' => 'uji-dikecualikan-'.Str::lower($this->tanda),
            'alasan_pengecualian' => 'uji',
            'status' => 'published',
        ]);
    }

    public function test_informasi_publik_menerima_tautan_unduh(): void
    {
        $baris = $this->informasiPublik();

        $this->withToken($this->token())
            ->putJson('/api/v1/informasi-publik/'.$baris->id, [
                'tautan' => 'https://foodstation.id/laporan-tahunan-fstj/',
                'tautan_unduh' => 'https://foodstation.id/unduh/laporan-tahunan-2025.pdf',
            ])
            ->assertOk();

        $baris->refresh();

        $this->assertSame('https://foodstation.id/laporan-tahunan-fstj/', $baris->tautan);
        $this->assertSame('https://foodstation.id/unduh/laporan-tahunan-2025.pdf', $baris->tautan_unduh);
    }

    public function test_informasi_publik_menolak_tautan_unduh_bukan_http(): void
    {
        $baris = $this->informasiPublik();

        $this->withToken($this->token())
            ->putJson('/api/v1/informasi-publik/'.$baris->id, [
                'tautan_unduh' => 'javascript:alert(1)',
            ])
            ->assertStatus(422);

        $this->assertNull($baris->refresh()->tautan_unduh);
    }

    /** Isian kosong berarti menghapus alamatnya, bukan galat. */
    public function test_informasi_publik_menerima_tautan_unduh_kosong(): void
    {
        $baris = $this->informasiPublik();
        $baris->forceFill(['tautan_unduh' => 'https://contoh.test/lama.pdf'])->save();

        $this->withToken($this->token())
            ->putJson('/api/v1/informasi-publik/'.$baris->id, ['tautan_unduh' => ''])
            ->assertOk();

        $this->assertNull($baris->refresh()->tautan_unduh);
    }

    /**
     * Muatan penuh seperti yang dikirim formulir panel, bukan hanya kolom yang
     * sedang diuji.
     *
     * Formulir mengirim seluruh isiannya sekaligus — termasuk `files` dan
     * `unduhan_terbatas`, yang menjalankan pemindahan berkas di `afterSave()`.
     * Menguji satu kolom saja tidak melewati jalur itu, padahal di sanalah
     * penyimpanan bisa gagal.
     */
    public function test_informasi_publik_menerima_muatan_penuh_dari_panel(): void
    {
        $baris = $this->informasiPublik();

        $this->withToken($this->token())
            ->putJson('/api/v1/informasi-publik/'.$baris->id, [
                'kategori_id' => $baris->kategori_id,
                'judul' => $baris->judul,
                'judul_en' => null,
                'slug' => $baris->slug,
                'ringkasan' => 'Ringkasan uji.',
                'ringkasan_en' => null,
                'konten' => '<p>Isi uji.</p>',
                'konten_en' => null,
                'tautan' => 'https://foodstation.id/laporan-tahunan-fstj/',
                'tautan_unduh' => 'https://foodstation.id/unduh/laporan-tahunan-2025.pdf',
                'unduhan_terbatas' => true,
                'nomor_klasifikasi' => '1.1',
                'tanggal_publikasi' => now()->toDateString(),
                'status' => 'published',
                'files' => [],
            ])
            ->assertOk();

        $this->assertSame(
            'https://foodstation.id/unduh/laporan-tahunan-2025.pdf',
            $baris->refresh()->tautan_unduh
        );
    }

    /**
     * Muatan persis seperti yang tercatat pada UAT poin 7, termasuk lampiran
     * yang dikosongkan dan berkas yang sudah terlanjur ada di barisnya.
     *
     * Mengosongkan `files` berarti seluruh lampiran barisnya dihapus lalu
     * penyimpanannya diselaraskan — jalur itu yang tidak pernah tersentuh bila
     * barisnya memang belum punya lampiran sama sekali.
     */
    public function test_muatan_uat_dengan_lampiran_dikosongkan(): void
    {
        $baris = $this->informasiPublik();

        InformasiPublikFile::forceCreate([
            'informasi_publik_id' => $baris->id,
            'nama_file' => 'lampiran-lama.pdf',
            'path_file' => 'uploads/informasi-publik/uji-'.Str::lower($this->tanda).'.pdf',
            'tipe_file' => 'application/pdf',
            'urutan' => 0,
        ]);

        $this->withToken($this->token())
            ->putJson('/api/v1/informasi-publik/'.$baris->id, [
                'files' => [],
                'judul' => 'Annual Report',
                'judul_en' => 'Annual Report',
                'kategori_id' => $baris->kategori_id,
                'konten' => null,
                'konten_en' => null,
                'nomor_klasifikasi' => '7',
                'ringkasan' => 'Laporan tahunan perusahaan berisi kinerja dan capaian sepanjang tahun buku.',
                'ringkasan_en' => "The company's annual report covering performance and achievements throughout the financial year.",
                'slug' => $baris->slug,
                'status' => 'published',
                'tanggal_publikasi' => '2026-08-20',
                'tautan' => 'https://foodstation.id/laporan-tahunan-fstj/',
                'tautan_unduh' => 'https://ppid.foodstation.co.id/akun/permohonan/baru',
                'unduhan_terbatas' => true,
            ])
            ->assertOk();

        $baris->refresh();

        $this->assertSame('https://ppid.foodstation.co.id/akun/permohonan/baru', $baris->tautan_unduh);
        $this->assertSame(0, InformasiPublikFile::where('informasi_publik_id', $baris->id)->count());
    }

    public function test_informasi_dikecualikan_menerima_kedua_tautan(): void
    {
        $baris = $this->informasiDikecualikan();

        $this->withToken($this->token())
            ->putJson('/api/v1/informasi-dikecualikan/'.$baris->id, [
                'tautan' => 'https://foodstation.id/keterangan-pengecualian',
                'tautan_unduh' => 'https://foodstation.id/unduh/surat-pengecualian.pdf',
            ])
            ->assertOk();

        $baris->refresh();

        $this->assertSame('https://foodstation.id/keterangan-pengecualian', $baris->tautan);
        $this->assertSame('https://foodstation.id/unduh/surat-pengecualian.pdf', $baris->tautan_unduh);
    }
}
