import { useEffect, useState } from 'react';
import Dialog from '@mui/material/Dialog';
import DialogTitle from '@mui/material/DialogTitle';
import DialogContent from '@mui/material/DialogContent';
import DialogActions from '@mui/material/DialogActions';
import Button from '@mui/material/Button';
import TextField from '@mui/material/TextField';
import MenuItem from '@mui/material/MenuItem';
import Alert from '@mui/material/Alert';
import Chip from '@mui/material/Chip';
import Divider from '@mui/material/Divider';
import Typography from '@mui/material/Typography';
import CircularProgress from '@mui/material/CircularProgress';
import { useQueryClient } from '@tanstack/react-query';
import { useSnackbar } from 'notistack';
import { useTranslation } from 'react-i18next';
import FuseSvgIcon from '@fuse/core/FuseSvgIcon';
import ppidApi, { PpidApiError } from '../api/ppidApi';
import { resourceKeys, useResourceItem } from '../api/useResource';
import { formatWaktu } from '../lib/waktu';

/** Sesuai `Pemohon::BATAS_DITOLAK` di API. */
const BATAS_DITOLAK = 3;

const LABEL_STATUS: Record<string, string> = {
	belum: 'Belum Diverifikasi',
	menunggu: 'Menunggu Pemeriksaan',
	terverifikasi: 'Terverifikasi',
	ditolak: 'Ditolak'
};

const WARNA_STATUS: Record<string, 'default' | 'warning' | 'success' | 'error'> = {
	belum: 'default',
	menunggu: 'warning',
	terverifikasi: 'success',
	ditolak: 'error'
};

const LABEL_JENIS: Record<string, string> = {
	perorangan: 'Perorangan',
	mahasiswa: 'Mahasiswa',
	lembaga: 'Lembaga / Organisasi / Perusahaan',
	kelompok: 'Kelompok Orang'
};

const waktu = formatWaktu;

function Baris({ label, nilai }: { label: string; nilai: unknown }) {
	const { t } = useTranslation();
	const teks = nilai === null || nilai === undefined || nilai === '' ? '—' : String(nilai);

	return (
		<div>
			<Typography
				variant="caption"
				color="text.secondary"
				className="block"
			>
				{t(label)}
			</Typography>
			<Typography variant="body2">{teks}</Typography>
		</div>
	);
}

/** Pratinjau KTP: diambil sebagai blob karena endpointnya menuntut token. */
function BerkasKtp({ pemohonId, ada }: { pemohonId: number; ada: boolean }) {
	const { t } = useTranslation();
	const [url, setUrl] = useState<string | null>(null);
	const [tipe, setTipe] = useState('');
	const [galat, setGalat] = useState('');
	const [memuat, setMemuat] = useState(false);

	useEffect(() => {
		if (!ada) {
			return undefined;
		}

		let objectUrl = '';
		let dibatalkan = false;

		setMemuat(true);
		setGalat('');

		ppidApi
			.berkas(`pemohon/${pemohonId}/berkas-ktp`)
			.then((hasil) => {
				if (dibatalkan) {
					URL.revokeObjectURL(hasil.objectUrl);
					return;
				}

				objectUrl = hasil.objectUrl;
				setUrl(hasil.objectUrl);
				setTipe(hasil.tipe);
			})
			.catch((error: unknown) => {
				if (!dibatalkan) {
					setGalat(error instanceof PpidApiError ? error.message : 'Berkas KTP gagal dimuat.');
				}
			})
			.finally(() => {
				if (!dibatalkan) {
					setMemuat(false);
				}
			});

		// Object URL menahan blob-nya di memori sampai dilepas.
		return () => {
			dibatalkan = true;

			if (objectUrl) {
				URL.revokeObjectURL(objectUrl);
			}
		};
	}, [pemohonId, ada]);

	if (!ada) {
		return <Alert severity="warning">{t('Pemohon belum mengunggah berkas KTP.')}</Alert>;
	}

	if (memuat) {
		return (
			<div className="flex justify-center py-6">
				<CircularProgress size={24} />
			</div>
		);
	}

	if (galat) {
		return <Alert severity="error">{galat}</Alert>;
	}

	if (!url) {
		return null;
	}

	return (
		<div className="flex flex-col gap-2">
			{tipe.startsWith('image/') ? (
				<img
					src={url}
					alt={t('Berkas KTP pemohon')}
					className="max-h-96 w-full rounded-lg border border-divider object-contain"
				/>
			) : (
				<iframe
					src={url}
					title={t('Berkas KTP pemohon')}
					className="h-96 w-full rounded-lg border border-divider"
				/>
			)}

			<Button
				size="small"
				href={url}
				target="_blank"
				rel="noreferrer noopener"
				startIcon={<FuseSvgIcon size={16}>lucide:external-link</FuseSvgIcon>}
				className="self-start"
			>
				{t('Buka di tab baru')}
			</Button>
		</div>
	);
}

