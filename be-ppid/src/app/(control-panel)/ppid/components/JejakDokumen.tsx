import Typography from '@mui/material/Typography';
import { useTranslation } from 'react-i18next';
import FuseSvgIcon from '@fuse/core/FuseSvgIcon';
import { formatWaktu } from '../lib/waktu';

type Pelaku = { name?: string } | null | undefined;

type JejakDokumenProps = {
	record?: Record<string, unknown>;
};

function namaPelaku(nilai: unknown): string | null {
	const pelaku = nilai as Pelaku;

	return pelaku?.name ? String(pelaku.name) : null;
}

/** Baris jejak tanpa waktu tidak dicetak sama sekali, jadi kosongnya `null`. */
function waktu(nilai: unknown): string | null {
	return nilai ? formatWaktu(nilai, '') || null : null;
}

function Baris({
	ikon,
	label,
	pelaku,
	pada,
	kosong = 'Tidak tercatat'
}: {
	ikon: string;
	label: string;
	pelaku: string | null;
	pada: string | null;
	/** Teks bila baris ini belum punya jejak sama sekali. */
	kosong?: string;
}) {
	const { t } = useTranslation();

	return (
		<div className="flex items-start gap-2">
			<FuseSvgIcon
				size={16}
				className="mt-0.5 text-hint"
			>
				{ikon}
			</FuseSvgIcon>
			<div>
				<Typography
					variant="caption"
					color="text.secondary"
					className="block"
				>
					{t(label)}
				</Typography>
				<Typography variant="body2">
					{pelaku ?? (pada ? t('Tidak tercatat') : t(kosong))}
					{pada ? ` — ${pada}` : ''}
				</Typography>
			</div>
		</div>
	);
}

/**
 * Jejak dokumen: siapa membuat, mengubah, dan menghapus baris ini, beserta
 * waktunya.
 *
 * Nilainya datang dari kolom seragam di API (`pembuat`/`created_at`,
 * `pengubah`/`updated_at`, `penghapus`/`deleted_at`), jadi blok ini bisa
 * dipasang di modul mana pun tanpa konfigurasi tambahan. Baris "Dihapus" hanya
 * muncul bila datanya memang sudah dihapus.
 *
 * Riwayat lengkap tiap perubahan tetap ada di modul Audit Log; yang di sini
 * adalah keadaan terakhir.
 */
export function JejakDokumen({ record }: JejakDokumenProps) {
	const { t } = useTranslation();

	// Modul tanpa kolom jejak (mis. Audit Log) tidak menampilkan apa pun.
	// Pemeriksaannya pada keberadaan kolom, bukan isinya: baris lama yang dibuat
	// sebelum jejak ada tetap menampilkan blok ini dengan "Tidak tercatat",
	// supaya jelas bedanya "belum pernah dicatat" dan "modul ini memang tanpa jejak".
	if (!record || !('created_at' in record)) {
		return null;
	}

	const dibuatOleh = namaPelaku(record.pembuat);
	const dibuatPada = waktu(record.created_at);
	const diubahOleh = namaPelaku(record.pengubah);
	const diubahPada = waktu(record.updated_at);
	const dihapusOleh = namaPelaku(record.penghapus);
	const dihapusPada = waktu(record.deleted_at);

	return (
		<div className="mt-4 rounded-lg border border-divider p-3">
			<Typography
				variant="subtitle2"
				className="mb-2 font-semibold"
			>
				{t('Jejak dokumen')}
			</Typography>

			<div className="grid grid-cols-1 gap-3 sm:grid-cols-3">
				<Baris
					ikon="lucide:file-plus"
					label="Dibuat"
					pelaku={dibuatOleh}
					pada={dibuatPada}
				/>
				<Baris
					ikon="lucide:pencil"
					label="Diubah terakhir"
					pelaku={diubahOleh}
					pada={diubahPada}
					kosong="Belum pernah diubah"
				/>
				{dihapusPada && (
					<Baris
						ikon="lucide:trash"
						label="Dihapus"
						pelaku={dihapusOleh}
						pada={dihapusPada}
					/>
				)}
			</div>
		</div>
	);
}

export default JejakDokumen;
