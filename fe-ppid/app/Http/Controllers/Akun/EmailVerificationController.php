<?php

namespace App\Http\Controllers\Akun;

use App\Http\Controllers\Controller;
use App\Models\Pemohon;
use App\Models\PengirimanTautanAkun;
use App\Support\PembatasTautan;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Verifikasi alamat email akun pengunjung.
 *
 * Karena email wajib terverifikasi *sebelum* bisa masuk, halaman ini juga
 * melayani pengunjung yang belum punya sesi: emailnya dititipkan di session
 * oleh SessionController atau RegisterController.
 */
class EmailVerificationController extends Controller
{
    /** Halaman "cek email Anda". */
    public function notice(Request $request): View|RedirectResponse
    {
        $pemohon = $this->pemohonMenunggu($request);

        if (!$pemohon) {
            return redirect()->route('akun.login');
        }

        if ($pemohon->hasVerifiedEmail()) {
            return Auth::guard('pemohon')->check()
                ? redirect()->route('akun.dashboard')
                : redirect()->route('akun.login');
        }

        return view('akun.verify-email', ['email' => $pemohon->email]);
    }

    /**
     * Tautan dari email. URL-nya bertanda tangan dan kedaluwarsa dalam 60
     * menit, jadi tidak bisa ditebak maupun dipakai ulang. Sengaja tidak
     * menuntut sesi login — tautan boleh dibuka di peramban mana pun.
     */
    public function verify(Request $request, string $id, string $hash): RedirectResponse
    {
        $pemohon = Pemohon::find($id);

        if (!$pemohon || !hash_equals(sha1($pemohon->getEmailForVerification()), $hash)) {
            abort(403);
        }

        if (!$pemohon->hasVerifiedEmail()) {
            $pemohon->markEmailAsVerified();
            event(new Verified($pemohon));
        }

        $request->session()->forget('verifikasi_email');

        if (Auth::guard('pemohon')->check()) {
            return redirect()->route('akun.dashboard')->with('status', __('Email Anda sudah terverifikasi.'));
        }

        return redirect()->route('akun.login')
            ->with('status', __('Email Anda sudah terverifikasi. Silakan masuk.'));
    }

    public function send(Request $request): RedirectResponse
    {
        $pemohon = $this->pemohonMenunggu($request);

        if ($pemohon && !$pemohon->hasVerifiedEmail()) {
            // Kirim ulang memakai jatah yang sama dengan pendaftaran: satu
            // tautan per 30 menit. Tanpa ini tombol "kirim ulang" jadi celah
            // yang membuat pembatasan di halaman daftar tidak ada gunanya.
            PembatasTautan::pastikanBoleh($request, PengirimanTautanAkun::JENIS_REGISTRASI, $pemohon->email, 'email');

            try {
                $pemohon->sendEmailVerificationNotification();
                PembatasTautan::catat($request, PengirimanTautanAkun::JENIS_REGISTRASI, $pemohon->email);
            } catch (\Throwable $e) {
                Log::warning('[PPID] Gagal mengirim ulang email verifikasi: '.$e->getMessage());

                return back()->with('status', __('Email verifikasi gagal dikirim. Hubungi petugas PPID bila berulang.'));
            }
        }

        // Jawaban selalu sama supaya halaman ini tidak bisa dipakai menebak
        // email mana yang terdaftar.
        return back()->with('status', __('Tautan verifikasi baru sudah dikirim ke email Anda.'));
    }

    /** Akun yang sedang menunggu verifikasi: dari sesi login atau titipan session. */
    private function pemohonMenunggu(Request $request): ?Pemohon
    {
        $pemohon = Auth::guard('pemohon')->user();

        if ($pemohon) {
            return $pemohon;
        }

        $email = $request->session()->get('verifikasi_email');

        return $email ? Pemohon::where('email', $email)->first() : null;
    }
}
