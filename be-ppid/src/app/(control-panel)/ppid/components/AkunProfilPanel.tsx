import { useEffect, useState } from 'react';
import Alert from '@mui/material/Alert';
import Button from '@mui/material/Button';
import Chip from '@mui/material/Chip';
import TextField from '@mui/material/TextField';
import Typography from '@mui/material/Typography';
import { useSnackbar } from 'notistack';
import { useTranslation } from 'react-i18next';
import FuseSvgIcon from '@fuse/core/FuseSvgIcon';
import { PpidApiError } from '../api/ppidApi';
import { AkunProfil, useSimpanProfil } from '../api/useAkun';
import { formatWaktu } from '../lib/waktu';
import UploadField from './UploadField';

/**
 * Tab **Profil** pada halaman Akun Saya.
 *
 * Yang bisa disunting sendiri hanya nama tampilan, nomor telepon, dan foto.
 * Email dan role sengaja ditampilkan sebagai keterangan, bukan isian: email
 * adalah identitas masuk sekaligus alamat pemberitahuan keamanan, dan role
 * menentukan hak akses — keduanya urusan administrator lewat modul Pengguna.
 * API pun tidak menerima keduanya di endpoint ini, jadi ini bukan sekadar
 * isian yang disembunyikan.
 */

type AkunProfilPanelProps = {
	profil: AkunProfil;
};

export function AkunProfilPanel({ profil }: AkunProfilPanelProps) {
	const { t } = useTranslation();
	const { enqueueSnackbar } = useSnackbar();
	const simpan = useSimpanProfil();

	const [nama, setNama] = useState(profil.name);
	const [telepon, setTelepon] = useState(profil.phone ?? '');
	const [foto, setFoto] = useState<string | null>(profil.photo_url);
	const [galat, setGalat] = useState<Record<string, string>>({});

	// Nilai server yang baru (mis. setelah invalidasi) menggantikan isian yang
	// belum disentuh; tanpa ini formulir memegang salinan basi.
	useEffect(() => {
		setNama(profil.name);
		setTelepon(profil.phone ?? '');
		setFoto(profil.photo_url);
	}, [profil.name, profil.phone, profil.photo_url]);

	const berubah = nama !== profil.name || telepon !== (profil.phone ?? '') || foto !== profil.photo_url;

	async function kirim(event: React.FormEvent) {
		event.preventDefault();
		setGalat({});

		try {
			await simpan.mutateAsync({
				name: nama,
				phone: telepon === '' ? null : telepon,
				photo_url: foto
			});

			enqueueSnackbar(t('Profil tersimpan.'), { variant: 'success' });
		} catch (err) {
			if (err instanceof PpidApiError) {
				setGalat(
					Object.entries(err.errors).reduce<Record<string, string>>((hasil, [kunci, pesan]) => {
						hasil[kunci] = pesan[0];
						return hasil;
					}, {})
				);
				enqueueSnackbar(err.message, { variant: 'error' });
			} else {
				enqueueSnackbar(t('Profil gagal disimpan.'), { variant: 'error' });
			}
		}
	}

	return (
		<form
			onSubmit={kirim}
			className="flex flex-col gap-4"
		>
			<div className="grid grid-cols-1 gap-4 md:grid-cols-2">
				<TextField
					label={t('Nama lengkap')}
					value={nama}
					onChange={(event) => setNama(event.target.value)}
					error={Boolean(galat.name)}
					helperText={galat.name}
					required
					fullWidth
				/>

				<TextField
					label={t('Nomor telepon')}
					value={telepon}
					onChange={(event) => setTelepon(event.target.value)}
					error={Boolean(galat.phone)}
					helperText={galat.phone}
					fullWidth
				/>
			</div>

			<UploadField
				field={{
					name: 'photo_url',
					label: 'Foto profil',
					type: 'image',
					upload: { folder: 'pengguna', jenis: 'gambar' },
					help: 'Opsional. Dipakai sebagai avatar di menu pengguna.'
				}}
				value={foto}
				onChange={setFoto}
				errorText={galat.photo_url}
			/>

			<div className="border-divider flex flex-col gap-3 rounded-lg border p-4">
				<Typography className="font-semibold">{t('Keterangan akun')}</Typography>

				<div className="grid grid-cols-1 gap-3 md:grid-cols-2">
					<Keterangan
						label={t('Email')}
						nilai={profil.email}
						catatan={t('Diubah administrator lewat modul Pengguna.')}
					/>
					<Keterangan
						label={t('Role')}
						nilai={profil.role?.name ?? '—'}
						catatan={profil.role?.description ?? undefined}
					/>
					<Keterangan
						label={t('Jabatan pada struktur')}
						nilai={profil.struktur?.jabatan ?? '—'}
						catatan={profil.struktur?.nama ?? undefined}
					/>
					<Keterangan
						label={t('Login terakhir')}
						nilai={formatWaktu(profil.last_login_at)}
					/>
					<Keterangan
						label={t('Akun dibuat')}
						nilai={formatWaktu(profil.created_at)}
					/>
					<div className="flex flex-col gap-1">
						<Typography
							variant="caption"
							color="text.secondary"
						>
							{t('Status')}
						</Typography>
						<div>
							<Chip
								size="small"
								label={profil.is_active ? t('Aktif') : t('Nonaktif')}
								color={profil.is_active ? 'success' : 'default'}
								variant="outlined"
							/>
						</div>
					</div>
				</div>
			</div>

			<Alert severity="info">
				{t('Nama dan foto pada menu pengguna di pojok panel ikut berganti setelah Anda memuat ulang halaman.')}
			</Alert>

			<div className="flex justify-end">
				<Button
					type="submit"
					variant="contained"
					color="secondary"
					disabled={!berubah || simpan.isPending}
					startIcon={<FuseSvgIcon size={18}>lucide:save</FuseSvgIcon>}
				>
					{simpan.isPending ? t('Menyimpan…') : t('Simpan Profil')}
				</Button>
			</div>
		</form>
	);
}

function Keterangan({ label, nilai, catatan }: { label: string; nilai: string; catatan?: string }) {
	return (
		<div className="flex flex-col gap-0.5">
			<Typography
				variant="caption"
				color="text.secondary"
			>
				{label}
			</Typography>
			<Typography className="font-medium break-words">{nilai}</Typography>
			{catatan && (
				<Typography
					variant="caption"
					color="text.secondary"
				>
					{catatan}
				</Typography>
			)}
		</div>
	);
}

export default AkunProfilPanel;