type PemohonDetailDialogProps = {
	open: boolean;
	onClose: () => void;
	pemohonId: number | null;
	/** Petugas punya hak `Setujui` pada modul permohonan. */
	bolehVerifikasi: boolean;
};

/**
 * Detail lengkap satu pemohon, sekaligus tempat memutuskan verifikasinya.
 *
 * Digabung dalam satu layar dengan sengaja: memutuskan tanpa melihat KTP dan
 * isian identitasnya berdampingan berarti menebak. NIK hanya muncul di sini —
 * daftar modul tetap tidak menampilkannya.
 */
export function PemohonDetailDialog({ open, onClose, pemohonId, bolehVerifikasi }: PemohonDetailDialogProps) {
	const { t } = useTranslation();
	const { data: pemohon, isLoading } = useResourceItem<Record<string, unknown>>(
		'pemohon',
		open && pemohonId ? pemohonId : undefined
	);

	const [status, setStatus] = useState('terverifikasi');
	const [catatan, setCatatan] = useState('');
	const [menyimpan, setMenyimpan] = useState(false);

	const queryClient = useQueryClient();
	const { enqueueSnackbar } = useSnackbar();

	useEffect(() => {
		if (open) {
			setStatus('terverifikasi');
			setCatatan('');
		}
	}, [open, pemohonId]);

	const statusSekarang = String(pemohon?.status_verifikasi ?? 'belum');
	const ditolakSekarang = Number(pemohon?.jumlah_ditolak ?? 0);
	const sisaKesempatan = Math.max(0, BATAS_DITOLAK - ditolakSekarang);
	const sudahDiblokir = sisaKesempatan === 0;
	const sudahTerverifikasi = statusSekarang === 'terverifikasi';
	const menolak = status === 'ditolak';

	async function kirim() {
		if (!pemohonId) {
			return;
		}

		setMenyimpan(true);

		try {
			await ppidApi.action(`pemohon/${pemohonId}/verifikasi`, {
				status,
				catatan: menolak ? catatan : undefined
			});

			await queryClient.invalidateQueries({ queryKey: resourceKeys.all('pemohon') });
			enqueueSnackbar(
				menolak ? t('Data pemohon ditolak.') : t('Data pemohon dinyatakan terverifikasi.'),
				{ variant: menolak ? 'warning' : 'success' }
			);
			onClose();
		} catch (error) {
			const pesan =
				error instanceof PpidApiError
					? (Object.values(error.errors)[0]?.[0] ?? error.message)
					: t('Verifikasi gagal disimpan');
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
			maxWidth="md"
			scroll="paper"
		>
			<DialogTitle>{t('Detail & Verifikasi Pemohon')}</DialogTitle>

			<DialogContent
				dividers
				className="flex flex-col gap-5"
			>
				{isLoading || !pemohon ? (
					<div className="flex justify-center py-10">
						<CircularProgress />
					</div>
				) : (
					<>
						<div className="flex flex-wrap items-center gap-3">
							<Typography
								variant="h6"
								className="font-semibold"
							>
								{String(pemohon.nama ?? '—')}
							</Typography>
							<Chip
								size="small"
								label={t(LABEL_STATUS[statusSekarang] ?? statusSekarang)}
								color={WARNA_STATUS[statusSekarang] ?? 'default'}
							/>
							{ditolakSekarang > 0 && (
								<Chip
									size="small"
									variant="outlined"
									color={sudahDiblokir ? 'error' : 'warning'}
									label={`${t('Ditolak')} ${ditolakSekarang}/${BATAS_DITOLAK}`}
								/>
							)}
						</div>

						<div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
							<Baris
								label="Email"
								nilai={pemohon.email}
							/>
							<Baris
								label="No. HP"
								nilai={pemohon.no_hp}
							/>
							<Baris
								label="NIK"
								nilai={pemohon.nik}
							/>
							<Baris
								label="Jenis Pemohon"
								nilai={t(LABEL_JENIS[String(pemohon.jenis_pemohon ?? '')] ?? String(pemohon.jenis_pemohon ?? ''))}
							/>
							<Baris
								label="Pekerjaan"
								nilai={pemohon.pekerjaan}
							/>
							<Baris
								label="Lembaga"
								nilai={pemohon.nama_lembaga}
							/>
							<div className="sm:col-span-2">
								<Baris
									label="Alamat"
									nilai={pemohon.alamat}
								/>
							</div>
							<Baris
								label="Email terverifikasi"
								nilai={pemohon.email_verified_at ? waktu(pemohon.email_verified_at) : t('Belum')}
							/>
							<Baris
								label="Jumlah permohonan"
								nilai={pemohon.jumlah_permohonan}
							/>
						</div>

						<Divider />

						<div>
							<Typography
								variant="subtitle2"
								className="mb-2 font-semibold"
							>
								{t('Berkas KTP')}
							</Typography>
							<BerkasKtp
								pemohonId={Number(pemohonId)}
								ada={Boolean(pemohon.punya_berkas_ktp)}
							/>
						</div>

						<Divider />

						<div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
							<Baris
								label="Diperiksa oleh"
								nilai={(pemohon.verifikator as { name?: string } | null)?.name}
							/>
							<Baris
								label="Tanggal verifikasi"
								nilai={waktu(pemohon.tanggal_verifikasi)}
							/>
							<Baris
								label="Terdaftar sejak"
								nilai={waktu(pemohon.created_at)}
							/>
						</div>

						{Boolean(pemohon.catatan_verifikasi) && (
							<Alert severity="warning">
								{t('Catatan penolakan sebelumnya')}: {String(pemohon.catatan_verifikasi)}
							</Alert>
						)}

						{bolehVerifikasi && (
							<>
								<Divider />

								{sudahDiblokir ? (
									<Alert severity="error">
										{t('Pemohon ini sudah ditolak :batas kali dan tidak dapat mengirim berkas lagi.').replace(
											':batas',
											String(BATAS_DITOLAK)
										)}
									</Alert>
								) : (
									<Alert severity={ditolakSekarang > 0 ? 'warning' : 'info'}>
										{t('Sudah ditolak :n kali. Sisa kesempatan kirim ulang: :sisa.')
											.replace(':n', String(ditolakSekarang))
											.replace(':sisa', String(sisaKesempatan))}
									</Alert>
								)}

								<TextField
									select
									size="small"
									label={t('Keputusan')}
									value={status}
									onChange={(event) => setStatus(event.target.value)}
									fullWidth
								>
									<MenuItem value="terverifikasi">{t('Terverifikasi')}</MenuItem>
									<MenuItem
										value="ditolak"
										disabled={sudahDiblokir || sudahTerverifikasi}
									>
										{t('Ditolak')}
									</MenuItem>
								</TextField>

								{sudahTerverifikasi && (
									<Alert severity="info">
										{t('Data yang sudah terverifikasi tidak dapat ditolak dari sini.')}
									</Alert>
								)}

								{menolak && (
									<TextField
										size="small"
										label={t('Alasan penolakan')}
										required
										multiline
										minRows={3}
										value={catatan}
										onChange={(event) => setCatatan(event.target.value)}
										fullWidth
										helperText={t('Ditampilkan kepada pemohon agar ia tahu apa yang harus diperbaiki.')}
									/>
								)}
							</>
						)}
					</>
				)}
			</DialogContent>

			<DialogActions>
				<Button
					onClick={onClose}
					disabled={menyimpan}
				>
					{t('Tutup')}
				</Button>
				{bolehVerifikasi && (
					<Button
						variant="contained"
						color={menolak ? 'error' : 'secondary'}
						disabled={menyimpan || isLoading || (menolak && !catatan.trim())}
						onClick={kirim}
						startIcon={menyimpan ? <CircularProgress size={16} /> : undefined}
					>
						{menolak ? t('Tolak Data') : t('Setujui Data')}
					</Button>
				)}
			</DialogActions>
		</Dialog>
	);
}

export default PemohonDetailDialog;
