import Typography from '@mui/material/Typography';

function SignOutPageTitle() {
	return (
		<div className="w-full">
			<div className="flex items-center justify-center gap-3">
				<img
					className="w-12"
					src="/assets/images/logo/logo.svg"
					alt="PPID Admin"
				/>
				<Typography className="text-2xl leading-none font-bold tracking-tight">PPID Admin</Typography>
			</div>

			<Typography className="mt-8 text-center text-4xl leading-[1.25] font-extrabold tracking-tight">
				You have signed out!
			</Typography>
		</div>
	);
}

export default SignOutPageTitle;
