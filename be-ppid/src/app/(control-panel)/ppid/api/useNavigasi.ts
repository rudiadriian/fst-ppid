import { useQuery } from '@tanstack/react-query';
import api from '@/utils/api';

/**
 * Menu dan hak akses modul milik role yang sedang login.
 *
 * Dipakai untuk menyusun menu samping dan menyembunyikan tombol aksi.
 * Ini murni soal tampilan — penegakan hak akses tetap di sisi API.
 */

export type AksesModul = {
	view: boolean;
	create: boolean;
	edit: boolean;
	delete: boolean;
	approve: boolean;
	export: boolean;
};

export type ModulNavigasi = {
	id: number;
	slug: string;
	nama: string;
	icon: string | null;
	route: string | null;
	urutan: number;
	parent_id: number | null;
	akses: AksesModul;
};

export type NavigasiResponse = {
	data: {
		role: string | null;
		modul: ModulNavigasi[];
	};
};

export const navigasiQueryKey = ['ppid', 'navigasi'];

export function useNavigasi() {
	return useQuery({
		queryKey: navigasiQueryKey,
		queryFn: () => api.get('v1/me/navigation').json<NavigasiResponse>(),
		staleTime: 5 * 60 * 1000
	});
}

const AKSES_KOSONG: AksesModul = {
	view: false,
	create: false,
	edit: false,
	delete: false,
	approve: false,
	export: false
};

/**
 * Hak akses satu modul. Selama data menu belum tiba, semua hak dianggap
 * tertutup supaya tombol aksi tidak sempat berkedip muncul.
 */
export function useAksesModul(modulSlug: string): { akses: AksesModul; isLoading: boolean } {
	const { data, isLoading } = useNavigasi();

	const modul = data?.data.modul.find((m) => m.slug === modulSlug);

	return { akses: modul?.akses ?? AKSES_KOSONG, isLoading };
}
