import { memo } from 'react';

/**
 * Layar pembuka saat aplikasi masih dimuat.
 *
 * Memakai markup dan kelas yang sama persis dengan splash di `index.html`
 * (langkah 99), karena keduanya bergantian tampil di layar yang sama: yang di
 * `index.html` menutupi jeda sebelum bundel React berjalan, yang ini menutupi
 * jeda sesudahnya. Bentuk yang berbeda akan terlihat sebagai kedipan.
 *
 * Gayanya tinggal di `<style>` pada `index.html` — satu-satunya tempat yang
 * sudah terpasang sebelum React sempat memuat lembar gayanya sendiri.
 */
function FuseSplashScreen() {
	return (
		<div id="fuse-splash-screen">
			<img
				className="loader"
				src="/assets/images/logo/loader-fs.gif"
				alt="Memuat…"
			/>
			<div className="keterangan">Memuat…</div>
		</div>
	);
}

export default memo(FuseSplashScreen);
