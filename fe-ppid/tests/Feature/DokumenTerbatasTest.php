<?php

namespace Tests\Feature;

use App\Models\InformasiPublik;
use App\Models\InformasiPublikFile;
use App\Models\KategoriInformasi;
use App\Models\Pemohon;
use App\Models\PermohonanInformasi;
use App\Support\AksesDokumen;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Dokumen berunduhan terbatas (langkah 83).
 *
 * Yang diuji satu aturan: melihat terbuka untuk siapa saja, mengunduh hanya
 * untuk pemohon yang permohonannya atas dokumen itu sudah disetujui petugas.
 */
class DokumenTerbatasTest extends TestCase
{
    use DatabaseTransactions;

    private InformasiPublik $dokumen;

    private string $path = 'uploads/informasi-publik/uji/dokumen-terbatas-uji.pdf';

    protected function setUp(): void
    {
        parent::setUp();

        Storage::disk('dokumen_terbatas')->put($this->path, '%PDF-1.4 uji');

        $kategori = KategoriInformasi::first();

        $this->dokumen = InformasiPublik::forceCreate([
            'kategori_id' => $kategori?->id,
            'judul' => 'Dokumen Uji Terbatas '.Str::random(6),
            'slug' => 'dokumen-uji-terbatas-'.Str::lower(Str::random(8)),
            'status' => 'published',
            'tanggal_publikasi' => now()->toDateString(),
            'tautan' => 'https://contoh.test/laporan-tahunan',
            'unduhan_terbatas' => true,
        ]);

        InformasiPublikFile::forceCreate([
            'informasi_publik_id' => $this->dokumen->id,
            'nama_file' => 'dokumen-terbatas-uji.pdf',
            'path_file' => $this->path,
            'tipe_file' => 'application/pdf',
            'urutan' => 0,
        ]);

        $this->dokumen->load('files');
    }

    protected function tearDown(): void
    {
        Storage::disk('dokumen_terbatas')->delete($this->path);

        parent::tearDown();
    }

    private function pemohon(): Pemohon
    {
        return Pemohon::forceCreate([
            'nama' => 'Pemohon Uji 83',
            'email' => 'uji83-'.Str::lower(Str::random(8)).'@contoh.test',
            'password' => bcrypt('RahasiaKuat123'),
            'status_verifikasi' => 'terverifikasi',
        ]);
    }

    private function permohonan(Pemohon $pemohon, string $status): PermohonanInformasi
    {
        return PermohonanInformasi::forceCreate([
            'kode_permohonan' => 'UJI83-'.Str::upper(Str::random(6)),
            'pemohon_id' => $pemohon->id,
            'informasi_publik_id' => $this->dokumen->id,
            'rincian_informasi' => 'uji',
            'tujuan_penggunaan' => 'uji',
            'cara_memperoleh' => array_key_first(PermohonanInformasi::CARA_MEMPEROLEH),
            'format_informasi' => 'softcopy',
            'cara_pengiriman' => 'email',
            'status' => $status,
            'tanggal_permohonan' => now(),
            'batas_waktu_tanggapan' => now()->addWeekdays(10),
        ]);
    }

    /**
     * Bentuk sebuah URL sebagaimana `@js()` mencetaknya ke atribut Alpine.
     *
     * Blade menanamnya sebagai literal JavaScript, jadi garis miringnya lolos
     * menjadi `\/`. Membandingkan URL mentah akan selalu gagal walau
     * halamannya benar.
     */
    private function sepertiDiJs(string $url): string
    {
        return trim(json_encode($url), '"');
    }

    public function test_pratinjau_terbuka_untuk_tamu(): void
    {
        $this->get(route('ppid.dokumen.pratinjau', $this->dokumen->id))->assertOk();
    }

    public function test_tamu_tidak_bisa_mengunduh(): void
    {
        $this->get(route('ppid.dokumen.unduh', $this->dokumen->id))
            ->assertRedirect(route('ppid.dokumen.pratinjau', $this->dokumen->id));
    }

    public function test_pemohon_tanpa_permohonan_tidak_bisa_mengunduh(): void
    {
        $pemohon = $this->pemohon();

        $this->assertSame('ajukan', AksesDokumen::keadaanUnduh($this->dokumen, $pemohon)['keadaan']);

        $this->actingAs($pemohon, 'pemohon')
            ->get(route('ppid.dokumen.unduh', $this->dokumen->id))
            ->assertRedirect(route('ppid.dokumen.pratinjau', $this->dokumen->id));
    }

