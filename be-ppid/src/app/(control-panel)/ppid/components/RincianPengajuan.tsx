import Chip from '@mui/material/Chip';
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

export function Judul({ teks }: { teks: string }) {
	const { t } = useTranslation();

	return (
		<Typography
			variant="subtitle2"
			className="font-semibold"
			color="text.secondary"
		>
			{t(teks)}
		</Typography>
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
