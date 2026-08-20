'use client';
import FuseLoading from '@fuse/core/FuseLoading';
import FusePageSimple from '@fuse/core/FusePageSimple/FusePageSimple';
import Typography from '@mui/material/Typography';
import Masonry from '@mui/lab/Masonry';
import _ from 'lodash';
import { useDeleteNotification } from '../../api/hooks/useDeleteNotification';
import { useGetAllNotifications } from '../../api/hooks/useGetAllNotifications';
import { useReadNotification } from '../../api/hooks/useReadNotification';
import NotificationCard from '../ui/NotificationCard';
import NotificationsAppHeader from '../ui/NotificationsAppHeader';

function NotificationsAppView() {
	const { mutate: deleteNotification } = useDeleteNotification();
	const { mutate: readNotification } = useReadNotification();

	// Halaman ini arsipnya, jadi yang sudah dibaca ikut ditarik — beda dengan
	// lonceng, yang hanya memuat yang belum dibaca.
	const { data: notifications, isLoading } = useGetAllNotifications(true);

	function handleDismiss(id: string) {
		deleteNotification(id);
	}

	function handleOpen(id: string) {
		readNotification(id);
	}

	if (isLoading) {
		return <FuseLoading />;
	}

	return (
		<FusePageSimple
			header={<NotificationsAppHeader />}
			content={
				<div className="mt-0 flex w-full flex-wrap p-4 sm:mt-2">
					<Masonry
						columns={{
							xs: 1,
							sm: 2,
							md: 3,
							lg: 4,
							xl: 5,
							xxl: 6
						}}
						spacing={2}
						className="my-masonry-grid flex w-full"
					>
						{_.orderBy(notifications, ['time'], ['desc']).map((notification) => (
							<NotificationCard
								key={notification.id}
								// Yang sudah dibaca diredupkan supaya yang baru tetap menonjol
								// walau keduanya berdampingan di arsip.
								className={notification.read ? 'opacity-60' : ''}
								item={notification}
								onClose={handleDismiss}
								onOpen={handleOpen}
							/>
						))}
					</Masonry>

					{notifications.length === 0 && (
						<div className="flex flex-1 items-center justify-center p-16">
							<Typography
								className="text-center text-xl"
								color="text.secondary"
							>
								Belum ada notifikasi.
							</Typography>
						</div>
					)}
				</div>
			}
		/>
	);
}

export default NotificationsAppView;
