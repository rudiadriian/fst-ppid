import { useCallback, useEffect, useMemo, useState } from 'react';
import { MRT_ColumnDef, MRT_PaginationState, MRT_SortingState, MRT_Row } from 'material-react-table';
import Button from '@mui/material/Button';
import Chip from '@mui/material/Chip';
import MenuItem from '@mui/material/MenuItem';
import TextField from '@mui/material/TextField';
import Typography from '@mui/material/Typography';
import Paper from '@mui/material/Paper';
import Alert from '@mui/material/Alert';
import ListItemIcon from '@mui/material/ListItemIcon';
import { useSnackbar } from 'notistack';
import { useTranslation } from 'react-i18next';
import FuseSvgIcon from '@fuse/core/FuseSvgIcon';
import DataTable from '@/components/data-table/DataTable';
import { ApiRecord, PpidApiError } from '../api/ppidApi';
import { useDeleteManyResource, useDeleteResource, useRelationOptions, useResourceList } from '../api/useResource';
import { useAksesModul } from '../api/useNavigasi';
import { apiPathOf, ColumnConfig, FilterConfig, ResourceConfig } from '../lib/types';
import { FILTER_TERHAPUS, kolomDenganJejak, punyaJejak, visibilitasAwalJejak } from '../lib/jejak';
import ResourceFormDialog from './ResourceFormDialog';
import { urlMedia } from './UploadField';

/** Ambil nilai bersarang, mis. "kategori.nama". */
function nilaiKolom(baris: ApiRecord, kunci: string): unknown {
	return kunci.split('.').reduce<unknown>((nilai, bagian) => {
		if (nilai && typeof nilai === 'object') {
			return (nilai as Record<string, unknown>)[bagian];
		}

		return undefined;
	}, baris);
}

function formatTanggal(nilai: unknown, denganJam: boolean): string {
	if (!nilai) {
		return '—';
	}

	const tanggal = new Date(String(nilai));

	if (Number.isNaN(tanggal.getTime())) {
		return String(nilai);
	}

	return tanggal.toLocaleString('id-ID', {
		day: '2-digit',
		month: 'short',
		year: 'numeric',
		...(denganJam ? { hour: '2-digit', minute: '2-digit' } : {})
	});
}

function SelPerKolom({ kolom, baris }: { kolom: ColumnConfig; baris: ApiRecord }) {
	const { t } = useTranslation();
	const nilai = nilaiKolom(baris, kolom.key);

	switch (kolom.type) {
		case 'boolean':
			return (
				<Chip
					size="small"
					label={nilai ? t('Aktif') : t('Nonaktif')}
					color={nilai ? 'success' : 'default'}
					variant="outlined"
				/>
			);

		case 'badge': {
			const info = kolom.badgeMap?.[String(nilai)];

			return (
				<Chip
					size="small"
					label={info?.label ? t(info.label) : String(nilai ?? '—')}
					color={info?.color ?? 'default'}
				/>
			);
		}

		case 'date':
			return <span>{formatTanggal(nilai, false)}</span>;

		case 'datetime':
			return <span>{formatTanggal(nilai, true)}</span>;

		case 'relation': {
			const objek = nilai as Record<string, unknown> | null | undefined;
			return <span>{objek ? String(objek[kolom.relationKey ?? 'nama'] ?? '—') : '—'}</span>;
		}

		case 'file':
			return nilai ? (
				<a
					href={urlMedia(String(nilai))}
					target="_blank"
					rel="noreferrer noopener"
					className="underline"
				>
					{t('Lihat berkas')}
				</a>
			) : (
				<span>—</span>
			);

		case 'number':
			return <span>{nilai === null || nilai === undefined ? '—' : String(nilai)}</span>;

		default: {
			const teks = nilai === null || nilai === undefined || nilai === '' ? '—' : String(nilai);
			return <span className="line-clamp-2">{teks}</span>;
		}
	}
}

