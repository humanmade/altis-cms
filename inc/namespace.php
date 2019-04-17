<?php

namespace HM\Platform\CMS;

use function HM\Platform\get_config;

/**
 * Main bootstrap / entry point for the CMS module.
 */
function bootstrap() {
	$config = get_config()['modules']['cms'];
	Remove_Updates\bootstrap();

	if ( $config['branding'] ) {
		Branding\bootstrap();
	}

	if ( $config['login-logo'] ) {
		add_action( 'login_header', __NAMESPACE__ . '\\add_login_logo' );
	}

	if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
		define( 'DISALLOW_FILE_EDIT', true );
	}
}

/**
 * Add the custom login logo to the login page.
 */
function add_login_logo() {
	$logo = get_config()['modules']['cms']['login-logo'];
	?>
	<style>
		.login h1 a {
			background-image: url('<?php echo site_url( $logo ) ?>');
			background-size: contain;
			width: auto;
		}
	</style>
	<?php
}
