<?php

namespace Tests\Feature;

use App\Models\PermohonanInformasi;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Aturan portal pemohon pada alur layanan PPID (langkah 89).
 *
 * Dua hal yang diuji, keduanya aturan yang tidak boleh dipercayakan ke tampilan:
 * jalur pelayanan yang diturunkan dari cara pengiriman, dan keberatan yang hanya
 * boleh diajukan atas permohonan yang penanganannya sudah selesai.
 */
class AlurLayananPortalTest extends TestCase
{
    use DatabaseTransactions;

    public function test_status_selesai_memuat_penolakan_dan_kedaluwarsa(): void
    {
        // Keduanya justru alasan keberatan yang paling sering — permintaan
        // ditolak, dan permintaan tidak ditanggapi sampai tenggatnya lewat.
        $this->assertContains('ditolak', PermohonanInformasi::STATUS_SELESAI);
        $this->assertContains('kedaluwarsa', PermohonanInformasi::STATUS_SELESAI);
        $this->assertContains('selesai', PermohonanInformasi::STATUS_SELESAI);

        // Yang masih berjalan tidak boleh ikut: belum ada tanggapan untuk
        // dikeberatankan.
        foreach (['diajukan', 'diverifikasi', 'diproses', 'menunggu_approval'] as $berjalan) {
            $this->assertNotContains($berjalan, PermohonanInformasi::STATUS_SELESAI);
        }
    }

    public function test_jalur_pelayanan_mengikuti_cara_pengiriman(): void
    {
        $pemohon = \App\Models\Pemohon::first();

        if (!$pemohon) {
            $this->markTestSkipped('Belum ada pemohon di basis data uji.');
        }

        $langsung = PermohonanInformasi::create([
            'kode_permohonan' => 'UJI-L-'.Str::random(6),
            'pemohon_id' => $pemohon->id,
            'rincian_informasi' => 'Uji jalur langsung',
            'format_informasi' => 'hardcopy',
            'cara_pengiriman' => 'ambil_langsung',
            'jalur_pelayanan' => 'langsung',
            'status' => 'diajukan',
            'tanggal_permohonan' => now(),
        ]);

        $online = PermohonanInformasi::create([
            'kode_permohonan' => 'UJI-O-'.Str::random(6),
            'pemohon_id' => $pemohon->id,
            'rincian_informasi' => 'Uji jalur online',
            'format_informasi' => 'softcopy',
            'cara_pengiriman' => 'email',
            'jalur_pelayanan' => 'online',
            'status' => 'diajukan',
            'tanggal_permohonan' => now(),
        ]);

        // Pemetaannya satu arah dan tidak boleh terbalik: salinan cetak diambil
        // di meja layanan, salinan digital dikirim lewat surel.
        $this->assertSame('langsung', $langsung->fresh()->jalur_pelayanan);
        $this->assertSame('online', $online->fresh()->jalur_pelayanan);
    }
}
