import { useEffect, useState } from 'react';
import { Controller, useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import TextField from '@mui/material/TextField';
import Button from '@mui/material/Button';
import Alert from '@mui/material/Alert';
import AlertTitle from '@mui/material/AlertTitle';
import Link from '@fuse/core/Link';
import { authMintaResetPassword } from '@auth/authApi';
import { bacaGalat } from '@auth/services/jwt/utils/pesanGalat';
import { AKSI_RECAPTCHA, ambilTokenRecaptcha, siapkanRecaptcha } from '@auth/services/jwt/utils/recaptcha';
import CatatanRecaptcha from '@auth/services/jwt/components/CatatanRecaptcha';

const schema = z.object({
	email: z.string().email('Format email tidak sah').nonempty('Email wajib diisi')
});

type FormType = z.infer<typeof schema>;

/**
 * Formulir permintaan tautan atur ulang password.
 *
 * Setelah berhasil, formulirnya diganti pesan — bukan dibiarkan terisi dengan
 * spanduk hijau di atasnya. Tombol yang masih bisa ditekan mengundang orang
 * menekannya lagi, dan permintaan kedua hanya akan ditolak pembatas jeda.
 */
function FormLupaPassword() {
	const [terkirim, setTerkirim] = useState<string | null>(null);
	const [spanduk, setSpanduk] = useState<{ pesan: string; jaringan: boolean } | null>(null);

	const { control, formState, handleSubmit, setError } = useForm<FormType>({
		mode: 'onChange',
		defaultValues: { email: '' },
		resolver: zodResolver(schema)
	});

	const { isValid, isSubmitting, errors } = formState;

	useEffect(() => {
		siapkanRecaptcha();
	}, []);

	async function onSubmit(formData: FormType) {
		setSpanduk(null);

		let token: string | undefined;

		try {
			token = await ambilTokenRecaptcha(AKSI_RECAPTCHA.lupaPassword);
		} catch {
			// Tanpa token, permintaannya pasti ditolak server dan tetap terhitung
			// oleh pembatas jeda kirim tautan. Lebih baik tidak dikirim.
			setSpanduk({
				pesan: 'Verifikasi keamanan tidak dapat dimuat. Periksa koneksi Anda lalu coba lagi.',
				jaringan: true
			});
			return;
		}

		try {
			const hasil = await authMintaResetPassword({
				email: formData.email,
				recaptcha_token: token
			});

			setTerkirim(hasil.message);
		} catch (error) {
			const galat = await bacaGalat(error);

			galat.isian.forEach((item) => {
				if (item.type === 'email') {
					setError(item.type, { type: 'manual', message: item.message });
				}
			});

			// Penolakan reCAPTCHA tidak punya isian untuk ditempeli, jadi
			// spanduk inilah satu-satunya tempat pesannya bisa terbaca.
			setSpanduk({ pesan: galat.ringkasan, jaringan: galat.jaringan });
		}
	}

	if (terkirim) {
		return (
			<div className="flex w-full flex-col gap-6">
				<Alert severity="success">
					<AlertTitle>Permintaan diterima</AlertTitle>
					{terkirim}
				</Alert>

				<Button
					component={Link}
					to="/sign-in"
					variant="outlined"
					size="large"
				>
					Kembali ke halaman Masuk
				</Button>
			</div>
		);
	}

	return (
		<form
			noValidate
			className="flex w-full flex-col justify-center"
			onSubmit={handleSubmit(onSubmit)}
		>
			{spanduk && (
				<Alert
					severity={spanduk.jaringan ? 'warning' : 'error'}
					className="mb-6"
					onClose={() => setSpanduk(null)}
				>
					<AlertTitle>{spanduk.jaringan ? 'Server tidak dapat dihubungi' : 'Permintaan ditolak'}</AlertTitle>
					{spanduk.pesan}
				</Alert>
			)}

			<Controller
				name="email"
				control={control}
				render={({ field }) => (
					<TextField
						{...field}
						className="mb-6"
						label="Email akun panel"
						autoFocus
						type="email"
						error={!!errors.email}
						helperText={errors?.email?.message}
						variant="outlined"
						required
						fullWidth
					/>
				)}
			/>

			<Button
				variant="contained"
				color="secondary"
				className="mt-2 w-full"
				disabled={!isValid || isSubmitting}
				type="submit"
				size="large"
			>
				{isSubmitting ? 'Mengirim…' : 'Kirim tautan'}
			</Button>

			<div className="mt-6 flex justify-center">
				<Link
					className="text-md font-medium"
					to="/sign-in"
				>
					Kembali ke halaman Masuk
				</Link>
			</div>

			<CatatanRecaptcha />
		</form>
	);
}

export default FormLupaPassword;
