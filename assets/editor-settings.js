( function () {

	// Check for existence os localStorage WP User Settings.
	if ( ! window.localStorage ) {
		return;
	}

	// Get user ID from utils localization data.
	const settingsKey = 'WP_DATA_USER_' + window.altisDefaultEditorSettings.uid;
	let settings = JSON.parse( localStorage.getItem( settingsKey ) || '{}' );

	// If this isn't an object then bail.
	if ( typeof settings !== 'object' ) {
		return;
	}

	// Update the setting with our desired defaults.
	function updateSettings() {
		localStorage.setItem( settingsKey, JSON.stringify( settings ) );
	}

	if ( settings['core/edit-post'] ) {
		// If there are already settings then check if the settings with bad defaults are present.
		if ( settings['core/edit-post']['preferences'] && settings['core/edit-post']['preferences']['features'] ) {
			if ( typeof settings['core/edit-post']['preferences']['features']['fullscreenMode'] === 'undefined' ) {
				settings['core/edit-post']['preferences']['features']['fullscreenMode'] = false;
				updateSettings();
			}
			if ( typeof settings['core/edit-post']['preferences']['features']['welcomeGuide'] === 'undefined' ) {
				settings['core/edit-post']['preferences']['features']['welcomeGuide'] = false;
				updateSettings();
			}
		}
	} else {
		// Set our own good defaults.
		settings['core/edit-post'] = {
			preferences: {
				features: {
					fullscreenMode: false,
					welcomeGuide: false,
				},
			},
		};
		updateSettings();
	}

} )();
