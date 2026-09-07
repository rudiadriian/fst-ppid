import { useCallback, useEffect, useRef, useState } from 'react';
import { useTheme } from '@mui/material/styles';
import Typography from '@mui/material/Typography';
import Skeleton from '@mui/material/Skeleton';
import { muatRecaptcha, recaptchaAktif, siteKeyRecaptcha } from '../utils/recaptcha';

type KolomRecaptchaProps = {
	/** Token yang sedang berlaku, atau null bila kotaknya belum dicentang. */
	onTokenChange: (token: string | null) => void;
	error?: boolean;
	helperText?: string;
	/**
	 * Dinaikkan pemanggil untuk memaksa kotaknya kosong kembali — dipakai
	 * setelah kiriman gagal, karena Google membuang token begitu ditukar.
	 */
	muatUlang?: number;
};

/**
 * Kotak centang "Saya bukan robot".
 *
 * Berbeda dari captcha gambar yang dulu dipakai panel ini, jawabannya tidak
 * diketik dan tidak diperiksa server sebagai teks: yang dikirim adalah token
 * dari Google, dan `KolomRecaptcha` hanya bertugas menyerahkannya ke atas
 * lewat `onTokenChange`.
 *
 * Token berumur dua menit. Kalau kedaluwarsa sebelum tombol kirim ditekan,
 * Google memanggil `expired-callback` dan tokennya dikosongkan di sini juga —
 * kalau tidak, formulir akan mengirim token mati dan orangnya melihat
 * penolakan yang tidak bisa dijelaskan dari apa yang ada di layar.
 *
 * Bila site key tidak dipasang saat build, komponen ini tidak menggambar apa
 * pun. Server yang tetap memutuskan captcha wajib atau tidak.
 */
function KolomRecaptcha(props: KolomRecaptchaProps) {
	const { onTokenChange, error, helperText, muatUlang = 0 } = props;

	const theme = useTheme();
	const wadah = useRef<HTMLDivElement | null>(null);
	const idWidget = useRef<number | null>(null);

	const [memuat, setMemuat] = useState(true);
	const [gagal, setGagal] = useState(false);

	/*
	 * Callback-nya disimpan di ref, bukan dipakai langsung di dalam efek.
	 * `grecaptcha.render` hanya boleh dipanggil sekali untuk satu wadah, jadi
	 * efeknya tidak boleh ikut berjalan ulang setiap kali pemanggil menyerahkan
	 * fungsi baru — dan pemanggil biasanya memang menyerahkannya inline.
	 */
	const kabarkan = useRef(onTokenChange);
	kabarkan.current = onTokenChange;

	const pasang = useCallback(async () => {
		if (!wadah.current || idWidget.current !== null) {
			return;
		}

		setGagal(false);

		try {
			const grecaptcha = await muatRecaptcha();

			// Bisa saja komponennya sudah dilepas selagi skripnya diunduh.
			if (!wadah.current || idWidget.current !== null) {
				return;
			}

			idWidget.current = grecaptcha.render(wadah.current, {
				sitekey: siteKeyRecaptcha(),
				theme: theme.palette.mode === 'dark' ? 'dark' : 'light',
				callback: (token: string) => kabarkan.current(token),
				'expired-callback': () => kabarkan.current(null),
				'error-callback': () => {
					kabarkan.current(null);
					setGagal(true);
				}
			});
		} catch {
			setGagal(true);
		} finally {
			setMemuat(false);
		}
	}, [theme.palette.mode]);

	useEffect(() => {
		if (!recaptchaAktif()) {
			setMemuat(false);
			return;
		}

		void pasang();
	}, [pasang]);

	// Kiriman gagal: kosongkan kotaknya supaya orangnya mencentang ulang.
	useEffect(() => {
		if (muatUlang === 0 || idWidget.current === null) {
			return;
		}

		window.grecaptcha?.reset(idWidget.current);
		kabarkan.current(null);
	}, [muatUlang]);

	if (!recaptchaAktif()) {
		return null;
	}

	return (
		<div className="mb-6 flex flex-col gap-2">
			{memuat && (
				<Skeleton
					variant="rounded"
					width={304}
					height={78}
				/>
			)}

			<div ref={wadah} />

			{(gagal || error) && (
				<Typography
					variant="caption"
					color="error"
				>
					{gagal
						? 'Verifikasi keamanan tidak dapat dimuat. Periksa koneksi Anda lalu muat ulang halaman.'
						: helperText}
				</Typography>
			)}
		</div>
	);
}

export default KolomRecaptcha;
