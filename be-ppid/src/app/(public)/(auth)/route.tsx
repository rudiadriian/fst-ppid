import { FuseRouteItemType } from '@fuse/utils/FuseUtils';
import authRoles from '@auth/authRoles';
import SignInPageView from './components/views/SignInPageView';
import SignUpPageView from './components/views/SignUpPageView';
import SignOutPageView from './components/views/SignOutPageView';
import LupaPasswordPageView from './components/views/LupaPasswordPageView';
import PasswordBaruPageView from './components/views/PasswordBaruPageView';

/**
 * Tata letak halaman auth: tanpa navbar, toolbar, footer, dan panel samping.
 *
 * Disimpan sebagai satu nilai karena kelima halaman memakainya sama persis;
 * sebelumnya blok yang identik ini ditulis ulang untuk tiap rute.
 */
const tanpaKerangka = {
	layout: {
		config: {
			navbar: { display: false },
			toolbar: { display: false },
			footer: { display: false },
			leftSidePanel: { display: false },
			rightSidePanel: { display: false }
		}
	}
};

const route: FuseRouteItemType = {
	children: [
		{
			path: 'lupa-password',
			element: <LupaPasswordPageView />,
			settings: tanpaKerangka,
			auth: authRoles.onlyGuest
		},
		{
			/*
			 * Dibuka dari tautan email, yang membawa `?token=…&email=…`.
			 * Sengaja `onlyGuest`: orang yang masih punya sesi aktif dan hendak
			 * mengganti passwordnya lewat tautan ini harus keluar dulu, supaya
			 * token lamanya tidak tertinggal hidup setelah password berganti.
			 */
			path: 'reset-password',
			element: <PasswordBaruPageView />,
			settings: tanpaKerangka,
			auth: authRoles.onlyGuest
		},
		{
			path: 'sign-in',
			element: <SignInPageView />,
			settings: tanpaKerangka,
			auth: authRoles.onlyGuest // []
		},
		{
			path: 'sign-up',
			element: <SignUpPageView />,
			settings: tanpaKerangka,
			auth: authRoles.onlyGuest
		},
		{
			path: 'sign-out',
			element: <SignOutPageView />,
			settings: tanpaKerangka,
			auth: null
		}
	]
};

export default route;
