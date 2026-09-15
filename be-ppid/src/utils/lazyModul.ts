import { lazy, ComponentType } from 'react';

/**
 * `React.lazy` yang tahan terhadap rilis baru.
 *
 * Panel ini satu aplikasi satu halaman: `index.html` dan chunk pertamanya
 * dimuat sekali, lalu halaman modul baru diambil saat menunya diklik. Nama
 * chunk itu ber-hash dan ditanam di bundel yang sedang berjalan — jadi peramban
 * yang sudah membuka panel sebelum deploy akan meminta nama berkas milik rilis
 * lama. Bila berkas itu tidak ada lagi di server, jawabannya 404 dan React
 * menjatuhkan seluruh halaman:
 *
 *     TypeError: Failed to fetch dynamically imported module: .../assets/PpidResourcePage-<hash>.js
 *
 * Itulah yang dilaporkan pada UAT poin 17 untuk modul Alur Prosedur — bukan
 * galat modulnya: modul mana pun yang kebetulan dibuka pertama kali sesudah
 * deploy akan memberi galat yang sama.
 *
 * Dua pagar dipasang untuk itu. Di pipeline, chunk rilis lama tidak lagi
 * dihapus saat deploy. Di sini, kegagalan memuat chunk dijawab dengan memuat
 * ulang halaman satu kali: muat ulang mengambil `index.html` terbaru berikut
 * nama chunk yang benar.
 *
 * Muat ulangnya dijaga penanda di `sessionStorage` supaya tidak berputar tanpa
 * henti bila berkasnya memang tidak bisa diambil — misalnya jaringan petugas
 * yang putus. Percobaan kedua dibiarkan gagal apa adanya sehingga
 * `ErrorBoundary` menampilkan sebabnya, bukan menyembunyikannya di balik
 * halaman yang memuat ulang terus-menerus.
 */
export function lazyModul<T extends ComponentType<never>>(muat: () => Promise<{ default: T }>, tanda: string) {
	const kunci = `ppid:muat-ulang-chunk:${tanda}`;

	return lazy(() =>
		muat()
			.then((modul) => {
				// Berhasil dimuat: penandanya dibuang supaya rilis berikutnya
				// tetap punya satu kesempatan memulihkan diri.
				try {
					window.sessionStorage.removeItem(kunci);
				} catch {
					// Penyimpanan tidak tersedia; tidak ada yang perlu dibersihkan.
				}

				return modul;
			})
			.catch((galat: unknown) => {
				let sudahPernah = true;

				// `sessionStorage` bisa melempar (mode privat, penyimpanan
				// diblokir). Kalau tidak terbaca, jangan memuat ulang — lebih
				// baik galatnya terlihat daripada halaman berputar.
				try {
					sudahPernah = window.sessionStorage.getItem(kunci) !== null;

					if (!sudahPernah) {
						window.sessionStorage.setItem(kunci, String(Date.now()));
					}
				} catch {
					sudahPernah = true;
				}

				if (!sudahPernah) {
					window.location.reload();

					// Janji yang tidak pernah selesai: halaman sedang berganti,
					// dan menolaknya di sini hanya memunculkan galat sekejap
					// sebelum muat ulangnya jalan.
					return new Promise<{ default: T }>(() => {});
				}

				throw galat;
			})
	);
}

export default lazyModul;
