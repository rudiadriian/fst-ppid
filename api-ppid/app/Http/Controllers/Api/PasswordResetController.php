<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\CaptchaBenar;
use App\Support\AuditLogger;
use App\Support\EmailAkunAdmin;
use App\Support\KunciLoginAdmin;
use App\Support\KunciTautanAdmin;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password as PasswordBroker;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;

/**
 * Lupa & atur ulang password akun panel admin.
 *
 * Memakai broker `users` bawaan (config/auth.php), yang menyimpan tokennya di
 * `password_reset_tokens` — tabel yang terpisah dari milik akun pengunjung,
 * sehingga token satu jenis akun tidak pernah bisa dipakai mengambil alih akun
 * jenis lain.
 *
 * Format galatnya sengaja sama dengan `AuthController`: array
 * `[{type, message}]`, supaya formulir di panel bisa menempelkan pesannya ke
 * isian yang bersangkutan tanpa cabang kode tersendiri.
 */
class PasswordResetController extends Controller
{
    /**
     * Kirim tautan atur ulang password.
     *
     * Jawabannya selalu sama, terkirim atau tidak, supaya endpoint ini tidak
     * bisa dipakai menebak email mana yang terdaftar sebagai petugas.
     */
    public function minta(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'email' => ['required', 'email', 'max:150'],
                'captcha_id' => ['nullable', 'string', 'max:64'],
                'captcha' => [
                    ...(config('ppid.akun.captcha_aktif') ? ['required'] : ['nullable']),
                    'string',
                    'max:16',
                    new CaptchaBenar($request->input('captcha_id')),
                ],
            ], [
                'captcha.required' => 'Kode captcha wajib diisi.',
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak sah.',
            ]);
        } catch (ValidationException $e) {
            return $this->galat($e->errors());
        }

        $email = Str::lower(trim($data['email']));

        $pemeriksaan = KunciTautanAdmin::periksa($request, $email);

        if ($pemeriksaan['terkunci']) {
            return response()->json([['type' => 'email', 'message' => $pemeriksaan['pesan']]], 429);
        }

        // Dihitung sebelum tahu emailnya terdaftar atau tidak. Kalau hanya yang
        // terdaftar yang dihitung, selisih perilakunya sendiri sudah cukup
        // untuk membedakan email yang ada dan yang tidak.
        $akibat = KunciTautanAdmin::catat($request, $email);

        if ($akibat['pesan'] !== null) {
            return response()->json([['type' => 'email', 'message' => $akibat['pesan']]], 429);
        }

        $user = User::denganEmail($email);
        $bolehTerimaTautan = $user && $user->is_active && $user->deleted_at === null && $user->disuspend_pada === null;

        /*
         * Alamat yang tidak berhak menerima tautan ditolak dengan alasannya.
         *
         * Sebelumnya semua jawaban diseragamkan supaya endpoint ini tidak bisa
         * dipakai memastikan alamat mana yang punya akun panel. Yang tidak
         * diperhitungkan: petugas yang salah mengetik alamatnya sendiri ikut
         * menerima "periksa folder Spam", lalu menunggu email yang tidak akan
         * pernah datang. Untuk panel tertutup dengan segelintir akun
         * institusional, kerugian itu lebih besar daripada informasi yang
         * dilindungi.
         *
         * Perilakunya bisa dikembalikan lewat `PPID_BERITAHU_EMAIL_ASING`;
         * lihat catatan lengkapnya di `config/ppid.php`.
         */
        if (!$bolehTerimaTautan && config('ppid.akun.beritahu_email_asing')) {
            return response()->json([[
                'type' => 'email',
                'message' => $this->alasanTidakBerhak($user),
            ]], 422);
        }

        if ($bolehTerimaTautan) {
            // Yang dikirim ke broker adalah email sebagaimana tersimpan, bukan
            // yang diketik: pencarian broker memakai `=` yang membedakan huruf
            // besar-kecil, sedangkan baris lama bisa saja belum dibakukan.
            $status = PasswordBroker::broker('users')->sendResetLink(['email' => $user->email]);

            if ($status === PasswordBroker::RESET_LINK_SENT) {
                KunciTautanAdmin::catatPengiriman($request, $email);
            }

            AuditLogger::record($user->id, 'reset_password_diminta', User::class, $user->id, null, [
                'email' => $email,
            ]);
        }

        return response()->json([
            'message' => config('ppid.akun.beritahu_email_asing')
                ? 'Tautan atur ulang password sudah dikirim ke email tersebut. Periksa juga folder Spam.'
                : 'Jika email tersebut terdaftar sebagai akun panel, tautan atur ulang password sudah kami kirim. '
                    .'Periksa juga folder Spam.',
        ]);
    }

    /**
     * Kenapa satu alamat tidak berhak menerima tautan.
     *
     * Tiga keadaan dibedakan karena tindak lanjutnya berbeda: yang pertama
     * salah alamat, dua sisanya harus menghubungi administrator.
     */
    private function alasanTidakBerhak(?User $user): string
    {
        if (!$user || $user->deleted_at !== null) {
            return 'Email ini tidak terdaftar sebagai akun panel. Periksa kembali ejaannya, '
                .'atau hubungi administrator bila Anda merasa seharusnya punya akun.';
        }

        if ($user->disuspend_pada !== null) {
            return 'Akun ini disuspend, sehingga tautan atur ulang password tidak dapat dikirim. '
                .'Hubungi administrator untuk membukanya kembali.';
        }

        return 'Akun ini nonaktif, sehingga tautan atur ulang password tidak dapat dikirim. '
            .'Hubungi administrator.';
    }

    /**
     * Pasang password baru memakai token dari email.
     */
    public function pasang(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'token' => ['required', 'string'],
                'email' => ['required', 'email', 'max:150'],
                /*
                 * Syaratnya tidak boleh lebih longgar daripada saat akun dibuat
                 * di modul Pengguna — kalau lebih longgar, "lupa password"
                 * berubah menjadi jalan memutar untuk memasang password lemah.
                 */
                'password' => [
                    'required',
                    'confirmed',
                    PasswordRule::min(10)->mixedCase()->letters()->numbers(),
                ],
                'captcha_id' => ['nullable', 'string', 'max:64'],
                'captcha' => [
                    ...(config('ppid.akun.captcha_aktif') ? ['required'] : ['nullable']),
                    'string',
                    'max:16',
                    new CaptchaBenar($request->input('captcha_id')),
                ],
            ], [
                'captcha.required' => 'Kode captcha wajib diisi.',
                'password.required' => 'Password baru wajib diisi.',
                'password.confirmed' => 'Ulangan password tidak sama.',
                'token.required' => 'Tautan tidak lengkap. Buka kembali tautan dari email Anda.',
            ]);
        } catch (ValidationException $e) {
            return $this->galat($e->errors());
        }

        $email = Str::lower(trim($data['email']));
        // Sama seperti pada `minta()`: broker mencari dengan `=`, jadi yang
        // diserahkan harus email sebagaimana tersimpan.
        $emailTersimpan = User::denganEmail($email)?->email ?? $email;

        $status = PasswordBroker::broker('users')->reset(
            [
                'email' => $emailTersimpan,
                'password' => $data['password'],
                'password_confirmation' => $request->input('password_confirmation'),
                'token' => $data['token'],
            ],
            function (User $user, string $password) use ($request, $email) {
                $user->timestamps = false;
                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->saveQuietly();
                $user->timestamps = true;

                /*
                 * Password baru menutup keadaan yang membuat kuncinya dipasang.
                 * Membiarkan hitungannya berarti orang yang baru saja
                 * membuktikan dirinya pemilik email itu tetap tidak bisa masuk
                 * — persis kebalikan dari gunanya fitur ini.
                 */
                KunciLoginAdmin::bersihkan($request, $email);
                KunciTautanAdmin::bersihkan($email);

                AuditLogger::record($user->id, 'reset_password_selesai', User::class, $user->id);

                event(new PasswordReset($user));

                EmailAkunAdmin::passwordDiubah($user);
            }
        );

        if ($status !== PasswordBroker::PASSWORD_RESET) {
            return response()->json([[
                'type' => 'email',
                'message' => $status === PasswordBroker::INVALID_TOKEN
                    ? 'Tautan sudah kedaluwarsa atau pernah dipakai. Minta tautan baru dari halaman Lupa password.'
                    : 'Email tidak dikenali atau tautannya tidak berlaku.',
            ]], 422);
        }

        return response()->json(['message' => 'Password berhasil diperbarui. Silakan masuk dengan password baru.']);
    }

    /** @param  array<string, array<int, string>>  $errors */
    private function galat(array $errors, int $status = 422): JsonResponse
    {
        $isi = [];

        foreach ($errors as $field => $pesan) {
            $isi[] = ['type' => $field, 'message' => $pesan[0]];
        }

        return response()->json($isi, $status);
    }
}
