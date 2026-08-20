import { useMutation, useQueryClient } from '@tanstack/react-query';
import { notificationsApiService } from '../services/notificationsApiService';
import { notificationsQueryKey } from './useGetAllNotifications';

/**
 * Tandai satu notifikasi sudah dibaca.
 *
 * Dipanggil saat kartunya dibuka; setelah itu barisnya hilang dari lonceng
 * (yang hanya memuat yang belum dibaca) tetapi tetap ada di halaman Notifikasi.
 */
export const useReadNotification = () => {
	const queryClient = useQueryClient();

	return useMutation({
		mutationFn: notificationsApiService.markAsRead,
		onSuccess: () => {
			queryClient.invalidateQueries({ queryKey: notificationsQueryKey });
		}
	});
};

export const useReadAllNotifications = () => {
	const queryClient = useQueryClient();

	return useMutation({
		mutationFn: notificationsApiService.markAllAsRead,
		onSuccess: () => {
			queryClient.invalidateQueries({ queryKey: notificationsQueryKey });
		}
	});
};
