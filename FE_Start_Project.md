Tolong jalankan langkah untuk menyesuaikan/ modifikasi halaman frontend aplikasi ppid ini, jika langkah demi langkah sudah selesai, tolong berikan checklist, berikut ini langkahnya:
1. Saya cek gambar kanal-nya — ada beberapa sub-menu yang belum ter-cover di skema sebelumnya:
   1. Daftar Informasi Dikecualikan — beda struktur data dari info publik biasa (butuh alasan pengecualian, dasar hukum, jangka waktu, pejabat penetap) → belum ada tabel.
   2. Laporan Statistik Informasi Publik & Laporan Pelayanan Informasi — laporan periodik berisi angka agregat (jumlah permohonan masuk/dikabulkan/ditolak, rata-rata waktu respon) → belum ada tabel.
   3. Register Permohonan Informasi — rekap publik dari daftar permohonan → butuh flag consent, belum ada di permohonan_informasi.
    Dasar Hukum (kanal Profil) vs Regulasi (kanal Layanan) — sama-sama pakai tabel regulasi, tapi belum ada pembeda kategori.

2. tambahkan widget beserta feature untuk penyandang disabelitas mudah mengakses website ini, saya sudah mendaftarkan akun EQUAL WEB, berikut codenya :
<!-- Accessibility Code for "ppid-fstj.vercel.app" -->
<script>
/*
Want to customize your button? visit our documentation page:
https://login.equalweb.com/custom-button
*/
window.interdeal = {
    get sitekey (){ return "210e797aab2a9c0d254a9c1af498d48e"} ,
    get domains(){
        return {
            "js": "https://cdn.equalweb.com/",
            "acc": "https://access.equalweb.com/"
        }
    },
    "Position": "right",
    "Menulang": "ID",
    "draggable": true,
    "btnStyle": {
        "vPosition": [
            "50%",
            "80%"
        ],
        "margin": [
            "0",
            "0"
        ],
        "scale": [
            "0.8",
            "0.5"
        ],
        "color": {
            "main": "#2e850f",
            "second": "#ffffff"
        },
        "icon": {
            "outline": true,
            "outlineColor": "#ffffff",
            "type":  5 ,
            "shape": "circle"
        }
    },
                            "showTooltip": true,
      
};

(function(doc, head, body){
    var coreCall             = doc.createElement('script');
    coreCall.src             = interdeal.domains.js + 'core/5.3.1/accessibility.js';
    coreCall.defer           = true;
    coreCall.integrity       = 'sha512-3qLj5jbjMQnXk+FqEdVJjUnjJBGuBTRVOwaiT0ms6mQKQcrz4nulBxl2Hsr0/PpvEqdyJsMsU1NB+Mtfzw8hxA==';
    coreCall.crossOrigin     = 'anonymous';
    coreCall.setAttribute('data-cfasync', true );
    body? body.appendChild(coreCall) : head.appendChild(coreCall);
})(document, document.head, document.body);
</script>