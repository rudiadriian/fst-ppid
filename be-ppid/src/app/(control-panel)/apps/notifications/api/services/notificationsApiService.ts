import { api } from '@/utils/api';
import type { Notification } from '../types';

/**
 * Notifikasi panel admin.
 *
 * Sumbernya tabel `notifikasi` di `ppiddb` lewat API PPID. Endpoint mock bawaan
 * Fuse (`/api/mock/notifications`) sudah tidak ada sejak data tiruan dilepas.
 */
export const notificationsApiService = {
	getAll: async (): Promise<Notification[]> => {
		return api.get('v1/notifikasi').json();
	},

	create: async (_notification: Notification): Promise<Notification> => {
		// Notifikasi diterbitkan proses di sisi server (mis. permohonan masuk),
		// bukan diketik dari panel. Tidak ada jalur tulis dari klien.
		return Promise.reject(new Error('Notifikasi tidak dapat dibuat dari panel admin.'));
	},

	deleteMany: async (notificationIds: string[]): Promise<void> => {
		await api.delete('v1/notifikasi', {
			json: notificationIds
		});
	},

	getById: async (notificationId: string): Promise<Notification> => {
		return api.get(`v1/notifikasi/${notificationId}`).json();
	},

	delete: async (notificationId: string): Promise<void> => {
		await api.delete(`v1/notifikasi/${notificationId}`);
	}
};
