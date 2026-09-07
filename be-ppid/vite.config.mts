import { defineConfig, loadEnv } from 'vite';
import react from '@vitejs/plugin-react';
import svgrPlugin from 'vite-plugin-svgr';
import tsconfigPaths from 'vite-tsconfig-paths';
import tailwindcss from "@tailwindcss/vite";

/*
 * Variabel yang harus ada sebelum berkas build boleh dihasilkan.
 *
 * Vite menyisipkan VITE_* saat build dan diam saja bila nilainya kosong —
 * hasilnya berkas yang tampak sehat tetapi rusak di tempat yang jauh dari
 * penyebabnya. Dua yang sudah pernah terjadi:
 *
 * - VITE_API_BASE_URL kosong membuat `API_BASE_URL` di src/utils/api.ts jatuh
 *   ke '/', prefix ky menjadi '//api', dan peramban membaca itu sebagai host
 *   bernama `api` — ERR_NAME_NOT_RESOLVED pada setiap panggilan API.
 * - VITE_RECAPTCHA_SITE_KEY kosong mematikan reCAPTCHA di peramban sementara
 *   server tetap mewajibkannya: semua login ditolak tanpa penjelasan.
 *
 * Keduanya baru ketahuan setelah artefaknya dipasang di server. Lebih baik
 * build gagal di sini sambil menyebut variabel mana yang kosong.
 */
const WAJIB_SAAT_BUILD = ['VITE_API_BASE_URL', 'VITE_RECAPTCHA_SITE_KEY'];

// https://vitejs.dev/config/
export default defineConfig(({ command, mode }) => {
	if (command === 'build') {
		const env = loadEnv(mode, process.cwd(), '');
		const kosong = WAJIB_SAAT_BUILD.filter((nama) => !env[nama]);

		if (kosong.length > 0) {
			throw new Error(
				[
					`Build dibatalkan. Variabel berikut kosong: ${kosong.join(', ')}.`,
					'Salin .env.example ke .env dan isi, atau setel sebagai variabel CI/CD.',
					'Lihat catatan di vite.config.mts untuk akibatnya bila diabaikan.'
				].join('\n')
			);
		}
	}

	return {
		plugins: [
			react({
				jsxImportSource: '@emotion/react'
			}),
			tsconfigPaths({
				parseNative: false
			}),
			svgrPlugin(),
			{
				name: 'custom-hmr-control',
				handleHotUpdate({ file, server }) {
					if (file.includes('src/app/configs/')) {
						server.ws.send({
							type: 'full-reload'
						});
						return [];
					}
				}
			},
			tailwindcss(),
		],
		build: {
			outDir: 'build',
			rollupOptions: {
				output: {
					manualChunks: {
						react: ['react', 'react-dom', 'react-router'],
						mui: ['@mui/material', '@mui/system', '@mui/icons-material', '@emotion/react', '@emotion/styled'],
						tabel: ['material-react-table'],
						grafik: ['apexcharts', 'react-apexcharts'],
						editor: ['@tiptap/react', '@tiptap/starter-kit']
					}
				}
			}
		},
		server: {
			host: '0.0.0.0',
			open: true,
			strictPort: false,
			port: 3000,
			// Endpoint asli (api-ppid / Laravel) di-proxy lewat origin dev server,
			// jadi tidak ada CORS saat development. Path /api/mock/* tetap ditangani MSW.
			proxy: {
				'/api/v1': {
					target: process.env.VITE_API_TARGET || 'http://127.0.0.1:8001',
					changeOrigin: true
				}
			}
		},
		define: {
			'import.meta.env.VITE_PORT': JSON.stringify(process.env.PORT || 3000),
			global: 'window'
		},
		resolve: {
			alias: {
				'@': '/src',
				'@fuse': '/src/@fuse',
				'@history': '/src/@history',
				'@lodash': '/src/@lodash',
				'@mock-api': '/src/@mock-api',
				'@schema': '/src/@schema',
				'app/store': '/src/app/store',
				'app/shared-components': '/src/app/shared-components',
				'app/configs': '/src/app/configs',
				'app/theme-layouts': '/src/app/theme-layouts',
				'app/AppContext': '/src/app/AppContext'
			}
		},
		/*
		 * Daftar ini menentukan apa yang di-prebundle saat dev server menyala.
		 *
		 * Pustaka yang baru ditemukan Vite di tengah sesi — karena hanya dipakai
		 * modul yang dimuat malas — memicu optimasi ulang plus MUAT ULANG HALAMAN
		 * penuh tepat saat operator membuka modul itu. Semua pustaka yang dipakai
		 * halaman CMS karena itu disebut di sini, bukan dibiarkan ditemukan sendiri.
		 */
		optimizeDeps: {
			include: [
				'@mui/icons-material',
				'@mui/material',
				'@mui/base',
				'@mui/system',
				'@mui/utils',
				'@emotion/cache',
				'@emotion/react',
				'@emotion/styled',
				'date-fns',
				'lodash',
				// Dipakai halaman daftar, formulir, dan dashboard modul CMS.
				'material-react-table',
				'apexcharts',
				'react-apexcharts',
				'notistack',
				'react-hook-form',
				'@hookform/resolvers/zod',
				'zod',
				'@tanstack/react-query',
				'react-i18next',
				'i18next',
				'react-router',
				'motion/react',
				'ky',
				'qs'
			],
			exclude: [],
			esbuildOptions: {
				loader: {
					'.js': 'jsx'
				}
			}
		}
	};
});
