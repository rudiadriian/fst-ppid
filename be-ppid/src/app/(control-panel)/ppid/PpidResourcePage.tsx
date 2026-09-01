import { useEffect, useState } from 'react';
import { useParams, useSearchParams } from 'react-router';
import MenuItem from '@mui/material/MenuItem';
import ListItemIcon from '@mui/material/ListItemIcon';
import Alert from '@mui/material/Alert';
import Typography from '@mui/material/Typography';
import FuseLoading from '@fuse/core/FuseLoading';
import FuseSvgIcon from '@fuse/core/FuseSvgIcon';
import ResourceListPage from './components/ResourceListPage';
import PermohonanDetailDialog from './components/PermohonanDetailDialog';
import KeberatanDetailDialog from './components/KeberatanDetailDialog';
import PemohonDetailDialog from './components/PemohonDetailDialog';
import RoleAksesDialog from './components/RoleAksesDialog';
import AlurTahapDialog from './components/AlurTahapDialog';
import { resourceBySlug } from './lib/resources';
import { useAksesModul } from './api/useNavigasi';
import { ApiRecord } from './api/ppidApi';

/**
 * Satu halaman untuk semua modul CMS.
 *
 * Modul ditentukan dari segmen URL (`/ppid/:resourceSlug`) lalu dicocokkan ke
 * registry konfigurasi. Karena itu menambah modul baru tidak memerlukan route
 * atau komponen halaman baru.
 */
