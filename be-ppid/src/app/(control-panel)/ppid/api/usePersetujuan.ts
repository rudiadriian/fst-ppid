import { useQuery } from '@tanstack/react-query';
import ppidApi from './ppidApi';

export type LangkahPersetujuan = {
	id: number;
	urutan: number;
	nama_tahap: string;
	nama_jabatan: string | null;
	status: string;
	catatan: string | null;
	tanggal_masuk: string | null;
	batas_waktu: string | null;
	tanggal_putusan: string | null;
	role: { id: number; name: string } | null;
	pemutus: { id: number; name: string } | null;
	/** Definisi tahapnya; `boleh_tolak: false` menandai jenjang penerima. */
	tahap: { id: number; boleh_tolak: boolean } | null;
};

export type KeadaanPersetujuan = {
	jenis: string;
	langkah: LangkahPersetujuan[];
	berjalan_id: number | null;
	boleh_memutus: boolean;
};

/**
 * Kunci cache dipisah dari cache resource biasa supaya menyegarkan daftar
 * modul tidak ikut memuat ulang tiap panel persetujuan yang sedang terbuka.
 */
export function kunciPersetujuan(modul: string, id: number) {
	return ['ppid', modul, 'persetujuan', id] as const;
}

/**
 * Keadaan jenjang satu pengajuan.
 *
 * Berdiri di berkasnya sendiri supaya panel putusan dan dialog rinciannya
 * membaca keadaan yang **sama persis** (langkah 100). Jenjang dibuat server
 * saat endpoint ini pertama kali dibaca, jadi `approvalLangkah` pada detail
 * bisa masih kosong ketika dialognya baru terbuka — dan panel status yang
 * menyimpulkan "belum ada jenjang" dari data basi itu akan membuka kunci yang
 * justru seharusnya terpasang. React Query menyatukan pemanggilnya lewat kunci
 * yang sama, jadi tidak ada permintaan tambahan.
 */
export function usePersetujuan(modul: 'permohonan' | 'keberatan', pengajuanId: number, aktif = true) {
	return useQuery<KeadaanPersetujuan>({
		queryKey: kunciPersetujuan(modul, pengajuanId),
		queryFn: () => ppidApi.ambil<KeadaanPersetujuan>(`${modul}/${pengajuanId}/approval`),
		enabled: aktif && Number.isFinite(pengajuanId) && pengajuanId > 0
	});
}
