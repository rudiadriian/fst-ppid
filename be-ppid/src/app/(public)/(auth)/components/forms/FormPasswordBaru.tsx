import { useState } from 'react';
import { Controller, useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { useSearchParams } from 'react-router';
import TextField from '@mui/material/TextField';
import Button from '@mui/material/Button';
import Alert from '@mui/material/Alert';
import AlertTitle from '@mui/material/AlertTitle';
import Link from '@fuse/core/Link';
import KolomCaptcha from '@auth/services/jwt/components/KolomCaptcha';
import { authPasangPasswordBaru } from '@auth/authApi';
import { bacaGalat } from '@auth/services/jwt/utils/pesanGalat';

/*
 * Syaratnya disamakan persis dengan yang ditegakkan API
 * (`PasswordRule::min(10)->mixedCase()->letters()->numbers()`).
 *
 * Kalau di sini lebih longgar, orang baru tahu passwordnya ditolak setelah
 * mengirim; kalau lebih ketat, ada password sah yang tidak bisa dipasang sama
 * sekali. Keduanya tetap diperiksa server — yang di sini semata-mata supaya
 * jawabannya datang seketika.
 */
const schema = z
	.object({
		password: z
			.string()
			.min(10, 'Minimal 10 karakter')
			.regex(/[a-z]/, 'Harus memuat huruf kecil')
			.regex(/[A-Z]/, 'Harus memuat huruf besar')
			.regex(/[0-9]/, 'Harus memuat angka'),
		password_confirmation: z.string().nonempty('Ulangi password baru'),
		captcha: z.string().optional()
	})
	.refine((nilai) => nilai.password === nilai.password_confirmation, {
		message: 'Ulangan password tidak sama',
		path: ['password_confirmation']
	});

type FormType = z.infer<typeof schema>;

/**
 * Formulir pemasangan password baru.
 *
 * `token` dan `email` datang dari query string tautan email. Keduanya tidak
 * ditampilkan sebagai isian yang bisa disunting: yang mengetiknya sendiri pasti
 * salah, dan token yang salah hanya menghasilkan penolakan yang membingungkan.
 */
function FormPasswordBaru() {
	const [searchParams] = useSearchParams();
	const token = searchParams.get('token') ?? '';
	const email = searchParams.get('email') ?? '';

	const [captchaId, setCaptchaId] = useState<string | null>(null);
	const [captchaVersi, setCaptchaVersi] = useState(0);
	const [selesai, setSelesai] = useState<string | null>(null);
	const [spanduk, setSpanduk] = useState<{ pesan: string; jaringan: boolean } | null>(null);

	const { control, formState, handleSubmit, setError, resetField } = useForm<FormType>({
		mode: 'onChange',
		defaultValues: { password: '', password_confirmation: '', captcha: '' },
		resolver: zodResolver(schema)
	});

	const { isValid, isSubmitting, errors } = formState;

	async function onSubmit(formData: FormType) {
		setSpanduk(null);

		try {
			const hasil = await authPasangPasswordBaru({
				token,
				email,
				password: formData.password,
				password_confirmation: formData.password_confirmation,
				captcha: formData.captcha,
				captcha_id: captchaId
			});

			setSelesai(hasil.message);
		} catch (error) {
			const galat = await bacaGalat(error);

			galat.isian.forEach((item) => {
				if (item.type === 'password' || item.type === 'captcha') {
					setError(item.type, { type: 'manual', message: item.message });
				}
			});

			setSpanduk({ pesan: galat.ringkasan, jaringan: galat.jaringan });

			resetField('captcha');
			setCaptchaVersi((versi) => versi + 1);
		}
	}

	// Tautan yang dibuka tanpa token — mis. alamatnya diketik sendiri, atau
	// pemutus baris email memotong URL-nya.
	if (!token || !email) {
		return (
			<div className="flex w-full flex-col gap-6">
				<Alert severity="warning">
					<AlertTitle>Tautan tidak lengkap</AlertTitle>
					Buka halaman ini lewat tautan pada email atur ulang password. Bila tautannya terpotong, minta tautan
					baru.
				</Alert>

				<Button
					component={Link}
					to="/lupa-password"
					variant="contained"
					color="secondary"
					size="large"
				>
					Minta tautan baru
				</Button>
			</div>
		);
	}

	if (selesai) {
		return (
			<div className="flex w-full flex-col gap-6">
				<Alert severity="success">
					<AlertTitle>Password diperbarui</AlertTitle>
					{selesai}
				</Alert>

				<Button
					component={Link}
					to="/sign-in"
					variant="contained"
					color="secondary"
					size="large"
				>
					Masuk sekarang
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
					<AlertTitle>{spanduk.jaringan ? 'Server tidak dapat dihubungi' : 'Gagal menyimpan'}</AlertTitle>
					{spanduk.pesan}
				</Alert>
			)}

			<TextField
				className="mb-6"
				label="Email"
				value={email}
				variant="outlined"
				fullWidth
				disabled
			/>

			<Controller
				name="password"
				control={control}
				render={({ field }) => (
					<TextField
						{...field}
						className="mb-6"
						label="Password baru"
						type="password"
						autoFocus
						error={!!errors.password}
						helperText={
							errors?.password?.message ??
							'Minimal 10 karakter, memuat huruf besar, huruf kecil, dan angka.'
						}
						variant="outlined"
						required
						fullWidth
					/>
				)}
			/>

			<Controller
				name="password_confirmation"
				control={control}
				render={({ field }) => (
					<TextField
						{...field}
						className="mb-6"
						label="Ulangi password baru"
						type="password"
						error={!!errors.password_confirmation}
						helperText={errors?.password_confirmation?.message}
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
				{isSubmitting ? 'Menyimpan…' : 'Simpan password baru'}
			</Button>
		</form>
	);
}

export default FormPasswordBaru;
