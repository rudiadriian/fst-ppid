Tolong jalankan langkah untuk menyesuaikan/ modifikasi halaman frontend aplikasi ppid ini, jika langkah demi langkah sudah selesai, tolong berikan checklist, berikut ini langkahnya:
1. tambahkan widget beserta feature untuk penyandang disabelitas mudah mengakses website ini, saya sudah mendaftarkan akun EQUAL WEB, berikut codenya :
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