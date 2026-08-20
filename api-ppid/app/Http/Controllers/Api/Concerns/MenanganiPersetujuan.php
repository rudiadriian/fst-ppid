<?php

namespace App\Http\Controllers\Api\Concerns;

use App\Models\ApprovalPengajuan;
use App\Support\AlurPersetujuan;
use App\Support\AuditLogger;
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

        $hasil = DB::transaction(function () use ($pengajuan, $langkah, $user, $data) {
            $hasil = AlurPersetujuan::putuskan(
                $pengajuan,
                $langkah,
                $user,
                $data['keputusan'],
                $data['catatan'] ?? null
            );

            // Alur yang masih punya jenjang di atasnya belum mengubah apa pun
            // pada pengajuannya: statusnya tetap "menunggu persetujuan".
            if ($hasil !== AlurPersetujuan::HASIL_LANJUT) {
                $this->terapkanHasilPersetujuan($pengajuan, $hasil, $data['catatan'] ?? null);
            }

            return $hasil;
        });

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
}
