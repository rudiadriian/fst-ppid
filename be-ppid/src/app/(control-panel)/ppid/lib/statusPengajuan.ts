/**
 * Label dan aturan transisi status pengajuan.
 *
 * Salinan dari `PermohonanInformasi::TRANSISI` dan
 * `KeberatanInformasi::TRANSISI` di api-ppid, dipakai agar pengguna hanya
 * ditawari tujuan yang memang sah. Server tetap menolak transisi terlarang
 * bila daftar ini ketinggalan zaman — keduanya tidak boleh diandalkan
 * sendirian.
 *
 * Dikumpulkan di satu berkas karena label yang sama sebelumnya ditulis ulang
 * di tiap dialog; begitu satu status ditambahkan, salinan yang terlewat
 * menampilkan nilai mentah kepada petugas.
 */

import { PilihanOpsi } from './types';

export type WarnaChip = 'default' | 'primary' | 'secondary' | 'success' | 'warning' | 'error' | 'info';

export type JenisPengajuan = 'permohonan' | 'keberatan';

export type StatusPengajuan = {
	value: string;
	label: string;
	warna: WarnaChip;
	/** Kategori pengajuan yang benar-benar memakai status ini. */
	jenis: JenisPengajuan[];
	/**
	 * Kosakata lama: tidak dipasang lagi oleh alur mana pun, tetapi baris yang
	 * telanjur tersimpan masih memakainya. Tetap bisa disaring supaya berkas
	 * lama tidak menjadi tidak terjangkau, dengan label yang mengatakan
	 * keadaannya.
	 */
	lama?: boolean;
};

/**
 * Satu katalog status pengajuan, berurut mengikuti alur prosesnya.
 *
 * Urutannya bukan abjad melainkan jalannya berkas: masuk → diperiksa →
 * diproses → putusan → tutup. Dari katalog inilah label chip, warna, dan
 * pilihan filter Status disusun — sebelumnya ketiganya ditulis ulang di dua
 * berkas, dan salinan yang terlewat membuat filter menawarkan status yang tidak
 * ada di alur sekaligus melewatkan status yang ada.
 *
 * Sumber kebenarannya tetap `PermohonanInformasi::TRANSISI` dan
 * `KeberatanInformasi::TRANSISI` di api-ppid; `jenis` di bawah menyalin
 * status mana yang dikenal masing-masing tabel (ikut CHECK constraint-nya).
 */
export const ALUR_STATUS_PENGAJUAN: StatusPengajuan[] = [
	{ value: 'diajukan', label: 'Diajukan', warna: 'info', jenis: ['permohonan', 'keberatan'] },
	// Hanya permohonan: keberatan tidak punya langkah verifikasi berkas.
	{ value: 'diverifikasi', label: 'Diverifikasi', warna: 'info', jenis: ['permohonan'] },
	{ value: 'diproses', label: 'Diproses', warna: 'warning', jenis: ['permohonan', 'keberatan'] },
	{ value: 'revisi', label: 'Revisi', warna: 'warning', jenis: ['permohonan', 'keberatan'] },
	{ value: 'disetujui', label: 'Disetujui', warna: 'success', jenis: ['permohonan'] },
	{ value: 'ditolak_sebagian', label: 'Ditolak Sebagian', warna: 'error', jenis: ['permohonan'] },
	{ value: 'ditolak', label: 'Ditolak', warna: 'error', jenis: ['permohonan', 'keberatan'] },
	{ value: 'selesai', label: 'Selesai', warna: 'success', jenis: ['permohonan', 'keberatan'] },
	{ value: 'kedaluwarsa', label: 'Kedaluwarsa', warna: 'default', jenis: ['permohonan'] },
	{
		value: 'menunggu_approval',
		label: 'Menunggu Persetujuan',
		warna: 'warning',
		jenis: ['permohonan', 'keberatan'],
		lama: true
	}
];

function petaStatus(jenis: JenisPengajuan): Record<string, { label: string; warna: WarnaChip }> {
	return ALUR_STATUS_PENGAJUAN.filter((status) => status.jenis.includes(jenis)).reduce<
		Record<string, { label: string; warna: WarnaChip }>
	>((hasil, status) => {
		hasil[status.value] = { label: status.label, warna: status.warna };
		return hasil;
	}, {});
}

