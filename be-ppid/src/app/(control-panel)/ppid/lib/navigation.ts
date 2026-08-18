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
		// Maklumat punya modul sendiri karena isinya berkas unggahan (PDF/gambar)
		// yang dibaca langsung di situs publik; kedua prosedur dan jalur/waktu
		// layanan tetap berupa halaman biasa lewat modul Halaman Statis.
		slugs: ['maklumat', 'halaman-statis']
	},
	{
		id: 'ppid.layanan',
		title: 'Layanan',
		icon: 'lucide:inbox',
		// `laporan-statistik` dihapus pada langkah 58; yang tersisa hanya
		// Laporan Pelayanan (berkas laporan per tahun).
		slugs: ['permohonan', 'keberatan', 'laporan-pelayanan', 'pemohon', 'survey-kepuasan']
	},
	{
		id: 'ppid.konten',
		title: 'Konten Situs',
		icon: 'lucide:layout-dashboard',
		// Dua modul sengaja tidak didaftarkan karena isinya tidak punya tempat
		// tayang di situs publik — menampilkannya hanya membuat operator
		// menyunting data yang tidak pernah muncul:
		//   - `galeri`        : modul Galeri dihapus dari situs pada langkah 9;
		//   - `menu-navigasi` : menu situs publik masih ditulis di template
		//                       (`layouts/header.blade.php`), belum dibaca dari
		//                       tabel ini.
		//   - `tautan-terkait`: modul Tautan dihapus dari panel pada langkah 54.
		// Modul, API, dan datanya tetap ada dan bisa dikembalikan ke menu.
		slugs: ['banner-slider', 'struktur-organisasi', 'regulasi', 'faq']
	},
	{
		id: 'ppid.sistem',
		title: 'Manajemen Sistem',
		icon: 'lucide:settings',
		// Daftar modul tidak lagi punya halaman sendiri (langkah 54): isinya
		// tampil sebagai baris matrix pada dialog "Atur hak akses" di modul Role.
		slugs: ['pengguna', 'role', 'pengaturan-situs', 'audit-log']
	}
];

/**
 * Bangun menu, hanya berisi modul yang boleh dilihat role saat ini.
 *
 * @param modulBolehLihat slug modul yang punya hak `view`; `null` berarti data
 *   hak akses belum tiba sehingga seluruh menu ditampilkan sementara.
 */
export function buatNavigasiPpid(
	modulBolehLihat: Set<string> | null,
	/** Penerjemah label menu; bawaannya mengembalikan teks Indonesia apa adanya. */
	t: (teks: string) => string = (teks) => teks
): FuseNavItemType[] {
	const bolehTampil = (modulSlug: string) => modulBolehLihat === null || modulBolehLihat.has(modulSlug);

	const menu: FuseNavItemType[] = [];

	// Ringkasan, analisa, SLA, dan KPI seluruhnya ada di halaman Dashboard —
	// tidak ada modul Analitik terpisah, supaya operator cukup membuka satu
	// halaman untuk melihat gambaran layanan.
	if (bolehTampil('dashboard')) {
		menu.push({
			id: 'ppid.dashboard',
			title: t('Dashboard'),
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
				title: t(config!.title),
				type: 'item' as const,
				icon: config!.icon,
				url: `/ppid/${config!.slug}`
			}));

		if (anak.length === 0) {
			return;
		}

		menu.push({
			id: grup.id,
			title: t(grup.title),
			type: 'group',
			icon: grup.icon,
			children: anak
		});
	});

	return menu;
}

export default buatNavigasiPpid;
