/** @type {import('tailwindcss').Config} */

// Palet diambil dari referensi desain (theme-color.jpeg & theme-color1.jpeg):
// hijau hutan pekat sebagai warna struktural, oranye sebagai aksen/CTA, krem sebagai latar sesi.
const brand = {
  50:  '#EFF6F1',
  100: '#D7E9DE',
  200: '#B0D3BF',
  300: '#7DB395',
  400: '#3E9C6C',
  500: '#26704E',
  600: '#175A3C',
  700: '#10462F',
  800: '#0B3524',
  900: '#08281B',
  950: '#041710',
};

const accent = {
  50:  '#FEF4E9',
  100: '#FCE3C8',
  200: '#F9C594',
  300: '#F5A94C',
  400: '#F08C2A',
  500: '#E87317',
  600: '#C85C10',
  700: '#9E470D',
  800: '#7A360B',
  900: '#5C2908',
  950: '#3A1905',
};

const cream = {
  50:  '#FDFAF3',
  100: '#FAF6EC',
  200: '#F3ECDD',
  300: '#E9DFC9',
  400: '#D9CBAD',
};

export default {
    darkMode: 'class',
    content: [
      // PENTING: Memindai semua file Blade di folder views
      "./resources/**/*.blade.php",
      "./resources/**/*.js",
      "./resources/**/*.vue",
    ],
    theme: {
      extend: {
        colors: {
          brand,
          accent,
          cream,
          // Kelas emerald/amber yang sudah dipakai di Blade ikut tema baru
          // tanpa perlu mengganti nama kelas satu per satu.
          emerald: brand,
          amber: accent,
          green: brand,
        },
      },
    },
    plugins: [],
  }
