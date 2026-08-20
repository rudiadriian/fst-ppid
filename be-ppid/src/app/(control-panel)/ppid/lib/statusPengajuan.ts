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
	diajukan: ['diverifikasi', 'ditolak', 'kedaluwarsa'],
	diverifikasi: ['diproses', 'ditolak', 'kedaluwarsa'],
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
	diajukan: ['diproses', 'ditolak'],
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

export const JENIS_KEBERATAN: Record<string, string> = {
	permohonan_ditolak: 'Permohonan Informasi Ditolak',
	informasi_tidak_disediakan: 'Informasi Tidak Disediakan',
	permintaan_tidak_ditanggapi: 'Permintaan Tidak Ditanggapi',
	informasi_tidak_sesuai: 'Informasi yang Diberikan Tidak Sesuai',
	biaya_tidak_wajar: 'Pengenaan Biaya yang Tidak Wajar',
	melebihi_jangka_waktu: 'Permintaan Melebihi Jangka Waktu Tanggapan'
};
