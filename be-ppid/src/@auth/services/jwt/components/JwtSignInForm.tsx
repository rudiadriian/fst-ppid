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
import { recaptchaAktif } from '../utils/recaptcha';
import KolomRecaptcha from './KolomRecaptcha';

/**
 * Form Validation Schema
 */
const schema = z.object({
	email: z.string().email('Format email tidak sah').nonempty('Email wajib diisi'),
	password: z.string().nonempty('Kata sandi wajib diisi'),
	remember: z.boolean().optional()
});

type FormType = z.infer<typeof schema>;

const defaultValues: FormType = {
	email: '',
	password: '',
	remember: true
};

function JwtSignInForm() {
	const { signIn } = useJwtAuth();

	/**
	 * Spanduk di atas formulir.
	 *
	 * Terpisah dari galat per isian karena tidak semua kegagalan punya isian
	 * yang bersangkutan: API mati, 502 dari proxy, atau akun terkunci bukan
	 * salah ketikan pada kotak mana pun.
	 */
	const [spanduk, setSpanduk] = useState<{ pesan: string; jaringan: boolean } | null>(null);
	const [tokenRecaptcha, setTokenRecaptcha] = useState<string | null>(null);
	const [recaptchaVersi, setRecaptchaVersi] = useState(0);

	const { control, formState, handleSubmit, setError } = useForm<FormType>({
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
				recaptcha_token: tokenRecaptcha ?? undefined
			});
		} catch (error) {
			const galat = await bacaGalat(error);

			galat.isian.forEach((item) => {
				if (item.type === 'email' || item.type === 'password') {
					setError(item.type, { type: 'manual', message: item.message });
				}
			});

			// Selalu tampil, juga saat galatnya sudah menempel di isian: pesan
			// kunci bertingkat ("coba lagi setelah 1 jam") terlalu penting untuk
			// disembunyikan sebagai teks kecil di bawah kotak password.
			setSpanduk({ pesan: galat.ringkasan, jaringan: galat.jaringan });

			/*
			 * Google membuang token begitu ditukar server — berhasil atau tidak.
			 * Tanpa mengosongkan kotaknya di sini, percobaan berikutnya pasti
			 * ditolak dengan alasan captcha, dan orangnya akan mengira
			 * passwordnya yang salah.
			 */
			setTokenRecaptcha(null);
			setRecaptchaVersi((versi) => versi + 1);
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

			<KolomRecaptcha
				onTokenChange={setTokenRecaptcha}
				muatUlang={recaptchaVersi}
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
				disabled={_.isEmpty(dirtyFields) || !isValid || isSubmitting || (recaptchaAktif() && !tokenRecaptcha)}
				type="submit"
				size="large"
			>
				{isSubmitting ? 'Memeriksa…' : 'Masuk'}
			</Button>
		</form>
	);
}

export default JwtSignInForm;
