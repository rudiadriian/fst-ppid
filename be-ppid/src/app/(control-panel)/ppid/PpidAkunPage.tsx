import { useState } from 'react';
import Alert from '@mui/material/Alert';
import Button from '@mui/material/Button';
import IconButton from '@mui/material/IconButton';
import InputAdornment from '@mui/material/InputAdornment';
import Paper from '@mui/material/Paper';
import TextField from '@mui/material/TextField';
import Typography from '@mui/material/Typography';
import { useSnackbar } from 'notistack';
import { useTranslation } from 'react-i18next';
import FuseSvgIcon from '@fuse/core/FuseSvgIcon';
import useUser from '@auth/useUser';
import ppidApi, { PpidApiError } from './api/ppidApi';

/**
 * Akun Saya: halaman petugas mengurus akunnya sendiri.
 *
 * Isinya baru satu hal — ubah password — tetapi sengaja berdiri sebagai halaman
 * akun, bukan dialog yang menempel di modul Pengguna. Modul Pengguna adalah
 * tempat administrator mengelola akun orang lain dan menuntut hak modul;
 * halaman ini terbuka untuk setiap petugas yang bisa masuk, berapa pun hak
 * modulnya, karena yang disentuh hanya akunnya sendiri.
 */

type IsianPassword = {
	password_lama: string;
	password: string;
	password_confirmation: string;
};

const KOSONG: IsianPassword = { password_lama: '', password: '', password_confirmation: '' };

const SYARAT_PASSWORD =
	'Minimal 12 karakter, memuat huruf besar dan kecil, angka, serta simbol. Syaratnya sama dengan saat akun dibuat administrator.';

function PpidAkunPage() {
	const { t } = useTranslation();
	const { enqueueSnackbar } = useSnackbar();
	const { data: user } = useUser();

	const [isian, setIsian] = useState<IsianPassword>(KOSONG);
	const [galat, setGalat] = useState<Record<string, string>>({});
	const [pesanUmum, setPesanUmum] = useState<string | null>(null);
	const [menyimpan, setMenyimpan] = useState(false);
	const [terlihat, setTerlihat] = useState(false);

	function ubah(kunci: keyof IsianPassword, nilai: string) {
		setIsian((lama) => ({ ...lama, [kunci]: nilai }));
		// Pesan galat isian yang sedang diperbaiki langsung dilepas; membiarkannya
		// membuat isian yang sudah dibetulkan tetap tampak salah.
		setGalat((lama) => {
			if (!lama[kunci]) {
				return lama;
			}

			const sisa = { ...lama };
			delete sisa[kunci];
			return sisa;
		});
	}

	const cocok =
		isian.password !== '' && isian.password_confirmation !== '' && isian.password === isian.password_confirmation;
	const siap = isian.password_lama !== '' && cocok && !menyimpan;

	async function kirim(event: React.FormEvent) {
		event.preventDefault();

		if (!siap) {
			return;
		}

		setMenyimpan(true);
		setGalat({});
		setPesanUmum(null);

		try {
			await ppidApi.action('auth/ubah-password', isian);

			setIsian(KOSONG);
			enqueueSnackbar(t('Password berhasil diubah.'), { variant: 'success' });
		} catch (err) {
			if (err instanceof PpidApiError) {
				const perField = Object.entries(err.errors).reduce<Record<string, string>>((hasil, [kunci, pesan]) => {
					hasil[kunci] = pesan[0];
					return hasil;
				}, {});

				setGalat(perField);
				// Galat yang tidak menempel pada isian mana pun — mis. 429 dari
				// rem laju — tetap harus terbaca, bukan hilang tanpa jejak.
				setPesanUmum(Object.keys(perField).length === 0 ? err.message : null);
			} else {
				setPesanUmum(t('Password gagal diubah. Coba lagi.'));
			}
		} finally {
			setMenyimpan(false);
		}
	}

	const tombolLihat = (
		<InputAdornment position="end">
			<IconButton
				edge="end"
				size="small"
				aria-label={t(terlihat ? 'Sembunyikan password' : 'Tampilkan password')}
				onClick={() => setTerlihat((lama) => !lama)}
			>
				<FuseSvgIcon size={18}>{terlihat ? 'lucide:eye-off' : 'lucide:eye'}</FuseSvgIcon>
			</IconButton>
		</InputAdornment>
	);

	return (
		<div className="flex w-full flex-col">
			<div className="flex flex-col gap-2 p-4 md:p-6">
				<Typography
					variant="h5"
					className="font-semibold"
				>
					{t('Akun Saya')}
				</Typography>
				<Typography
					variant="body2"
					color="text.secondary"
				>
					{user?.displayName} — {user?.email}
				</Typography>
			</div>

			<Paper
				elevation={0}
				component="form"
				onSubmit={kirim}
				className="border-divider mx-4 mb-6 flex max-w-2xl flex-col gap-4 rounded-lg border p-4 md:mx-6 md:p-6"
			>
				<div>
					<Typography className="font-semibold">{t('Ubah Password')}</Typography>
					<Typography
						variant="body2"
						color="text.secondary"
					>
						{t(SYARAT_PASSWORD)}
					</Typography>
				</div>

				{pesanUmum && <Alert severity="error">{pesanUmum}</Alert>}

				<TextField
					label={t('Password lama')}
					type={terlihat ? 'text' : 'password'}
					value={isian.password_lama}
					onChange={(event) => ubah('password_lama', event.target.value)}
					error={Boolean(galat.password_lama)}
					helperText={galat.password_lama}
					autoComplete="current-password"
					required
					fullWidth
					slotProps={{ input: { endAdornment: tombolLihat } }}
				/>

				<TextField
					label={t('Password baru')}
					type={terlihat ? 'text' : 'password'}
					value={isian.password}
					onChange={(event) => ubah('password', event.target.value)}
					error={Boolean(galat.password)}
					helperText={galat.password}
					autoComplete="new-password"
					required
					fullWidth
					slotProps={{ input: { endAdornment: tombolLihat } }}
				/>

				<TextField
					label={t('Ulangi password baru')}
					type={terlihat ? 'text' : 'password'}
					value={isian.password_confirmation}
					onChange={(event) => ubah('password_confirmation', event.target.value)}
					error={Boolean(galat.password_confirmation) || (isian.password_confirmation !== '' && !cocok)}
					helperText={
						galat.password_confirmation ??
						(isian.password_confirmation !== '' && !cocok ? t('Ulangan password tidak sama.') : undefined)
					}
					autoComplete="new-password"
					required
					fullWidth
					slotProps={{ input: { endAdornment: tombolLihat } }}
				/>

				<Alert severity="info">
					{t(
						'Setelah password diganti, sesi yang sedang berjalan tetap terbuka. Perangkat lain yang masih memegang token lama juga belum otomatis keluar — bila akun Anda diduga dipakai orang lain, minta administrator menonaktifkannya lebih dulu.'
					)}
				</Alert>

				<div className="flex justify-end">
					<Button
						type="submit"
						variant="contained"
						color="secondary"
						disabled={!siap}
						startIcon={<FuseSvgIcon size={18}>lucide:key-round</FuseSvgIcon>}
					>
						{menyimpan ? t('Menyimpan…') : t('Simpan Password Baru')}
					</Button>
				</div>
			</Paper>
		</div>
	);
}

export default PpidAkunPage;
