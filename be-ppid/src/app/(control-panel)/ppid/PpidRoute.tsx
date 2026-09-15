import { FuseRouteItemType } from '@fuse/utils/FuseUtils';
import { Navigate } from 'react-router';
import lazyModul from '@/utils/lazyModul';
import PpidAuthGuard from './components/PpidAuthGuard';

/*
 * `lazyModul`, bukan `lazy` biasa: panel yang sudah terbuka sebelum deploy
 * memegang nama chunk rilis lama, dan meminta berkas yang sudah tidak ada
 * menjatuhkan halaman dengan "Failed to fetch dynamically imported module"
 * (UAT poin 17). Pembungkus ini memuat ulang halaman satu kali supaya nama
 * chunk yang benar terambil.
 */
const PpidDashboard = lazyModul(() => import('./PpidDashboard'), 'dashboard');
const PpidResourcePage = lazyModul(() => import('./PpidResourcePage'), 'resource');
/** Akun milik petugas sendiri: ubah password mandiri. */
const PpidAkunPage = lazyModul(() => import('./PpidAkunPage'), 'akun');
/**
 * Halaman arsip notifikasi. Komponennya sudah lama ada di `apps/notifications`
 * tetapi tidak pernah punya route, jadi satu-satunya jalan melihat notifikasi
 * adalah lonceng — dan lonceng hanya memuat yang belum dibaca.
 */
const NotifikasiPage = lazyModul(
	() => import('@/app/(control-panel)/apps/notifications/components/views/NotificationsAppView'),
	'notifikasi'
);

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
			// Sama alasannya: didaftarkan sebelum `:resourceSlug` supaya tidak
			// dikira nama modul. Tidak digantung hak modul mana pun — isinya
			// akun pemiliknya sendiri.
			{ path: 'akun', element: <PpidAkunPage /> },
			{ path: ':resourceSlug', element: <PpidResourcePage /> }
		]
	}
];

export default route;
