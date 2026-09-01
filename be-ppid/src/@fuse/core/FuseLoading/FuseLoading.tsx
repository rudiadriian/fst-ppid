import { useTimeout } from '@fuse/hooks';
import { useState } from 'react';
import clsx from 'clsx';

export type FuseLoadingProps = {
	delay?: number;
	className?: string;
};

/**
 * FuseLoading displays a loading state with an optional delay
 *
 * Tiga titik pantul bawaan template diganti maskot Food Station (langkah 99),
 * sama dengan yang dipakai layar pembuka. Ukurannya lebih kecil karena
 * komponen ini muncul di dalam halaman — kadang di dalam panel sempit — bukan
 * menutupi seluruh layar.
 *
 * Latarnya sengaja tidak diberi warna: pemuat ini menumpang warna halaman yang
 * sedang menunggu isinya, dan memberi warna sendiri justru melahirkan kotak
 * putih di tengah panel gelap.
 */
function FuseLoading(props: FuseLoadingProps) {
	const { delay = 0, className } = props;
	const [showLoading, setShowLoading] = useState(!delay);

	useTimeout(() => {
		setShowLoading(true);
	}, delay);

	return (
		<div
			className={clsx(
				className,
				'flex h-full min-h-full w-full flex-1 flex-col items-center justify-center self-center p-6',
				!showLoading ? 'hidden' : ''
			)}
		>
			<img
				className="h-auto w-28 max-w-[40vw]"
				src="/assets/images/logo/loader-fs.gif"
				alt="Memuat…"
			/>
		</div>
	);
}

export default FuseLoading;