function FilterRelasi({
	filter,
	value,
	onChange
}: {
	filter: FilterConfig;
	value: string;
	onChange: (nilai: string) => void;
}) {
	const { t } = useTranslation();
	const { data: opsi } = useRelationOptions(
		filter.relation?.resource ?? '',
		filter.relation?.labelKey ?? 'nama',
		Boolean(filter.relation)
	);

	return (
		<TextField
			select
			size="small"
			label={t(filter.label)}
			value={value}
			onChange={(event) => onChange(event.target.value)}
			sx={{ minWidth: 170 }}
		>
			<MenuItem value="">{t('Semua')}</MenuItem>
			{(opsi ?? []).map((item) => (
				<MenuItem
					key={String(item.value)}
					value={String(item.value)}
				>
					{item.label}
				</MenuItem>
			))}
		</TextField>
	);
}

type ResourceListPageProps = {
	config: ResourceConfig;
	/** Aksi tambahan per baris (mis. kelola status permohonan). */
	aksiBaris?: (baris: ApiRecord, tutupMenu: () => void) => React.ReactNode[];
	/** Konten tambahan di bawah judul. */
	headerExtra?: React.ReactNode;
};

/**
 * Halaman daftar CMS.
 *
 * Pencarian, filter, urutan, dan paginasi semuanya dikerjakan server; tabel
 * hanya menampilkan halaman yang sedang diminta. Ini menjaga panel tetap
 * ringan walau satu modul berisi puluhan ribu baris.
 */
