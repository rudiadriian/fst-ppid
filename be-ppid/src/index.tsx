import '@i18n/i18n';
import './styles/index.css';
import { createRoot } from 'react-dom/client';
import { createBrowserRouter, RouterProvider } from 'react-router';
import routes from 'src/configs/routesConfig';

/**
 * Cabut service worker mock (MSW) bawaan template Fuse.
 *
 * Panel PPID mengambil seluruh datanya dari `api-ppid`, jadi tidak ada modul
 * yang butuh data tiruan lagi. Service worker yang terlanjur terpasang di
 * peramban ikut mencegat `/api/v1/*` sehingga permintaan asli gagal — termasuk
 * saat login. Registrasi lama dicabut di sini supaya peramban yang pernah
 * membuka versi sebelumnya pulih tanpa perlu menghapus data situs manual.
 */
async function bersihkanServiceWorkerLama(): Promise<void> {
	if (!('serviceWorker' in navigator)) {
		return;
	}

	try {
		const registrations = await navigator.serviceWorker.getRegistrations();
		await Promise.all(registrations.map((registration) => registration.unregister()));
	} catch (error) {
		// Kegagalan pembersihan tidak boleh menghalangi aplikasi tampil.
		console.warn('Gagal mencabut service worker lama:', error);
	}
}

const container = document.getElementById('app');

if (!container) {
	throw new Error('Failed to find the root element');
}

function render(target: HTMLElement) {
	const root = createRoot(target, {
		onUncaughtError: (error, errorInfo) => {
			console.error('UncaughtError error', error, errorInfo.componentStack);
		},
		onCaughtError: (error, errorInfo) => {
			console.error('Caught error', error, errorInfo.componentStack);
		}
	});

	const router = createBrowserRouter(routes);

	root.render(<RouterProvider router={router} />);
}

// Aplikasi tetap dirender walau pembersihan service worker gagal.
bersihkanServiceWorkerLama().finally(() => render(container));
