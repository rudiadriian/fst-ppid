import { useEffect, useMemo, useState } from 'react';
import Dialog from '@mui/material/Dialog';
import DialogTitle from '@mui/material/DialogTitle';
import DialogContent from '@mui/material/DialogContent';
import DialogActions from '@mui/material/DialogActions';
import Button from '@mui/material/Button';
import Checkbox from '@mui/material/Checkbox';
import Table from '@mui/material/Table';
import TableBody from '@mui/material/TableBody';
import TableCell from '@mui/material/TableCell';
import TableHead from '@mui/material/TableHead';
import TableRow from '@mui/material/TableRow';
import Typography from '@mui/material/Typography';
import Alert from '@mui/material/Alert';
import CircularProgress from '@mui/material/CircularProgress';
import { useQuery, useQueryClient } from '@tanstack/react-query';
import { useSnackbar } from 'notistack';
import { useTranslation } from 'react-i18next';
import ppidApi, { PpidApiError } from '../api/ppidApi';
import { navigasiQueryKey } from '../api/useNavigasi';

/**
 * Matrix hak akses satu role.
 *
 * Daftar modulnya **diambil dari API**, bukan ditulis di panel: modul yang baru
 * ditambahkan ke tabel `modul_sistem` otomatis muncul di sini tanpa perlu
 * mengubah kode. Centang di sini menentukan dua hal sekaligus:
 *
 *  - **Lihat** → menu modul muncul/hilang di sidebar pengguna dengan role ini;
 *  - **Tambah / Ubah / Hapus / Setujui / Ekspor** → tombol CRUD pada halaman
 *    modul, sekaligus izin di API (middleware `akses:{slug},{aksi}`).
 *
 * Panel hanya menyembunyikan tombol; penolakan sebenarnya tetap dilakukan API,
 * jadi mematikan centang di sini tidak bisa diakali dari peramban.
 */

type BarisAkses = {
	modul_id: number;
	slug: string;
	nama: string;
	can_view: boolean;
	can_create: boolean;
	can_edit: boolean;
	can_delete: boolean;
	can_approve: boolean;
	can_export: boolean;
};

type ResponsAkses = {
	role: { id: number; name: string; slug: string };
	akses: BarisAkses[];
};

type HakKunci = 'can_view' | 'can_create' | 'can_edit' | 'can_delete' | 'can_approve' | 'can_export';

const KOLOM_HAK: { kunci: HakKunci; label: string }[] = [
	{ kunci: 'can_view', label: 'Lihat' },
	{ kunci: 'can_create', label: 'Tambah' },
	{ kunci: 'can_edit', label: 'Ubah' },
	{ kunci: 'can_delete', label: 'Hapus' },
	{ kunci: 'can_approve', label: 'Setujui' },
	{ kunci: 'can_export', label: 'Ekspor' }
];

type RoleAksesDialogProps = {
	open: boolean;
	onClose: () => void;
	role: { id: number; name?: string; slug?: string } | null;
};

