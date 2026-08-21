import Typography from '@mui/material/Typography';

function SignInPageTitle() {
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
