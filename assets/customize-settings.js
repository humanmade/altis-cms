/*
 * Custom JS to modify the Altis CMS module Add New Site page
 */
const siteSubdomain = document.getElementById( 'site-subdomain' ),
	siteSubdirectory = document.getElementById( 'site-subdirectory' ),
	siteCustomDomain = document.getElementById( 'site-custom-domain' ),
	domainTypeRadioBtns = document.getElementsByName( 'domain-type' ),
	siteAddress = document.getElementById( 'site-address' ),
	siteAddressDesc = document.getElementById( 'site-address-desc' ),
	networkAddress = window.location.hostname;
let domainTextEl = document.createElement( 'span' );
domainTextEl.className = 'site-address-domain-text';

// Add domain hints around the site address field.
function updatedomainText( event ) {
	switch ( event.target ) {
		case siteSubdomain:
			domainTextEl.innerHTML = '.' + networkAddress;
			siteAddress.parentElement.appendChild( domainTextEl );
			siteAddressDesc.style.visibility = 'visible';
			break;
		case siteSubdirectory:
			domainTextEl.innerHTML = networkAddress + '/ ';
			siteAddress.parentElement.insertBefore( domainTextEl, siteAddress );
			siteAddressDesc.style.visibility = 'visible';
			break;
		case siteCustomDomain:
			siteAddress.parentElement.removeChild( domainTextEl );
			siteAddressDesc.style.visibility = 'hidden';
			break;
		default:
			break;
	}
}

// Add an event listener to each radio button.
domainTypeRadioBtns.forEach( function ( element ) {
	element.addEventListener( 'change', updatedomainText );

	// On load, dispatch a change event for the selected radio button.
	if ( element.checked ) {
		let changeEvent = new Event( 'change' );
		element.dispatchEvent( changeEvent );
	}
} );
