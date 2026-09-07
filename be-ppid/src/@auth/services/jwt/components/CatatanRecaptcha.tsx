import Typography from '@mui/material/Typography';
import { recaptchaAktif } from '../utils/recaptcha';

/**
 * Keterangan bahwa formulir dilindungi reCAPTCHA.
 *
 * reCAPTCHA v3 tidak menampilkan teka-teki apa pun — tidak ada kotak yang
 * dicentang, tidak ada gambar yang dibaca. Satu-satunya jejaknya di layar
 * adalah lencana kecil Google di pojok kanan bawah, dan lencana itu mudah
 * terlewat: orang yang gagal masuk tidak punya cara menduga bahwa ada
 * pemeriksaan ketiga selain email dan password.
 *
 * Google juga mensyaratkan penyebutan ini bila lencananya disembunyikan.
 * Lencananya di sini tidak disembunyikan, tetapi kalimat ini tetap dipasang:
 * yang menaruh biaya adalah orang yang ditolak tanpa tahu apa yang menolaknya.
 */
function CatatanRecaptcha() {
	if (!recaptchaAktif()) {
		return null;
	}

	return (
		<Typography
			variant="caption"
			color="text.secondary"
			className="mt-4 block text-center"
		>
			Dilindungi reCAPTCHA. Berlaku{' '}
			<a
				href="https://policies.google.com/privacy"
				target="_blank"
				rel="noopener noreferrer"
				className="underline"
			>
				Kebijakan Privasi
			</a>{' '}
			dan{' '}
			<a
				href="https://policies.google.com/terms"
				target="_blank"
				rel="noopener noreferrer"
				className="underline"
			>
				Persyaratan Layanan
			</a>{' '}
			Google.
		</Typography>
	);
}

export default CatatanRecaptcha;
