import './bootstrap';

import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

Alpine.plugin(collapse);
window.Alpine = Alpine;

Alpine.start();

/**
 * Tabel dari isi CMS dibungkus penggeser mendatar.
 *
 * Isi Halaman Statis dan Berita ditulis petugas lewat editor, jadi lebar
 * tabelnya tidak bisa ditentukan dari template. Tanpa pembungkus ini, satu
 * tabel enam kolom melebarkan seluruh halaman di ponsel — yang bergeser bukan
 * tabelnya melainkan semua isi halaman, termasuk header dan footer.
 *
 * Dikerjakan di sini, bukan di CSS, supaya tabelnya tetap dirender sebagai
 * tabel: `display: block` pada elemen `<table>` memang membuatnya bisa
 * digeser, tetapi sekaligus melepas penyelarasan kolom antar barisnya.
 */
function bungkusTabelRichText() {
    document.querySelectorAll('.fs-rte table').forEach((tabel) => {
        if (tabel.parentElement?.classList.contains('fs-tabel-scroll')) {
            return;
        }

        const pembungkus = document.createElement('div');
        pembungkus.className = 'fs-tabel-scroll';
        tabel.parentElement?.insertBefore(pembungkus, tabel);
        pembungkus.appendChild(tabel);
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bungkusTabelRichText);
} else {
    bungkusTabelRichText();
}
