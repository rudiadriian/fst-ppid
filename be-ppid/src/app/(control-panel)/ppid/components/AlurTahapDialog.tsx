import { useEffect, useState } from 'react';
import Dialog from '@mui/material/Dialog';
import DialogTitle from '@mui/material/DialogTitle';
import DialogContent from '@mui/material/DialogContent';
import DialogActions from '@mui/material/DialogActions';
import Alert from '@mui/material/Alert';
import Button from '@mui/material/Button';
import Checkbox from '@mui/material/Checkbox';
import CircularProgress from '@mui/material/CircularProgress';
import FormControlLabel from '@mui/material/FormControlLabel';
import IconButton from '@mui/material/IconButton';
import MenuItem from '@mui/material/MenuItem';
import TextField from '@mui/material/TextField';
import Tooltip from '@mui/material/Tooltip';
import Typography from '@mui/material/Typography';
import { useQuery, useQueryClient } from '@tanstack/react-query';
import { useSnackbar } from 'notistack';
import { useTranslation } from 'react-i18next';
import FuseSvgIcon from '@fuse/core/FuseSvgIcon';
import ppidApi, { PpidApiError } from '../api/ppidApi';
import { resourceKeys, useRelationOptions } from '../api/useResource';

type Tahap = {
	id: number | null;
	nama: string;
	role_id: number | null;
	struktur_id: number | null;
	sla_hari: number | null;
	boleh_tolak: boolean;
	keterangan: string;
	is_active: boolean;
};

type MuatanTahap = {
	alur: { id: number; jenis: string; nama: string; is_active: boolean };
	tahap: (Omit<Tahap, 'keterangan'> & { urutan: number; keterangan: string | null })[];
};

const TAHAP_KOSONG: Tahap = {
	id: null,
	nama: '',
	role_id: null,
	struktur_id: null,
	sla_hari: null,
	boleh_tolak: true,
	keterangan: '',
	is_active: true
};

type AlurTahapDialogProps = {
	open: boolean;
	onClose: () => void;
	alur: { id: number; nama: string; jenis: string } | null;
	/** Role pengguna punya hak `Ubah` pada modul Alur Persetujuan. */
	bolehUbah: boolean;
};

/**
 * Penyusun jenjang satu alur persetujuan.
 *
 * Urutannya ditentukan **posisi baris**, bukan angka yang diketik: susunan
 * yang terlihat di sini dan urutan yang dijalankan server tidak boleh bisa
 * berbeda. Karena itu tidak ada kolom "urutan" yang bisa diisi — yang ada
 * tombol naik/turun.
 *
 * Seluruh jenjang disimpan sekaligus dalam satu permintaan. Menyimpan tahap
 * satu per satu berarti alur bisa tersimpan setengah jadi, dan alur setengah
 * jadi adalah alur yang macet.
 */
