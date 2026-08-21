<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\CaptchaBenar;
use App\Support\AuditLogger;
use App\Support\KunciLoginAdmin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Exceptions\JWTException;

/**
 * Endpoint autentikasi panel admin.
 *
 * Kontrak response sengaja dibuat sama dengan yang diharapkan Fuse React
 * (`{ user, access_token }` dan header `New-Access-Token`) supaya frontend
 * admin tidak perlu diubah strukturnya.
 */
class AuthController extends Controller
{
    public function signIn(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'email' => ['required', 'email', 'max:150'],
                'password' => ['required', 'string', 'max:255'],
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
                'password.required' => 'Kata sandi wajib diisi.',
            ]);
        } catch (ValidationException $e) {
            return $this->fieldErrors($e->errors(), 422);
        }

        $credentials = ['email' => $data['email'], 'password' => $data['password']];

        /*
         * Satu-satunya penghitung kegagalan jangka panjang adalah tangga
         * bertingkat di `KunciLoginAdmin`.
         *
         * Sebelumnya ada penghitung kedua di kelas ini — 10 kegagalan per 15
         * menit lewat cache. Dua penghitung untuk pekerjaan yang sama bukan
         * pertahanan berlapis melainkan saling menutupi: yang lebih longgar
         * berbunyi lebih dulu, sehingga tangga yang seharusnya berakhir di
         * suspend pada kegagalan ke-12 tidak pernah sampai ke sana. Yang
         * bertahan adalah yang lebih kuat: tangganya menyimpan hitungan di
         * basis data (tidak hilang bila cache dibersihkan), masa tunggunya naik
         * sampai 14 hari, dan ujungnya menutup akun.
         *
         * `throttle:login` di berkas rute tetap ada dan tidak tumpang-tindih:
         * jendelanya satu menit dan tugasnya menahan banjir, bukan menghitung
         * kegagalan sepanjang hari.
         */
        $kunci = KunciLoginAdmin::periksa($request, $credentials['email']);

        if ($kunci['terkunci']) {
            AuditLogger::record(null, 'login_locked', null, null, null, [
                'email' => $credentials['email'],
            ]);

            return response()->json([['type' => 'password', 'message' => $kunci['pesan']]], 429);
        }

        /*
         * Akun yang sudah disuspend dihentikan sebelum passwordnya diperiksa.
         *
         * Kalau tidak, password yang benar tetap menghabiskan ~230 ms bcrypt
         * pada akun yang jelas-jelas tidak boleh masuk — dan yang lebih penting,
         * pemiliknya akan mendapat pesan "kata sandi salah" yang menyesatkan
         * alih-alih tahu bahwa akunnya perlu dibuka administrator.
         */
        $akun = User::denganEmail($credentials['email']);

        if ($akun && $akun->disuspend_pada !== null) {
            AuditLogger::record(null, 'login_suspended', User::class, $akun->id, null, [
                'email' => $akun->email,
            ]);

            return response()->json([[
                'type' => 'email',
                'message' => 'Akun ini disuspend karena aktivitas masuk yang mencurigakan. '
                    .'Hubungi administrator untuk membukanya kembali.',
            ]], 403);
        }

        /** @var string|false $token */
        $token = Auth::guard('api')->attempt($credentials);

        if (!$token) {
            /*
             * Email yang tidak terdaftar diperlakukan persis sama dengan
             * password yang salah — termasuk ikut menaikkan hitungan kunci.
             *
             * Permintaan "hanya email terdaftar yang bisa menjalankan fitur
             * auth" dipenuhi dengan menolaknya, bukan dengan mengatakannya:
             * jawaban yang membedakan keduanya berubah menjadi alat untuk
             * mendaftar email petugas mana saja yang ada di sistem ini.
             */
            $akibat = KunciLoginAdmin::catatGagal($request, $credentials['email']);

            AuditLogger::record(null, 'login_failed', null, null, null, [
                'email' => $credentials['email'],
                'terdaftar' => $akun !== null,
            ]);

            /*
             * Status dibedakan supaya panel tidak perlu membaca kalimatnya
             * untuk tahu apa yang terjadi: 403 akun ditutup, 429 sedang dalam
             * masa tunggu, 401 masih boleh mencoba lagi. Kegagalan yang tepat
             * memicu kunci baru dijawab 429, bukan 401 — pesannya sudah
             * menyuruh menunggu, jadi statusnya harus mengatakan hal yang sama.
             */
            $status = match (true) {
                $akibat['suspend'] => 403,
                $akibat['terkunci'] => 429,
                default => 401,
            };

            return response()->json([
                ['type' => 'password', 'message' => $akibat['pesan']],
            ], $status);
        }

        KunciLoginAdmin::bersihkan($request, $credentials['email']);

        /** @var User $user */
        $user = Auth::guard('api')->user();

        if (!$user->is_active || $user->deleted_at !== null) {
            Auth::guard('api')->logout();

            return response()->json([
                ['type' => 'email', 'message' => 'Akun ini nonaktif. Hubungi administrator.'],
            ], 403);
        }

        // Cap waktu login bukan penyuntingan data pengguna: `timestamps`
        // dimatikan sebentar supaya kolom "Diubah" pada modul Pengguna tidak
        // ikut terisi setiap kali orangnya masuk.
        $user->timestamps = false;
        $user->forceFill(['last_login_at' => now()])->saveQuietly();
        $user->timestamps = true;
        $user->loadMissing('role');

        AuditLogger::record($user->id, 'login', User::class, $user->id);

        return response()->json([
            'user' => $user->toFuseUser(),
            'access_token' => $token,
        ]);
    }

    /**
     * Auto-login memakai token yang tersimpan di browser.
     */
    public function signInWithToken(Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = Auth::guard('api')->user();

        if (!$user || !$user->is_active) {
            return response()->json(['error' => 'Token tidak valid atau user tidak ditemukan'], 401);
        }

        $user->loadMissing('role');

        /*
        Token sengaja tidak diputar di sini. `refresh()` langsung memasukkan
        token lama ke blacklist, sedangkan panel admin biasanya menembakkan
        beberapa permintaan sekaligus saat halaman dimuat; permintaan yang
        masih membawa token lama akan ditolak 401 dan pengguna ikut terlempar
        keluar. Pembaruan token dilakukan lewat endpoint `auth/refresh`.
        */
        return response()->json($user->toFuseUser());
    }

    public function refresh(): JsonResponse
    {
        try {
            $token = Auth::guard('api')->refresh();
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token tidak dapat diperbarui'], 401);
        }

        return response()->json(null, 200, ['New-Access-Token' => $token]);
    }

    public function signOut(): JsonResponse
    {
        $userId = Auth::guard('api')->id();

        Auth::guard('api')->logout(); // token masuk blacklist

        AuditLogger::record($userId, 'logout', User::class, $userId);

        return response()->json(['message' => 'Berhasil keluar']);
    }

    /**
     * Update preferensi user yang sedang login (shortcut & setting tampilan).
     * Hanya kolom milik UI yang boleh diubah lewat sini.
     */
    public function updateUser(Request $request, string $id): JsonResponse
    {
        /** @var User $user */
        $user = Auth::guard('api')->user();

        if ((string) $user->id !== $id) {
            return response()->json(['error' => 'Tidak diizinkan'], 403);
        }

        $data = $request->validate([
            'displayName' => ['sometimes', 'string', 'max:150'],
            'photoURL' => ['sometimes', 'nullable', 'string', 'max:500'],
            'shortcuts' => ['sometimes', 'array'],
            'shortcuts.*' => ['string', 'max:100'],
            'settings' => ['sometimes', 'array'],
        ]);

        $user->fill(array_filter([
            'name' => $data['displayName'] ?? null,
            'photo_url' => $data['photoURL'] ?? null,
        ], fn ($v) => $v !== null));

        if (array_key_exists('shortcuts', $data)) {
            $user->shortcuts = $data['shortcuts'];
        }

        if (array_key_exists('settings', $data)) {
            $user->settings = $data['settings'];
        }

        $user->save();
        $user->loadMissing('role');

        return response()->json($user->toFuseUser());
    }

    /**
     * Ubah error validasi Laravel ke format array {type, message}
     * yang dipakai form login Fuse.
     */
    private function fieldErrors(array $errors, int $status): JsonResponse
    {
        $payload = [];

        foreach ($errors as $field => $messages) {
            $payload[] = ['type' => $field, 'message' => $messages[0]];
        }

        return response()->json($payload, $status);
    }
}
