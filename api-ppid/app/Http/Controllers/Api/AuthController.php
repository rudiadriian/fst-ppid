<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
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
    /**
     * Kunci akun sementara setelah sekian kali gagal berturut-turut.
     *
     * Melengkapi `throttle:login` yang jendelanya hanya satu menit: pembatas
     * per menit masih mengizinkan penebakan pelan-pelan sepanjang hari, sedang
     * hitungan ini memakai jendela panjang sehingga percobaan yang sabar pun
     * ikut tertahan.
     */
    private const GAGAL_MAKSIMUM = 10;

    /** Lama kunci sekaligus lama jendela hitungan, dalam detik. */
    private const LAMA_KUNCI = 900;

    public function signIn(Request $request): JsonResponse
    {
        try {
            $credentials = $request->validate([
                'email' => ['required', 'email', 'max:150'],
                'password' => ['required', 'string', 'max:255'],
            ]);
        } catch (ValidationException $e) {
            return $this->fieldErrors($e->errors(), 422);
        }

        $kunciGagal = $this->kunciGagal($request, $credentials['email']);

        if (RateLimiter::tooManyAttempts($kunciGagal, self::GAGAL_MAKSIMUM)) {
            $detik = RateLimiter::availableIn($kunciGagal);

            AuditLogger::record(null, 'login_locked', null, null, null, [
                'email' => $credentials['email'],
            ]);

            return response()->json([
                ['type' => 'password', 'message' => "Terlalu banyak percobaan masuk. Coba lagi dalam {$detik} detik."],
            ], 429);
        }

        /** @var string|false $token */
        $token = Auth::guard('api')->attempt($credentials);

        if (!$token) {
            RateLimiter::hit($kunciGagal, self::LAMA_KUNCI);

            // Pesan sengaja tidak membedakan "email tidak ada" vs "password salah"
            // agar tidak bisa dipakai untuk enumerasi akun.
            AuditLogger::record(null, 'login_failed', null, null, null, [
                'email' => $credentials['email'],
            ]);

            return response()->json([
                ['type' => 'password', 'message' => 'Email atau kata sandi salah.'],
            ], 401);
        }

        RateLimiter::clear($kunciGagal);

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

    /**
     * Kunci hitungan kegagalan: per kombinasi email + IP.
     *
     * Dipisah per email supaya satu penyerang tidak bisa mengunci akun orang
     * lain hanya dengan menghabiskan jatah dari IP-nya sendiri, dan dipisah per
     * IP supaya kegagalan sah dari kantor yang sama tidak saling menjatuhkan.
     */
    private function kunciGagal(Request $request, string $email): string
    {
        return 'admin-login|'.Str::transliterate(Str::lower($email)).'|'.$request->ip();
    }
}
