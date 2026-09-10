<?php

namespace Tests\Feature;

use App\Models\LaporanLayanan;
use App\Models\Regulasi;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * "Diunggah oleh" menyebut jabatan, bukan nama petugas (UAT poin 13).
 *
 * Nama pegawai tidak menambah apa pun bagi pengunjung — yang dijawab label itu
 * adalah pejabat mana yang menerbitkan dokumennya — sementara menampilkannya
 * menyebarkan nama orang ke halaman publik yang terindeks mesin pencari.
 *
 * `DatabaseTransactions`, bukan `RefreshDatabase`: basis data pengembangan ini
 * berisi data nyata, jadi baris uji cukup digulung balik.
 */
class PengunggahDokumenTest extends TestCase
{
    use DatabaseTransactions;

    private string $tanda;

    private string $namaPetugas;

    private string $namaRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tanda = Str::random(6);
        $this->namaPetugas = 'Petugas Rahasia '.$this->tanda;
        $this->namaRole = 'PPID Pelaksana Uji '.$this->tanda;
    }

    /** Baris `users` + `roles` milik be-ppid; fe-ppid hanya membacanya. */
    private function petugas(bool $denganRole = true): int
    {
        $roleId = $denganRole
            ? DB::table('roles')->insertGetId([
                'name' => $this->namaRole,
                'slug' => 'uji-'.Str::lower($this->tanda),
                'description' => 'Role uji',
                'created_at' => now(),
            ])
            : null;

        return (int) DB::table('users')->insertGetId([
            'name' => $this->namaPetugas,
            'email' => 'petugas-'.Str::lower($this->tanda).'@uji.test',
            'password' => bcrypt('RahasiaKuat#123'),
            'role_id' => $roleId,
            'is_active' => true,
            'created_at' => now(),
        ]);
    }

    /**
     * Baris disisipkan lewat query builder, bukan `create()`: model di fe-ppid
     * sengaja tidak punya `$fillable` karena situs publik hanya membacanya.
     */
    private function regulasi(int $userId): Regulasi
    {
        $id = DB::table('regulasi')->insertGetId([
            'judul' => 'Regulasi Uji '.$this->tanda,
            'kategori' => 'regulasi',
            'jenis_peraturan' => 'Peraturan Direksi',
            'tahun' => 2026,
            'uploaded_by' => $userId,
            'created_at' => now(),
        ]);

        return Regulasi::findOrFail($id);
    }

    private function laporan(int $userId): LaporanLayanan
    {
        $id = DB::table('laporan_layanan')->insertGetId([
            'judul' => 'Laporan Uji '.$this->tanda,
            'tahun' => 2026,
            'tipe_laporan' => 'pelayanan_informasi',
            'status' => 'published',
            'published_by' => $userId,
            'created_at' => now(),
        ]);

        return LaporanLayanan::findOrFail($id);
    }

    public function test_daftar_regulasi_menyebut_role_bukan_nama_petugas(): void
    {
        $userId = $this->petugas();
        $this->regulasi($userId);

        $respons = $this->get('/regulasi')->assertOk();

        $respons->assertSee($this->namaRole);
        $respons->assertDontSee($this->namaPetugas);
    }

    public function test_detail_regulasi_menyebut_role_bukan_nama_petugas(): void
    {
        $userId = $this->petugas();
        $regulasi = $this->regulasi($userId);

        $respons = $this->get(route('ppid.regulation.show', $regulasi->id))->assertOk();

        $respons->assertSee($this->namaRole);
        $respons->assertDontSee($this->namaPetugas);
    }

    public function test_laporan_pelayanan_menyebut_role_bukan_nama_petugas(): void
    {
        $userId = $this->petugas();
        $laporan = $this->laporan($userId);

        $daftar = $this->get('/laporan/pelayanan-informasi')->assertOk();
        $daftar->assertSee($this->namaRole);
        $daftar->assertDontSee($this->namaPetugas);

        $detail = $this->get(route('ppid.report.show', $laporan->id))->assertOk();
        $detail->assertSee($this->namaRole);
        $detail->assertDontSee($this->namaPetugas);
    }

    /** Maklumat pada halaman Standar Layanan memakai aturan yang sama. */
    public function test_maklumat_menyebut_role_bukan_nama_petugas(): void
    {
        $userId = $this->petugas();

        DB::table('maklumat')->insert([
            'judul' => 'Maklumat Uji '.$this->tanda,
            'file_dokumen' => 'uploads/maklumat/uji-'.Str::lower($this->tanda).'.png',
            // Tanggal terbit paling baru: halaman memilih baris ini, bukan yang
            // sudah ada di basis data pengembangan.
            'tanggal_terbit' => now()->addYear()->toDateString(),
            'status' => 'published',
            'published_by' => $userId,
            'created_at' => now(),
        ]);

        $respons = $this->get('/standar-layanan/maklumat-pelayanan')->assertOk();

        $respons->assertSee($this->namaRole);
        $respons->assertDontSee($this->namaPetugas);
    }

    /** Akun tanpa role jatuh ke sebutan umum, bukan ke nama orangnya. */
    public function test_petugas_tanpa_role_disebut_petugas_ppid(): void
    {
        $userId = $this->petugas(denganRole: false);
        $this->regulasi($userId);

        $respons = $this->get('/regulasi')->assertOk();

        $respons->assertSee('Petugas PPID');
        $respons->assertDontSee($this->namaPetugas);
    }
}
