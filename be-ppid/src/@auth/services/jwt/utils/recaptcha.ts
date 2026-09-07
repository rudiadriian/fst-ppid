/**
 * Google reCAPTCHA v3 untuk formulir masuk dan pemulihan password panel.
 *
 * Menggantikan captcha gambar yang dulu diambil dari `v1/auth/captcha`. v3
 * tidak meminta apa pun dari orangnya: skrip Google menilai perilaku halaman
 * lalu mengeluarkan token sekali pakai, dan server yang memutuskan lolos atau
 * tidak berdasarkan skornya.
 *
 * Skripnya dimuat sesuai kebutuhan, bukan dari `index.html`. Dua alasannya:
 * lencana reCAPTCHA hanya pantas muncul di halaman yang memang dilindunginya,
 * dan sebagian besar panel adalah halaman di balik login yang tidak perlu ikut
 * menanggung unduhan skrip pihak ketiga.
 */

const SITE_KEY = (import.meta.env.VITE_RECAPTCHA_SITE_KEY as string | undefined) ?? '';

const ID_SKRIP = 'recaptcha-v3';

type Grecaptcha = {
	ready: (siap: () => void) => void;
	execute: (siteKey: string, opsi: { action: string }) => Promise<string>;
};

declare global {
	interface Window {
		grecaptcha?: Grecaptcha;
	}
}

/**
 * Menyala hanya bila site key terpasang saat build.
 *
 * Kalau kosong, formulir tetap bisa dikirim tanpa token — dan server yang
 * menolaknya bila `PPID_RECAPTCHA_AKTIF` di sana menyala. Sengaja begitu:
 * satu-satunya tempat yang boleh memutuskan captcha wajib atau tidak adalah
 * server, karena apa pun yang diputuskan peramban bisa dilewati penyerang.
 */
export function recaptchaAktif(): boolean {
	return SITE_KEY !== '';
}

/** Janji pemuatan skrip; dipakai bersama supaya tidak dimuat dua kali. */
let pemuatan: Promise<Grecaptcha> | null = null;

function muat(): Promise<Grecaptcha> {
	if (pemuatan) {
		return pemuatan;
	}

	pemuatan = new Promise<Grecaptcha>((selesai, gagal) => {
		if (window.grecaptcha) {
			selesai(window.grecaptcha);
			return;
		}

		const adaSkrip = document.getElementById(ID_SKRIP) as HTMLScriptElement | null;

		const tunggu = () => {
			if (!window.grecaptcha) {
				gagal(new Error('reCAPTCHA tidak tersedia setelah skrip dimuat.'));
				return;
			}

			// `ready` menunggu inisialisasi internal Google selesai; memanggil
			// `execute` sebelum itu melempar.
			window.grecaptcha.ready(() => selesai(window.grecaptcha as Grecaptcha));
		};

		if (adaSkrip) {
			adaSkrip.addEventListener('load', tunggu);
			adaSkrip.addEventListener('error', () => gagal(new Error('Gagal memuat skrip reCAPTCHA.')));
			return;
		}

		const skrip = document.createElement('script');
		skrip.id = ID_SKRIP;
		skrip.src = `https://www.google.com/recaptcha/api.js?render=${encodeURIComponent(SITE_KEY)}`;
		skrip.async = true;
		skrip.defer = true;
		skrip.onload = tunggu;
		skrip.onerror = () => {
			// Dilepas supaya percobaan berikutnya benar-benar memuat ulang,
			// bukan menyangkut pada elemen mati yang tidak akan pernah `load`.
			pemuatan = null;
			skrip.remove();
			gagal(new Error('Gagal memuat skrip reCAPTCHA.'));
		};

		document.head.appendChild(skrip);
	});

	return pemuatan;
}

/**
 * Mulai mengunduh skrip lebih awal.
 *
 * Dipanggil saat halaman auth dibuka supaya unduhannya sudah selesai sebelum
 * orangnya menekan tombol kirim — tanpa ini, penantian ~1 detik itu terjadi
 * tepat setelah tombol ditekan dan terasa seperti panel yang menggantung.
 * Kegagalannya diabaikan di sini; yang menentukan tetap panggilan saat kirim.
 */
export function siapkanRecaptcha(): void {
	if (!recaptchaAktif()) {
		return;
	}

	void muat().catch(() => {});
}

/**
 * Token untuk satu kali kirim formulir.
 *
 * `aksi` harus sama dengan yang diperiksa server (`masuk_panel`,
 * `lupa_password`, `password_baru`) — di sanalah token dicegah dipakai lintas
 * formulir.
 *
 * Mengembalikan `undefined` bila reCAPTCHA tidak dikonfigurasi; melempar bila
 * dikonfigurasi tetapi gagal, supaya pemanggilnya bisa menampilkan sebabnya
 * alih-alih mengirim permintaan yang sudah pasti ditolak server.
 */
export async function ambilTokenRecaptcha(aksi: string): Promise<string | undefined> {
	if (!recaptchaAktif()) {
		return undefined;
	}

	const grecaptcha = await muat();

	return grecaptcha.execute(SITE_KEY, { action: aksi });
}

/** Nama aksi, disatukan di sini supaya tidak pernah beda dengan sisi server. */
export const AKSI_RECAPTCHA = {
	masuk: 'masuk_panel',
	lupaPassword: 'lupa_password',
	passwordBaru: 'password_baru'
} as const;
