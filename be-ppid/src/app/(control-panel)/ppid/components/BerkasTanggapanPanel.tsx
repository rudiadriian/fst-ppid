import { useRef, useState } from 'react';
import Button from '@mui/material/Button';
import Chip from '@mui/material/Chip';
import Alert from '@mui/material/Alert';
import Typography from '@mui/material/Typography';
import CircularProgress from '@mui/material/CircularProgress';
import { useQueryClient } from '@tanstack/react-query';
import { useSnackbar } from 'notistack';
import { useTranslation } from 'react-i18next';
import FuseSvgIcon from '@fuse/core/FuseSvgIcon';
import ppidApi, { PpidApiError } from '../api/ppidApi';
import { resourceKeys } from '../api/useResource';
import { urlMedia } from './UploadField';
import ArsipDokumenPicker from './ArsipDokumenPicker';

type Berkas = {
	id?: number;
	nama_file?: string;
	path_file?: string;
};

/** Berkas yang sudah terunggah tetapi belum dilampirkan ke permohonan. */
type BerkasAntre = {
	nama_file: string;
	path_file: string;
	/** Berasal dari arsip, bukan unggahan baru — hanya untuk keterangan. */
	dariArsip?: boolean;
};

/** Sama dengan `UploadController::BATAS_KB` untuk dokumen: 20 MB. */
const BATAS_UNGGAH_BYTE = 20 * 1024 * 1024;

/** Ekstensi yang diterima API pada jenis `dokumen_gambar`. */
const EKSTENSI_PDF_GAMBAR = ['pdf', 'jpg', 'jpeg', 'png', 'webp'];

/**
 * Jenis unggahan yang tepat untuk satu berkas.
 *
 * API memeriksa ekstensi terhadap daftar putih per jenis. `dokumen_gambar`
 * hanya menerima PDF dan gambar, sedangkan tanggapan petugas kerap berupa
 * dokumen Office — memaksa semuanya lewat `dokumen_gambar` membuat berkas
 * seperti .docx ditolak dengan "Jenis berkas tidak diizinkan". Jenisnya
 * karena itu ditentukan per berkas, bukan dipatok satu untuk semua.
 */
function jenisUnggahan(namaBerkas: string): 'dokumen' | 'dokumen_gambar' {
	const ekstensi = namaBerkas.split('.').pop()?.toLowerCase() ?? '';

	return EKSTENSI_PDF_GAMBAR.includes(ekstensi) ? 'dokumen_gambar' : 'dokumen';
}

type BerkasTanggapanPanelProps = {
	permohonanId: number;
	berkas: unknown;
	/** Role pengguna punya hak `Ubah` pada modul Permohonan. */
	bolehUbah: boolean;
};

/**
 * Berkas tanggapan petugas: dokumen yang diberikan kepada pemohon.
 *
 * Memilih berkas **tidak** langsung melampirkannya (langkah 97). Berkasnya
 * naik ke penyimpanan lebih dulu supaya punya alamat, lalu menunggu di daftar
 * "belum disimpan" sampai petugas menekan Simpan. Sebelum itu tidak ada yang
 * tercatat pada permohonan dan tidak ada pemberitahuan apa pun yang berangkat —
 * sebelumnya satu klik unggah sudah mengirim notifikasi ke portal pemohon,
 * padahal petugas masih menyusun jawabannya.
 *
 * Pemohon sendiri baru diberi tahu saat permohonannya diserahkan (status
 * Disetujui/Selesai), bukan saat berkasnya disimpan — itu ditentukan API,
 * karena selama masih disiapkan berkasnya memang belum tampil di portal.
 */
