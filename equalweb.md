<!-- Accessibility Code for "ppid.foodstation.co.id" -->
<script>
/*
Want to customize your button? visit our documentation page:
https://login.equalweb.com/custom-button
*/
window.interdeal = {
    get sitekey (){ return "456aef3abfbc03147000a14fb44e602d"} ,
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
            "main": "#1c4bb6",
            "second": "#ffffff"
        },
        "icon": {
            "outline": false,
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