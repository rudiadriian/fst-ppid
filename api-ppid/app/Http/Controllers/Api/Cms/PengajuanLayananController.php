<?php

namespace App\Http\Controllers\Api\Cms;

use App\Http\Controllers\Controller;
use App\Support\SlaLayanan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Daftar gabungan pengajuan layanan: permohonan informasi dan keberatan.
 *
 * Petugas menangani keduanya dengan gerak yang sama — buka detail, periksa,
 * teruskan — jadi keduanya tampil di satu daftar dan dibedakan **kategori**
 * (langkah 89), bukan dipisah jadi dua menu yang harus diperiksa bergantian.
 * Sebelumnya berkas yang menumpuk di modul Keberatan tidak terlihat sama sekali
 * dari modul Permohonan, dan tidak ada satu tempat pun yang menjawab "apa saja
 * yang menunggu saya hari ini".
 *
 * Hanya baca. Perubahan tetap lewat endpoint modul masing-masing
 * (`permohonan/{id}/…`, `keberatan/{id}/…`) karena aturan status, jenjang
 * persetujuan, dan pencatatan log keduanya memang berbeda — menyeragamkannya di
 * sini hanya akan menyembunyikan perbedaan yang penting.
 *
 * Gabungnya dilakukan di PHP, bukan `UNION` di basis data. Kedua tabel punya
 * kolom yang jauh berbeda, dan `UNION` menuntut daftar kolom yang dipaksa sama
 * panjang — bentuk yang sulit dibaca dan mudah salah begitu satu tabel berubah.
 * Jumlah pengajuan per halaman kecil, jadi mengambil sedikit lebih banyak baris
 * lalu mengurutkannya di memori tidak menimbulkan beban yang berarti.
 */
class PengajuanLayananController extends Controller
{
    /** Batas baris yang diambil per tabel sebelum digabung dan dipotong. */
    private const AMBIL_MAKS = 500;

    public function index(Request $request): JsonResponse
    {
        $jenis = (string) $request->query('jenis', '');
        $status = (string) $request->query('status', '');
        $jalur = (string) $request->query('jalur_pelayanan', '');
        $keadaanSla = (string) $request->query('sla_keadaan', '');
        $cari = trim((string) $request->query('search', ''));

        $baris = collect();

        if ($jenis !== 'keberatan') {
            $baris = $baris->concat($this->permohonan($status, $cari, $jalur));
        }

        if ($jenis !== 'permohonan') {
            $baris = $baris->concat($this->keberatan($status, $cari, $jalur));
        }

        // Penyaringan keadaan tenggat dilakukan setelah barisnya terbentuk:
        // keadaannya hasil hitungan, bukan kolom yang bisa ditanyakan ke basis
        // data.
        if ($keadaanSla !== '') {
            $baris = $baris->filter(fn ($b) => ($b['sla_keadaan'] ?? null) === $keadaanSla);
        }

        // Terbaru di atas: yang baru masuk itulah yang tenggatnya paling jauh
        // dari selesai, dan itulah yang dicari petugas saat membuka daftarnya.
        $baris = $baris->sortByDesc('tanggal_pengajuan')->values();

        $perPage = min(max((int) $request->query('per_page', 15), 1), 100);
        $page = max((int) $request->query('page', 1), 1);
        $total = $baris->count();

        return response()->json([
            'data' => $baris->forPage($page, $perPage)->values()->all(),
            'meta' => [
                'total' => $total,
                'page' => $page,
                'per_page' => $perPage,
                'last_page' => max(1, (int) ceil($total / $perPage)),
            ],
        ]);
    }

    private function permohonan(string $status, string $cari, string $jalur = '')
    {
        $query = DB::table('permohonan_informasi as p')
            ->leftJoin('pemohon as m', 'm.id', '=', 'p.pemohon_id')
            ->whereNull('p.deleted_at')
            ->select([
                'p.id', 'p.kode_permohonan', 'p.rincian_informasi', 'p.status',
                'p.jalur_pelayanan', 'p.tanggal_permohonan', 'p.batas_waktu_tanggapan',
                'p.tanggal_tanggapan', 'p.diperpanjang_pada', 'm.nama as nama_pemohon',
            ]);

        if ($status !== '') {
            $query->where('p.status', $status);
        }

        if ($jalur !== '') {
            $query->where('p.jalur_pelayanan', $jalur);
        }

        if ($cari !== '') {
            $query->where(function ($q) use ($cari) {
                $q->where('p.kode_permohonan', 'ilike', "%$cari%")
                    ->orWhere('p.rincian_informasi', 'ilike', "%$cari%")
                    ->orWhere('m.nama', 'ilike', "%$cari%");
            });
        }

        return $query->orderByDesc('p.tanggal_permohonan')->limit(self::AMBIL_MAKS)->get()
            ->map(fn ($r) => $this->seragamkan(
                'permohonan',
                'Permohonan Informasi',
                $r->id,
                $r->kode_permohonan,
                $r->nama_pemohon,
                $r->rincian_informasi,
                $r->status,
                $r->jalur_pelayanan,
                $r->tanggal_permohonan,
                $r->batas_waktu_tanggapan,
                $r->tanggal_tanggapan,
                $r->diperpanjang_pada
            ));
    }

