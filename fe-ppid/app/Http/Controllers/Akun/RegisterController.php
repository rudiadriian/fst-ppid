<?php

namespace App\Http\Controllers\Akun;

use App\Http\Controllers\Controller;
use App\Models\Pemohon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Pendaftaran akun pengunjung.
 *
 * Tabel `pemohon` bisa sudah berisi orang yang permohonannya dulu diinput
 * petugas lewat be-ppid. Kalau emailnya cocok dan baris itu belum punya
 * password, baris tersebut "diklaim" — riwayat permohonannya tetap menempel.
 * Kalau sudah punya password, berarti akunnya memang sudah ada.
 */
class RegisterController extends Controller
{
    public function create(): View
    {
        return view('akun.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'no_hp' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Password::defaults()->min(8)->letters()->numbers()],
            'setuju' => ['accepted'],
        ]);

        $email = Str::lower($data['email']);

        $pemohon = Pemohon::where('email', $email)->first();

        if ($pemohon && $pemohon->sudahPunyaAkun()) {
            throw ValidationException::withMessages([
                'email' => __('Email ini sudah terdaftar. Silakan masuk atau gunakan tautan lupa password.'),
            ]);
        }

        if ($pemohon) {
            // Klaim baris lama milik petugas: email belum pernah dibuktikan
            // pemiliknya, jadi status verifikasi sengaja dikosongkan ulang.
            $pemohon->fill([
                'nama' => $data['nama'],
                'no_hp' => $data['no_hp'] ?? $pemohon->no_hp,
                'password' => $data['password'],
                'email_verified_at' => null,
            ])->save();
        } else {
            $pemohon = Pemohon::create([
                'nama' => $data['nama'],
                'email' => $email,
                'no_hp' => $data['no_hp'] ?? null,
                'password' => $data['password'],
                'jenis_pemohon' => 'pribadi',
            ]);
        }

        // Email verifikasi tidak boleh menggagalkan pendaftaran kalau SMTP mati.
        try {
            event(new Registered($pemohon));
        } catch (\Throwable $e) {
            Log::warning('[PPID] Gagal mengirim email verifikasi pemohon: '.$e->getMessage());
        }

        /*
         * Tidak langsung masuk: email harus dibuktikan dulu. Emailnya
         * dititipkan di session supaya halaman verifikasi bisa mengirim ulang
         * tautan tanpa perlu sesi login.
         */
        if (config('ppid.akun.wajib_verifikasi_email')) {
            $request->session()->put('verifikasi_email', $pemohon->email);

            return redirect()->route('akun.verifikasi.notice')
                ->with('status', __('Akun berhasil dibuat. Buka tautan verifikasi yang kami kirim ke email Anda sebelum masuk.'));
        }

        Auth::guard('pemohon')->login($pemohon);
        $request->session()->regenerate();

        return redirect()->intended(route('akun.dashboard'))
            ->with('status', __('Akun berhasil dibuat.'));
    }
}