    public function test_permohonan_yang_belum_diputus_belum_membuka_unduhan(): void
    {
        $pemohon = $this->pemohon();

        foreach (['diajukan', 'diverifikasi', 'diproses', 'menunggu_approval'] as $status) {
            $permohonan = $this->permohonan($pemohon, $status);

            $this->assertSame(
                'menunggu',
                AksesDokumen::keadaanUnduh($this->dokumen, $pemohon)['keadaan'],
                "Status {$status} seharusnya belum membuka unduhan"
            );

            $this->actingAs($pemohon, 'pemohon')
                ->get(route('ppid.dokumen.unduh', $this->dokumen->id))
                ->assertRedirect(route('ppid.dokumen.pratinjau', $this->dokumen->id));

            $permohonan->forceDelete();
        }
    }

    public function test_permohonan_ditolak_tidak_membuka_unduhan(): void
    {
        $pemohon = $this->pemohon();
        $this->permohonan($pemohon, 'ditolak');

        // Bukan 'menunggu': keputusannya sudah ada, dan hasilnya tidak.
        // Pemohon boleh mengajukan lagi, karena itu keadaannya 'ajukan'.
        $this->assertSame('ajukan', AksesDokumen::keadaanUnduh($this->dokumen, $pemohon)['keadaan']);

        $this->actingAs($pemohon, 'pemohon')
            ->get(route('ppid.dokumen.unduh', $this->dokumen->id))
            ->assertRedirect(route('ppid.dokumen.pratinjau', $this->dokumen->id));
    }

    public function test_permohonan_disetujui_membuka_unduhan(): void
    {
        $pemohon = $this->pemohon();
        $permohonan = $this->permohonan($pemohon, 'selesai');

        $keadaan = AksesDokumen::keadaanUnduh($this->dokumen, $pemohon);

        $this->assertSame('terbuka', $keadaan['keadaan']);
        $this->assertSame($permohonan->id, $keadaan['permohonan']->id);

        $this->actingAs($pemohon, 'pemohon')
            ->get(route('ppid.dokumen.unduh', $this->dokumen->id))
            ->assertOk()
            ->assertDownload(Str::slug($this->dokumen->judul).'.pdf');
    }

    /**
     * Persetujuan berlaku bagi pemohonnya sendiri, bukan bagi dokumennya.
     *
     * Kalau salah, satu persetujuan akan membuka dokumen itu untuk semua orang
     * yang sudah punya akun — dan keputusan petugas kehilangan artinya.
     */
    public function test_persetujuan_tidak_berlaku_untuk_pemohon_lain(): void
    {
        $pemilik = $this->pemohon();
        $this->permohonan($pemilik, 'selesai');

        $orangLain = $this->pemohon();

        $this->assertSame('ajukan', AksesDokumen::keadaanUnduh($this->dokumen, $orangLain)['keadaan']);

        $this->actingAs($orangLain, 'pemohon')
            ->get(route('ppid.dokumen.unduh', $this->dokumen->id))
            ->assertRedirect(route('ppid.dokumen.pratinjau', $this->dokumen->id));
    }

    /**
     * Persetujuan atas dokumen lain tidak membuka dokumen ini.
     */
    public function test_persetujuan_dokumen_lain_tidak_membuka_dokumen_ini(): void
    {
        $pemohon = $this->pemohon();

        $permohonan = $this->permohonan($pemohon, 'selesai');
        $permohonan->forceFill(['informasi_publik_id' => null])->save();

        $this->assertSame('ajukan', AksesDokumen::keadaanUnduh($this->dokumen, $pemohon)['keadaan']);
    }

    /**
     * Dokumen tanpa penanda terbatas tetap bisa diunduh siapa saja.
     */
    public function test_dokumen_biasa_tetap_bebas_diunduh(): void
    {
        $this->dokumen->forceFill(['unduhan_terbatas' => false])->save();
        // Berkasnya ikut pindah ke disk publik saat penandanya dimatikan lewat
        // panel; di tes ini pemindahannya ditiru langsung.
        Storage::disk('public')->put($this->path, Storage::disk('dokumen_terbatas')->get($this->path));

        $this->assertSame('bebas', AksesDokumen::keadaanUnduh($this->dokumen->fresh('files'), null)['keadaan']);

        $this->get(route('ppid.dokumen.unduh', $this->dokumen->id))->assertOk();

        Storage::disk('public')->delete($this->path);
    }

