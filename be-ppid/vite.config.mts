import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import svgrPlugin from 'vite-plugin-svgr';
import tsconfigPaths from 'vite-tsconfig-paths';
import tailwindcss from "@tailwindcss/vite";

// https://vitejs.dev/config/
export default defineConfig({
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
});
