import Dialog from '@mui/material/Dialog';
import DialogTitle from '@mui/material/DialogTitle';
import DialogContent from '@mui/material/DialogContent';
import DialogActions from '@mui/material/DialogActions';
import Button from '@mui/material/Button';
import Alert from '@mui/material/Alert';
import Chip from '@mui/material/Chip';
import Divider from '@mui/material/Divider';
import Typography from '@mui/material/Typography';
import CircularProgress from '@mui/material/CircularProgress';
import { useTranslation } from 'react-i18next';
import FuseSvgIcon from '@fuse/core/FuseSvgIcon';
import { useResourceItem } from '../api/useResource';
import { usePersetujuan } from '../api/usePersetujuan';
import { JENIS_KEBERATAN, labelStatus, STATUS_KEBERATAN, warnaStatus } from '../lib/statusPengajuan';
import { formatWaktu } from '../lib/waktu';
import PersetujuanBerjenjang from './PersetujuanBerjenjang';
import KeberatanTanggapanPanel from './KeberatanTanggapanPanel';
import { Baris, DaftarBerkas, Judul } from './RincianPengajuan';

const LABEL_JALUR: Record<string, string> = {
	online: 'Online — dokumen dikirim lewat email',
	langsung: 'Langsung — pemohon hadir di meja layanan'
};

type KeberatanDetailDialogProps = {
	open: boolean;
	onClose: () => void;
	keberatanId: number | null;
	/** Role pengguna punya hak `Setujui` pada modul Keberatan. */
	bolehSetujui: boolean;
	/** Role pengguna punya hak `Ubah` — menentukan panel tanggapan bisa dipakai. */
	bolehUbah: boolean;
};

/**
 * Rincian satu keberatan.
 *
 * Keberatan tidak punya tabel log status seperti permohonan, jadi linimasanya
 * adalah jenjang persetujuan itu sendiri — tiap tahap membawa siapa memutus,
 * kapan, dan catatannya.
 */
