import { useEffect, useMemo, useState } from 'react';
import Dialog from '@mui/material/Dialog';
import DialogTitle from '@mui/material/DialogTitle';
import DialogContent from '@mui/material/DialogContent';
import DialogActions from '@mui/material/DialogActions';
import Button from '@mui/material/Button';
import TextField from '@mui/material/TextField';
import Checkbox from '@mui/material/Checkbox';
import Typography from '@mui/material/Typography';
import Alert from '@mui/material/Alert';
import CircularProgress from '@mui/material/CircularProgress';
import { useTranslation } from 'react-i18next';
import FuseSvgIcon from '@fuse/core/FuseSvgIcon';
import { useResourceList } from '../api/useResource';
import { urlMedia } from './UploadField';
import { formatWaktu } from '../lib/waktu';

type BarisArsip = {
	id: number;
	nama?: string;
	kategori?: string | null;
	keterangan?: string | null;
	nama_file?: string | null;
	path_file?: string;
	created_at?: string | null;
};

type ArsipDokumenPickerProps = {
	open: boolean;
	onClose: () => void;
	/** Menerima berkas terpilih dalam bentuk yang diterima endpoint lampiran. */
	onPilih: (berkas: { nama_file: string; path_file: string }[]) => void | Promise<void>;
};

/**
 * Pemilih berkas dari Arsip Dokumen (langkah 95).
 *
 * Dipakai dialog rincian permohonan untuk melampirkan dokumen yang sudah ada
 * tanpa mengunggahnya lagi. Hanya arsip aktif yang ditawarkan: arsip yang
 * dinonaktifkan biasanya versi lama yang tidak boleh lagi dikirimkan.
 */
export function ArsipDokumenPicker({ open, onClose, onPilih }: ArsipDokumenPickerProps) {
	const { t } = useTranslation();
	const [cari, setCari] = useState('');
	const [cariTertunda, setCariTertunda] = useState('');
	const [terpilih, setTerpilih] = useState<Record<number, boolean>>({});

	// Pencarian ditunda 400 ms, sama seperti daftar modul.
	useEffect(() => {
		const timer = setTimeout(() => setCari(cariTertunda), 400);
		return () => clearTimeout(timer);
	}, [cariTertunda]);

	useEffect(() => {
		if (open) {
			setTerpilih({});
			setCari('');
			setCariTertunda('');
		}
	}, [open]);

	const { data, isLoading } = useResourceList<BarisArsip>(
		'arsip-dokumen',
		{ per_page: 20, sort: '-id', is_active: 'true', search: cari || undefined },
		open
	);

	const baris = useMemo(() => data?.data ?? [], [data]);
	const jumlahTerpilih = Object.values(terpilih).filter(Boolean).length;

	function kirim() {
		const dipilih = baris
			.filter((satu) => terpilih[satu.id])
			.map((satu) => ({
				nama_file: String(satu.nama_file || satu.nama || 'Berkas'),
				path_file: String(satu.path_file ?? '')
			}))
			.filter((satu) => satu.path_file !== '');

		void onPilih(dipilih);
	}

	return (
		<Dialog
			open={open}
			onClose={onClose}
			fullWidth
			maxWidth="sm"
			scroll="paper"
		>
			<DialogTitle>{t('Pilih dari Arsip Dokumen')}</DialogTitle>

			<DialogContent
				dividers
				className="flex flex-col gap-3"
			>
				<TextField
					size="small"
					fullWidth
					label={t('Cari dokumen')}
					value={cariTertunda}
					onChange={(event) => setCariTertunda(event.target.value)}
					placeholder={t('Nama, kategori, atau keterangan…')}
				/>

				{isLoading ? (
					<div className="flex justify-center py-8">
						<CircularProgress />
					</div>
				) : baris.length === 0 ? (
					<Alert severity="info">
						{t(
							'Arsip masih kosong. Berkas yang Anda unggah dari panel ini otomatis tercatat di sana, dan bisa juga ditambahkan lewat modul Arsip Dokumen.'
						)}
					</Alert>
				) : (
					<ul className="flex flex-col gap-2">
						{baris.map((satu) => (
							<li
								key={satu.id}
								className="border-divider flex items-start gap-2 rounded-lg border p-2"
							>
								<Checkbox
									size="small"
									checked={Boolean(terpilih[satu.id])}
									onChange={(event) =>
										setTerpilih((lama) => ({ ...lama, [satu.id]: event.target.checked }))
									}
								/>
								<div className="min-w-0 flex-auto">
									<Typography
										variant="body2"
										className="font-semibold"
									>
										{satu.nama || satu.nama_file || t('Berkas')}
									</Typography>
									<Typography
										variant="caption"
										color="text.secondary"
										component="div"
									>
										{[satu.kategori, formatWaktu(satu.created_at)].filter(Boolean).join(' · ')}
									</Typography>
									{satu.keterangan ? (
										<Typography
											variant="caption"
											color="text.secondary"
											component="div"
										>
											{satu.keterangan}
										</Typography>
									) : null}
								</div>
								<Button
									size="small"
									onClick={() =>
										window.open(urlMedia(String(satu.path_file ?? '')), '_blank', 'noopener')
									}
									startIcon={<FuseSvgIcon size={16}>lucide:external-link</FuseSvgIcon>}
								>
									{t('Buka')}
								</Button>
							</li>
						))}
					</ul>
				)}

				{data && data.meta.total > baris.length && (
					<Typography
						variant="caption"
						color="text.secondary"
					>
						{t('Menampilkan :tampil dari :total dokumen. Persempit dengan pencarian.')
							.replace(':tampil', String(baris.length))
							.replace(':total', String(data.meta.total))}
					</Typography>
				)}
			</DialogContent>

			<DialogActions>
				<Button onClick={onClose}>{t('Batal')}</Button>
				<Button
					variant="contained"
					color="secondary"
					disabled={jumlahTerpilih === 0}
					onClick={kirim}
				>
					{t('Lampirkan')} {jumlahTerpilih > 0 ? `(${jumlahTerpilih})` : ''}
				</Button>
			</DialogActions>
		</Dialog>
	);
}

export default ArsipDokumenPicker;
