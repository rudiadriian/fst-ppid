import { useQuery } from '@tanstack/react-query';
import useAuth from '@fuse/core/FuseAuthProvider/useAuth';
import { notificationsApiService } from '../services/notificationsApiService';

/** Kunci induk; kedua ragam daftar (lonceng & riwayat) berada di bawahnya. */
export const notificationsQueryKey = ['notifications', 'list'] as const;

/**
 * @param semua `false` (baku) hanya yang belum dibaca — isi lonceng.
 *              `true` termasuk yang sudah dibaca — halaman Notifikasi.
 */
export const useGetAllNotifications = (semua = false) => {
	const { authState } = useAuth();

	return useQuery({
		queryFn: () => notificationsApiService.getAll(semua),
		queryKey: [...notificationsQueryKey, semua ? 'semua' : 'belum-dibaca'],
		// Panel notifikasi terpasang di layout sejak halaman pertama. Tanpa
		// penjagaan ini ia menembak API sebelum login, dijawab 401, dan
		// interceptor auth memperlakukan 401 apa pun sebagai perintah sign out —
		// pengguna terlempar kembali ke halaman login tepat setelah masuk.
		enabled: Boolean(authState?.isAuthenticated),
		retry: false,
		// Permohonan & keberatan masuk dari situs publik, bukan dari panel ini,
		// jadi daftarnya disegarkan berkala supaya lonceng tidak perlu menunggu
		// admin memuat ulang halaman.
		refetchInterval: 60_000,
		refetchIntervalInBackground: false
	});
};
