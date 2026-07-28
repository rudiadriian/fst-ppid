import { useEffect, useState } from 'react';
import Dialog from '@mui/material/Dialog';
import DialogTitle from '@mui/material/DialogTitle';
import DialogContent from '@mui/material/DialogContent';
import DialogActions from '@mui/material/DialogActions';
import Button from '@mui/material/Button';
import TextField from '@mui/material/TextField';
import MenuItem from '@mui/material/MenuItem';
import Alert from '@mui/material/Alert';
import CircularProgress from '@mui/material/CircularProgress';
import { useQueryClient } from '@tanstack/react-query';
import { useSnackbar } from 'notistack';
import ppidApi, { PpidApiError } from '../api/ppidApi';
import { resourceKeys } from '../api/useResource';

/**
 * Alur status permohonan.
 *
 * Daftar ini menyalin aturan transisi milik API supaya pengguna hanya melihat
 * tujuan yang memang sah. Penolakan tetap terjadi di server bila daftar ini
 * ketinggalan zaman — jadi keduanya tidak boleh diandalkan sendirian.
 */
const TRANSISI: Record<string, string[]> = {
	diajukan: ['diverifikasi', 'ditolak', 'kedaluwarsa'],
	diverifikasi: ['diproses', 'ditolak', 'kedaluwarsa'],
	diproses: ['menunggu_approval', 'ditolak', 'kedaluwarsa'],
	menunggu_approval: ['disetujui', 'ditolak', 'ditolak_sebagian', 'diproses'],
	disetujui: ['selesai'],
	ditolak: ['selesai'],
	ditolak_sebagian: ['selesai'],
	selesai: [],
	kedaluwarsa: []
};

const LABEL_STATUS: Record<string, string> = {
	diajukan: 'Diajukan',
	diverifikasi: 'Diverifikasi',
	diproses: 'Diproses',
	menunggu_approval: 'Menunggu Persetujuan',
	disetujui: 'Disetujui',
	ditolak: 'Ditolak',
	ditolak_sebagian: 'Ditolak Sebagian',
	selesai: 'Selesai',
	kedaluwarsa: 'Kedaluwarsa'
};

type PermohonanStatusDialogProps = {
	open: boolean;
	onClose: () => void;
	permohonan: { id: number; kode_permohonan?: string; status?: string } | null;
};

export function PermohonanStatusDialog({ open, onClose, permohonan }: PermohonanStatusDialogProps) {
	const statusSekarang = permohonan?.status ?? '';
	const tujuan = TRANSISI[statusSekarang] ?? [];

	const [statusBaru, setStatusBaru] = useState('');
	const [catatan, setCatatan] = useState('');
	const [alasan, setAlasan] = useState('');
	const [menyimpan, setMenyimpan] = useState(false);

	const queryClient = useQueryClient();
	const { enqueueSnackbar } = useSnackbar();

	useEffect(() => {
		if (open) {
			setStatusBaru(tujuan[0] ?? '');
			setCatatan('');
			setAlasan('');
		}
		// `tujuan` diturunkan dari status permohonan, jadi cukup dipantau lewat itu.
	}, [open, statusSekarang]);

	const perluAlasan = statusBaru === 'ditolak' || statusBaru === 'ditolak_sebagian';

	async function kirim() {
		if (!permohonan || !statusBaru) {
			return;
		}

		setMenyimpan(true);

		try {
			await ppidApi.action(`permohonan/${permohonan.id}/status`, {
				status_baru: statusBaru,
				catatan: catatan || undefined,
				alasan_penolakan: perluAlasan ? alasan : undefined
			});

			await queryClient.invalidateQueries({ queryKey: resourceKeys.all('permohonan') });
			enqueueSnackbar(`Status menjadi ${LABEL_STATUS[statusBaru] ?? statusBaru}`, { variant: 'success' });
			onClose();
		} catch (error) {
			const pesan =
				error instanceof PpidApiError
					? (Object.values(error.errors)[0]?.[0] ?? error.message)
					: 'Status gagal diubah';
			enqueueSnackbar(pesan, { variant: 'error' });
		} finally {
			setMenyimpan(false);
		}
	}

	return (
		<Dialog
			open={open}
			onClose={menyimpan ? undefined : onClose}
			fullWidth
			maxWidth="sm"
		>
			<DialogTitle>Ubah status permohonan</DialogTitle>

			<DialogContent
				dividers
				className="flex flex-col gap-4"
			>
				<Alert severity="info">
					{permohonan?.kode_permohonan ?? '—'} — status sekarang:{' '}
					<strong>{LABEL_STATUS[statusSekarang] ?? statusSekarang}</strong>
				</Alert>

				{tujuan.length === 0 ? (
					<Alert severity="warning">
						Status ini sudah final. Tidak ada perpindahan status lanjutan yang diizinkan.
					</Alert>
				) : (
					<>
						<TextField
							select
							size="small"
							label="Status baru"
							value={statusBaru}
							onChange={(event) => setStatusBaru(event.target.value)}
							fullWidth
						>
							{tujuan.map((status) => (
								<MenuItem
									key={status}
									value={status}
								>
									{LABEL_STATUS[status] ?? status}
								</MenuItem>
							))}
						</TextField>

						{perluAlasan && (
							<TextField
								size="small"
								label="Alasan penolakan"
								required
								multiline
								minRows={3}
								value={alasan}
								onChange={(event) => setAlasan(event.target.value)}
								fullWidth
								helperText="Alasan ini disampaikan kepada pemohon."
							/>
						)}

						<TextField
							size="small"
							label="Catatan internal"
							multiline
							minRows={2}
							value={catatan}
							onChange={(event) => setCatatan(event.target.value)}
							fullWidth
							helperText="Tersimpan di riwayat status, tidak ditampilkan ke pemohon."
						/>
					</>
				)}
			</DialogContent>

			<DialogActions>
				<Button
					onClick={onClose}
					disabled={menyimpan}
				>
					Batal
				</Button>
				<Button
					variant="contained"
					color="secondary"
					disabled={menyimpan || tujuan.length === 0 || !statusBaru || (perluAlasan && !alasan.trim())}
					onClick={kirim}
					startIcon={menyimpan ? <CircularProgress size={16} /> : undefined}
				>
					Simpan status
				</Button>
			</DialogActions>
		</Dialog>
	);
}

export default PermohonanStatusDialog;
