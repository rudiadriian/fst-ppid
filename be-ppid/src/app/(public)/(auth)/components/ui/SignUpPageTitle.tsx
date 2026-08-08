import Typography from '@mui/material/Typography';
import Link from '@fuse/core/Link';

function SignUpPageTitle() {
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
