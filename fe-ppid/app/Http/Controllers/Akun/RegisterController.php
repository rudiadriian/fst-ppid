<?php

namespace App\Http\Controllers\Akun;

use App\Http\Controllers\Controller;
use App\Models\Pemohon;
use App\Models\PengirimanTautanAkun;
use App\Rules\CaptchaBenar;
use App\Support\NotifikasiAdmin;
use App\Support\PembatasTautan;
use App\Support\PerisaiFormulir;
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
        // Honeypot dan jeda pengisian diperiksa lebih dulu supaya kiriman bot
        // tidak sempat menyentuh basis data maupun antrean email.
        PerisaiFormulir::periksa($request);

        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255'],
            // Nomor WhatsApp dipakai petugas untuk mengonfirmasi berkas
            // verifikasi, jadi wajib diisi.
            'no_hp' => ['required', 'string', 'max:20', 'regex:/^[0-9+()\-\s]{8,20}$/'],
            'password' => [
                'required',
                'confirmed',
                Password::defaults()->min(10)->mixedCase()->letters()->numbers(),
            ],
            'setuju' => ['accepted'],
            // Wajibnya ikut sakelar konfigurasi: saat captcha dimatikan,
            // isiannya juga tidak dirender, jadi menuntutnya berarti formulir
            // tidak akan pernah bisa dikirim.
            'captcha' => [
                config('ppid.akun.captcha_aktif') ? 'required' : 'nullable',
                'string',
                new CaptchaBenar(),
            ],
        ], [
            'no_hp.regex' => __('Nomor telepon hanya boleh berisi angka, spasi, dan tanda + ( ) -.'),
        ]);

        $email = Str::lower($data['email']);

        // Satu tautan per 30 menit per email / IP / perangkat. Diperiksa sebelum
        // baris pemohon dibuat supaya percobaan yang ditolak tidak meninggalkan
        // akun setengah jadi.
        PembatasTautan::pastikanBoleh($request, PengirimanTautanAkun::JENIS_REGISTRASI, $email, 'email');

        $pemohon = Pemohon::where('email', $email)->first();

        if ($pemohon && $pemohon->verifikasiDiblokir()) {
            // Berkasnya sudah ditolak sampai batas. Mendaftar ulang dengan email
            // yang sama tidak boleh dipakai untuk mengulang dari nol.
            throw ValidationException::withMessages([
                'email' => __('Pendaftaran dengan email ini diblokir karena data diri sudah ditolak :batas kali. Hubungi petugas PPID untuk melanjutkan.', [
                    'batas' => Pemohon::BATAS_DITOLAK,
                ]),
            ]);
        }

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
                'no_hp' => $data['no_hp'],
                'password' => $data['password'],
                'email_verified_at' => null,
            ])->save();
        } else {
            $pemohon = Pemohon::create([
                'nama' => $data['nama'],
                'email' => $email,
                'no_hp' => $data['no_hp'],
                'password' => $data['password'],
                'jenis_pemohon' => 'perorangan',
            ]);
        }

        // Petugas diberi tahu lewat lonceng notifikasi be-ppid; kegagalannya
        // ditelan di dalam NotifikasiAdmin sendiri.
        NotifikasiAdmin::pendaftaranBaru($pemohon);

        // Email verifikasi tidak boleh menggagalkan pendaftaran kalau SMTP mati.
        try {
            event(new Registered($pemohon));
            PembatasTautan::catat($request, PengirimanTautanAkun::JENIS_REGISTRASI, $email);
        } catch (\Throwable $e) {
            Log::warning('[PPID] Gagal mengirim email verifikasi pemohon: '.$e->getMessage());
        }

        /*
         * Tidak langsung masuk: email harus dibuktikan dulu. Pendaftar
         * dikembalikan ke halaman masuk dengan peringatan, bukan ke halaman
         * verifikasi — halaman masuk adalah tempat ia akan kembali setelah
         * membuka tautan di emailnya, jadi alurnya tidak bercabang.
         *
         * Emailnya dititipkan di session supaya tautan "kirim ulang" pada
         * halaman verifikasi tetap bisa dipakai tanpa sesi login.
         */
        if (config('ppid.akun.wajib_verifikasi_email')) {
            $request->session()->put('verifikasi_email', $pemohon->email);

            return redirect()->route('akun.login')
                ->with('peringatan', __('Akun berhasil dibuat. Sebelum masuk, buka dulu tautan verifikasi yang kami kirim ke :email. Tautannya berlaku :jam jam.', [
                    'email' => $pemohon->email,
                    'jam' => (int) (config('auth.verification.expire', 1440) / 60),
                ]));
        }

        Auth::guard('pemohon')->login($pemohon);
        $request->session()->regenerate();

        return redirect()->intended(route('akun.dashboard'))
            ->with('status', __('Akun berhasil dibuat.'));
    }
}
