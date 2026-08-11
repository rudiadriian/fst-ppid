import { useEffect, useMemo, useState } from 'react';
import { FieldValues, useForm } from 'react-hook-form';
import Dialog from '@mui/material/Dialog';
import DialogTitle from '@mui/material/DialogTitle';
import DialogContent from '@mui/material/DialogContent';
import DialogActions from '@mui/material/DialogActions';
import Button from '@mui/material/Button';
import CircularProgress from '@mui/material/CircularProgress';
import Alert from '@mui/material/Alert';
import { useSnackbar } from 'notistack';
import { ppidApi, PpidApiError } from '../api/ppidApi';
import { useCreateResource, useResourceItem, useUpdateResource } from '../api/useResource';
import { apiPathOf, FieldConfig, ResourceConfig } from '../lib/types';
import ResourceField from './ResourceField';

type ResourceFormDialogProps = {
	config: ResourceConfig;
	/** null = mode tambah; angka = mode ubah. */
	recordId: number | null;
	open: boolean;
	onClose: () => void;
};

function fieldTampil(field: FieldConfig, modeUbah: boolean): boolean {
	return modeUbah ? !field.hideOnEdit : !field.hideOnCreate;
}

/**
 * Nilai awal formulir.
 *
 * Field yang tidak ada di data (mode tambah) diberi nilai kosong yang cocok
 * dengan tipenya, supaya input tidak berpindah dari uncontrolled ke controlled.
 */
function nilaiAwal(fields: FieldConfig[], data?: Record<string, unknown>): FieldValues {
	return fields.reduce<FieldValues>((hasil, field) => {
		const dariData = data?.[field.name];

		if (dariData !== undefined && dariData !== null) {
			// Input date HTML hanya menerima format YYYY-MM-DD.
			if (field.type === 'date' && typeof dariData === 'string') {
				hasil[field.name] = dariData.slice(0, 10);
				return hasil;
			}

			hasil[field.name] = dariData;
			return hasil;
		}

		if (field.defaultValue !== undefined) {
			hasil[field.name] = field.defaultValue;
			return hasil;
		}

		hasil[field.name] = field.type === 'boolean' ? false : field.type === 'files' ? [] : '';

		return hasil;
	}, {});
}

