import { useState } from 'react';
import { useForm, Controller } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import _ from 'lodash';
import TextField from '@mui/material/TextField';
import FormControl from '@mui/material/FormControl';
import FormControlLabel from '@mui/material/FormControlLabel';
import Checkbox from '@mui/material/Checkbox';
import Alert from '@mui/material/Alert';
import AlertTitle from '@mui/material/AlertTitle';
import Link from '@fuse/core/Link';
import Button from '@mui/material/Button';
import useJwtAuth from '../useJwtAuth';
import { bacaGalat } from '../utils/pesanGalat';
import KolomCaptcha from './KolomCaptcha';

/**
 * Form Validation Schema
 */
const schema = z.object({
	email: z.string().email('Format email tidak sah').nonempty('Email wajib diisi'),
	password: z.string().nonempty('Kata sandi wajib diisi'),
	// Wajibnya ditegakkan server: saat captcha dimatikan di API, komponen
	// gambarnya tidak tampil sama sekali dan isian ini memang kosong.
	captcha: z.string().optional(),
	remember: z.boolean().optional()
});

type FormType = z.infer<typeof schema>;

const defaultValues: FormType = {
	email: '',
	password: '',
	captcha: '',
	remember: true
};

function JwtSignInForm() {
	const { signIn } = useJwtAuth();

	const [captchaId, setCaptchaId] = useState<string | null>(null);
	const [captchaVersi, setCaptchaVersi] = useState(0);
	/**
	 * Spanduk di atas formulir.
	 *
	 * Terpisah dari galat per isian karena tidak semua kegagalan punya isian
	 * yang bersangkutan: API mati, 502 dari proxy, atau akun terkunci bukan
	 * salah ketikan pada kotak mana pun.
	 */
	const [spanduk, setSpanduk] = useState<{ pesan: string; jaringan: boolean } | null>(null);

	const { control, formState, handleSubmit, setError, resetField } = useForm<FormType>({
		mode: 'onChange',
		defaultValues,
		resolver: zodResolver(schema)
	});

	const { isValid, isSubmitting, dirtyFields, errors } = formState;

	async function onSubmit(formData: FormType) {
		setSpanduk(null);

		try {
			await signIn({
				email: formData.email,
				password: formData.password,
				captcha: formData.captcha,
				captcha_id: captchaId
			});
		} catch (error) {
			const galat = await bacaGalat(error);

			galat.isian.forEach((item) => {
				if (item.type === 'email' || item.type === 'password' || item.type === 'captcha') {
					setError(item.type, { type: 'manual', message: item.message });
				}
			});

			// Selalu tampil, juga saat galatnya sudah menempel di isian: pesan
			// kunci bertingkat ("coba lagi setelah 1 jam") terlalu penting untuk
			// disembunyikan sebagai teks kecil di bawah kotak password.
			setSpanduk({ pesan: galat.ringkasan, jaringan: galat.jaringan });

			/*
			 * Server membuang kode captcha begitu diperiksa — benar atau salah.
			 * Tanpa memuat gambar baru di sini, percobaan berikutnya pasti
			 * ditolak dengan alasan captcha, dan orangnya akan mengira
			 * passwordnya yang salah.
			 */
			resetField('captcha');
			setCaptchaVersi((versi) => versi + 1);
		}
	}

	return (
		<form
			name="loginForm"
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
					<AlertTitle>{spanduk.jaringan ? 'Server tidak dapat dihubungi' : 'Gagal masuk'}</AlertTitle>
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
						label="Email"
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
				name="password"
				control={control}
				render={({ field }) => (
					<TextField
						{...field}
						className="mb-6"
						label="Kata sandi"
						type="password"
						error={!!errors.password}
						helperText={errors?.password?.message}
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

			<div className="flex flex-col items-center justify-center sm:flex-row sm:justify-between">
				<Controller
					name="remember"
					control={control}
					render={({ field }) => (
						<FormControl>
							<FormControlLabel
								label="Ingat saya"
								control={
									<Checkbox
										size="small"
										{...field}
										checked={!!field.value}
									/>
								}
							/>
						</FormControl>
					)}
				/>

				<Link
					className="text-md font-medium"
					to="/lupa-password"
				>
					Lupa password?
				</Link>
			</div>

			<Button
				variant="contained"
				color="secondary"
				className="mt-4 w-full"
				aria-label="Masuk"
				disabled={_.isEmpty(dirtyFields) || !isValid || isSubmitting}
				type="submit"
				size="large"
			>
				{isSubmitting ? 'Memeriksa…' : 'Masuk'}
			</Button>
		</form>
	);
}

export default JwtSignInForm;
