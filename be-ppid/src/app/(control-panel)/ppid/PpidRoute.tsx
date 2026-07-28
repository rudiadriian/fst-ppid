import { lazy } from 'react';
import { FuseRouteItemType } from '@fuse/utils/FuseUtils';
import { Navigate } from 'react-router';

const PpidDashboard = lazy(() => import('./PpidDashboard'));
const PpidResourcePage = lazy(() => import('./PpidResourcePage'));

/**
 * Route panel CMS PPID.
 *
 * Satu route berparameter melayani seluruh modul; modul yang tersedia
 * ditentukan registry di `lib/resources.ts`, bukan daftar route.
 */
const route: FuseRouteItemType[] = [
	{
		path: 'ppid',
		children: [
			{ path: '', element: <Navigate to="/ppid/dashboard" replace /> },
			{ path: 'dashboard', element: <PpidDashboard /> },
			{ path: ':resourceSlug', element: <PpidResourcePage /> }
		]
	}
];

export default route;
