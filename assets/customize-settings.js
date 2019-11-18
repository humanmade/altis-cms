( function () {
	/*
	* Custom JS to modify the Altis CMS module Add New Site page
	*/
	const siteSubdomain = document.getElementById( 'site-subdomain' );
	const siteSubdirectory = document.getElementById( 'site-subdirectory' );
	const siteCustomDomain = document.getElementById( 'site-custom-domain' );
	const siteAddress = document.getElementById( 'site-address' );
	const siteAddressDesc = document.getElementById( 'site-address-desc' );
	const networkAddress = window.location.hostname;
	const domainTextEl = document.createElement( 'span' );
	domainTextEl.className = 'site-address-domain-text';

	// Add domain hints around the site address field.
	function updatedomainText( event ) {
		switch ( event.target ) {
			case siteSubdomain:
				domainTextEl.innerHTML = '.' + networkAddress;
				siteAddress.parentElement.appendChild( domainTextEl );
				siteAddress.placeholder = 'name';
				siteAddressDesc.style.visibility = 'visible';
				break;
			case siteSubdirectory:
				domainTextEl.innerHTML = networkAddress + '/ ';
				siteAddress.parentElement.insertBefore( domainTextEl, siteAddress );
				siteAddress.placeholder = 'name';
				siteAddressDesc.style.visibility = 'visible';
				break;
			case siteCustomDomain:
				if ( siteAddress.parentElement.contains( domainTextEl ) ) {
					siteAddress.parentElement.removeChild( domainTextEl );
				}

				siteAddress.placeholder = 'example.com';
				siteAddressDesc.style.visibility = 'hidden';
				break;
			default:
				break;
		}
	}

	// Add an event listener to each radio button.
	document.getElementsByName( 'domain-type' ).forEach( function ( element ) {
		element.addEventListener( 'change', updatedomainText );

		// On load, dispatch a change event for the selected radio button.
		if ( element.checked ) {
			const changeEvent = new Event( 'change' );
			element.dispatchEvent( changeEvent );
		}
	} );
} )();
