import FuseScrollbars from '@fuse/core/FuseScrollbars';
import { styled } from '@mui/material/styles';
import IconButton from '@mui/material/IconButton';
import SwipeableDrawer from '@mui/material/SwipeableDrawer';
import Typography from '@mui/material/Typography';
import { useEffect } from 'react';
import FuseSvgIcon from '@fuse/core/FuseSvgIcon';
import _ from 'lodash';
import usePathname from '@fuse/hooks/usePathname';
import { Link } from 'react-router';
import NotificationCard from '../ui/NotificationCard';
import { useGetAllNotifications } from '../../api/hooks/useGetAllNotifications';
import { useDeleteNotification } from '../../api/hooks/useDeleteNotification';
import { useReadAllNotifications, useReadNotification } from '../../api/hooks/useReadNotification';
import { useNotificationPanelContext } from '../../contexts/NotificationPanelContext/useNotificationPanelContext';

const StyledSwipeableDrawer = styled(SwipeableDrawer)(({ theme }) => ({
	'& .MuiDrawer-paper': {
		backgroundColor: theme.vars.palette.background.default,
		width: 320
	}
}));

/**
 * The notification panel.
 */
function NotificationPanel() {
	const pathname = usePathname();
	// Lonceng hanya memuat yang belum dibaca; riwayatnya di halaman Notifikasi.
	const { data: notifications, refetch } = useGetAllNotifications();
	const { mutate: deleteNotification } = useDeleteNotification();
	const { mutate: readNotification } = useReadNotification();
	const { mutate: readAllNotifications } = useReadAllNotifications();
	const { isOpen, close, toggle } = useNotificationPanelContext();

	useEffect(() => {
		if (isOpen) {
			close();
		}
		// eslint-disable-next-line react-hooks/exhaustive-deps
	}, [pathname]);

	/*
	 * Membuka lonceng selalu menarik ulang daftarnya.
	 *
	 * Pengambilan berkalanya berhenti selama tab panel tidak aktif, dan
	 * kejadian yang diberitahukan justru datang dari situs publik saat petugas
	 * sedang mengerjakan hal lain. Tanpa penarikan ini, lonceng yang baru
	 * dibuka bisa menampilkan keadaan satu menit yang lalu — dan yang paling
	 * sering terjadi, menampilkan kosong padahal barisnya sudah ada.
	 */
	useEffect(() => {
		if (isOpen) {
			refetch();
		}
		// eslint-disable-next-line react-hooks/exhaustive-deps
	}, [isOpen]);

	function handleClose() {
		close();
	}

	function handleDismiss(id: string) {
		deleteNotification(id);
	}

	/*
	 * Membuka kartu menandainya sudah dibaca, bukan menghapusnya: barisnya
	 * hilang dari lonceng tetapi tetap tersimpan sebagai riwayat di halaman
	 * Notifikasi. Menghapus tetap tersedia lewat tombol silang.
	 */
	function handleOpen(id: string) {
		readNotification(id);
	}

	function handleReadAll() {
		readAllNotifications();
	}

	return (
		<StyledSwipeableDrawer
			open={isOpen}
			anchor="right"
			onOpen={() => {}}
			onClose={() => toggle()}
			disableSwipeToOpen
		>
			<IconButton
				className="absolute top-0 right-0 z-999 m-1"
				onClick={handleClose}
				size="large"
			>
				<FuseSvgIcon color="action">lucide:x</FuseSvgIcon>
			</IconButton>

			<FuseScrollbars className="flex h-full flex-col p-4">
				{notifications && notifications?.length > 0 ? (
					<div className="flex flex-auto flex-col">
						<div className="mb-8 flex items-end justify-between pt-34">
							<Typography className="text-2xl leading-none font-bold">Notifikasi</Typography>
							<Typography
								className="text-md cursor-pointer underline"
								color="secondary"
								onClick={handleReadAll}
							>
								tandai semua dibaca
							</Typography>
						</div>
						{/*
						 * Lonceng hanya memuat yang belum dibaca, jadi jalan ke
						 * arsipnya harus ada di sini — kalau tidak, notifikasi
						 * yang terlanjur dibuka terasa hilang.
						 */}
						<Link
							className="mb-4 text-md underline"
							to="/ppid/notifikasi"
							onClick={handleClose}
						>
							Lihat semua notifikasi
						</Link>
						{_.orderBy(notifications, ['time'], ['desc']).map((item) => (
							<NotificationCard
								key={item.id}
								className="mb-4"
								item={item}
								onClose={handleDismiss}
								onOpen={handleOpen}
							/>
						))}
					</div>
				) : (
					<div className="flex flex-1 flex-col items-center justify-center gap-3 p-4">
						<Typography
							className="text-center text-xl"
							color="text.secondary"
						>
							Tidak ada notifikasi baru.
						</Typography>
						<Link
							className="text-md underline"
							to="/ppid/notifikasi"
							onClick={handleClose}
						>
							Lihat riwayat notifikasi
						</Link>
					</div>
				)}
			</FuseScrollbars>
		</StyledSwipeableDrawer>
	);
}

export default NotificationPanel;
