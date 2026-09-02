import { JENIS_KEBERATAN } from './statusPengajuan';
import { ResourceConfig } from './types';

/**
 * Definisi seluruh modul CMS PPID.
 *
 * Ini satu-satunya tempat yang perlu disentuh untuk menambah/mengubah modul:
 * halaman daftar, formulir, filter, dan route dibangun dari sini. Slug modul
 * harus sama dengan slug di tabel `modul_sistem` agar hak akses cocok.
 */

const STATUS_KONTEN = [
	{ value: 'draft', label: 'Draft' },
	{ value: 'published', label: 'Terbit' },
	{ value: 'archived', label: 'Arsip' }
];

const BADGE_KONTEN = {
	draft: { label: 'Draft', color: 'default' as const },
	menunggu_review: { label: 'Menunggu Review', color: 'warning' as const },
	published: { label: 'Terbit', color: 'success' as const },
	archived: { label: 'Arsip', color: 'default' as const }
};

/**
 * Halaman Standar Layanan yang bisa menayangkan alur bergambar.
 *
 * Nilainya adalah slug rute situs publik (`/standar-layanan/{slug}`), jadi
 * menambah pilihan di sini harus dibarengi halaman yang sudah ada di fe-ppid.
 */
const HALAMAN_ALUR = [
	{ value: 'prosedur-permohonan', label: 'Prosedur Permohonan Informasi' },
	{ value: 'prosedur-keberatan', label: 'Prosedur Permohonan Keberatan' }
];

const BADGE_HALAMAN_ALUR = {
	'prosedur-permohonan': { label: 'Prosedur Permohonan', color: 'success' as const },
	'prosedur-keberatan': { label: 'Prosedur Keberatan', color: 'warning' as const }
};

/** Status Verifikasi Data Diri Pemohon; nilainya dari kolom `status_verifikasi`. */
const BADGE_VERIFIKASI = {
	belum: { label: 'Belum Diverifikasi', color: 'default' as const },
	menunggu: { label: 'Menunggu Pemeriksaan', color: 'warning' as const },
	terverifikasi: { label: 'Terverifikasi', color: 'success' as const },
	ditolak: { label: 'Ditolak', color: 'error' as const }
};

/**
 * Dua kategori pengajuan layanan (langkah 89).
 *
 * Nilainya sama dengan kolom `jenis` pada endpoint gabungan `pengajuan` di
 * api-ppid, dan menentukan dialog detail mana yang dibuka aksi barisnya.
 */
const JENIS_PENGAJUAN = [
	{ value: 'permohonan', label: 'Permohonan Informasi' },
	{ value: 'keberatan', label: 'Permohonan Keberatan Informasi' }
];

const BADGE_JENIS_PENGAJUAN = {
	permohonan: { label: 'Permohonan Informasi', color: 'info' as const },
	keberatan: { label: 'Keberatan Informasi', color: 'warning' as const }
};

/** Jalur pelayanan; menentukan bentuk tindak lanjut petugas. */
const JALUR_PELAYANAN = [
	{ value: 'online', label: 'Online' },
	{ value: 'langsung', label: 'Langsung' }
];

const BADGE_JALUR = {
	online: { label: 'Online', color: 'info' as const },
	langsung: { label: 'Langsung', color: 'success' as const }
};

/**
 * Keadaan tenggat, dihitung server dari `App\Support\SlaLayanan`.
 *
 * Warnanya dipilih untuk dibaca sekilas dari seberang ruangan: merah hanya
 * untuk yang sudah lewat, kuning untuk yang menuntut tindakan hari ini.
 */
const BADGE_SLA = {
	aman: { label: 'Dalam tenggat', color: 'success' as const },
	segera: { label: 'Segera jatuh tempo', color: 'warning' as const },
	lewat_tenggat: { label: 'Lewat tenggat', color: 'error' as const },
	tepat_waktu: { label: 'Tepat waktu', color: 'success' as const },
	terlambat: { label: 'Terlambat', color: 'error' as const }
};

const KEADAAN_SLA = Object.entries(BADGE_SLA).map(([value, info]) => ({ value, label: info.label }));

const BADGE_PERMOHONAN = {
	diajukan: { label: 'Diajukan', color: 'info' as const },
	diverifikasi: { label: 'Diverifikasi', color: 'info' as const },
	diproses: { label: 'Diproses', color: 'warning' as const },
	revisi: { label: 'Revisi', color: 'warning' as const },
	menunggu_approval: { label: 'Menunggu Persetujuan', color: 'warning' as const },
	disetujui: { label: 'Disetujui', color: 'success' as const },
	ditolak: { label: 'Ditolak', color: 'error' as const },
	ditolak_sebagian: { label: 'Ditolak Sebagian', color: 'error' as const },
	selesai: { label: 'Selesai', color: 'success' as const },
	kedaluwarsa: { label: 'Kedaluwarsa', color: 'default' as const }
};

/**
 * Status keberatan; nilainya dari `KeberatanInformasi::TRANSISI` di api-ppid.
 *
 * Sebelum alur persetujuan berjenjang dipakai, daftar ini hanya memuat tiga
 * status. `revisi`, `menunggu_approval`, dan `ditolak` sudah lama diterima
 * CHECK constraint tabelnya tetapi tidak punya label di panel — barisnya
 * tampil sebagai nilai mentah.
 */
const BADGE_KEBERATAN = {
	diajukan: { label: 'Diajukan', color: 'info' as const },
	diproses: { label: 'Diproses', color: 'warning' as const },
	revisi: { label: 'Revisi', color: 'warning' as const },
	menunggu_approval: { label: 'Menunggu Persetujuan', color: 'warning' as const },
	ditolak: { label: 'Ditolak', color: 'error' as const },
	selesai: { label: 'Selesai', color: 'success' as const }
};

