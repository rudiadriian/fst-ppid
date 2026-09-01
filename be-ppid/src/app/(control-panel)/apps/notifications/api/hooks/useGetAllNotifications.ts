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
		refetchIntervalInBackground: false,
		/*
		 * Tiga penjagaan berikut sengaja menyimpang dari setelan bawaan
		 * QueryClient (`staleTime` 5 menit, tanpa refetch saat fokus/tersambung
		 * lagi). Setelan itu benar untuk daftar modul, tetapi salah untuk
		 * lonceng: kejadian yang diberitahukan justru datang dari luar panel.
		 *
		 * Tanpa ini, petugas yang mengurus formulir di tab lain lalu kembali ke
		 * panel melihat lonceng kosong — pengambilan berkalanya berhenti selama
		 * tabnya tidak aktif dan baru berdetak lagi satu menit kemudian,
		 * sementara data lamanya masih dianggap segar.
		 */
		staleTime: 0,
		refetchOnMount: 'always',
		refetchOnWindowFocus: true,
		refetchOnReconnect: true
	});
};
