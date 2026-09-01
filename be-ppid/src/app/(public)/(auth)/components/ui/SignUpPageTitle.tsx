import Typography from '@mui/material/Typography';
import Link from '@fuse/core/Link';

function SignUpPageTitle() {
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

			<Typography className="mt-8 text-4xl leading-[1.25] font-extrabold tracking-tight">Sign up</Typography>
			<div className="mt-0.5 flex items-baseline font-medium">
				<Typography>Already have an account?</Typography>
				<Link
					className="ml-1"
					to="/sign-in"
				>
					Sign in
				</Link>
			</div>
		</div>
	);
}

export default SignUpPageTitle;