export function RoleAksesDialog({ open, onClose, role }: RoleAksesDialogProps) {
	const { t } = useTranslation();
	const { enqueueSnackbar } = useSnackbar();
	const queryClient = useQueryClient();

	const [baris, setBaris] = useState<BarisAkses[]>([]);
	const [menyimpan, setMenyimpan] = useState(false);

	const superAdmin = role?.slug === 'super-admin';

	const { data, isLoading, error } = useQuery<ResponsAkses>({
		queryKey: ['ppid', 'role', 'akses', role?.id ?? 0],
		queryFn: () => ppidApi.ambil<ResponsAkses>(`role/${role?.id}/akses`),
		enabled: open && Boolean(role?.id)
	});

	useEffect(() => {
		if (data?.akses) {
			setBaris(data.akses);
		}
	}, [data]);

	const ringkasan = useMemo(() => baris.filter((m) => m.can_view).length, [baris]);

	function ubah(modulId: number, kunci: HakKunci, nilai: boolean) {
		setBaris((lama) =>
			lama.map((item) => {
				if (item.modul_id !== modulId) {
					return item;
				}

				const berubah = { ...item, [kunci]: nilai };

				// Hak tulis tanpa hak lihat tidak ada gunanya: modulnya tidak
				// muncul di menu. Mencentang salah satunya ikut menyalakan Lihat,
				// dan mematikan Lihat ikut mematikan sisanya.
				if (kunci !== 'can_view' && nilai) {
					berubah.can_view = true;
				}

				if (kunci === 'can_view' && !nilai) {
					berubah.can_create = false;
					berubah.can_edit = false;
					berubah.can_delete = false;
					berubah.can_approve = false;
					berubah.can_export = false;
				}

				return berubah;
			})
		);
	}

	function ubahSeluruhModul(modulId: number, nilai: boolean) {
		setBaris((lama) =>
			lama.map((item) =>
				item.modul_id === modulId
					? {
							...item,
							can_view: nilai,
							can_create: nilai,
							can_edit: nilai,
							can_delete: nilai,
							can_approve: nilai,
							can_export: nilai
						}
					: item
			)
		);
	}

	function ubahSekolom(kunci: HakKunci, nilai: boolean) {
		setBaris((lama) =>
			lama.map((item) => {
				const berubah = { ...item, [kunci]: nilai };

				if (kunci !== 'can_view' && nilai) {
					berubah.can_view = true;
				}

				if (kunci === 'can_view' && !nilai) {
					berubah.can_create = false;
					berubah.can_edit = false;
					berubah.can_delete = false;
					berubah.can_approve = false;
					berubah.can_export = false;
				}

				return berubah;
			})
		);
	}

	async function simpan() {
		if (!role) {
			return;
		}

		setMenyimpan(true);

		try {
			await ppidApi.simpan(`role/${role.id}/akses`, {
				akses: baris.map((item) => ({
					modul_id: item.modul_id,
					can_view: item.can_view,
					can_create: item.can_create,
					can_edit: item.can_edit,
					can_delete: item.can_delete,
					can_approve: item.can_approve,
					can_export: item.can_export
				}))
			});

			// Menu dan tombol milik pengguna yang sedang login ikut disegarkan —
			// kalau yang diubah adalah rolenya sendiri, perubahannya langsung terasa.
			await queryClient.invalidateQueries({ queryKey: navigasiQueryKey });
			await queryClient.invalidateQueries({ queryKey: ['ppid', 'role', 'akses', role.id] });

			enqueueSnackbar(t('Hak akses role disimpan'), { variant: 'success' });
			onClose();
		} catch (err) {
			const pesan =
				err instanceof PpidApiError
					? (Object.values(err.errors)[0]?.[0] ?? err.message)
					: t('Hak akses gagal disimpan');
			enqueueSnackbar(pesan, { variant: 'error' });
		} finally {
			setMenyimpan(false);
		}
	}

	return (
		<Dialog
			open={open}
			onClose={menyimpan ? undefined : onClose}
			fullWidth
			maxWidth="lg"
		>
			<DialogTitle>
				{t('Hak akses role')}
				{role?.name ? ` — ${role.name}` : ''}
			</DialogTitle>

			<DialogContent
				dividers
				className="flex flex-col gap-4"
			>
				{superAdmin ? (
					<Alert severity="info">
						{t('Role super-admin selalu punya akses penuh ke seluruh modul dan tidak dapat dibatasi.')}
					</Alert>
				) : (
					<Alert severity="info">
						{t(
							'Kolom Lihat menentukan modul yang muncul di menu; kolom lainnya menentukan tombol Tambah, Ubah, Hapus, Setujui, dan Ekspor pada modul tersebut.'
						)}
					</Alert>
				)}

				{error && (
					<Alert severity="error">
						{error instanceof PpidApiError ? error.message : t('Data gagal dimuat.')}
					</Alert>
				)}

				{isLoading ? (
					<div className="flex justify-center py-10">
						<CircularProgress size={28} />
					</div>
				) : (
					<>
						<Typography
							variant="body2"
							color="text.secondary"
						>
							{ringkasan} {t('dari')} {baris.length} {t('modul dapat dilihat role ini.')}
						</Typography>

						<Table
							size="small"
							stickyHeader
						>
							<TableHead>
								<TableRow>
									<TableCell className="font-semibold">{t('Modul')}</TableCell>
									{KOLOM_HAK.map((kolom) => (
										<TableCell
											key={kolom.kunci}
											align="center"
											className="font-semibold"
										>
											<div className="flex flex-col items-center">
												{t(kolom.label)}
												<Checkbox
													size="small"
													disabled={superAdmin}
													checked={baris.length > 0 && baris.every((item) => item[kolom.kunci])}
													indeterminate={
														baris.some((item) => item[kolom.kunci]) &&
														!baris.every((item) => item[kolom.kunci])
													}
													onChange={(event) => ubahSekolom(kolom.kunci, event.target.checked)}
												/>
											</div>
										</TableCell>
									))}
									<TableCell align="center" className="font-semibold">
										{t('Semua')}
									</TableCell>
								</TableRow>
							</TableHead>

							<TableBody>
								{baris.map((item) => {
									const penuh = KOLOM_HAK.every((kolom) => item[kolom.kunci]);

									return (
										<TableRow
											key={item.modul_id}
											hover
										>
											<TableCell>
												<Typography variant="body2" className="font-medium">
													{t(item.nama)}
												</Typography>
												<Typography variant="caption" color="text.secondary">
													{item.slug}
												</Typography>
											</TableCell>

											{KOLOM_HAK.map((kolom) => (
												<TableCell
													key={kolom.kunci}
													align="center"
												>
													<Checkbox
														size="small"
														disabled={superAdmin}
														checked={Boolean(item[kolom.kunci])}
														onChange={(event) =>
															ubah(item.modul_id, kolom.kunci, event.target.checked)
														}
													/>
												</TableCell>
											))}

											<TableCell align="center">
												<Checkbox
													size="small"
													disabled={superAdmin}
													checked={penuh}
													indeterminate={!penuh && KOLOM_HAK.some((kolom) => item[kolom.kunci])}
													onChange={(event) => ubahSeluruhModul(item.modul_id, event.target.checked)}
												/>
											</TableCell>
										</TableRow>
									);
								})}
							</TableBody>
						</Table>
					</>
				)}
			</DialogContent>

			<DialogActions>
				<Button
					onClick={onClose}
					disabled={menyimpan}
				>
					{t('Batal')}
				</Button>
				<Button
					variant="contained"
					color="secondary"
					disabled={menyimpan || isLoading || superAdmin || baris.length === 0}
					onClick={simpan}
					startIcon={menyimpan ? <CircularProgress size={16} /> : undefined}
				>
					{t('Simpan')}
				</Button>
			</DialogActions>
		</Dialog>
	);
}

export default RoleAksesDialog;
