import { ReactNode, useState } from 'react';
import Accordion from '@mui/material/Accordion';
import AccordionDetails from '@mui/material/AccordionDetails';
import AccordionSummary from '@mui/material/AccordionSummary';
import Alert from '@mui/material/Alert';
import Button from '@mui/material/Button';
import Chip from '@mui/material/Chip';
import CircularProgress from '@mui/material/CircularProgress';
import MenuItem from '@mui/material/MenuItem';
import TextField from '@mui/material/TextField';
import Typography from '@mui/material/Typography';
import { useQueryClient } from '@tanstack/react-query';
import { useSnackbar } from 'notistack';
import { useTranslation } from 'react-i18next';
import FuseSvgIcon from '@fuse/core/FuseSvgIcon';
import ppidApi, { PpidApiError } from '../api/ppidApi';
import { resourceKeys } from '../api/useResource';
import { kunciPersetujuan, langkahBerjalan, LangkahPersetujuan, usePersetujuan } from '../api/usePersetujuan';
import { STATUS_LANGKAH } from '../lib/statusPengajuan';
import { formatWaktu } from '../lib/waktu';

/**
 * Keterangan meja layanan, ditampilkan saat menjadwalkan kunjungan pemohon.
 *
 * Ditulis di sini dan bukan diambil dari Pengaturan Situs karena petugas
 * membacanya untuk disalin ke undangan, bukan untuk ditayangkan — dan alamat
 * yang salah pada undangan jauh lebih merugikan daripada alamat yang tertinggal
 * satu versi di halaman kontak.
 */
const MEJA_LAYANAN = [
	'Komplek Pasar Induk Beras Cipinang, Jl. Pisangan Lama Selatan No. 1, Jakarta Timur 13230',
	'ppid@foodstation.co.id · (021) 4718011 (Ext. PPID)',
	'Senin–Jumat pukul 08.00–15.00 WIB, istirahat 12.00–13.00 WIB'
];

/**
 * Satu tahap sebagai baris garis waktu, bukan kartu berbingkai.
 *
 * Rincian permohonan sudah panjang; jenjangnya dulu menambah satu kartu
 * berbingkai per tahap, masing-masing dengan ikon, dua chip, dan tiga baris
 * keterangan — empat tahap berarti empat kotak yang menuntut perhatian sama
 * besar dengan berkas dan formulir putusannya (langkah 100). Isinya sama
 * persis; yang dilepas hanya bingkai, ikon berwarna, dan chip "Giliran
 * sekarang" — giliran sudah ditandai titik dan tebalnya baris, dan diumumkan
 * di kepala rincian.
 */
function SatuLangkah({ langkah, berjalan }: { langkah: LangkahPersetujuan; berjalan: boolean }) {
	const { t } = useTranslation();
	const info = STATUS_LANGKAH[langkah.status] ?? STATUS_LANGKAH.dilewati;
	const telatnya = berjalan && langkah.batas_waktu && new Date(langkah.batas_waktu).getTime() < Date.now();

	const pemegang = [langkah.nama_jabatan, langkah.role?.name ?? t('Role belum ditetapkan')]
		.filter(Boolean)
		.join(' · ');

	const waktu = langkah.tanggal_putusan
		? `${t('Diputus')} ${formatWaktu(langkah.tanggal_putusan)}${langkah.pemutus?.name ? ` · ${langkah.pemutus.name}` : ''}`
		: langkah.tanggal_masuk
			? `${t('Masuk')} ${formatWaktu(langkah.tanggal_masuk)}${
					langkah.batas_waktu ? ` · ${t('batas')} ${formatWaktu(langkah.batas_waktu)}` : ''
				}`
			: t('Belum tiba gilirannya');

	return (
		<div className="flex gap-2.5 py-1.5">
			{/* Titik penanda, sekaligus ruas garis waktunya. */}
			<div className="flex flex-col items-center pt-1.5">
				<span
					className={`h-2 w-2 shrink-0 rounded-full ${
						berjalan ? 'bg-secondary-main ring-secondary-main/25 ring-3' : 'bg-divider'
					}`}
				/>
			</div>

			<div className="flex min-w-0 flex-auto flex-col">
				<div className="flex flex-wrap items-baseline gap-x-2 gap-y-0.5">
					<Typography
						variant="body2"
						className={berjalan ? 'font-semibold' : ''}
					>
						{langkah.urutan}. {langkah.nama_tahap}
					</Typography>
					<Typography
						variant="caption"
						color="text.secondary"
					>
						{pemegang}
					</Typography>
					<Chip
						size="small"
						variant="outlined"
						label={t(info.label)}
						color={info.warna}
					/>
					{telatnya && (
						<Chip
							size="small"
							color="error"
							variant="outlined"
							label={t('Lewat batas waktu')}
						/>
					)}
				</div>

				<Typography
					variant="caption"
					color="text.secondary"
				>
					{waktu}
				</Typography>

				{langkah.catatan && (
					<Typography
						variant="caption"
						className="mt-0.5 whitespace-pre-line"
					>
						{langkah.catatan}
					</Typography>
				)}
			</div>
		</div>
	);
}