/**
 * Peta status untuk chip pada tabel (`badgeMap` memakai `color`, bukan `warna`).
 *
 * Tanpa argumen: seluruh status kedua kategori — dipakai daftar gabungan
 * Permohonan, yang memuat permohonan dan keberatan sekaligus.
 */
export function petaBadgeStatus(jenis?: JenisPengajuan): Record<string, { label: string; color: WarnaChip }> {
	return ALUR_STATUS_PENGAJUAN.filter((status) => !jenis || status.jenis.includes(jenis)).reduce<
		Record<string, { label: string; color: WarnaChip }>
	>((hasil, status) => {
		hasil[status.value] = { label: status.label, color: status.warna };
		return hasil;
	}, {});
}

/**
 * Pilihan filter Status.
 *
 * Daftarnya mengikuti kategori yang sedang dipilih operator: memilih
 * **Keberatan** tidak lagi menawarkan "Diverifikasi" atau "Kedaluwarsa", yang
 * memang tidak pernah dipakai tabel keberatan dan hanya menghasilkan daftar
 * kosong. Saat kategorinya belum dipilih, status yang cuma berlaku di satu
 * kategori diberi keterangan supaya jelas ke mana ia menyaring.
 */
export function opsiStatusPengajuan(jenis?: string): PilihanOpsi[] {
	const kategori = jenis === 'permohonan' || jenis === 'keberatan' ? (jenis as JenisPengajuan) : undefined;

	return ALUR_STATUS_PENGAJUAN.filter((status) => !kategori || status.jenis.includes(kategori)).map((status) => {
		const keterangan: string[] = [];

		if (!kategori && status.jenis.length === 1) {
			keterangan.push(status.jenis[0] === 'permohonan' ? 'permohonan saja' : 'keberatan saja');
		}

		if (status.lama) {
			keterangan.push('kosakata lama');
		}

		return {
			value: status.value,
			label: keterangan.length > 0 ? `${status.label} (${keterangan.join(', ')})` : status.label
		};
	});
}

export const STATUS_PERMOHONAN: Record<string, { label: string; warna: WarnaChip }> = petaStatus('permohonan');

export const TRANSISI_PERMOHONAN: Record<string, string[]> = {
	diajukan: ['diverifikasi', 'diproses', 'ditolak', 'kedaluwarsa'],
	diverifikasi: ['diproses', 'ditolak', 'kedaluwarsa'],
	// Diproses = sudah disetujui PPID Pelaksana, tinggal putusan PPID.
	// Persetujuan PPID menutup perkaranya langsung ke `selesai`.
	diproses: ['selesai', 'disetujui', 'ditolak', 'ditolak_sebagian', 'revisi', 'kedaluwarsa'],
	// Dikembalikan ke PPID Pelaksana; dari sini berkasnya diajukan lagi.
	revisi: ['diproses', 'ditolak', 'kedaluwarsa'],
	// Kosakata lama, artinya sama dengan `diproses`; tidak dipasang lagi.
	menunggu_approval: ['disetujui', 'ditolak', 'ditolak_sebagian', 'revisi', 'diproses'],
	disetujui: ['selesai'],
	ditolak: ['selesai'],
	ditolak_sebagian: ['selesai'],
	selesai: [],
	kedaluwarsa: []
};

export const STATUS_KEBERATAN: Record<string, { label: string; warna: WarnaChip }> = petaStatus('keberatan');

export const TRANSISI_KEBERATAN: Record<string, string[]> = {
	diajukan: ['diproses', 'ditolak'],
	// Diproses = sudah diteruskan PPID Pelaksana, tinggal putusan PPID.
	diproses: ['selesai', 'revisi', 'ditolak'],
	revisi: ['diproses', 'ditolak'],
	// Kosakata lama, artinya sama dengan `diproses`; tidak dipasang lagi.
	menunggu_approval: ['selesai', 'revisi', 'ditolak', 'diproses'],
	selesai: [],
	ditolak: []
};

