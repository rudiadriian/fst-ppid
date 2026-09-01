<?php

namespace App\Http\Controllers\Api\Concerns;

use App\Models\ApprovalPengajuan;
use App\Support\AlurPersetujuan;
use App\Support\AuditLogger;
use App\Support\EmailPemohon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Dua endpoint persetujuan berjenjang, dipakai Permohonan dan Keberatan.
 *
 * Bagian yang berbeda di antara keduanya hanya satu: status apa yang dipasang
 * setelah alurnya berakhir. Itu diserahkan ke {@see terapkanHasilPersetujuan()}
 * di masing-masing controller, karena perpindahan status permohonan menulis
 * `permohonan_log_status` sedangkan keberatan tidak punya tabel itu.
 */
trait MenanganiPersetujuan
{
    /** Model pengajuan beserta relasi detailnya. */
    abstract protected function pengajuanPersetujuan(int $id): Model;

    /**
     * Terjemahkan hasil alur menjadi status pengajuan.
     *
     * @param  string  $hasil  salah satu `AlurPersetujuan::HASIL_*` selain `lanjut`
     */
    abstract protected function terapkanHasilPersetujuan(Model $pengajuan, string $hasil, ?string $catatan): void;

    /**
     * Pasang status "sedang di jenjang persetujuan" pada pengajuannya.
     *
     * Dipanggil begitu satu tahap disetujui dan masih ada tahap di atasnya.
     * Sebelum langkah 100 keadaan ini tidak tercermin di status sama sekali:
     * berkas yang sudah diteruskan PPID Pelaksana tetap berstatus "Diproses",
     * sehingga dropdown status masih menawarkan seluruh perpindahan kepada
     * petugas yang justru sudah selesai bagiannya.
     */
    abstract protected function terapkanLanjutPersetujuan(Model $pengajuan): void;

    /**
     * Keadaan persetujuan satu pengajuan: seluruh jenjang beserta putusannya.
     *
     * Ikut mengembalikan `boleh_memutus` supaya panel tidak perlu menebak
     * sendiri dari role — jawabannya sudah dihitung server dengan aturan yang
     * sama persis dengan yang dipakai saat putusan dikirim.
     */
    public function daftarPersetujuan(int $id): JsonResponse
    {
        $pengajuan = $this->pengajuanPersetujuan($id);
        $jenis = AlurPersetujuan::jenisDari($pengajuan);

        /*
         * Endpoint baca yang menulis, dan itu disengaja (langkah 100).
         * Pengajuan lahir di portal pemohon — aplikasi terpisah yang tidak
         * memuat mesin persetujuan ini — jadi jenjangnya tidak bisa dibuat di
         * tempat berkasnya dibuat. Membuatnya di sini berarti berkas apa pun
         * yang dibuka petugas sudah punya jenjang, tanpa ada satu pun langkah
         * manual yang bisa terlewat. Pemanggilannya idempoten dan melewati
         * berkas yang sudah tuntas.
         */
        AlurPersetujuan::pastikanBerjalan($pengajuan);

        $berjalan = AlurPersetujuan::berjalan($jenis, $id);
        $user = Auth::guard('api')->user();

        return response()->json([
            'data' => [
                'jenis' => $jenis,
                'langkah' => AlurPersetujuan::langkah($jenis, $id),
                'berjalan_id' => $berjalan?->getKey(),
                'boleh_memutus' => $berjalan !== null && $user !== null
                    && AlurPersetujuan::bolehMemutus($user, $berjalan),
            ],
        ]);
    }