export const resources: ResourceConfig[] = [
	// ------------------------------------------------------------------
	// Informasi publik
	// ------------------------------------------------------------------
	{
		slug: 'kategori-informasi',
		modul: 'kategori-informasi',
		title: 'Kategori Informasi',
		singular: 'Kategori',
		description: 'Pengelompokan informasi publik yang tampil di situs.',
		icon: 'lucide:tag',
		defaultSort: 'urutan',
		columns: [
			{ key: 'nama', label: 'Nama' },
			{ key: 'slug', label: 'Slug' },
			{ key: 'parent', label: 'Induk', type: 'relation', relationKey: 'nama', noSort: true },
			{ key: 'urutan', label: 'Urutan', type: 'number', size: 90 },
			{ key: 'is_active', label: 'Status', type: 'boolean', size: 110 }
		],
		fields: [
			{ name: 'nama', label: 'Nama kategori', type: 'text', required: true, maxLength: 150 },
			{ name: 'nama_en', label: 'Nama kategori (English)', type: 'text', maxLength: 150, help: 'Opsional. Dipakai saat pengunjung memilih bahasa Inggris; bila kosong, teks Indonesia yang tampil.' },
			{ name: 'slug', label: 'Slug', type: 'text', help: 'Kosongkan agar dibuat otomatis dari nama.' },
			{
				name: 'parent_id',
				label: 'Kategori induk',
				type: 'relation',
				relation: { resource: 'kategori-informasi', labelKey: 'nama' },
				help: 'Isi bila kategori ini adalah kelompok di dalam kategori lain. Di situs, kategori anak tampil sebagai kartu dengan tombol "Selengkapnya" pada halaman induknya.'
			},
			{ name: 'urutan', label: 'Urutan tampil', type: 'number', min: 0, defaultValue: 0 },
			{
				name: 'deskripsi',
				label: 'Deskripsi',
				type: 'textarea',
				span: 2,
				rows: 3,
				help: 'Tampil sebagai teks pengantar di halaman kategori dan ringkasan pada kartunya (dipotong ~120 karakter).'
			},
			{ name: 'deskripsi_en', label: 'Deskripsi (English)', type: 'textarea', span: 2, rows: 3, help: 'Opsional. Dipakai saat pengunjung memilih bahasa Inggris; bila kosong, teks Indonesia yang tampil.' },
			{ name: 'is_active', label: 'Aktif', type: 'boolean', defaultValue: true }
		],
		filters: [
			{
				name: 'is_active',
				label: 'Status',
				type: 'select',
				options: [
					{ value: 'true', label: 'Aktif' },
					{ value: 'false', label: 'Nonaktif' }
				]
			}
		]
	},
	{
		slug: 'informasi-publik',
		modul: 'informasi-publik',
		title: 'Informasi Publik',
		singular: 'Informasi',
		description: 'Daftar informasi yang wajib disediakan dan diumumkan.',
		icon: 'lucide:file-text',
		defaultSort: '-id',
		searchPlaceholder: 'Cari judul, ringkasan, nomor klasifikasi…',
		columns: [
			{ key: 'nomor_klasifikasi', label: 'No.', size: 80, noSort: true },
			{ key: 'judul', label: 'Judul', size: 320 },
			{ key: 'kategori', label: 'Kategori', type: 'relation', relationKey: 'nama', noSort: true },
			{ key: 'status', label: 'Status', type: 'badge', badgeMap: BADGE_KONTEN, size: 140 },
			{ key: 'tanggal_publikasi', label: 'Terbit', type: 'date', size: 130 },
			{ key: 'unduhan_terbatas', label: 'Unduhan terbatas', type: 'boolean', size: 150, noSort: true },
			{ key: 'views_count', label: 'Dilihat', type: 'number', size: 100 }
		],
		fields: [
			{ name: 'judul', label: 'Judul', type: 'text', required: true, span: 2, maxLength: 255 },
			{ name: 'judul_en', label: 'Judul (English)', type: 'text', span: 2, maxLength: 255, help: 'Opsional. Dipakai saat pengunjung memilih bahasa Inggris; bila kosong, teks Indonesia yang tampil.' },
			{
				name: 'kategori_id',
				label: 'Kategori',
				type: 'relation',
				required: true,
				relation: { resource: 'kategori-informasi', labelKey: 'nama' }
			},
			{ name: 'nomor_klasifikasi', label: 'Nomor klasifikasi', type: 'text', maxLength: 50 },
			{
				name: 'status',
				label: 'Status',
				type: 'select',
				options: [
					{ value: 'draft', label: 'Draft' },
					{ value: 'menunggu_review', label: 'Menunggu Review' },
					{ value: 'published', label: 'Terbit' },
					{ value: 'archived', label: 'Arsip' }
				],
				defaultValue: 'draft',
				help: 'Situs publik hanya menampilkan entri berstatus "Terbit".'
			},
			{ name: 'tanggal_publikasi', label: 'Tanggal publikasi', type: 'date' },
			{ name: 'slug', label: 'Slug', type: 'text', help: 'Kosongkan agar dibuat otomatis. Mengubahnya memutus tautan lama.' },
			{ name: 'ringkasan', label: 'Ringkasan', type: 'textarea', span: 2, rows: 3 },
			{ name: 'ringkasan_en', label: 'Ringkasan (English)', type: 'textarea', span: 2, rows: 3, help: 'Opsional. Dipakai saat pengunjung memilih bahasa Inggris; bila kosong, teks Indonesia yang tampil.' },
			{
				name: 'tautan',
				label: 'Tautan halaman',
				type: 'text',
				span: 2,
				maxLength: 500,
				help: 'Alamat halaman tempat informasi ini dapat DIBACA (mis. https://foodstation.id/laporan-tahunan-fstj/). Di situs, tombol "Di Lihat Saja" pada dialog dokumen menuju alamat ini — terbuka untuk siapa saja tanpa masuk. Lampiran dokumen di bawah dipakai untuk salinan yang DIUNDUH, dan itu menuntut permohonan yang disetujui.'
			},
			{ name: 'konten', label: 'Isi informasi', type: 'richtext', span: 2 },
			{ name: 'konten_en', label: 'Isi informasi (English)', type: 'richtext', span: 2, help: 'Opsional. Dipakai saat pengunjung memilih bahasa Inggris; bila kosong, teks Indonesia yang tampil.' },
			{
				name: 'files',
				label: 'Lampiran dokumen',
				type: 'files',
				span: 2,
				upload: { folder: 'informasi-publik', jenis: 'dokumen' },
				help: 'PDF/Office, maksimal 20 MB per berkas. Dipakai bila entri ini berupa berkas, bukan tautan.'
			},
			{
				name: 'unduhan_terbatas',
				label: 'Unduhan terbatas',
				type: 'boolean',
				span: 2,
				defaultValue: true,
				help: 'Menyala secara bawaan. Dokumen tetap bisa dibaca siapa saja lewat Tautan halaman, tetapi salinannya hanya bisa diunduh pemohon yang permohonannya atas dokumen ini sudah Anda setujui; berkasnya dipindahkan ke penyimpanan tertutup begitu disimpan. Matikan hanya bila dokumen ini memang boleh diunduh siapa saja tanpa permohonan.'
			}
		],
		filters: [
			{
				name: 'status',
				label: 'Status',
				type: 'select',
				options: [
					{ value: 'draft', label: 'Draft' },
					{ value: 'menunggu_review', label: 'Menunggu Review' },
					{ value: 'published', label: 'Terbit' },
					{ value: 'archived', label: 'Arsip' }
				]
			},
			{
				name: 'kategori_id',
				label: 'Kategori',
				type: 'relation',
				relation: { resource: 'kategori-informasi', labelKey: 'nama' }
			}
		]
	},
	{
		slug: 'informasi-dikecualikan',
		modul: 'informasi-dikecualikan',
		title: 'Informasi Dikecualikan',
		singular: 'Informasi Dikecualikan',
		description: 'Daftar informasi yang dikecualikan beserta dasar hukumnya.',
		icon: 'lucide:lock',
		columns: [
			{ key: 'judul', label: 'Judul', size: 300 },
			{ key: 'dasar_hukum_pengecualian', label: 'Dasar hukum' },
			{ key: 'jangka_waktu_pengecualian', label: 'Jangka waktu', size: 140 },
			{ key: 'status', label: 'Status', type: 'badge', badgeMap: BADGE_KONTEN, size: 120 },
			{ key: 'tanggal_penetapan', label: 'Ditetapkan', type: 'date', size: 130 }
		],
		fields: [
			{ name: 'judul', label: 'Judul', type: 'text', required: true, span: 2, maxLength: 255 },
			{ name: 'judul_en', label: 'Judul (English)', type: 'text', span: 2, maxLength: 255, help: 'Opsional. Dipakai saat pengunjung memilih bahasa Inggris; bila kosong, teks Indonesia yang tampil.' },
			{ name: 'dasar_hukum_pengecualian', label: 'Dasar hukum', type: 'text', maxLength: 255 },
			{ name: 'jangka_waktu_pengecualian', label: 'Jangka waktu', type: 'text', maxLength: 100 },
			{ name: 'tanggal_penetapan', label: 'Tanggal penetapan', type: 'date' },
			{ name: 'status', label: 'Status', type: 'select', options: STATUS_KONTEN, defaultValue: 'draft' },
			{ name: 'ringkasan', label: 'Ringkasan', type: 'textarea', span: 2, rows: 2 },
			{ name: 'ringkasan_en', label: 'Ringkasan (English)', type: 'textarea', span: 2, rows: 2, help: 'Opsional. Dipakai saat pengunjung memilih bahasa Inggris; bila kosong, teks Indonesia yang tampil.' },
			{
				name: 'alasan_pengecualian',
				label: 'Alasan pengecualian',
				type: 'textarea',
				span: 2,
				rows: 4,
				help: 'Opsional. Keterangan penetapan tidak ditampilkan di situs publik; isi hanya bila diperlukan untuk arsip internal.'
			},
			{
				name: 'file_surat_penetapan',
				label: 'Surat penetapan',
				type: 'file',
				span: 2,
				upload: { folder: 'informasi-dikecualikan', jenis: 'dokumen' }
			}
		],
		filters: [{ name: 'status', label: 'Status', type: 'select', options: STATUS_KONTEN }]
	},

	// ------------------------------------------------------------------
	// Layanan permohonan
	// ------------------------------------------------------------------
	{
		slug: 'pemohon',
		modul: 'permohonan',
		title: 'Pemohon',
		singular: 'Pemohon',
		description:
			'Identitas pemohon informasi. Datanya diisi sendiri oleh pengunjung lewat Registrasi Akun di situs publik; petugas memeriksa dan memutuskan hasil Verifikasi Data Diri lewat aksi baris. NIK tidak ditampilkan di panel.',
		icon: 'lucide:user',
		// Tanpa tambah/ubah/hapus: datanya milik pengunjung dan disunting dari
		// akunnya sendiri. Satu-satunya tindakan petugas adalah menyetujui atau
		// menolak berkas verifikasi — butuh hak `Setujui`, bukan `Ubah`.
		readOnly: true,
		defaultSort: '-created_at',
		columns: [
			{ key: 'nama', label: 'Nama' },
			{ key: 'email', label: 'Email' },
			{ key: 'no_hp', label: 'No. HP', size: 140 },
			{ key: 'jenis_pemohon', label: 'Jenis', size: 120 },
			{
				key: 'status_verifikasi',
				label: 'Verifikasi',
				type: 'badge',
				badgeMap: BADGE_VERIFIKASI,
				size: 160
			},
			{ key: 'jumlah_ditolak', label: 'Jumlah Ditolak', type: 'number', size: 130 },
			{ key: 'verifikator', label: 'Diperiksa oleh', type: 'relation', relationKey: 'name', size: 160, noSort: true },
			{ key: 'nama_lembaga', label: 'Lembaga' }
		],
		// Tanpa formulir: modulnya hanya dibaca.
		fields: [],
		filters: [
			{
				name: 'status_verifikasi',
				label: 'Verifikasi',
				type: 'select',
				options: [
					{ value: 'menunggu', label: 'Menunggu Pemeriksaan' },
					{ value: 'terverifikasi', label: 'Terverifikasi' },
					{ value: 'ditolak', label: 'Ditolak' },
					{ value: 'belum', label: 'Belum Diverifikasi' }
				]
			},
			{
				name: 'jenis_pemohon',
				label: 'Jenis',
				type: 'select',
				// Sama dengan pilihan pada formulir Data Pemohon di situs publik
				// (`App\Models\Pemohon::JENIS`).
				options: [
					{ value: 'perorangan', label: 'Perorangan' },
					{ value: 'mahasiswa', label: 'Mahasiswa' },
					{ value: 'lembaga', label: 'Lembaga / Organisasi / Perusahaan' },
					{ value: 'kelompok', label: 'Kelompok Orang' }
				]
			}
		]
	},
	{
		slug: 'permohonan',
		modul: 'permohonan',
		title: 'Permohonan',
		singular: 'Pengajuan',
		/*
		 * Satu daftar untuk dua kategori (langkah 89). Petugas menangani
		 * permohonan dan keberatan dengan gerak yang sama — buka detail,
		 * periksa, teruskan — jadi keduanya dibaca di sini dan dibedakan kolom
		 * Kategori. Dua menu terpisah memaksa petugas memeriksa dua tempat
		 * untuk menjawab satu pertanyaan: apa yang menunggu saya hari ini.
		 */
		apiPath: 'pengajuan',
		description:
			'Permohonan Informasi dan Permohonan Keberatan Informasi dalam satu daftar, dibedakan kolom Kategori. Isinya ditulis pemohon lewat portal, jadi tidak bisa ditambah, disunting, atau dihapus dari panel — tindakan petugas berupa verifikasi, putusan persetujuan berjenjang, dan perpindahan status lewat aksi baris, yang seluruhnya tercatat. Buka Detail untuk melihat pengajuan selengkapnya.',
		icon: 'lucide:inbox',
		// Data kiriman pemohon: tanpa tambah, ubah, dan hapus. Endpoint
		// `store`/`update`/`destroy`-nya juga tidak didaftarkan di api-ppid.
		tanpaTambah: true,
		tanpaUbah: true,
		tanpaHapus: true,
		defaultSort: '-tanggal_pengajuan',
		searchPlaceholder: 'Cari kode, pokok pengajuan, atau nama pemohon…',
		columns: [
			{
				key: 'jenis',
				label: 'Kategori',
				type: 'badge',
				badgeMap: BADGE_JENIS_PENGAJUAN,
				size: 190,
				noSort: true
			},
			{ key: 'kode', label: 'Kode', size: 190, noSort: true },
			{ key: 'nama_pemohon', label: 'Pemohon', size: 180, noSort: true },
			{ key: 'pokok', label: 'Pokok pengajuan', size: 260, noSort: true },
			{ key: 'status', label: 'Status', type: 'badge', badgeMap: BADGE_PERMOHONAN, size: 170, noSort: true },
			{ key: 'jalur_pelayanan', label: 'Jalur', type: 'badge', badgeMap: BADGE_JALUR, size: 120, noSort: true },
			{ key: 'sla_keadaan', label: 'Tenggat', type: 'badge', badgeMap: BADGE_SLA, size: 170, noSort: true },
			{ key: 'tanggal_pengajuan', label: 'Diajukan', type: 'datetime', size: 160, noSort: true },
			{ key: 'batas_waktu_tanggapan', label: 'Batas waktu', type: 'datetime', size: 160, noSort: true }
		],
		// Tanpa formulir: modul ini tidak punya jalur tambah maupun ubah.
		// Seluruh isian pengajuan dibaca lewat aksi baris "Lihat detail".
		fields: [],
		filters: [
			{ name: 'jenis', label: 'Kategori', type: 'select', options: JENIS_PENGAJUAN },
			{
				name: 'status',
				label: 'Status',
				type: 'select',
				options: Object.entries(BADGE_PERMOHONAN).map(([value, info]) => ({ value, label: info.label }))
			},
			{ name: 'jalur_pelayanan', label: 'Jalur pelayanan', type: 'select', options: JALUR_PELAYANAN },
			{ name: 'sla_keadaan', label: 'Tenggat', type: 'select', options: KEADAAN_SLA }
		]
	},
	{
		slug: 'keberatan',
		modul: 'keberatan',
		title: 'Keberatan',
		singular: 'Keberatan',
		description:
			'Keberatan atas layanan informasi. Alasan dan kasus posisinya pernyataan pemohon, jadi tidak bisa ditambah, disunting, atau dihapus dari panel — petugas mengisi status dan tanggapan atasan lewat aksi baris. Buka Detail untuk melihat keberatan selengkapnya.',
		icon: 'lucide:triangle-alert',
		tanpaTambah: true,
		tanpaUbah: true,
		tanpaHapus: true,
		defaultSort: '-tanggal_keberatan',
		columns: [
			{ key: 'kode_keberatan', label: 'Kode keberatan', size: 190 },
			{ key: 'permohonan', label: 'Kode permohonan', type: 'relation', relationKey: 'kode_permohonan', noSort: true },
			{ key: 'pemohon', label: 'Pemohon', type: 'relation', relationKey: 'nama', noSort: true },
			{ key: 'jenis_keberatan', label: 'Alasan', type: 'map', mapValues: JENIS_KEBERATAN, size: 260 },
			{ key: 'status', label: 'Status', type: 'badge', size: 170, badgeMap: BADGE_KEBERATAN },
			{ key: 'tanggal_keberatan', label: 'Diajukan', type: 'datetime', size: 160 }
		],
		// Tanpa formulir: jenis, alasan, kasus posisi, dan penguasaan adalah
		// pernyataan pemohon. Petugas menanggapinya lewat aksi baris
		// "Tanggapan & status" dan membacanya lewat "Lihat detail".
		fields: [],
		filters: [
			{
				name: 'status',
				label: 'Status',
				type: 'select',
				options: Object.entries(BADGE_KEBERATAN).map(([value, info]) => ({ value, label: info.label }))
			},
			{
				name: 'jenis_keberatan',
				label: 'Alasan keberatan',
				type: 'select',
				options: Object.entries(JENIS_KEBERATAN).map(([value, label]) => ({ value, label }))
			}
		]
	},

	// ------------------------------------------------------------------
	// Laporan
	// ------------------------------------------------------------------
	// Modul Laporan Statistik dilepas dari panel pada langkah 58, lalu dihapus
	// tuntas pada langkah 68: halaman publiknya, endpoint
	// `laporan-layanan/rekap`, dan tipe `statistik_informasi` sudah tidak ada.
	// Tabel `laporan_layanan` kini melayani satu modul saja, Laporan Pelayanan.
	{
		slug: 'laporan-pelayanan',
		apiPath: 'laporan-layanan',
		modul: 'laporan-layanan',
		title: 'Laporan Pelayanan',
		singular: 'Laporan Pelayanan',
		description: 'Berkas Laporan Pelayanan Informasi per tahun untuk situs publik.',
		icon: 'lucide:file-text',
		defaultSort: '-tahun',
		nilaiTetap: { tipe_laporan: 'pelayanan_informasi' },
		searchPlaceholder: 'Cari judul atau periode…',
		columns: [
			{ key: 'judul', label: 'Judul', size: 320 },
			{ key: 'tahun', label: 'Tahun', type: 'number', size: 90 },
			{ key: 'periode', label: 'Periode', size: 130 },
			{ key: 'status', label: 'Status', type: 'badge', badgeMap: BADGE_KONTEN, size: 120 },
			{ key: 'publisher', label: 'Diunggah oleh', type: 'relation', relationKey: 'name', size: 180, noSort: true },
			{ key: 'created_at', label: 'Tanggal publikasi', type: 'datetime', size: 170 },
			{ key: 'file_laporan', label: 'Berkas', type: 'file', size: 110, noSort: true }
		],
		fields: [
			{ name: 'judul', label: 'Judul laporan', type: 'text', required: true, span: 2, maxLength: 255 },
			{ name: 'tahun', label: 'Tahun', type: 'number', required: true, min: 2000, max: 2100 },
			{ name: 'periode', label: 'Periode', type: 'text', maxLength: 30, help: 'Contoh: Triwulan I, Semester II, Tahunan.' },
			{
				name: 'status',
				label: 'Status',
				type: 'select',
				options: STATUS_KONTEN,
				defaultValue: 'draft',
				help: 'Situs publik hanya menampilkan laporan berstatus Terbit.'
			},
			{
				name: 'ringkasan',
				label: 'Ringkasan',
				type: 'textarea',
				span: 2,
				rows: 3,
				help: 'Kutipan singkat yang tampil pada kartu laporan dan halaman detail di situs publik.'
			},
			{
				name: 'file_laporan',
				label: 'Berkas laporan',
				type: 'file',
				span: 2,
				help: 'Hanya PDF atau gambar (JPG/PNG/WEBP). Halaman pertamanya dipakai sebagai sampul kartu di situs publik dan isinya dibaca langsung di halaman detail.',
				upload: { folder: 'laporan', jenis: 'dokumen_gambar' }
			}
		],
		filters: [{ name: 'status', label: 'Status', type: 'select', options: STATUS_KONTEN }]
	},
	{
		slug: 'survey-kepuasan',
		// Hak aksesnya menumpang modul Permohonan: survei melekat pada
		// permohonan yang sudah dilayani.
		modul: 'permohonan',
		title: 'Survei',
		singular: 'Survei Kepuasan',
		description:
			'Penilaian pemohon atas layanan informasi, diisi pemohon dari Portal Pemohon setelah permohonannya selesai.',
		icon: 'lucide:smile',
		defaultSort: '-id',
		searchPlaceholder: 'Cari komentar…',
		columns: [
			{ key: 'permohonan', label: 'Permohonan', type: 'relation', relationKey: 'kode_permohonan', size: 180 },
			{ key: 'rating', label: 'Rating', type: 'number', size: 90 },
			{ key: 'komentar', label: 'Komentar', size: 380 },
			{ key: 'created_at', label: 'Tanggal', type: 'datetime', size: 160 }
		],
		fields: [
			{
				name: 'permohonan_id',
				label: 'Permohonan',
				type: 'relation',
				relation: { resource: 'permohonan', labelKey: 'kode_permohonan' },
				help: 'Boleh dikosongkan bila survei tidak terkait satu permohonan tertentu.'
			},
			{
				name: 'rating',
				label: 'Rating (1–5)',
				type: 'number',
				required: true,
				min: 1,
				max: 5,
				help: 'Rata-rata rating dibagi 5 menjadi persentase kepuasan di situs publik.'
			},
			{ name: 'komentar', label: 'Komentar', type: 'textarea', span: 2, rows: 3 }
		],
		filters: [
			{
				name: 'rating',
				label: 'Rating',
				type: 'select',
				options: [
					{ value: 1, label: '1' },
					{ value: 2, label: '2' },
					{ value: 3, label: '3' },
					{ value: 4, label: '4' },
					{ value: 5, label: '5' }
				]
			}
		]
	},

	// ------------------------------------------------------------------
	// Konten situs
	// ------------------------------------------------------------------
	{
		slug: 'kategori-berita',
		modul: 'berita',
		title: 'Kategori Berita',
		singular: 'Kategori Berita',
		icon: 'lucide:tags',
		defaultSort: 'nama',
		columns: [
			{ key: 'nama', label: 'Nama' },
			{ key: 'slug', label: 'Slug' }
		],
		fields: [
			{ name: 'nama', label: 'Nama kategori', type: 'text', required: true, maxLength: 100 },
			{ name: 'nama_en', label: 'Nama kategori (English)', type: 'text', maxLength: 100, help: 'Opsional. Dipakai saat pengunjung memilih bahasa Inggris; bila kosong, teks Indonesia yang tampil.' },
			{ name: 'slug', label: 'Slug', type: 'text', help: 'Kosongkan agar dibuat otomatis.' }
		]
	},
	{
		slug: 'berita',
		modul: 'berita',
		title: 'Berita',
		singular: 'Berita',
		description: 'Artikel dan siaran pers yang tampil di situs publik.',
		icon: 'lucide:newspaper',
		columns: [
			{ key: 'judul', label: 'Judul', size: 320 },
			{ key: 'kategori', label: 'Kategori', type: 'relation', relationKey: 'nama', noSort: true },
			{ key: 'status', label: 'Status', type: 'badge', badgeMap: BADGE_KONTEN, size: 120 },
			{ key: 'tanggal_publikasi', label: 'Terbit', type: 'date', size: 130 },
			{ key: 'views_count', label: 'Dilihat', type: 'number', size: 100 }
		],
		fields: [
			{ name: 'judul', label: 'Judul', type: 'text', required: true, span: 2, maxLength: 255 },
			{ name: 'judul_en', label: 'Judul (English)', type: 'text', span: 2, maxLength: 255, help: 'Opsional. Dipakai saat pengunjung memilih bahasa Inggris; bila kosong, teks Indonesia yang tampil.' },
			{
				name: 'kategori_berita_id',
				label: 'Kategori',
				type: 'relation',
				relation: { resource: 'kategori-berita', labelKey: 'nama' }
			},
			{ name: 'status', label: 'Status', type: 'select', options: STATUS_KONTEN, defaultValue: 'draft' },
			{ name: 'tanggal_publikasi', label: 'Tanggal publikasi', type: 'date' },
			{ name: 'slug', label: 'Slug', type: 'text', help: 'Kosongkan agar dibuat otomatis.' },
			{
				name: 'thumbnail',
				label: 'Gambar sampul',
				type: 'image',
				span: 2,
				upload: { folder: 'berita', jenis: 'gambar' }
			},
			{ name: 'ringkasan', label: 'Ringkasan', type: 'textarea', span: 2, rows: 3 },
			{ name: 'ringkasan_en', label: 'Ringkasan (English)', type: 'textarea', span: 2, rows: 3, help: 'Opsional. Dipakai saat pengunjung memilih bahasa Inggris; bila kosong, teks Indonesia yang tampil.' },
			{ name: 'konten', label: 'Isi berita', type: 'richtext', span: 2 },
			{ name: 'konten_en', label: 'Isi berita (English)', type: 'richtext', span: 2, help: 'Opsional. Dipakai saat pengunjung memilih bahasa Inggris; bila kosong, teks Indonesia yang tampil.' },
		],
		filters: [{ name: 'status', label: 'Status', type: 'select', options: STATUS_KONTEN }]
	},
	{
		slug: 'galeri',
		modul: 'galeri',
		title: 'Galeri',
		singular: 'Item Galeri',
		description: 'Foto dan video kegiatan.',
		icon: 'lucide:image',
		columns: [
			{ key: 'judul', label: 'Judul' },
			{ key: 'tipe', label: 'Tipe', size: 100 },
			{ key: 'path_file', label: 'Berkas', type: 'file', size: 110, noSort: true },
			{ key: 'tanggal', label: 'Tanggal', type: 'date', size: 130 }
		],
		fields: [
			{ name: 'judul', label: 'Judul', type: 'text', maxLength: 255 },
			{
				name: 'tipe',
				label: 'Tipe',
				type: 'select',
				required: true,
				options: [
					{ value: 'foto', label: 'Foto' },
					{ value: 'video', label: 'Video' }
				],
				defaultValue: 'foto'
			},
			{
				name: 'path_file',
				label: 'Berkas media',
				type: 'image',
				required: true,
				span: 2,
				upload: { folder: 'galeri', jenis: 'gambar' },
				help: 'Untuk video, unggah berkas MP4/WebM lewat modul yang sama.'
			},
			{ name: 'tanggal', label: 'Tanggal', type: 'date' },
			{ name: 'deskripsi', label: 'Deskripsi', type: 'textarea', span: 2, rows: 2 }
		],
		filters: [
			{
				name: 'tipe',
				label: 'Tipe',
				type: 'select',
				options: [
					{ value: 'foto', label: 'Foto' },
					{ value: 'video', label: 'Video' }
				]
			}
		]
	},
	{
		slug: 'faq',
		modul: 'faq',
		title: 'FAQ',
		singular: 'FAQ',
		description: 'Pertanyaan yang sering diajukan pemohon.',
		// Nama ikon harus sama dengan id di sprite `public/assets/icons/lucide.svg`.
		// Sebelumnya `circle-question-mark` — id itu tidak ada di sprite, jadi
		// menu FAQ tampil tanpa ikon.
		icon: 'lucide:circle-help',
		defaultSort: 'urutan',
		columns: [
			{ key: 'pertanyaan', label: 'Pertanyaan', size: 340 },
			{ key: 'kategori', label: 'Kategori', size: 160 },
			{ key: 'urutan', label: 'Urutan', type: 'number', size: 90 },
			{ key: 'is_active', label: 'Status', type: 'boolean', size: 110 }
		],
		fields: [
			{ name: 'pertanyaan', label: 'Pertanyaan', type: 'textarea', required: true, span: 2, rows: 2 },
			{ name: 'pertanyaan_en', label: 'Pertanyaan (English)', type: 'textarea', span: 2, rows: 2, help: 'Opsional. Dipakai saat pengunjung memilih bahasa Inggris; bila kosong, teks Indonesia yang tampil.' },
			{ name: 'jawaban', label: 'Jawaban', type: 'richtext', required: true, span: 2 },
			{ name: 'jawaban_en', label: 'Jawaban (English)', type: 'richtext', span: 2, help: 'Opsional. Dipakai saat pengunjung memilih bahasa Inggris; bila kosong, teks Indonesia yang tampil.' },
			{ name: 'kategori', label: 'Kategori', type: 'text', maxLength: 100 },
			{ name: 'kategori_en', label: 'Kategori (English)', type: 'text', maxLength: 100, help: 'Opsional. Dipakai saat pengunjung memilih bahasa Inggris; bila kosong, teks Indonesia yang tampil.' },
			{ name: 'urutan', label: 'Urutan', type: 'number', min: 0, defaultValue: 0 },
			{ name: 'is_active', label: 'Aktif', type: 'boolean', defaultValue: true }
		]
	},
	{
		slug: 'banner-slider',
		modul: 'banner-slider',
		title: 'Banner',
		singular: 'Banner',
		description: 'Gambar sorotan pada beranda situs. Boleh lebih dari satu — tampil bergantian sebagai slider.',
		icon: 'lucide:panels-top-left',
		defaultSort: 'urutan',
		columns: [
			{ key: 'judul', label: 'Judul' },
			{ key: 'ringkasan', label: 'Ringkasan', size: 280 },
			{ key: 'gambar', label: 'Gambar', type: 'file', size: 110, noSort: true },
			{ key: 'urutan', label: 'Urutan', type: 'number', size: 90 },
			{ key: 'tanggal_mulai', label: 'Mulai', type: 'date', size: 130 },
			{ key: 'tanggal_selesai', label: 'Selesai', type: 'date', size: 130 },
			{ key: 'is_active', label: 'Status', type: 'boolean', size: 110 }
		],
		fields: [
			{
				name: 'judul',
				label: 'Judul',
				type: 'text',
				span: 2,
				maxLength: 255,
				help: 'Tampil sebagai judul besar di atas gambar. Kosongkan bila banner ini hanya gambar — teks bawaan beranda yang dipakai.'
			},
			// Field versi Inggris sengaja tidak ada di modul ini: teks banner
			// dicocokkan ke kamus situs (`lang/en.json`) saat pengunjung memilih
			// bahasa Inggris. Kolom `judul_en`/`ringkasan_en` tetap ada di basis
			// data bila suatu saat perlu diisi manual lagi.
			{
				name: 'ringkasan',
				label: 'Ringkasan',
				type: 'textarea',
				span: 2,
				rows: 2,
				maxLength: 500,
				help: 'Satu sampai dua kalimat di bawah judul. Kosongkan bila tidak perlu.'
			},
			{
				name: 'gambar',
				label: 'Gambar banner',
				type: 'image',
				required: true,
				span: 2,
				help: 'Ukuran ideal 1920 × 1080 px (rasio 16:9), minimal 1600 × 900 px. JPG/WEBP, usahakan di bawah 500 KB. Banner mengisi satu layar penuh di beranda, jadi bagian tepi gambar ikut terpotong mengikuti bentuk layar pengunjung — taruh objek penting di tengah, dan hindari teks di 15% tepi kiri/kanan. Sisi kiri juga tertutup judul dan tombol hero.',
				upload: { folder: 'banner', jenis: 'gambar' }
			},
			{ name: 'link', label: 'Tautan tujuan', type: 'text', maxLength: 500 },
			{ name: 'urutan', label: 'Urutan', type: 'number', min: 0, defaultValue: 0 },
			{ name: 'tanggal_mulai', label: 'Tayang mulai', type: 'date' },
			{ name: 'tanggal_selesai', label: 'Tayang sampai', type: 'date' },
			{ name: 'is_active', label: 'Aktif', type: 'boolean', defaultValue: true }
		]
	},
	{
		slug: 'struktur-organisasi',
		modul: 'struktur-organisasi',
		title: 'Struktur',
		singular: 'Anggota',
		description: 'Susunan pejabat pengelola informasi dan dokumentasi.',
		icon: 'lucide:users',
		defaultSort: 'urutan',
		columns: [
			{ key: 'nama', label: 'Nama' },
			{ key: 'jabatan', label: 'Jabatan' },
			{ key: 'parent', label: 'Induk', type: 'relation', relationKey: 'jabatan', noSort: true },
			{ key: 'tipe_node', label: 'Tipe kotak', size: 120 },
			{ key: 'urutan', label: 'Urutan', type: 'number', size: 90 },
			{ key: 'is_active', label: 'Status', type: 'boolean', size: 110 }
		],
		fields: [
			{ name: 'nama', label: 'Nama', type: 'text', required: true, maxLength: 150 },
			{ name: 'jabatan', label: 'Jabatan', type: 'text', required: true, maxLength: 150 },
			{ name: 'jabatan_en', label: 'Jabatan (English)', type: 'text', maxLength: 150, help: 'Opsional. Dipakai saat pengunjung memilih bahasa Inggris; bila kosong, teks Indonesia yang tampil.' },
			{
				name: 'foto',
				label: 'Foto',
				type: 'image',
				span: 2,
				upload: { folder: 'struktur-organisasi', jenis: 'gambar' }
			},
			{
				name: 'parent_id',
				label: 'Kotak induk',
				type: 'relation',
				relation: { resource: 'struktur-organisasi', labelKey: 'jabatan' },
				help: 'Kosongkan untuk kotak paling atas pada bagan. Isi untuk menempatkan kotak ini di bawah kotak lain.'
			},
			{
				name: 'tipe_node',
				label: 'Tipe kotak pada bagan',
				type: 'select',
				defaultValue: 'utama',
				options: [
					{ value: 'utama', label: 'Utama — kotak pada alur, terhubung panah ke induknya' },
					{ value: 'samping', label: 'Samping — di sisi induk, garis putus-putus' },
					{ value: 'grup', label: 'Grup — bingkai berjudul yang membungkus anak-anaknya' }
				],
				help: 'Menentukan cara kotak digambar pada Bagan Struktur Organisasi di situs publik.'
			},
			{
				name: 'poin',
				label: 'Butir isi kotak',
				type: 'textarea',
				span: 2,
				rows: 3,
				help: 'Satu butir per baris. Diisi bila isi kotak berupa daftar (mis. Tim Pertimbangan PPID); kalau kosong, yang tampil adalah kolom Nama.'
			},
			{ name: 'poin_en', label: 'Butir isi kotak (English)', type: 'textarea', span: 2, rows: 3, help: 'Opsional. Dipakai saat pengunjung memilih bahasa Inggris; bila kosong, teks Indonesia yang tampil.' },
			{ name: 'urutan', label: 'Urutan', type: 'number', min: 0, defaultValue: 0 },
			{ name: 'is_active', label: 'Aktif', type: 'boolean', defaultValue: true },
			{ name: 'deskripsi', label: 'Deskripsi tugas', type: 'textarea', span: 2, rows: 3 },
			{ name: 'deskripsi_en', label: 'Deskripsi tugas (English)', type: 'textarea', span: 2, rows: 3, help: 'Opsional. Dipakai saat pengunjung memilih bahasa Inggris; bila kosong, teks Indonesia yang tampil.' },
		]
	},
	{
		slug: 'halaman-statis',
		modul: 'halaman-statis',
		title: 'Halaman',
		singular: 'Halaman',
		description: 'Halaman profil, visi misi, maklumat, dan sejenisnya.',
		icon: 'lucide:layout-template',
		defaultSort: 'judul',
		columns: [
			{ key: 'judul', label: 'Judul' },
			{ key: 'slug', label: 'Slug' },
			// Kolom "Diubah oleh"/"Diubah" tidak ditulis di sini lagi: keduanya
			// sudah datang dari jejak dokumen (`lib/jejak.ts`) seperti modul lain,
			// termasuk aturan sembunyikan-dulu-nya.
			{ key: 'is_active', label: 'Status', type: 'boolean', size: 110 }
		],
		fields: [
			{ name: 'judul', label: 'Judul halaman', type: 'text', required: true, maxLength: 255 },
			{ name: 'judul_en', label: 'Judul halaman (English)', type: 'text', maxLength: 255, help: 'Opsional. Dipakai saat pengunjung memilih bahasa Inggris; bila kosong, teks Indonesia yang tampil.' },
			{ name: 'slug', label: 'Slug', type: 'text', help: 'Menentukan URL di situs publik. Kosongkan agar dibuat otomatis.' },
			{ name: 'konten', label: 'Isi halaman', type: 'richtext', span: 2 },
			{ name: 'konten_en', label: 'Isi halaman (English)', type: 'richtext', span: 2, help: 'Opsional. Dipakai saat pengunjung memilih bahasa Inggris; bila kosong, teks Indonesia yang tampil.' },
			{ name: 'is_active', label: 'Aktif', type: 'boolean', defaultValue: true }
		]
	},
	{
		slug: 'maklumat',
		// Maklumat adalah salah satu halaman Standar Layanan, jadi hak aksesnya
		// menumpang modul Halaman Statis — tidak ada modul baru di matrix role.
		modul: 'halaman-statis',
		title: 'Maklumat',
		singular: 'Maklumat',
		description:
			'Berkas Maklumat Pelayanan Informasi Publik yang dibaca langsung di situs publik. Situs memakai satu maklumat berstatus Terbit dengan tanggal terbit terbaru.',
		icon: 'lucide:scroll-text',
		defaultSort: '-tanggal_terbit',
		searchPlaceholder: 'Cari judul maklumat…',
		columns: [
			{ key: 'judul', label: 'Judul', size: 320 },
			{ key: 'tanggal_terbit', label: 'Tanggal terbit', type: 'date', size: 150 },
			{ key: 'status', label: 'Status', type: 'badge', badgeMap: BADGE_KONTEN, size: 120 },
			{ key: 'publisher', label: 'Diunggah oleh', type: 'relation', relationKey: 'name', size: 180, noSort: true },
			{ key: 'file_dokumen', label: 'Dokumen', type: 'file', size: 120, noSort: true }
		],
		fields: [
			{ name: 'judul', label: 'Judul maklumat', type: 'text', required: true, span: 2, maxLength: 255 },
			{
				name: 'judul_en',
				label: 'Judul maklumat (English)',
				type: 'text',
				span: 2,
				maxLength: 255,
				help: 'Opsional. Dipakai saat pengunjung memilih bahasa Inggris; bila kosong, teks Indonesia yang tampil.'
			},
			{
				name: 'file_dokumen',
				label: 'Dokumen maklumat',
				type: 'file',
				required: true,
				span: 2,
				help: 'Wajib. PDF atau gambar (JPG/PNG/WEBP) hasil pindai maklumat yang sudah ditandatangani. Isinya ditampilkan utuh di halaman Standar Layanan, bukan diketik ulang di sini.',
				upload: { folder: 'maklumat', jenis: 'dokumen_gambar' }
			},
			{ name: 'tanggal_terbit', label: 'Tanggal terbit', type: 'date', help: 'Kosongkan agar diisi tanggal saat maklumat diterbitkan.' },
			{
				name: 'status',
				label: 'Status',
				type: 'select',
				options: STATUS_KONTEN,
				defaultValue: 'draft',
				help: 'Situs publik hanya menayangkan maklumat berstatus Terbit; maklumat lama cukup diubah jadi Arsip agar tetap tersimpan.'
			}
			// Isian Pengantar (`ringkasan`/`ringkasan_en`) dilepas pada langkah 88:
			// halaman Maklumat kini hanya menayangkan dokumennya, jadi kalimat
			// pengantar itu tidak pernah muncul di mana pun. Kolomnya tetap ada di
			// basis data — isian lama tersimpan dan tetap bisa dicari lewat kotak
			// pencarian modul — hanya formulirnya yang tidak lagi memintanya.
		],
		filters: [{ name: 'status', label: 'Status', type: 'select', options: STATUS_KONTEN }]
	},
	{
		slug: 'alur-prosedur',
		// Alasannya sama dengan Maklumat: alur bergambar adalah isi halaman
		// Standar Layanan, bukan modul layanan sendiri, jadi hak aksesnya
		// menumpang Halaman Statis dan matrix role tidak bertambah.
		modul: 'halaman-statis',
		title: 'Alur Prosedur',
		singular: 'Gambar alur',
		description:
			'Infografis alur yang tayang di halaman Prosedur Permohonan Informasi. Gambar tampil berurutan sesuai kolom Urutan — situs publik menayangkan gambarnya utuh, isinya tidak diketik ulang di sini.',
		icon: 'lucide:route',
		defaultSort: 'urutan',
		searchPlaceholder: 'Cari judul gambar alur…',
		columns: [
			{ key: 'urutan', label: 'Urutan', type: 'number', size: 90 },
			{ key: 'judul', label: 'Judul', size: 340 },
			{ key: 'halaman', label: 'Halaman', type: 'badge', badgeMap: BADGE_HALAMAN_ALUR, size: 200 },
			{ key: 'gambar', label: 'Gambar', type: 'file', size: 110, noSort: true },
			{ key: 'is_active', label: 'Status', type: 'boolean', size: 110 }
		],
		fields: [
			{
				name: 'halaman',
				label: 'Tayang di halaman',
				type: 'select',
				options: HALAMAN_ALUR,
				defaultValue: 'prosedur-permohonan',
				help: 'Menentukan halaman Standar Layanan yang menayangkan gambar ini.'
			},
			{
				name: 'urutan',
				label: 'Urutan',
				type: 'number',
				min: 0,
				defaultValue: 0,
				help: 'Angka kecil tampil lebih dulu. Pakai kelipatan seperti 1, 2, 3 agar mudah disisipi nanti.'
			},
			{
				name: 'judul',
				label: 'Judul gambar',
				type: 'text',
				required: true,
				span: 2,
				maxLength: 255,
				help: 'Tampil sebagai judul di atas gambar dan dipakai sebagai teks alternatif bagi pembaca layar.'
			},
			{
				name: 'judul_en',
				label: 'Judul gambar (English)',
				type: 'text',
				span: 2,
				maxLength: 255,
				help: 'Opsional. Dipakai saat pengunjung memilih bahasa Inggris; bila kosong, teks Indonesia yang tampil.'
			},
			{
				name: 'gambar',
				label: 'Gambar alur',
				type: 'image',
				required: true,
				span: 2,
				help: 'Wajib. JPG/PNG/WEBP, lebar minimal 1200 px. Gambar tampil selebar isi halaman tanpa dipotong, jadi rasio bebas — tapi pastikan tulisan di dalamnya masih terbaca saat dibuka di layar ponsel.',
				upload: { folder: 'alur-prosedur', jenis: 'gambar' }
			},
			{
				name: 'keterangan',
				label: 'Keterangan',
				type: 'textarea',
				span: 2,
				rows: 3,
				maxLength: 2000,
				help: 'Satu sampai dua kalimat di bawah gambar. Isinya merangkum, bukan menyalin seluruh teks di gambar. Boleh dikosongkan.'
			},
			{
				name: 'keterangan_en',
				label: 'Keterangan (English)',
				type: 'textarea',
				span: 2,
				rows: 3,
				maxLength: 2000,
				help: 'Opsional. Dipakai saat pengunjung memilih bahasa Inggris; bila kosong, teks Indonesia yang tampil.'
			},
			{
				name: 'is_active',
				label: 'Aktif',
				type: 'boolean',
				defaultValue: true,
				help: 'Gambar nonaktif tetap tersimpan tetapi tidak tayang di situs publik.'
			}
		],
		filters: [{ name: 'halaman', label: 'Halaman', type: 'select', options: HALAMAN_ALUR }]
	},
	{
		slug: 'arsip-dokumen',
		modul: 'arsip-dokumen',
		title: 'Arsip Dokumen',
		singular: 'Dokumen Arsip',
		description:
			'Berkas yang dipakai berulang untuk menjawab permohonan — SK, laporan, daftar informasi. Diunggah sekali di sini, lalu dilampirkan ke permohonan mana pun lewat tombol "Pilih dari Arsip" pada dialog rinciannya, tanpa unggahan kedua. Berkas yang diunggah langsung dari dialog permohonan juga otomatis tercatat di sini.',
		icon: 'lucide:folder-open',
		defaultSort: '-id',
		searchPlaceholder: 'Cari nama dokumen, kategori, atau keterangan…',
		columns: [
			{ key: 'nama', label: 'Nama dokumen', size: 320 },
			{ key: 'kategori', label: 'Kategori', size: 160 },
			{ key: 'pembuat', label: 'Diunggah oleh', type: 'relation', relationKey: 'name', size: 180, noSort: true },
			{ key: 'created_at', label: 'Diunggah', type: 'datetime', size: 160 },
			{ key: 'is_active', label: 'Aktif', type: 'boolean', size: 90 },
			{ key: 'path_file', label: 'Berkas', type: 'file', size: 110, noSort: true }
		],
		fields: [
			{ name: 'nama', label: 'Nama dokumen', type: 'text', required: true, span: 2, maxLength: 255 },
			{
				name: 'kategori',
				label: 'Kategori',
				type: 'text',
				maxLength: 100,
				help: 'Bebas diisi, mis. "SK", "Laporan Tahunan". Dipakai menyaring daftar saat memilih berkas.'
			},
			{
				name: 'is_active',
				label: 'Aktif',
				type: 'boolean',
				defaultValue: true,
				help: 'Dokumen nonaktif tetap tersimpan tetapi tidak ditawarkan saat melampirkan berkas ke permohonan.'
			},
			{
				name: 'keterangan',
				label: 'Keterangan',
				type: 'textarea',
				span: 2,
				rows: 3,
				maxLength: 2000,
				help: 'Catatan untuk sesama petugas, mis. periode berlakunya. Tidak dibaca pemohon.'
			},
			{
				name: 'path_file',
				label: 'Berkas',
				type: 'file',
				required: true,
				span: 2,
				help: 'PDF, gambar, atau dokumen Office. Satu berkas hanya boleh punya satu baris arsip.',
				upload: { folder: 'permohonan', jenis: 'dokumen' }
			}
		],
		filters: [
			{
				name: 'is_active',
				label: 'Status',
				type: 'select',
				options: [
					{ value: 'true', label: 'Aktif' },
					{ value: 'false', label: 'Nonaktif' }
				]
			}
		]
	},
	{
		slug: 'regulasi',
		modul: 'regulasi',
		title: 'Regulasi',
		singular: 'Regulasi',
		description: 'Peraturan, dasar hukum PPID, dan pedoman layanan.',
		icon: 'lucide:scale',
		// Kolom Nomor dan Tahun dilepas dari modul ini, jadi urutan bawaannya
		// memakai judul.
		defaultSort: 'judul',
		columns: [
			{ key: 'judul', label: 'Judul', size: 380 },
			{ key: 'kategori', label: 'Kategori', size: 170 },
			{ key: 'jenis_peraturan', label: 'Jenis', size: 180 },
			{ key: 'pengunggah', label: 'Diunggah oleh', type: 'relation', relationKey: 'name', size: 180, noSort: true },
			{ key: 'created_at', label: 'Tanggal publikasi', type: 'datetime', size: 170 },
			{ key: 'file_path', label: 'Berkas', type: 'file', size: 110, noSort: true }
		],
		fields: [
			{ name: 'judul', label: 'Judul peraturan', type: 'text', required: true, span: 2, maxLength: 255 },
			{ name: 'judul_en', label: 'Judul peraturan (English)', type: 'text', span: 2, maxLength: 255, help: 'Opsional. Dipakai saat pengunjung memilih bahasa Inggris; bila kosong, teks Indonesia yang tampil.' },
			{
				name: 'ringkasan',
				label: 'Ringkasan',
				type: 'textarea',
				span: 2,
				rows: 3,
				maxLength: 2000,
				help: 'Dipakai sebagai kutipan singkat pada daftar regulasi di situs publik.'
			},
			{ name: 'ringkasan_en', label: 'Ringkasan (English)', type: 'textarea', span: 2, rows: 3, maxLength: 2000, help: 'Opsional. Dipakai saat pengunjung memilih bahasa Inggris; bila kosong, teks Indonesia yang tampil.' },
			{
				name: 'kategori',
				label: 'Kategori',
				type: 'select',
				options: [
					{ value: 'dasar_hukum_ppid', label: 'Dasar Hukum PPID' },
					{ value: 'regulasi', label: 'Regulasi' },
					{ value: 'pedoman', label: 'Pedoman' }
				],
				defaultValue: 'regulasi'
			},
			{ name: 'jenis_peraturan', label: 'Jenis peraturan', type: 'text', maxLength: 100 },
			{ name: 'tanggal_berlaku', label: 'Berlaku sejak', type: 'date' },
			{
				name: 'file_path',
				label: 'Berkas peraturan',
				type: 'file',
				span: 2,
				help: 'Hanya PDF atau gambar (JPG/PNG/WEBP). Halaman pertamanya dipakai sebagai sampul di situs publik.',
				upload: { folder: 'regulasi', jenis: 'dokumen_gambar' }
			}
		],
		filters: [
			{
				name: 'kategori',
				label: 'Kategori',
				type: 'select',
				options: [
					{ value: 'dasar_hukum_ppid', label: 'Dasar Hukum PPID' },
					{ value: 'regulasi', label: 'Regulasi' },
					{ value: 'pedoman', label: 'Pedoman' }
				]
			}
		]
	},
	// Modul Tautan dilepas dari panel: isinya (tautan mitra di footer) tidak
	// dikelola lagi dari sini. Tabel `tautan_terkait` dan endpoint-nya masih
	// ada, jadi modulnya bisa dikembalikan hanya dengan menambah konfigurasi
	// ini lagi.
	{
		slug: 'menu-navigasi',
		modul: 'menu-navigasi',
		title: 'Navigasi',
		singular: 'Menu',
		description: 'Struktur menu situs publik.',
		icon: 'lucide:menu',
		defaultSort: 'urutan',
		columns: [
			{ key: 'label', label: 'Label' },
			{ key: 'url', label: 'URL', size: 260 },
			{ key: 'parent', label: 'Induk', type: 'relation', relationKey: 'label', noSort: true },
			{ key: 'urutan', label: 'Urutan', type: 'number', size: 90 },
			{ key: 'is_active', label: 'Status', type: 'boolean', size: 110 }
		],
		fields: [
			{ name: 'label', label: 'Label menu', type: 'text', required: true, maxLength: 100 },
			{ name: 'url', label: 'URL', type: 'text', maxLength: 255, help: 'Path internal (/informasi/xxx) atau URL lengkap.' },
			{
				name: 'parent_id',
				label: 'Menu induk',
				type: 'relation',
				relation: { resource: 'menu-navigasi', labelKey: 'label' }
			},
			{ name: 'urutan', label: 'Urutan', type: 'number', min: 0, defaultValue: 0 },
			{
				name: 'target',
				label: 'Target',
				type: 'select',
				options: [
					{ value: '_self', label: 'Tab yang sama' },
					{ value: '_blank', label: 'Tab baru' }
				],
				defaultValue: '_self'
			},
			{ name: 'is_active', label: 'Aktif', type: 'boolean', defaultValue: true }
		]
	},

	// ------------------------------------------------------------------
	// Administrasi sistem
	// ------------------------------------------------------------------
	{
		slug: 'pengguna',
		modul: 'pengguna',
		title: 'Pengguna',
		singular: 'Pengguna',
		description: 'Akun admin panel beserta rolenya.',
		icon: 'lucide:user-cog',
		columns: [
			{ key: 'name', label: 'Nama' },
			{ key: 'email', label: 'Email' },
			{ key: 'role', label: 'Role', type: 'relation', relationKey: 'name', noSort: true },
			{ key: 'last_login_at', label: 'Login terakhir', type: 'datetime', size: 170 },
			{ key: 'is_active', label: 'Status', type: 'boolean', size: 110 }
		],
		fields: [
			{ name: 'name', label: 'Nama lengkap', type: 'text', required: true, maxLength: 150 },
			{ name: 'email', label: 'Email', type: 'text', required: true, maxLength: 150 },
			{
				name: 'role_id',
				label: 'Role',
				type: 'relation',
				required: true,
				relation: { resource: 'role', labelKey: 'name' }
			},
			{ name: 'phone', label: 'Nomor telepon', type: 'text', maxLength: 30 },
			{
				name: 'password',
				label: 'Kata sandi',
				type: 'text',
				span: 2,
				help: 'Minimal 12 karakter, kombinasi huruf besar-kecil, angka, dan simbol. Kosongkan saat mengubah bila tidak ingin menggantinya.'
			},
			{ name: 'is_active', label: 'Akun aktif', type: 'boolean', defaultValue: true }
		],
		filters: [
			{ name: 'role_id', label: 'Role', type: 'relation', relation: { resource: 'role', labelKey: 'name' } },
			{
				name: 'is_active',
				label: 'Status',
				type: 'select',
				options: [
					{ value: 'true', label: 'Aktif' },
					{ value: 'false', label: 'Nonaktif' }
				]
			}
		]
	},
	{
		slug: 'role',
		modul: 'pengguna',
		title: 'Role',
		singular: 'Role',
		description: 'Peran pengguna dan matrix hak akses per modul.',
		icon: 'lucide:shield-check',
		defaultSort: 'name',
		columns: [
			{ key: 'name', label: 'Nama role' },
			{ key: 'slug', label: 'Slug' },
			{ key: 'description', label: 'Deskripsi', size: 320 }
		],
		fields: [
			{ name: 'name', label: 'Nama role', type: 'text', required: true, maxLength: 100 },
			{ name: 'slug', label: 'Slug', type: 'text', help: 'Huruf kecil dan tanda hubung. Kosongkan agar dibuat otomatis.' },
			{ name: 'description', label: 'Deskripsi', type: 'textarea', span: 2, rows: 2 }
		]
	},
	// Modul "Modul Sistem" dilepas dari panel. Daftar modul tidak perlu
	// disunting tangan: isinya sudah menjadi baris-baris matrix pada dialog
	// "Atur hak akses" di modul Role, dan diambil langsung dari tabel
	// `modul_sistem` lewat API. Endpoint CRUD-nya masih ada bila suatu saat
	// modul ini ingin dikembalikan.
	{
		slug: 'pengaturan-situs',
		modul: 'pengaturan-situs',
		title: 'Pengaturan',
		singular: 'Pengaturan',
		description: 'Pasangan kunci–nilai yang dibaca situs publik.',
		icon: 'lucide:settings',
		defaultSort: 'group_name',
		columns: [
			{ key: 'key', label: 'Kunci', size: 240 },
			{ key: 'value', label: 'Nilai', size: 380 },
			{ key: 'group_name', label: 'Grup', size: 160 }
		],
		fields: [
			{ name: 'key', label: 'Kunci', type: 'text', required: true, maxLength: 100, help: 'Huruf kecil, angka, titik, dan garis bawah.' },
			{ name: 'group_name', label: 'Grup', type: 'text', maxLength: 50 },
			{ name: 'value', label: 'Nilai', type: 'textarea', span: 2, rows: 3 }
		]
	},
	{
		slug: 'alur-approval',
		modul: 'alur-approval',
		title: 'Alur Persetujuan',
		singular: 'Alur Persetujuan',
		description:
			'Susunan persetujuan berjenjang untuk Permohonan dan Keberatan Informasi. Tiap jenjang menunjuk satu role penyetuju dan kotak pada Struktur Organisasi yang diwakilinya, jadi perubahan struktur cukup diikuti dari sini tanpa mengubah aplikasi. Satu jenis pengajuan hanya boleh punya satu alur aktif; mengaktifkan yang baru menonaktifkan yang lama. Jenjangnya diatur lewat aksi baris "Atur tahap".',
		icon: 'lucide:git-merge',
		defaultSort: 'jenis',
		columns: [
			{
				key: 'jenis',
				label: 'Jenis pengajuan',
				type: 'badge',
				size: 190,
				badgeMap: {
					permohonan: { label: 'Permohonan Informasi', color: 'info' },
					keberatan: { label: 'Keberatan Informasi', color: 'warning' }
				}
			},
			{ key: 'nama', label: 'Nama alur' },
			{ key: 'tahap_jumlah', label: 'Jumlah tahap', type: 'number', size: 130, noSort: true },
			{ key: 'is_active', label: 'Aktif', type: 'boolean', size: 110 }
		],
		fields: [
			{
				name: 'jenis',
				label: 'Jenis pengajuan',
				type: 'select',
				required: true,
				options: [
					{ value: 'permohonan', label: 'Permohonan Informasi' },
					{ value: 'keberatan', label: 'Keberatan Informasi' }
				],
				help: 'Alur ini dipakai saat pengajuan jenis tersebut masuk status Menunggu Persetujuan.'
			},
			{ name: 'nama', label: 'Nama alur', type: 'text', required: true, maxLength: 150 },
			{ name: 'keterangan', label: 'Keterangan', type: 'textarea', span: 2, rows: 2 },
			{
				name: 'is_active',
				label: 'Aktifkan alur ini',
				type: 'boolean',
				span: 2,
				defaultValue: true,
				help: 'Mengaktifkan alur ini otomatis menonaktifkan alur lain pada jenis yang sama.'
			}
		],
		filters: [
			{
				name: 'jenis',
				label: 'Jenis',
				type: 'select',
				options: [
					{ value: 'permohonan', label: 'Permohonan Informasi' },
					{ value: 'keberatan', label: 'Keberatan Informasi' }
				]
			}
		]
	},
	{
		slug: 'audit-log',
		modul: 'audit-log',
		title: 'Audit',
		singular: 'Log',
		description: 'Jejak seluruh perubahan data dan aktivitas login.',
		icon: 'lucide:clipboard-list',
		defaultSort: '-created_at',
		readOnly: true,
		// Tabel `audit_log` adalah catatannya sendiri: barisnya tidak pernah
		// diubah atau dihapus, jadi tidak punya kolom jejak seperti modul lain.
		tanpaJejak: true,
		columns: [
			{ key: 'created_at', label: 'Waktu', type: 'datetime', size: 170 },
			{ key: 'user', label: 'Pengguna', type: 'relation', relationKey: 'name', noSort: true },
			{ key: 'action', label: 'Aksi', size: 140 },
			{ key: 'model_type', label: 'Objek', size: 260 },
			{ key: 'model_id', label: 'ID', type: 'number', size: 80 },
			{ key: 'ip_address', label: 'IP', size: 140 }
		],
		fields: [],
		filters: [
			{
				name: 'action',
				label: 'Aksi',
				type: 'select',
				options: [
					{ value: 'login', label: 'Login' },
					{ value: 'login_failed', label: 'Login gagal' },
					{ value: 'logout', label: 'Logout' },
					{ value: 'create', label: 'Tambah' },
					{ value: 'update', label: 'Ubah' },
					{ value: 'delete', label: 'Hapus' },
					{ value: 'update_status', label: 'Ubah status' },
					{ value: 'approval', label: 'Persetujuan' },
					{ value: 'upload', label: 'Unggah berkas' }
				]
			}
		]
	}
];

export const resourceBySlug = new Map(resources.map((config) => [config.slug, config]));

export default resources;