function PpidResourcePage() {
	const { resourceSlug } = useParams<{ resourceSlug: string }>();
	const config = resourceSlug ? resourceBySlug.get(resourceSlug) : undefined;

	const { akses, isLoading } = useAksesModul(config?.modul ?? '');

	const [roleTerpilih, setRoleTerpilih] = useState<ApiRecord | null>(null);
	const [alurTerpilih, setAlurTerpilih] = useState<ApiRecord | null>(null);
	const [pemohonTerpilih, setPemohonTerpilih] = useState<number | null>(null);
	/*
	 * Satu dialog per kategori pengajuan, dan tidak ada lagi dialog status
	 * terpisah (langkah 94): memindahkan status maupun memutus persetujuan
	 * dilakukan di dalam rinciannya, tempat berkas yang jadi dasar keputusannya
	 * ikut terbaca. Menu "Ubah status" di tabel dilepas karena ia mengajak
	 * memutuskan tanpa membuka berkasnya.
	 */
	const [detailPermohonan, setDetailPermohonan] = useState<number | null>(null);
	const [detailKeberatan, setDetailKeberatan] = useState<number | null>(null);

	/*
	 * Notifikasi panel menaut ke `/ppid/{modul}?detail={id}` supaya sekali klik
	 * langsung membuka barisnya, bukan berhenti di daftar modul. Parameternya
	 * dibaca sekali lalu dibersihkan dari URL — kalau dibiarkan, menutup dialog
	 * dan memuat ulang halaman akan membukanya lagi.
	 */
	const [searchParams, setSearchParams] = useSearchParams();
	const idDariNotifikasi = Number(searchParams.get('detail') ?? 0);
	/*
	 * Modul Permohonan memuat dua kategori dalam satu daftar (langkah 89), dan
	 * nomor barisnya berasal dari dua tabel berbeda — id 3 bisa berarti
	 * permohonan 3 maupun keberatan 3. Karena itu notifikasi keberatan membawa
	 * `jenis=keberatan`; tanpa itu tautannya bisa membuka berkas orang lain.
	 */
	const jenisDariNotifikasi = searchParams.get('jenis');

	useEffect(() => {
		if (!idDariNotifikasi) {
			return;
		}

		// Notifikasi persetujuan menaut ke modul Permohonan; yang dibuka
		// rincian pengajuannya, tempat jenjang persetujuannya berada.
		const pembuka: Record<string, (id: number) => void> = {
			pemohon: setPemohonTerpilih,
			permohonan: jenisDariNotifikasi === 'keberatan' ? setDetailKeberatan : setDetailPermohonan,
			// Modul Keberatan tidak lagi ada di menu, tetapi halamannya masih
			// bisa dibuka lewat URL — termasuk tautan notifikasi lama.
			keberatan: setDetailKeberatan
		};

		const buka = resourceSlug ? pembuka[resourceSlug] : undefined;

		if (!buka) {
			return;
		}

		buka(idDariNotifikasi);
		setSearchParams({}, { replace: true });
	}, [idDariNotifikasi, jenisDariNotifikasi, resourceSlug, setSearchParams]);

	if (!config) {
		return (
			<div className="p-6">
				<Alert severity="error">Modul &quot;{resourceSlug}&quot; tidak dikenal.</Alert>
			</div>
		);
	}

	if (isLoading) {
		return <FuseLoading />;
	}

	if (!akses.view) {
		return (
			<div className="p-6">
				<Alert severity="warning">
					<Typography className="font-medium">Akses ditolak</Typography>
					Role Anda tidak memiliki hak lihat pada modul {config.title}.
				</Alert>
			</div>
		);
	}

	const modulPermohonan = config.slug === 'permohonan';
	const modulKeberatan = config.slug === 'keberatan';
	const modulRole = config.slug === 'role';
	const modulAlur = config.slug === 'alur-approval';
	// Verifikasi data pemohon memakai hak `approve`, bukan `edit`: yang
	// dilakukan petugas adalah menyetujui/menolak berkas, bukan menyunting
	// datanya. Modul Pemohon sendiri tetap `readOnly`.
	const modulPemohon = config.slug === 'pemohon';

	/**
	 * Membuka rincian satu baris — dipakai klik baris maupun menu aksinya.
	 *
	 * Daftar Permohonan gabungan dua kategori (langkah 89), jadi barisnya
	 * sendiri yang menentukan dialog mana yang dibuka. `ref_id` adalah id asli
	 * di tabelnya — `id` sudah dipakai sebagai kunci baris yang unik lintas
	 * tabel (`permohonan-3` / `keberatan-3`), dan mengirimnya ke dialog akan
	 * meminta rincian dengan nomor yang tidak ada.
	 */
	const bukaRincian = (baris: ApiRecord) => {
		const idAsli = Number(baris.ref_id ?? baris.id);

		if (modulPemohon) {
			setPemohonTerpilih(idAsli);
			return;
		}

		if (modulKeberatan || (modulPermohonan && baris.jenis === 'keberatan')) {
			setDetailKeberatan(idAsli);
			return;
		}

		if (modulPermohonan) {
			setDetailPermohonan(idAsli);
		}
	};

	/*
	 * Aksi baris per modul.
	 *
	 * Untuk Permohonan dan Keberatan, aksi inilah satu-satunya jalur yang
	 * tersisa: keduanya berisi kiriman pemohon, jadi menu Tambah, Ubah, dan
	 * Hapus sudah dilepas (`tanpaTambah`/`tanpaUbah`/`tanpaHapus`) dan
	 * endpoint-nya pun tidak ada di API.
	 *
	 * Sejak langkah 94 menu ini menyisakan satu entri saja untuk keduanya —
	 * "Detail & verifikasi". Perpindahan status dan tanggapan pindah ke dalam
	 * dialog rinciannya, karena memutuskan dari baris tabel berarti memutuskan
	 * tanpa membaca berkas yang sedang diputus.
	 *
	 * "Lihat detail" sengaja tidak dijaga hak `edit`: membaca rincian pengajuan
	 * adalah bagian dari melihat modulnya. Yang menuntut hak tulis adalah panel
	 * status dan tanggapan di dalamnya, dan itu dijaga sendiri.
	 *
	 * Detail pemohon boleh dibuka siapa pun yang berhak melihat modulnya;
	 * tombol keputusannya yang menuntut hak `Setujui`.
	 */
	const aksiBaris = ((): ((baris: ApiRecord, tutupMenu: () => void) => React.ReactNode[]) | undefined => {
		if (modulPemohon || modulPermohonan || modulKeberatan) {
			return (baris, tutupMenu) => [
				<MenuItem
					key="detail"
					onClick={() => {
						bukaRincian(baris);
						tutupMenu();
					}}
				>
					<ListItemIcon>
						<FuseSvgIcon size={18}>{modulPemohon ? 'lucide:user-check' : 'lucide:file-search'}</FuseSvgIcon>
					</ListItemIcon>
					{akses.approve || akses.edit ? 'Detail & verifikasi' : 'Lihat detail'}
				</MenuItem>
			];
		}

		if (modulAlur) {
			return (baris, tutupMenu) => [
				<MenuItem
					key="tahap"
					onClick={() => {
						setAlurTerpilih(baris);
						tutupMenu();
					}}
				>
					<ListItemIcon>
						<FuseSvgIcon size={18}>lucide:list-ordered</FuseSvgIcon>
					</ListItemIcon>
					{akses.edit ? 'Atur tahap' : 'Lihat tahap'}
				</MenuItem>
			];
		}

		if (!akses.edit) {
			return undefined;
		}

		if (modulRole) {
			return (baris, tutupMenu) => [
				<MenuItem
					key="akses"
					onClick={() => {
						setRoleTerpilih(baris);
						tutupMenu();
					}}
				>
					<ListItemIcon>
						<FuseSvgIcon size={18}>lucide:shield-check</FuseSvgIcon>
					</ListItemIcon>
					Atur hak akses
				</MenuItem>
			];
		}

		return undefined;
	})();

	return (
		<>
			<ResourceListPage
				// Key memaksa state tabel (halaman, filter, pencarian) direset
				// saat berpindah modul lewat menu.
				key={config.slug}
				config={config}
				aksiBaris={aksiBaris}
				// Modul yang rinciannya berupa dialog membukanya lewat klik baris;
				// sisanya memakai perilaku bawaan ResourceListPage (buka formulir
				// Ubah bila rolenya boleh menyunting).
				onRowClick={modulPemohon || modulPermohonan || modulKeberatan ? bukaRincian : undefined}
			/>

			{modulPermohonan && (
				<>
					<PermohonanDetailDialog
						open={detailPermohonan !== null}
						onClose={() => setDetailPermohonan(null)}
						permohonanId={detailPermohonan}
						bolehSetujui={akses.approve}
						bolehUbah={akses.edit}
					/>

					{/*
					 * Dua kategori dalam satu daftar: baris keberatan dibuka dengan
					 * dialognya sendiri walaupun modulnya Permohonan.
					 */}
					<KeberatanDetailDialog
						open={detailKeberatan !== null}
						onClose={() => setDetailKeberatan(null)}
						keberatanId={detailKeberatan}
						bolehSetujui={akses.approve}
						bolehUbah={akses.edit}
					/>
				</>
			)}

			{modulKeberatan && (
				<KeberatanDetailDialog
					open={detailKeberatan !== null}
					onClose={() => setDetailKeberatan(null)}
					keberatanId={detailKeberatan}
					bolehSetujui={akses.approve}
					bolehUbah={akses.edit}
				/>
			)}

			{modulPemohon && (
				<PemohonDetailDialog
					open={pemohonTerpilih !== null}
					onClose={() => setPemohonTerpilih(null)}
					pemohonId={pemohonTerpilih}
					bolehVerifikasi={akses.approve}
				/>
			)}

			{modulRole && (
				<RoleAksesDialog
					open={roleTerpilih !== null}
					onClose={() => setRoleTerpilih(null)}
					role={
						roleTerpilih
							? {
									id: Number(roleTerpilih.id),
									name: String(roleTerpilih.name ?? ''),
									slug: String(roleTerpilih.slug ?? '')
								}
							: null
					}
				/>
			)}

			{modulAlur && (
				<AlurTahapDialog
					open={alurTerpilih !== null}
					onClose={() => setAlurTerpilih(null)}
					alur={
						alurTerpilih
							? {
									id: Number(alurTerpilih.id),
									nama: String(alurTerpilih.nama ?? ''),
									jenis: String(alurTerpilih.jenis ?? '')
								}
							: null
					}
					bolehUbah={akses.edit}
				/>
			)}
		</>
	);
}

export default PpidResourcePage;
