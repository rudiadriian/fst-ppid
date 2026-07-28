import i18n from '@i18n';
import { FuseNavItemType } from '@fuse/core/FuseNavigation/types/FuseNavItemType';
import { buatNavigasiPpid } from '@/app/(control-panel)/ppid/lib/navigation';
import ar from './navigation-i18n/ar';
import en from './navigation-i18n/en';
import tr from './navigation-i18n/tr';

i18n.addResourceBundle('en', 'navigation', en);
i18n.addResourceBundle('tr', 'navigation', tr);
i18n.addResourceBundle('ar', 'navigation', ar);

/**
 * Menu awal panel admin PPID.
 *
 * Berisi seluruh modul dan dipakai selama data hak akses dari API belum tiba.
 * Setelah tiba, `PpidNavigationSync` menggantinya dengan menu yang sudah
 * disaring sesuai role pengguna.
 */
const navigationConfig: FuseNavItemType[] = buatNavigasiPpid(null);

export default navigationConfig;
