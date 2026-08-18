import { useRef, useState } from 'react';
import Button from '@mui/material/Button';
import IconButton from '@mui/material/IconButton';
import Typography from '@mui/material/Typography';
import CircularProgress from '@mui/material/CircularProgress';
import Box from '@mui/material/Box';
import { useSnackbar } from 'notistack';
import { useTranslation } from 'react-i18next';
import FuseSvgIcon from '@fuse/core/FuseSvgIcon';
import ppidApi, { PpidApiError } from '../api/ppidApi';
import { FieldConfig } from '../lib/types';

/**
 * URL berkas media untuk pratinjau di panel admin.
 *
 * Nilai yang disimpan di DB adalah path relatif (mis. `uploads/berita/...`)
 * supaya situs publik bisa menyusun URL-nya sendiri. Panel admin berada di
 * origin berbeda, jadi di sini path itu diberi awalan base URL media.
 */
export const MEDIA_BASE_URL = (import.meta.env.VITE_MEDIA_BASE_URL as string) || 'http://localhost:8000/storage';

export function urlMedia(path?: string | null): string {
	if (!path) {
		return '';
	}

	if (/^https?:\/\//i.test(path)) {
		return path;
	}

	return `${MEDIA_BASE_URL.replace(/\/$/, '')}/${String(path).replace(/^\//, '')}`;
}

type UploadFieldProps = {
	field: FieldConfig;
	value: string | null;
	onChange: (path: string | null) => void;
	disabled?: boolean;
	errorText?: string;
};

/**
 * Unggah satu berkas. Nilai yang disimpan ke form adalah path dari API,
 * bukan objek File — jadi payload simpan tetap JSON biasa.
 */
export function UploadField({ field, value, onChange, disabled, errorText }: UploadFieldProps) {
	const inputRef = useRef<HTMLInputElement>(null);
	const { t } = useTranslation();
	const [mengunggah, setMengunggah] = useState(false);
	const { enqueueSnackbar } = useSnackbar();

	const upload = field.upload ?? { folder: 'umum', jenis: 'dokumen' as const };
	const gambar = field.type === 'image' || upload.jenis === 'gambar';

	const terimaEkstensi =
		upload.jenis === 'gambar'
			? 'image/jpeg,image/png,image/webp,image/gif'
			: upload.jenis === 'video'
				? 'video/mp4,video/webm'
				: upload.jenis === 'dokumen_gambar'
					? // Modul yang berkasnya dibaca langsung di halaman publik
						// (mis. Regulasi) hanya menerima PDF dan gambar.
						'.pdf,image/jpeg,image/png,image/webp'
					: '.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.csv,.txt';

	async function pilihBerkas(event: React.ChangeEvent<HTMLInputElement>) {
		const file = event.target.files?.[0];

		// Input direset agar berkas yang sama bisa dipilih ulang setelah dihapus.
		event.target.value = '';

		if (!file) {
			return;
		}

		setMengunggah(true);

		try {
			const hasil = await ppidApi.upload(file, upload.folder, upload.jenis);
			onChange(hasil.path);
			enqueueSnackbar('Berkas terunggah', { variant: 'success' });
		} catch (error) {
			const pesan =
				error instanceof PpidApiError
					? (error.errors.file?.[0] ?? error.message)
					: 'Berkas gagal diunggah';
			enqueueSnackbar(pesan, { variant: 'error' });
		} finally {
			setMengunggah(false);
		}
	}

	return (
		<Box>
			<Typography
				variant="caption"
				className="mb-1 block font-medium"
			>
				{t(field.label)}
				{field.required ? ' *' : ''}
			</Typography>

			{value ? (
				<div className="flex items-center gap-2 rounded border border-divider p-2">
					{gambar ? (
						<img
							src={urlMedia(value)}
							alt={field.label}
							className="h-16 w-16 rounded object-cover"
						/>
					) : (
						<FuseSvgIcon color="action">lucide:file-text</FuseSvgIcon>
					)}

					<a
						href={urlMedia(value)}
						target="_blank"
						rel="noreferrer noopener"
						className="min-w-0 flex-1 truncate text-sm underline"
					>
						{String(value).split('/').pop()}
					</a>

					<IconButton
						size="small"
						disabled={disabled}
						onClick={() => onChange(null)}
						aria-label={`${t('Hapus')} ${t(field.label)}`}
					>
						<FuseSvgIcon size={18}>lucide:x</FuseSvgIcon>
					</IconButton>
				</div>
			) : (
				<Button
					variant="outlined"
					size="small"
					disabled={disabled || mengunggah}
					onClick={() => inputRef.current?.click()}
					startIcon={
						mengunggah ? <CircularProgress size={16} /> : <FuseSvgIcon size={18}>lucide:upload</FuseSvgIcon>
					}
				>
					{mengunggah ? t('Mengunggah…') : t('Pilih berkas')}
				</Button>
			)}

			<input
				ref={inputRef}
				type="file"
				accept={terimaEkstensi}
				className="hidden"
				onChange={pilihBerkas}
			/>

			{(errorText || field.help) && (
				<Typography
					variant="caption"
					color={errorText ? 'error' : 'text.secondary'}
					className="mt-1 block"
				>
					{errorText || field.help}
				</Typography>
			)}
		</Box>
	);
}

