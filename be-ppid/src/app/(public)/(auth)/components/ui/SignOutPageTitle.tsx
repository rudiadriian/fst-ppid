import Typography from '@mui/material/Typography';

function SignOutPageTitle() {
	return (
		<div className="w-full">
			<div className="flex items-center justify-center gap-3">
				{/* Lebarnya mengikuti tinggi: logo perusahaan berbanding 707x243,
				    bukan persegi seperti tanda bawaan template (langkah 85). */}
				<img
					className="h-10 w-auto"
					src="/assets/images/logo/logo-fstj.png"
					alt="PT Food Station Tjipinang Jaya (Perseroda)"
				/>
			</div>

			<Typography className="mt-8 text-center text-4xl leading-[1.25] font-extrabold tracking-tight">
				You have signed out!
			</Typography>
		</div>
	);
}

export default SignOutPageTitle;
