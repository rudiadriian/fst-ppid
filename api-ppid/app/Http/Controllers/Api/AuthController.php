<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            $credentials = $request->validate([
                'email' => ['required', 'email', 'max:150'],
                'password' => ['required', 'string', 'max:255'],
            ]);
        } catch (ValidationException $e) {
            return $this->fieldErrors($e->errors(), 422);
        }

        /** @var string|false $token */
        $token = Auth::guard('api')->attempt($credentials);

        if (!$token) {
            // Pesan sengaja tidak membedakan "email tidak ada" vs "password salah"
            // agar tidak bisa dipakai untuk enumerasi akun.
            AuditLogger::record(null, 'login_failed', null, null, null, [
                'email' => $credentials['email'],
            ]);

            return response()->json([
                ['type' => 'password', 'message' => 'Email atau kata sandi salah.'],
            ], 401);
        }

        /** @var User $user */
        $user = Auth::guard('api')->user();

        if (!$user->is_active || $user->deleted_at !== null) {
            Auth::guard('api')->logout();

            return response()->json([
                ['type' => 'email', 'message' => 'Akun ini nonaktif. Hubungi administrator.'],
            ], 403);
        }

        $user->forceFill(['last_login_at' => now()])->saveQuietly();
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

        return response()->json($user->toFuseUser(), 200, [
            'New-Access-Token' => Auth::guard('api')->refresh(),
        ]);
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
