<?php

namespace Tests\Feature;

use App\Models\KeberatanInformasi;
use App\Models\Pemohon;
use App\Models\PermohonanInformasi;
use App\Support\SlaLayanan;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Penomoran permohonan dan keberatan: bulan sebagai penanda, deret tahunan.
 *
 * `DatabaseTransactions` dengan alasan yang sama seperti
 * {@see PenomoranKeberatanTest}: sebagian skema `ppiddb` dibuat lewat DDL di
 * luar migration, jadi membangun ulang basis data akan menghapus yang tidak
 * bisa dikembalikan.
 */
class PenomoranBulananTest extends TestCase
{
    use DatabaseTransactions;

    private string $tanda;

    private int $urut = 0;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tanda = Str::random(8);
    }

    private function pemohon(): Pemohon
    {
        return Pemohon::create([
            'nama' => "Pemohon Uji $this->tanda",
            'email' => 'pemohon'.(++$this->urut).'.'.Str::lower($this->tanda).'@uji.test',
            'password' => Hash::make('RahasiaKuat123'),
            'jenis_pemohon' => 'pribadi',
        ]);
    }

    /** Sengaja tanpa `kode_permohonan`: nomornya harus lahir dari trigger. */
    private function permohonan(): PermohonanInformasi
    {
        return PermohonanInformasi::create([
            'pemohon_id' => $this->pemohon()->id,
            'rincian_informasi' => "Rincian uji $this->tanda",
            'status' => 'diajukan',
            'jalur_pelayanan' => 'online',
            'tanggal_permohonan' => now(),
            'batas_waktu_tanggapan' => SlaLayanan::batasPermohonan(),
            'batas_waktu_awal' => SlaLayanan::batasPermohonan(),
        ])->refresh();
    }

    private function keberatan(): KeberatanInformasi
    {
        $pemohon = $this->pemohon();

        $permohonan = PermohonanInformasi::create([
            'kode_permohonan' => 'UJI-'.$this->urut.'-'.$this->tanda,
            'pemohon_id' => $pemohon->id,
            'rincian_informasi' => "Rincian uji $this->tanda",
            'status' => 'selesai',
            'jalur_pelayanan' => 'online',
            'tanggal_permohonan' => now(),
            'batas_waktu_tanggapan' => SlaLayanan::batasPermohonan(),
            'batas_waktu_awal' => SlaLayanan::batasPermohonan(),
        ]);

        return KeberatanInformasi::create([
            'permohonan_id' => $permohonan->id,
            'pemohon_id' => $pemohon->id,
            'jenis_keberatan' => 'permohonan_ditolak',
            'alasan_keberatan' => "Alasan uji $this->tanda",
            'kasus_posisi' => "Kasus uji $this->tanda",
            'status' => 'diajukan',
            'jalur_pelayanan' => 'online',
            'tanggal_keberatan' => now(),
            'batas_waktu_tanggapan' => SlaLayanan::batasKeberatan(),
        ])->refresh();
    }

    public function test_nomor_permohonan_memakai_penanda_bulan(): void
    {
        $kode = (string) $this->permohonan()->kode_permohonan;

        // Segmen tengah enam angka — tahun dan bulan, bukan tanggal penuh.
        $this->assertMatchesRegularExpression(
            '#^PPID-FSTJ/'.now()->format('Ym').'/\d{3,}$#',
            $kode,
            "Nomor permohonan tidak berbentuk PPID-FSTJ/YYYYMM/NNN: $kode"
        );
    }

    public function test_nomor_keberatan_memakai_penanda_bulan(): void
    {
        $kode = (string) $this->keberatan()->kode_keberatan;

        $this->assertMatchesRegularExpression(
            '#^KBT-FSTJ/'.now()->format('Ym').'/\d{3,}$#',
            $kode,
            "Nomor keberatan tidak berbentuk KBT-FSTJ/YYYYMM/NNN: $kode"
        );
    }

    public function test_deret_permohonan_berlanjut_bukan_dimulai_ulang(): void
    {
        $pertama = (int) explode('/', (string) $this->permohonan()->kode_permohonan)[2];
        $kedua = (int) explode('/', (string) $this->permohonan()->kode_permohonan)[2];

        $this->assertSame($pertama + 1, $kedua, 'Urutan permohonan tidak berlanjut.');
    }

    public function test_deret_keberatan_berlanjut_bukan_dimulai_ulang(): void
    {
        $pertama = (int) explode('/', (string) $this->keberatan()->kode_keberatan)[2];
        $kedua = (int) explode('/', (string) $this->keberatan()->kode_keberatan)[2];

        $this->assertSame($pertama + 1, $kedua, 'Urutan keberatan tidak berlanjut.');
    }

    /**
     * Deret keduanya harus berdiri sendiri; nomor permohonan tidak boleh ikut
     * menggeser nomor keberatan, dan sebaliknya.
     */
    public function test_deret_permohonan_dan_keberatan_terpisah(): void
    {
        $this->permohonan();
        $sebelum = (int) explode('/', (string) $this->keberatan()->kode_keberatan)[2];
        $this->permohonan();
        $sesudah = (int) explode('/', (string) $this->keberatan()->kode_keberatan)[2];

        $this->assertSame($sebelum + 1, $sesudah, 'Nomor permohonan ikut menggeser deret keberatan.');
    }

    /**
     * Nomor harian bentuk lama tidak ikut dihitung — kalau ikut, deret barunya
     * akan melompat mengikuti urutan harian terakhir yang kebetulan tertinggi.
     */
    public function test_nomor_bentuk_lama_tidak_ikut_dihitung(): void
    {
        PermohonanInformasi::create([
            'kode_permohonan' => 'PPID-FSTJ/'.now()->format('Ymd').'/9999',
            'pemohon_id' => $this->pemohon()->id,
            'rincian_informasi' => "Rincian uji lama $this->tanda",
            'status' => 'diajukan',
            'jalur_pelayanan' => 'online',
            'tanggal_permohonan' => now(),
            'batas_waktu_tanggapan' => SlaLayanan::batasPermohonan(),
            'batas_waktu_awal' => SlaLayanan::batasPermohonan(),
        ]);

        $urutan = (int) explode('/', (string) $this->permohonan()->kode_permohonan)[2];

        $this->assertLessThan(9999, $urutan, 'Deret baru ikut menghitung nomor harian bentuk lama.');
    }
}
