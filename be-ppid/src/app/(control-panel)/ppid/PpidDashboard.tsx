import { useState } from 'react';
import { useQuery } from '@tanstack/react-query';
import Paper from '@mui/material/Paper';
import Typography from '@mui/material/Typography';
import Alert from '@mui/material/Alert';
import Chip from '@mui/material/Chip';
import MenuItem from '@mui/material/MenuItem';
import TextField from '@mui/material/TextField';
import LinearProgress from '@mui/material/LinearProgress';
import FuseLoading from '@fuse/core/FuseLoading';
import FuseSvgIcon from '@fuse/core/FuseSvgIcon';
import { useTranslation } from 'react-i18next';
import api from '@/utils/api';

/**
 * Dashboard PPID — satu halaman untuk seluruh gambaran layanan.
 *
 * Isinya empat lapis yang sengaja disusun berurutan dari "apa keadaannya"
 * sampai "apa yang harus dikerjakan":
 *
 *   1. Ringkasan   — beban kerja dan angka pokok layanan;
 *   2. SLA         — kepatuhan batas waktu menurut UU No. 14 Tahun 2008;
 *   3. KPI         — capaian tiap indikator terhadap targetnya;
 *   4. Analisa     — tren, sebaran status, kategori paling diminta;
 *   5. Tindakan    — permohonan yang harus segera ditangani.
 *
 * Semua angkanya datang dari satu endpoint (`v1/dashboard/analitik`) supaya
 * tidak ada dua bagian halaman yang menampilkan hitungan berbeda.
 */

type IndikatorKpi = {
	kode: string;
	nama: string;
	satuan: string;
	arah: 'naik' | 'turun';
	realisasi: number | null;
	target: number;
	dasar: string;
	tercapai: boolean | null;
};

type Analitik = {
	data: {
		tahun: number | null;
		tahun_tersedia: number[];
		ringkasan: {
			permohonan: number;
			selesai: number;
			sedang_berjalan: number;
			keberatan: number;
			keberatan_belum_selesai: number;
			rata_rata_hari: number | null;
			kepuasan_persen: number | null;
			responden_survei: number;
			perlu_tindakan: number;
			menunggu_approval: number;
		};
		konten: {
			informasi_publik: number;
			informasi_publik_published: number;
			berita: number;
			berita_draft: number;
		};
		sla: {
			target_hari: number;
			perpanjangan_hari: number;
			target_keberatan_hari: number;
			ambang_siaga_hari: number;
			dinilai: number;
			tepat_waktu: number;
			telat_dijawab: number;
			lewat_batas: number;
			mendekati_batas: number;
			persen: number | null;
			keberatan: { ditanggapi: number; tepat_waktu: number; persen: number | null };
		};
		analisa: {
			tren: {
				tahun_utama: number;
				tahun_dibanding: number[];
				bulanan: {
					bulan: number;
					label: string;
					tahun: Record<string, { masuk: number; ditanggapi: number }>;
				}[];
				total: Record<string, { masuk: number; ditanggapi: number }>;
			};
			per_status: Record<string, number>;
			kategori_teratas: { nama: string; jumlah: number }[];
			cara_pengiriman: Record<string, number>;
		};
		kpi: IndikatorKpi[];
		tindakan: {
			id: number;
			kode_permohonan: string;
			pemohon: string | null;
			status: string;
			batas_waktu: string | null;
			telat_hari: number;
		}[];
	};
};

const LABEL_STATUS: Record<string, string> = {
	diajukan: 'Diajukan',
	diverifikasi: 'Diverifikasi',
	diproses: 'Diproses',
	menunggu_approval: 'Menunggu Persetujuan',
	disetujui: 'Disetujui',
	ditolak: 'Ditolak',
	ditolak_sebagian: 'Ditolak Sebagian',
	selesai: 'Selesai',
	kedaluwarsa: 'Kedaluwarsa'
};

