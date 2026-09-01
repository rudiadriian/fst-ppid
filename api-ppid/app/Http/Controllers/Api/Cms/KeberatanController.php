<?php

namespace App\Http\Controllers\Api\Cms;

use App\Http\Controllers\Api\Concerns\MenanganiPersetujuan;
use App\Http\Controllers\Api\CrudController;
use App\Models\KeberatanInformasi;
use App\Support\AlurPersetujuan;
use App\Support\AuditLogger;
use App\Support\EmailPemohon;
use App\Support\NotifikasiPortal;
use App\Support\SlaLayanan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Modul keberatan informasi.
 *
 * Sama seperti Permohonan: tanpa `store`, `update`, dan `destroy`. Jenis
 * keberatan, alasan, kasus posisi, dan penguasaan adalah pernyataan pemohon —
 * petugas menanggapinya, bukan mengubahnya, dan tidak pula mencatatkannya atas
 * nama orang lain. Dua jalur tulis yang tersisa adalah `ubahTanggapan()` dan
 * putusan tahap persetujuan.
 */
class KeberatanController extends CrudController
{
    use MenanganiPersetujuan;

    protected string $model = KeberatanInformasi::class;

    protected string $modulSlug = 'keberatan';

    protected array $searchable = ['kode_keberatan', 'alasan_keberatan', 'tanggapan_atasan_ppid'];

    protected array $sortable = ['id', 'kode_keberatan', 'status', 'jenis_keberatan', 'tanggal_keberatan'];

    protected string $defaultSort = '-tanggal_keberatan';

    protected array $withList = ['pemohon:id,nama,email', 'permohonan:id,kode_permohonan', 'petugas:id,name'];

    protected array $withDetail = [
        'pemohon',
        'permohonan',
        'petugas:id,name',
        'files',
        'approvalLangkah.role:id,name',
        'approvalLangkah.pemutus:id,name',
    ];

    protected array $filterable = [
        'status' => 'exact',
        'jenis_keberatan' => 'exact',
        'permohonan_id' => 'exact',
    ];

    /**
     * Tidak ada endpoint tulis yang memakainya — lihat catatan kelas.
     */
    protected function rules(string $mode, ?Model $record): array
    {
        return [];
    }

