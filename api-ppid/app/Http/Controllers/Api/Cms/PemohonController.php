<?php

namespace App\Http\Controllers\Api\Cms;

use App\Http\Controllers\Api\CrudController;
use App\Models\Pemohon;
use App\Support\AuditLogger;
use App\Support\EmailPemohon;
use App\Support\NotifikasiPortal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PemohonController extends CrudController
{
    protected string $model = Pemohon::class;

    protected string $modulSlug = 'permohonan';

    protected array $searchable = ['nama', 'email', 'no_hp', 'nama_lembaga'];

    protected array $sortable = ['id', 'nama', 'email', 'created_at', 'status_verifikasi', 'tanggal_verifikasi'];

    protected array $withList = ['verifikator:id,name'];

    protected array $withDetail = ['verifikator:id,name'];

    protected array $filterable = [
        'jenis_pemohon' => 'exact',
        'status_verifikasi' => 'exact',
    ];

    protected function rules(string $mode, ?Model $record): array
    {
        $wajib = $mode === 'create' ? 'required' : 'sometimes';

        return [
            'nik' => ['nullable', 'string', 'max:20', 'regex:/^[0-9]+$/'],
            'nama' => [$wajib, 'string', 'max:150'],
            'email' => [$wajib, 'email', 'max:150'],
            'no_hp' => ['nullable', 'string', 'max:30'],
            'alamat' => ['nullable', 'string'],
            'pekerjaan' => ['nullable', 'string', 'max:100'],
            'jenis_pemohon' => ['sometimes', Rule::in(['pribadi', 'instansi', 'kelompok'])],
            'nama_lembaga' => ['nullable', 'string', 'max:200'],
        ];
    }

    /**
     * Detail satu pemohon untuk keperluan verifikasi.
     *
     * Berbeda dari daftar, di sini **NIK ikut ditampilkan**. Tanpa NIK petugas
     * tidak bisa mencocokkan isian dengan KTP yang diunggah — itu inti
     * pemeriksaannya. Pembukaannya sempit dan disengaja: hanya endpoint ini,
     * hanya untuk yang berhak melihat modul, dan tetap tidak muncul di daftar
     * maupun ekspor.
     */
    public function show(int $id): JsonResponse
    {
        /** @var Pemohon $pemohon */
        $pemohon = Pemohon::with($this->withDetail)->findOrFail($id);

        $data = $pemohon->toArray();
        $data['nik'] = $pemohon->getAttribute('nik');
        $data['jumlah_permohonan'] = $pemohon->permohonan()->count();
        $data['punya_berkas_ktp'] = filled($pemohon->file_ktp);

        return response()->json(['data' => $data]);
    }

    /**
     * Sajikan berkas KTP pemohon.
     *
     * Berkasnya memang bisa dibaca lewat URL media publik situs, tetapi jalur
     * itu tidak menuntut siapa pun masuk — untuk dokumen identitas, itu terlalu
     * longgar. Endpoint ini menyajikannya di belakang token panel dan hak akses
     * modul, sehingga panel tidak perlu memakai URL publiknya.
     *
     * Berkas dikirim `inline` supaya bisa dilihat langsung di panel, dengan
     * `nosniff` agar tipe isinya tidak ditafsirkan ulang peramban.
     */
    public function berkasKtp(int $id): BinaryFileResponse
    {
        /** @var Pemohon $pemohon */
        $pemohon = Pemohon::findOrFail($id);

        abort_if(blank($pemohon->file_ktp), 404, 'Pemohon ini belum mengunggah KTP.');

        $disk = Storage::disk('media');

        abort_unless($disk->exists($pemohon->file_ktp), 404, 'Berkas KTP tidak ditemukan.');

        return response()->file($disk->path($pemohon->file_ktp), [
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ]);
    }

    /**
     * Putuskan hasil Verifikasi Data Diri Pemohon.
     *
     * Hanya dua keputusan: **terverifikasi** atau **ditolak**. Penolakan
     * menaikkan `jumlah_ditolak`; pada penolakan ketiga pemohon tidak boleh
     * mengirim ulang berkasnya lagi (aturan itu ditegakkan di situs publik,
     * lihat `Akun\PengaturanController`).
     *
     * Dua pagar yang berpasangan:
     *
     * 1. **Data yang belum lengkap tidak bisa disetujui.** Verifikasi menjawab
     *    "identitas pemohon ini sudah jelas"; tanpa NIK, alamat, atau berkas
     *    KTP tidak ada yang bisa dinyatakan jelas. Persetujuan semacam itu
     *    membuka layanan atas identitas yang tidak pernah diperiksa.
     * 2. **Persetujuan yang telanjur diberikan atas data tak lengkap masih
     *    bisa dicabut.** Berkas yang sudah disetujui memang tidak boleh
     *    ditolak belakangan — membalik keputusan berarti mencabut layanan yang
     *    mungkin sudah berjalan. Tetapi persetujuan atas data yang belum
     *    lengkap tidak pernah sah sejak awal, dan menguncinya berarti satu
     *    salah klik menutup berkas itu selamanya: pemohon tidak bisa
     *    memperbaiki apa pun (isiannya terkunci selama berstatus
     *    terverifikasi) dan petugas tidak bisa membatalkannya.
     */
    public function verifikasi(Request $request, int $id): JsonResponse
    {
        /** @var Pemohon $pemohon */
        $pemohon = Pemohon::findOrFail($id);

        $data = $request->validate([
            'status' => ['required', Rule::in(['terverifikasi', 'ditolak'])],
            // Alasan wajib saat menolak: pemohon harus tahu apa yang diperbaiki,
            // apalagi kesempatannya terbatas.
            'catatan' => [Rule::requiredIf($request->input('status') === 'ditolak'), 'nullable', 'string', 'max:1000'],
        ], [
            'catatan.required' => 'Alasan penolakan wajib diisi agar pemohon tahu apa yang harus diperbaiki.',
        ]);

        $dataLengkap = $pemohon->data_lengkap;

        if ($data['status'] === 'terverifikasi' && !$dataLengkap) {
            throw ValidationException::withMessages([
                'status' => 'Data Pemohon belum lengkap sehingga belum dapat disetujui. Belum terisi: '
                    .implode(', ', $pemohon->kekurangan_data).'.',
            ]);
        }

        /*
         * Pencabutan persetujuan yang tidak pernah sah. Hanya mungkin selama
         * datanya memang belum lengkap; begitu lengkap, penguncian lama berlaku
         * lagi.
         */
        $mencabutPersetujuan = $pemohon->status_verifikasi === 'terverifikasi'
            && $data['status'] === 'ditolak'
            && !$dataLengkap;

        if ($pemohon->status_verifikasi === 'terverifikasi' && $data['status'] === 'ditolak' && $dataLengkap) {
            throw ValidationException::withMessages([
                'status' => 'Data yang sudah terverifikasi tidak dapat ditolak dari sini.',
            ]);
        }

        // Pencabutan dilewatkan: yang dihitung pagar ini adalah berkas yang
        // ditolak setelah diperiksa, dan pencabutan di bawah memang tidak
        // menaikkan hitungan itu.
        if ($pemohon->verifikasi_diblokir && $data['status'] === 'ditolak' && !$mencabutPersetujuan) {
            throw ValidationException::withMessages([
                'status' => 'Pemohon ini sudah ditolak '.Pemohon::BATAS_DITOLAK.' kali dan tidak dapat mengirim berkas lagi.',
            ]);
        }

        $sebelum = [
            'status_verifikasi' => $pemohon->status_verifikasi,
            'jumlah_ditolak' => $pemohon->jumlah_ditolak,
        ];

        /*
         * Putusan yang sama disimpan dua kali bukan keputusan baru.
         *
         * Terjadi lebih sering daripada dugaan: tombolnya diklik dua kali, atau
         * petugas membuka ulang berkas yang sudah diputus untuk memastikan. Bagi
         * pemohon bedanya besar — tanpa penyaring ini ia menerima dua surel dan
         * dua baris lonceng untuk satu pemeriksaan.
         */
        $putusanSama = $pemohon->status_verifikasi === $data['status']
            && (string) $pemohon->catatan_verifikasi === (string) ($data['catatan'] ?? '');

        if ($putusanSama) {
            // Penolakan yang diulang juga tidak boleh menaikkan `jumlah_ditolak`:
            // klik kedua akan memakan jatah kirim ulang milik pemohon untuk
            // berkas yang bahkan belum sempat ia perbaiki.
            return response()->json([
                'message' => 'Putusan verifikasi ini sudah tercatat sebelumnya; tidak ada perubahan yang disimpan.',
                'data' => $pemohon->fresh($this->withList),
            ]);
        }

        $pemohon->status_verifikasi = $data['status'];
        $pemohon->catatan_verifikasi = $data['catatan'] ?? null;
        $pemohon->tanggal_verifikasi = now();
        $pemohon->diverifikasi_oleh = Auth::guard('api')->id();

        /*
         * Pencabutan tidak memakan jatah kirim ulang pemohon: berkasnya belum
         * pernah diperiksa dan ditolak — yang salah adalah persetujuannya.
         * Membebankan satu penolakan di situ berarti menghukum pemohon atas
         * salah klik petugas.
         */
        if ($data['status'] === 'ditolak' && !$mencabutPersetujuan) {
            $pemohon->jumlah_ditolak = (int) $pemohon->jumlah_ditolak + 1;
        }

        $pemohon->save();

        AuditLogger::record(
            Auth::guard('api')->id(),
            'verifikasi_pemohon',
            Pemohon::class,
            $pemohon->getKey(),
            $sebelum,
            [
                'status_verifikasi' => $pemohon->status_verifikasi,
                'jumlah_ditolak' => $pemohon->jumlah_ditolak,
                'catatan_verifikasi' => $pemohon->catatan_verifikasi,
            ]
        );

        // Keputusan diberitahukan lewat email dan lonceng portal; kegagalan
        // keduanya tidak boleh membatalkan keputusan yang sudah tersimpan
        // (lihat EmailPemohon dan NotifikasiPortal).
        EmailPemohon::hasilVerifikasiData($pemohon);
        NotifikasiPortal::hasilVerifikasiData($pemohon);

        return response()->json([
            'message' => $data['status'] === 'terverifikasi'
                ? 'Data pemohon dinyatakan terverifikasi.'
                : 'Data pemohon ditolak. Sisa kesempatan kirim ulang: '.$pemohon->sisa_kesempatan.'.',
            'data' => $pemohon->fresh($this->withList),
        ]);
    }

    protected function beforeDelete(Model $record): void
    {
        if ($record->permohonan()->exists()) {
            throw ValidationException::withMessages([
                'id' => 'Pemohon masih punya permohonan tercatat.',
            ]);
        }
    }
}
