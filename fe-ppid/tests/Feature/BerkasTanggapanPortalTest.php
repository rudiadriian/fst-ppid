<?php

namespace Tests\Feature;

use App\Models\Pemohon;
use App\Models\PermohonanInformasi;
use App\Models\PermohonanTanggapanFile;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Berkas tanggapan di sisi pemohon (langkah 97).
 *
 * Sebelum ini berkas jawaban petugas tidak punya tempat sama sekali di portal:
 * loncengnya memberitahukan "Berkas Tanggapan…" sementara halaman rincian
 * permohonan tidak menampilkan satu pun berkas, dan tidak ada jalur unduh.
 *
 * Yang dijaga di sini: berkasnya tampil dan bisa diunduh hanya setelah
 * permohonannya diserahkan, dan hanya oleh pemiliknya.
 */
class BerkasTanggapanPortalTest extends TestCase
{
    use DatabaseTransactions;

    private function pemohon(string $surel): Pemohon
    {
        return Pemohon::create([
            'nama' => 'Uji Tanggapan',
            'email' => $surel,
            'no_hp' => '08000000097',
            'jenis_pemohon' => 'perorangan',
            'status_verifikasi' => 'terverifikasi',
            'email_verified_at' => now(),
            'password' => 'RahasiaUji12345',
        ]);
    }

    private function permohonan(Pemohon $pemohon, string $status): PermohonanInformasi
    {
        return PermohonanInformasi::create([
            'kode_permohonan' => 'UJI-97-'.Str::random(6),
            'pemohon_id' => $pemohon->id,
            'rincian_informasi' => 'Uji berkas tanggapan',
            'status' => $status,
            'tanggal_permohonan' => now(),
        ]);
    }

    private function berkas(PermohonanInformasi $permohonan, string $path): PermohonanTanggapanFile
    {
        return PermohonanTanggapanFile::create([
            'permohonan_id' => $permohonan->id,
            'nama_file' => 'Tanggapan.pdf',
            'path_file' => $path,
        ]);
    }

    public function test_berkas_tampil_setelah_permohonan_diserahkan(): void
    {
        $pemohon = $this->pemohon('uji-tanggapan-97@example.test');
        $permohonan = $this->permohonan($pemohon, 'selesai');
        $berkas = $this->berkas($permohonan, 'uploads/permohonan/2026/08/uji-97.pdf');

        $this->actingAs($pemohon, 'pemohon')
            ->get(route('akun.permohonan.show', $permohonan->id))
            ->assertOk()
            ->assertSee('Berkas Tanggapan', false)
            ->assertSee(route('akun.permohonan.berkas-tanggapan', $berkas->id), false);
    }

    /** Selama masih disiapkan, berkasnya belum menjadi jawaban resmi. */
    public function test_berkas_tidak_tampil_selama_masih_diproses(): void
    {
        $pemohon = $this->pemohon('uji-tanggapan-97b@example.test');
        $permohonan = $this->permohonan($pemohon, 'diproses');
        $berkas = $this->berkas($permohonan, 'uploads/permohonan/2026/08/uji-97b.pdf');

        $this->actingAs($pemohon, 'pemohon')
            ->get(route('akun.permohonan.show', $permohonan->id))
            ->assertOk()
            ->assertDontSee(route('akun.permohonan.berkas-tanggapan', $berkas->id), false);
    }

    public function test_unduhan_ditolak_selama_belum_diserahkan(): void
    {
        $pemohon = $this->pemohon('uji-tanggapan-97c@example.test');
        $permohonan = $this->permohonan($pemohon, 'menunggu_approval');
        $berkas = $this->berkas($permohonan, 'uploads/permohonan/2026/08/uji-97c.pdf');

        $this->actingAs($pemohon, 'pemohon')
            ->get(route('akun.permohonan.berkas-tanggapan', $berkas->id))
            ->assertStatus(403);
    }

    public function test_unduhan_berkas_akun_lain_ditolak(): void
    {
        $pemilik = $this->pemohon('uji-tanggapan-97d@example.test');
        $lain = $this->pemohon('uji-tanggapan-97e@example.test');

        $permohonan = $this->permohonan($pemilik, 'selesai');
        $berkas = $this->berkas($permohonan, 'uploads/permohonan/2026/08/uji-97d.pdf');

        $this->actingAs($lain, 'pemohon')
            ->get(route('akun.permohonan.berkas-tanggapan', $berkas->id))
            ->assertStatus(403);
    }

    public function test_pemilik_bisa_mengunduh_berkasnya(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('uploads/permohonan/2026/08/uji-97f.pdf', 'isi berkas uji');

        $pemohon = $this->pemohon('uji-tanggapan-97f@example.test');
        $permohonan = $this->permohonan($pemohon, 'disetujui');
        $berkas = $this->berkas($permohonan, 'uploads/permohonan/2026/08/uji-97f.pdf');

        $this->actingAs($pemohon, 'pemohon')
            ->get(route('akun.permohonan.berkas-tanggapan', $berkas->id))
            ->assertOk()
            ->assertDownload('Tanggapan.pdf');
    }