    /**
     * Satu-satunya jalur tulis petugas atas keberatan yang sudah masuk.
     *
     * Hanya `status` dan `tanggapan_atasan_ppid` yang divalidasi, dan kolom
     * lain tidak pernah tersentuh meski ikut dikirim. Perpindahan statusnya
     * mengikuti {@see KeberatanInformasi::TRANSISI} supaya berkas tidak bisa
     * melompati tahap atau dibuka ulang setelah ditutup.
     */
    public function ubahTanggapan(Request $request, int $id): JsonResponse
    {
        /** @var KeberatanInformasi $keberatan */
        $keberatan = KeberatanInformasi::findOrFail($id);

        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(KeberatanInformasi::TRANSISI))],
            'tanggapan_atasan_ppid' => ['nullable', 'string', 'max:5000'],
        ]);

        $statusLama = (string) $keberatan->status;
        $statusBaru = $data['status'];
        $diizinkan = KeberatanInformasi::TRANSISI[$statusLama] ?? [];

        // Menyimpan ulang status yang sama dibiarkan lewat: petugas kerap
        // membetulkan tanggapannya tanpa memindahkan berkas ke tahap lain.
        if ($statusBaru !== $statusLama && !in_array($statusBaru, $diizinkan, true)) {
            throw ValidationException::withMessages([
                'status' => "Status tidak bisa berpindah dari '{$statusLama}' ke '{$statusBaru}'.",
            ]);
        }

        /*
         * Selama satu tahap persetujuan masih menunggu, perpindahan status
         * bukan lagi milik dropdown ini — milik putusan penyetujunya
         * (langkah 100). Sebelumnya yang dijaga hanya putusan akhir dari
         * "Menunggu Persetujuan", sehingga PPID Pelaksana yang sudah
         * meneruskan berkasnya masih bisa menariknya kembali, menolaknya
         * sendiri, atau menyatakannya kedaluwarsa — tiga hal yang seluruhnya
         * melangkahi jenjang di atasnya.
         *
         * Super admin dikecualikan dengan alasan yang sama seperti pada
         * {@see AlurPersetujuan::bolehMemutus()}: berkas yang macet karena
         * rolenya kosong atau pemegangnya nonaktif harus tetap bisa
         * dibebaskan tanpa menyunting basis data.
         */
        if ($statusBaru !== $statusLama
            && !$this->penggunaSuperAdmin()
            && AlurPersetujuan::berjalan(AlurPersetujuan::JENIS_KEBERATAN, $id) !== null) {
            throw ValidationException::withMessages([
                'status' => 'Keberatan ini sedang berada di jenjang persetujuan; '
                    .'perpindahannya ditentukan putusan pada panel Persetujuan Berjenjang.',
            ]);
        }

        DB::transaction(function () use ($keberatan, $data, $statusLama, $statusBaru) {
            if (array_key_exists('tanggapan_atasan_ppid', $data)) {
                $keberatan->tanggapan_atasan_ppid = $data['tanggapan_atasan_ppid'];
            }

            $this->terapkanStatus($keberatan, $statusLama, $statusBaru);
        });

        AuditLogger::record(
            Auth::guard('api')->id(),
            'update_status',
            KeberatanInformasi::class,
            $keberatan->id,
            ['status' => $statusLama],
            ['status' => $statusBaru]
        );

        return response()->json([
            'data' => $keberatan->fresh($this->withDetail),
            'message' => "Status menjadi '{$statusBaru}'.",
        ]);
    }

    /**
     * Simpan status baru beserta pemberitahuannya.
     *
     * Dipakai dialog tanggapan petugas dan hasil akhir alur persetujuan;
     * jalurnya satu supaya keduanya meninggalkan jejak yang sama.
     *
     * Harus dijalankan di dalam transaksi.
     */
    private function terapkanStatus(KeberatanInformasi $keberatan, string $statusLama, string $statusBaru): void
    {
        $keberatan->status = $statusBaru;

        // Petugas penanggung jawab terisi otomatis begitu keberatan mulai ditangani.
        if ($statusBaru !== 'diajukan' && $keberatan->ditangani_oleh === null) {
            $keberatan->ditangani_oleh = Auth::guard('api')->id();
        }

        if (in_array($statusBaru, ['selesai', 'ditolak'], true)) {
            $keberatan->tanggal_tanggapan = $keberatan->tanggal_tanggapan ?? now();

            /*
             * Batas pemohon membawa perkara ke Komisi Informasi: 14 hari kerja
             * sejak tanggapan diterima (Pasal 37 UU KIP). Kolomnya sudah ada
             * sejak langkah 89 tetapi tidak pernah diisi, sehingga tenggat itu
             * hanya disebut di badan surel dan tidak bisa ditampilkan,
             * disaring, maupun dihitung di mana pun.
             *
             * Dihitung dari tanggal tanggapan, bukan dari hari ini: keberatan
             * yang tanggal tanggapannya sudah terisi lebih dulu (misalnya
             * berpindah selesai → ditolak) tidak boleh memperpanjang hak
             * pemohon secara diam-diam.
             */
            $keberatan->batas_waktu_sengketa = $keberatan->batas_waktu_sengketa
                ?? SlaLayanan::batasSengketa($keberatan->tanggal_tanggapan);
        }

        $keberatan->save();

        if ($statusBaru === 'menunggu_approval') {
            AlurPersetujuan::mulai($keberatan);
        }

        // Menunggu commit: perubahan yang batal tidak boleh menyisakan
        // pemberitahuan atas keadaan yang tidak pernah tersimpan.
        //
        // Tanggapan atasan ikut dibawa ke lonceng: itu isi umpan baliknya, dan
        // keberatan tidak punya tabel log status seperti permohonan.
        DB::afterCommit(function () use ($keberatan, $statusLama, $statusBaru) {
            EmailPemohon::statusBerubah($keberatan, $statusLama, $statusBaru);
            NotifikasiPortal::statusPengajuan($keberatan, $statusLama, $statusBaru, $keberatan->tanggapan_atasan_ppid);
        });
    }

    // -----------------------------------------------------------------
    // Persetujuan berjenjang
    // -----------------------------------------------------------------

    protected function pengajuanPersetujuan(int $id): Model
    {
        return KeberatanInformasi::findOrFail($id);
    }

    /**
     * Status keberatan setelah alur persetujuan berakhir.
     *
     * Keberatan tidak mengenal status `disetujui`: yang disetujui adalah
     * tanggapan atasan atasnya, dan berkasnya langsung tertutup sebagai
     * `selesai`.
     */
    /** Pengguna yang sedang masuk adalah super admin? */
    private function penggunaSuperAdmin(): bool
    {
        $user = Auth::guard('api')->user();
        $user?->loadMissing('role');

        return $user?->role?->slug === 'super-admin';
    }

    /** Berkas naik ke jenjang berikutnya; lihat catatan yang sama di PermohonanController. */
    protected function terapkanLanjutPersetujuan(Model $pengajuan): void
    {
        /** @var KeberatanInformasi $pengajuan */
        $statusLama = (string) $pengajuan->status;

        if ($statusLama === 'menunggu_approval') {
            return;
        }

        $this->terapkanStatus($pengajuan, $statusLama, 'menunggu_approval');

        AuditLogger::record(
            Auth::guard('api')->id(),
            'update_status',
            KeberatanInformasi::class,
            $pengajuan->id,
            ['status' => $statusLama],
            ['status' => 'menunggu_approval']
        );
    }

    protected function terapkanHasilPersetujuan(Model $pengajuan, string $hasil, ?string $catatan): void
    {
        /** @var KeberatanInformasi $pengajuan */
        $statusLama = (string) $pengajuan->status;

        $statusBaru = match ($hasil) {
            AlurPersetujuan::HASIL_DISETUJUI => 'selesai',
            AlurPersetujuan::HASIL_DITOLAK => 'ditolak',
            default => 'diproses',
        };

        // Catatan penolakan penyetuju menjadi tanggapan yang dibaca pemohon:
        // keberatan tidak punya kolom alasan penolakan tersendiri, dan menutup
        // berkas tanpa keterangan apa pun membuat pemohon tidak tahu apa yang
        // diputuskan atas keberatannya.
        if ($hasil === AlurPersetujuan::HASIL_DITOLAK && filled($catatan) && blank($pengajuan->tanggapan_atasan_ppid)) {
            $pengajuan->tanggapan_atasan_ppid = $catatan;
        }

        $this->terapkanStatus($pengajuan, $statusLama, $statusBaru);

        // Sama seperti permohonan: revisi mengembalikan berkas ke jenjang
        // pertama beserta lonceng gilirannya, bukan sekadar menurunkan status.
        if ($hasil === AlurPersetujuan::HASIL_REVISI) {
            AlurPersetujuan::mulai($pengajuan->refresh());
        }

        AuditLogger::record(
            Auth::guard('api')->id(),
            'update_status',
            KeberatanInformasi::class,
            $pengajuan->id,
            ['status' => $statusLama],
            ['status' => $statusBaru]
        );
    }
}
