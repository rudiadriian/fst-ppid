import { useEffect } from 'react';
import { useTranslation } from 'react-i18next';
import { useNavigationContext } from '@/components/theme-layouts/components/navigation/contexts/useNavigationContext';
import { useNavigasi } from '../api/useNavigasi';
import { buatNavigasiPpid } from '../lib/navigation';

/**
 * Selaraskan menu samping dengan hak akses role yang sedang login.
 *
 * Komponen ini tidak menggambar apa pun; ia hanya menulis ulang daftar menu
 * begitu data hak akses tiba dari API. Sebelum itu, menu bawaan tetap tampil.
 */
function PpidNavigationSync() {
	const { data } = useNavigasi();
	const { setNavigation } = useNavigationContext();
	// `i18n.language` ikut jadi dependensi supaya menu ditulis ulang saat
	// pengguna mengganti bahasa, bukan hanya saat hak akses tiba.
	const { t, i18n } = useTranslation();

	useEffect(() => {
		if (!data) {
			return;
		}

		const bolehLihat = new Set(data.data.modul.filter((m) => m.akses.view).map((m) => m.slug));

		setNavigation(buatNavigasiPpid(bolehLihat, t));
	}, [data, setNavigation, t, i18n.language]);

	return null;
}

export default PpidNavigationSync;
