import { useQuery } from '@tanstack/react-query';
import useAuth from '@fuse/core/FuseAuthProvider/useAuth';
import { notificationsApiService } from '../services/notificationsApiService';

export const notificationsQueryKey = ['notifications', 'list'] as const;

export const useGetAllNotifications = () => {
	const { authState } = useAuth();

	return useQuery({
		queryFn: notificationsApiService.getAll,
		queryKey: notificationsQueryKey,
		// Panel notifikasi terpasang di layout sejak halaman pertama. Tanpa
		// penjagaan ini ia menembak API sebelum login, dijawab 401, dan
		// interceptor auth memperlakukan 401 apa pun sebagai perintah sign out —
		// pengguna terlempar kembali ke halaman login tepat setelah masuk.
		enabled: Boolean(authState?.isAuthenticated),
		retry: false
	});
};
