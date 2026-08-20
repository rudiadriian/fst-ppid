import { useEffect, useState } from 'react';
import { useParams, useSearchParams } from 'react-router';
import MenuItem from '@mui/material/MenuItem';
import ListItemIcon from '@mui/material/ListItemIcon';
import Alert from '@mui/material/Alert';
import Typography from '@mui/material/Typography';
import FuseLoading from '@fuse/core/FuseLoading';
import FuseSvgIcon from '@fuse/core/FuseSvgIcon';
import ResourceListPage from './components/ResourceListPage';
import PermohonanStatusDialog from './components/PermohonanStatusDialog';
import PermohonanDetailDialog from './components/PermohonanDetailDialog';
import KeberatanTanggapanDialog from './components/KeberatanTanggapanDialog';
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

	const [permohonanTerpilih, setPermohonanTerpilih] = useState<ApiRecord | null>(null);
	const [keberatanTerpilih, setKeberatanTerpilih] = useState<ApiRecord | null>(null);
	const [roleTerpilih, setRoleTerpilih] = useState<ApiRecord | null>(null);
	const [alurTerpilih, setAlurTerpilih] = useState<ApiRecord | null>(null);
	const [pemohonTerpilih, setPemohonTerpilih] = useState<number | null>(null);
	// Rincian dipisah dari baris terpilih untuk aksi tulis: petugas kerap
	// membuka detail hanya untuk membaca, dan itu tidak boleh ikut membuka
	// dialog status.
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

	useEffect(() => {
		if (!idDariNotifikasi) {
			return;
		}

		// Notifikasi persetujuan menaut ke modul Permohonan/Keberatan; yang
		// dibuka rincian pengajuannya, tempat jenjang persetujuannya berada.
		const pembuka: Record<string, (id: number) => void> = {
			pemohon: setPemohonTerpilih,
			permohonan: setDetailPermohonan,
			keberatan: setDetailKeberatan
		};

		const buka = resourceSlug ? pembuka[resourceSlug] : undefined;

		if (!buka) {
			return;
		}

		buka(idDariNotifikasi);
		setSearchParams({}, { replace: true });
	}, [idDariNotifikasi, resourceSlug, setSearchParams]);

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

	/*
	 * Aksi baris per modul.
	 *
	 * Untuk Permohonan dan Keberatan, aksi inilah satu-satunya jalur yang
	 * tersisa: keduanya berisi kiriman pemohon, jadi menu Tambah, Ubah, dan
	 * Hapus sudah dilepas (`tanpaTambah`/`tanpaUbah`/`tanpaHapus`) dan
	 * endpoint-nya pun tidak ada di API.
	 *
	 * "Lihat detail" sengaja tidak dijaga hak `edit`: membaca rincian pengajuan
	 * adalah bagian dari melihat modulnya. Yang menuntut hak tulis adalah
	 * dialog status dan tanggapan.
	 *
	 * Detail pemohon boleh dibuka siapa pun yang berhak melihat modulnya;
	 * tombol keputusannya yang menuntut hak `Setujui`.
	 */
	const aksiBaris = ((): ((baris: ApiRecord, tutupMenu: () => void) => React.ReactNode[]) | undefined => {
		if (modulPemohon) {
			return (baris, tutupMenu) => [
				<MenuItem
					key="detail"
					onClick={() => {
						setPemohonTerpilih(Number(baris.id));
						tutupMenu();
					}}
				>
					<ListItemIcon>
						<FuseSvgIcon size={18}>lucide:user-check</FuseSvgIcon>
					</ListItemIcon>
					{akses.approve ? 'Detail & verifikasi' : 'Lihat detail'}
				</MenuItem>
			];
		}

		if (modulPermohonan) {
			return (baris, tutupMenu) => [
				<MenuItem
					key="detail"
					onClick={() => {
						setDetailPermohonan(Number(baris.id));
						tutupMenu();
					}}
				>
					<ListItemIcon>
						<FuseSvgIcon size={18}>lucide:file-search</FuseSvgIcon>
					</ListItemIcon>
					Lihat detail
				</MenuItem>,
				akses.edit ? (
					<MenuItem
						key="status"
						onClick={() => {
							setPermohonanTerpilih(baris);
							tutupMenu();
						}}
					>
						<ListItemIcon>
							<FuseSvgIcon size={18}>lucide:git-branch</FuseSvgIcon>
						</ListItemIcon>
						Ubah status
					</MenuItem>
				) : null
			];
		}

		if (modulKeberatan) {
			return (baris, tutupMenu) => [
				<MenuItem
					key="detail"
					onClick={() => {
						setDetailKeberatan(Number(baris.id));
						tutupMenu();
					}}
				>
					<ListItemIcon>
						<FuseSvgIcon size={18}>lucide:file-search</FuseSvgIcon>
					</ListItemIcon>
					Lihat detail
				</MenuItem>,
				akses.edit ? (
					<MenuItem
						key="tanggapan"
						onClick={() => {
							setKeberatanTerpilih(baris);
							tutupMenu();
						}}
					>
						<ListItemIcon>
							<FuseSvgIcon size={18}>lucide:message-square-reply</FuseSvgIcon>
						</ListItemIcon>
						Tanggapan &amp; status
					</MenuItem>
				) : null
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
			/>

			{modulPermohonan && (
				<>
					<PermohonanStatusDialog
						open={permohonanTerpilih !== null}
						onClose={() => setPermohonanTerpilih(null)}
						permohonan={
							permohonanTerpilih
								? {
										id: Number(permohonanTerpilih.id),
										kode_permohonan: String(permohonanTerpilih.kode_permohonan ?? ''),
										status: String(permohonanTerpilih.status ?? '')
									}
								: null
						}
					/>

					<PermohonanDetailDialog
						open={detailPermohonan !== null}
						onClose={() => setDetailPermohonan(null)}
						permohonanId={detailPermohonan}
						bolehSetujui={akses.approve}
					/>
				</>
			)}

			{modulKeberatan && (
				<>
					<KeberatanTanggapanDialog
						open={keberatanTerpilih !== null}
						onClose={() => setKeberatanTerpilih(null)}
						keberatan={
							keberatanTerpilih
								? {
										id: Number(keberatanTerpilih.id),
										// Keberatan tidak punya nomor sendiri; di
										// seluruh sistem ia dirujuk lewat nomor
										// permohonan induknya.
										kode_permohonan: String(
											(keberatanTerpilih.permohonan as { kode_permohonan?: string } | null)
												?.kode_permohonan ?? ''
										),
										status: String(keberatanTerpilih.status ?? ''),
										tanggapan_atasan_ppid: String(keberatanTerpilih.tanggapan_atasan_ppid ?? '')
									}
								: null
						}
					/>

					<KeberatanDetailDialog
						open={detailKeberatan !== null}
						onClose={() => setDetailKeberatan(null)}
						keberatanId={detailKeberatan}
						bolehSetujui={akses.approve}
					/>
				</>
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
