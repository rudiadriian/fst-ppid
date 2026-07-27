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
export async function authSignIn(credentials: { email: string; password: string }): Promise<AuthResponse> {
	return withErrorData(() =>
		api
			.post(`${AUTH_PREFIX}/sign-in`, {
				json: credentials
			})
			.json<AuthResponse>()
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
	return Promise.reject(
		new Error('Pendaftaran mandiri dinonaktifkan. Hubungi administrator untuk pembuatan akun.')
	);
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
