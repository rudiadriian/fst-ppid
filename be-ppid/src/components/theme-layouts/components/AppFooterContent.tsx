import Typography from '@mui/material/Typography';

/**
 * The app footer content.
 */
function AppFooterContent() {
	return (
		<div className="flex w-full items-center justify-between gap-2">
			<Typography
				className="text-md"
				color="text.secondary"
			>
				PPID Admin
			</Typography>

			<Typography
				className="text-md"
				color="text.secondary"
			>
				&copy; {new Date().getFullYear()} PPID. Seluruh hak cipta dilindungi.
			</Typography>
		</div>
	);
}

export default AppFooterContent;
