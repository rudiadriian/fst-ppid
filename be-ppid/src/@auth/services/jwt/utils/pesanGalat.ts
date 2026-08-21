import { HTTPError, TimeoutError } from 'ky';

/**
 * Galat per isian, sebagaimana dikirim api-ppid: `[{ type, message }]`.
 */
export type GalatIsian = {
	type: string;
	message: string;
};

export type GalatTerbaca = {
	/** Pesan untuk ditampilkan sebagai spanduk di atas formulir. */
	ringkasan: string;
	/** Galat yang bisa ditempelkan ke isian tertentu. */
	isian: GalatIsian[];
	/** Status HTTP, bila permintaannya sempat sampai ke server. */
	status: number | null;
	/**
	 * Benar bila permintaannya tidak pernah sampai — API mati, salah alamat,
	 * atau jaringan putus. Halaman auth memakainya untuk menyarankan tindakan
	 * yang berbeda: memeriksa server, bukan memeriksa ketikan.
	 */
	jaringan: boolean;
};

const KOSONG: GalatIsian[] = [];

/**
 * Ubah galat apa pun dari lapisan HTTP menjadi sesuatu yang bisa dibaca orang.
 *
 * Sebelum ada ini, formulir auth hanya membaca `error.data` dan menempelkannya
 * ke isian. Akibatnya seluruh kegagalan yang **tidak** berbentuk galat validasi
 * — API belum dinyalakan, proxy menjawab 502, koneksi putus di tengah — tidak
 * memunculkan apa pun: tombol berhenti berputar dan halaman diam saja, seolah
 * tidak terjadi apa-apa. Itu keadaan terburuk untuk halaman masuk, karena orang
 * akan mencoba lagi dengan password berbeda dan menghabiskan jatah percobaannya
 * sendiri.
 *
 * Setiap cabang di bawah karena itu wajib menghasilkan kalimat, bukan string
 * kosong.
 */
export async function bacaGalat(error: unknown): Promise<GalatTerbaca> {
	// Permintaan tidak pernah sampai ke server.
	if (error instanceof TimeoutError) {
		return {
			ringkasan:
				'Server tidak menjawab dalam waktu yang wajar. Periksa apakah layanan API sedang berjalan, lalu coba lagi.',
			isian: KOSONG,
			status: null,
			jaringan: true
		};
	}

	if (!(error instanceof HTTPError)) {
		/*
		 * `fetch` melempar TypeError untuk kegagalan jaringan — termasuk saat
		 * server API mati sama sekali. Pesan aslinya ("Failed to fetch") tidak
		 * menolong siapa pun, jadi diganti keterangan yang menyebut kemungkinan
		 * penyebabnya.
		 */
		const pesan = error instanceof Error ? error.message : '';

		return {
			ringkasan:
				'Tidak dapat menghubungi server API. Pastikan layanan api-ppid sedang berjalan, lalu coba lagi.' +
				(pesan ? ` (${pesan})` : ''),
			isian: KOSONG,
			status: null,
			jaringan: true
		};
	}

	const { status } = error.response;

	let body: unknown = null;

	try {
		body = await error.response.clone().json();
	} catch {
		// Bukan JSON — mis. halaman galat HTML dari proxy. Biarkan null;
		// cabang di bawah sudah menyediakan kalimat untuk tiap status.
	}

	// Bentuk baku api-ppid: array galat per isian.
	if (Array.isArray(body) && body.length > 0) {
		const isian = body.filter(
			(item): item is GalatIsian =>
				typeof item === 'object' && item !== null && typeof (item as GalatIsian).message === 'string'
		);

		if (isian.length > 0) {
			return {
				ringkasan: isian[0].message,
				isian,
				status,
				jaringan: false
			};
		}
	}

	// Bentuk lain: { message } atau { error }.
	const pesanBody =
		typeof body === 'object' && body !== null
			? ((body as { message?: unknown; error?: unknown }).message ??
				(body as { message?: unknown; error?: unknown }).error)
			: null;

	if (typeof pesanBody === 'string' && pesanBody !== '') {
		return { ringkasan: pesanBody, isian: KOSONG, status, jaringan: false };
	}

	return {
		ringkasan: pesanUmum(status),
		isian: KOSONG,
		status,
		jaringan: false
	};
}

/**
 * Kalimat cadangan ketika badan jawabannya tidak bisa dibaca.
 *
 * Statusnya tetap disebut di ujung kalimat: kalau nanti ada yang melaporkan
 * masalah ini, angka itulah yang membedakan "salah kredensial" dari "server
 * tumbang" tanpa perlu membuka alat pengembang.
 */
function pesanUmum(status: number): string {
	switch (status) {
		case 401:
			return 'Email atau kata sandi salah.';
		case 403:
			return 'Akses ditolak. Hubungi administrator bila ini seharusnya tidak terjadi.';
		case 404:
			return 'Alamat endpoint tidak ditemukan. Versi panel dan API kemungkinan tidak cocok. (404)';
		case 419:
		case 429:
			return 'Terlalu banyak percobaan. Tunggu sebentar sebelum mencoba lagi. (429)';
		case 500:
			return 'Server mengalami galat saat memproses permintaan. Coba lagi; bila berulang, hubungi administrator. (500)';
		case 502:
		case 503:
		case 504:
			return `Layanan API sedang tidak dapat dihubungi. Pastikan api-ppid berjalan, lalu coba lagi. (${status})`;
		default:
			return `Permintaan gagal diproses. (${status})`;
	}
}
