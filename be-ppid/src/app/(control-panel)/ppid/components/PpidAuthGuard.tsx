import { Navigate, Outlet, useLocation } from 'react-router';
import useAuth from '@fuse/core/FuseAuthProvider/useAuth';
import FuseLoading from '@fuse/core/FuseLoading';

/**
 * Gerbang halaman panel CMS: wajib sudah login.
 *
 * Pemeriksaan role sengaja tidak dilakukan di sini. Role PPID tersimpan di
 * database dan bisa bertambah lewat modul Pengguna, jadi daftar role di
 * frontend akan cepat basi. Hak akses per modul dibaca dari API
 * (`/me/navigation`) untuk keperluan tampilan, dan ditegakkan sungguhan oleh
 * middleware `akses:` di setiap endpoint.
 */
function PpidAuthGuard() {
	const { authState } = useAuth();
	const location = useLocation();

	// Status awal: provider masih mencoba auto login dengan token tersimpan.
	if (!authState || authState.authStatus === 'configuring') {
		return <FuseLoading />;
	}

	if (!authState.isAuthenticated) {
		return (
			<Navigate
				to="/sign-in"
				state={{ from: location.pathname }}
				replace
			/>
		);
	}

	return <Outlet />;
}

export default PpidAuthGuard;