/**
 * Judul kecil pemisah bagian di dalam kartu Verifikasi Permohonan.
 *
 * Kartu ini menampung empat hal berbeda — alur, isian petugas, lampiran, dan
 * tombol putusan (langkah 102). Tanpa penanda bagian keempatnya mengalir jadi
 * satu tumpukan kolom yang menuntut petugas mengira-ira di mana satu urusan
 * berakhir dan urusan berikutnya mulai.
 */
function Bagian({ judul, children }: { judul: string; children: ReactNode }) {
	const { t } = useTranslation();

	return (
		<section className="flex flex-col gap-2">
			<Typography
				variant="caption"
				color="text.secondary"
				className="font-semibold tracking-wide uppercase"
			>
				{t(judul)}
			</Typography>
			{children}
		</section>
	);
}

type PersetujuanBerjenjangProps = {
	/** Segmen API modulnya: `permohonan` atau `keberatan`. */
	modul: 'permohonan' | 'keberatan';
	pengajuanId: number;
	/** Role pengguna punya hak `Setujui` pada modul ini. */
	bolehSetujui: boolean;
	/**
	 * Panel lampiran, disisipkan sebagai bagian ketiga.
	 *
	 * Diserahkan dari luar, bukan dibangun di sini: berkas tanggapan punya
	 * aturan unggah, arsip, dan penguncian sendiri yang tidak ada hubungannya
	 * dengan jenjang persetujuan. Yang ditentukan komponen ini cuma tempatnya —
	 * setelah isian petugas, sebelum tombol putusan (langkah 102).
	 */
	lampiran?: ReactNode;
};

/**
 * Jenjang persetujuan satu pengajuan, sekaligus tempat memutuskannya.
 *
 * Yang diputus selalu tahap yang sedang berjalan — tidak ada tombol per baris.
 * Jenjang berarti berurutan, dan memberi pilihan memutus tahap lain hanya
 * menawarkan sesuatu yang pasti ditolak server.
 *
 * `boleh_memutus` datang dari server, bukan dihitung dari role di klien:
 * jawabannya harus persis sama dengan aturan yang dipakai saat putusannya
 * dikirim, dan aturan itu hanya ada di satu tempat.
 */
