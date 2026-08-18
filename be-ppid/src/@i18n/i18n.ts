import i18n from 'i18next';
import { initReactI18next } from 'react-i18next';
import kamusPpid from './kamusPpid';

/**
 * Panel ini berbahasa Indonesia; Inggris disediakan lewat kamus terjemahan.
 *
 * Kuncinya adalah teks Bahasa Indonesia apa adanya, jadi mode `id` tidak
 * memerlukan kamus sama sekali — i18next mengembalikan kuncinya sendiri saat
 * tidak ketemu. Label baru yang belum diterjemahkan karena itu tetap tampil
 * dalam Bahasa Indonesia, bukan hilang atau berubah jadi kode.
 */
const resources = {
	id: { translation: {} },
	en: { translation: kamusPpid }
};

export const BAHASA_TERSIMPAN = 'ppid-bahasa';

/** Bahasa pilihan terakhir; dipakai ulang saat panel dibuka lagi. */
function bahasaAwal(): string {
	if (typeof window === 'undefined') {
		return 'id';
	}

	const tersimpan = window.localStorage.getItem(BAHASA_TERSIMPAN);

	return tersimpan === 'en' || tersimpan === 'id' ? tersimpan : 'id';
}

i18n.use(initReactI18next).init({
	resources,
	lng: bahasaAwal(),
	fallbackLng: 'id',

	// Kunci berupa kalimat penuh, jadi pemisah kunci dan namespace harus mati —
	// tanpa ini teks yang memuat "." atau ":" akan dipotong i18next.
	keySeparator: false,
	nsSeparator: false,

	interpolation: {
		escapeValue: false // react already safes from xss
	}
});

export default i18n;
