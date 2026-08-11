import { FuseNavItemType } from '@fuse/core/FuseNavigation/types/FuseNavItemType';
import { resources } from './resources';

/**
 * Susunan menu panel CMS.
 *
 * Urutan grup dan isinya ditentukan di sini; label, ikon, dan URL diambil dari
 * registry modul supaya menu tidak pernah menyimpang dari halaman yang ada.
 */
/**
 * Susunannya sengaja dibuat sama dengan menu situs publik (Informasi Publik →
 * Standar Layanan → Layanan), supaya operator mencari modul di tempat yang sama
 * dengan yang dilihat pengunjung. Modul yang tidak punya padanan di situs publik
 * tetap berkumpul di Konten Situs dan Administrasi Sistem.
 */
const GRUP: { id: string; title: string; icon: string; slugs: string[] }[] = [
	{
		id: 'ppid.informasi',
		title: 'Informasi Publik',
		icon: 'lucide:folder-open',
		// Berita ikut di sini: di situs publik ia sub modul Informasi Publik.
		slugs: ['informasi-publik', 'informasi-dikecualikan', 'kategori-informasi', 'berita', 'kategori-berita']
	},
	{
		id: 'ppid.standar',
		title: 'Standar Layanan',
		icon: 'lucide:clipboard-list',
		// Maklumat, kedua prosedur, dan jalur/waktu layanan adalah halaman —
		// isinya disunting lewat modul Halaman Statis.
		slugs: ['halaman-statis']
	},
	{
		id: 'ppid.layanan',
		title: 'Layanan',
		icon: 'lucide:inbox',
		slugs: ['permohonan', 'keberatan', 'laporan-layanan', 'pemohon', 'survey-kepuasan']
	},
	{
		id: 'ppid.konten',
		title: 'Konten Situs',
		icon: 'lucide:layout-dashboard',
		slugs: ['galeri', 'banner-slider', 'struktur-organisasi', 'regulasi', 'faq', 'tautan-terkait', 'menu-navigasi']
	},
	{
		id: 'ppid.sistem',
		title: 'Administrasi Sistem',
		icon: 'lucide:settings',
		slugs: ['pengguna', 'role', 'pengaturan-situs', 'audit-log']
	}
];

/**
 * Bangun menu, hanya berisi modul yang boleh dilihat role saat ini.
 *
 * @param modulBolehLihat slug modul yang punya hak `view`; `null` berarti data
 *   hak akses belum tiba sehingga seluruh menu ditampilkan sementara.
 */
export function buatNavigasiPpid(modulBolehLihat: Set<string> | null): FuseNavItemType[] {
	const bolehTampil = (modulSlug: string) => modulBolehLihat === null || modulBolehLihat.has(modulSlug);

	const menu: FuseNavItemType[] = [];

	if (bolehTampil('dashboard')) {
		menu.push({
			id: 'ppid.dashboard',
			title: 'Dashboard',
			type: 'item',
			icon: 'lucide:layout-dashboard',
			url: '/ppid/dashboard'
		});
	}

	GRUP.forEach((grup) => {
		const anak = grup.slugs
			.map((slug) => resources.find((r) => r.slug === slug))
			.filter((config) => config !== undefined && bolehTampil(config.modul))
			.map((config) => ({
				id: `ppid.${config!.slug}`,
				title: config!.title,
				type: 'item' as const,
				icon: config!.icon,
				url: `/ppid/${config!.slug}`
			}));

		if (anak.length === 0) {
			return;
		}

		menu.push({
			id: grup.id,
			title: grup.title,
			type: 'group',
			icon: grup.icon,
			children: anak
		});
	});

	return menu;
}

export default buatNavigasiPpid;