    private function keberatan(string $status, string $cari, string $jalur = '')
    {
        $query = DB::table('keberatan_informasi as k')
            ->leftJoin('pemohon as m', 'm.id', '=', 'k.pemohon_id')
            ->leftJoin('permohonan_informasi as p', 'p.id', '=', 'k.permohonan_id')
            ->whereNull('k.deleted_at')
            ->select([
                'k.id', 'k.kasus_posisi', 'k.alasan_keberatan', 'k.status',
                'k.jalur_pelayanan', 'k.tanggal_keberatan', 'k.batas_waktu_tanggapan',
                'k.tanggal_tanggapan', 'm.nama as nama_pemohon',
                'k.kode_keberatan', 'p.kode_permohonan',
            ]);

        if ($status !== '') {
            $query->where('k.status', $status);
        }

        if ($jalur !== '') {
            $query->where('k.jalur_pelayanan', $jalur);
        }

        if ($cari !== '') {
            $query->where(function ($q) use ($cari) {
                $q->where('k.kode_keberatan', 'ilike', "%$cari%")
                    ->orWhere('p.kode_permohonan', 'ilike', "%$cari%")
                    ->orWhere('k.kasus_posisi', 'ilike', "%$cari%")
                    ->orWhere('m.nama', 'ilike', "%$cari%");
            });
        }

        return $query->orderByDesc('k.tanggal_keberatan')->limit(self::AMBIL_MAKS)->get()
            ->map(fn ($r) => $this->seragamkan(
                'keberatan',
                'Permohonan Keberatan Informasi',
                $r->id,
                // Nomor keberatan sendiri (KBT-FSTJ/…), bukan nomor
                // permohonan induknya: keduanya berkas terpisah dengan tenggat
                // berbeda, dan arsipnya menuntut nomor yang berbeda pula.
                $r->kode_keberatan,
                $r->nama_pemohon,
                $r->kasus_posisi ?: $r->alasan_keberatan,
                $r->status,
                $r->jalur_pelayanan,
                $r->tanggal_keberatan,
                $r->batas_waktu_tanggapan,
                $r->tanggal_tanggapan,
                null
            ));
    }

    /**
     * Satu bentuk baris untuk kedua jenis pengajuan.
     *
     * `id` dibuat unik lintas tabel (`permohonan-3`, `keberatan-3`) karena
     * daftarnya memakainya sebagai kunci baris — dua tabel yang sama-sama
     * berid 3 akan saling menimpa kalau id aslinya dipakai langsung. Id asli
     * tetap dibawa sebagai `ref_id`, dan itulah yang dipakai panel untuk
     * membuka detail ke endpoint yang benar bersama `jenis`.
     */
    private function seragamkan(
        string $jenis,
        string $labelJenis,
        int $id,
        ?string $kode,
        ?string $namaPemohon,
        ?string $pokok,
        ?string $status,
        ?string $jalur,
        ?string $tanggal,
        ?string $batas,
        ?string $tanggapan,
        ?string $diperpanjang
    ): array {
        // `SlaLayanan::keadaan()` membaca model; di sini barisnya hasil query
        // builder, jadi dibungkus objek ringan dengan tiga properti yang dibaca.
        $sla = SlaLayanan::keadaan(new class($batas, $tanggapan, $diperpanjang) extends \Illuminate\Database\Eloquent\Model
        {
            public function __construct($batas, $tanggapan, $diperpanjang)
            {
                parent::__construct();
                $this->batas_waktu_tanggapan = $batas ? Carbon::parse($batas) : null;
                $this->tanggal_tanggapan = $tanggapan ? Carbon::parse($tanggapan) : null;
                $this->diperpanjang_pada = $diperpanjang ? Carbon::parse($diperpanjang) : null;
            }
        });

        return [
            'id' => "$jenis-$id",
            'ref_id' => $id,
            'jenis' => $jenis,
            'jenis_label' => $labelJenis,
            'kode' => $kode,
            'nama_pemohon' => $namaPemohon,
            'pokok' => $pokok,
            'status' => $status,
            'jalur_pelayanan' => $jalur,
            'tanggal_pengajuan' => $tanggal,
            'batas_waktu_tanggapan' => $batas,
            'tanggal_tanggapan' => $tanggapan,
            'sla' => $sla,
            // Dua salinan datar dari `sla`: daftar modul membaca kolom per nama,
            // bukan jalur bersarang, dan menambah dukungan jalur bersarang di
            // sana hanya untuk satu modul tidak sepadan dengan rumitnya.
            'sla_keadaan' => $sla['keadaan'] ?? null,
            'sla_label' => $sla['label'] ?? null,
        ];
    }
}
