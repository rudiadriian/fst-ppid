import Alert from '@mui/material/Alert';
import Table from '@mui/material/Table';
import TableBody from '@mui/material/TableBody';
import TableCell from '@mui/material/TableCell';
import TableHead from '@mui/material/TableHead';
import TableRow from '@mui/material/TableRow';
import Typography from '@mui/material/Typography';
import { useTranslation } from 'react-i18next';
import FuseSvgIcon from '@fuse/core/FuseSvgIcon';
import { AkunProfil } from '../api/useAkun';

/**
 * Tab **Role & Hak Akses** pada halaman Akun Saya.
 *
 * Matrix yang sama dengan dialog "Atur hak akses" di modul Role, tetapi hanya
 * bisa dibaca: yang menentukan isinya adalah role, dan role diubah administrator
 * — bukan pemiliknya sendiri.
 *
 * Seluruh modul aktif ditampilkan, termasuk yang tertutup untuk role ini.
 * Halaman ini menjawab "saya boleh apa saja"; jawabannya tidak lengkap kalau
 * modul yang tertutup justru disembunyikan.
 */

const KOLOM_HAK = [
	{ kunci: 'view', label: 'Lihat' },
	{ kunci: 'create', label: 'Tambah' },
	{ kunci: 'edit', label: 'Ubah' },
	{ kunci: 'delete', label: 'Hapus' },
	{ kunci: 'approve', label: 'Setujui' },
	{ kunci: 'export', label: 'Ekspor' }
] as const;

type AkunAksesPanelProps = {
	profil: AkunProfil;
};

export function AkunAksesPanel({ profil }: AkunAksesPanelProps) {
	const { t } = useTranslation();

	const jumlahBolehLihat = profil.akses.filter((baris) => baris.view).length;

	return (
		<div className="flex flex-col gap-3">
			<div>
				<Typography className="font-semibold">
					{t('Role')}: {profil.role?.name ?? '—'}
				</Typography>
				<Typography
					variant="body2"
					color="text.secondary"
				>
					{profil.role?.description ??
						t('Hak akses di bawah ini mengikuti role Anda; perubahannya lewat administrator.')}
				</Typography>
				<Typography
					variant="body2"
					color="text.secondary"
				>
					{t('Modul yang boleh dilihat')}: {jumlahBolehLihat} {t('dari')} {profil.akses.length}
				</Typography>
			</div>

			{profil.super_admin && (
				<Alert severity="info">
					{t(
						'Role super admin memegang seluruh hak pada semua modul, termasuk modul yang ditambahkan kemudian.'
					)}
				</Alert>
			)}

			<div className="border-divider overflow-x-auto rounded-lg border">
				<Table size="small">
					<TableHead>
						<TableRow>
							<TableCell>{t('Modul')}</TableCell>
							{KOLOM_HAK.map((kolom) => (
								<TableCell
									key={kolom.kunci}
									align="center"
								>
									{t(kolom.label)}
								</TableCell>
							))}
						</TableRow>
					</TableHead>
					<TableBody>
						{profil.akses.map((baris) => (
							<TableRow key={baris.modul_id}>
								<TableCell>
									<span className="font-medium">{baris.nama}</span>
									<Typography
										variant="caption"
										color="text.secondary"
										className="block"
									>
										{baris.slug}
									</Typography>
								</TableCell>
								{KOLOM_HAK.map((kolom) => (
									<TableCell
										key={kolom.kunci}
										align="center"
									>
										{baris[kolom.kunci] ? (
											<FuseSvgIcon
												size={18}
												color="success"
											>
												lucide:check
											</FuseSvgIcon>
										) : (
											<FuseSvgIcon
												size={18}
												color="disabled"
											>
												lucide:minus
											</FuseSvgIcon>
										)}
									</TableCell>
								))}
							</TableRow>
						))}
					</TableBody>
				</Table>
			</div>

			<Typography
				variant="caption"
				color="text.secondary"
			>
				{t(
					'Penegakan hak akses ada di API (middleware per endpoint), bukan di tampilan — tombol yang tersembunyi bukan satu-satunya penjaga.'
				)}
			</Typography>
		</div>
	);
}

export default AkunAksesPanel;
