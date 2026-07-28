import { useEffect } from 'react';
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

	useEffect(() => {
		if (!data) {
			return;
		}

		const bolehLihat = new Set(data.data.modul.filter((m) => m.akses.view).map((m) => m.slug));

		setNavigation(buatNavigasiPpid(bolehLihat));
	}, [data, setNavigation]);

	return null;
}

export default PpidNavigationSync;
