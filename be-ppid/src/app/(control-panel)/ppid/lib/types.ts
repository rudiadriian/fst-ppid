/**
 * Kontrak konfigurasi modul CMS.
 *
 * Satu modul = satu objek `ResourceConfig`. Halaman daftar dan formulirnya
 * dirakit otomatis dari konfigurasi ini, jadi menambah modul baru tidak
 * memerlukan komponen React baru.
 */

export type FieldType =
	| 'text'
	| 'textarea'
	| 'richtext'
	| 'number'
	| 'select'
	| 'boolean'
	| 'date'
	| 'file'
	| 'image'
	| 'relation'
	| 'files';

export type PilihanOpsi = {
	value: string | number;
	label: string;
};

export type FieldConfig = {
	name: string;
	label: string;
	type: FieldType;
	required?: boolean;
	/** Untuk type 'select'. */
	options?: PilihanOpsi[];
	/** Untuk type 'relation': ambil opsi dari resource lain. */
	relation?: { resource: string; labelKey: string };
	/** Untuk type 'file' | 'image' | 'files'. */
	upload?: { folder: string; jenis: 'gambar' | 'dokumen' | 'video' };
	help?: string;
	rows?: number;
	/** Lebar kolom pada grid dua kolom. */
	span?: 1 | 2;
	defaultValue?: unknown;
	/** Sembunyikan field pada mode tertentu (mis. status hanya saat edit). */
	hideOnCreate?: boolean;
	hideOnEdit?: boolean;
	/** Tampilkan tapi tidak bisa diubah. */
	readOnly?: boolean;
	min?: number;
	max?: number;
	maxLength?: number;
};

export type ColumnType = 'text' | 'number' | 'date' | 'datetime' | 'boolean' | 'badge' | 'relation' | 'file';

export type ColumnConfig = {
	key: string;
	label: string;
	type?: ColumnType;
	/** Untuk type 'relation': `key` menunjuk objek relasi, ini nama propertinya. */
	relationKey?: string;
	/** Untuk type 'badge': pemetaan nilai ke label + warna chip MUI. */
	badgeMap?: Record<string, { label: string; color: 'default' | 'primary' | 'secondary' | 'success' | 'warning' | 'error' | 'info' }>;
	size?: number;
	/** Kolom ini tidak bisa diurutkan di server. */
	noSort?: boolean;
};

export type FilterConfig = {
	name: string;
	label: string;
	type: 'select' | 'relation' | 'date';
	options?: PilihanOpsi[];
	relation?: { resource: string; labelKey: string };
};

export type ResourceConfig = {
	/** Segmen URL di panel admin: /ppid/{slug}. */
	slug: string;
	/** Path resource di API; default sama dengan slug. */
	apiPath?: string;
	/** Slug modul di `modul_sistem`, dasar pemeriksaan hak akses. */
	modul: string;
	title: string;
	singular: string;
	description?: string;
	icon?: string;
	columns: ColumnConfig[];
	fields: FieldConfig[];
	filters?: FilterConfig[];
	defaultSort?: string;
	searchPlaceholder?: string;
	/** Modul yang hanya bisa dibaca (mis. audit log). */
	readOnly?: boolean;
	/** Formulir dibuka sebagai halaman penuh, bukan dialog. */
	formPenuh?: boolean;
};

export function apiPathOf(config: ResourceConfig): string {
	return config.apiPath ?? config.slug;
}