    /**
     * Alamat berkasnya tidak boleh muncul di daftar Informasi Publik.
     *
     * Kalau muncul, gerbang unduh bisa dilewati cukup dengan menyalinnya —
     * berkasnya memang tidak lagi di folder publik, tetapi kebocoran alamat
     * tetap menandakan tombolnya menunjuk pintu yang salah.
     */
    public function test_daftar_tidak_membocorkan_alamat_berkas(): void
    {
        $kategori = KategoriInformasi::find($this->dokumen->kategori_id);

        if (!$kategori) {
            $this->markTestSkipped('Tidak ada kategori informasi untuk diuji.');
        }

        $html = $this->get(route('ppid.information', $kategori->slug))->assertOk()->getContent();

        $this->assertStringNotContainsString('dokumen-terbatas-uji.pdf', $html);
        // Tombolnya membuka dialog; yang tercetak alamat rute unduh, bukan berkasnya.
        $this->assertStringContainsString('buka-dialog-dokumen', $html);
        /*
         * Alamatnya ditanam lewat `@js()`, yang mengeluarkan JSON — garis
         * miringnya lolos jadi `\/`. Dibandingkan dalam bentuk yang sama,
         * bukan sebagai URL mentah.
         */
        $this->assertStringContainsString($this->sepertiDiJs(route('ppid.dokumen.unduh', $this->dokumen->id)), $html);
    }

    /**
     * Dialog memuat kedua pilihan: tautan luar untuk dibaca, dan rute unduh
     * yang dijaga untuk salinannya.
     */
    public function test_dialog_memuat_tautan_lihat_dan_rute_unduh(): void
    {
        $kategori = KategoriInformasi::find($this->dokumen->kategori_id);

        if (!$kategori) {
            $this->markTestSkipped('Tidak ada kategori informasi untuk diuji.');
        }

        $html = $this->get(route('ppid.information', $kategori->slug))->assertOk()->getContent();

        $this->assertStringContainsString($this->sepertiDiJs('https://contoh.test/laporan-tahunan'), $html);
        $this->assertStringContainsString($this->sepertiDiJs(route('ppid.dokumen.unduh', $this->dokumen->id)), $html);
    }

    /**
     * Dialog dua pilihan tampil di keempat daftar yang disebut langkah 83.
     *
     * Indeks seluruh dokumen dan halaman per kategori dulu memakai salinan
     * markup masing-masing; tes ini yang menahannya agar tidak berpisah lagi.
     */
    public function test_dialog_tampil_di_indeks_dan_halaman_kategori(): void
    {
        $kategori = KategoriInformasi::find($this->dokumen->kategori_id);

        if (!$kategori) {
            $this->markTestSkipped('Tidak ada kategori informasi untuk diuji.');
        }

        foreach ([route('ppid.information.index'), route('ppid.information', $kategori->slug)] as $url) {
            $html = $this->get($url)->assertOk()->getContent();

            $this->assertStringContainsString('buka-dialog-dokumen', $html, "Tombol dialog hilang di {$url}");
            $this->assertStringContainsString('Di Lihat Saja', $html, "Markup dialog hilang di {$url}");
        }
    }

    /**
     * Entri yang tidak punya tautan maupun berkas tetap menawarkan
     * Mohon Dokumen, bukan dialog kosong.
     */
    public function test_entri_tanpa_isi_tidak_membuka_dialog(): void
    {
        InformasiPublikFile::where('informasi_publik_id', $this->dokumen->id)->delete();
        $this->dokumen->forceFill(['tautan' => null])->save();

        $kategori = KategoriInformasi::find($this->dokumen->kategori_id);

        if (!$kategori) {
            $this->markTestSkipped('Tidak ada kategori informasi untuk diuji.');
        }

        $html = $this->get(route('ppid.information', $kategori->slug))->assertOk()->getContent();

        // Barisnya tetap terdaftar…
        $this->assertStringContainsString((string) $this->dokumen->judul, $html);
        // …tetapi judulnya tidak ikut jadi muatan dialog, dan tombolnya
        // menawarkan Mohon Dokumen.
        $this->assertStringNotContainsString(
            'judul: '.$this->sepertiDiJs((string) $this->dokumen->judul),
            $html
        );
        $this->assertStringContainsString('Mohon Dokumen', $html);
    }

    /**
     * Halaman akses tetap terbuka walau berkas salinannya belum diunggah.
     *
     * Di keadaan itu ia justru berguna sebagai penjelas: tautan bacanya ada,
     * tombol unduhnya tidak, dan alasannya dikatakan.
     */
    public function test_halaman_akses_terbuka_tanpa_berkas(): void
    {
        InformasiPublikFile::where('informasi_publik_id', $this->dokumen->id)->delete();

        $this->get(route('ppid.dokumen.pratinjau', $this->dokumen->id))
            ->assertOk()
            ->assertSee('Salinan untuk diunduh belum tersedia', false)
            ->assertSee('https://contoh.test/laporan-tahunan', false);
    }
}
