import { useState } from 'react';
import Alert from '@mui/material/Alert';
import Avatar from '@mui/material/Avatar';
import Paper from '@mui/material/Paper';
import Tab from '@mui/material/Tab';
import Tabs from '@mui/material/Tabs';
import Typography from '@mui/material/Typography';
import { useTranslation } from 'react-i18next';
import FuseLoading from '@fuse/core/FuseLoading';
import { PpidApiError } from './api/ppidApi';
import { useAkunProfil } from './api/useAkun';
import AkunAksesPanel from './components/AkunAksesPanel';
import AkunAktivitasPanel from './components/AkunAktivitasPanel';
import AkunPasswordPanel from './components/AkunPasswordPanel';
import AkunProfilPanel from './components/AkunProfilPanel';
import { urlMedia } from './components/UploadField';

/**
 * Halaman **Akun Saya**: tempat petugas mengurus akunnya sendiri.
 *
 * Empat tab: Profil, Ubah Password, Aktivitas, dan Role & Hak Akses. Semuanya
 * berdiri di luar modul Pengguna dengan sengaja — modul itu adalah tempat
 * administrator mengelola akun orang lain dan menuntut hak modulnya, sedangkan
 * halaman ini terbuka untuk setiap petugas yang bisa masuk, berapa pun hak
 * modulnya, karena yang disentuh hanya akunnya sendiri.
 */

type TabAkun = 'profil' | 'password' | 'aktivitas' | 'akses';

function PpidAkunPage() {
	const { t } = useTranslation();
	const [tab, setTab] = useState<TabAkun>('profil');

	const { data: profil, isLoading, error } = useAkunProfil();

	if (isLoading) {
		return <FuseLoading />;
	}

	if (error || !profil) {
		return (
			<div className="p-6">
				<Alert severity="error">
					{error instanceof PpidApiError ? error.message : t('Data akun gagal dimuat.')}
				</Alert>
			</div>
		);
	}

	return (
		<div className="flex w-full flex-col">
			<div className="flex items-center gap-3 p-4 md:p-6">
				<Avatar
					src={profil.photo_url ? urlMedia(profil.photo_url) : undefined}
					alt={profil.name}
					className="h-14 w-14"
				>
					{profil.name?.[0]}
				</Avatar>
				<div className="min-w-0">
					<Typography
						variant="h5"
						className="truncate font-semibold"
					>
						{profil.name}
					</Typography>
					<Typography
						variant="body2"
						color="text.secondary"
						className="truncate"
					>
						{profil.email}
						{profil.role ? ` — ${profil.role.name}` : ''}
					</Typography>
				</div>
			</div>

			<Paper
				elevation={0}
				className="border-divider mx-4 mb-6 flex flex-auto flex-col rounded-lg border md:mx-6"
			>
				<Tabs
					value={tab}
					onChange={(_event, nilai: TabAkun) => setTab(nilai)}
					variant="scrollable"
					scrollButtons="auto"
					className="border-divider border-b"
				>
					<Tab
						value="profil"
						label={t('Profil')}
					/>
					<Tab
						value="password"
						label={t('Ubah Password')}
					/>
					<Tab
						value="aktivitas"
						label={t('Aktivitas')}
					/>
					<Tab
						value="akses"
						label={t('Role & Hak Akses')}
					/>
				</Tabs>

				<div className="p-4 md:p-6">
					{tab === 'profil' && <AkunProfilPanel profil={profil} />}
					{tab === 'password' && <AkunPasswordPanel />}
					{/* Dipasang hanya saat tabnya dibuka: riwayatnya bisa panjang,
					    tidak perlu ditembak setiap kali halaman ini dibuka. */}
					{tab === 'aktivitas' && <AkunAktivitasPanel />}
					{tab === 'akses' && <AkunAksesPanel profil={profil} />}
				</div>
			</Paper>
		</div>
	);
}

export default PpidAkunPage;
