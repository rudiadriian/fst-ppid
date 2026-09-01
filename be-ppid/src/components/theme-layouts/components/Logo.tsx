import { styled } from '@mui/material/styles';
import clsx from 'clsx';

const Root = styled('div')(({ theme }) => ({
	'& > .logo-icon': {
		transition: theme.transitions.create(['width', 'height'], {
			duration: theme.transitions.duration.shortest,
			easing: theme.transitions.easing.easeInOut
		})
	},
	'& > .badge': {
		transition: theme.transitions.create('opacity', {
			duration: theme.transitions.duration.shortest,
			easing: theme.transitions.easing.easeInOut
		})
	}
}));

type LogoProps = {
	className?: string;
};

/**
 * The logo component.
 */
function Logo(props: LogoProps) {
	const { className = '' } = props;
	return (
		<Root className={clsx('flex flex-shrink-0 flex-grow items-center gap-3', className)}>
			<div className="flex flex-1 items-center gap-2">
				{/*
				 * Logo perusahaan, bukan lagi tanda bawaan template (langkah 85).
				 *
				 * `w-auto`, bukan lebar tetap: berkasnya berbanding 707×243,
				 * jauh lebih lebar daripada tanda persegi yang digantikannya.
				 * Mengunci lebarnya akan memipihkannya. Navbar style-1 lebarnya
				 * tetap 280 px, jadi logo selebar ini muat utuh. Label teks
				 * "PPID Admin" di sebelahnya dibuang (langkah 98) — logo saja.
				 */}
				<img
					className="logo-icon h-8 w-auto"
					src="/assets/images/logo/logo-fstj.png"
					alt="PT Food Station Tjipinang Jaya (Perseroda)"
				/>
			</div>
		</Root>
	);
}

export default Logo;
