import { useMutation, useQuery, useQueryClient, keepPreviousData } from '@tanstack/react-query';
import ppidApi, { PaginatedResponse } from './ppidApi';

/**
 * Data halaman **Akun Saya**: profil, role & hak akses, riwayat aktivitas, dan
 * ubah password.
 *
 * Semuanya menembak grup `auth/akun` di API — endpoint yang hanya melayani akun
 * pemanggil sendiri, jadi tidak ada satu pun yang digantung hak modul Pengguna
 * atau Audit Log.
 */

export type AkunAkses = {
	modul_id: number;
	slug: string;
	nama: string;
	view: boolean;
	create: boolean;
	edit: boolean;
	delete: boolean;
	approve: boolean;
	export: boolean;
};

export type AkunProfil = {
	id: number;
	name: string;
	email: string;
	phone: string | null;
	photo_url: string | null;
	is_active: boolean;
	last_login_at: string | null;
	created_at: string | null;
	role: { id: number; name: string; slug: string; description: string | null } | null;
	struktur: { id: number; nama: string | null; jabatan: string | null } | null;
	super_admin: boolean;
	akses: AkunAkses[];
};

export type AkunAktivitas = {
	id: number;
	action: string;
	model: string | null;
	model_id: number | null;
	ip_address: string | null;
	created_at: string | null;
};

export type UbahProfilPayload = {
	name?: string;
	phone?: string | null;
	photo_url?: string | null;
};

export type UbahPasswordPayload = {
	password_lama: string;
	password: string;
	password_confirmation: string;
};

export const akunKeys = {
	all: ['ppid', 'akun'] as const,
	profil: ['ppid', 'akun', 'profil'] as const,
	aktivitas: (page: number, perPage: number) => ['ppid', 'akun', 'aktivitas', page, perPage] as const
};

export function useAkunProfil() {
	return useQuery<AkunProfil>({
		queryKey: akunKeys.profil,
		queryFn: () => ppidApi.ambil<AkunProfil>('auth/akun')
	});
}

export function useSimpanProfil() {
	const queryClient = useQueryClient();

	return useMutation({
		mutationFn: (payload: UbahProfilPayload) => ppidApi.simpan<{ data: AkunProfil }>('auth/akun', payload),
		onSuccess: () => queryClient.invalidateQueries({ queryKey: akunKeys.profil })
	});
}

export function useAkunAktivitas(page: number, perPage: number) {
	return useQuery<PaginatedResponse<AkunAktivitas>>({
		queryKey: akunKeys.aktivitas(page, perPage),
		queryFn: () => ppidApi.list<AkunAktivitas>('auth/akun/aktivitas', { page, per_page: perPage }),
		// Halaman sebelumnya ditahan supaya tabel tidak berkedip kosong saat
		// pindah halaman.
		placeholderData: keepPreviousData
	});
}

export function useUbahPassword() {
	const queryClient = useQueryClient();

	return useMutation({
		mutationFn: (payload: UbahPasswordPayload) =>
			ppidApi.action<{ message: string }>('auth/ubah-password', payload),
		// Penggantian password ikut tercatat di riwayat aktivitas.
		onSuccess: () => queryClient.invalidateQueries({ queryKey: akunKeys.all })
	});
}
