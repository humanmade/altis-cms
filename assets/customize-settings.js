/*
 * Custom JS to modify the Altis CMS module Add New Site page
 */
const siteSubdomain = document.getElementById( 'site-subdomain' ),
	siteSubdirectory = document.getElementById( 'site-subdirectory' ),
	siteCustomDomain = document.getElementById( 'site-custom-domain' ),
	domainTypeRadioBtns = document.getElementsByName( 'domain-type' ),
	siteAddress = document.getElementById( 'site-address' ),
	networkAddress = window.location.hostname;
let domainTextEl = document.createElement("span");
domainTextEl.className = 'site-address-domain-text';

domainTypeRadioBtns.forEach( function( element ) {
	element.addEventListener( 'change', updatedomainText );
} );

function updatedomainText( event) {

	switch ( event.target ) {
		case siteSubdomain:
			console.log( 'subdomain' );
			domainTextEl.innerHTML = '.' + networkAddress;
			siteAddress.parentElement.appendChild( domainTextEl );
			break;
		case siteSubdirectory:
			console.log( 'subdirectory' );
			domainTextEl.innerHTML = networkAddress + '/ ';
			siteAddress.parentElement.insertBefore( domainTextEl, siteAddress );
			break;
		case siteCustomDomain:
			console.log( 'custom-domain' );
			siteAddress.parentElement.removeChild( domainTextEl );
			break;
	}
}
