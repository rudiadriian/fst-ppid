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
import { labelStatus, STATUS_PERMOHONAN, TRANSISI_PERMOHONAN as TRANSISI } from '../lib/statusPengajuan';

type PermohonanStatusDialogProps = {
	open: boolean;
	onClose: () => void;
	permohonan: { id: number; kode_permohonan?: string; status?: string } | null;
};

/**
 * Perpindahan status permohonan.
 *
 * Tujuan yang ditawarkan diambil dari {@see TRANSISI_PERMOHONAN}, salinan
 * aturan milik API supaya pengguna hanya melihat perpindahan yang memang sah.
 * Penolakan tetap terjadi di server bila salinan itu ketinggalan zaman — jadi
 * keduanya tidak boleh diandalkan sendirian.
 *
 * Sejak persetujuan berjenjang dipakai, dari `menunggu_approval` dialog ini
 * tidak lagi bisa memasang putusan akhir: selama masih ada tahap yang
 * menunggu, `disetujui`/`ditolak` milik penyetuju dan server menolaknya di
 * sini. Yang tersisa dari dialog ini adalah menarik berkas kembali ke
 * `diproses`.
 */
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
			enqueueSnackbar(`Status menjadi ${labelStatus(STATUS_PERMOHONAN, statusBaru)}`, { variant: 'success' });
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
					<strong>{labelStatus(STATUS_PERMOHONAN, statusSekarang)}</strong>
				</Alert>

				{statusSekarang === 'menunggu_approval' && (
					<Alert severity="warning">
						Selama masih ada tahap persetujuan yang menunggu, putusan Disetujui/Ditolak hanya bisa dikirim
						penyetujunya lewat menu Detail → Persetujuan Berjenjang. Dari sini berkasnya hanya bisa ditarik
						kembali ke Diproses.
					</Alert>
				)}

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
									{labelStatus(STATUS_PERMOHONAN, status)}
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
