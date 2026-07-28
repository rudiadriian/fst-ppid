import { useMutation, useQuery, useQueryClient, keepPreviousData } from '@tanstack/react-query';
import ppidApi, { ApiRecord, ListParams, PaginatedResponse } from './ppidApi';

/**
 * Hook data generik per resource CMS.
 *
 * Kunci cache selalu diawali nama resource sehingga invalidasi setelah
 * simpan/hapus cukup menyasar satu modul, tidak membuang cache modul lain.
 */

export const resourceKeys = {
	all: (resource: string) => ['ppid', resource] as const,
	list: (resource: string, params: ListParams) => ['ppid', resource, 'list', params] as const,
	detail: (resource: string, id: number | string) => ['ppid', resource, 'detail', String(id)] as const
};

export function useResourceList<T = ApiRecord>(resource: string, params: ListParams = {}, enabled = true) {
	return useQuery<PaginatedResponse<T>>({
		queryKey: resourceKeys.list(resource, params),
		queryFn: () => ppidApi.list<T>(resource, params),
		enabled,
		// Data halaman sebelumnya ditahan saat pindah halaman supaya tabel
		// tidak berkedip kosong.
		placeholderData: keepPreviousData
	});
}

export function useResourceItem<T = ApiRecord>(resource: string, id?: number | string) {
	return useQuery<T>({
		queryKey: resourceKeys.detail(resource, id ?? ''),
		queryFn: () => ppidApi.get<T>(resource, id as number | string),
		enabled: Boolean(id)
	});
}

export function useCreateResource<T = ApiRecord>(resource: string) {
	const queryClient = useQueryClient();

	return useMutation({
		mutationFn: (payload: unknown) => ppidApi.create<T>(resource, payload),
		onSuccess: () => queryClient.invalidateQueries({ queryKey: resourceKeys.all(resource) })
	});
}

export function useUpdateResource<T = ApiRecord>(resource: string) {
	const queryClient = useQueryClient();

	return useMutation({
		mutationFn: ({ id, payload }: { id: number | string; payload: unknown }) =>
			ppidApi.update<T>(resource, id, payload),
		onSuccess: () => queryClient.invalidateQueries({ queryKey: resourceKeys.all(resource) })
	});
}

export function useDeleteResource(resource: string) {
	const queryClient = useQueryClient();

	return useMutation({
		mutationFn: (id: number | string) => ppidApi.remove(resource, id),
		onSuccess: () => queryClient.invalidateQueries({ queryKey: resourceKeys.all(resource) })
	});
}

export function useDeleteManyResource(resource: string) {
	const queryClient = useQueryClient();

	return useMutation({
		mutationFn: (ids: number[]) => ppidApi.removeMany(resource, ids),
		onSuccess: () => queryClient.invalidateQueries({ queryKey: resourceKeys.all(resource) })
	});
}

/**
 * Daftar ringkas untuk isi dropdown relasi (mis. pilihan kategori).
 */
export function useRelationOptions(resource: string, labelKey: string, enabled = true) {
	return useQuery({
		queryKey: ['ppid', resource, 'options', labelKey],
		queryFn: async () => {
			const hasil = await ppidApi.list<ApiRecord>(resource, { per_page: 100, sort: labelKey });

			return hasil.data.map((baris) => ({
				value: baris.id,
				label: String(baris[labelKey] ?? baris.id)
			}));
		},
		enabled,
		staleTime: 5 * 60 * 1000
	});
}
