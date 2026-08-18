/**
 * Jejak dokumen (traceability) yang berlaku untuk semua modul.
 *
 * Setiap tabel modul punya enam kolom seragam di API — `created_by`/`created_at`,
 * `updated_by`/`updated_at`, `deleted_by`/`deleted_at` — beserta relasi
 * `pembuat`, `pengubah`, dan `penghapus`. Definisi kolomnya ditulis sekali di
 * sini lalu ditempelkan otomatis ke tiap daftar, jadi modul baru ikut punya
 * jejaknya tanpa perlu menambahkan apa pun di `resources.ts`.
 */

import { ColumnConfig, FilterConfig, ResourceConfig } from './types';

/** Kolom "siapa & kapan" untuk pembuatan dan perubahan terakhir. */
export const KOLOM_JEJAK_UBAH: ColumnConfig[] = [
	{ key: 'pembuat', label: 'Dibuat oleh', type: 'relation', relationKey: 'name', size: 160, noSort: true },
	{ key: 'created_at', label: 'Dibuat', type: 'datetime', size: 165 },
	{ key: 'pengubah', label: 'Diubah oleh', type: 'relation', relationKey: 'name', size: 160, noSort: true },
	{ key: 'updated_at', label: 'Diubah', type: 'datetime', size: 165 }
];

/**
 * Kolom penghapusan. Hanya ditampilkan saat daftar sedang memuat data terhapus
 * — pada data aktif isinya selalu kosong dan cuma memperlebar tabel.
 */
export const KOLOM_JEJAK_HAPUS: ColumnConfig[] = [
	{ key: 'penghapus', label: 'Dihapus oleh', type: 'relation', relationKey: 'name', size: 160, noSort: true },
	{ key: 'deleted_at', label: 'Dihapus', type: 'datetime', size: 165 }
];

/**
 * Kolom jejak yang disembunyikan lebih dulu.
 *
 * Pasangan "Diubah" jarang dipakai sehari-hari dan membuat tabel melebar, jadi
 * bawaannya tersembunyi. Operator bisa memunculkannya kapan saja lewat tombol
 * **Show/Hide columns** di toolbar tabel — kolomnya tetap ada, hanya tidak
 * ditampilkan sejak awal.
 */
const KUNCI_TERSEMBUNYI = ['pengubah', 'updated_at'];

/**
 * Filter "Status data": pintu masuk ke arsip penghapusan.
 *
 * Tanpa pilihan apa pun daftar berisi data aktif saja — sama seperti sebelum
 * ada jejak — sehingga tidak ada modul yang tiba-tiba menampilkan baris yang
 * sudah dihapus petugas.
 */
export const FILTER_TERHAPUS: FilterConfig = {
	name: 'terhapus',
	label: 'Status data',
	type: 'select',
	labelKosong: 'Aktif',
	options: [
		{ value: 'hanya', label: 'Terhapus' },
		{ value: 'semua', label: 'Aktif + terhapus' }
	]
};

export function punyaJejak(config: ResourceConfig): boolean {
	return !config.tanpaJejak;
}

/**
 * Kolom daftar lengkap dengan jejaknya.
 *
 * Kolom yang sudah ditulis sendiri oleh modul tidak digandakan — mis. Laporan
 * Pelayanan sudah punya `created_at` dengan label "Tanggal publikasi".
 */
export function kolomDenganJejak(config: ResourceConfig, sertakanHapus: boolean): ColumnConfig[] {
	if (!punyaJejak(config)) {
		return config.columns;
	}

	const sudahAda = new Set(config.columns.map((kolom) => kolom.key));
	const tambahan = [...KOLOM_JEJAK_UBAH, ...(sertakanHapus ? KOLOM_JEJAK_HAPUS : [])];

	return [...config.columns, ...tambahan.filter((kolom) => !sudahAda.has(kolom.key))];
}

/**
 * Visibilitas awal kolom untuk `initialState` tabel.
 *
 * Hanya kolom yang ditempelkan jejak yang disembunyikan. Kolom bernama sama
 * yang ditulis sendiri oleh modul tetap tampil, karena modul itu memang sengaja
 * memilihnya sebagai kolom utama.
 */
export function visibilitasAwalJejak(config: ResourceConfig): Record<string, boolean> {
	if (!punyaJejak(config)) {
		return {};
	}

	const punyaSendiri = new Set(config.columns.map((kolom) => kolom.key));

	return KUNCI_TERSEMBUNYI.reduce<Record<string, boolean>>((hasil, kunci) => {
		if (!punyaSendiri.has(kunci)) {
			hasil[kunci] = false;
		}

		return hasil;
	}, {});
}
