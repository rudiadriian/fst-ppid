import { lazy } from 'react';
import { FuseRouteItemType } from '@fuse/utils/FuseUtils';
import { Navigate } from 'react-router';
import PpidAuthGuard from './components/PpidAuthGuard';

const PpidDashboard = lazy(() => import('./PpidDashboard'));
const PpidResourcePage = lazy(() => import('./PpidResourcePage'));
/**
 * Halaman arsip notifikasi. Komponennya sudah lama ada di `apps/notifications`
 * tetapi tidak pernah punya route, jadi satu-satunya jalan melihat notifikasi
 * adalah lonceng — dan lonceng hanya memuat yang belum dibaca.
 */
const NotifikasiPage = lazy(() => import('@/app/(control-panel)/apps/notifications/components/views/NotificationsAppView'));

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
			// Didaftarkan sebelum `:resourceSlug`: tanpa itu alamatnya tertangkap
			// pola modul dan dijawab "Modul tidak dikenal".
			{ path: 'notifikasi', element: <NotifikasiPage /> },
			{ path: ':resourceSlug', element: <PpidResourcePage /> }
		]
	}
];

export default route;