    /**
     * Putuskan langkah persetujuan yang sedang berjalan.
     *
     * Yang diputus selalu langkah berjalan, tidak pernah langkah yang dipilih
     * klien: jenjang berarti berurutan, dan id yang datang dari luar tidak
     * boleh bisa melompati tahap di bawahnya.
     */
    public function putuskanPersetujuan(Request $request, int $id): JsonResponse
    {
        $pengajuan = $this->pengajuanPersetujuan($id);
        $jenis = AlurPersetujuan::jenisDari($pengajuan);

        $data = $request->validate([
            'keputusan' => ['required', Rule::in(['disetujui', 'ditolak', 'revisi'])],
            'catatan' => ['nullable', 'string', 'max:2000'],
            /*
             * Tiga isian tambahan milik jalur pelayanan (langkah 89). Semuanya
             * opsional di sini dan diwajibkan belakangan hanya pada keadaan
             * yang benar-benar menuntutnya — lihat `wajibkanIsianJalur()`.
             * Menaruh `required_if` di sini akan menuntutnya juga pada jenjang
             * PPID yang memutus, padahal jenjang itu tidak menjadwalkan apa pun.
             */
            'jalur_pelayanan' => ['nullable', Rule::in(['online', 'langsung'])],
            'jadwal_layanan' => ['nullable', 'date', 'after:now'],
            'keterangan_petugas' => ['nullable', 'string', 'max:2000'],
        ]);

        $langkah = AlurPersetujuan::berjalan($jenis, $id);

        if ($langkah === null) {
            throw ValidationException::withMessages([
                'keputusan' => 'Tidak ada tahap persetujuan yang sedang berjalan pada pengajuan ini.',
            ]);
        }

        $user = Auth::guard('api')->user();

        if ($user === null || !AlurPersetujuan::bolehMemutus($user, $langkah)) {
            return response()->json([
                'error' => "Tahap '{$langkah->nama_tahap}' hanya dapat diputus oleh role yang ditetapkan pada alur persetujuan.",
            ], 403);
        }

        // Alasan wajib saat menolak atau meminta perbaikan: keduanya
        // dikembalikan ke petugas — dan pada penolakan ikut sampai ke pemohon —
        // jadi putusan tanpa keterangan tidak bisa ditindaklanjuti siapa pun.
        if ($data['keputusan'] !== 'disetujui' && blank($data['catatan'] ?? null)) {
            throw ValidationException::withMessages([
                'catatan' => 'Catatan wajib diisi saat menolak atau meminta perbaikan.',
            ]);
        }

        if ($data['keputusan'] === 'ditolak' && $langkah->tahap !== null && !$langkah->tahap->boleh_tolak) {
            throw ValidationException::withMessages([
                'keputusan' => "Tahap '{$langkah->nama_tahap}' tidak diberi hak menolak pada alur persetujuan.",
            ]);
        }

        // Jenjang penerima — yang tidak diberi hak menolak — adalah tempat
        // jalur pelayanan ditetapkan: dialah yang berhubungan dengan pemohon,
        // mengunggah dokumennya, atau menetapkan waktu kedatangannya.
        $jenjangPenerima = $langkah->tahap !== null && !$langkah->tahap->boleh_tolak;

        if ($jenjangPenerima && $data['keputusan'] === 'disetujui') {
            $this->wajibkanIsianJalur($pengajuan, $data);
        }

        $hasil = DB::transaction(function () use ($pengajuan, $langkah, $user, $data, $jenjangPenerima) {
            if ($jenjangPenerima && $data['keputusan'] === 'disetujui') {
                $this->simpanIsianJalur($pengajuan, $data);
            }

            $hasil = AlurPersetujuan::putuskan(
                $pengajuan,
                $langkah,
                $user,
                $data['keputusan'],
                $data['catatan'] ?? null
            );

            if ($hasil === AlurPersetujuan::HASIL_LANJUT) {
                // Masih ada jenjang di atasnya: berkasnya berpindah ke
                // "Menunggu Persetujuan" dan sejak itu tidak lagi bisa
                // digeser lewat dropdown status oleh petugas di bawahnya.
                $this->terapkanLanjutPersetujuan($pengajuan);
            } else {
                $this->terapkanHasilPersetujuan($pengajuan, $hasil, $data['catatan'] ?? null);
            }

            return $hasil;
        });

        // Di luar transaksi: pemberitahuan yang gagal terkirim tidak boleh
        // membatalkan putusan yang sudah sah.
        if ($jenjangPenerima && $data['keputusan'] === 'disetujui') {
            $pengajuan->refresh();

            EmailPemohon::jalurPelayanan(
                $pengajuan,
                (string) $pengajuan->jalur_pelayanan,
                $data['keterangan_petugas'] ?? null
            );
        }

        AuditLogger::record(
            $user->getKey(),
            'approval',
            ApprovalPengajuan::class,
            $langkah->getKey(),
            null,
            [
                'jenis' => $jenis,
                'pengajuan_id' => $id,
                'tahap' => $langkah->nama_tahap,
                'keputusan' => $data['keputusan'],
            ]
        );

        return response()->json([
            'data' => [
                'hasil' => $hasil,
                'langkah' => AlurPersetujuan::langkah($jenis, $id),
            ],
            'message' => match ($hasil) {
                AlurPersetujuan::HASIL_LANJUT => 'Disetujui. Berkas diteruskan ke tahap berikutnya.',
                AlurPersetujuan::HASIL_DISETUJUI => 'Seluruh tahap persetujuan selesai.',
                AlurPersetujuan::HASIL_REVISI => 'Berkas dikembalikan ke petugas untuk diperbaiki.',
                default => 'Pengajuan ditolak pada tahap persetujuan.',
            },
        ]);
    }

    /**
     * Isian yang wajib ada sebelum jenjang penerima meneruskan berkas.
     *
     * Jalurnya sendiri wajib: tanpa itu tidak ada yang tahu apakah dokumennya
     * dikirim atau pemohonnya diundang. Jalur `langsung` menambah satu tuntutan
     * lagi — tanggal dan jamnya — karena undangan tanpa waktu bukan undangan.
     *
     * Dokumen jalur `online` tidak diwajibkan di sini: berkasnya diunggah lewat
     * endpoint tanggapan tersendiri dan bisa menyusul, sedangkan waktu kunjungan
     * harus sudah pasti pada saat pemohon diberi tahu.
     */
    private function wajibkanIsianJalur(Model $pengajuan, array $data): void
    {
        $jalur = $data['jalur_pelayanan'] ?? $pengajuan->jalur_pelayanan;

        if (blank($jalur)) {
            throw ValidationException::withMessages([
                'jalur_pelayanan' => 'Pilih jalur pelayanan: Online atau Langsung.',
            ]);
        }

        if ($jalur === 'langsung' && blank($data['jadwal_layanan'] ?? $pengajuan->jadwal_layanan)) {
            throw ValidationException::withMessages([
                'jadwal_layanan' => 'Tanggal dan waktu undangan wajib diisi untuk jalur Langsung.',
            ]);
        }
    }

    /** Simpan jalur, jadwal, dan keterangan petugas pada pengajuannya. */
    private function simpanIsianJalur(Model $pengajuan, array $data): void
    {
        $isi = array_filter([
            'jalur_pelayanan' => $data['jalur_pelayanan'] ?? null,
            'jadwal_layanan' => $data['jadwal_layanan'] ?? null,
            'keterangan_petugas' => $data['keterangan_petugas'] ?? null,
        ], fn ($nilai) => filled($nilai));

        // Jalur Online tidak punya jadwal; jadwal sisa dari percobaan sebelumnya
        // dikosongkan agar pemohon tidak melihat undangan yang sudah dibatalkan.
        if (($data['jalur_pelayanan'] ?? null) === 'online') {
            $isi['jadwal_layanan'] = null;
        }

        if ($isi !== []) {
            $pengajuan->fill($isi)->save();
        }
    }
}
