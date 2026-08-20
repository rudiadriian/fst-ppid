/**
 * Pencetak tanggal & jam panel PPID.
 *
 * Zonanya **dipatok** ke Asia/Jakarta, bukan mengikuti setelan peramban.
 * Layanan ini hanya melayani satu zona waktu, dan seluruh tenggat pada UU
 * No. 14 Tahun 2008 dihitung dalam hari kerja setempat — petugas yang membuka
 * panel dari mesin berzona lain tidak boleh melihat jam yang berbeda dari
 * rekannya di kantor.
 *
 * Nilai yang masuk berasal dari API dan selalu membawa offset (`+07:00`), jadi
 * `Date` menerjemahkannya ke instan yang benar sebelum dicetak ulang di sini.
 */
export const ZONA_WAKTU = 'Asia/Jakarta';

const LOKAL = 'id-ID';

function keDate(nilai: unknown): Date | null {
	if (!nilai) {
		return null;
	}

	const tanggal = new Date(String(nilai));

	return Number.isNaN(tanggal.getTime()) ? null : tanggal;
}

/** Tanggal saja: "19 Agu 2026". */
export function formatTanggal(nilai: unknown, kosong = '—'): string {
	const tanggal = keDate(nilai);

	if (!tanggal) {
		return nilai ? String(nilai) : kosong;
	}

	return tanggal.toLocaleString(LOKAL, {
		timeZone: ZONA_WAKTU,
		day: '2-digit',
		month: 'short',
		year: 'numeric'
	});
}

/** Tanggal beserta jamnya: "19 Agu 2026, 16.42". */
export function formatWaktu(nilai: unknown, kosong = '—'): string {
	const tanggal = keDate(nilai);

	if (!tanggal) {
		return nilai ? String(nilai) : kosong;
	}

	return tanggal.toLocaleString(LOKAL, {
		timeZone: ZONA_WAKTU,
		day: '2-digit',
		month: 'short',
		year: 'numeric',
		hour: '2-digit',
		minute: '2-digit'
	});
}
