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
import { labelStatus, STATUS_PERMOHONAN, warnaStatus } from '../lib/statusPengajuan';
import { formatWaktu } from '../lib/waktu';
import PersetujuanBerjenjang from './PersetujuanBerjenjang';
import PermohonanStatusPanel from './PermohonanStatusPanel';
import BerkasTanggapanPanel from './BerkasTanggapanPanel';
import { Baris, DaftarBerkas, Judul, RiwayatStatus } from './RincianPengajuan';

const LABEL_FORMAT: Record<string, string> = {
	softcopy: 'Softcopy',
	hardcopy: 'Hardcopy'
};

const LABEL_KIRIM: Record<string, string> = {
	email: 'Email',
	ambil_langsung: 'Ambil langsung',
	pos: 'Pos'
};

const LABEL_JALUR: Record<string, string> = {
	online: 'Online — dokumen dikirim lewat email',
	langsung: 'Langsung — pemohon hadir di meja layanan'
};

const LABEL_JENIS_PEMOHON: Record<string, string> = {
	perorangan: 'Perorangan',
	mahasiswa: 'Mahasiswa',
	lembaga: 'Lembaga / Organisasi / Perusahaan',
	kelompok: 'Kelompok Orang'
};

type PermohonanDetailDialogProps = {
	open: boolean;
	onClose: () => void;
	permohonanId: number | null;
	/** Role pengguna punya hak `Setujui` pada modul Permohonan. */
	bolehSetujui: boolean;
	/** Role pengguna punya hak `Ubah` — menentukan panel status bisa dipakai. */
	bolehUbah: boolean;
};

/**
 * Rincian satu permohonan, dalam satu layar.
 *
 * Modul ini tidak punya formulir sama sekali (langkah 78): tanpa halaman
 * rincian, isian yang ditulis pemohon — tujuan penggunaan, cara pengiriman,
 * berkas lampiran — tidak punya tempat untuk dibaca petugas selain kolom
 * tabel yang terpotong. Halaman ini menggantikannya, dan sekaligus menampung
 * jenjang persetujuan supaya penyetuju bisa membaca berkasnya sebelum
 * memutuskan, bukan memutuskan dari baris tabel.
 */
export function PermohonanDetailDialog({
	open,
	onClose,
	permohonanId,
	bolehSetujui,
	bolehUbah
}: PermohonanDetailDialogProps) {
	const { t } = useTranslation();
	const { data, isLoading } = useResourceItem<Record<string, unknown>>(
		'permohonan',
		open && permohonanId ? permohonanId : undefined
	);

	const status = String(data?.status ?? '');
	const pemohon = data?.pemohon as Record<string, unknown> | null;
	const kategori = data?.kategori as { nama?: string } | null;
	const petugas = data?.petugas as { name?: string } | null;
	const keberatan = (data?.keberatan as unknown[] | undefined) ?? [];
	const { data: persetujuan } = usePersetujuan(
		'permohonan',
		Number(permohonanId ?? 0),
		open && Boolean(permohonanId)
	);
	const alurBerjalan = Boolean(persetujuan?.berjalan_id);

	return (
		<Dialog
			open={open}
			onClose={onClose}
			fullWidth
			maxWidth="md"
			scroll="paper"
		>
			<DialogTitle>{t('Detail Permohonan Informasi')}</DialogTitle>

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
								{String(data.kode_permohonan ?? '—')}
							</Typography>
							<Chip
								size="small"
								label={t(labelStatus(STATUS_PERMOHONAN, status))}
								color={warnaStatus(STATUS_PERMOHONAN, status)}
							/>
							{keberatan.length > 0 && (
								<Chip
									size="small"
									variant="outlined"
									color="warning"
									label={`${keberatan.length} ${t('keberatan terkait')}`}
								/>
							)}
						</div>

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
								label="Jenis Pemohon"
								nilai={t(
									LABEL_JENIS_PEMOHON[String(pemohon?.jenis_pemohon ?? '')] ??
										String(pemohon?.jenis_pemohon ?? '')
								)}
							/>
							<Baris
								label="Pekerjaan"
								nilai={pemohon?.pekerjaan}
							/>
							<Baris
								label="Lembaga"
								nilai={pemohon?.nama_lembaga}
							/>
							<div className="sm:col-span-2">
								<Baris
									label="Alamat"
									nilai={pemohon?.alamat}
								/>
							</div>
						</div>

						<Divider />

						<Judul teks="Isi Permohonan" />
						<div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
							<div className="sm:col-span-2">
								<Baris
									label="Rincian informasi yang diminta"
									nilai={data.rincian_informasi}
								/>
							</div>
							<div className="sm:col-span-2">
								<Baris
									label="Tujuan penggunaan"
									nilai={data.tujuan_penggunaan}
								/>
							</div>
							<Baris
								label="Kategori informasi"
								nilai={kategori?.nama}
							/>
							<Baris
								label="Cara memperoleh"
								nilai={data.cara_memperoleh}
							/>
							<Baris
								label="Format informasi"
								nilai={t(LABEL_FORMAT[String(data.format_informasi ?? '')] ?? '')}
							/>
							<Baris
								label="Cara pengiriman"
								nilai={t(LABEL_KIRIM[String(data.cara_pengiriman ?? '')] ?? '')}
							/>
						</div>

						<Divider />

						<Judul teks="Penanganan" />
						<div className="grid grid-cols-1 gap-4 sm:grid-cols-3">
							<Baris
								label="Diajukan"
								nilai={formatWaktu(data.tanggal_permohonan)}
							/>
							<Baris
								label="Batas waktu tanggapan"
								nilai={formatWaktu(data.batas_waktu_tanggapan)}
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
								label="Tampil di register publik"
								nilai={data.tampil_di_register_publik ? t('Ya') : t('Tidak')}
							/>
							{/*
							 * Tiga isian yang ditetapkan jenjang penerima
							 * (langkah 100). Tanpanya penyetuju di atasnya
							 * memutus tanpa tahu jalur mana yang dijanjikan
							 * ke pemohon, kapan ia diundang, dan keterangan
							 * apa yang sudah dikirimkan.
							 */}
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

						{Boolean(data.alasan_penolakan) && (
							<Alert severity="warning">
								{t('Alasan penolakan')}: {String(data.alasan_penolakan)}
							</Alert>
						)}

						<Divider />

						<Judul teks="Berkas Lampiran Pemohon" />
						<DaftarBerkas berkas={data.files} />

						<Judul teks="Berkas Tanggapan Petugas" />
						<BerkasTanggapanPanel
							permohonanId={Number(permohonanId)}
							berkas={data.tanggapanFiles}
							bolehUbah={bolehUbah}
						/>

						<Divider />

						<Judul teks="Persetujuan Berjenjang" />
						<PersetujuanBerjenjang
							modul="permohonan"
							pengajuanId={Number(permohonanId)}
							bolehSetujui={bolehSetujui}
						/>

						<Divider />

						<Judul teks="Ubah Status" />
						<PermohonanStatusPanel
							permohonanId={Number(permohonanId)}
							status={status}
							bolehUbah={bolehUbah}
							alurBerjalan={alurBerjalan}
						/>

						<Divider />

						<Judul teks="Riwayat Status" />
						<RiwayatStatus
							riwayat={data.logStatus}
							peta={STATUS_PERMOHONAN}
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

export default PermohonanDetailDialog;