function KartuAngka({
	label,
	nilai,
	keterangan,
	ikon,
	warna
}: {
	label: string;
	nilai: string | number;
	keterangan?: string;
	ikon: string;
	warna: string;
}) {
	return (
		<Paper
			elevation={0}
			className="flex flex-col gap-2 rounded-lg border border-divider p-4"
		>
			<div className="flex items-center justify-between gap-2">
				<Typography
					variant="body2"
					color="text.secondary"
				>
					{label}
				</Typography>
				<FuseSvgIcon
					size={20}
					className={warna}
				>
					{ikon}
				</FuseSvgIcon>
			</div>
			<Typography
				variant="h4"
				className="font-semibold"
			>
				{nilai}
			</Typography>
			{keterangan && (
				<Typography
					variant="caption"
					color="text.secondary"
				>
					{keterangan}
				</Typography>
			)}
		</Paper>
	);
}

function PpidDashboard() {
	const { t } = useTranslation();
	const [tahun, setTahun] = useState<string>('');

	const { data, isLoading, error } = useQuery({
		queryKey: ['ppid', 'dashboard', tahun],
		queryFn: () =>
			api.get('v1/dashboard/analitik', { searchParams: tahun ? { tahun } : undefined }).json<Analitik>(),
		staleTime: 60 * 1000
	});

	if (isLoading) {
		return <FuseLoading />;
	}

	if (error || !data) {
		return (
			<div className="p-6">
				<Alert severity="error">{t('Ringkasan gagal dimuat. Pastikan layanan API sedang berjalan.')}</Alert>
			</div>
		);
	}

	const { ringkasan, konten, sla, analisa, kpi, tindakan, tahun_tersedia: tahunTersedia } = data.data;
	const { tren } = analisa;
	const tahunUtama = String(tren.tahun_utama);
	const angka = (nilai: number | null, satuan = '') => (nilai === null ? '—' : `${nilai}${satuan}`);

	// Skala batang tren disamakan lintas tahun supaya perbandingan antar tahun
	// jujur — bukan tiap tahun dinormalkan ke lebar penuhnya sendiri.
	const trenMaks = Math.max(
		1,
		...tren.bulanan.flatMap((b) =>
			tren.tahun_dibanding.flatMap((th) => [b.tahun[String(th)]?.masuk ?? 0, b.tahun[String(th)]?.ditanggapi ?? 0])
		)
	);

	return (
		<div className="flex w-full flex-col gap-6 p-4 md:p-6">
			<div className="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
				<div>
					<Typography
						variant="h5"
						className="font-semibold"
					>
						{t('Dashboard PPID')}
					</Typography>
					<Typography
						variant="body2"
						color="text.secondary"
					>
						{t('Ringkasan layanan, analisa tren, kepatuhan batas waktu, dan capaian KPI.')}
					</Typography>
				</div>

				<TextField
					select
					size="small"
					label={t('Tahun')}
					value={tahun}
					onChange={(event) => setTahun(event.target.value)}
					sx={{ minWidth: 160 }}
				>
					<MenuItem value="">{t('Semua Tahun')}</MenuItem>
					{tahunTersedia.map((item) => (
						<MenuItem
							key={item}
							value={String(item)}
						>
							{item}
						</MenuItem>
					))}
				</TextField>
			</div>

			{/* --- 1. Beban kerja layanan --- */}
			<div className="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
				<KartuAngka
					label={t('Total permohonan')}
					nilai={ringkasan.permohonan}
					keterangan={`${ringkasan.perlu_tindakan} ${t('menunggu tindakan')}`}
					ikon="lucide:inbox"
					warna="text-blue-500"
				/>
				<KartuAngka
					label={t('Menunggu persetujuan')}
					nilai={ringkasan.menunggu_approval}
					keterangan={t('Perlu putusan PPID Utama')}
					ikon="lucide:clock"
					warna="text-amber-500"
				/>
				<KartuAngka
					label={t('Lewat batas waktu')}
					nilai={sla.lewat_batas}
					keterangan={t('Permohonan aktif melewati SLA')}
					ikon="lucide:triangle-alert"
					warna={sla.lewat_batas > 0 ? 'text-red-500' : 'text-green-500'}
				/>
				<KartuAngka
					label={t('Keberatan belum selesai')}
					nilai={ringkasan.keberatan_belum_selesai}
					keterangan={`${t('dari')} ${ringkasan.keberatan} ${t('keberatan')}`}
					ikon="lucide:scale"
					warna="text-purple-500"
				/>
			</div>

			{/* --- 2. Kondisi konten --- */}
			<div className="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
				<KartuAngka
					label={t('Informasi publik')}
					nilai={konten.informasi_publik}
					keterangan={`${konten.informasi_publik_published} ${t('sudah terbit')}`}
					ikon="lucide:file-text"
					warna="text-teal-500"
				/>
				<KartuAngka
					label={t('Berita')}
					nilai={konten.berita}
					keterangan={`${konten.berita_draft} ${t('masih draft')}`}
					ikon="lucide:newspaper"
					warna="text-indigo-500"
				/>
				<KartuAngka
					label={t('Kepuasan pemohon')}
					nilai={angka(ringkasan.kepuasan_persen, '%')}
					keterangan={`${ringkasan.responden_survei} ${t('responden survei')}`}
					ikon="lucide:smile"
					warna="text-green-500"
				/>
			</div>

			{/* --- 3. Perlu tindakan segera — sengaja disorot: ini satu-satunya
			     bagian yang menuntut petugas berbuat sesuatu hari ini. --- */}
			<Paper
				elevation={0}
				className="rounded-lg border-2 p-4"
				sx={(theme) => ({
					borderColor:
						tindakan.length > 0 ? theme.vars.palette.error.main : theme.vars.palette.success.main,
					backgroundColor:
						tindakan.length > 0
							? `rgba(${theme.vars.palette.error.mainChannel} / 0.06)`
							: `rgba(${theme.vars.palette.success.mainChannel} / 0.06)`
				})}
			>
				<div className="mb-1 flex items-center gap-2">
					<FuseSvgIcon
						size={20}
						className={tindakan.length > 0 ? 'text-red-500' : 'text-green-500'}
					>
						{tindakan.length > 0 ? 'lucide:siren' : 'lucide:circle-check'}
					</FuseSvgIcon>
					<Typography className="font-semibold">{t('Perlu tindakan segera')}</Typography>
					{tindakan.length > 0 && (
						<Chip
							size="small"
							color="error"
							label={tindakan.length}
						/>
					)}
				</div>
				<Typography
					variant="body2"
					color="text.secondary"
					className="mb-4"
				>
					{t('Permohonan yang batas waktunya sudah lewat atau tinggal beberapa hari lagi.')}
				</Typography>

				{tindakan.length === 0 ? (
					<Alert severity="success">{t('Tidak ada permohonan yang mendesak saat ini.')}</Alert>
				) : (
					<div className="flex flex-col gap-2">
						{tindakan.map((item) => (
							<div
								key={item.id}
								className="flex flex-wrap items-center justify-between gap-2 rounded-md border border-divider bg-paper px-3 py-2"
							>
								<div className="min-w-0">
									<Typography
										variant="body2"
										className="font-medium"
									>
										{item.kode_permohonan}
									</Typography>
									<Typography
										variant="caption"
										color="text.secondary"
									>
										{item.pemohon ?? t('pemohon')} · {t(LABEL_STATUS[item.status] ?? item.status)} ·{' '}
										{t('batas')} {item.batas_waktu ?? '—'}
									</Typography>
								</div>

								<Chip
									size="small"
									color={item.telat_hari > 0 ? 'error' : 'warning'}
									label={
										item.telat_hari > 0
											? `${t('telat')} ${item.telat_hari} ${t('hari')}`
											: `${Math.abs(item.telat_hari)} ${t('hari lagi')}`
									}
								/>
							</div>
						))}
					</div>
				)}
			</Paper>

			{/* --- 4. Kepatuhan SLA --- */}
			<Paper
				elevation={0}
				className="rounded-lg border border-divider p-4"
			>
				<div className="mb-4 flex flex-wrap items-baseline justify-between gap-2">
					<Typography className="font-medium">{t('Kepatuhan SLA')}</Typography>
					<Typography
						variant="caption"
						color="text.secondary"
					>
						{t('Tanggapan')} {sla.target_hari} {t('hari kerja')} (+{sla.perpanjangan_hari}) ·{' '}
						{t('Keberatan')} {sla.target_keberatan_hari} {t('hari kerja')}
					</Typography>
				</div>

				{sla.dinilai === 0 ? (
					<Alert severity="info">
						{t('Belum ada permohonan yang bisa dinilai kepatuhannya pada periode ini.')}
					</Alert>
				) : (
					<>
						<div className="mb-4 flex items-center gap-3">
							<LinearProgress
								variant="determinate"
								value={sla.persen ?? 0}
								color={
									(sla.persen ?? 0) >= 90 ? 'success' : (sla.persen ?? 0) >= 75 ? 'warning' : 'error'
								}
								className="h-2 flex-1 rounded"
							/>
							<Typography className="w-16 shrink-0 text-right font-semibold">
								{angka(sla.persen, '%')}
							</Typography>
						</div>

						<div className="grid grid-cols-2 gap-4 lg:grid-cols-4">
							<KartuAngka
								label={t('Tepat waktu')}
								nilai={sla.tepat_waktu}
								keterangan={`${t('dari')} ${sla.dinilai} ${t('dinilai')}`}
								ikon="lucide:circle-check"
								warna="text-green-500"
							/>
							<KartuAngka
								label={t('Telat dijawab')}
								nilai={sla.telat_dijawab}
								ikon="lucide:clock-alert"
								warna="text-amber-500"
							/>
							<KartuAngka
								label={t('Lewat batas, belum dijawab')}
								nilai={sla.lewat_batas}
								ikon="lucide:triangle-alert"
								warna={sla.lewat_batas > 0 ? 'text-red-500' : 'text-green-500'}
							/>
							<KartuAngka
								label={t('Mendekati batas')}
								nilai={sla.mendekati_batas}
								keterangan={`≤ ${sla.ambang_siaga_hari} ${t('hari lagi')}`}
								ikon="lucide:hourglass"
								warna="text-blue-500"
							/>
						</div>

						<Typography
							variant="caption"
							color="text.secondary"
							className="mt-3 block"
						>
							{t('Keberatan ditanggapi tepat waktu')}: {angka(sla.keberatan.persen, '%')} (
							{sla.keberatan.tepat_waktu}/{sla.keberatan.ditanggapi})
						</Typography>
					</>
				)}
			</Paper>

			{/* --- 5. Capaian KPI --- */}
			<Paper
				elevation={0}
				className="rounded-lg border border-divider p-4"
			>
				<Typography className="mb-4 font-medium">{t('Capaian KPI')}</Typography>

				<div className="flex flex-col gap-4">
					{kpi.map((item) => {
						const persenBar =
							item.realisasi === null
								? 0
								: item.arah === 'naik'
									? Math.min(100, (item.realisasi / item.target) * 100)
									: Math.min(100, (item.target / Math.max(item.realisasi, 0.1)) * 100);

						return (
							<div key={item.kode}>
								<div className="mb-1 flex flex-wrap items-baseline justify-between gap-2">
									<Typography variant="body2">{t(item.nama)}</Typography>
									<div className="flex items-center gap-2">
										<Typography
											variant="body2"
											className="font-semibold"
										>
											{angka(item.realisasi, ` ${item.satuan}`)}
										</Typography>
										<Typography
											variant="caption"
											color="text.secondary"
										>
											{t('target')} {item.arah === 'naik' ? '≥' : '≤'} {item.target} {item.satuan}
										</Typography>
										<Chip
											size="small"
											label={
												item.tercapai === null
													? t('Belum ada data')
													: item.tercapai
														? t('Tercapai')
														: t('Belum tercapai')
											}
											color={
												item.tercapai === null ? 'default' : item.tercapai ? 'success' : 'error'
											}
										/>
									</div>
								</div>

								<LinearProgress
									variant="determinate"
									value={persenBar}
									color={item.tercapai ? 'success' : item.tercapai === null ? 'inherit' : 'warning'}
									className="h-1.5 rounded"
								/>

								<Typography
									variant="caption"
									color="text.secondary"
									className="mt-1 block"
								>
									{t(item.dasar)}
								</Typography>
							</div>
						);
					})}
				</div>
			</Paper>

			{/* --- 6. Tren bulanan + sebaran --- */}
			<div className="grid grid-cols-1 gap-4 lg:grid-cols-3">
				<Paper
					elevation={0}
					className="rounded-lg border border-divider p-4 lg:col-span-2"
				>
					<div className="mb-1 flex flex-wrap items-baseline justify-between gap-2">
						<Typography className="font-medium">{t('Permohonan masuk vs ditanggapi')}</Typography>
						<Typography
							variant="caption"
							color="text.secondary"
						>
							{t('Ringkasan per bulan')} · {t('pembanding')} {tren.tahun_dibanding.length}{' '}
							{t('tahun terakhir')}
						</Typography>
					</div>

					{/* Total setahun tiap tahun pembanding. */}
					<div className="mb-4 flex flex-wrap gap-2">
						{tren.tahun_dibanding.map((th) => {
							const total = tren.total[String(th)];
							return (
								<Chip
									key={th}
									size="small"
									variant={String(th) === tahunUtama ? 'filled' : 'outlined'}
									color={String(th) === tahunUtama ? 'primary' : 'default'}
									label={`${th}: ${total?.masuk ?? 0} ${t('masuk')} / ${total?.ditanggapi ?? 0} ${t('ditanggapi')}`}
								/>
							);
						})}
					</div>

					<div className="flex flex-col gap-3">
						{tren.bulanan.map((baris) => (
							<div
								key={baris.bulan}
								className="flex items-center gap-3"
							>
								<span className="w-10 shrink-0 text-sm text-secondary">{baris.label}</span>

								<div className="flex flex-1 flex-col gap-1">
									{tren.tahun_dibanding.map((th) => {
										const nilai = baris.tahun[String(th)] ?? { masuk: 0, ditanggapi: 0 };
										const utama = String(th) === tahunUtama;

										return (
											<div
												key={th}
												className="flex items-center gap-2"
											>
												<span className="w-10 shrink-0 text-right text-xs text-secondary">
													{th}
												</span>
												<LinearProgress
													variant="determinate"
													value={(nilai.masuk / trenMaks) * 100}
													color={utama ? 'primary' : 'inherit'}
													className={utama ? 'h-2 flex-1 rounded' : 'h-1.5 flex-1 rounded opacity-60'}
												/>
												<span className="w-14 shrink-0 text-right text-xs font-medium">
													{nilai.masuk}/{nilai.ditanggapi}
												</span>
											</div>
										);
									})}
								</div>
							</div>
						))}
					</div>

					<Typography
						variant="caption"
						color="text.secondary"
						className="mt-3 block"
					>
						{t('Panjang batang mengikuti jumlah permohonan masuk; angka di kanan menunjukkan masuk/ditanggapi. Skala batang sama untuk semua tahun.')}
					</Typography>
				</Paper>

				<div className="flex flex-col gap-4">
					<Paper
						elevation={0}
						className="rounded-lg border border-divider p-4"
					>
						<Typography className="mb-3 font-medium">{t('Sebaran status')}</Typography>

						<div className="flex flex-wrap gap-2">
							{Object.entries(analisa.per_status).length === 0 ? (
								<Typography
									variant="body2"
									color="text.secondary"
								>
									{t('Belum ada data.')}
								</Typography>
							) : (
								Object.entries(analisa.per_status).map(([status, jumlah]) => (
									<Chip
										key={status}
										size="small"
										label={`${t(LABEL_STATUS[status] ?? status)}: ${jumlah}`}
									/>
								))
							)}
						</div>
					</Paper>

					<Paper
						elevation={0}
						className="rounded-lg border border-divider p-4"
					>
						<Typography className="mb-3 font-medium">{t('Kategori paling diminta')}</Typography>

						{analisa.kategori_teratas.length === 0 ? (
							<Typography
								variant="body2"
								color="text.secondary"
							>
								{t('Belum ada data.')}
							</Typography>
						) : (
							<div className="flex flex-col gap-2">
								{analisa.kategori_teratas.map((item) => (
									<div
										key={item.nama}
										className="flex items-center justify-between gap-3"
									>
										<Typography variant="body2">{t(item.nama)}</Typography>
										<Typography
											variant="body2"
											className="font-semibold"
										>
											{item.jumlah}
										</Typography>
									</div>
								))}
							</div>
						)}
					</Paper>
				</div>
			</div>
		</div>
	);
}

export default PpidDashboard;