export function labelStatus(peta: Record<string, { label: string }>, nilai: unknown): string {
	const kunci = String(nilai ?? '');
	return peta[kunci]?.label ?? (kunci || '—');
}

export function warnaStatus(peta: Record<string, { warna: WarnaChip }>, nilai: unknown): WarnaChip {
	return peta[String(nilai ?? '')]?.warna ?? 'default';
}

/** Status langkah persetujuan; nilainya dari `approval_pengajuan.status`. */
export const STATUS_LANGKAH: Record<string, { label: string; warna: WarnaChip; ikon: string }> = {
	menunggu: { label: 'Menunggu', warna: 'warning', ikon: 'lucide:clock' },
	disetujui: { label: 'Disetujui', warna: 'success', ikon: 'lucide:check' },
	ditolak: { label: 'Ditolak', warna: 'error', ikon: 'lucide:x' },
	revisi: { label: 'Perlu Perbaikan', warna: 'warning', ikon: 'lucide:undo-2' },
	dilewati: { label: 'Tidak Dijalankan', warna: 'default', ikon: 'lucide:minus' }
};

/**
 * Tujuh dasar keberatan menurut Pasal 35 UU No. 14 Tahun 2008.
 *
 * Salinan dari `KeberatanInformasi::JENIS` di api-ppid; urutannya mengikuti
 * bunyi pasalnya, bukan abjad, supaya sebaran alasan di Dashboard terbaca
 * sejajar dengan undang-undangnya.
 */
export const JENIS_KEBERATAN: Record<string, string> = {
	permohonan_ditolak: 'Penolakan atas Permintaan Informasi',
	permintaan_tidak_ditanggapi: 'Tidak Ditanggapinya Permintaan Informasi',
	melebihi_jangka_waktu: 'Penyampaian Informasi Melebihi Waktu yang Diatur',
	informasi_tidak_sesuai: 'Permintaan Informasi Tidak Ditanggapi Sebagaimana yang Diminta',
	permintaan_tidak_dipenuhi: 'Tidak Dipenuhinya Permintaan Informasi',
	biaya_tidak_wajar: 'Pengenaan Biaya yang Tidak Wajar',
	informasi_tidak_disediakan: 'Tidak Disediakannya Informasi Berkala'
};

/**
 * Status yang masih boleh dipasang pemegang giliran lewat dropdown.
 *
 * Salinan `PermohonanController::STATUS_LANJUT_PEMEGANG`. Yang membedakannya
 * dari perpindahan lain: berkasnya tetap di meja yang sama. Perpindahan yang
 * memindahkan berkas ke meja lain atau menutup perkaranya adalah hasil putusan
 * di panel Persetujuan Berjenjang, bukan pilihan dropdown.
 */
export const LANJUT_PEMEGANG = ['diverifikasi'];

/** Tujuan status yang boleh ditawarkan pada keadaan ini. */
export function tujuanStatus(status: string, alurBerjalan: boolean, giliranSaya: boolean): string[] {
	const daftar = TRANSISI_PERMOHONAN[status] ?? [];

	return alurBerjalan && giliranSaya ? daftar.filter((nilai) => LANJUT_PEMEGANG.includes(nilai)) : daftar;
}

/**
 * Panel Ubah Status punya sesuatu untuk ditawarkan?
 *
 * Dipakai dialog rinciannya untuk memutuskan apakah bagian **Ubah Status**
 * perlu dipasang sama sekali (langkah 100). Bagian yang isinya cuma keterangan
 * "lanjutkan dari panel Persetujuan Berjenjang" menambah satu blok yang harus
 * dilewati mata tanpa menambah satu pun tindakan — keterangan itu sudah ada di
 * kepala rincian, tepat di tempat petugas membacanya.
 */
export function adaPilihanStatus(
	status: string,
	bolehUbah: boolean,
	alurBerjalan: boolean,
	giliranSaya: boolean
): boolean {
	if (!bolehUbah || (alurBerjalan && !giliranSaya)) {
		return false;
	}

	return tujuanStatus(status, alurBerjalan, giliranSaya).length > 0;
}
