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
	/** Langkah putaran yang sedang berjalan saja. */
	langkah: LangkahPersetujuan[];
	/** Putaran-putaran sebelumnya, terlama lebih dulu; kosong bila baru sekali. */
	riwayat_putaran: LangkahPersetujuan[][];
	/** Berkas ini sedang di putaran keberapa. */
	putaran: number;
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

/** Tahap yang sedang menunggu putusan; `null` bila jenjangnya sudah tuntas. */
export function langkahBerjalan(data?: KeadaanPersetujuan): LangkahPersetujuan | null {
	if (!data?.berjalan_id) {
		return null;
	}

	return data.langkah.find((satu) => satu.id === data.berjalan_id) ?? null;
}

/**
 * Siapa yang sedang memegang berkasnya, dalam satu baris.
 *
 * Dipakai di tempat-tempat yang harus menjawab "kenapa saya tidak bisa apa-apa
 * di sini" (langkah 100). Jawaban "bukan giliran Anda" saja membuat penyetuju
 * di jenjang atas melihat rincian tanpa satu pun tombol dan tanpa tahu berkas
 * itu tertahan di siapa — persis keluhan yang membuka putaran ini.
 */
export function pemegangGiliran(data?: KeadaanPersetujuan): string | null {
	const langkah = langkahBerjalan(data);

	if (!langkah) {
		return null;
	}

	const pemegang = langkah.nama_jabatan ?? langkah.role?.name ?? null;

	return pemegang ? `${langkah.nama_tahap} (${pemegang})` : langkah.nama_tahap;
}