export function ResourceFormDialog({ config, recordId, open, onClose }: ResourceFormDialogProps) {
	const apiPath = apiPathOf(config);
	const modeUbah = recordId !== null;
	const { enqueueSnackbar } = useSnackbar();

	const { data: record, isLoading: memuat } = useResourceItem<Record<string, unknown>>(
		apiPath,
		open && modeUbah ? recordId : undefined
	);

	const fields = useMemo(() => config.fields.filter((f) => fieldTampil(f, modeUbah)), [config.fields, modeUbah]);

	const { control, handleSubmit, reset, setError, getValues, setValue } = useForm<FieldValues>({
		defaultValues: nilaiAwal(fields)
	});

	const [menghitung, setMenghitung] = useState(false);

	const buat = useCreateResource(apiPath);
	const ubah = useUpdateResource(apiPath);
	const menyimpan = buat.isPending || ubah.isPending;

	useEffect(() => {
		if (!open) {
			return;
		}

		reset(nilaiAwal(fields, modeUbah ? record : undefined));
	}, [open, record, fields, modeUbah, reset]);

	/**
	 * Isi field rekap dari endpoint hitung otomatis. Nilai lama ditimpa supaya
	 * angka yang tersimpan selalu cocok dengan data permohonan di database.
	 */
	async function isiOtomatis() {
		const aksi = config.aksiIsiOtomatis;

		if (!aksi) {
			return;
		}

		const params: Record<string, string> = {};

		for (const nama of aksi.params) {
			const nilai = getValues(nama);

			if (nilai === undefined || nilai === null || nilai === '') {
				const label = config.fields.find((f) => f.name === nama)?.label ?? nama;
				enqueueSnackbar(`Isi dulu ${label} sebelum menghitung otomatis.`, { variant: 'warning' });
				return;
			}

			params[nama] = String(nilai);
		}

		setMenghitung(true);

		try {
			const hasil = await ppidApi.ambil<Record<string, unknown>>(aksi.endpoint, params);

			aksi.isi.forEach((nama) => {
				if (nama in hasil) {
					setValue(nama, (hasil[nama] ?? '') as never, { shouldDirty: true });
				}
			});

			enqueueSnackbar('Angka rekap diperbarui dari data permohonan.', { variant: 'success' });
		} catch (error) {
			enqueueSnackbar(
				error instanceof PpidApiError ? error.message : 'Gagal menghitung rekap otomatis.',
				{ variant: 'error' }
			);
		} finally {
			setMenghitung(false);
		}
	}

	async function simpan(values: FieldValues) {
		// String kosong dikirim sebagai null supaya kolom nullable di DB tidak
		// terisi string kosong yang lolos validasi tapi tidak berarti apa-apa.
		const payload = Object.entries(values).reduce<Record<string, unknown>>((hasil, [kunci, nilai]) => {
			hasil[kunci] = nilai === '' ? null : nilai;
			return hasil;
		}, {});

		try {
			if (modeUbah) {
				await ubah.mutateAsync({ id: recordId, payload });
			} else {
				await buat.mutateAsync(payload);
			}

			enqueueSnackbar(`${config.singular} ${modeUbah ? 'diperbarui' : 'ditambahkan'}`, { variant: 'success' });
			onClose();
		} catch (error) {
			if (error instanceof PpidApiError) {
				// Pesan validasi API ditempelkan ke input yang bersangkutan.
				Object.entries(error.errors).forEach(([nama, pesan]) => {
					setError(nama as never, { type: 'server', message: pesan[0] });
				});

				enqueueSnackbar(error.message, { variant: 'error' });
				return;
			}

			enqueueSnackbar('Data gagal disimpan', { variant: 'error' });
		}
	}

	return (
		<Dialog
			open={open}
			onClose={menyimpan ? undefined : onClose}
			fullWidth
			maxWidth="md"
			scroll="paper"
		>
			<DialogTitle>
				{modeUbah ? `Ubah ${config.singular}` : `Tambah ${config.singular}`}
			</DialogTitle>

			<DialogContent dividers>
				{memuat ? (
					<div className="flex justify-center py-10">
						<CircularProgress />
					</div>
				) : (
					<form
						id="form-resource"
						onSubmit={handleSubmit(simpan)}
						className="grid grid-cols-1 gap-4 pt-1 md:grid-cols-2"
					>
						{fields.map((field) => (
							<div
								key={field.name}
								className={field.span === 2 || field.type === 'richtext' ? 'md:col-span-2' : ''}
							>
								<ResourceField
									field={field}
									control={control}
									disabled={menyimpan}
								/>
							</div>
						))}
					</form>
				)}

				{config.aksiIsiOtomatis?.help && (
					<Alert
						severity="info"
						className="mt-4"
					>
						{config.aksiIsiOtomatis.help}
					</Alert>
				)}

				{config.readOnly && (
					<Alert
						severity="info"
						className="mt-4"
					>
						Modul ini hanya dapat dibaca.
					</Alert>
				)}
			</DialogContent>

			<DialogActions>
				{config.aksiIsiOtomatis && (
					<Button
						onClick={isiOtomatis}
						disabled={menyimpan || memuat || menghitung}
						startIcon={menghitung ? <CircularProgress size={16} /> : undefined}
						className="mr-auto"
					>
						{config.aksiIsiOtomatis.label}
					</Button>
				)}
				<Button
					onClick={onClose}
					disabled={menyimpan}
				>
					Batal
				</Button>
				<Button
					type="submit"
					form="form-resource"
					variant="contained"
					color="secondary"
					disabled={menyimpan || memuat}
					startIcon={menyimpan ? <CircularProgress size={16} /> : undefined}
				>
					Simpan
				</Button>
			</DialogActions>
		</Dialog>
	);
}

export default ResourceFormDialog;
