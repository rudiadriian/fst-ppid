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

const BADGE_PERMOHONAN = {
	diajukan: { label: 'Diajukan', color: 'info' as const },
	diverifikasi: { label: 'Diverifikasi', color: 'info' as const },
	diproses: { label: 'Diproses', color: 'warning' as const },
	menunggu_approval: { label: 'Menunggu Persetujuan', color: 'warning' as const },
	disetujui: { label: 'Disetujui', color: 'success' as const },
	ditolak: { label: 'Ditolak', color: 'error' as const },
	ditolak_sebagian: { label: 'Ditolak Sebagian', color: 'error' as const },
	selesai: { label: 'Selesai', color: 'success' as const },
	kedaluwarsa: { label: 'Kedaluwarsa', color: 'default' as const }
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
			{ key: 'judul', label: 'Judul', size: 320 },
			{ key: 'kategori', label: 'Kategori', type: 'relation', relationKey: 'nama', noSort: true },
			{ key: 'status', label: 'Status', type: 'badge', badgeMap: BADGE_KONTEN, size: 140 },
			{ key: 'tanggal_publikasi', label: 'Terbit', type: 'date', size: 130 },
			{ key: 'views_count', label: 'Dilihat', type: 'number', size: 100 }
		],
		fields: [
			{ name: 'judul', label: 'Judul', type: 'text', required: true, span: 2, maxLength: 255 },
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
			{
				name: 'tautan',
				label: 'Tautan halaman',
				type: 'text',
				span: 2,
				maxLength: 500,
				help: 'Isi bila informasi ini ada di halaman lain (mis. https://foodstation.id/sejarah-perusahaan/). Tombol "Selengkapnya" di situs akan menuju alamat ini. Kosongkan bila memakai lampiran dokumen.'
			},
			{ name: 'konten', label: 'Isi informasi', type: 'richtext', span: 2 },
			{
				name: 'files',
				label: 'Lampiran dokumen',
				type: 'files',
				span: 2,
				upload: { folder: 'informasi-publik', jenis: 'dokumen' },
				help: 'PDF/Office, maksimal 20 MB per berkas. Dipakai bila entri ini berupa berkas, bukan tautan.'
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
			{ name: 'dasar_hukum_pengecualian', label: 'Dasar hukum', type: 'text', maxLength: 255 },
			{ name: 'jangka_waktu_pengecualian', label: 'Jangka waktu', type: 'text', maxLength: 100 },
			{ name: 'tanggal_penetapan', label: 'Tanggal penetapan', type: 'date' },
			{ name: 'status', label: 'Status', type: 'select', options: STATUS_KONTEN, defaultValue: 'draft' },
			{ name: 'ringkasan', label: 'Ringkasan', type: 'textarea', span: 2, rows: 2 },
			{ name: 'alasan_pengecualian', label: 'Alasan pengecualian', type: 'textarea', required: true, span: 2, rows: 4 },
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
		title: 'Data Pemohon',
		singular: 'Pemohon',
		description: 'Identitas pemohon informasi. NIK tidak ditampilkan di panel.',
		icon: 'lucide:user',
		columns: [
			{ key: 'nama', label: 'Nama' },
			{ key: 'email', label: 'Email' },
			{ key: 'no_hp', label: 'No. HP', size: 140 },
			{ key: 'jenis_pemohon', label: 'Jenis', size: 120 },
			{ key: 'nama_lembaga', label: 'Lembaga' }
		],
		fields: [
			{ name: 'nama', label: 'Nama lengkap', type: 'text', required: true, maxLength: 150 },
			{ name: 'email', label: 'Email', type: 'text', required: true, maxLength: 150 },
			{ name: 'no_hp', label: 'Nomor HP', type: 'text', maxLength: 30 },
			{ name: 'nik', label: 'NIK', type: 'text', maxLength: 16, help: 'Hanya angka. Tidak ditampilkan kembali setelah disimpan.' },
			{
				name: 'jenis_pemohon',
				label: 'Jenis pemohon',
				type: 'select',
				options: [
					{ value: 'pribadi', label: 'Pribadi' },
					{ value: 'instansi', label: 'Instansi' },
					{ value: 'kelompok', label: 'Kelompok' }
				],
				defaultValue: 'pribadi'
			},
			{ name: 'nama_lembaga', label: 'Nama lembaga', type: 'text', maxLength: 200 },
			{ name: 'pekerjaan', label: 'Pekerjaan', type: 'text', maxLength: 100 },
			{ name: 'alamat', label: 'Alamat', type: 'textarea', span: 2, rows: 2 }
		],
		filters: [
			{
				name: 'jenis_pemohon',
				label: 'Jenis',
				type: 'select',
				options: [
					{ value: 'pribadi', label: 'Pribadi' },
					{ value: 'instansi', label: 'Instansi' },
					{ value: 'kelompok', label: 'Kelompok' }
				]
			}
		]
	},
	{
		slug: 'permohonan',
		modul: 'permohonan',
		title: 'Permohonan Informasi',
		singular: 'Permohonan',
		description: 'Permohonan masuk beserta status penanganannya.',
		icon: 'lucide:inbox',
		defaultSort: '-tanggal_permohonan',
		searchPlaceholder: 'Cari kode permohonan atau rincian…',
		columns: [
			{ key: 'kode_permohonan', label: 'Kode', size: 170 },
			{ key: 'pemohon', label: 'Pemohon', type: 'relation', relationKey: 'nama', noSort: true },
			{ key: 'rincian_informasi', label: 'Rincian', size: 280, noSort: true },
			{ key: 'status', label: 'Status', type: 'badge', badgeMap: BADGE_PERMOHONAN, size: 170 },
			{ key: 'tanggal_permohonan', label: 'Diajukan', type: 'datetime', size: 160 },
			{ key: 'batas_waktu_tanggapan', label: 'Batas waktu', type: 'datetime', size: 160 }
		],
		fields: [
			{
				name: 'pemohon_id',
				label: 'Pemohon',
				type: 'relation',
				required: true,
				relation: { resource: 'pemohon', labelKey: 'nama' }
			},
			{
				name: 'kategori_id',
				label: 'Kategori informasi',
				type: 'relation',
				relation: { resource: 'kategori-informasi', labelKey: 'nama' }
			},
			{ name: 'rincian_informasi', label: 'Rincian informasi diminta', type: 'textarea', required: true, span: 2, rows: 3 },
			{ name: 'tujuan_penggunaan', label: 'Tujuan penggunaan', type: 'textarea', span: 2, rows: 2 },
			{
				name: 'format_informasi',
				label: 'Format informasi',
				type: 'select',
				options: [
					{ value: 'softcopy', label: 'Softcopy' },
					{ value: 'hardcopy', label: 'Hardcopy' }
				]
			},
			{
				name: 'cara_pengiriman',
				label: 'Cara pengiriman',
				type: 'select',
				options: [
					{ value: 'email', label: 'Email' },
					{ value: 'ambil_langsung', label: 'Ambil langsung' },
					{ value: 'pos', label: 'Pos' }
				]
			},
			{ name: 'batas_waktu_tanggapan', label: 'Batas waktu tanggapan', type: 'date' },
			{
				name: 'ditangani_oleh',
				label: 'Petugas penanggung jawab',
				type: 'relation',
				relation: { resource: 'pengguna', labelKey: 'name' }
			},
			{
				name: 'tampil_di_register_publik',
				label: 'Tampilkan di register permohonan publik',
				type: 'boolean',
				span: 2
			}
		],
		filters: [
			{
				name: 'status',
				label: 'Status',
				type: 'select',
				options: Object.entries(BADGE_PERMOHONAN).map(([value, info]) => ({ value, label: info.label }))
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
		slug: 'keberatan',
		modul: 'keberatan',
		title: 'Keberatan Informasi',
		singular: 'Keberatan',
		description: 'Keberatan atas layanan informasi dan tanggapan atasan PPID.',
		icon: 'lucide:triangle-alert',
		defaultSort: '-tanggal_keberatan',
		columns: [
			{ key: 'permohonan', label: 'Kode permohonan', type: 'relation', relationKey: 'kode_permohonan', noSort: true },
			{ key: 'pemohon', label: 'Pemohon', type: 'relation', relationKey: 'nama', noSort: true },
			{ key: 'jenis_keberatan', label: 'Jenis', size: 220 },
			{
				key: 'status',
				label: 'Status',
				type: 'badge',
				size: 120,
				badgeMap: {
					diajukan: { label: 'Diajukan', color: 'info' },
					diproses: { label: 'Diproses', color: 'warning' },
					selesai: { label: 'Selesai', color: 'success' }
				}
			},
			{ key: 'tanggal_keberatan', label: 'Diajukan', type: 'datetime', size: 160 }
		],
		fields: [
			{
				name: 'permohonan_id',
				label: 'Permohonan terkait',
				type: 'relation',
				required: true,
				relation: { resource: 'permohonan', labelKey: 'kode_permohonan' }
			},
			{
				name: 'pemohon_id',
				label: 'Pemohon',
				type: 'relation',
				required: true,
				relation: { resource: 'pemohon', labelKey: 'nama' }
			},
			{
				name: 'jenis_keberatan',
				label: 'Jenis keberatan',
				type: 'select',
				required: true,
				span: 2,
				options: [
					{ value: 'permohonan_ditolak', label: 'Permohonan ditolak' },
					{ value: 'informasi_tidak_disediakan', label: 'Informasi tidak disediakan' },
					{ value: 'permintaan_tidak_ditanggapi', label: 'Permintaan tidak ditanggapi' },
					{ value: 'informasi_tidak_sesuai', label: 'Informasi tidak sesuai' },
					{ value: 'biaya_tidak_wajar', label: 'Biaya tidak wajar' },
					{ value: 'melebihi_jangka_waktu', label: 'Melebihi jangka waktu' }
				]
			},
			{ name: 'alasan_keberatan', label: 'Alasan keberatan', type: 'textarea', required: true, span: 2, rows: 4 },
			{
				name: 'status',
				label: 'Status',
				type: 'select',
				options: [
					{ value: 'diajukan', label: 'Diajukan' },
					{ value: 'diproses', label: 'Diproses' },
					{ value: 'selesai', label: 'Selesai' }
				],
				defaultValue: 'diajukan'
			},
			{ name: 'tanggapan_atasan_ppid', label: 'Tanggapan atasan PPID', type: 'textarea', span: 2, rows: 4 }
		],
		filters: [
			{
				name: 'status',
				label: 'Status',
				type: 'select',
				options: [
					{ value: 'diajukan', label: 'Diajukan' },
					{ value: 'diproses', label: 'Diproses' },
					{ value: 'selesai', label: 'Selesai' }
				]
			}
		]
	},

	// ------------------------------------------------------------------
	// Laporan
	// ------------------------------------------------------------------
	{
		slug: 'laporan-layanan',
		modul: 'laporan-layanan',
		title: 'Laporan Layanan',
		singular: 'Laporan',
		description: 'Laporan statistik informasi dan laporan pelayanan informasi.',
		icon: 'lucide:chart-bar',
		defaultSort: '-tahun',
		aksiIsiOtomatis: {
			label: 'Hitung otomatis',
			endpoint: 'laporan-layanan/rekap',
			params: ['tahun'],
			isi: [
				'jumlah_permohonan_masuk',
				'jumlah_dikabulkan',
				'jumlah_ditolak',
				'jumlah_ditolak_sebagian',
				'jumlah_keberatan',
				'rata_rata_hari_respon'
			],
			help: 'Isi Tahun lalu tekan "Hitung otomatis" untuk mengambil angka rekap langsung dari data permohonan & keberatan. Angka tetap bisa disunting sebelum disimpan.'
		},
		columns: [
			{ key: 'judul', label: 'Judul', size: 280 },
			{ key: 'tipe_laporan', label: 'Tipe', size: 180 },
			{ key: 'tahun', label: 'Tahun', type: 'number', size: 90 },
			{ key: 'periode', label: 'Periode', size: 130 },
			{ key: 'status', label: 'Status', type: 'badge', badgeMap: BADGE_KONTEN, size: 120 },
			{ key: 'file_laporan', label: 'Berkas', type: 'file', size: 110, noSort: true }
		],
		fields: [
			{ name: 'judul', label: 'Judul laporan', type: 'text', required: true, span: 2, maxLength: 255 },
			{
				name: 'tipe_laporan',
				label: 'Tipe laporan',
				type: 'select',
				required: true,
				options: [
					{ value: 'statistik_informasi', label: 'Statistik Informasi' },
					{ value: 'pelayanan_informasi', label: 'Pelayanan Informasi' }
				]
			},
			{ name: 'tahun', label: 'Tahun', type: 'number', required: true, min: 2000, max: 2100 },
			{ name: 'periode', label: 'Periode', type: 'text', maxLength: 30, help: 'Contoh: Triwulan I, Semester II, Tahunan.' },
			{
				name: 'status',
				label: 'Status',
				type: 'select',
				options: STATUS_KONTEN,
				defaultValue: 'draft',
				help: 'Halaman Laporan Statistik Informasi Publik di situs publik hanya menampilkan laporan berstatus Terbit.'
			},
			{ name: 'jumlah_permohonan_masuk', label: 'Permohonan masuk', type: 'number', min: 0, defaultValue: 0 },
			{ name: 'jumlah_dikabulkan', label: 'Dikabulkan', type: 'number', min: 0, defaultValue: 0 },
			{ name: 'jumlah_ditolak', label: 'Ditolak', type: 'number', min: 0, defaultValue: 0 },
			{ name: 'jumlah_ditolak_sebagian', label: 'Ditolak sebagian', type: 'number', min: 0, defaultValue: 0 },
			{ name: 'jumlah_keberatan', label: 'Keberatan', type: 'number', min: 0, defaultValue: 0 },
			{ name: 'rata_rata_hari_respon', label: 'Rata-rata hari respon', type: 'number', min: 0 },
			{ name: 'ringkasan', label: 'Ringkasan', type: 'textarea', span: 2, rows: 3 },
			{
				name: 'file_laporan',
				label: 'Berkas laporan',
				type: 'file',
				span: 2,
				upload: { folder: 'laporan', jenis: 'dokumen' }
			}
		],
		filters: [
			{
				name: 'tipe_laporan',
				label: 'Tipe',
				type: 'select',
				options: [
					{ value: 'statistik_informasi', label: 'Statistik Informasi' },
					{ value: 'pelayanan_informasi', label: 'Pelayanan Informasi' }
				]
			},
			{ name: 'status', label: 'Status', type: 'select', options: STATUS_KONTEN }
		]
	},
	{
		slug: 'survey-kepuasan',
		// Hak aksesnya menumpang modul Permohonan: survei melekat pada
		// permohonan yang sudah dilayani.
		modul: 'permohonan',
		title: 'Survei Kepuasan',
		singular: 'Survei Kepuasan',
		description:
			'Penilaian pemohon atas layanan informasi. Rata-ratanya menjadi angka "Kepuasan" pada halaman Laporan Statistik Informasi Publik di situs publik.',
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
			{ name: 'konten', label: 'Isi berita', type: 'richtext', span: 2 }
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
		icon: 'lucide:circle-question-mark',
		defaultSort: 'urutan',
		columns: [
			{ key: 'pertanyaan', label: 'Pertanyaan', size: 340 },
			{ key: 'kategori', label: 'Kategori', size: 160 },
			{ key: 'urutan', label: 'Urutan', type: 'number', size: 90 },
			{ key: 'is_active', label: 'Status', type: 'boolean', size: 110 }
		],
		fields: [
			{ name: 'pertanyaan', label: 'Pertanyaan', type: 'textarea', required: true, span: 2, rows: 2 },
			{ name: 'jawaban', label: 'Jawaban', type: 'richtext', required: true, span: 2 },
			{ name: 'kategori', label: 'Kategori', type: 'text', maxLength: 100 },
			{ name: 'urutan', label: 'Urutan', type: 'number', min: 0, defaultValue: 0 },
			{ name: 'is_active', label: 'Aktif', type: 'boolean', defaultValue: true }
		]
	},
	{
		slug: 'banner-slider',
		modul: 'banner-slider',
		title: 'Banner Slider',
		singular: 'Banner',
		description: 'Gambar sorotan pada beranda situs.',
		icon: 'lucide:panels-top-left',
		defaultSort: 'urutan',
		columns: [
			{ key: 'judul', label: 'Judul' },
			{ key: 'gambar', label: 'Gambar', type: 'file', size: 110, noSort: true },
			{ key: 'urutan', label: 'Urutan', type: 'number', size: 90 },
			{ key: 'tanggal_mulai', label: 'Mulai', type: 'date', size: 130 },
			{ key: 'tanggal_selesai', label: 'Selesai', type: 'date', size: 130 },
			{ key: 'is_active', label: 'Status', type: 'boolean', size: 110 }
		],
		fields: [
			{ name: 'judul', label: 'Judul', type: 'text', maxLength: 255 },
			{
				name: 'gambar',
				label: 'Gambar banner',
				type: 'image',
				required: true,
				span: 2,
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
		title: 'Struktur Organisasi',
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
			{ name: 'urutan', label: 'Urutan', type: 'number', min: 0, defaultValue: 0 },
			{ name: 'is_active', label: 'Aktif', type: 'boolean', defaultValue: true },
			{ name: 'deskripsi', label: 'Deskripsi tugas', type: 'textarea', span: 2, rows: 3 }
		]
	},
	{
		slug: 'halaman-statis',
		modul: 'halaman-statis',
		title: 'Halaman Statis',
		singular: 'Halaman',
		description: 'Halaman profil, visi misi, maklumat, dan sejenisnya.',
		icon: 'lucide:layout-template',
		defaultSort: 'judul',
		columns: [
			{ key: 'judul', label: 'Judul' },
			{ key: 'slug', label: 'Slug' },
			{ key: 'editor', label: 'Diubah oleh', type: 'relation', relationKey: 'name', noSort: true },
			{ key: 'updated_at', label: 'Diperbarui', type: 'datetime', size: 160 },
			{ key: 'is_active', label: 'Status', type: 'boolean', size: 110 }
		],
		fields: [
			{ name: 'judul', label: 'Judul halaman', type: 'text', required: true, maxLength: 255 },
			{ name: 'slug', label: 'Slug', type: 'text', help: 'Menentukan URL di situs publik. Kosongkan agar dibuat otomatis.' },
			{ name: 'konten', label: 'Isi halaman', type: 'richtext', span: 2 },
			{ name: 'is_active', label: 'Aktif', type: 'boolean', defaultValue: true }
		]
	},
	{
		slug: 'regulasi',
		modul: 'regulasi',
		title: 'Regulasi & Dasar Hukum',
		singular: 'Regulasi',
		description: 'Peraturan, dasar hukum PPID, dan pedoman layanan.',
		icon: 'lucide:scale',
		defaultSort: '-tahun',
		columns: [
			{ key: 'judul', label: 'Judul', size: 320 },
			{ key: 'kategori', label: 'Kategori', size: 170 },
			{ key: 'nomor_peraturan', label: 'Nomor', size: 160 },
			{ key: 'tahun', label: 'Tahun', type: 'number', size: 90 },
			{ key: 'file_path', label: 'Berkas', type: 'file', size: 110, noSort: true }
		],
		fields: [
			{ name: 'judul', label: 'Judul peraturan', type: 'text', required: true, span: 2, maxLength: 255 },
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
			{ name: 'nomor_peraturan', label: 'Nomor peraturan', type: 'text', maxLength: 100 },
			{ name: 'jenis_peraturan', label: 'Jenis peraturan', type: 'text', maxLength: 100 },
			{ name: 'tahun', label: 'Tahun', type: 'number', min: 1945, max: 2100 },
			{ name: 'tanggal_berlaku', label: 'Berlaku sejak', type: 'date' },
			{
				name: 'file_path',
				label: 'Berkas peraturan',
				type: 'file',
				span: 2,
				upload: { folder: 'regulasi', jenis: 'dokumen' }
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
	{
		slug: 'tautan-terkait',
		modul: 'tautan-terkait',
		title: 'Tautan Terkait',
		singular: 'Tautan',
		description: 'Tautan mitra dan instansi terkait di footer situs.',
		icon: 'lucide:link',
		defaultSort: 'urutan',
		columns: [
			{ key: 'nama', label: 'Nama' },
			{ key: 'url', label: 'URL', size: 300 },
			{ key: 'urutan', label: 'Urutan', type: 'number', size: 90 },
			{ key: 'is_active', label: 'Status', type: 'boolean', size: 110 }
		],
		fields: [
			{ name: 'nama', label: 'Nama', type: 'text', required: true, maxLength: 150 },
			{ name: 'url', label: 'URL', type: 'text', required: true, maxLength: 500, help: 'Harus diawali http:// atau https://' },
			{ name: 'logo', label: 'Logo', type: 'image', upload: { folder: 'tautan', jenis: 'gambar' } },
			{ name: 'urutan', label: 'Urutan', type: 'number', min: 0, defaultValue: 0 },
			{ name: 'is_active', label: 'Aktif', type: 'boolean', defaultValue: true }
		]
	},
	{
		slug: 'menu-navigasi',
		modul: 'menu-navigasi',
		title: 'Menu Navigasi',
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
		title: 'Role & Hak Akses',
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
	{
		slug: 'pengaturan-situs',
		modul: 'pengaturan-situs',
		title: 'Pengaturan Situs',
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
		slug: 'audit-log',
		modul: 'audit-log',
		title: 'Audit Log',
		singular: 'Log',
		description: 'Jejak seluruh perubahan data dan aktivitas login.',
		icon: 'lucide:clipboard-list',
		defaultSort: '-created_at',
		readOnly: true,
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