export function AlurTahapDialog({ open, onClose, alur, bolehUbah }: AlurTahapDialogProps) {
	const { t } = useTranslation();
	const queryClient = useQueryClient();
	const { enqueueSnackbar } = useSnackbar();

	const [tahap, setTahap] = useState<Tahap[]>([]);
	const [menyimpan, setMenyimpan] = useState(false);

	const { data: opsiRole } = useRelationOptions('role', 'name', open);
	const { data: opsiStruktur } = useRelationOptions('struktur-organisasi', 'jabatan', open);

	const { data, isLoading } = useQuery<MuatanTahap>({
		queryKey: ['ppid', 'alur-approval', 'tahap', alur?.id ?? 0],
		queryFn: () => ppidApi.ambil<MuatanTahap>(`alur-approval/${alur?.id}/tahap`),
		enabled: open && Boolean(alur?.id)
	});

	useEffect(() => {
		if (!data) {
			return;
		}

		setTahap(
			data.tahap.map((baris) => ({
				id: baris.id,
				nama: baris.nama,
				role_id: baris.role_id,
				struktur_id: baris.struktur_id,
				sla_hari: baris.sla_hari,
				boleh_tolak: baris.boleh_tolak,
				keterangan: baris.keterangan ?? '',
				is_active: baris.is_active
			}))
		);
	}, [data]);

	function ubah(index: number, isi: Partial<Tahap>) {
		setTahap((lama) => lama.map((baris, i) => (i === index ? { ...baris, ...isi } : baris)));
	}

	function geser(index: number, arah: -1 | 1) {
		const tujuan = index + arah;

		if (tujuan < 0 || tujuan >= tahap.length) {
			return;
		}

		setTahap((lama) => {
			const salinan = [...lama];
			[salinan[index], salinan[tujuan]] = [salinan[tujuan], salinan[index]];
			return salinan;
		});
	}

	async function simpan() {
		if (!alur) {
			return;
		}

		setMenyimpan(true);

		try {
			await ppidApi.simpan(`alur-approval/${alur.id}/tahap`, {
				tahap: tahap.map((baris) => ({
					id: baris.id,
					nama: baris.nama,
					role_id: baris.role_id,
					struktur_id: baris.struktur_id,
					sla_hari: baris.sla_hari,
					boleh_tolak: baris.boleh_tolak,
					keterangan: baris.keterangan || null,
					is_active: baris.is_active
				}))
			});

			await Promise.all([
				queryClient.invalidateQueries({ queryKey: ['ppid', 'alur-approval', 'tahap', alur.id] }),
				queryClient.invalidateQueries({ queryKey: resourceKeys.all('alur-approval') })
			]);

			enqueueSnackbar(t('Jenjang persetujuan tersimpan'), { variant: 'success' });
			onClose();
		} catch (error) {
			const pesan =
				error instanceof PpidApiError
					? (Object.values(error.errors)[0]?.[0] ?? error.message)
					: t('Jenjang gagal disimpan');
			enqueueSnackbar(pesan, { variant: 'error' });
		} finally {
			setMenyimpan(false);
		}
	}

	const belumLengkap = tahap.some((baris) => !baris.nama.trim() || (baris.is_active && !baris.role_id));

	return (
		<Dialog
			open={open}
			onClose={menyimpan ? undefined : onClose}
			fullWidth
			maxWidth="md"
			scroll="paper"
		>
			<DialogTitle>
				{t('Atur tahap')} — {alur?.nama ?? ''}
			</DialogTitle>

			<DialogContent
				dividers
				className="flex flex-col gap-4"
			>
				{isLoading ? (
					<div className="flex justify-center py-10">
						<CircularProgress />
					</div>
				) : (
					<>
						<Alert severity="info">
							{t(
								'Berkas berjalan dari tahap teratas ke bawah. Satu penolakan menutup seluruh sisa jenjang; "Kembalikan untuk diperbaiki" memulangkan berkas ke petugas.'
							)}
						</Alert>

						{tahap.length === 0 && (
							<Alert severity="warning">
								{t(
									'Alur tanpa tahap tidak akan pernah berjalan: pengajuan yang masuk akan menunggu tanpa penyetuju.'
								)}
							</Alert>
						)}

						{tahap.map((baris, index) => (
							<div
								key={baris.id ?? `baru-${index}`}
								className="border-divider flex flex-col gap-3 rounded-lg border p-3"
							>
								<div className="flex items-center justify-between gap-2">
									<Typography
										variant="subtitle2"
										className="font-semibold"
									>
										{t('Tahap')} {index + 1}
									</Typography>

									<div className="flex items-center">
										<Tooltip title={t('Naikkan')}>
											<span>
												<IconButton
													size="small"
													disabled={!bolehUbah || index === 0}
													onClick={() => geser(index, -1)}
												>
													<FuseSvgIcon size={16}>lucide:arrow-up</FuseSvgIcon>
												</IconButton>
											</span>
										</Tooltip>
										<Tooltip title={t('Turunkan')}>
											<span>
												<IconButton
													size="small"
													disabled={!bolehUbah || index === tahap.length - 1}
													onClick={() => geser(index, 1)}
												>
													<FuseSvgIcon size={16}>lucide:arrow-down</FuseSvgIcon>
												</IconButton>
											</span>
										</Tooltip>
										<Tooltip title={t('Hapus tahap')}>
											<span>
												<IconButton
													size="small"
													disabled={!bolehUbah}
													onClick={() =>
														setTahap((lama) => lama.filter((_, i) => i !== index))
													}
												>
													<FuseSvgIcon
														size={16}
														color="error"
													>
														lucide:trash
													</FuseSvgIcon>
												</IconButton>
											</span>
										</Tooltip>
									</div>
								</div>

								<div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
									<TextField
										size="small"
										label={t('Nama tahap')}
										required
										value={baris.nama}
										disabled={!bolehUbah}
										onChange={(event) => ubah(index, { nama: event.target.value })}
									/>

									<TextField
										select
										size="small"
										label={t('Role penyetuju')}
										required
										value={baris.role_id ?? ''}
										disabled={!bolehUbah}
										onChange={(event) =>
											ubah(index, { role_id: Number(event.target.value) || null })
										}
										helperText={t('Hanya pemegang role ini yang bisa memutus tahapnya.')}
									>
										{(opsiRole ?? []).map((opsi) => (
											<MenuItem
												key={String(opsi.value)}
												value={String(opsi.value)}
											>
												{opsi.label}
											</MenuItem>
										))}
									</TextField>

									<TextField
										select
										size="small"
										label={t('Jabatan pada struktur organisasi')}
										value={baris.struktur_id ?? ''}
										disabled={!bolehUbah}
										onChange={(event) =>
											ubah(index, { struktur_id: Number(event.target.value) || null })
										}
										helperText={t('Nama jabatan yang ditampilkan pada jenjang persetujuan.')}
									>
										<MenuItem value="">{t('Tidak ditautkan')}</MenuItem>
										{(opsiStruktur ?? []).map((opsi) => (
											<MenuItem
												key={String(opsi.value)}
												value={String(opsi.value)}
											>
												{opsi.label}
											</MenuItem>
										))}
									</TextField>

									<TextField
										size="small"
										type="number"
										label={t('Batas waktu tahap (hari)')}
										value={baris.sla_hari ?? ''}
										disabled={!bolehUbah}
										onChange={(event) =>
											ubah(index, { sla_hari: Number(event.target.value) || null })
										}
										helperText={t('Kosongkan bila tahap ini tanpa batas waktu.')}
									/>

									<div className="sm:col-span-2">
										<TextField
											size="small"
											fullWidth
											label={t('Keterangan')}
											value={baris.keterangan}
											disabled={!bolehUbah}
											onChange={(event) => ubah(index, { keterangan: event.target.value })}
										/>
									</div>

									<FormControlLabel
										control={
											<Checkbox
												checked={baris.boleh_tolak}
												disabled={!bolehUbah}
												onChange={(event) => ubah(index, { boleh_tolak: event.target.checked })}
											/>
										}
										label={t('Boleh menolak pengajuan')}
									/>

									<FormControlLabel
										control={
											<Checkbox
												checked={baris.is_active}
												disabled={!bolehUbah}
												onChange={(event) => ubah(index, { is_active: event.target.checked })}
											/>
										}
										label={t('Tahap aktif')}
									/>
								</div>
							</div>
						))}

						{bolehUbah && (
							<Button
								variant="outlined"
								className="self-start"
								startIcon={<FuseSvgIcon size={18}>lucide:plus</FuseSvgIcon>}
								onClick={() => setTahap((lama) => [...lama, { ...TAHAP_KOSONG }])}
							>
								{t('Tambah tahap')}
							</Button>
						)}
					</>
				)}
			</DialogContent>

			<DialogActions>
				<Button
					onClick={onClose}
					disabled={menyimpan}
				>
					{bolehUbah ? t('Batal') : t('Tutup')}
				</Button>
				{bolehUbah && (
					<Button
						variant="contained"
						color="secondary"
						disabled={menyimpan || isLoading || belumLengkap}
						onClick={simpan}
						startIcon={menyimpan ? <CircularProgress size={16} /> : undefined}
					>
						{t('Simpan jenjang')}
					</Button>
				)}
			</DialogActions>
		</Dialog>
	);
}

export default AlurTahapDialog;
