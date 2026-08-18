import { useEffect, useMemo } from 'react';
import { FieldValues, useForm } from 'react-hook-form';
import Dialog from '@mui/material/Dialog';
import DialogTitle from '@mui/material/DialogTitle';
import DialogContent from '@mui/material/DialogContent';
import DialogActions from '@mui/material/DialogActions';
import Button from '@mui/material/Button';
import CircularProgress from '@mui/material/CircularProgress';
import Alert from '@mui/material/Alert';
import { useSnackbar } from 'notistack';
import { useTranslation } from 'react-i18next';
import { PpidApiError } from '../api/ppidApi';
import { useCreateResource, useResourceItem, useUpdateResource } from '../api/useResource';
import { apiPathOf, FieldConfig, ResourceConfig } from '../lib/types';
import ResourceField from './ResourceField';
import JejakDokumen from './JejakDokumen';

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
	const { t } = useTranslation();

	const { data: record, isLoading: memuat } = useResourceItem<Record<string, unknown>>(
		apiPath,
		open && modeUbah ? recordId : undefined
	);

	const fields = useMemo(() => config.fields.filter((f) => fieldTampil(f, modeUbah)), [config.fields, modeUbah]);

	const { control, handleSubmit, reset, setError } = useForm<FieldValues>({
		defaultValues: nilaiAwal(fields)
	});

	const buat = useCreateResource(apiPath);
	const ubah = useUpdateResource(apiPath);
	const menyimpan = buat.isPending || ubah.isPending;

	useEffect(() => {
		if (!open) {
			return;
		}

		reset(nilaiAwal(fields, modeUbah ? record : undefined));
	}, [open, record, fields, modeUbah, reset]);

	async function simpan(values: FieldValues) {
		// String kosong dikirim sebagai null supaya kolom nullable di DB tidak
		// terisi string kosong yang lolos validasi tapi tidak berarti apa-apa.
		const payload = Object.entries(values).reduce<Record<string, unknown>>((hasil, [kunci, nilai]) => {
			hasil[kunci] = nilai === '' ? null : nilai;
			return hasil;
		}, {});

		// Kolom yang dikunci modul (mis. `tipe_laporan`) diisi di sini, bukan
		// lewat formulir, supaya operator tidak bisa salah memilih.
		Object.assign(payload, config.nilaiTetap ?? {});

		try {
			if (modeUbah) {
				await ubah.mutateAsync({ id: recordId, payload });
			} else {
				await buat.mutateAsync(payload);
			}

			enqueueSnackbar(`${t(config.singular)} ${modeUbah ? t('diperbarui') : t('ditambahkan')}`, { variant: 'success' });
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

			enqueueSnackbar(t('Data gagal disimpan'), { variant: 'error' });
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
				{modeUbah ? `${t('Ubah')} ${t(config.singular)}` : `${t('Tambah')} ${t(config.singular)}`}
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

				{modeUbah && !memuat && <JejakDokumen record={record} />}

				{config.readOnly && (
					<Alert
						severity="info"
						className="mt-4"
					>
						{t('Modul ini hanya dapat dibaca.')}
					</Alert>
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
					type="submit"
					form="form-resource"
					variant="contained"
					color="secondary"
					disabled={menyimpan || memuat}
					startIcon={menyimpan ? <CircularProgress size={16} /> : undefined}
				>
					{t('Simpan')}
				</Button>
			</DialogActions>
		</Dialog>
	);
}

export default ResourceFormDialog;
