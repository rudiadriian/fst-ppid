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
import { adaPilihanStatus, labelStatus, STATUS_PERMOHONAN, warnaStatus } from '../lib/statusPengajuan';
import { formatWaktu } from '../lib/waktu';
import PersetujuanBerjenjang from './PersetujuanBerjenjang';
import PermohonanStatusPanel from './PermohonanStatusPanel';
import BerkasTanggapanPanel from './BerkasTanggapanPanel';
import { Baris, DaftarBerkas, Kartu, Ringkasan, RiwayatStatus } from './RincianPengajuan';

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

/** Versi pendek untuk strip ringkasan; yang panjang dipakai di kartunya. */
const LABEL_JALUR_RINGKAS: Record<string, string> = {
	online: 'Online',
	langsung: 'Langsung'
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
	/*
	 * Layar sempit memakai dialog penuh (langkah 102). Dialog `lg` di layar
	 * ponsel menyisakan bingkai tipis di tiap sisi dan memotong isinya dua kali
	 * — sekali oleh dialognya, sekali oleh jendelanya.
	 */
	const layarSempit = useMediaQuery(useTheme().breakpoints.down('md'));
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
	const pemegang = pemegangGiliran(persetujuan);
	const giliranSaya = Boolean(persetujuan?.boleh_memutus) && bolehSetujui;
	/*
	 * Berkas tanggapan hanya boleh disentuh pemegang giliran.
	 *
	 * PPID Pelaksana yang sudah meneruskan berkasnya masih bisa menambah,
	 * menukar, dan membuang lampiran — berkas yang sedang dibaca PPID karena
	 * itu bisa berubah isi di tengah pertimbangannya, dan yang akhirnya
	 * dikirim ke pemohon bukan yang disetujui. Server menjaga aturan yang sama.
	 *
	 * Berkas yang jenjangnya sudah tuntas tetap terbuka: itu jalur melampirkan
	 * dokumen susulan setelah permohonannya diserahkan.
	 */
	const bolehUbahBerkas = !alurBerjalan || Boolean(persetujuan?.boleh_memutus);

	return (
		<Dialog
			open={open}
			onClose={onClose}
			fullWidth
			fullScreen={layarSempit}
			maxWidth="lg"
			scroll="paper"
		>
			{/*
			 * Nomor berkas dan statusnya naik ke judul dialog (langkah 102).
			 * Judul yang cuma berbunyi "Detail Permohonan Informasi" tidak
			 * memberi tahu apa pun tentang berkas yang sedang dibuka, sementara
			 * nomornya — satu-satunya penanda yang dipakai petugas saat bicara
			 * dengan pemohon — dulu tenggelam sebagai baris pertama isi.
			 */}
			<DialogTitle
				component="div"
				className="flex flex-wrap items-center gap-x-3 gap-y-1.5"
			>
				<Typography
					variant="h6"
					className="font-semibold"
				>
					{String(data?.kode_permohonan ?? t('Detail Permohonan Informasi'))}
				</Typography>
				{Boolean(data) && (
					<Chip
						size="small"
						label={t(labelStatus(STATUS_PERMOHONAN, status))}
						color={warnaStatus(STATUS_PERMOHONAN, status)}
					/>
				)}
				{keberatan.length > 0 && (
					<Chip
						size="small"
						variant="outlined"
						color="warning"
						icon={<FuseSvgIcon size={14}>lucide:scale</FuseSvgIcon>}
						label={`${keberatan.length} ${t('keberatan terkait')}`}
					/>
				)}
			</DialogTitle>

			<DialogContent
				dividers
				className="bg-default flex flex-col gap-4"
			>
				{isLoading || !data ? (
					<div className="flex justify-center py-10">
						<CircularProgress />
					</div>
				) : (
					<>
						{/*
						 * Giliran diumumkan di kepala rincian, bukan hanya di
						 * panel jenjang yang berada jauh di bawah (langkah 100).
						 * Petugas yang membuka berkas untuk membacanya tidak
						 * menggulung sampai bawah, dan berkas karena itu diam di
						 * tahap pertama tanpa ada yang merasa ditunggu — persis
						 * keadaan yang membuat PPID tidak pernah kebagian
						 * giliran.
						 */}
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

						{/*
						 * Ringkasan lebih dulu, lalu dua kolom kartu (langkah 102).
						 *
						 * Rincian ini sempat jadi satu aliran judul-kecil-lalu-isi
						 * yang panjang: semua bagian tampil setara, tidak ada yang
						 * menonjol, dan petugas membaca dari atas tiap kali hanya
						 * untuk menemukan satu isian. Sekarang empat angka pokoknya
						 * berdiri di kepala, sisanya berkartu — kiri yang dikerjakan
						 * (putusan, berkas tanggapan, status), kanan yang dibaca. Di
						 * layar sempit keduanya menumpuk dengan kolom kerja lebih
						 * dulu, dan kolom kerja menempel saat kolom kanan digulung.
						 */}
						<Ringkasan
							isi={[
								{
									label: 'Diajukan',
									ikon: 'lucide:calendar-plus',
									nilai: formatWaktu(data.tanggal_permohonan)
								},
								{
									label: 'Batas waktu tanggapan',
									ikon: 'lucide:timer',
									nilai: formatWaktu(data.batas_waktu_tanggapan)
								},
								{
									label: 'Jalur pelayanan',
									ikon: 'lucide:route',
									nilai: t(LABEL_JALUR_RINGKAS[String(data.jalur_pelayanan ?? '')] ?? '')
								},
								{ label: 'Petugas', ikon: 'lucide:user-check', nilai: petugas?.name }
							]}
						/>

						<div className="grid grid-cols-1 items-start gap-4 lg:grid-cols-5">
							<div className="flex flex-col gap-4 lg:sticky lg:top-0 lg:col-span-2">
								{/*
								 * Lampiran tidak lagi berdiri sebagai kartu sendiri
								 * (langkah 102): ia bagian ketiga di dalam Verifikasi
								 * Permohonan, di antara isian petugas dan tombol
								 * putusannya. Urutan kerjanya memang begitu —
								 * melampirkan dokumen dan menulis keterangannya, baru
								 * memutus.
								 */}
								<Kartu
									judul="Verifikasi Permohonan"
									ikon="lucide:stamp"
								>
									<PersetujuanBerjenjang
										modul="permohonan"
										pengajuanId={Number(permohonanId)}
										bolehSetujui={bolehSetujui}
										lampiran={
											<BerkasTanggapanPanel
												permohonanId={Number(permohonanId)}
												berkas={data.tanggapan_files}
												bolehUbah={bolehUbah && bolehUbahBerkas}
												alasanTerkunci={
													bolehUbah && !bolehUbahBerkas
														? 'Berkas ini sudah diteruskan dan sedang menunggu putusan tahap berikutnya. Lampirannya baru bisa diubah lagi bila dikembalikan untuk diperbaiki.'
														: undefined
												}
											/>
										}
									/>
								</Kartu>

								{/*
								 * Kartu Ubah Status hanya dipasang bila memang ada
								 * perpindahan yang bisa dipilih. Kartu yang isinya
								 * cuma keterangan "lanjutkan dari panel di atas"
								 * menambah satu blok tanpa menambah satu tindakan.
								 */}
								{adaPilihanStatus(status, bolehUbah, alurBerjalan, giliranSaya) && (
									<Kartu
										judul="Ubah Status"
										ikon="lucide:refresh-cw"
									>
										<PermohonanStatusPanel
											permohonanId={Number(permohonanId)}
											status={status}
											bolehUbah={bolehUbah}
											alurBerjalan={alurBerjalan}
											giliranSaya={giliranSaya}
										/>
									</Kartu>
								)}
							</div>

							<div className="flex flex-col gap-4 lg:col-span-3">
								<Kartu
									judul="Isi Permohonan"
									ikon="lucide:file-text"
								>
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

									{/* Lampiran pemohon menempel pada isi permohonannya,
									    bukan berdiri sebagai kartu tersendiri: yang
									    dilampirkan adalah bagian dari permintaan itu. */}
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
									judul="Pemohon"
									ikon="lucide:user"
									aksi={
										<Chip
											size="small"
											variant="outlined"
											label={t(
												LABEL_JENIS_PEMOHON[String(pemohon?.jenis_pemohon ?? '')] ??
													String(pemohon?.jenis_pemohon ?? '—')
											)}
										/>
									}
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
								</Kartu>

								{/*
								 * Tiga isian yang ditetapkan jenjang penerima
								 * (langkah 100) berkumpul di satu kartu bersama
								 * tanggal tanggapan dan penanda register publik.
								 * Tanpanya penyetuju memutus tanpa tahu jalur mana
								 * yang dijanjikan ke pemohon, kapan ia diundang, dan
								 * keterangan apa yang sudah dikirimkan.
								 */}
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
										<Baris
											label="Tampil di register publik"
											nilai={data.tampil_di_register_publik ? t('Ya') : t('Tidak')}
										/>
										<div className="sm:col-span-2">
											<Baris
												label="Keterangan petugas untuk pemohon"
												nilai={data.keterangan_petugas}
											/>
										</div>
									</div>

									{Boolean(data.alasan_penolakan) && (
										<Alert
											severity="warning"
											className="mt-4"
										>
											{t('Alasan penolakan')}: {String(data.alasan_penolakan)}
										</Alert>
									)}
								</Kartu>

								<Kartu
									judul="Riwayat Status"
									ikon="lucide:history"
								>
									<RiwayatStatus
										riwayat={data.log_status}
										peta={STATUS_PERMOHONAN}
									/>
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

export default PermohonanDetailDialog;
