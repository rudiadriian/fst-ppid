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
import { labelStatus, STATUS_KEBERATAN, TRANSISI_KEBERATAN } from '../lib/statusPengajuan';

type KeberatanTanggapanDialogProps = {
	open: boolean;
	onClose: () => void;
	keberatan: { id: number; kode_permohonan?: string; status?: string; tanggapan_atasan_ppid?: string } | null;
};

/**
 * Tanggapan atasan PPID atas satu keberatan.
 *
 * Menggantikan formulir ubah: isi keberatan (jenis, alasan, kasus posisi)
 * adalah pernyataan pemohon dan tidak boleh disunting petugas. Yang bisa
 * ditulis dari sini hanya status dan tanggapan — sama persis dengan yang
 * diterima endpoint `keberatan/{id}/tanggapan`.
 *
 * Tujuan status dibatasi ke {@see TRANSISI_KEBERATAN}: sejak alur persetujuan
 * berjenjang dipakai, `selesai` dan `ditolak` bukan lagi milik petugas begitu
 * berkasnya berada di `menunggu_approval` — putusannya milik penyetuju, dan
 * server menolak jalan pintasnya.
 *
 * Tanggapannya **dibaca pemohon** di portal, jadi tidak ada kolom catatan
 * internal di sini; tidak ada tempat menaruhnya yang tidak ikut terkirim.
 */
export function KeberatanTanggapanDialog({ open, onClose, keberatan }: KeberatanTanggapanDialogProps) {
	const statusSekarang = keberatan?.status ?? 'diajukan';
	const tujuan = TRANSISI_KEBERATAN[statusSekarang] ?? [];

	const [status, setStatus] = useState(statusSekarang);
	const [tanggapan, setTanggapan] = useState('');
	const [menyimpan, setMenyimpan] = useState(false);

	const queryClient = useQueryClient();
	const { enqueueSnackbar } = useSnackbar();

	useEffect(() => {
		if (open) {
			// Status sekarang selalu ikut sebagai pilihan: menyimpan ulang
			// tanpa memindahkan berkas adalah cara membetulkan tanggapan.
			setStatus(statusSekarang);
			setTanggapan(keberatan?.tanggapan_atasan_ppid ?? '');
		}
		// Isian direset tiap dialog dibuka untuk baris yang berbeda.
	}, [open, keberatan?.id, statusSekarang]);

	// Keberatan yang dinyatakan selesai wajib punya tanggapan: itu jawaban
	// resmi yang dibaca pemohon, dan tanpa isinya status "selesai" tidak
	// berarti apa-apa baginya.
	const perluTanggapan = status === 'selesai';
	const pilihan = [statusSekarang, ...tujuan];

	async function kirim() {
		if (!keberatan) {
			return;
		}

		setMenyimpan(true);

		try {
			await ppidApi.action(`keberatan/${keberatan.id}/tanggapan`, {
				status,
				tanggapan_atasan_ppid: tanggapan || undefined
			});

			await queryClient.invalidateQueries({ queryKey: resourceKeys.all('keberatan') });
			enqueueSnackbar(`Status menjadi ${labelStatus(STATUS_KEBERATAN, status)}`, { variant: 'success' });
			onClose();
		} catch (error) {
			const pesan =
				error instanceof PpidApiError
					? (Object.values(error.errors)[0]?.[0] ?? error.message)
					: 'Tanggapan gagal disimpan';
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
			<DialogTitle>Tanggapan &amp; status keberatan</DialogTitle>

			<DialogContent
				dividers
				className="flex flex-col gap-4"
			>
				<Alert severity="info">
					{keberatan?.kode_permohonan ?? '—'} — status sekarang:{' '}
					<strong>{labelStatus(STATUS_KEBERATAN, statusSekarang)}</strong>
				</Alert>

				{statusSekarang === 'menunggu_approval' && (
					<Alert severity="warning">
						Keberatan ini sedang menunggu putusan tahap persetujuan. Selesaikan tahapnya lewat menu Detail →
						Persetujuan Berjenjang.
					</Alert>
				)}

				{tujuan.length === 0 ? (
					<Alert severity="warning">
						Status ini sudah final. Tidak ada perpindahan status lanjutan yang diizinkan.
					</Alert>
				) : (
					<TextField
						select
						size="small"
						label="Status"
						value={status}
						onChange={(event) => setStatus(event.target.value)}
						fullWidth
					>
						{pilihan.map((nilai) => (
							<MenuItem
								key={nilai}
								value={nilai}
							>
								{labelStatus(STATUS_KEBERATAN, nilai)}
								{nilai === statusSekarang ? ' (tetap)' : ''}
							</MenuItem>
						))}
					</TextField>
				)}

				<TextField
					size="small"
					label="Tanggapan atasan PPID"
					required={perluTanggapan}
					multiline
					minRows={4}
					value={tanggapan}
					onChange={(event) => setTanggapan(event.target.value)}
					fullWidth
					helperText="Tanggapan ini dibaca pemohon di Portal Pemohon."
				/>
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
					disabled={menyimpan || (perluTanggapan && !tanggapan.trim())}
					onClick={kirim}
					startIcon={menyimpan ? <CircularProgress size={16} /> : undefined}
				>
					Simpan tanggapan
				</Button>
			</DialogActions>
		</Dialog>
	);
}

export default KeberatanTanggapanDialog;
