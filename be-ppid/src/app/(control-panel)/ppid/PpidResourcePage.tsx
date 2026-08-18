import { useEffect, useState } from 'react';
import { useParams, useSearchParams } from 'react-router';
import MenuItem from '@mui/material/MenuItem';
import ListItemIcon from '@mui/material/ListItemIcon';
import Alert from '@mui/material/Alert';
import Typography from '@mui/material/Typography';
import FuseLoading from '@fuse/core/FuseLoading';
import FuseSvgIcon from '@fuse/core/FuseSvgIcon';
import ResourceListPage from './components/ResourceListPage';
import PermohonanStatusDialog from './components/PermohonanStatusDialog';
import PemohonDetailDialog from './components/PemohonDetailDialog';
import RoleAksesDialog from './components/RoleAksesDialog';
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
	const [roleTerpilih, setRoleTerpilih] = useState<ApiRecord | null>(null);
	const [pemohonTerpilih, setPemohonTerpilih] = useState<number | null>(null);

	/*
	 * Notifikasi panel menaut ke `/ppid/{modul}?detail={id}` supaya sekali klik
	 * langsung membuka barisnya, bukan berhenti di daftar modul. Parameternya
	 * dibaca sekali lalu dibersihkan dari URL — kalau dibiarkan, menutup dialog
	 * dan memuat ulang halaman akan membukanya lagi.
	 */
	const [searchParams, setSearchParams] = useSearchParams();
	const idDariNotifikasi = Number(searchParams.get('detail') ?? 0);

	useEffect(() => {
		if (!idDariNotifikasi || resourceSlug !== 'pemohon') {
			return;
		}

		setPemohonTerpilih(idDariNotifikasi);
		setSearchParams({}, { replace: true });
	}, [idDariNotifikasi, resourceSlug, setSearchParams]);

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
	const modulRole = config.slug === 'role';
	// Verifikasi data pemohon memakai hak `approve`, bukan `edit`: yang
	// dilakukan petugas adalah menyetujui/menolak berkas, bukan menyunting
	// datanya. Modul Pemohon sendiri tetap `readOnly`.
	const modulPemohon = config.slug === 'pemohon';

	// Detail pemohon boleh dibuka siapa pun yang berhak melihat modulnya;
	// tombol keputusannya yang menuntut hak `Setujui`.
	const bolehAksi = modulPemohon ? true : akses.edit;

	const aksiBaris =
		bolehAksi && (modulPermohonan || modulRole || modulPemohon)
			? (baris: ApiRecord, tutupMenu: () => void) => [
					modulPermohonan ? (
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
					) : modulPemohon ? (
						<MenuItem
							key="detail"
							onClick={() => {
								setPemohonTerpilih(Number(baris.id));
								tutupMenu();
							}}
						>
							<ListItemIcon>
								<FuseSvgIcon size={18}>lucide:user-check</FuseSvgIcon>
							</ListItemIcon>
							{akses.approve ? 'Detail & verifikasi' : 'Lihat detail'}
						</MenuItem>
					) : (
						<MenuItem
							key="akses"
							onClick={() => {
								setRoleTerpilih(baris);
								tutupMenu();
							}}
						>
							<ListItemIcon>
								<FuseSvgIcon size={18}>lucide:shield-check</FuseSvgIcon>
							</ListItemIcon>
							Atur hak akses
						</MenuItem>
					)
				]
			: undefined;

	return (
		<>
			<ResourceListPage
				// Key memaksa state tabel (halaman, filter, pencarian) direset
				// saat berpindah modul lewat menu.
				key={config.slug}
				config={config}
				aksiBaris={aksiBaris}
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

			{modulPemohon && (
				<PemohonDetailDialog
					open={pemohonTerpilih !== null}
					onClose={() => setPemohonTerpilih(null)}
					pemohonId={pemohonTerpilih}
					bolehVerifikasi={akses.approve}
				/>
			)}

			{modulRole && (
				<RoleAksesDialog
					open={roleTerpilih !== null}
					onClose={() => setRoleTerpilih(null)}
					role={
						roleTerpilih
							? {
									id: Number(roleTerpilih.id),
									name: String(roleTerpilih.name ?? ''),
									slug: String(roleTerpilih.slug ?? '')
								}
							: null
					}
				/>
			)}
		</>
	);
}

export default PpidResourcePage;