export function KeberatanDetailDialog({
	open,
	onClose,
	keberatanId,
	bolehSetujui,
	bolehUbah
}: KeberatanDetailDialogProps) {
	const { t } = useTranslation();
	const { data, isLoading } = useResourceItem<Record<string, unknown>>(
		'keberatan',
		open && keberatanId ? keberatanId : undefined
	);

	const status = String(data?.status ?? '');
	const { data: persetujuan } = usePersetujuan('keberatan', Number(keberatanId ?? 0), open && Boolean(keberatanId));
	const alurBerjalan = Boolean(persetujuan?.berjalan_id);
	const pemohon = data?.pemohon as Record<string, unknown> | null;
	const permohonan = data?.permohonan as Record<string, unknown> | null;
	const petugas = data?.petugas as { name?: string } | null;

	return (
		<Dialog
			open={open}
			onClose={onClose}
			fullWidth
			maxWidth="md"
			scroll="paper"
		>
			<DialogTitle>{t('Detail Keberatan Informasi')}</DialogTitle>

			<DialogContent
				dividers
				className="flex flex-col gap-5"
			>
				{isLoading || !data ? (
					<div className="flex justify-center py-10">
						<CircularProgress />
					</div>
				) : (
					<>
						<div className="flex flex-wrap items-center gap-3">
							<Typography
								variant="h6"
								className="font-semibold"
							>
								{String(data.kode_keberatan ?? '—')}
							</Typography>
							<Chip
								size="small"
								label={t(labelStatus(STATUS_KEBERATAN, status))}
								color={warnaStatus(STATUS_KEBERATAN, status)}
							/>
						</div>

						<Alert severity="info">
							{t(
								'Nomor keberatan (KBT-FSTJ/…) berdiri sendiri, terpisah dari nomor permohonan yang dikeberatankan — keduanya berkas dengan tenggat yang berbeda.'
							)}
						</Alert>

						<Judul teks="Pemohon" />
						<div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
							<Baris
								label="Nama"
								nilai={pemohon?.nama}
							/>
							<Baris
								label="Email"
								nilai={pemohon?.email}
							/>
							<Baris
								label="No. HP"
								nilai={pemohon?.no_hp}
							/>
							<Baris
								label="Lembaga"
								nilai={pemohon?.nama_lembaga}
							/>
						</div>

						<Divider />

						<Judul teks="Permohonan Terkait" />
						<div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
							<Baris
								label="Kode permohonan"
								nilai={permohonan?.kode_permohonan}
							/>
							<Baris
								label="Status permohonan"
								nilai={permohonan?.status}
							/>
							<div className="sm:col-span-2">
								<Baris
									label="Rincian informasi yang diminta"
									nilai={permohonan?.rincian_informasi}
								/>
							</div>
						</div>

						<Divider />

						<Judul teks="Isi Keberatan" />
						<div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
							<Baris
								label="Jenis keberatan"
								nilai={t(
									JENIS_KEBERATAN[String(data.jenis_keberatan ?? '')] ??
										String(data.jenis_keberatan ?? '')
								)}
							/>
							<Baris
								label="Dikuasakan"
								nilai={data.dikuasakan ? t('Ya') : t('Tidak')}
							/>
							<div className="sm:col-span-2">
								<Baris
									label="Alasan keberatan"
									nilai={data.alasan_keberatan}
								/>
							</div>
							<div className="sm:col-span-2">
								<Baris
									label="Kasus posisi"
									nilai={data.kasus_posisi}
								/>
							</div>
						</div>

						<Divider />

						<Judul teks="Penanganan" />
						<div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
							<Baris
								label="Diajukan"
								nilai={formatWaktu(data.tanggal_keberatan)}
							/>
							<Baris
								label="Tanggal tanggapan"
								nilai={formatWaktu(data.tanggal_tanggapan)}
							/>
							<Baris
								label="Petugas penanggung jawab"
								nilai={petugas?.name}
							/>
							<Baris
								label="Batas waktu tanggapan"
								nilai={formatWaktu(data.batas_waktu_tanggapan)}
							/>
							{/*
							 * Dua tenggat dengan satuan berbeda, dan itu bukan
							 * kelalaian: tanggapan dihitung 30 hari kalender
							 * sejak keberatan diregistrasi, sedangkan batas
							 * pemohon membawa perkara ke Komisi Informasi 14
							 * hari kerja sejak tanggapan diterima.
							 */}
							<Baris
								label="Batas ajukan sengketa"
								nilai={formatWaktu(data.batas_waktu_sengketa)}
							/>
							<Baris
								label="Jalur pelayanan"
								nilai={t(LABEL_JALUR[String(data.jalur_pelayanan ?? '')] ?? '')}
							/>
							<Baris
								label="Jadwal layanan"
								nilai={formatWaktu(data.jadwal_layanan)}
							/>
							<div className="sm:col-span-3">
								<Baris
									label="Keterangan petugas untuk pemohon"
									nilai={data.keterangan_petugas}
								/>
							</div>
						</div>

						<Alert severity="info">
							{t(
								'Tanggapan keberatan paling lambat 30 hari kalender sejak diregistrasi. Bila pemohon tidak puas, sengketa informasi dapat diajukan ke Komisi Informasi paling lambat 14 hari kerja setelah tanggapan diterima.'
							)}
						</Alert>

						{Boolean(data.tanggapan_atasan_ppid) && (
							<Alert severity="success">
								{t('Tanggapan atasan PPID')}: {String(data.tanggapan_atasan_ppid)}
							</Alert>
						)}

						<Divider />

						<Judul teks="Berkas Lampiran Pemohon" />
						<DaftarBerkas berkas={data.files} />

						<Divider />

						<Judul teks="Persetujuan Berjenjang" />
						<PersetujuanBerjenjang
							modul="keberatan"
							pengajuanId={Number(keberatanId)}
							bolehSetujui={bolehSetujui}
						/>

						<Divider />

						<Judul teks="Tanggapan & Status" />
						<KeberatanTanggapanPanel
							keberatanId={Number(keberatanId)}
							status={status}
							tanggapanAwal={String(data.tanggapan_atasan_ppid ?? '')}
							bolehUbah={bolehUbah}
							alurBerjalan={alurBerjalan}
						/>
					</>
				)}
			</DialogContent>

			<DialogActions>
				<Button
					onClick={onClose}
					startIcon={<FuseSvgIcon size={16}>lucide:x</FuseSvgIcon>}
				>
					{t('Tutup')}
				</Button>
			</DialogActions>
		</Dialog>
	);
}

export default KeberatanDetailDialog;
