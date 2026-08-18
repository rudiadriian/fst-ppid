import { lazy } from 'react';
import { FuseRouteItemType } from '@fuse/utils/FuseUtils';
import { Navigate } from 'react-router';
import PpidAuthGuard from './components/PpidAuthGuard';

const PpidDashboard = lazy(() => import('./PpidDashboard'));
const PpidResourcePage = lazy(() => import('./PpidResourcePage'));

/**
 * Route panel CMS PPID.
 *
 * Satu route berparameter melayani seluruh modul; modul yang tersedia
 * ditentukan registry di `lib/resources.ts`, bukan daftar route. Semuanya
 * berada di balik `PpidAuthGuard` sehingga tidak bisa dibuka tanpa login.
 */
const route: FuseRouteItemType[] = [
	{
		path: 'ppid',
		element: <PpidAuthGuard />,
		children: [
			{ path: '', element: <Navigate to="/ppid/dashboard" replace /> },
			{ path: 'dashboard', element: <PpidDashboard /> },
			// Analitik & SLA melebur ke Dashboard. Alamat lamanya dibiarkan
			// hidup sebagai pengalihan supaya tautan/bookmark tidak mati.
			{ path: 'analitik', element: <Navigate to="/ppid/dashboard" replace /> },
			{ path: ':resourceSlug', element: <PpidResourcePage /> }
		]
	}
];

export default route;