export type LampiranBerkas = {
	nama_file?: string;
	path_file: string;
	ukuran_file?: number;
	tipe_file?: string;
};

type MultiUploadFieldProps = {
	field: FieldConfig;
	value: LampiranBerkas[];
	onChange: (files: LampiranBerkas[]) => void;
	disabled?: boolean;
};

/**
 * Unggah banyak berkas (lampiran informasi publik).
 */
export function MultiUploadField({ field, value, onChange, disabled }: MultiUploadFieldProps) {
	const inputRef = useRef<HTMLInputElement>(null);
	const { t } = useTranslation();
	const [mengunggah, setMengunggah] = useState(false);
	const { enqueueSnackbar } = useSnackbar();

	const upload = field.upload ?? { folder: 'umum', jenis: 'dokumen' as const };
	const daftar = Array.isArray(value) ? value : [];

	async function pilihBerkas(event: React.ChangeEvent<HTMLInputElement>) {
		const files = Array.from(event.target.files ?? []);
		event.target.value = '';

		if (files.length === 0) {
			return;
		}

		setMengunggah(true);

		try {
			const terunggah: LampiranBerkas[] = [];

			// Berurutan, bukan paralel: menjaga agar rate limit unggahan di API
			// tidak tersentuh saat pengguna memilih banyak berkas sekaligus.
			for (const file of files) {
				// eslint-disable-next-line no-await-in-loop
				const hasil = await ppidApi.upload(file, upload.folder, upload.jenis);
				terunggah.push({
					nama_file: hasil.nama_file,
					path_file: hasil.path,
					ukuran_file: hasil.ukuran_file,
					tipe_file: hasil.tipe_file
				});
			}

			onChange([...daftar, ...terunggah]);
			enqueueSnackbar(`${terunggah.length} berkas terunggah`, { variant: 'success' });
		} catch (error) {
			const pesan =
				error instanceof PpidApiError ? (error.errors.file?.[0] ?? error.message) : 'Berkas gagal diunggah';
			enqueueSnackbar(pesan, { variant: 'error' });
		} finally {
			setMengunggah(false);
		}
	}

	return (
		<Box>
			<Typography
				variant="caption"
				className="mb-1 block font-medium"
			>
				{t(field.label)}
			</Typography>

			<div className="mb-2 flex flex-col gap-1">
				{daftar.map((berkas, index) => (
					<div
						key={berkas.path_file}
						className="flex items-center gap-2 rounded border border-divider px-2 py-1"
					>
						<FuseSvgIcon
							size={18}
							color="action"
						>
							lucide:paperclip
						</FuseSvgIcon>
						<a
							href={urlMedia(berkas.path_file)}
							target="_blank"
							rel="noreferrer noopener"
							className="min-w-0 flex-1 truncate text-sm underline"
						>
							{berkas.nama_file || berkas.path_file.split('/').pop()}
						</a>
						<IconButton
							size="small"
							disabled={disabled}
							onClick={() => onChange(daftar.filter((_, i) => i !== index))}
							aria-label={t('Hapus lampiran')}
						>
							<FuseSvgIcon size={18}>lucide:x</FuseSvgIcon>
						</IconButton>
					</div>
				))}
			</div>

			<Button
				variant="outlined"
				size="small"
				disabled={disabled || mengunggah}
				onClick={() => inputRef.current?.click()}
				startIcon={
					mengunggah ? <CircularProgress size={16} /> : <FuseSvgIcon size={18}>lucide:upload</FuseSvgIcon>
				}
			>
				{mengunggah ? t('Mengunggah…') : t('Tambah lampiran')}
			</Button>

			<input
				ref={inputRef}
				type="file"
				multiple
				className="hidden"
				onChange={pilihBerkas}
			/>

			{field.help && (
				<Typography
					variant="caption"
					color="text.secondary"
					className="mt-1 block"
				>
					{field.help}
				</Typography>
			)}
		</Box>
	);
}

export default UploadField;
