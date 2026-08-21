import { User } from '@auth/user';
import UserModel from '@auth/user/models/UserModel';
import { PartialDeep } from 'type-fest';
import { HTTPError } from 'ky';
import api from '@/utils/api';

type AuthResponse = {
	user: User;
	access_token: string;
};

/**
 * Endpoint asli PPID (project api-ppid / Laravel).
 * Path /api/v1/* diteruskan ke API lewat proxy dev server, jadi tidak kena CORS
 * saat development. Path /api/mock/* masih ditangani MSW untuk modul demo Fuse.
 */
const AUTH_PREFIX = 'v1/auth';

/**
 * Ambil body error API dan tempelkan ke `error.data` supaya form login bisa
 * menampilkannya per field. API mengirim array [{ type, message }].
 */
async function withErrorData<T>(run: () => Promise<T>): Promise<T> {
	try {
		return await run();
	} catch (error) {
		if (error instanceof HTTPError) {
			try {
				(error as HTTPError & { data?: unknown }).data = await error.response.clone().json();
			} catch {
				// body bukan JSON (mis. 502 dari proxy) — biarkan apa adanya
			}
		}

		throw error;
	}
}

/**
 * Refreshes the access token
 */
export async function authRefreshToken(): Promise<Response> {
	return api.post(`${AUTH_PREFIX}/refresh`, {
		retry: 0 // Don't retry refresh token requests
	});
}

/**
 * Sign in with token
 */
export async function authSignInWithToken(accessToken: string): Promise<Response> {
	return api.get(`${AUTH_PREFIX}/sign-in-with-token`, {
		headers: { Authorization: `Bearer ${accessToken}` }
	});
}

/**
 * Sign in
 */
export async function authSignIn(credentials: {
	email: string;
	password: string;
	captcha?: string;
	captcha_id?: string | null;
}): Promise<AuthResponse> {
	return withErrorData(() =>
		api
			.post(`${AUTH_PREFIX}/sign-in`, {
				json: credentials,
				// Kredensial tidak boleh dikirim ulang otomatis: setiap percobaan
				// yang gagal menaikkan hitungan kunci bertingkat di server, jadi
				// satu kali tekan tombol harus berarti tepat satu percobaan.
				retry: 0
			})
			.json<AuthResponse>()
	);
}

export type CaptchaResponse = {
	aktif: boolean;
	id: string | null;
	gambar: string | null;
};

/**
 * Ambil satu kode captcha beserta gambarnya.
 *
 * Tiap panggilan menghasilkan kode baru dan membatalkan yang sebelumnya, jadi
 * ini juga yang dipakai tombol "ganti gambar".
 */
export async function authAmbilCaptcha(): Promise<CaptchaResponse> {
	const hasil = await api.get(`${AUTH_PREFIX}/captcha`, { retry: 0 }).json<{ data: CaptchaResponse }>();

	return hasil.data;
}

/**
 * Minta tautan atur ulang password dikirim ke email.
 *
 * Jawaban server selalu sama, terdaftar atau tidak — itu disengaja, supaya
 * endpoint ini tidak bisa dipakai menebak email petugas mana yang ada.
 */
export async function authMintaResetPassword(payload: {
	email: string;
	captcha?: string;
	captcha_id?: string | null;
}): Promise<{ message: string }> {
	return withErrorData(() =>
		api.post(`${AUTH_PREFIX}/lupa-password`, { json: payload, retry: 0 }).json<{ message: string }>()
	);
}

/**
 * Pasang password baru memakai token dari email.
 */
export async function authPasangPasswordBaru(payload: {
	token: string;
	email: string;
	password: string;
	password_confirmation: string;
	captcha?: string;
	captcha_id?: string | null;
}): Promise<{ message: string }> {
	return withErrorData(() =>
		api.post(`${AUTH_PREFIX}/reset-password`, { json: payload, retry: 0 }).json<{ message: string }>()
	);
}

/**
 * Sign up.
 * Pendaftaran mandiri ditutup: akun admin hanya dibuat lewat modul Pengguna
 * oleh super admin, supaya tidak ada jalur pembuatan akun dari luar.
 */
export async function authSignUp(_data: {
	displayName: string;
	email: string;
	password: string;
}): Promise<AuthResponse> {
	return Promise.reject(new Error('Pendaftaran mandiri dinonaktifkan. Hubungi administrator untuk pembuatan akun.'));
}

/**
 * Get user by id
 */
export async function authGetDbUser(userId: string): Promise<User> {
	return api.get(`${AUTH_PREFIX}/user/${userId}`).json();
}

/**
 * Get user by email
 */
export async function authGetDbUserByEmail(email: string): Promise<User> {
	return api.get(`${AUTH_PREFIX}/user-by-email/${email}`).json();
}

/**
 * Update user
 */
export function authUpdateDbUser(user: PartialDeep<User>): Promise<Response> {
	return api.put(`${AUTH_PREFIX}/user/${user.id}`, {
		json: UserModel(user)
	});
}

/**
 * Create user
 */
export async function authCreateDbUser(user: PartialDeep<User>): Promise<User> {
	return api
		.post(`${AUTH_PREFIX}/user`, {
			json: UserModel(user)
		})
		.json();
}
