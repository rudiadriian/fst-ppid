import { useState } from 'react';
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
import { kunciPersetujuan, LangkahPersetujuan, usePersetujuan } from '../api/usePersetujuan';
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

function SatuLangkah({ langkah, berjalan }: { langkah: LangkahPersetujuan; berjalan: boolean }) {
	const { t } = useTranslation();
	const info = STATUS_LANGKAH[langkah.status] ?? STATUS_LANGKAH.dilewati;
	const telatnya = berjalan && langkah.batas_waktu && new Date(langkah.batas_waktu).getTime() < Date.now();

	return (
		<div
			className={`flex gap-3 rounded-lg border p-3 ${
				berjalan ? 'border-secondary-main bg-secondary-main/5' : 'border-divider'
			}`}
		>
			<div className="flex flex-col items-center pt-0.5">
				<FuseSvgIcon
					size={18}
					color={info.warna === 'default' ? 'disabled' : info.warna}
				>
					{info.ikon}
				</FuseSvgIcon>
			</div>

			<div className="flex min-w-0 flex-auto flex-col gap-1">
				<div className="flex flex-wrap items-center gap-2">
					<Typography
						variant="body2"
						className="font-semibold"
					>
						{langkah.urutan}. {langkah.nama_tahap}
					</Typography>
					<Chip
						size="small"
						label={t(info.label)}
						color={info.warna}
					/>
					{berjalan && (
						<Chip
							size="small"
							variant="outlined"
							color="secondary"
							label={t('Giliran sekarang')}
						/>
					)}
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
					{langkah.nama_jabatan ? `${langkah.nama_jabatan} · ` : ''}
					{langkah.role?.name ?? t('Role belum ditetapkan')}
				</Typography>

				<Typography
					variant="caption"
					color="text.secondary"
				>
					{langkah.tanggal_putusan
						? `${t('Diputus')} ${formatWaktu(langkah.tanggal_putusan)} · ${langkah.pemutus?.name ?? '—'}`
						: langkah.tanggal_masuk
							? `${t('Masuk')} ${formatWaktu(langkah.tanggal_masuk)}${
									langkah.batas_waktu ? ` · ${t('batas')} ${formatWaktu(langkah.batas_waktu)}` : ''
								}`
							: t('Belum tiba gilirannya')}
				</Typography>

				{langkah.catatan && (
					<Typography
						variant="body2"
						className="whitespace-pre-line"
					>
						{langkah.catatan}
					</Typography>
				)}
			</div>
		</div>
	);
}

type PersetujuanBerjenjangProps = {
	/** Segmen API modulnya: `permohonan` atau `keberatan`. */
	modul: 'permohonan' | 'keberatan';
	pengajuanId: number;
	/** Role pengguna punya hak `Setujui` pada modul ini. */
	bolehSetujui: boolean;
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
export function PersetujuanBerjenjang({ modul, pengajuanId, bolehSetujui }: PersetujuanBerjenjangProps) {
	const { t } = useTranslation();
	const queryClient = useQueryClient();
	const { enqueueSnackbar } = useSnackbar();

	const [keputusan, setKeputusan] = useState('disetujui');
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
	const perluCatatan = keputusan !== 'disetujui';

	/*
	 * Jenjang penerima dikenali dari haknya, bukan dari namanya: tahap yang
	 * tidak diberi hak menolak adalah tahap yang tugasnya meneruskan, dan
	 * dialah yang berhubungan dengan pemohon. Namanya bisa diubah super admin
	 * kapan saja; haknya menentukan perannya.
	 */
	const langkahBerjalan = langkah.find((satu) => satu.id === data?.berjalan_id) ?? null;
	const jenjangPenerima = langkahBerjalan?.tahap ? !langkahBerjalan.tahap.boleh_tolak : false;
	const perluJadwal = jenjangPenerima && jalur === 'langsung';
	const isianJalurLengkap =
		!jenjangPenerima || keputusan !== 'disetujui' || (jalur !== '' && (jalur !== 'langsung' || jadwal !== ''));

	async function kirim() {
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
			setKeputusan('disetujui');
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

	return (
		<div className="flex flex-col gap-3">
			{langkah.map((satu) => (
				<SatuLangkah
					key={satu.id}
					langkah={satu}
					berjalan={satu.id === data?.berjalan_id}
				/>
			))}

			{data?.berjalan_id === null && (
				<Alert severity="success">{t('Seluruh tahap persetujuan sudah dilalui.')}</Alert>
			)}

			{data?.berjalan_id !== null && !bolehKirim && (
				<Alert severity="info">
					{t('Tahap yang berjalan bukan giliran role Anda, jadi putusannya tidak bisa dikirim dari sini.')}
				</Alert>
			)}

			{bolehKirim && (
				<div className="border-divider flex flex-col gap-3 rounded-lg border p-3">
					<Typography
						variant="subtitle2"
						className="font-semibold"
					>
						{t('Putusan Anda')}
					</Typography>

					<TextField
						select
						size="small"
						label={t('Keputusan')}
						value={keputusan}
						onChange={(event) => setKeputusan(event.target.value)}
						fullWidth
					>
						<MenuItem value="disetujui">{t('Setujui')}</MenuItem>
						<MenuItem value="revisi">{t('Kembalikan untuk diperbaiki')}</MenuItem>
						{/* Jenjang penerima memang tidak diberi hak menolak, jadi
						    pilihannya pun tidak ditawarkan — menawarkan sesuatu
						    yang pasti ditolak server hanya membuang waktu petugas. */}
						{!jenjangPenerima && <MenuItem value="ditolak">{t('Tolak')}</MenuItem>}
					</TextField>

					{jenjangPenerima && keputusan === 'disetujui' && (
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
										'Unggah dokumen yang diminta lewat panel Berkas Tanggapan pada detail pengajuan ini. Pemohon menerima pemberitahuan begitu berkasnya tersimpan.'
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
						required={perluCatatan}
						multiline
						minRows={2}
						value={catatan}
						onChange={(event) => setCatatan(event.target.value)}
						fullWidth
						helperText={
							keputusan === 'ditolak'
								? t('Wajib diisi. Menjadi alasan penolakan yang dibaca pemohon.')
								: keputusan === 'revisi'
									? t('Wajib diisi. Menjelaskan apa yang harus diperbaiki petugas.')
									: t('Opsional.')
						}
					/>

					<Button
						variant="contained"
						color={keputusan === 'ditolak' ? 'error' : 'secondary'}
						className="self-start"
						disabled={menyimpan || (perluCatatan && !catatan.trim()) || !isianJalurLengkap}
						onClick={kirim}
						startIcon={
							menyimpan ? (
								<CircularProgress size={16} />
							) : (
								<FuseSvgIcon size={18}>lucide:stamp</FuseSvgIcon>
							)
						}
					>
						{t('Kirim putusan')}
					</Button>
				</div>
			)}
		</div>
	);
}

export default PersetujuanBerjenjang;
