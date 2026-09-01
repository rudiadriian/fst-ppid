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

export type WarnaChip = 'default' | 'primary' | 'secondary' | 'success' | 'warning' | 'error' | 'info';

export const STATUS_PERMOHONAN: Record<string, { label: string; warna: WarnaChip }> = {
	diajukan: { label: 'Diajukan', warna: 'info' },
	diverifikasi: { label: 'Diverifikasi', warna: 'info' },
	diproses: { label: 'Diproses', warna: 'warning' },
	revisi: { label: 'Revisi', warna: 'warning' },
	menunggu_approval: { label: 'Menunggu Persetujuan', warna: 'warning' },
	disetujui: { label: 'Disetujui', warna: 'success' },
	ditolak: { label: 'Ditolak', warna: 'error' },
	ditolak_sebagian: { label: 'Ditolak Sebagian', warna: 'error' },
	selesai: { label: 'Selesai', warna: 'success' },
	kedaluwarsa: { label: 'Kedaluwarsa', warna: 'default' }
};

export const TRANSISI_PERMOHONAN: Record<string, string[]> = {
	diajukan: ['diverifikasi', 'menunggu_approval', 'ditolak', 'kedaluwarsa'],
	diverifikasi: ['diproses', 'menunggu_approval', 'ditolak', 'kedaluwarsa'],
	diproses: ['menunggu_approval', 'ditolak', 'kedaluwarsa'],
	menunggu_approval: ['disetujui', 'ditolak', 'ditolak_sebagian', 'diproses'],
	disetujui: ['selesai'],
	ditolak: ['selesai'],
	ditolak_sebagian: ['selesai'],
	selesai: [],
	kedaluwarsa: []
};

export const STATUS_KEBERATAN: Record<string, { label: string; warna: WarnaChip }> = {
	diajukan: { label: 'Diajukan', warna: 'info' },
	diproses: { label: 'Diproses', warna: 'warning' },
	revisi: { label: 'Revisi', warna: 'warning' },
	menunggu_approval: { label: 'Menunggu Persetujuan', warna: 'warning' },
	ditolak: { label: 'Ditolak', warna: 'error' },
	selesai: { label: 'Selesai', warna: 'success' }
};

export const TRANSISI_KEBERATAN: Record<string, string[]> = {
	diajukan: ['diproses', 'menunggu_approval', 'ditolak'],
	diproses: ['menunggu_approval', 'revisi', 'ditolak'],
	revisi: ['diproses', 'ditolak'],
	menunggu_approval: ['selesai', 'ditolak', 'diproses'],
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
