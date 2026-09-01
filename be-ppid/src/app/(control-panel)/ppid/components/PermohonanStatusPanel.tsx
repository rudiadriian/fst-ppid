import { useEffect, useState } from 'react';
import Button from '@mui/material/Button';
import TextField from '@mui/material/TextField';
import MenuItem from '@mui/material/MenuItem';
import Alert from '@mui/material/Alert';
import CircularProgress from '@mui/material/CircularProgress';
import { useQueryClient } from '@tanstack/react-query';
import { useSnackbar } from 'notistack';
import { useTranslation } from 'react-i18next';
import ppidApi, { PpidApiError } from '../api/ppidApi';
import { resourceKeys } from '../api/useResource';
import { labelStatus, STATUS_PERMOHONAN, TRANSISI_PERMOHONAN as TRANSISI } from '../lib/statusPengajuan';

type PermohonanStatusPanelProps = {
	permohonanId: number;
	status: string;
	/** Role pengguna punya hak `Ubah` pada modul Permohonan. */
	bolehUbah: boolean;
	/** Ada tahap persetujuan yang sedang menunggu putusan. */
	alurBerjalan: boolean;
};

/**
 * Perpindahan status permohonan, di dalam dialog rinciannya.
 *
 * Dulu ini dialog tersendiri yang dibuka dari menu baris (langkah 94): petugas
 * memilih status dari tabel, tanpa melihat isi permohonan yang sedang ia
 * pindahkan. Sekarang ia berdiri di bawah rinciannya, satu layar dengan berkas
 * dan jenjang persetujuan yang jadi dasar keputusannya.
 *
 * Tujuan yang ditawarkan diambil dari {@see TRANSISI_PERMOHONAN}, salinan
 * aturan milik API supaya petugas hanya melihat perpindahan yang memang sah.
 * Penolakan tetap terjadi di server bila salinan itu ketinggalan zaman — jadi
 * keduanya tidak boleh diandalkan sendirian.
 *
 * Selama satu tahap persetujuan masih menunggu, panel ini terkunci penuh
 * (langkah 100). Sebelumnya hanya putusan akhir yang dijaga, sehingga PPID
 * Pelaksana yang sudah meneruskan berkasnya masih bisa menariknya kembali,
 * menolaknya sendiri, atau menyatakannya kedaluwarsa — tiga hal yang
 * seluruhnya melangkahi jenjang di atasnya. Perpindahan berkas yang sedang
 * berjalan adalah hasil putusan, bukan pilihan dropdown.
 *
 * Server menjaga aturan yang sama; keduanya tidak boleh diandalkan sendirian.
 */
export function PermohonanStatusPanel({ permohonanId, status, bolehUbah, alurBerjalan }: PermohonanStatusPanelProps) {
	const { t } = useTranslation();
	const tujuan = TRANSISI[status] ?? [];

	const [statusBaru, setStatusBaru] = useState('');
	const [catatan, setCatatan] = useState('');
	const [alasan, setAlasan] = useState('');
	const [menyimpan, setMenyimpan] = useState(false);

	const queryClient = useQueryClient();
	const { enqueueSnackbar } = useSnackbar();

	useEffect(() => {
		// Daftar tujuan dibaca ulang di dalam efek, bukan dari `tujuan` di atas:
		// nilainya diturunkan dari `status`, jadi memasukkannya ke daftar
		// kebergantungan hanya menambah acuan yang berubah tiap render.
		setStatusBaru((TRANSISI[status] ?? [])[0] ?? '');
		setCatatan('');
		setAlasan('');
	}, [permohonanId, status]);

	const perluAlasan = statusBaru === 'ditolak' || statusBaru === 'ditolak_sebagian';

	if (alurBerjalan) {
		return (
			<Alert severity="info">
				{t(
					'Permohonan ini sedang berada di jenjang persetujuan. Perpindahannya ditentukan putusan pada panel Persetujuan Berjenjang di atas — Setujui, Tolak, atau Kembalikan untuk diperbaiki — bukan dari sini.'
				)}
			</Alert>
		);
	}

	if (!bolehUbah) {
		return (
			<Alert severity="info">
				{t('Role Anda tidak punya hak mengubah status permohonan. Rinciannya tetap bisa dibaca.')}
			</Alert>
		);
	}

	async function kirim() {
		if (!statusBaru) {
			return;
		}

		setMenyimpan(true);

		try {
			await ppidApi.action(`permohonan/${permohonanId}/status`, {
				status_baru: statusBaru,
				catatan: catatan || undefined,
				alasan_penolakan: perluAlasan ? alasan : undefined
			});

			await Promise.all([
				queryClient.invalidateQueries({ queryKey: resourceKeys.all('permohonan') }),
				// Daftarnya gabungan dua kategori dan dilayani endpoint `pengajuan`
				// (langkah 89); tanpa ini tabelnya masih menunjukkan status lama.
				queryClient.invalidateQueries({ queryKey: resourceKeys.all('pengajuan') })
			]);

			setCatatan('');
			setAlasan('');
			enqueueSnackbar(`${t('Status menjadi')} ${labelStatus(STATUS_PERMOHONAN, statusBaru)}`, {
				variant: 'success'
			});
		} catch (error) {
			const pesan =
				error instanceof PpidApiError
					? (Object.values(error.errors)[0]?.[0] ?? error.message)
					: t('Status gagal diubah');
			enqueueSnackbar(pesan, { variant: 'error' });
		} finally {
			setMenyimpan(false);
		}
	}

	return (
		<div className="flex flex-col gap-3">
			{tujuan.length === 0 ? (
				<Alert severity="info">
					{t('Status ini sudah final. Tidak ada perpindahan status lanjutan yang diizinkan.')}
				</Alert>
			) : (
				<>
					<TextField
						select
						size="small"
						label={t('Status baru')}
						value={statusBaru}
						onChange={(event) => setStatusBaru(event.target.value)}
						fullWidth
					>
						{tujuan.map((nilai) => (
							<MenuItem
								key={nilai}
								value={nilai}
							>
								{t(labelStatus(STATUS_PERMOHONAN, nilai))}
							</MenuItem>
						))}
					</TextField>

					{perluAlasan && (
						<TextField
							size="small"
							label={t('Alasan penolakan')}
							required
							multiline
							minRows={3}
							value={alasan}
							onChange={(event) => setAlasan(event.target.value)}
							fullWidth
							helperText={t('Alasan ini disampaikan kepada pemohon.')}
						/>
					)}

					<TextField
						size="small"
						label={t('Catatan internal')}
						multiline
						minRows={2}
						value={catatan}
						onChange={(event) => setCatatan(event.target.value)}
						fullWidth
						helperText={t('Tersimpan di riwayat status, tidak ditampilkan ke pemohon.')}
					/>

					<div className="flex justify-end">
						<Button
							variant="contained"
							color="secondary"
							disabled={menyimpan || !statusBaru || (perluAlasan && !alasan.trim())}
							onClick={kirim}
							startIcon={menyimpan ? <CircularProgress size={16} /> : undefined}
						>
							{t('Simpan status')}
						</Button>
					</div>
				</>
			)}
		</div>
	);
}

export default PermohonanStatusPanel;
