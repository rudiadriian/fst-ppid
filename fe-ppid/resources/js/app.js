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

/**
 * Formulir ber-`data-sekali-kirim` hanya boleh terkirim satu kali.
 *
 * Menekan Kirim dua kali pada formulir Permohonan dan Keberatan melahirkan dua
 * berkas dengan dua nomor registrasi atas satu permintaan yang sama — dan pada
 * Keberatan, yang membawa unggahan lampiran, jeda kirimnya justru paling
 * panjang sehingga klik keduanya paling mungkin terjadi.
 *
 * Yang dikerjakan di sini hanya sisi layar: menahan kiriman berikutnya dan
 * membuat kuncinya terlihat, supaya pemohon tahu berkasnya sedang dikirim dan
 * tidak menekan lagi. Penjagaan yang sebenarnya ada di server
 * (App\Support\SekaliKirim); tanpa itu, JavaScript yang mati atau tombol Muat
 * Ulang setelah POST tetap bisa menyimpan berkas kedua.
 */
function kunciTombolKirim(form) {
    form.querySelectorAll('[data-kirim]').forEach((tombol) => {
        const label = tombol.querySelector('[data-label]');
        const sibuk = tombol.dataset.labelSibuk;

        if (label && sibuk) {
            tombol.dataset.labelAwal = label.textContent;
            label.textContent = sibuk;
        }

        tombol.querySelector('[data-putaran]')?.classList.remove('hidden');
        tombol.setAttribute('aria-busy', 'true');

        /*
         * Menonaktifkan tombol ditunda satu putaran: tombol yang sudah
         * `disabled` pada saat peristiwa submit masih berjalan tidak ikut
         * terkirim nilainya di sebagian peramban.
         */
        window.setTimeout(() => {
            tombol.disabled = true;
        }, 0);
    });

    // Tautan Batal ikut dimatikan: berpindah halaman saat berkasnya sedang
    // dikirim membuat pemohon tidak pernah melihat nomor registrasinya.
    form.querySelectorAll('[data-batal]').forEach((tautan) => {
        tautan.setAttribute('aria-disabled', 'true');
        tautan.classList.add('pointer-events-none', 'opacity-50');
    });
}

document.addEventListener('submit', (peristiwa) => {
    const form = peristiwa.target;

    if (!(form instanceof HTMLFormElement) || !form.hasAttribute('data-sekali-kirim')) {
        return;
    }

    if (form.dataset.terkirim === '1') {
        peristiwa.preventDefault();
        peristiwa.stopImmediatePropagation();

        return;
    }

    // Kiriman yang sudah dibatalkan penangan lain tidak dihitung terkirim —
    // formulirnya masih di layar dan tombolnya harus tetap bisa ditekan.
    if (peristiwa.defaultPrevented) {
        return;
    }

    form.dataset.terkirim = '1';
    kunciTombolKirim(form);
});

/*
 * Halaman yang dibuka kembali lewat tombol Kembali datang dari simpanan
 * peramban apa adanya, termasuk tombol yang terkunci. Kuncinya dilepas supaya
 * formulirnya tidak tertinggal mati — tokennya sendiri sudah tidak berlaku, jadi
 * kiriman ulang tetap ditahan server.
 */
window.addEventListener('pageshow', (peristiwa) => {
    if (!peristiwa.persisted) {
        return;
    }

    document.querySelectorAll('form[data-sekali-kirim]').forEach((form) => {
        delete form.dataset.terkirim;

        form.querySelectorAll('[data-kirim]').forEach((tombol) => {
            tombol.disabled = false;
            tombol.removeAttribute('aria-busy');
            tombol.querySelector('[data-putaran]')?.classList.add('hidden');

            const label = tombol.querySelector('[data-label]');

            if (label && tombol.dataset.labelAwal) {
                label.textContent = tombol.dataset.labelAwal;
            }
        });

        form.querySelectorAll('[data-batal]').forEach((tautan) => {
            tautan.removeAttribute('aria-disabled');
            tautan.classList.remove('pointer-events-none', 'opacity-50');
        });
    });
});
