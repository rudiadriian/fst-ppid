import { useCallback, useEffect, useState } from 'react';
import TextField from '@mui/material/TextField';
import IconButton from '@mui/material/IconButton';
import Tooltip from '@mui/material/Tooltip';
import Skeleton from '@mui/material/Skeleton';
import Typography from '@mui/material/Typography';
import FuseSvgIcon from '@fuse/core/FuseSvgIcon';
import { authAmbilCaptcha } from '@auth/authApi';

type KolomCaptchaProps = {
	/** Jawaban yang sedang diketik. */
	value: string;
	onChange: (nilai: string) => void;
	/** Id kode yang sedang berlaku; dikirim bersama jawaban. */
	onIdChange: (id: string | null) => void;
	error?: boolean;
	helperText?: string;
	/**
	 * Dinaikkan pemanggil untuk memaksa gambar baru — dipakai setelah kiriman
	 * gagal, karena kode lama sudah dibuang server begitu diperiksa.
	 */
	muatUlang?: number;
	disabled?: boolean;
};

/**
 * Isian captcha beserta gambarnya.
 *
 * Gambar dan id-nya diambil dari `v1/auth/captcha`. Bila captcha dimatikan di
 * server (`PPID_CAPTCHA_AKTIF=false`, dipakai saat pengujian), endpoint-nya
 * menjawab `aktif: false` dan komponen ini tidak menampilkan apa pun — jadi
 * formulir pemanggilnya tidak perlu tahu keadaan sakelar itu.
 */
function KolomCaptcha(props: KolomCaptchaProps) {
	const { value, onChange, onIdChange, error, helperText, muatUlang = 0, disabled } = props;

	const [gambar, setGambar] = useState<string | null>(null);
	const [aktif, setAktif] = useState(true);
	const [memuat, setMemuat] = useState(true);
	const [gagal, setGagal] = useState(false);

	const ambil = useCallback(async () => {
		setMemuat(true);
		setGagal(false);

		try {
			const hasil = await authAmbilCaptcha();

			setAktif(hasil.aktif);
			setGambar(hasil.gambar);
			onIdChange(hasil.id);
		} catch {
			/*
			 * Kegagalan mengambil captcha hampir selalu berarti API-nya mati —
			 * keadaan yang sama yang membuat tombol Masuk gagal. Dicatat sebagai
			 * keadaan tersendiri supaya kotaknya tidak diam berupa bingkai
			 * kosong: orang harus tahu ada tombol untuk mencobanya lagi.
			 */
			setGagal(true);
			setGambar(null);
			onIdChange(null);
		} finally {
			setMemuat(false);
		}
	}, [onIdChange]);

	useEffect(() => {
		void ambil();
	}, [ambil, muatUlang]);

	if (!aktif) {
		return null;
	}

	return (
		<div className="mb-6 flex flex-col gap-2">
			<div className="flex items-center gap-2">
				{memuat ? (
					<Skeleton
						variant="rounded"
						width={190}
						height={60}
					/>
				) : gambar ? (
					<img
						src={gambar}
						alt="Kode captcha"
						width={190}
						height={60}
						className="rounded border border-black/10 dark:border-white/20"
					/>
				) : (
					<div className="flex h-15 w-48 items-center justify-center rounded border border-dashed border-black/20 px-2 dark:border-white/20">
						<Typography
							variant="caption"
							color="text.secondary"
							className="text-center"
						>
							Gambar gagal dimuat
						</Typography>
					</div>
				)}

				<Tooltip title="Ganti gambar">
					<span>
						<IconButton
							onClick={() => void ambil()}
							disabled={memuat || disabled}
							aria-label="Ganti gambar captcha"
						>
							<FuseSvgIcon size={20}>lucide:refresh-cw</FuseSvgIcon>
						</IconButton>
					</span>
				</Tooltip>
			</div>

			<TextField
				label="Ketik kode di gambar"
				value={value}
				onChange={(event) => onChange(event.target.value)}
				error={error || gagal}
				helperText={gagal ? 'Tidak dapat memuat captcha. Tekan tombol ganti gambar.' : helperText}
				variant="outlined"
				required
				fullWidth
				disabled={disabled}
				slotProps={{
					htmlInput: {
						autoCapitalize: 'characters',
						autoComplete: 'off',
						maxLength: 16
					}
				}}
			/>
		</div>
	);
}

export default KolomCaptcha;
