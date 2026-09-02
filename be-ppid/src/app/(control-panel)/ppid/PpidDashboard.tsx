import { useState } from 'react';
import { useQuery } from '@tanstack/react-query';
import Paper from '@mui/material/Paper';
import Typography from '@mui/material/Typography';
import Alert from '@mui/material/Alert';
import Chip from '@mui/material/Chip';
import MenuItem from '@mui/material/MenuItem';
import TextField from '@mui/material/TextField';
import LinearProgress from '@mui/material/LinearProgress';
import { useTheme } from '@mui/material/styles';
import FuseLoading from '@fuse/core/FuseLoading';
import FuseSvgIcon from '@fuse/core/FuseSvgIcon';
import { useTranslation } from 'react-i18next';
import { JENIS_KEBERATAN } from './lib/statusPengajuan';
import { ApexOptions } from 'apexcharts';
import ReactApexChart from 'react-apexcharts';
import api from '@/utils/api';

/**
 * Dashboard PPID — satu halaman untuk seluruh gambaran layanan.
 *
 * Urutannya sengaja dari "siapa yang dilayani" sampai "apa yang harus
 * dikerjakan hari ini":
 *
 *   1. Pemohon     — jumlah, jenis, dan keadaan Verifikasi Data Dirinya;
 *   2. Pengajuan   — Permohonan Informasi dan Keberatan Informasi;
 *   3. Sebaran     — status tiap jenis pengajuan, dan jenis pemohonnya;
 *   4. Konten      — pustaka informasi publik dan berita;
 *   5. Tindakan    — permohonan yang harus segera ditangani;
 *   6. SLA & KPI   — kepatuhan batas waktu dan capaian indikator;
 *   7. Tren        — permohonan masuk vs ditanggapi per bulan.
 *
 * Permohonan dan Keberatan selalu ditampilkan berpasangan: keduanya layanan
 * yang sama-sama diatur batas waktunya, dan membaca salah satunya saja
 * memberi gambaran yang timpang.
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
		pemohon: {
			total: number;
			per_jenis: Record<string, number>;
			verifikasi: { menunggu: number; terverifikasi: number; belum: number; ditolak: number };
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
			per_status: { permohonan: Record<string, number>; keberatan: Record<string, number> };
			per_jenis_pemohon: { permohonan: Record<string, number>; keberatan: Record<string, number> };
			cara_pengiriman: Record<string, number>;
			alasan_keberatan: { kode: string; label: string; jumlah: number }[];
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
	revisi: 'Revisi',
	menunggu_approval: 'Menunggu Persetujuan',
	disetujui: 'Disetujui',
	ditolak: 'Ditolak',
	ditolak_sebagian: 'Ditolak Sebagian',
	selesai: 'Selesai',
	kedaluwarsa: 'Kedaluwarsa'
};

/**
 * Jenis pemohon.
 *
 * Empat nilai pertama dipakai formulir portal sekarang; `pribadi` dan
 * `instansi` peninggalan data lama yang belum dipetakan ulang, dan tetap
 * diberi label supaya tidak muncul sebagai kode mentah di dashboard.
 */
const LABEL_JENIS: Record<string, string> = {
	perorangan: 'Perorangan',
	mahasiswa: 'Mahasiswa',
	lembaga: 'Lembaga / Organisasi / Perusahaan',
	kelompok: 'Kelompok Orang',
	pribadi: 'Pribadi',
	instansi: 'Instansi',
	tidak_diisi: 'Belum diisi'
};

/**
 * Daftar sebaran: label, batang sepanjang porsinya, lalu angkanya.
 *
 * Batangnya diskala terhadap nilai terbesar di daftar itu sendiri — yang
 * dibandingkan di sini memang antarbaris dalam satu daftar, bukan antar daftar.
 */
