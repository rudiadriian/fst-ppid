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
import { labelStatus, STATUS_KEBERATAN, TRANSISI_KEBERATAN } from '../lib/statusPengajuan';

type KeberatanTanggapanPanelProps = {
	keberatanId: number;
	status: string;
	tanggapanAwal?: string;
	/** Role pengguna punya hak `Ubah` pada modul Keberatan. */
	bolehUbah: boolean;
	/** Ada tahap persetujuan yang sedang menunggu putusan. */
	alurBerjalan: boolean;
};

/**
 * Tanggapan atasan PPID atas satu keberatan, di dalam dialog rinciannya.
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
export function KeberatanTanggapanPanel({
	keberatanId,
	status: statusSekarang,
	tanggapanAwal,
	bolehUbah,
	alurBerjalan
}: KeberatanTanggapanPanelProps) {
	const { t } = useTranslation();
	const tujuan = TRANSISI_KEBERATAN[statusSekarang] ?? [];

	const [status, setStatus] = useState(statusSekarang);
	const [tanggapan, setTanggapan] = useState(tanggapanAwal ?? '');
	const [menyimpan, setMenyimpan] = useState(false);

	const queryClient = useQueryClient();
	const { enqueueSnackbar } = useSnackbar();

	useEffect(() => {
		// Status sekarang selalu ikut sebagai pilihan: menyimpan ulang tanpa
		// memindahkan berkas adalah cara membetulkan tanggapan.
		setStatus(statusSekarang);
		setTanggapan(tanggapanAwal ?? '');
	}, [keberatanId, statusSekarang, tanggapanAwal]);

	// Keberatan yang dinyatakan selesai wajib punya tanggapan: itu jawaban
	// resmi yang dibaca pemohon, dan tanpa isinya status "selesai" tidak
	// berarti apa-apa baginya.
	const perluTanggapan = status === 'selesai';
	const pilihan = [statusSekarang, ...tujuan];

	// Terkunci penuh selama jenjang berjalan (langkah 100): perpindahan
	// berkasnya hasil putusan, bukan pilihan dropdown.
	if (alurBerjalan) {
		return (
			<Alert severity="info">
				{t(
					'Keberatan ini sedang berada di jenjang persetujuan. Perpindahannya ditentukan putusan pada panel Persetujuan Berjenjang di atas — Setujui, Tolak, atau Kembalikan untuk diperbaiki — bukan dari sini.'
				)}
			</Alert>
		);
	}

	if (!bolehUbah) {
		return (
			<Alert severity="info">
				{t('Role Anda tidak punya hak menulis tanggapan keberatan. Rinciannya tetap bisa dibaca.')}
			</Alert>
		);
	}

	async function kirim() {
		setMenyimpan(true);

		try {
			await ppidApi.action(`keberatan/${keberatanId}/tanggapan`, {
				status,
				tanggapan_atasan_ppid: tanggapan || undefined
			});

			await Promise.all([
				queryClient.invalidateQueries({ queryKey: resourceKeys.all('keberatan') }),
				// Daftar panelnya gabungan permohonan + keberatan lewat endpoint
				// `pengajuan` (langkah 89), bukan `keberatan`.
				queryClient.invalidateQueries({ queryKey: resourceKeys.all('pengajuan') })
			]);

			enqueueSnackbar(`${t('Status menjadi')} ${labelStatus(STATUS_KEBERATAN, status)}`, { variant: 'success' });
		} catch (error) {
			const pesan =
				error instanceof PpidApiError
					? (Object.values(error.errors)[0]?.[0] ?? error.message)
					: t('Tanggapan gagal disimpan');
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
				<TextField
					select
					size="small"
					label={t('Status')}
					value={status}
					onChange={(event) => setStatus(event.target.value)}
					fullWidth
				>
					{pilihan.map((nilai) => (
						<MenuItem
							key={nilai}
							value={nilai}
						>
							{t(labelStatus(STATUS_KEBERATAN, nilai))}
							{nilai === statusSekarang ? ` ${t('(tetap)')}` : ''}
						</MenuItem>
					))}
				</TextField>
			)}

			<TextField
				size="small"
				label={t('Tanggapan atasan PPID')}
				required={perluTanggapan}
				multiline
				minRows={4}
				value={tanggapan}
				onChange={(event) => setTanggapan(event.target.value)}
				fullWidth
				helperText={t('Tanggapan ini dibaca pemohon di Portal Pemohon.')}
			/>

			<div className="flex justify-end">
				<Button
					variant="contained"
					color="secondary"
					disabled={menyimpan || (perluTanggapan && !tanggapan.trim())}
					onClick={kirim}
					startIcon={menyimpan ? <CircularProgress size={16} /> : undefined}
				>
					{t('Simpan tanggapan')}
				</Button>
			</div>
		</div>
	);
}

export default KeberatanTanggapanPanel;
