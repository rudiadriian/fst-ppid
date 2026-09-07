import { useEffect, useState } from 'react';
import { Controller, useForm } from 'react-hook-form';
import { zodResolver } from '@hookform/resolvers/zod';
import { z } from 'zod';
import { useSearchParams } from 'react-router';
import TextField from '@mui/material/TextField';
import Button from '@mui/material/Button';
import Alert from '@mui/material/Alert';
import AlertTitle from '@mui/material/AlertTitle';
import Link from '@fuse/core/Link';
import { authPasangPasswordBaru } from '@auth/authApi';
import { bacaGalat } from '@auth/services/jwt/utils/pesanGalat';
import { AKSI_RECAPTCHA, ambilTokenRecaptcha, siapkanRecaptcha } from '@auth/services/jwt/utils/recaptcha';
import CatatanRecaptcha from '@auth/services/jwt/components/CatatanRecaptcha';

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
		password_confirmation: z.string().nonempty('Ulangi password baru')
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

	const [selesai, setSelesai] = useState<string | null>(null);
	const [spanduk, setSpanduk] = useState<{ pesan: string; jaringan: boolean } | null>(null);

	const { control, formState, handleSubmit, setError } = useForm<FormType>({
		mode: 'onChange',
		defaultValues: { password: '', password_confirmation: '' },
		resolver: zodResolver(schema)
	});

	const { isValid, isSubmitting, errors } = formState;

	useEffect(() => {
		siapkanRecaptcha();
	}, []);

	async function onSubmit(formData: FormType) {
		setSpanduk(null);

		let recaptchaToken: string | undefined;

		try {
			recaptchaToken = await ambilTokenRecaptcha(AKSI_RECAPTCHA.passwordBaru);
		} catch {
			// Token reset hanya sekali pakai dan berumur pendek. Mengirim
			// permintaan yang sudah pasti ditolak reCAPTCHA membuang tautannya
			// dan memaksa orangnya meminta email baru.
			setSpanduk({
				pesan: 'Verifikasi keamanan tidak dapat dimuat. Periksa koneksi Anda lalu coba lagi.',
				jaringan: true
			});
			return;
		}

		try {
			const hasil = await authPasangPasswordBaru({
				token,
				email,
				password: formData.password,
				password_confirmation: formData.password_confirmation,
				recaptcha_token: recaptchaToken
			});

			setSelesai(hasil.message);
		} catch (error) {
			const galat = await bacaGalat(error);

			galat.isian.forEach((item) => {
				if (item.type === 'password') {
					setError(item.type, { type: 'manual', message: item.message });
				}
			});

			// Penolakan reCAPTCHA tidak punya isian untuk ditempeli.
			setSpanduk({ pesan: galat.ringkasan, jaringan: galat.jaringan });
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

			<CatatanRecaptcha />
		</form>
	);
}

export default FormPasswordBaru;