function DaftarSebaran({
	data,
	label,
	kosong,
	warna = 'primary'
}: {
	data: Record<string, number>;
	label: (kunci: string) => string;
	kosong: string;
	warna?: 'primary' | 'secondary';
}) {
	const baris = Object.entries(data ?? {}).sort((a, b) => b[1] - a[1]);
	const maks = Math.max(1, ...baris.map(([, jumlah]) => jumlah));

	if (baris.length === 0) {
		return (
			<Typography
				variant="body2"
				color="text.secondary"
			>
				{kosong}
			</Typography>
		);
	}

	return (
		<div className="flex flex-col gap-2.5">
			{baris.map(([kunci, jumlah]) => (
				<div key={kunci}>
					<div className="mb-1 flex items-baseline justify-between gap-3">
						<Typography variant="body2">{label(kunci)}</Typography>
						<Typography
							variant="body2"
							className="font-semibold"
						>
							{jumlah}
						</Typography>
					</div>
					<LinearProgress
						variant="determinate"
						value={(jumlah / maks) * 100}
						color={warna}
						className="h-1.5 rounded"
					/>
				</div>
			))}
		</div>
	);
}

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
	const theme = useTheme();
	const [tahun, setTahun] = useState<string>('');
	const [seri, setSeri] = useState<'masuk' | 'ditanggapi'>('masuk');

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

	const { ringkasan, pemohon, konten, sla, analisa, kpi, tindakan, tahun_tersedia: tahunTersedia } = data.data;
	const { tren } = analisa;
	const tahunUtama = String(tren.tahun_utama);
	const angka = (nilai: number | null, satuan = '') => (nilai === null ? '—' : `${nilai}${satuan}`);

	/*
	 * Tren: satu grafik batang, sumbu X tetap Januari–Desember, satu seri per
	 * tahun (tahun terpilih + paling banyak tiga tahun sebelumnya).
	 *
	 * Sumbu bulan yang tetap itu yang membuat grafiknya bisa dipakai
	 * membandingkan tahun — pada bentuk "12 bulan terakhir", Maret satu tahun
	 * tidak pernah berdiri sejajar dengan Maret tahun lain.
	 */
	const seriTren = tren.tahun_dibanding.map((th) => ({
		name: String(th),
		data: tren.bulanan.map((baris) => baris.tahun[String(th)]?.[seri] ?? 0)
	}));

	const opsiTren: ApexOptions = {
		chart: {
			type: 'bar',
			fontFamily: 'inherit',
			foreColor: 'inherit',
			toolbar: { show: false },
			animations: { enabled: false }
		},
		// Tahun terpilih selalu seri pertama, jadi warna paling pekat jatuh ke
		// tahun yang sedang dibaca.
		colors: [
			theme.palette.primary.main,
			theme.palette.secondary.main,
			theme.palette.warning.main,
			theme.palette.info.main
		],
		plotOptions: { bar: { columnWidth: '70%', borderRadius: 2 } },
		dataLabels: { enabled: false },
		grid: { borderColor: theme.palette.divider, strokeDashArray: 3 },
		legend: { position: 'top', horizontalAlign: 'left' },
		tooltip: { theme: theme.palette.mode },
		xaxis: { categories: tren.bulanan.map((baris) => baris.label) },
		// Jumlah permohonan selalu bilangan bulat; tanpa ini sumbu Y bisa
		// menampilkan 0,5 permohonan saat angkanya masih kecil.
		yaxis: { min: 0, forceNiceScale: true, labels: { formatter: (nilai) => String(Math.round(nilai)) } },
		noData: { text: t('Belum ada data.') }
	};

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

			{/* --- 1. Pemohon: siapa yang memakai layanan --- */}
			<div className="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
				<KartuAngka
					label={t('Total pemohon')}
					nilai={pemohon.total}
					keterangan={`${Object.keys(pemohon.per_jenis ?? {}).length} ${t('jenis pemohon')}`}
					ikon="lucide:users"
					warna="text-blue-500"
				/>
				<KartuAngka
					label={t('Sudah diverifikasi')}
					nilai={pemohon.verifikasi.terverifikasi}
					keterangan={t('Data dirinya disetujui petugas')}
					ikon="lucide:user-check"
					warna="text-green-500"
				/>
				<KartuAngka
					label={t('Menunggu diverifikasi')}
					nilai={pemohon.verifikasi.menunggu}
					keterangan={`${pemohon.verifikasi.ditolak} ${t('berkas ditolak')}`}
					ikon="lucide:user-search"
					warna={pemohon.verifikasi.menunggu > 0 ? 'text-amber-500' : 'text-green-500'}
				/>
				<KartuAngka
					label={t('Belum verifikasi data')}
					nilai={pemohon.verifikasi.belum}
					keterangan={t('Belum mengirim berkas sama sekali')}
					ikon="lucide:user-x"
					warna="text-slate-500"
				/>
			</div>

			{/* --- 2. Beban kerja layanan --- */}
			<div className="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
				<KartuAngka
					label={t('Permohonan Informasi')}
					nilai={ringkasan.permohonan}
					keterangan={`${ringkasan.perlu_tindakan} ${t('menunggu tindakan')}`}
					ikon="lucide:inbox"
					warna="text-blue-500"
				/>
				<KartuAngka
					label={t('Keberatan Informasi')}
					nilai={ringkasan.keberatan}
					keterangan={`${ringkasan.keberatan_belum_selesai} ${t('belum selesai')}`}
					ikon="lucide:scale"
					warna="text-purple-500"
				/>
				<KartuAngka
					label={t('Menunggu persetujuan')}
					nilai={ringkasan.menunggu_approval}
					keterangan={t('Diproses — tinggal putusan PPID')}
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
			</div>

			{/* --- 3. Sebaran pemohon & pengajuan --- */}
			<div className="grid grid-cols-1 gap-4 lg:grid-cols-3">
				<Paper
					elevation={0}
					className="rounded-lg border border-divider p-4"
				>
					<Typography className="mb-1 font-medium">{t('Pemohon per jenis')}</Typography>
					<Typography
						variant="caption"
						color="text.secondary"
						className="mb-3 block"
					>
						{t('Dari')} {pemohon.total} {t('pemohon terdaftar')}
					</Typography>

					<DaftarSebaran
						data={pemohon.per_jenis}
						label={(kunci) => t(LABEL_JENIS[kunci] ?? kunci)}
						kosong={t('Belum ada pemohon terdaftar.')}
					/>
				</Paper>

				<Paper
					elevation={0}
					className="rounded-lg border border-divider p-4"
				>
					<Typography className="mb-3 font-medium">{t('Status Permohonan Informasi')}</Typography>
					<DaftarSebaran
						data={analisa.per_status.permohonan}
						label={(kunci) => t(LABEL_STATUS[kunci] ?? kunci)}
						kosong={t('Belum ada permohonan.')}
					/>
				</Paper>

				<Paper
					elevation={0}
					className="rounded-lg border border-divider p-4"
				>
					<Typography className="mb-3 font-medium">{t('Status Keberatan Informasi')}</Typography>
					<DaftarSebaran
						data={analisa.per_status.keberatan}
						label={(kunci) => t(LABEL_STATUS[kunci] ?? kunci)}
						kosong={t('Belum ada keberatan.')}
						warna="secondary"
					/>
				</Paper>
			</div>

			{/* --- 4. Pengajuan menurut jenis pemohonnya --- */}
			<div className="grid grid-cols-1 gap-4 lg:grid-cols-2">
				<Paper
					elevation={0}
					className="rounded-lg border border-divider p-4"
				>
					<Typography className="mb-1 font-medium">
						{t('Permohonan Informasi per jenis pemohon')}
					</Typography>
					<Typography
						variant="caption"
						color="text.secondary"
						className="mb-3 block"
					>
						{t('Dihitung dari pemohon pada tiap permohonan, bukan dari jumlah pemohonnya.')}
					</Typography>

					<DaftarSebaran
						data={analisa.per_jenis_pemohon.permohonan}
						label={(kunci) => t(LABEL_JENIS[kunci] ?? kunci)}
						kosong={t('Belum ada permohonan.')}
					/>
				</Paper>

				<Paper
					elevation={0}
					className="rounded-lg border border-divider p-4"
				>
					<Typography className="mb-1 font-medium">{t('Keberatan Informasi per jenis pemohon')}</Typography>
					<Typography
						variant="caption"
						color="text.secondary"
						className="mb-3 block"
					>
						{t('Dihitung dari pemohon pada tiap keberatan, bukan dari jumlah pemohonnya.')}
					</Typography>

					<DaftarSebaran
						data={analisa.per_jenis_pemohon.keberatan}
						label={(kunci) => t(LABEL_JENIS[kunci] ?? kunci)}
						kosong={t('Belum ada keberatan.')}
						warna="secondary"
					/>
				</Paper>
			</div>

			{/* --- 4b. Alasan keberatan --- */}
			<Paper
				elevation={0}
				className="border-divider rounded-lg border p-4"
			>
				<Typography className="mb-1 font-medium">{t('Alasan Keberatan Informasi')}</Typography>
				<Typography
					variant="caption"
					color="text.secondary"
					className="mb-3 block"
				>
					{t(
						'Tujuh dasar keberatan menurut Pasal 35 UU KIP. Angka ini menunjuk sebab layanan dinilai gagal, bukan sekadar berapa banyak yang gagal.'
					)}
				</Typography>

				<DaftarSebaran
					data={Object.fromEntries(analisa.alasan_keberatan.map((a) => [a.kode, a.jumlah]))}
					label={(kunci) => t(JENIS_KEBERATAN[kunci] ?? kunci)}
					kosong={t('Belum ada keberatan.')}
					warna="secondary"
				/>
			</Paper>

			{/* --- 5. Kondisi konten --- */}
			<div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
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
			</div>

			{/* --- 6. Perlu tindakan segera — sengaja disorot: ini satu-satunya
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

			{/* --- 7. Kepatuhan SLA --- */}
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
						{t('Keberatan')} {sla.target_keberatan_hari} {t('hari kalender')}
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

			{/* --- 8. Capaian KPI --- */}
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

			{/* --- 9. Tren bulanan: masuk vs ditanggapi --- */}
			<div className="grid grid-cols-1 gap-4">
				<Paper
					elevation={0}
					className="rounded-lg border border-divider p-4"
				>
					<div className="mb-3 flex flex-wrap items-center justify-between gap-2">
						<div>
							<Typography className="font-medium">{t('Permohonan masuk vs ditanggapi')}</Typography>
							<Typography
								variant="caption"
								color="text.secondary"
							>
								{t('Januari–Desember')} · {tren.tahun_dibanding.length} {t('tahun dibandingkan')}
							</Typography>
						</div>

						{/* Keduanya dilihat pada grafik yang sama, bergantian —
						    menggambar masuk dan ditanggapi sekaligus untuk empat
						    tahun berarti delapan batang per bulan, dan tidak ada
						    yang bisa dibaca dari sana. */}
						<TextField
							select
							size="small"
							label={t('Data')}
							value={seri}
							onChange={(event) => setSeri(event.target.value as 'masuk' | 'ditanggapi')}
							sx={{ minWidth: 160 }}
						>
							<MenuItem value="masuk">{t('Masuk')}</MenuItem>
							<MenuItem value="ditanggapi">{t('Ditanggapi')}</MenuItem>
						</TextField>
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

					<ReactApexChart
						options={opsiTren}
						series={seriTren}
						type="bar"
						height={340}
					/>

					<Typography
						variant="caption"
						color="text.secondary"
						className="mt-2 block"
					>
						{t('Satu batang per tahun di tiap bulan, jadi bulan yang sama antar tahun berdiri sejajar. Ganti pilihan Data untuk melihat permohonan yang ditanggapi.')}
					</Typography>
				</Paper>
			</div>
		</div>
	);
}

export default PpidDashboard;
