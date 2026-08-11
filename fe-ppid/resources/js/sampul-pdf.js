/**
 * Sampul kartu Regulasi: menggambar halaman pertama berkas PDF ke <canvas>.
 *
 * Memakai pdf.js, bukan <object>/<iframe>, karena pembaca PDF bawaan peramban
 * tidak tersedia di semua perangkat (sebagian peramban ponsel menampilkan kotak
 * kosong). Menggambar sendiri membuat hasilnya seragam sekaligus memastikan yang
 * tampil memang halaman pertama.
 *
 * Berkas baru diunduh saat kartunya mendekati layar, jadi halaman berisi banyak
 * dokumen tidak menarik semuanya sekaligus. Pengecekan posisi dilakukan lewat
 * getBoundingClientRect() pada saat muat, gulir, dan ubah ukuran — bukan
 * IntersectionObserver — supaya perilakunya sama di semua peramban.
 */
import * as pdfjs from 'pdfjs-dist/build/pdf.mjs';
import * as pekerjaPdf from 'pdfjs-dist/build/pdf.worker.mjs';

// Kode worker ikut dibundel lalu dijalankan di thread utama (fake worker).
// Worker terpisah sempat dipakai, tetapi permintaannya menggantung tanpa galat
// di sebagian peramban; menggambar satu halaman pertama cukup ringan untuk
// dikerjakan di thread utama, apalagi berkasnya baru diambil saat mendekat layar.
globalThis.pdfjsWorker = pekerjaPdf;
pdfjs.GlobalWorkerOptions.workerSrc = '';

/** Lebar gambar sampul; tingginya mengikuti rasio halaman aslinya. */
const LEBAR_RENDER = 560;

/** Lebar dasar halaman dokumen pada halaman detail (dikali rasio piksel layar). */
const LEBAR_DOKUMEN = 900;

/** Jarak di luar layar yang sudah dianggap "hampir terlihat", dalam piksel. */
const AMBANG_PX = 400;

const antrean = new Set();
let sedangJalan = false;

async function gambarSampul(wadah) {
    const sumber = wadah.dataset.pdfCover;

    if (!sumber) {
        return;
    }

    const dokumen = await pdfjs.getDocument({ url: sumber, isEvalSupported: false }).promise;

    try {
        const halaman = await dokumen.getPage(1);
        const skala = LEBAR_RENDER / halaman.getViewport({ scale: 1 }).width;
        const viewport = halaman.getViewport({ scale: skala });

        const kanvas = document.createElement('canvas');
        kanvas.width = Math.round(viewport.width);
        kanvas.height = Math.round(viewport.height);
        kanvas.className = 'absolute inset-0 w-full h-full';
        kanvas.style.objectFit = 'cover';
        kanvas.style.objectPosition = 'top';

        await halaman.render({ canvas: kanvas, canvasContext: kanvas.getContext('2d'), viewport }).promise;

        wadah.querySelector('[data-pdf-cover-fallback]')?.remove();
        wadah.appendChild(kanvas);
    } finally {
        dokumen.destroy();
    }
}

function hampirTerlihat(el) {
    const kotak = el.getBoundingClientRect();

    return kotak.top < window.innerHeight + AMBANG_PX && kotak.bottom > -AMBANG_PX;
}

/** Gambar satu per satu supaya tidak mengunduh semua PDF sekaligus. */
async function prosesAntrean() {
    if (sedangJalan) {
        return;
    }

    sedangJalan = true;

    try {
        for (const wadah of Array.from(antrean)) {
            if (!hampirTerlihat(wadah)) {
                continue;
            }

            antrean.delete(wadah);

            try {
                await gambarSampul(wadah);
            } catch (e) {
                // Berkas rusak, bukan PDF, atau gagal diunduh: sampul cadangan
                // dibiarkan apa adanya.
                wadah.removeAttribute('data-pdf-cover');
            }
        }
    } finally {
        sedangJalan = false;
    }
}

/**
 * Halaman detail regulasi: seluruh halaman dokumen digambar berurutan di dalam
 * halaman, jadi pengunjung membacanya tanpa pindah tab maupun mengunduh dulu.
 */
async function gambarDokumen(wadah) {
    const sumber = wadah.dataset.pdfDokumen;
    const status = wadah.querySelector('[data-pdf-dokumen-status]');

    if (!sumber) {
        return;
    }

    const dokumen = await pdfjs.getDocument({ url: sumber, isEvalSupported: false }).promise;

    try {
        for (let nomor = 1; nomor <= dokumen.numPages; nomor += 1) {
            const halaman = await dokumen.getPage(nomor);
            const skala = (LEBAR_DOKUMEN * (window.devicePixelRatio || 1)) / halaman.getViewport({ scale: 1 }).width;
            const viewport = halaman.getViewport({ scale: skala });

            const kanvas = document.createElement('canvas');
            kanvas.width = Math.round(viewport.width);
            kanvas.height = Math.round(viewport.height);
            kanvas.className = 'w-full h-auto rounded-xl border border-gray-100 dark:border-white/10 bg-white';

            await halaman.render({ canvas: kanvas, canvasContext: kanvas.getContext('2d'), viewport }).promise;

            status?.remove();
            wadah.appendChild(kanvas);
        }
    } finally {
        dokumen.destroy();
    }
}

function mulai() {
    const dokumen = document.querySelector('[data-pdf-dokumen]');

    if (dokumen) {
        gambarDokumen(dokumen).catch(() => {
            const status = dokumen.querySelector('[data-pdf-dokumen-status] p');

            if (status) {
                status.textContent = status.dataset.gagal || 'Dokumen gagal dimuat. Coba muat ulang halaman.';
            }
        });
    }

    document.querySelectorAll('[data-pdf-cover]').forEach((wadah) => antrean.add(wadah));

    if (antrean.size === 0) {
        return;
    }

    prosesAntrean();

    window.addEventListener('scroll', prosesAntrean, { passive: true });
    window.addEventListener('resize', prosesAntrean, { passive: true });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', mulai);
} else {
    mulai();
}
