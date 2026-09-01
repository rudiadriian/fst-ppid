import Typography from '@mui/material/Typography';

function SignInPageTitle() {
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

			<Typography className="mt-8 text-4xl leading-[1.25] font-extrabold tracking-tight">Masuk</Typography>
			{/*
			 * Sebelumnya di sini ada tautan "Sign up". Pendaftaran mandiri sudah
			 * ditutup — `authSignUp` menolak setiap panggilan — jadi tautan itu
			 * hanya mengantar orang ke formulir yang pasti gagal. Diganti
			 * keterangan tentang dari mana akun panel sebenarnya berasal.
			 */}
			<div className="mt-0.5 font-medium">
				<Typography color="text.secondary">
					Akun panel dibuat oleh administrator melalui modul Pengguna.
				</Typography>
			</div>
		</div>
	);
}

export default SignInPageTitle;