export function BerkasTanggapanPanel({ permohonanId, berkas, bolehUbah }: BerkasTanggapanPanelProps) {
	const { t } = useTranslation();
	const inputRef = useRef<HTMLInputElement>(null);
	const [sibuk, setSibuk] = useState(false);
	const [arsipTerbuka, setArsipTerbuka] = useState(false);
	const [antrean, setAntrean] = useState<BerkasAntre[]>([]);
	const queryClient = useQueryClient();
	const { enqueueSnackbar } = useSnackbar();

	const daftar = Array.isArray(berkas) ? (berkas as Berkas[]) : [];

	async function segarkan() {
		await Promise.all([
			queryClient.invalidateQueries({ queryKey: resourceKeys.all('permohonan') }),
			queryClient.invalidateQueries({ queryKey: resourceKeys.all('pengajuan') })
		]);
	}

	function pesanGalat(error: unknown, bawaan: string): string {
		return error instanceof PpidApiError ? (Object.values(error.errors)[0]?.[0] ?? error.message) : t(bawaan);
	}

	/** Tambah ke antrean tanpa menggandakan berkas yang sudah ada di sana. */
	function antrekan(baru: BerkasAntre[]) {
		setAntrean((lama) => {
			const adaPath = new Set([
				...lama.map((satu) => satu.path_file),
				...daftar.map((satu) => String(satu.path_file ?? ''))
			]);

			return [...lama, ...baru.filter((satu) => !adaPath.has(satu.path_file))];
		});
	}

	async function unggah(event: React.ChangeEvent<HTMLInputElement>) {
		const files = Array.from(event.target.files ?? []);

		// Input direset supaya berkas yang sama bisa dipilih lagi setelah dihapus.
		event.target.value = '';

		if (files.length === 0) {
			return;
		}

		// Ukurannya diperiksa di sini juga, bukan hanya di server: berkas yang
		// melebihi batas ditolak web server sebelum sampai ke Laravel, dan
		// jawabannya bukan JSON — yang terlihat petugas cuma "gagal" tanpa
		// alasan. Batas 20 MB mengikuti `UploadController::BATAS_KB`.
		const kebesaran = files.find((file) => file.size > BATAS_UNGGAH_BYTE);

		if (kebesaran) {
			enqueueSnackbar(`${t('Berkas terlalu besar')}: ${kebesaran.name} — ${t('batasnya 20 MB per berkas.')}`, {
				variant: 'error'
			});
			return;
		}

		setSibuk(true);

		try {
			const terunggah: BerkasAntre[] = [];

			for (const file of files) {
				// Berurutan, bukan serentak: unggahan besar yang ditembakkan
				// bersamaan lebih sering kena batas ukuran/waktu di server.
				// Folder `permohonan`: daftar folder unggahan di API tertutup
				// (`UploadController::FOLDER`), dan berkas tanggapan memang milik
				// permohonan yang sedang ditangani.
				const hasil = await ppidApi.upload(file, 'permohonan', jenisUnggahan(file.name));
				terunggah.push({ nama_file: hasil.nama_file, path_file: hasil.path });
			}

			antrekan(terunggah);

			enqueueSnackbar(`${terunggah.length} ${t('berkas siap disimpan. Tekan Simpan berkas tanggapan.')}`, {
				variant: 'info'
			});
		} catch (error) {
			enqueueSnackbar(pesanGalat(error, 'Berkas gagal diunggah'), { variant: 'error' });
		} finally {
			setSibuk(false);
		}
	}

	/**
	 * Ambil berkas dari Arsip Dokumen ke antrean.
	 *
	 * Tidak ada unggahan sama sekali: yang dibawa hanya `path_file` milik
	 * arsipnya, jadi satu berkas fisik bisa dipakai banyak permohonan.
	 */
	function ambilDariArsip(dipilih: { nama_file: string; path_file: string }[]) {
		antrekan(dipilih.map((satu) => ({ ...satu, dariArsip: true })));
		setArsipTerbuka(false);

		if (dipilih.length > 0) {
			enqueueSnackbar(`${dipilih.length} ${t('berkas arsip siap disimpan. Tekan Simpan berkas tanggapan.')}`, {
				variant: 'info'
			});
		}
	}

	/** Lampirkan seluruh antrean ke permohonan dalam satu permintaan. */
	async function simpan() {
		if (antrean.length === 0) {
			return;
		}

		setSibuk(true);

		try {
			await ppidApi.action(`permohonan/${permohonanId}/tanggapan-files`, {
				files: antrean.map((satu) => ({ nama_file: satu.nama_file, path_file: satu.path_file }))
			});

			const jumlah = antrean.length;

			setAntrean([]);
			await segarkan();

			enqueueSnackbar(`${jumlah} ${t('berkas tanggapan tersimpan.')}`, { variant: 'success' });
		} catch (error) {
			enqueueSnackbar(pesanGalat(error, 'Berkas gagal disimpan'), { variant: 'error' });
		} finally {
			setSibuk(false);
		}
	}

	async function hapus(fileId: number, nama: string) {
		// Berkas yang sudah tersimpan bisa saja sudah diunduh pemohon bila
		// permohonannya sudah diserahkan, jadi penghapusannya dikonfirmasi.
		if (!window.confirm(`${t('Hapus berkas')} "${nama}"?`)) {
			return;
		}

		setSibuk(true);

		try {
			await ppidApi.remove(`permohonan/${permohonanId}/tanggapan-files`, fileId);
			await segarkan();
			enqueueSnackbar(t('Berkas tanggapan dihapus'), { variant: 'success' });
		} catch (error) {
			enqueueSnackbar(pesanGalat(error, 'Berkas gagal dihapus'), { variant: 'error' });
		} finally {
			setSibuk(false);
		}
	}

	return (
		<div className="flex flex-col gap-3">
			{daftar.length === 0 ? (
				<Typography
					variant="body2"
					color="text.secondary"
				>
					{t('Belum ada berkas tanggapan.')}
				</Typography>
			) : (
				<div className="flex flex-wrap gap-2">
					{daftar.map((file, index) => (
						<Chip
							key={file.id ?? index}
							size="small"
							variant="outlined"
							icon={<FuseSvgIcon size={14}>lucide:paperclip</FuseSvgIcon>}
							label={file.nama_file || t('Berkas')}
							onClick={() => window.open(urlMedia(String(file.path_file ?? '')), '_blank', 'noopener')}
							onDelete={
								bolehUbah && file.id
									? () => hapus(Number(file.id), String(file.nama_file ?? ''))
									: undefined
							}
						/>
					))}
				</div>
			)}

			{bolehUbah && (
				<>
					{antrean.length > 0 && (
						<div className="border-divider flex flex-col gap-2 rounded-lg border border-dashed p-3">
							<Typography
								variant="caption"
								className="font-semibold"
							>
								{t('Belum disimpan')} ({antrean.length})
							</Typography>

							<div className="flex flex-wrap gap-2">
								{antrean.map((satu) => (
									<Chip
										key={satu.path_file}
										size="small"
										color="warning"
										variant="outlined"
										icon={
											<FuseSvgIcon size={14}>
												{satu.dariArsip ? 'lucide:folder-open' : 'lucide:file-plus'}
											</FuseSvgIcon>
										}
										label={satu.nama_file}
										onDelete={() =>
											setAntrean((lama) =>
												lama.filter((berkasAntre) => berkasAntre.path_file !== satu.path_file)
											)
										}
									/>
								))}
							</div>

							<div>
								<Button
									variant="contained"
									color="secondary"
									size="small"
									disabled={sibuk}
									onClick={simpan}
									startIcon={
										sibuk ? (
											<CircularProgress size={16} />
										) : (
											<FuseSvgIcon size={18}>lucide:save</FuseSvgIcon>
										)
									}
								>
									{t('Simpan berkas tanggapan')}
								</Button>
							</div>
						</div>
					)}

					<Alert severity="info">
						{t(
							'Berkas baru tercatat pada permohonan setelah Anda menekan Simpan. Pemohon menerima pemberitahuan saat permohonannya diserahkan — status Disetujui atau Selesai — bukan saat berkasnya disimpan.'
						)}
					</Alert>

					{/* Daftar terimanya mengikuti daftar putih API: PDF, gambar,
					    dan dokumen Office. */}
					<input
						ref={inputRef}
						type="file"
						multiple
						hidden
						accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.csv,.txt,image/jpeg,image/png,image/webp"
						onChange={unggah}
					/>

					<div className="flex flex-wrap gap-2">
						<Button
							variant="outlined"
							size="small"
							disabled={sibuk}
							onClick={() => inputRef.current?.click()}
							startIcon={
								sibuk ? (
									<CircularProgress size={16} />
								) : (
									<FuseSvgIcon size={18}>lucide:upload</FuseSvgIcon>
								)
							}
						>
							{t('Pilih berkas tanggapan')}
						</Button>

						{/* Dokumen yang sama dikirim ke banyak pemohon; mengunggahnya
						    berulang hanya menambah salinan di disk. */}
						<Button
							variant="outlined"
							size="small"
							disabled={sibuk}
							onClick={() => setArsipTerbuka(true)}
							startIcon={<FuseSvgIcon size={18}>lucide:folder-open</FuseSvgIcon>}
						>
							{t('Pilih dari Arsip')}
						</Button>
					</div>

					<ArsipDokumenPicker
						open={arsipTerbuka}
						onClose={() => setArsipTerbuka(false)}
						onPilih={ambilDariArsip}
					/>
				</>
			)}
		</div>
	);
}

export default BerkasTanggapanPanel;
