<?php

namespace App\Http\Controllers\Akun;

use App\Http\Controllers\Controller;
use App\Models\Pemohon;
use App\Models\PengirimanTautanAkun;
use App\Rules\CaptchaBenar;
use App\Support\PembatasTautan;
use App\Support\PerisaiFormulir;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password as PasswordBroker;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Lupa & reset password akun pengunjung.
 *
 * Memakai broker `pemohon` (config/auth.php) yang menyimpan tokennya di
 * tabel terpisah dari token akun petugas.
 */
class PasswordResetController extends Controller
{
    public function request(): View
    {
        return view('akun.forgot-password');
    }

    public function email(Request $request): RedirectResponse
    {
        PerisaiFormulir::periksa($request);

        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
            'captcha' => [
                config('ppid.akun.captcha_aktif') ? 'required' : 'nullable',
                'string',
                new CaptchaBenar(),
            ],
        ]);

        $email = Str::lower($request->string('email')->toString());

        // Satu tautan per 30 menit per email / IP / perangkat.
        PembatasTautan::pastikanBoleh($request, PengirimanTautanAkun::JENIS_LUPA_PASSWORD, $email, 'email');

        try {
            PasswordBroker::broker('pemohon')->sendResetLink(['email' => $email]);
            PembatasTautan::catat($request, PengirimanTautanAkun::JENIS_LUPA_PASSWORD, $email);
        } catch (\Throwable $e) {
            Log::warning('[PPID] Gagal mengirim tautan reset password pemohon: '.$e->getMessage());
        }

        // Jawaban selalu sama, terkirim atau tidak, supaya tidak bisa dipakai
        // untuk menebak email mana yang terdaftar.
        return back()->with('status', __('Jika email tersebut terdaftar, tautan pengaturan ulang password sudah kami kirim.'));
    }

    public function reset(Request $request, string $token): View
    {
        return view('akun.reset-password', [
            'token' => $token,
            'email' => $request->string('email')->toString(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'string', 'email'],
            // Syaratnya disamakan dengan formulir pendaftaran; kalau lebih
            // longgar, reset password jadi jalan memutar untuk memasang
            // password lemah.
            'password' => ['required', 'confirmed', PasswordRule::defaults()->min(10)->mixedCase()->letters()->numbers()],
        ]);

        $status = PasswordBroker::broker('pemohon')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (Pemohon $pemohon, string $password) {
                $pemohon->forceFill(['password' => $password])->save();

                event(new PasswordReset($pemohon));
            }
        );

        if ($status !== PasswordBroker::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        return redirect()->route('akun.login')->with('status', __('Password berhasil diperbarui. Silakan masuk.'));
    }
}
