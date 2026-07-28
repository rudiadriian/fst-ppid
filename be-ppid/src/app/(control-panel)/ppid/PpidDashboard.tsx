import { useQuery } from '@tanstack/react-query';
import Paper from '@mui/material/Paper';
import Typography from '@mui/material/Typography';
import Alert from '@mui/material/Alert';
import Chip from '@mui/material/Chip';
import LinearProgress from '@mui/material/LinearProgress';
import FuseLoading from '@fuse/core/FuseLoading';
import FuseSvgIcon from '@fuse/core/FuseSvgIcon';
import api from '@/utils/api';

type Ringkasan = {
	data: {
		permohonan: {
			total: number;
			per_status: Record<string, number>;
			perlu_tindakan: number;
			menunggu_approval: number;
			lewat_batas_waktu: number;
		};
		keberatan: { total: number; belum_selesai: number };
		konten: {
			informasi_publik: number;
			informasi_publik_published: number;
			berita: number;
			berita_draft: number;
		};
		kepuasan: { jumlah_responden: number; rata_rating: number | null; persen: number | null };
		tren_permohonan: { bulan: string; jumlah: number }[];
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
			<div className="flex items-center justify-between">
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

/**
 * Dashboard ringkas PPID: beban kerja layanan permohonan dan kondisi konten.
 */
function PpidDashboard() {
	const { data, isLoading, error } = useQuery({
		queryKey: ['ppid', 'dashboard'],
		queryFn: () => api.get('v1/dashboard/ringkasan').json<Ringkasan>(),
		staleTime: 60 * 1000
	});

	if (isLoading) {
		return <FuseLoading />;
	}

	if (error || !data) {
		return (
			<div className="p-6">
				<Alert severity="error">Ringkasan gagal dimuat. Pastikan layanan API sedang berjalan.</Alert>
			</div>
		);
	}

	const { permohonan, keberatan, konten, kepuasan, tren_permohonan: tren } = data.data;
	const trenMaks = Math.max(1, ...tren.map((t) => t.jumlah));

	return (
		<div className="flex w-full flex-col gap-6 p-4 md:p-6">
			<div>
				<Typography
					variant="h5"
					className="font-semibold"
				>
					Dashboard PPID
				</Typography>
				<Typography
					variant="body2"
					color="text.secondary"
				>
					Ringkasan layanan informasi PT Food Station Tjipinang Jaya (Perseroda).
				</Typography>
			</div>

			<div className="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
				<KartuAngka
					label="Total permohonan"
					nilai={permohonan.total}
					keterangan={`${permohonan.perlu_tindakan} menunggu tindakan`}
					ikon="lucide:inbox"
					warna="text-blue-500"
				/>
				<KartuAngka
					label="Menunggu persetujuan"
					nilai={permohonan.menunggu_approval}
					keterangan="Perlu putusan PPID Utama"
					ikon="lucide:clock"
					warna="text-amber-500"
				/>
				<KartuAngka
					label="Lewat batas waktu"
					nilai={permohonan.lewat_batas_waktu}
					keterangan="Permohonan aktif melewati SLA"
					ikon="lucide:triangle-alert"
					warna={permohonan.lewat_batas_waktu > 0 ? 'text-red-500' : 'text-green-500'}
				/>
				<KartuAngka
					label="Keberatan belum selesai"
					nilai={keberatan.belum_selesai}
					keterangan={`dari ${keberatan.total} keberatan`}
					ikon="lucide:scale"
					warna="text-purple-500"
				/>
			</div>

			<div className="grid grid-cols-1 gap-4 lg:grid-cols-3">
				<Paper
					elevation={0}
					className="rounded-lg border border-divider p-4 lg:col-span-2"
				>
					<Typography className="mb-4 font-medium">Permohonan 6 bulan terakhir</Typography>

					{tren.length === 0 ? (
						<Typography
							variant="body2"
							color="text.secondary"
						>
							Belum ada permohonan pada periode ini.
						</Typography>
					) : (
						<div className="flex flex-col gap-3">
							{tren.map((baris) => (
								<div
									key={baris.bulan}
									className="flex items-center gap-3"
								>
									<span className="w-20 shrink-0 text-sm text-secondary">{baris.bulan}</span>
									<LinearProgress
										variant="determinate"
										value={(baris.jumlah / trenMaks) * 100}
										className="h-2 flex-1 rounded"
									/>
									<span className="w-10 shrink-0 text-right text-sm font-medium">
										{baris.jumlah}
									</span>
								</div>
							))}
						</div>
					)}
				</Paper>

				<Paper
					elevation={0}
					className="rounded-lg border border-divider p-4"
				>
					<Typography className="mb-4 font-medium">Sebaran status</Typography>

					<div className="flex flex-wrap gap-2">
						{Object.entries(permohonan.per_status).length === 0 ? (
							<Typography
								variant="body2"
								color="text.secondary"
							>
								Belum ada data.
							</Typography>
						) : (
							Object.entries(permohonan.per_status).map(([status, jumlah]) => (
								<Chip
									key={status}
									size="small"
									label={`${LABEL_STATUS[status] ?? status}: ${jumlah}`}
								/>
							))
						)}
					</div>
				</Paper>
			</div>

			<div className="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
				<KartuAngka
					label="Informasi publik"
					nilai={konten.informasi_publik}
					keterangan={`${konten.informasi_publik_published} sudah terbit`}
					ikon="lucide:file-text"
					warna="text-teal-500"
				/>
				<KartuAngka
					label="Berita"
					nilai={konten.berita}
					keterangan={`${konten.berita_draft} masih draft`}
					ikon="lucide:newspaper"
					warna="text-indigo-500"
				/>
				<KartuAngka
					label="Kepuasan pemohon"
					nilai={kepuasan.persen !== null ? `${kepuasan.persen}%` : '—'}
					keterangan={`${kepuasan.jumlah_responden} responden survei`}
					ikon="lucide:smile"
					warna="text-green-500"
				/>
			</div>
		</div>
	);
}

export default PpidDashboard;
