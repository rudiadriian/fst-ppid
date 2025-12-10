/** @type {import('tailwindcss').Config} */
export default {
    content: [
      // PENTING: Memindai semua file Blade di folder views
      "./resources/**/*.blade.php",
      "./resources/**/*.js",
      "./resources/**/*.vue",
    ],
    theme: {
      extend: {},
    },
    plugins: [],
  }
