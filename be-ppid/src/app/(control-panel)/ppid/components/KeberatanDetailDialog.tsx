import Dialog from '@mui/material/Dialog';
import DialogTitle from '@mui/material/DialogTitle';
import DialogContent from '@mui/material/DialogContent';
import DialogActions from '@mui/material/DialogActions';
import Button from '@mui/material/Button';
import Alert from '@mui/material/Alert';
import Chip from '@mui/material/Chip';
import Typography from '@mui/material/Typography';
import CircularProgress from '@mui/material/CircularProgress';
import useMediaQuery from '@mui/material/useMediaQuery';
import { useTheme } from '@mui/material/styles';
import { useTranslation } from 'react-i18next';
import FuseSvgIcon from '@fuse/core/FuseSvgIcon';
import { useResourceItem } from '../api/useResource';
import { pemegangGiliran, usePersetujuan } from '../api/usePersetujuan';
import { JENIS_KEBERATAN, labelStatus, STATUS_KEBERATAN, warnaStatus } from '../lib/statusPengajuan';
import { formatWaktu } from '../lib/waktu';
import PersetujuanBerjenjang from './PersetujuanBerjenjang';
import KeberatanTanggapanPanel from './KeberatanTanggapanPanel';
import { Baris, DaftarBerkas, Kartu, Ringkasan } from './RincianPengajuan';

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
	/*
	 * Layar sempit memakai dialog penuh (langkah 102). Dialog `lg` di layar
	 * ponsel menyisakan bingkai tipis di tiap sisi dan memotong isinya dua kali
	 * — sekali oleh dialognya, sekali oleh jendelanya.
	 */
	const layarSempit = useMediaQuery(useTheme().breakpoints.down('md'));
	const { data, isLoading } = useResourceItem<Record<string, unknown>>(
		'keberatan',
		open && keberatanId ? keberatanId : undefined
	);

	const status = String(data?.status ?? '');
	const { data: persetujuan } = usePersetujuan('keberatan', Number(keberatanId ?? 0), open && Boolean(keberatanId));
	const alurBerjalan = Boolean(persetujuan?.berjalan_id);
	const pemegang = pemegangGiliran(persetujuan);
	const giliranSaya = Boolean(persetujuan?.boleh_memutus) && bolehSetujui;
	const pemohon = data?.pemohon as Record<string, unknown> | null;
	const permohonan = data?.permohonan as Record<string, unknown> | null;
	const petugas = data?.petugas as { name?: string } | null;

	return (
		<Dialog
			open={open}
			onClose={onClose}
			fullWidth
			fullScreen={layarSempit}
			maxWidth="lg"
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

						{/* Sama dengan rincian permohonan: giliran diumumkan di
						    kepala berkas, bukan hanya di panel jenjang yang
						    berada jauh di bawah (langkah 100). */}
						{giliranSaya ? (
							<Alert
								severity="warning"
								icon={<FuseSvgIcon size={20}>lucide:stamp</FuseSvgIcon>}
							>
								{t('Giliran Anda memutus berkas ini pada tahap')} <strong>{pemegang}</strong>.{' '}
								{t('Putusannya dikirim dari panel Verifikasi Permohonan.')}
							</Alert>
						) : (
							pemegang !== null && (
								<Alert severity="info">
									{t('Menunggu putusan tahap')} <strong>{pemegang}</strong>.
								</Alert>
							)
						)}

						<Alert severity="info">
							{t(
								'Nomor keberatan (KBT-FSTJ/…) berdiri sendiri, terpisah dari nomor permohonan yang dikeberatankan — keduanya berkas dengan tenggat yang berbeda.'
							)}
						</Alert>

						{/*
						 * Ringkasan lalu dua kolom kartu, sama susunannya dengan
						 * rincian permohonan (langkah 102) — dua modul yang isinya
						 * sejenis tidak boleh menuntut dua kebiasaan membaca.
						 */}
						<Ringkasan
							isi={[
								{
									label: 'Diajukan',
									ikon: 'lucide:calendar-plus',
									nilai: formatWaktu(data.tanggal_keberatan)
								},
								{
									label: 'Batas waktu tanggapan',
									ikon: 'lucide:timer',
									nilai: formatWaktu(data.batas_waktu_tanggapan)
								},
								{
									label: 'Batas ajukan sengketa',
									ikon: 'lucide:gavel',
									nilai: formatWaktu(data.batas_waktu_sengketa)
								},
								{ label: 'Petugas', ikon: 'lucide:user-check', nilai: petugas?.name }
							]}
						/>

						<div className="grid grid-cols-1 items-start gap-4 lg:grid-cols-5">
							<div className="flex flex-col gap-4 lg:sticky lg:top-0 lg:col-span-2">
								<Kartu
									judul="Verifikasi Permohonan"
									ikon="lucide:stamp"
								>
									<PersetujuanBerjenjang
										modul="keberatan"
										pengajuanId={Number(keberatanId)}
										bolehSetujui={bolehSetujui}
									/>
								</Kartu>

								<Kartu
									judul="Tanggapan & Status"
									ikon="lucide:message-square-reply"
								>
									<KeberatanTanggapanPanel
										keberatanId={Number(keberatanId)}
										status={status}
										tanggapanAwal={String(data.tanggapan_atasan_ppid ?? '')}
										bolehUbah={bolehUbah}
										alurBerjalan={alurBerjalan}
									/>
								</Kartu>
							</div>

							<div className="flex flex-col gap-4 lg:col-span-3">
								<Kartu
									judul="Isi Keberatan"
									ikon="lucide:scale"
									aksi={
										data.dikuasakan ? (
											<Chip
												size="small"
												variant="outlined"
												label={t('Dikuasakan')}
											/>
										) : undefined
									}
								>
									<div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
										<div className="sm:col-span-2">
											<Baris
												label="Jenis keberatan"
												nilai={t(
													JENIS_KEBERATAN[String(data.jenis_keberatan ?? '')] ??
														String(data.jenis_keberatan ?? '')
												)}
											/>
										</div>
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

									<div className="mt-4 flex flex-col gap-1.5">
										<Typography
											variant="caption"
											color="text.secondary"
										>
											{t('Berkas lampiran pemohon')}
										</Typography>
										<DaftarBerkas berkas={data.files} />
									</div>
								</Kartu>

								<Kartu
									judul="Permohonan Terkait"
									ikon="lucide:file-text"
								>
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
								</Kartu>

								<Kartu
									judul="Pemohon"
									ikon="lucide:user"
								>
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
								</Kartu>

								<Kartu
									judul="Pelayanan & Tanggapan"
									ikon="lucide:route"
								>
									<div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
										<Baris
											label="Jalur pelayanan"
											nilai={t(LABEL_JALUR[String(data.jalur_pelayanan ?? '')] ?? '')}
										/>
										<Baris
											label="Jadwal layanan"
											nilai={formatWaktu(data.jadwal_layanan)}
										/>
										<Baris
											label="Tanggal tanggapan"
											nilai={formatWaktu(data.tanggal_tanggapan)}
										/>
										<div className="sm:col-span-2">
											<Baris
												label="Keterangan petugas untuk pemohon"
												nilai={data.keterangan_petugas}
											/>
										</div>
									</div>

									{Boolean(data.tanggapan_atasan_ppid) && (
										<Alert
											severity="success"
											className="mt-4"
										>
											{t('Tanggapan atasan PPID')}: {String(data.tanggapan_atasan_ppid)}
										</Alert>
									)}

									{/*
									 * Dua tenggat dengan satuan berbeda, dan itu bukan
									 * kelalaian: tanggapan dihitung 30 hari kalender
									 * sejak keberatan diregistrasi, sedangkan batas
									 * pemohon membawa perkara ke Komisi Informasi 14
									 * hari kerja sejak tanggapan diterima.
									 */}
									<Alert
										severity="info"
										className="mt-4"
									>
										{t(
											'Tanggapan keberatan paling lambat 30 hari kalender sejak diregistrasi. Bila pemohon tidak puas, sengketa informasi dapat diajukan ke Komisi Informasi paling lambat 14 hari kerja setelah tanggapan diterima.'
										)}
									</Alert>
								</Kartu>
							</div>
						</div>
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
