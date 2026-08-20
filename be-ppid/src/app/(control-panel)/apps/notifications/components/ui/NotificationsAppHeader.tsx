import Typography from '@mui/material/Typography';
import Button from '@mui/material/Button';
import FuseSvgIcon from '@fuse/core/FuseSvgIcon';
import PageBreadcrumb from 'src/components/PageBreadcrumb';
import { useDeleteNotifications } from '../../api/hooks/useDeleteNotifications';
import { useGetAllNotifications } from '../../api/hooks/useGetAllNotifications';
import { useReadAllNotifications } from '../../api/hooks/useReadNotification';

/**
 * Kepala halaman Notifikasi.
 *
 * Dua tindakan massal, dan keduanya berbeda akibat: menandai dibaca hanya
 * mengosongkan lonceng, menghapus membuang riwayatnya untuk selamanya.
 */
function NotificationsAppHeader() {
	// Halaman ini arsip, jadi hitungannya termasuk yang sudah dibaca.
	const { data: notifications } = useGetAllNotifications(true);

	const { mutate: deleteNotifications } = useDeleteNotifications();
	const { mutate: readAllNotifications } = useReadAllNotifications();

	const belumDibaca = (notifications ?? []).filter((item) => !item.read).length;

	function handleReadAll() {
		readAllNotifications();
	}

	function handleDeleteAll() {
		if (!window.confirm('Hapus semua notifikasi? Riwayatnya tidak dapat dikembalikan.')) {
			return;
		}

		deleteNotifications((notifications ?? []).map((notification) => notification.id));
	}

	return (
		<div className="container flex w-full">
			<div className="flex min-w-0 flex-auto flex-col p-4 pb-0 sm:flex-row sm:items-center md:p-6">
				<div className="flex flex-auto flex-col">
					<PageBreadcrumb className="mb-2" />

					<Typography className="mb-1 text-3xl leading-none font-extrabold tracking-tight">
						Notifikasi
					</Typography>
					<Typography
						className="font-medium tracking-tight"
						color="text.secondary"
					>
						Seluruh notifikasi, termasuk yang sudah dibaca.
					</Typography>
				</div>
				<div className="mt-3 flex items-center gap-2 sm:mx-2 sm:mt-0">
					{belumDibaca > 0 && (
						<Button
							className="whitespace-nowrap"
							variant="contained"
							color="secondary"
							onClick={handleReadAll}
							startIcon={<FuseSvgIcon>lucide:bell</FuseSvgIcon>}
						>
							Tandai semua dibaca ({belumDibaca})
						</Button>
					)}

					<Button
						className="whitespace-nowrap"
						variant="outlined"
						color="error"
						onClick={handleDeleteAll}
						startIcon={<FuseSvgIcon>lucide:trash</FuseSvgIcon>}
					>
						Hapus semua
					</Button>
				</div>
			</div>
		</div>
	);
}

export default NotificationsAppHeader;
