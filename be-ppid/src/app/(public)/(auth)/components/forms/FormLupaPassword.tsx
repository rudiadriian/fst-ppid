import { useState } from 'react';
import { Controller, useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import TextField from '@mui/material/TextField';
import Button from '@mui/material/Button';
import Alert from '@mui/material/Alert';
import AlertTitle from '@mui/material/AlertTitle';
import Link from '@fuse/core/Link';
import KolomCaptcha from '@auth/services/jwt/components/KolomCaptcha';
import { authMintaResetPassword } from '@auth/authApi';
import { bacaGalat } from '@auth/services/jwt/utils/pesanGalat';

const schema = z.object({
	email: z.string().email('Format email tidak sah').nonempty('Email wajib diisi'),
	captcha: z.string().optional()
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
	const [captchaId, setCaptchaId] = useState<string | null>(null);
	const [captchaVersi, setCaptchaVersi] = useState(0);
	const [terkirim, setTerkirim] = useState<string | null>(null);
	const [spanduk, setSpanduk] = useState<{ pesan: string; jaringan: boolean } | null>(null);

	const { control, formState, handleSubmit, setError, resetField } = useForm<FormType>({
		mode: 'onChange',
		defaultValues: { email: '', captcha: '' },
		resolver: zodResolver(schema)
	});

	const { isValid, isSubmitting, errors } = formState;

	async function onSubmit(formData: FormType) {
		setSpanduk(null);

		try {
			const hasil = await authMintaResetPassword({
				email: formData.email,
				captcha: formData.captcha,
				captcha_id: captchaId
			});

			setTerkirim(hasil.message);
		} catch (error) {
			const galat = await bacaGalat(error);

			galat.isian.forEach((item) => {
				if (item.type === 'email' || item.type === 'captcha') {
					setError(item.type, { type: 'manual', message: item.message });
				}
			});

			setSpanduk({ pesan: galat.ringkasan, jaringan: galat.jaringan });

			// Kode captcha sudah dibuang server saat diperiksa.
			resetField('captcha');
			setCaptchaVersi((versi) => versi + 1);
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

			<Controller
				name="captcha"
				control={control}
				render={({ field }) => (
					<KolomCaptcha
						value={field.value ?? ''}
						onChange={field.onChange}
						onIdChange={setCaptchaId}
						error={!!errors.captcha}
						helperText={errors?.captcha?.message}
						muatUlang={captchaVersi}
						disabled={isSubmitting}
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
		</form>
	);
}

export default FormLupaPassword;
