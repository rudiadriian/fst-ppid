import FuseScrollbars from '@fuse/core/FuseScrollbars';
import { styled } from '@mui/material/styles';
import IconButton from '@mui/material/IconButton';
import SwipeableDrawer from '@mui/material/SwipeableDrawer';
import Typography from '@mui/material/Typography';
import { useEffect } from 'react';
import FuseSvgIcon from '@fuse/core/FuseSvgIcon';
import _ from 'lodash';
import usePathname from '@fuse/hooks/usePathname';
import NotificationCard from '../ui/NotificationCard';
import { useGetAllNotifications } from '../../api/hooks/useGetAllNotifications';
import { useDeleteNotification } from '../../api/hooks/useDeleteNotification';
import { useDeleteNotifications } from '../../api/hooks/useDeleteNotifications';
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
	const { data: notifications } = useGetAllNotifications();
	const { mutate: deleteNotification } = useDeleteNotification();
	const { mutate: deleteNotifications } = useDeleteNotifications();
	const { isOpen, close, toggle } = useNotificationPanelContext();

	useEffect(() => {
		if (isOpen) {
			close();
		}
		// eslint-disable-next-line react-hooks/exhaustive-deps
	}, [pathname]);

	function handleClose() {
		close();
	}

	function handleDismiss(id: string) {
		deleteNotification(id);
	}

	function handleDismissAll() {
		deleteNotifications(notifications.map((notification) => notification.id));
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
							<Typography className="text-2xl leading-none font-bold">Notifications</Typography>
							<Typography
								className="text-md cursor-pointer underline"
								color="secondary"
								onClick={handleDismissAll}
							>
								dismiss all
							</Typography>
						</div>
						{_.orderBy(notifications, ['time'], ['desc']).map((item) => (
							<NotificationCard
								key={item.id}
								className="mb-4"
								item={item}
								onClose={handleDismiss}
							/>
						))}
					</div>
				) : (
					<div className="flex flex-1 items-center justify-center p-4">
						<Typography
							className="text-center text-xl"
							color="text.secondary"
						>
							There are no notifications for now.
						</Typography>
					</div>
				)}
			</FuseScrollbars>
		</StyledSwipeableDrawer>
	);
}

export default NotificationPanel;
