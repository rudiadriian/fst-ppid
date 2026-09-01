import Typography from '@mui/material/Typography';

type JudulAuthProps = {
	judul: string;
	keterangan?: string;
};

/**
 * Kop halaman auth: logo, nama panel, judul, dan satu baris keterangan.
 *
 * Dipakai halaman Lupa password dan Password baru supaya keduanya sebangun
 * dengan halaman Masuk tanpa menyalin markup logonya berulang kali.
 */
function JudulAuth({ judul, keterangan }: JudulAuthProps) {
	return (
		<div className="w-full">
			<div className="flex items-center gap-3">
				{/* Lebarnya mengikuti tinggi: logo perusahaan berbanding 707x243,
				    bukan persegi seperti tanda bawaan template (langkah 85). */}
				<img
					className="h-10 w-auto"
					src="/assets/images/logo/logo-fstj.png"
					alt="PT Food Station Tjipinang Jaya (Perseroda)"
				/>
			</div>

			<Typography className="mt-8 text-4xl leading-[1.25] font-extrabold tracking-tight">{judul}</Typography>

			{keterangan && <Typography className="mt-2 text-base">{keterangan}</Typography>}
		</div>
	);
}

export default JudulAuth;