export function PersetujuanBerjenjang({ modul, pengajuanId, bolehSetujui, lampiran }: PersetujuanBerjenjangProps) {
	const { t } = useTranslation();
	const queryClient = useQueryClient();
	const { enqueueSnackbar } = useSnackbar();

	const [catatan, setCatatan] = useState('');
	const [menyimpan, setMenyimpan] = useState(false);
	// Isian jalur pelayanan; hanya dipakai jenjang penerima (langkah 89).
	const [jalur, setJalur] = useState('');
	const [jadwal, setJadwal] = useState('');
	const [keteranganPetugas, setKeteranganPetugas] = useState('');

	const { data, isLoading, error } = usePersetujuan(modul, pengajuanId);

	if (isLoading) {
		return (
			<div className="flex justify-center py-6">
				<CircularProgress size={24} />
			</div>
		);
	}

	if (error) {
		return (
			<Alert severity="error">
				{error instanceof PpidApiError ? error.message : t('Jenjang persetujuan gagal dimuat.')}
			</Alert>
		);
	}

	const langkah = data?.langkah ?? [];

	if (langkah.length === 0) {
		return (
			<Alert severity="info">
				{t(
					'Alur persetujuan untuk jenis pengajuan ini belum disusun, jadi tidak ada jenjang yang bisa dijalankan. Super admin dapat menyusunnya lewat modul Alur Persetujuan.'
				)}
			</Alert>
		);
	}

	// Petugas boleh memutus hanya bila server mengizinkan **dan** rolenya punya
	// hak Setujui pada modulnya. Keduanya diperiksa: yang pertama menentukan
	// giliran, yang kedua menentukan wewenang.
	const bolehKirim = Boolean(data?.boleh_memutus) && bolehSetujui;

	/*
	 * Jenjang penerima dikenali dari haknya, bukan dari namanya: tahap yang
	 * tidak diberi hak menolak adalah tahap yang tugasnya meneruskan, dan
	 * dialah yang berhubungan dengan pemohon. Namanya bisa diubah super admin
	 * kapan saja; haknya menentukan perannya.
	 */
	const berjalan = langkahBerjalan(data);
	const jenjangPenerima = berjalan?.tahap ? !berjalan.tahap.boleh_tolak : false;
	const perluJadwal = jenjangPenerima && jalur === 'langsung';
	const isianJalurLengkap = !jenjangPenerima || (jalur !== '' && (jalur !== 'langsung' || jadwal !== ''));
	const adaCatatan = catatan.trim() !== '';

	/**
	 * Kirim satu putusan.
	 *
	 * Putusannya datang dari tombol yang ditekan, bukan dari dropdown yang
	 * dipilih lebih dulu (langkah 102). Dropdown menyembunyikan dua dari tiga
	 * pilihan di balik satu klik tambahan, dan tombol kirimnya lalu harus
	 * berbunyi netral — petugas menekan "Kirim putusan" tanpa tulisan apa pun
	 * yang menyebut bahwa perkara pemohon akan ditutup.
	 */
	async function kirim(keputusan: string) {
		// Dijaga di sini juga, bukan cuma lewat tombol yang mati: alasan wajib
		// ada pada penolakan dan permintaan perbaikan, dan keduanya dibaca orang
		// lain — pemohon pada penolakan, petugas pada revisi.
		if (keputusan !== 'disetujui' && !adaCatatan) {
			enqueueSnackbar(t('Catatan wajib diisi untuk Tolak dan Revisi.'), { variant: 'warning' });
			return;
		}

		setMenyimpan(true);

		try {
			await ppidApi.action(`${modul}/${pengajuanId}/approval`, {
				keputusan,
				catatan: catatan || undefined,
				// Hanya dikirim saat memang ditetapkan; jenjang pemutus di
				// atasnya tidak menjadwalkan apa pun.
				jalur_pelayanan: jenjangPenerima && keputusan === 'disetujui' ? jalur : undefined,
				jadwal_layanan: perluJadwal && keputusan === 'disetujui' ? jadwal : undefined,
				keterangan_petugas:
					jenjangPenerima && keputusan === 'disetujui' && keteranganPetugas ? keteranganPetugas : undefined
			});

			await Promise.all([
				queryClient.invalidateQueries({ queryKey: kunciPersetujuan(modul, pengajuanId) }),
				queryClient.invalidateQueries({ queryKey: resourceKeys.all(modul) }),
				// Daftar di panel dilayani endpoint gabungan `pengajuan` (langkah
				// 89), bukan `permohonan`/`keberatan`; tanpa ini status di tabel
				// masih yang lama setelah putusan tersimpan.
				queryClient.invalidateQueries({ queryKey: resourceKeys.all('pengajuan') })
			]);

			setCatatan('');
			setJalur('');
			setJadwal('');
			setKeteranganPetugas('');
			enqueueSnackbar(t('Putusan persetujuan tersimpan'), { variant: 'success' });
		} catch (err) {
			const pesan =
				err instanceof PpidApiError
					? (Object.values(err.errors)[0]?.[0] ?? err.message)
					: t('Putusan gagal disimpan');
			enqueueSnackbar(pesan, { variant: 'error' });
		} finally {
			setMenyimpan(false);
		}
	}

	const riwayat = data?.riwayat_putaran ?? [];
	/*
	 * Empat bagian, dalam urutan kerjanya (langkah 102):
	 *
	 *   1. Alur Persetujuan Permohonan — di mana berkasnya sekarang;
	 *   2. Verifikasi Petugas PPID     — isian yang harus ditetapkan petugas;
	 *   3. Lampiran dan Keterangan     — dokumen yang akan diserahkan;
	 *   4. Putusan                     — apa yang terjadi setelahnya.
	 *
	 * Bagian 1 dan 3 selalu ada; keduanya bacaan, dan yang belum kebagian
	 * giliran pun perlu melihatnya. Bagian 2 dan 4 hanya untuk pemegang giliran.
	 */
	return (
		<div className="flex flex-col gap-5">
			<Bagian judul="Alur Persetujuan Permohonan">
				{/*
				 * Putaran sebelumnya dilipat, bukan dibuang dan bukan pula dijejer
				 * bersama yang berjalan (langkah 100): berkas yang sudah dua kali
				 * dikembalikan menampilkan "1, 2, 1, 2" bila diratakan — urutan
				 * yang mundur di tengah daftar dan terbaca sebagai data rusak.
				 */}
				{riwayat.length > 0 && (
					<Accordion
						disableGutters
						elevation={0}
						className="border-divider rounded-lg border before:hidden"
					>
						<AccordionSummary expandIcon={<FuseSvgIcon size={18}>lucide:chevron-down</FuseSvgIcon>}>
							<Typography variant="body2">
								{t('Alur Persetujuan sebelumnya')} ({riwayat.length}) —{' '}
								{t('berkas ini pernah dikembalikan untuk diperbaiki')}
							</Typography>
						</AccordionSummary>
						<AccordionDetails className="flex flex-col gap-3">
							{riwayat.map((putaran, urutanPutaran) => (
								<div
									key={putaran[0]?.id ?? urutanPutaran}
									className="flex flex-col gap-2"
								>
									<Typography
										variant="caption"
										color="text.secondary"
										className="font-semibold"
									>
										{t('Alur Persetujuan')} {urutanPutaran + 1}
									</Typography>
									{putaran.map((satu) => (
										<SatuLangkah
											key={satu.id}
											langkah={satu}
											berjalan={false}
										/>
									))}
								</div>
							))}
						</AccordionDetails>
					</Accordion>
				)}

				{riwayat.length > 0 && (
					<Typography
						variant="caption"
						color="text.secondary"
						className="font-semibold"
					>
						{t('Alur Persetujuan')} {data?.putaran ?? riwayat.length + 1} ({t('berjalan')})
					</Typography>
				)}

				<div className="divide-divider divide-y">
					{langkah.map((satu) => (
						<SatuLangkah
							key={satu.id}
							langkah={satu}
							berjalan={satu.id === data?.berjalan_id}
						/>
					))}
				</div>

				{data?.berjalan_id === null && (
					<Alert severity="success">{t('Seluruh tahap persetujuan sudah dilalui.')}</Alert>
				)}

				{/*
				 * Yang tidak kebagian giliran tidak diberi kotak keterangan di
				 * sini. Tahap yang memegang berkasnya sudah diumumkan di kepala
				 * rincian, tempat petugas benar-benar membacanya — mengulanginya
				 * di bawah hanya menambah blok yang harus dilewati mata (langkah
				 * 100). Yang tetap perlu disebut cuma satu hal yang tidak terbaca
				 * dari mana pun: rolenya memang tidak diberi hak memutus.
				 */}
				{data?.berjalan_id !== null && !bolehSetujui && (
					<Alert severity="info">{t('Role Anda tidak diberi hak memutus pada modul ini.')}</Alert>
				)}
			</Bagian>

			{bolehKirim && (
				<Bagian judul="Verifikasi Petugas PPID">
					{/*
					 * Jenjang penerima menetapkan jalur pelayanan; jenjang pemutus
					 * di atasnya tidak menjadwalkan apa pun dan hanya menulis
					 * catatan.
					 */}
					{jenjangPenerima && (
						<>
							<TextField
								select
								size="small"
								label={t('Jalur pelayanan')}
								required
								value={jalur}
								onChange={(event) => setJalur(event.target.value)}
								fullWidth
								helperText={t('Menentukan cara informasi sampai ke pemohon.')}
							>
								<MenuItem value="online">{t('Online — dokumen dikirim lewat email')}</MenuItem>
								<MenuItem value="langsung">{t('Langsung — pemohon hadir di meja layanan')}</MenuItem>
							</TextField>

							{jalur === 'online' && (
								<Alert severity="info">
									{t(
										'Unggah dokumen yang diminta pada bagian Lampiran dan Keterangan di bawah. Pemohon menerima pemberitahuan begitu permohonannya disetujui.'
									)}
								</Alert>
							)}

							{perluJadwal && (
								<>
									<TextField
										size="small"
										type="datetime-local"
										label={t('Tanggal & waktu undangan')}
										required
										value={jadwal}
										onChange={(event) => setJadwal(event.target.value)}
										fullWidth
										slotProps={{ inputLabel: { shrink: true } }}
										helperText={t(
											'Pilih di dalam jam layanan. Waktu ini dikirim ke pemohon sebagai undangan.'
										)}
									/>

									<Alert severity="info">
										<Typography
											variant="caption"
											component="div"
											className="font-semibold"
										>
											{t('Meja layanan PPID')}
										</Typography>
										{MEJA_LAYANAN.map((baris) => (
											<Typography
												key={baris}
												variant="caption"
												component="div"
											>
												{baris}
											</Typography>
										))}
									</Alert>
								</>
							)}

							<TextField
								size="small"
								label={t('Keterangan untuk pemohon')}
								multiline
								minRows={2}
								value={keteranganPetugas}
								onChange={(event) => setKeteranganPetugas(event.target.value)}
								fullWidth
								helperText={t(
									'Opsional. Ikut dikirim ke pemohon bersama pemberitahuan jalur pelayanan.'
								)}
							/>
						</>
					)}

					<TextField
						size="small"
						label={t('Catatan')}
						multiline
						minRows={2}
						value={catatan}
						onChange={(event) => setCatatan(event.target.value)}
						fullWidth
						helperText={
							jenjangPenerima
								? t('Opsional. Catatan internal, tidak dikirim ke pemohon.')
								: t('Wajib diisi untuk Tolak dan Revisi. Menjadi alasan yang dibaca penerimanya.')
						}
					/>
				</Bagian>
			)}

			{lampiran && <Bagian judul="Lampiran dan Keterangan">{lampiran}</Bagian>}

			{bolehKirim && (
				<Bagian judul="Putusan">
					{/*
					 * Tombol per putusan, bukan dropdown lalu satu tombol netral.
					 *
					 * Jenjang penerima hanya meneruskan berkasnya, jadi tombolnya
					 * satu: **Konfirmasi** — ia meminta konfirmasi PPID, bukan
					 * menyetujui permohonannya. Jenjang pemutus mendapat ketiganya,
					 * dan tiap tombol menyebut sendiri apa yang akan terjadi.
					 */}
					<div className="flex flex-wrap gap-2">
						<Button
							variant="contained"
							color="secondary"
							disabled={menyimpan || !isianJalurLengkap}
							onClick={() => kirim('disetujui')}
							startIcon={
								menyimpan ? (
									<CircularProgress size={16} />
								) : (
									<FuseSvgIcon size={18}>lucide:stamp</FuseSvgIcon>
								)
							}
						>
							{jenjangPenerima ? t('Konfirmasi') : t('Setuju')}
						</Button>

						{!jenjangPenerima && (
							<>
								<Button
									variant="outlined"
									color="warning"
									disabled={menyimpan || !adaCatatan}
									onClick={() => kirim('revisi')}
									startIcon={<FuseSvgIcon size={18}>lucide:undo-2</FuseSvgIcon>}
								>
									{t('Revisi')}
								</Button>

								<Button
									variant="outlined"
									color="error"
									disabled={menyimpan || !adaCatatan}
									onClick={() => kirim('ditolak')}
									startIcon={<FuseSvgIcon size={18}>lucide:x</FuseSvgIcon>}
								>
									{t('Tolak')}
								</Button>
							</>
						)}
					</div>

					<Typography
						variant="caption"
						color="text.secondary"
					>
						{jenjangPenerima
							? t('Konfirmasi meneruskan berkas ini ke PPID untuk diputuskan.')
							: t(
									'Setuju menutup permohonan dan memberitahukan hasilnya ke pemohon. Revisi mengembalikannya ke PPID Pelaksana untuk diperbaiki.'
								)}
					</Typography>
				</Bagian>
			)}
		</div>
	);
}

export default PersetujuanBerjenjang;
