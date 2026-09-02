import { ReactNode } from 'react';
import Chip from '@mui/material/Chip';
import Paper from '@mui/material/Paper';
import Typography from '@mui/material/Typography';
import { useTranslation } from 'react-i18next';
import FuseSvgIcon from '@fuse/core/FuseSvgIcon';
import { labelStatus, warnaStatus, WarnaChip } from '../lib/statusPengajuan';
import { formatWaktu } from '../lib/waktu';
import { urlMedia } from './UploadField';

/**
 * Potongan tampilan yang dipakai bersama oleh rincian Permohonan dan rincian
 * Keberatan.
 *
 * Keduanya menampilkan hal yang sama persis — pasangan label/nilai, daftar
 * berkas, linimasa status — jadi bentuknya disatukan di sini. Kalau disalin,
 * satu perbaikan tampilan hanya sampai ke separuh panel.
 */

/**
 * Satu bagian rincian, berbingkai dan berjudul.
 *
 * Rincian pengajuan sebelumnya satu aliran panjang: judul kecil, isi, garis
 * pemisah, judul kecil lagi (langkah 102). Semua bagian karena itu tampil
 * setara dan tidak ada yang menonjol — petugas harus membaca dari atas tiap
 * kali hanya untuk menemukan satu isian. Kartu memberi tiap bagian batas yang
 * kelihatan, ikon yang bisa dikenali sekilas, dan tempat tetap untuk aksinya.
 */
export function Kartu({
	judul,
	ikon,
	aksi,
	children
}: {
	judul: string;
	ikon: string;
	/** Tombol atau chip di ujung kanan kepala kartu. */
	aksi?: ReactNode;
	children: ReactNode;
}) {
	const { t } = useTranslation();

	return (
		<Paper
			elevation={0}
			className="border-divider overflow-hidden rounded-xl border"
		>
			<div className="border-divider bg-default flex items-center gap-2 border-b px-4 py-2.5">
				<FuseSvgIcon
					size={16}
					color="action"
				>
					{ikon}
				</FuseSvgIcon>
				<Typography
					variant="subtitle2"
					className="flex-auto font-semibold"
				>
					{t(judul)}
				</Typography>
				{aksi}
			</div>

			<div className="p-4">{children}</div>
		</Paper>
	);
}

/**
 * Angka-angka pokok berkas dalam satu baris, di kepala rincian.
 *
 * Yang dicari petugas lebih dulu — kapan masuk, kapan tenggatnya, lewat jalur
 * apa, siapa yang memegang — dulu tersebar di dalam blok "Penanganan" di
 * tengah halaman, bercampur dengan isian yang jarang dibaca. Di sini keempatnya
 * berdiri sendiri dan terbaca sebelum petugas menggulung apa pun.
 */
export function Ringkasan({ isi }: { isi: { label: string; nilai: unknown; ikon: string }[] }) {
	const { t } = useTranslation();

	return (
		<div className="border-divider grid grid-cols-2 gap-px overflow-hidden rounded-xl border bg-transparent sm:grid-cols-4">
			{isi.map((satu) => (
				<div
					key={satu.label}
					className="bg-paper flex flex-col gap-0.5 px-4 py-3"
				>
					<div className="text-secondary flex items-center gap-1.5">
						<FuseSvgIcon size={14}>{satu.ikon}</FuseSvgIcon>
						<Typography
							variant="caption"
							color="text.secondary"
						>
							{t(satu.label)}
						</Typography>
					</div>
					<Typography
						variant="body2"
						className="font-semibold"
					>
						{satu.nilai === null || satu.nilai === undefined || satu.nilai === ''
							? '—'
							: String(satu.nilai)}
					</Typography>
				</div>
			))}
		</div>
	);
}

export function Baris({ label, nilai }: { label: string; nilai: unknown }) {
	const { t } = useTranslation();
	const teks = nilai === null || nilai === undefined || nilai === '' ? '—' : String(nilai);

	return (
		<div>
			<Typography
				variant="caption"
				color="text.secondary"
				className="block"
			>
				{t(label)}
			</Typography>
			<Typography
				variant="body2"
				className="whitespace-pre-line"
			>
				{teks}
			</Typography>
		</div>
	);
}

type Berkas = { id?: number; nama_file?: string; path_file?: string };

/**
 * Berkas dibuka di tab baru lewat URL media situs, sama seperti kolom berkas
 * pada tabel modul — bukan diunduh lewat token panel. Lampiran permohonan
 * bukan dokumen identitas; yang disajikan di belakang token hanya KTP pemohon.
 */
export function DaftarBerkas({ berkas }: { berkas: unknown }) {
	const { t } = useTranslation();
	const daftar = Array.isArray(berkas) ? (berkas as Berkas[]) : [];

	if (daftar.length === 0) {
		return (
			<Typography
				variant="body2"
				color="text.secondary"
			>
				{t('Tidak ada berkas.')}
			</Typography>
		);
	}

	return (
		<div className="flex flex-wrap gap-2">
			{daftar.map((file, index) => (
				<Chip
					key={file.id ?? index}
					size="small"
					variant="outlined"
					component="a"
					clickable
					href={urlMedia(String(file.path_file ?? ''))}
					target="_blank"
					rel="noreferrer noopener"
					icon={<FuseSvgIcon size={14}>lucide:paperclip</FuseSvgIcon>}
					label={file.nama_file || t('Berkas')}
				/>
			))}
		</div>
	);
}

type BarisRiwayat = {
	id?: number;
	status_sebelumnya?: string | null;
	status_baru?: string;
	catatan?: string | null;
	created_at?: string | null;
	petugas?: { name?: string } | null;
};

/**
 * Linimasa perpindahan status.
 *
 * Catatannya keterangan **internal** petugas — tidak pernah dikirim ke
 * pemohon — jadi hanya muncul di sini, bukan di portal.
 */
export function RiwayatStatus({
	riwayat,
	peta
}: {
	riwayat: unknown;
	peta: Record<string, { label: string; warna: WarnaChip }>;
}) {
	const { t } = useTranslation();
	const daftar = Array.isArray(riwayat) ? (riwayat as BarisRiwayat[]) : [];

	if (daftar.length === 0) {
		return (
			<Typography
				variant="body2"
				color="text.secondary"
			>
				{t('Belum ada perpindahan status.')}
			</Typography>
		);
	}

	return (
		<div className="flex flex-col gap-2">
			{daftar.map((baris, index) => (
				<div
					key={baris.id ?? index}
					className="border-divider flex flex-col gap-1 rounded-lg border p-3"
				>
					<div className="flex flex-wrap items-center gap-2">
						{baris.status_sebelumnya && (
							<>
								<Chip
									size="small"
									variant="outlined"
									label={t(labelStatus(peta, baris.status_sebelumnya))}
								/>
								<FuseSvgIcon size={14}>lucide:arrow-right</FuseSvgIcon>
							</>
						)}
						<Chip
							size="small"
							label={t(labelStatus(peta, baris.status_baru))}
							color={warnaStatus(peta, baris.status_baru)}
						/>
						<Typography
							variant="caption"
							color="text.secondary"
						>
							{formatWaktu(baris.created_at)} · {baris.petugas?.name ?? t('Sistem')}
						</Typography>
					</div>

					{baris.catatan && (
						<Typography
							variant="body2"
							className="whitespace-pre-line"
						>
							{t('Catatan internal')}: {baris.catatan}
						</Typography>
					)}
				</div>
			))}
		</div>
	);
}
