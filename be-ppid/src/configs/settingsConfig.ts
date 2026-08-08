import themesConfig from 'src/configs/themesConfig';
import { FuseSettingsConfigType } from '@fuse/core/FuseSettings/FuseSettings';

import i18n from '@i18n/i18n';

/**
 * The settingsConfig object is a configuration object for the Fuse application's settings.
 */
const settingsConfig: FuseSettingsConfigType = {
	/**
	 * The layout object defines the layout style and configuration for the application.
	 */
	layout: {
		/**
		 * The style property defines the layout style for the application.
		 */
		style: 'layout1', // layout1 layout2 layout3
		/**
		 * The config property defines the layout configuration for the application.
		 * Check out default layout configs at src/components/theme-layouts for example src/components/theme-layouts/layout1/Layout1Config.js
		 */
		config: {
			navbar: {
				style: 'style-1'
			}
		} // checkout default layout configs at src/components/theme-layouts for example  src/components/theme-layouts/layout1/Layout1Config.js
	},

	/**
	 * The customScrollbars property defines whether or not to use custom scrollbars in the application.
	 */
	customScrollbars: true,

	/**
	 * The direction property defines the text direction for the application.
	 */
	direction: i18n.dir(i18n.options.lng) || 'ltr', // rtl, ltr
	/**
	 * The theme object defines the color theme for the application.
	 */
	theme: {
		main: themesConfig.default,
		navbar: themesConfig.defaultNavbar,
		toolbar: themesConfig.default,
		footer: themesConfig.default
	},

	/**
	 * Gerbang akses bawaan dimatikan (null) karena role PPID berasal dari tabel
	 * `roles` di database dan bisa ditambah lewat CMS — daftar role statis di
	 * frontend pasti ketinggalan. Halaman panel dijaga `PpidAuthGuard` (harus
	 * sudah login) sedangkan hak per modul ditegakkan API lewat middleware
	 * `akses:{modul},{aksi}`.
	 */
	defaultAuth: null,

	/**
	 * The loginRedirectUrl property defines the default redirect URL for the logged-in user.
	 */
	loginRedirectUrl: '/ppid/dashboard'
};

export default settingsConfig;
