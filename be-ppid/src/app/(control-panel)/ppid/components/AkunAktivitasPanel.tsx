import { useState } from 'react';
import Alert from '@mui/material/Alert';
import Chip from '@mui/material/Chip';
import LinearProgress from '@mui/material/LinearProgress';
import Table from '@mui/material/Table';
import TableBody from '@mui/material/TableBody';
import TableCell from '@mui/material/TableCell';
import TableHead from '@mui/material/TableHead';
import TablePagination from '@mui/material/TablePagination';
import TableRow from '@mui/material/TableRow';
import Typography from '@mui/material/Typography';
import { useTranslation } from 'react-i18next';
import { PpidApiError } from '../api/ppidApi';
import { useAkunAktivitas } from '../api/useAkun';
import { formatWaktu } from '../lib/waktu';

/**
 * Tab **Aktivitas** pada halaman Akun Saya.
 *
 * Isinya `audit_log` yang `user_id`-nya milik pemanggil saja — bukan modul Audit
 * Log, yang menampilkan jejak seluruh petugas dan menuntut haknya sendiri.
 * Nilai lama/baru tiap perubahan sengaja tidak ikut dikirim API: isinya bisa
 * memuat data modul yang rolenya sendiri tidak berhak melihat.
 */

/** Nama aksi apa adanya dari API dipetakan ke kalimat yang bisa dibaca. */
const LABEL_AKSI: Record<string, { label: string; warna: 'default' | 'success' | 'info' | 'warning' | 'error' }> = {
	login: { label: 'Masuk', warna: 'success' },
	logout: { label: 'Keluar', warna: 'default' },
	login_failed: { label: 'Gagal masuk', warna: 'warning' },
	login_locked: { label: 'Ditahan kunci masuk', warna: 'warning' },
	login_suspended: { label: 'Akun disuspend', warna: 'error' },
	create: { label: 'Menambah data', warna: 'info' },
	update: { label: 'Mengubah data', warna: 'info' },
	delete: { label: 'Menghapus data', warna: 'warning' },
	force_delete: { label: 'Menghapus permanen', warna: 'error' },
	ubah_password: { label: 'Mengubah password', warna: 'success' },
	ubah_profil_sendiri: { label: 'Mengubah profil', warna: 'success' },
	reset_password_diminta: { label: 'Meminta atur ulang password', warna: 'info' },
	reset_password_selesai: { label: 'Atur ulang password selesai', warna: 'success' }
};

export function AkunAktivitasPanel() {
	const { t } = useTranslation();
	const [halaman, setHalaman] = useState(0);
	const [perHalaman, setPerHalaman] = useState(15);

	const { data, isLoading, isFetching, error } = useAkunAktivitas(halaman + 1, perHalaman);
	const baris = data?.data ?? [];

	if (error) {
		return (
			<Alert severity="error">
				{error instanceof PpidApiError ? error.message : t('Riwayat aktivitas gagal dimuat.')}
			</Alert>
		);
	}

	return (
		<div className="flex flex-col gap-3">
			<Typography
				variant="body2"
				color="text.secondary"
			>
				{t(
					'Jejak yang tercatat atas akun Anda: masuk, keluar, percobaan masuk yang gagal, serta perubahan data yang Anda lakukan di panel.'
				)}
			</Typography>

			{isFetching && <LinearProgress />}

			<div className="border-divider overflow-x-auto rounded-lg border">
				<Table size="small">
					<TableHead>
						<TableRow>
							<TableCell>{t('Waktu')}</TableCell>
							<TableCell>{t('Aktivitas')}</TableCell>
							<TableCell>{t('Data')}</TableCell>
							<TableCell>{t('Alamat IP')}</TableCell>
						</TableRow>
					</TableHead>
					<TableBody>
						{baris.map((item) => {
							const info = LABEL_AKSI[item.action];

							return (
								<TableRow key={item.id}>
									<TableCell className="whitespace-nowrap">{formatWaktu(item.created_at)}</TableCell>
									<TableCell>
										<Chip
											size="small"
											label={info ? t(info.label) : item.action}
											color={info?.warna ?? 'default'}
											variant="outlined"
										/>
									</TableCell>
									<TableCell>
										{item.model ? `${item.model}${item.model_id ? ` #${item.model_id}` : ''}` : '—'}
									</TableCell>
									<TableCell>{item.ip_address ?? '—'}</TableCell>
								</TableRow>
							);
						})}

						{baris.length === 0 && !isLoading && (
							<TableRow>
								<TableCell
									colSpan={4}
									align="center"
								>
									<Typography
										variant="body2"
										color="text.secondary"
										className="py-4"
									>
										{t('Belum ada aktivitas yang tercatat.')}
									</Typography>
								</TableCell>
							</TableRow>
						)}
					</TableBody>
				</Table>
			</div>

			<TablePagination
				component="div"
				count={data?.meta.total ?? 0}
				page={halaman}
				onPageChange={(_event, halamanBaru) => setHalaman(halamanBaru)}
				rowsPerPage={perHalaman}
				rowsPerPageOptions={[15, 30, 50]}
				onRowsPerPageChange={(event) => {
					setPerHalaman(Number(event.target.value));
					setHalaman(0);
				}}
				labelRowsPerPage={t('Baris per halaman')}
			/>
		</div>
	);
}

export default AkunAktivitasPanel;
