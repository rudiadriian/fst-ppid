<?php

namespace App\Http\Controllers\Akun;

use App\Http\Controllers\Controller;
use App\Models\Pemohon;
use App\Support\NotifikasiAdmin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Pengaturan akun: Profil, Data Pemohon & Berkas, dan Ubah Password.
 */
class PengaturanController extends Controller
{
    public function profil(): View
    {
        $pemohon = Auth::guard('pemohon')->user();

        return view('akun.pengaturan.profil', [
            'pemohon' => $pemohon,
            // Dipakai tab Aktivitas; dihitung di sini supaya view tidak
            // menembak query sendiri.
            'jumlahPermohonan' => $pemohon->permohonan()->count(),
            'jumlahKeberatan' => $pemohon->keberatan()->count(),
        ]);
    }

    /**
     * Satu-satunya isian profil yang boleh diubah sendiri: foto avatar.
     *
     * Nama, email, nomor telepon, dan seluruh data diri lain ikut dipakai
     * sebagai identitas pada permohonan yang sudah diverifikasi petugas —
     * mengubahnya diam-diam berarti mengubah identitas di berkas yang sudah
     * terlanjur diproses. Perubahannya karena itu harus lewat petugas PPID.
     */
    public function perbaruiProfil(Request $request): RedirectResponse
    {
        $pemohon = Auth::guard('pemohon')->user();

        $request->validate([
            'foto' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $this->hapusBerkas($pemohon->foto);

        $pemohon->forceFill([
            'foto' => $this->simpanBerkas($request->file('foto'), 'uploads/avatar'),
        ])->save();

        return back()->with('status', __('Foto profil berhasil diperbarui.'));
    }

    public function dataPemohon(): View
    {
        return view('akun.pengaturan.data-pemohon', ['pemohon' => Auth::guard('pemohon')->user()]);
    }

    public function simpanDataPemohon(Request $request): RedirectResponse
    {
        $pemohon = Auth::guard('pemohon')->user();

        /*
         * Data yang sudah disetujui petugas terkunci. Permohonan yang berjalan
         * memakai identitas itu sebagai dasar verifikasinya; mengubahnya sendiri
         * berarti berkas lama diproses atas nama data yang sudah tidak ada.
         * Perubahan setelah ini harus lewat petugas PPID.
         */
        if ($pemohon->dataTerverifikasi()) {
            throw ValidationException::withMessages([
                'nik' => __('Data Pemohon Anda sudah terverifikasi sehingga tidak dapat diubah sendiri. Hubungi petugas PPID bila ada data yang perlu diperbaiki.'),
            ]);
        }

        // Sudah ditolak sampai batas: pengiriman ulang ditutup di sisi server,
        // bukan hanya dengan menyembunyikan tombolnya.
        if ($pemohon->verifikasiDiblokir()) {
            throw ValidationException::withMessages([
                'nik' => __('Data diri Anda sudah ditolak :batas kali sehingga pengiriman ulang ditutup. Hubungi petugas PPID untuk melanjutkan.', [
                    'batas' => Pemohon::BATAS_DITOLAK,
                ]),
            ]);
        }

        $data = $request->validate([
            'jenis_pemohon' => ['required', 'in:'.implode(',', array_keys(Pemohon::JENIS))],
            'nik' => ['required', 'string', 'digits_between:8,30'],
            'pekerjaan' => ['required', 'string', 'max:100'],
            'alamat' => ['required', 'string', 'max:500'],
            'nama_lembaga' => ['nullable', 'required_unless:jenis_pemohon,perorangan', 'string', 'max:255'],
            // KTP wajib pada pengiriman pertama; boleh dikosongkan kalau berkas
            // lama masih ada dan pengguna hanya memperbaiki data lain.
            'file_ktp' => [$pemohon->file_ktp ? 'nullable' : 'required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ]);

        if ($request->hasFile('file_ktp')) {
            $this->hapusBerkas($pemohon->file_ktp);
            $data['file_ktp'] = $this->simpanBerkas($request->file('file_ktp'), 'uploads/ktp');
        } else {
            unset($data['file_ktp']);
        }

        // Data berubah berarti harus diperiksa ulang petugas. Catatan penolakan
        // lama dibuang supaya pemohon tidak melihat alasan yang sudah tidak
        // berlaku untuk berkas barunya.
        $data['status_verifikasi'] = 'menunggu';
        $data['tanggal_verifikasi'] = null;
        $data['catatan_verifikasi'] = null;

        $pemohon->fill($data)->save();

        // Berkasnya baru menunggu diperiksa — inilah yang perlu dilihat petugas
        // di lonceng notifikasi be-ppid, bukan sekadar pendaftaran akunnya.
        NotifikasiAdmin::verifikasiPemohonMenunggu($pemohon);

        return back()->with('status', __('Data Pemohon terkirim dan menunggu pemeriksaan petugas PPID. Pemeriksaan memerlukan waktu paling lama :hari hari kerja.', [
            'hari' => (int) config('ppid.akun.sla_verifikasi_hari_kerja', 14),
        ]));
    }

    public function password(): View
    {
        return view('akun.pengaturan.password', ['pemohon' => Auth::guard('pemohon')->user()]);
    }

    public function perbaruiPassword(Request $request): RedirectResponse
    {
        $pemohon = Auth::guard('pemohon')->user();

        $data = $request->validate([
            'password_lama' => ['required', 'string'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()->min(8)->letters()->numbers()],
        ]);

        if (!Hash::check($data['password_lama'], $pemohon->password)) {
            throw ValidationException::withMessages([
                'password_lama' => __('Password lama tidak cocok.'),
            ]);
        }

        $pemohon->forceFill(['password' => $data['password']])->save();

        return back()->with('status', __('Password berhasil diubah.'));
    }

    /** Berkas KTP hanya boleh dibuka pemiliknya sendiri. */
    public function berkasKtp()
    {
        $pemohon = Auth::guard('pemohon')->user();

        abort_if(blank($pemohon->file_ktp), 404);

        $disk = Storage::disk('public');

        abort_unless($disk->exists($pemohon->file_ktp), 404);

        return $disk->response($pemohon->file_ktp, null, ['X-Content-Type-Options' => 'nosniff']);
    }

    private function simpanBerkas($berkas, string $folder): string
    {
        return $berkas->storeAs($folder, Str::uuid().'.'.$berkas->getClientOriginalExtension(), 'public');
    }

    private function hapusBerkas(?string $path): void
    {
        if (filled($path) && Str::startsWith($path, 'uploads/')) {
            Storage::disk('public')->delete($path);
        }
    }
}
