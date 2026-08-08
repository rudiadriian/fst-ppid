import { lazy, memo, Suspense } from 'react';

const NotificationPanel = lazy(
	() => import('@/app/(control-panel)/apps/notifications/components/views/NotificationPanel')
);

/**
 * The right side layout 3.
 */
function RightSideLayout3() {
	return (
		<Suspense>
			<NotificationPanel />
		</Suspense>
	);
}

export default memo(RightSideLayout3);
