<?php

namespace App\Http\Controllers\Akun;

use App\Http\Controllers\Controller;
use App\Models\Pemohon;
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
        return view('akun.pengaturan.profil', ['pemohon' => Auth::guard('pemohon')->user()]);
    }

    public function perbaruiProfil(Request $request): RedirectResponse
    {
        $pemohon = Auth::guard('pemohon')->user();

        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'no_hp' => ['required', 'string', 'max:20'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        if ($request->hasFile('foto')) {
            $this->hapusBerkas($pemohon->foto);
            $data['foto'] = $this->simpanBerkas($request->file('foto'), 'uploads/avatar');
        } else {
            unset($data['foto']);
        }

        // Email sengaja tidak bisa diubah sendiri: menggantinya berarti
        // memindahkan kepemilikan akun, jadi harus lewat petugas PPID.
        $pemohon->fill($data)->save();

        return back()->with('status', __('Profil berhasil diperbarui.'));
    }

    public function dataPemohon(): View
    {
        return view('akun.pengaturan.data-pemohon', ['pemohon' => Auth::guard('pemohon')->user()]);
    }

    public function simpanDataPemohon(Request $request): RedirectResponse
    {
        $pemohon = Auth::guard('pemohon')->user();

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

        // Data berubah berarti harus diperiksa ulang petugas.
        $data['status_verifikasi'] = 'menunggu';
        $data['tanggal_verifikasi'] = null;

        $pemohon->fill($data)->save();

        return back()->with('status', __('Data Pemohon terkirim dan menunggu pemeriksaan petugas PPID.'));
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
