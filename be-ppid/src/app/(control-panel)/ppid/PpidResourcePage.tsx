import { useState } from 'react';
import { useParams } from 'react-router';
import MenuItem from '@mui/material/MenuItem';
import ListItemIcon from '@mui/material/ListItemIcon';
import Alert from '@mui/material/Alert';
import Typography from '@mui/material/Typography';
import FuseLoading from '@fuse/core/FuseLoading';
import FuseSvgIcon from '@fuse/core/FuseSvgIcon';
import ResourceListPage from './components/ResourceListPage';
import PermohonanStatusDialog from './components/PermohonanStatusDialog';
import { resourceBySlug } from './lib/resources';
import { useAksesModul } from './api/useNavigasi';
import { ApiRecord } from './api/ppidApi';

/**
 * Satu halaman untuk semua modul CMS.
 *
 * Modul ditentukan dari segmen URL (`/ppid/:resourceSlug`) lalu dicocokkan ke
 * registry konfigurasi. Karena itu menambah modul baru tidak memerlukan route
 * atau komponen halaman baru.
 */
function PpidResourcePage() {
	const { resourceSlug } = useParams<{ resourceSlug: string }>();
	const config = resourceSlug ? resourceBySlug.get(resourceSlug) : undefined;

	const { akses, isLoading } = useAksesModul(config?.modul ?? '');

	const [permohonanTerpilih, setPermohonanTerpilih] = useState<ApiRecord | null>(null);

	if (!config) {
		return (
			<div className="p-6">
				<Alert severity="error">Modul &quot;{resourceSlug}&quot; tidak dikenal.</Alert>
			</div>
		);
	}

	if (isLoading) {
		return <FuseLoading />;
	}

	if (!akses.view) {
		return (
			<div className="p-6">
				<Alert severity="warning">
					<Typography className="font-medium">Akses ditolak</Typography>
					Role Anda tidak memiliki hak lihat pada modul {config.title}.
				</Alert>
			</div>
		);
	}

	const modulPermohonan = config.slug === 'permohonan';

	return (
		<>
			<ResourceListPage
				// Key memaksa state tabel (halaman, filter, pencarian) direset
				// saat berpindah modul lewat menu.
				key={config.slug}
				config={config}
				aksiBaris={
					modulPermohonan && akses.edit
						? (baris, tutupMenu) => [
								<MenuItem
									key="status"
									onClick={() => {
										setPermohonanTerpilih(baris);
										tutupMenu();
									}}
								>
									<ListItemIcon>
										<FuseSvgIcon size={18}>lucide:git-branch</FuseSvgIcon>
									</ListItemIcon>
									Ubah status
								</MenuItem>
							]
						: undefined
				}
			/>

			{modulPermohonan && (
				<PermohonanStatusDialog
					open={permohonanTerpilih !== null}
					onClose={() => setPermohonanTerpilih(null)}
					permohonan={
						permohonanTerpilih
							? {
									id: Number(permohonanTerpilih.id),
									kode_permohonan: String(permohonanTerpilih.kode_permohonan ?? ''),
									status: String(permohonanTerpilih.status ?? '')
								}
							: null
					}
				/>
			)}
		</>
	);
}

export default PpidResourcePage;
