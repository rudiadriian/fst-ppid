import { HTTPError } from 'ky';
import api from '@/utils/api';

/**
 * Klien HTTP tunggal untuk seluruh modul CMS PPID.
 *
 * Kontrak endpoint sengaja seragam (`/api/v1/{resource}`) sehingga satu klien
 * ini melayani semua modul. Menambah modul baru cukup menambah konfigurasi,
 * tidak perlu menulis service baru.
 */

export type PaginatedMeta = {
	total: number;
	page: number;
	per_page: number;
	last_page: number;
};

export type PaginatedResponse<T> = {
	data: T[];
	meta: PaginatedMeta;
};

export type ListParams = Record<string, string | number | boolean | undefined | null>;

export type ApiRecord = Record<string, unknown> & { id: number };

/** Bentuk error validasi Laravel: { message, errors: { field: [pesan] } } */
export type ApiErrorBody = {
	message?: string;
	error?: string;
	errors?: Record<string, string[]>;
};

export class PpidApiError extends Error {
	status: number;

	errors: Record<string, string[]>;

	constructor(message: string, status: number, errors: Record<string, string[]> = {}) {
		super(message);
		this.name = 'PpidApiError';
		this.status = status;
		this.errors = errors;
	}
}

/**
 * Ubah HTTPError ky menjadi error yang membawa pesan per field, supaya form
 * bisa menempelkan pesan validasi ke input yang bersangkutan.
 */
async function toPpidError(error: unknown): Promise<never> {
	if (error instanceof HTTPError) {
		let body: ApiErrorBody = {};

		try {
			body = (await error.response.clone().json()) as ApiErrorBody;
		} catch {
			// Body bukan JSON (mis. 502 dari proxy) — pakai pesan bawaan.
		}

		const pesan =
			body.message ||
			body.error ||
			(error.response.status === 403
				? 'Role Anda tidak punya hak untuk aksi ini.'
				: 'Permintaan gagal diproses.');

		throw new PpidApiError(pesan, error.response.status, body.errors ?? {});
	}

	throw error;
}

function bersihkanParams(params: ListParams): Record<string, string> {
	return Object.entries(params).reduce<Record<string, string>>((hasil, [kunci, nilai]) => {
		if (nilai !== undefined && nilai !== null && nilai !== '') {
			hasil[kunci] = String(nilai);
		}

		return hasil;
	}, {});
}

export const ppidApi = {
	async list<T = ApiRecord>(resource: string, params: ListParams = {}): Promise<PaginatedResponse<T>> {
		try {
			return await api
				.get(`v1/${resource}`, { searchParams: bersihkanParams(params) })
				.json<PaginatedResponse<T>>();
		} catch (error) {
			return toPpidError(error);
		}
	},

	async get<T = ApiRecord>(resource: string, id: number | string): Promise<T> {
		try {
			const hasil = await api.get(`v1/${resource}/${id}`).json<{ data: T }>();
			return hasil.data;
		} catch (error) {
			return toPpidError(error);
		}
	},

	async create<T = ApiRecord>(resource: string, payload: unknown): Promise<T> {
		try {
			const hasil = await api.post(`v1/${resource}`, { json: payload }).json<{ data: T }>();
			return hasil.data;
		} catch (error) {
			return toPpidError(error);
		}
	},

	async update<T = ApiRecord>(resource: string, id: number | string, payload: unknown): Promise<T> {
		try {
			const hasil = await api.put(`v1/${resource}/${id}`, { json: payload }).json<{ data: T }>();
			return hasil.data;
		} catch (error) {
			return toPpidError(error);
		}
	},

	async remove(resource: string, id: number | string): Promise<void> {
		try {
			await api.delete(`v1/${resource}/${id}`);
		} catch (error) {
			await toPpidError(error);
		}
	},

	async removeMany(resource: string, ids: number[]): Promise<void> {
		try {
			await api.post(`v1/${resource}/hapus-massal`, { json: { ids } });
		} catch (error) {
			await toPpidError(error);
		}
	},

	/** Baca endpoint non-CRUD ber-GET (mis. `laporan-layanan/rekap?tahun=2026`). */
	async ambil<T = unknown>(path: string, params: ListParams = {}): Promise<T> {
		try {
			const hasil = await api.get(`v1/${path}`, { searchParams: bersihkanParams(params) }).json<{ data: T }>();
			return hasil.data;
		} catch (error) {
			return toPpidError(error);
		}
	},

	/**
	 * Unduh berkas dari endpoint yang menuntut token, mis. KTP pemohon.
	 *
	 * Tidak bisa dipasang langsung sebagai `src` gambar/iframe karena peramban
	 * tidak menyertakan header Authorization pada permintaan subresource —
	 * berkasnya diambil dulu sebagai blob, lalu dipakai lewat object URL.
	 * Pemanggil wajib memanggil `URL.revokeObjectURL` setelah selesai.
	 */
	async berkas(path: string): Promise<{ objectUrl: string; tipe: string }> {
		try {
			const respons = await api.get(`v1/${path}`);
			const blob = await respons.blob();

			return { objectUrl: URL.createObjectURL(blob), tipe: blob.type };
		} catch (error) {
			return toPpidError(error);
		}
	},

	/** Simpan (PUT) endpoint non-CRUD, mis. `role/3/akses`. */
	async simpan<T = unknown>(path: string, payload: unknown): Promise<T> {
		try {
			return await api.put(`v1/${path}`, { json: payload }).json<T>();
		} catch (error) {
			return toPpidError(error);
		}
	},

	/** Aksi non-CRUD (transisi status, approval, rekap, simpan massal). */
	async action<T = unknown>(path: string, payload?: unknown): Promise<T> {
		try {
			return await api.post(`v1/${path}`, payload === undefined ? undefined : { json: payload }).json<T>();
		} catch (error) {
			return toPpidError(error);
		}
	},

	async upload(
		file: File,
		folder: string,
		jenis: 'gambar' | 'dokumen' | 'dokumen_gambar' | 'video'
	): Promise<{ path: string; url: string; nama_file: string; ukuran_file: number; tipe_file: string }> {
		const form = new FormData();
		form.append('file', file);
		form.append('folder', folder);
		form.append('jenis', jenis);

		try {
			const hasil = await api
				.post('v1/uploads', { body: form, timeout: 120_000 })
				.json<{ data: { path: string; url: string; nama_file: string; ukuran_file: number; tipe_file: string } }>();

			return hasil.data;
		} catch (error) {
			return toPpidError(error);
		}
	}
};

export default ppidApi;
