/**
 * Google reCAPTCHA v2 (kotak centang) untuk formulir masuk dan pemulihan
 * password panel.
 *
 * Berkas ini hanya mengurus pemuatan skrip Google. Yang menggambar kotaknya
 * dan memegang tokennya adalah `components/KolomRecaptcha.tsx`.
 *
 * Bedanya dari v3 yang dipakai sebelumnya: token tidak lagi bisa diminta
 * kapan saja lewat `execute()`. Token baru ada setelah orangnya mencentang —
 * dan hilang lagi setelah dua menit. Karena itu formulir harus menunggu
 * centangan, bukan mengambil token saat tombol kirim ditekan.
 *
 * Skripnya dimuat sesuai kebutuhan, bukan dari `index.html`: sebagian besar
 * panel adalah halaman di balik login yang tidak perlu ikut menanggung
 * unduhan skrip pihak ketiga.
 */

const SITE_KEY = (import.meta.env.VITE_RECAPTCHA_SITE_KEY as string | undefined) ?? '';

const ID_SKRIP = 'recaptcha-v2';

export type OpsiRender = {
	sitekey: string;
	theme?: 'light' | 'dark';
	callback: (token: string) => void;
	'expired-callback': () => void;
	'error-callback': () => void;
};

type Grecaptcha = {
	ready: (siap: () => void) => void;
	render: (wadah: HTMLElement, opsi: OpsiRender) => number;
	reset: (idWidget?: number) => void;
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

export function siteKeyRecaptcha(): string {
	return SITE_KEY;
}

/** Janji pemuatan skrip; dipakai bersama supaya tidak dimuat dua kali. */
let pemuatan: Promise<Grecaptcha> | null = null;

/**
 * Muat skrip Google, selesai setelah `grecaptcha` siap dipakai.
 *
 * `render=explicit` dipakai supaya Google tidak menggambar sendiri kotak pada
 * elemen ber-class `g-recaptcha` yang kebetulan ada di halaman; komponen React
 * yang memutuskan kapan dan di mana kotaknya muncul, jadi ia perlu memanggil
 * `grecaptcha.render` sendiri. `hl=id` menyamakan bahasa kotaknya dengan panel.
 */
export function muatRecaptcha(): Promise<Grecaptcha> {
	if (pemuatan) {
		return pemuatan;
	}

	pemuatan = new Promise<Grecaptcha>((selesai, gagal) => {
		if (window.grecaptcha?.render) {
			selesai(window.grecaptcha);
			return;
		}

		const tunggu = () => {
			if (!window.grecaptcha) {
				gagal(new Error('reCAPTCHA tidak tersedia setelah skrip dimuat.'));
				return;
			}

			// `ready` menunggu inisialisasi internal Google selesai; memanggil
			// `render` sebelum itu melempar.
			window.grecaptcha.ready(() => selesai(window.grecaptcha as Grecaptcha));
		};

		const adaSkrip = document.getElementById(ID_SKRIP) as HTMLScriptElement | null;

		if (adaSkrip) {
			adaSkrip.addEventListener('load', tunggu);
			adaSkrip.addEventListener('error', () => gagal(new Error('Gagal memuat skrip reCAPTCHA.')));
			return;
		}

		const skrip = document.createElement('script');
		skrip.id = ID_SKRIP;
		skrip.src = 'https://www.google.com/recaptcha/api.js?render=explicit&hl=id';
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
