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


# HASIL KONFIGRUASI BARU DI TAMPILAN DEVTOOLS WEB BROWSER ketika akses ppid.foodstation.co.id :
/*
Domain Error :
origin header: 
reffer header: https://ppid.foodstation.co.id/
setDomainNameNoWW: 
setDomainName: ppid-fstj.vercel.app
domainName REF:ppid.foodstation.co.id
refrerNowww:   
currDomain:    

@valid : false
*/






interdeal.menu.innerHTML = `
<div id="INDerror">

	

<link rel="stylesheet" href="https://access.equalweb.com/assets/includes/errorpages/css/error-menu.css">

<div class="INDmenuHeader">
    <button aria-label="close menu" id="INDcloseAccMenu" onclick="interdeal.CloseMenu()"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
    width="16px" height="16px" viewBox="0 0 38 38" enable-background="new 0 0 38 38" xml:space="preserve">
    <path stroke="White" stroke-width="7" stroke-linecap="round" d="M7  6 L30 31" />
    <path stroke="White" stroke-width="7" stroke-linecap="round" d="M30 7 L7  30" />
</svg></button>
    
</div>
<div  class="INDmenuBody"><div class="INDmenuBody-inner">
	<h2 id="INDmenu-heading" tabindex="0"> <span aria-hidden="true" class="INDmenu-heading-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path d="M270.2 160h35.5c3.4 0 6.1 2.8 6 6.2l-7.5 196c-.1 3.2-2.8 5.8-6 5.8h-20.5c-3.2 0-5.9-2.5-6-5.8l-7.5-196c-.1-3.4 2.6-6.2 6-6.2zM288 388c-15.5 0-28 12.5-28 28s12.5 28 28 28 28-12.5 28-28-12.5-28-28-28zm281.5 52L329.6 24c-18.4-32-64.7-32-83.2 0L6.5 440c-18.4 31.9 4.6 72 41.6 72H528c36.8 0 60-40 41.5-72zM528 480H48c-12.3 0-20-13.3-13.9-24l240-416c6.1-10.6 21.6-10.7 27.7 0l240 416c6.2 10.6-1.5 24-13.8 24z"/></svg></span><span class="INDerrTitle">Domain Error</span></h2>
	<p>Apologies, it looks like the site key isn't matching the domain.</p>
	<p class="INDerrNotif">Contact us for further assistance <br /><a href="mailto:support@equalweb.com">support@equalweb.com</a></p>
	<a href="https://equalweb.com" target="_blank" class="INDmenuBody-equal-logo">
		<img src="https://access.equalweb.com/assets/images/ew-icon-b.png" alt="equalweb logo">
		www.equalweb.com
	</a>
</div></div>

<div class="INDmenuFooter">
       Powered by 
        
            <a href="https://equalweb.com" target="_blank">Equalweb</a>
        
</div>


</div>`
interdeal.isError = true;
interdeal.menu.setAttribute( 'aria-hidden', false );
interdeal?.a11y?.removeLoader()
if(interdeal.a11y.a11yBtn.disabled){
	interdeal.isPending = false
	interdeal.a11y.a11yBtn.disabled = false
}
