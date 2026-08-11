<?php

namespace App\Http\Controllers\Akun;

use App\Http\Controllers\Controller;
use App\Http\Requests\Akun\LoginPemohonRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/** Masuk & keluar akun pengunjung (guard `pemohon`). */
class SessionController extends Controller
{
    public function create(): View
    {
        return view('akun.login');
    }

    public function store(LoginPemohonRequest $request): RedirectResponse
    {
        $request->authenticate();

        $pemohon = Auth::guard('pemohon')->user();

        /*
         * Email wajib terverifikasi sebelum akun bisa dipakai. Sesi yang
         * terlanjur dibuat langsung dibatalkan, lalu pengguna diarahkan ke
         * halaman verifikasi yang bisa mengirim ulang tautannya.
         */
        if (config('ppid.akun.wajib_verifikasi_email') && !$pemohon->hasVerifiedEmail()) {
            Auth::guard('pemohon')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Dipakai halaman verifikasi untuk tahu email mana yang menunggu.
            $request->session()->put('verifikasi_email', $pemohon->email);

            return redirect()->route('akun.verifikasi.notice')
                ->with('status', __('Email Anda belum diverifikasi. Buka tautan verifikasi yang kami kirim, atau minta tautan baru di bawah ini.'));
        }

        // Cegah session fixation: ID session diganti setelah kredensial diterima.
        $request->session()->regenerate();

        return redirect()->intended(route('akun.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('pemohon')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('ppid.home')->with('status', __('Anda telah keluar dari akun.'));
    }
}