    /**
     * Berkas yang barisnya ada tetapi fisiknya hilang (UAT 21): pemohon
     * dikembalikan ke rincian permohonan dengan pesan, bukan halaman
     * "404 | NOT FOUND" kosong.
     */
    public function test_berkas_yang_hilang_kembali_ke_rincian_dengan_pesan(): void
    {
        $this->palsukanDisk();

        $pemohon = $this->pemohon('uji-tanggapan-97g@example.test');
        $permohonan = $this->permohonan($pemohon, 'selesai');
        $berkas = $this->berkas($permohonan, 'uploads/permohonan/2026/08/tidak-ada.pdf');

        $this->actingAs($pemohon, 'pemohon')
            ->get(route('akun.permohonan.berkas-tanggapan', $berkas->id))
            ->assertRedirect(route('akun.permohonan.show', $permohonan->id))
            ->assertSessionHasErrors('berkas');
    }

    /** Nama lampiran tanpa ekstensi diberi ekstensi berkas aslinya. */
    public function test_nama_unduhan_diberi_ekstensi(): void
    {
        $this->palsukanDisk();
        Storage::disk('public')->put('uploads/permohonan/2026/09/uji-21a.pdf', 'isi');

        $pemohon = $this->pemohon('uji-tanggapan-21a@example.test');
        $permohonan = $this->permohonan($pemohon, 'selesai');
        $berkas = PermohonanTanggapanFile::create([
            'permohonan_id' => $permohonan->id,
            'nama_file' => 'Laporan tahunan 2025',
            'path_file' => 'uploads/permohonan/2026/09/uji-21a.pdf',
        ]);

        $this->actingAs($pemohon, 'pemohon')
            ->get(route('akun.permohonan.berkas-tanggapan', $berkas->id))
            ->assertOk()
            ->assertDownload('Laporan tahunan 2025.pdf');
    }

    /** Path lama berawalan `storage/` tetap ditemukan. */
    public function test_path_berawalan_storage_tetap_ditemukan(): void
    {
        $this->palsukanDisk();
        Storage::disk('public')->put('uploads/permohonan/2026/09/uji-21b.pdf', 'isi');

        $pemohon = $this->pemohon('uji-tanggapan-21b@example.test');
        $permohonan = $this->permohonan($pemohon, 'selesai');
        $berkas = $this->berkas($permohonan, '/storage/uploads/permohonan/2026/09/uji-21b.pdf');

        $this->actingAs($pemohon, 'pemohon')
            ->get(route('akun.permohonan.berkas-tanggapan', $berkas->id))
            ->assertOk()
            ->assertDownload('Tanggapan.pdf');
    }

    /**
     * Berkas yang dipilih dari arsip lalu dipindah ke disk terbatas (atau
     * ditulis api-ppid ke MEDIA_ROOT lain) tetap bisa diunduh pemiliknya.
     */
    public function test_berkas_di_disk_lain_tetap_ditemukan(): void
    {
        $this->palsukanDisk();
        Storage::disk('dokumen_terbatas')->put('uploads/permohonan/2026/09/uji-21c.pdf', 'isi');
        Storage::disk('media')->put('uploads/permohonan/2026/09/uji-21d.pdf', 'isi');

        $pemohon = $this->pemohon('uji-tanggapan-21c@example.test');
        $permohonan = $this->permohonan($pemohon, 'selesai');
        $terbatas = $this->berkas($permohonan, 'uploads/permohonan/2026/09/uji-21c.pdf');
        $media = $this->berkas($permohonan, 'uploads/permohonan/2026/09/uji-21d.pdf');

        $this->actingAs($pemohon, 'pemohon')
            ->get(route('akun.permohonan.berkas-tanggapan', $terbatas->id))
            ->assertOk();

        $this->actingAs($pemohon, 'pemohon')
            ->get(route('akun.permohonan.berkas-tanggapan', $media->id))
            ->assertOk();
    }

    public function test_path_keluar_folder_ditolak(): void
    {
        $this->palsukanDisk();

        $pemohon = $this->pemohon('uji-tanggapan-21e@example.test');
        $permohonan = $this->permohonan($pemohon, 'selesai');
        $berkas = $this->berkas($permohonan, 'uploads/../../.env');

        $this->actingAs($pemohon, 'pemohon')
            ->get(route('akun.permohonan.berkas-tanggapan', $berkas->id))
            ->assertRedirect(route('akun.permohonan.show', $permohonan->id));
    }

    private function palsukanDisk(): void
    {
        Storage::fake('public');
        Storage::fake('media');
        Storage::fake('dokumen_terbatas');
    }
}