export function ResourceListPage({ config, aksiBaris, headerExtra }: ResourceListPageProps) {
	const apiPath = apiPathOf(config);
	const { akses } = useAksesModul(config.modul);
	const { enqueueSnackbar } = useSnackbar();
	const { t } = useTranslation();

	const [pagination, setPagination] = useState<MRT_PaginationState>({ pageIndex: 0, pageSize: 15 });
	const [sorting, setSorting] = useState<MRT_SortingState>([]);
	const [pencarian, setPencarian] = useState('');
	const [pencarianTertunda, setPencarianTertunda] = useState('');
	const [nilaiFilter, setNilaiFilter] = useState<Record<string, string>>({});
	const [formTerbuka, setFormTerbuka] = useState(false);
	const [idTerpilih, setIdTerpilih] = useState<number | null>(null);

	// Pencarian ditunda 400 ms supaya tiap ketikan tidak memicu request.
	useEffect(() => {
		const timer = setTimeout(() => setPencarian(pencarianTertunda), 400);
		return () => clearTimeout(timer);
	}, [pencarianTertunda]);

	// Kembali ke halaman pertama setiap kali kriteria berubah.
	useEffect(() => {
		setPagination((lama) => ({ ...lama, pageIndex: 0 }));
	}, [pencarian, nilaiFilter]);

	const paramSort = useMemo(() => {
		if (sorting.length === 0) {
			return config.defaultSort ?? '-id';
		}

		return `${sorting[0].desc ? '-' : ''}${sorting[0].id}`;
	}, [sorting, config.defaultSort]);

	const params = useMemo(
		() => ({
			page: pagination.pageIndex + 1,
			per_page: pagination.pageSize,
			search: pencarian || undefined,
			sort: paramSort,
			// Nilai terkunci modul selalu ikut sebagai filter dan tidak bisa
			// ditimpa filter pilihan operator.
			...nilaiFilter,
			...(config.nilaiTetap ?? {})
		}),
		[pagination, pencarian, paramSort, nilaiFilter, config.nilaiTetap]
	);

	const { data, isLoading, isFetching, error } = useResourceList<ApiRecord>(apiPath, params);
	const hapus = useDeleteResource(apiPath);
	const hapusBanyak = useDeleteManyResource(apiPath);

	// Data terhapus hanya bisa dilihat, bukan disunting atau dihapus lagi.
	const melihatTerhapus = (nilaiFilter.terhapus ?? '') !== '';

	const kolomTampil = useMemo(
		() => kolomDenganJejak(config, melihatTerhapus),
		[config, melihatTerhapus]
	);

	const daftarFilter = useMemo<FilterConfig[]>(
		() => [...(config.filters ?? []), ...(punyaJejak(config) ? [FILTER_TERHAPUS] : [])],
		[config]
	);

	// Dihitung sekali saat tabel dipasang: MRT hanya membaca `initialState` pada
	// render pertama, dan setelah itu visibilitas kolom milik operator.
	const [visibilitasAwal] = useState(() => visibilitasAwalJejak(config));

	const kolom = useMemo<MRT_ColumnDef<ApiRecord>[]>(
		() =>
			kolomTampil.map((kol) => ({
				accessorKey: kol.key,
				header: t(kol.label),
				size: kol.size,
				enableSorting: !kol.noSort,
				enableColumnFilter: false,
				enableGrouping: false,
				Cell: ({ row }) => (
					<SelPerKolom
						kolom={kol}
						baris={row.original}
					/>
				)
			})),
		[kolomTampil, t]
	);

	const jalankanHapus = useCallback(
		async (id: number) => {
			// eslint-disable-next-line no-alert
			if (!window.confirm(t('Hapus data ini? Tindakan ini tidak dapat dibatalkan.'))) {
				return;
			}

			try {
				await hapus.mutateAsync(id);
				enqueueSnackbar(t('Data dihapus'), { variant: 'success' });
			} catch (err) {
				const pesan = err instanceof PpidApiError ? (Object.values(err.errors)[0]?.[0] ?? err.message) : t('Gagal menghapus data');
				enqueueSnackbar(pesan, { variant: 'error' });
			}
		},
		[hapus, enqueueSnackbar, t]
	);

	// Saat daftar menampilkan data terhapus, aksi tulis dimatikan: barisnya
	// tinggal arsip jejak, bukan data yang masih dipakai.
	const bolehTulis = !config.readOnly && !melihatTerhapus;

	return (
		<div className="flex w-full flex-col">
			<div className="flex flex-col gap-2 p-4 sm:flex-row sm:items-center sm:justify-between md:p-6">
				<div>
					<Typography
						variant="h5"
						className="font-semibold"
					>
						{t(config.title)}
					</Typography>
					{config.description && (
						<Typography
							variant="body2"
							color="text.secondary"
						>
							{t(config.description)}
						</Typography>
					)}
					{headerExtra}
				</div>

				{bolehTulis && akses.create && (
					<Button
						variant="contained"
						color="secondary"
						startIcon={<FuseSvgIcon size={18}>lucide:plus</FuseSvgIcon>}
						onClick={() => {
							setIdTerpilih(null);
							setFormTerbuka(true);
						}}
					>
						{t('Tambah')} {t(config.singular)}
					</Button>
				)}
			</div>

			{error && (
				<Alert
					severity="error"
					className="mx-4 mb-4 md:mx-6"
				>
					{error instanceof PpidApiError ? error.message : t('Data gagal dimuat.')}
				</Alert>
			)}

			<Paper
				elevation={0}
				className="mx-4 mb-6 flex flex-auto flex-col overflow-hidden rounded-lg border border-divider md:mx-6"
			>
				<DataTable<ApiRecord>
					columns={kolom}
					data={data?.data ?? []}
					getRowId={(baris) => String(baris.id)}
					initialState={{ columnVisibility: visibilitasAwal }}
					manualPagination
					manualSorting
					manualFiltering
					enableGrouping={false}
					enableFacetedValues={false}
					enableColumnFilterModes={false}
					rowCount={data?.meta.total ?? 0}
					state={{
						pagination,
						sorting,
						globalFilter: pencarianTertunda,
						isLoading,
						showProgressBars: isFetching
					}}
					onPaginationChange={setPagination}
					onSortingChange={setSorting}
					onGlobalFilterChange={(nilai: unknown) => setPencarianTertunda(String(nilai ?? ''))}
					muiSearchTextFieldProps={{
						placeholder: config.searchPlaceholder
							? t(config.searchPlaceholder)
							: `${t('Cari')} ${t(config.title).toLowerCase()}…`,
						sx: { minWidth: '260px' },
						variant: 'outlined',
						size: 'small'
					}}
					enableRowSelection={bolehTulis && akses.delete}
					// Kolom aksi juga muncul untuk modul baca-saja yang punya
					// aksi khusus — mis. Pemohon, yang datanya tidak boleh
					// disunting tetapi berkasnya perlu diverifikasi petugas.
					enableRowActions={Boolean(aksiBaris) || (bolehTulis && (akses.edit || akses.delete))}
					renderTopToolbarCustomActions={({ table }) => (
						<div className="flex flex-wrap items-center gap-2">
							{akses.delete && table.getSelectedRowModel().rows.length > 0 && (
								<Button
									variant="contained"
									color="error"
									size="small"
									onClick={async () => {
										const terpilih = table.getSelectedRowModel().rows;

										// eslint-disable-next-line no-alert
										if (!window.confirm(`${t('Hapus')} ${terpilih.length} ${t('data terpilih?')}`)) {
											return;
										}

										try {
											await hapusBanyak.mutateAsync(terpilih.map((r) => Number(r.original.id)));
											table.resetRowSelection();
											enqueueSnackbar(t('Data terpilih dihapus'), { variant: 'success' });
										} catch (err) {
											enqueueSnackbar(
												err instanceof PpidApiError ? err.message : t('Gagal menghapus data'),
												{ variant: 'error' }
											);
										}
									}}
								>
									{t('Hapus')} {table.getSelectedRowModel().rows.length} {t('data')}
								</Button>
							)}

							{daftarFilter.map((filter) =>
									filter.type === 'relation' ? (
										<FilterRelasi
											key={filter.name}
											filter={filter}
											value={nilaiFilter[filter.name] ?? ''}
											onChange={(nilai) =>
												setNilaiFilter((lama) => ({ ...lama, [filter.name]: nilai }))
											}
										/>
									) : filter.type === 'date' ? (
										<TextField
											key={filter.name}
											type="date"
											size="small"
											label={t(filter.label)}
											value={nilaiFilter[filter.name] ?? ''}
											slotProps={{ inputLabel: { shrink: true } }}
											onChange={(event) =>
												setNilaiFilter((lama) => ({
													...lama,
													[filter.name]: event.target.value
												}))
											}
										/>
									) : (
										<TextField
											key={filter.name}
											select
											size="small"
											label={t(filter.label)}
											value={nilaiFilter[filter.name] ?? ''}
											onChange={(event) =>
												setNilaiFilter((lama) => ({
													...lama,
													[filter.name]: event.target.value
												}))
											}
											sx={{ minWidth: 170 }}
										>
											<MenuItem value="">{t(filter.labelKosong ?? 'Semua')}</MenuItem>
											{(filter.options ?? []).map((opsi) => (
												<MenuItem
													key={String(opsi.value)}
													value={String(opsi.value)}
												>
													{t(opsi.label)}
												</MenuItem>
											))}
									</TextField>
								)
							)}
						</div>
					)}
					renderRowActionMenuItems={({ row, closeMenu }: { row: MRT_Row<ApiRecord>; closeMenu: () => void }) =>
						[
							...(aksiBaris?.(row.original, closeMenu) ?? []),
							akses.edit ? (
								<MenuItem
									key="ubah"
									onClick={() => {
										setIdTerpilih(Number(row.original.id));
										setFormTerbuka(true);
										closeMenu();
									}}
								>
									<ListItemIcon>
										<FuseSvgIcon size={18}>lucide:pencil</FuseSvgIcon>
									</ListItemIcon>
									{t('Ubah')}
								</MenuItem>
							) : null,
							akses.delete ? (
								<MenuItem
									key="hapus"
									onClick={() => {
										closeMenu();
										void jalankanHapus(Number(row.original.id));
									}}
								>
									<ListItemIcon>
										<FuseSvgIcon
											size={18}
											color="error"
										>
											lucide:trash
										</FuseSvgIcon>
									</ListItemIcon>
									{t('Hapus')}
								</MenuItem>
							) : null
						].filter(Boolean)
					}
				/>
			</Paper>

			{bolehTulis && (
				<ResourceFormDialog
					config={config}
					recordId={idTerpilih}
					open={formTerbuka}
					onClose={() => setFormTerbuka(false)}
				/>
			)}
		</div>
	);
}

export default ResourceListPage;
