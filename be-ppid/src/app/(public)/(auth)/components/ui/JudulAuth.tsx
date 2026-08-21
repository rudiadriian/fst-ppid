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
				<img
					className="w-12"
					src="/assets/images/logo/logo.svg"
					alt="PPID Admin"
				/>
				<Typography className="text-2xl leading-none font-bold tracking-tight">PPID Admin</Typography>
			</div>

			<Typography className="mt-8 text-4xl leading-[1.25] font-extrabold tracking-tight">{judul}</Typography>

			{keterangan && <Typography className="mt-2 text-base">{keterangan}</Typography>}
		</div>
	);
}

export default JudulAuth;
