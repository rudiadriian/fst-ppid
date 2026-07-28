import { Control, Controller, FieldValues } from 'react-hook-form';
import TextField from '@mui/material/TextField';
import MenuItem from '@mui/material/MenuItem';
import FormControlLabel from '@mui/material/FormControlLabel';
import Switch from '@mui/material/Switch';
import Typography from '@mui/material/Typography';
import { SimpleEditor } from '@/components/tiptap/tiptap-templates/simple/simple-editor';
import { FieldConfig } from '../lib/types';
import { useRelationOptions } from '../api/useResource';
import { LampiranBerkas, MultiUploadField, UploadField } from './UploadField';

type ResourceFieldProps = {
	field: FieldConfig;
	control: Control<FieldValues>;
	disabled?: boolean;
};

/**
 * Satu input formulir, dipilih berdasarkan `field.type`.
 *
 * Semua tipe dibungkus Controller react-hook-form supaya perilakunya seragam
 * (nilai terkendali, pesan error per field dari API bisa ditempelkan).
 */
export function ResourceField({ field, control, disabled }: ResourceFieldProps) {
	const perluRelasi = field.type === 'relation' && Boolean(field.relation);
	const { data: opsiRelasi, isLoading: memuatRelasi } = useRelationOptions(
		field.relation?.resource ?? '',
		field.relation?.labelKey ?? 'nama',
		perluRelasi
	);

	const opsi = field.type === 'relation' ? (opsiRelasi ?? []) : (field.options ?? []);

	return (
		<Controller
			name={field.name}
			control={control}
			rules={{ required: field.required ? `${field.label} wajib diisi.` : false }}
			render={({ field: rhf, fieldState }) => {
				const pesanError = fieldState.error?.message;
				const nonaktif = disabled || field.readOnly;

				if (field.type === 'boolean') {
					return (
						<div>
							<FormControlLabel
								control={
									<Switch
										checked={Boolean(rhf.value)}
										onChange={(event) => rhf.onChange(event.target.checked)}
										disabled={nonaktif}
									/>
								}
								label={field.label}
							/>
							{(pesanError || field.help) && (
								<Typography
									variant="caption"
									color={pesanError ? 'error' : 'text.secondary'}
									className="block"
								>
									{pesanError || field.help}
								</Typography>
							)}
						</div>
					);
				}

				if (field.type === 'richtext') {
					return (
						<div>
							<Typography
								variant="caption"
								className="mb-1 block font-medium"
							>
								{field.label}
								{field.required ? ' *' : ''}
							</Typography>
							<SimpleEditor
								value={(rhf.value as string) ?? ''}
								onChange={rhf.onChange}
								error={pesanError}
								required={field.required}
								className=""
							/>
							{(pesanError || field.help) && (
								<Typography
									variant="caption"
									color={pesanError ? 'error' : 'text.secondary'}
									className="mt-1 block"
								>
									{pesanError || field.help}
								</Typography>
							)}
						</div>
					);
				}

				if (field.type === 'file' || field.type === 'image') {
					return (
						<UploadField
							field={field}
							value={(rhf.value as string) ?? null}
							onChange={rhf.onChange}
							disabled={nonaktif}
							errorText={pesanError}
						/>
					);
				}

				if (field.type === 'files') {
					return (
						<MultiUploadField
							field={field}
							value={(rhf.value as LampiranBerkas[]) ?? []}
							onChange={rhf.onChange}
							disabled={nonaktif}
						/>
					);
				}

				if (field.type === 'select' || field.type === 'relation') {
					return (
						<TextField
							select
							label={field.label}
							required={field.required}
							fullWidth
							size="small"
							disabled={nonaktif || (field.type === 'relation' && memuatRelasi)}
							error={Boolean(pesanError)}
							helperText={pesanError || field.help}
							value={rhf.value ?? ''}
							onChange={(event) => {
								const nilai = event.target.value;
								rhf.onChange(nilai === '' ? null : nilai);
							}}
							onBlur={rhf.onBlur}
						>
							{!field.required && (
								<MenuItem value="">
									<em>— tidak dipilih —</em>
								</MenuItem>
							)}
							{opsi.map((item) => (
								<MenuItem
									key={String(item.value)}
									value={item.value}
								>
									{item.label}
								</MenuItem>
							))}
						</TextField>
					);
				}

				const tipeInput = field.type === 'number' ? 'number' : field.type === 'date' ? 'date' : 'text';

				return (
					<TextField
						{...rhf}
						value={rhf.value ?? ''}
						type={tipeInput}
						label={field.label}
						required={field.required}
						fullWidth
						size="small"
						disabled={nonaktif}
						multiline={field.type === 'textarea'}
						minRows={field.type === 'textarea' ? (field.rows ?? 3) : undefined}
						error={Boolean(pesanError)}
						helperText={pesanError || field.help}
						slotProps={{
							inputLabel: field.type === 'date' ? { shrink: true } : undefined,
							htmlInput: {
								maxLength: field.maxLength,
								min: field.min,
								max: field.max
							}
						}}
						onChange={(event) => {
							const nilai = event.target.value;

							if (field.type === 'number') {
								rhf.onChange(nilai === '' ? null : Number(nilai));
								return;
							}

							rhf.onChange(nilai);
						}}
					/>
				);
			}}
		/>
	);
}

export default ResourceField;
