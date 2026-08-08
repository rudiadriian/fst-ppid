import { lazy, memo, Suspense } from 'react';

const NotificationPanel = lazy(
	() => import('@/app/(control-panel)/apps/notifications/components/views/NotificationPanel')
);

/**
 * The right side layout 1.
 */
function RightSideLayout1() {
	return (
		<Suspense>
			<NotificationPanel />
		</Suspense>
	);
}

export default memo(RightSideLayout1);
